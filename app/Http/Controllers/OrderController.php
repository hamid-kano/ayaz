<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\User;
use App\Models\Attachment;
use App\Models\AudioRecording;
use App\Services\OneSignalService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class OrderController extends Controller
{
    public function index(Request $request)
    {
        if (!auth()->user()->canViewOrders()) {
            abort(403, 'غير مصرح لك بعرض الطلبيات');
        }

        $query = Order::with(['executor']);

        // إذا كان المستخدم عادي، إظهار طلبياته فقط
        if (!auth()->user()->isAdmin() && !auth()->user()->isAuditor()) {
            $query->where('executor_id', auth()->id());
        }

        // حساب عدد الطلبيات لكل حالة
        $baseQuery = clone $query;
        $statusCounts = [
            'all' => $baseQuery->count(),
            'new' => (clone $baseQuery)->where('status', 'new')->count(),
            'in-progress' => (clone $baseQuery)->where('status', 'in-progress')->count(),
            'ready' => (clone $baseQuery)->where('status', 'ready')->count(),
            'delivered' => (clone $baseQuery)->where('status', 'delivered')->count(),
            'archived' => (clone $baseQuery)->where('status', 'archived')->count(),
            'cancelled' => (clone $baseQuery)->where('status', 'cancelled')->count(),
        ];

        // فلتر حسب الحالة
        if ($request->has('status') && $request->status != '') {
            $query->where('status', $request->status);
        }

        if ($request->has('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('order_number', 'like', "%{$search}%")
                    ->orWhere('customer_name', 'like', "%{$search}%")
                    ->orWhere('order_type', 'like', "%{$search}%")
                    ->orWhere('reviewer_name', 'like', "%{$search}%");
            });
        }

        $orders = $query->latest()->get();

        return view('orders.index', compact('orders', 'statusCounts'));
    }

    public function create()
    {
        if (!auth()->user()->canEditOrders()) {
            abort(403, 'غير مصرح لك بإنشاء طلبيات');
        }
        
        $users = User::all();
        return view('orders.create', compact('users'));
    }

    public function store(Request $request)
    {
        if (!auth()->user()->canEditOrders()) {
            abort(403, 'غير مصرح لك بإنشاء طلبيات');
        }
        
        $validated = $request->validate([
            'customer_name' => 'required|string|max:255',
            'customer_phone' => 'nullable|string|max:20',
            'order_type' => 'required|string|max:255',
            'order_details' => 'required|string',
            'delivery_date' => 'required|date_format:Y-m-d\TH:i',
            'is_urgent' => 'boolean',
            'reviewer_name' => 'nullable|string|max:255',
            'executor_id' => 'nullable|exists:users,id',
            'items' => 'nullable|array',
            'items.*.item_name' => 'required|string|max:255',
            'items.*.quantity' => 'required|integer|min:1',
            'items.*.price' => 'required|numeric|min:0|regex:/^\d+(\.\d{1,6})?$/',
            'items.*.currency' => 'required|in:syp,usd',
            'receipt_amount' => 'nullable|numeric|min:0|regex:/^\d+(\.\d{1,6})?$/',
            'receipt_currency' => 'nullable|in:syp,usd',
            'receipt_date' => 'nullable|date',
            'receipt_notes' => 'nullable|string',
        ]);

        // إنشاء رقم طلبية تلقائي
        $lastOrder = Order::orderBy('id', 'desc')->first();
        $nextNumber = $lastOrder ? ($lastOrder->id + 10001) : 10001;
        $validated['order_number'] = 'ORD-' . $nextNumber;
        $validated['order_date'] = \Carbon\Carbon::now('Asia/Damascus')->toDateString();

        $order = Order::create($validated);

        // إضافة المواد
        if (isset($validated['items'])) {
            foreach ($validated['items'] as $item) {
                $order->items()->create($item);
            }
        }

        // إرسال إشعار للمنفذ عند تعيين طلبية جديدة
        if ($order->executor_id) {
            // إضافة إشعار محلي
            $urgentText = $order->is_urgent ? ' (مستعجلة)' : '';
            \App\Models\Notification::create([
                'user_id' => $order->executor_id,
                'type' => 'new_order',
                'title' => 'طلبية جديدة' . $urgentText,
                'message' => "تم تعيين طلبية جديدة لك من: {$order->customer_name}" . $urgentText,
                'data' => ['order_id' => $order->id]
            ]);
            
            // إرسال push notification
            if ($order->executor->player_id) {
                $oneSignal = new OneSignalService();
                $oneSignal->sendToUser(
                    $order->executor->player_id,
                    'طلبية جديدة' . $urgentText,
                    "تم تعيين طلبية جديدة لك من: {$order->customer_name}" . $urgentText,
                    ['order_id' => $order->id, 'type' => 'new_order']
                );
            }
        }

        // إنشاء سند القبض إذا تم إدخال مبلغ
        if (!empty($validated['receipt_amount']) && $validated['receipt_amount'] > 0) {
            \App\Models\Receipt::create([
                'order_id' => $order->id,
                'amount' => $validated['receipt_amount'],
                'currency' => $validated['receipt_currency'] ?? 'syp',
                'receipt_date' => $validated['receipt_date'] ?? now()->toDateString(),
                'notes' => $validated['receipt_notes'],
            ]);
        }

        // Handle file uploads
        if ($request->hasFile('attachments')) {
            foreach ($request->file('attachments') as $file) {
                $fileName = time() . '_' . $file->getClientOriginalName();
                $orderPath = 'attachments/' . $order->id;
                
                if (!file_exists(public_path($orderPath))) {
                    mkdir(public_path($orderPath), 0755, true);
                }
                
                $file->move(public_path($orderPath), $fileName);

                $order->attachments()->create([
                    'file_name' => $file->getClientOriginalName(),
                    'file_path' => $orderPath . '/' . $fileName,
                    'file_type' => $file->getClientMimeType(),
                    'file_size' => filesize(public_path($orderPath . '/' . $fileName)),
                ]);
            }
        }

        return redirect()->route('orders.index')->with('success', 'تم إنشاء الطلبية بنجاح');
    }

    public function show(Order $order)
    {
        $order->load(['executor', 'attachments', 'audioRecordings', 'receipts', 'items']);
        return view('orders.show', compact('order'));
    }

    public function edit(Order $order)
    {
        if (!auth()->user()->canEditOrders()) {
            abort(403, 'غير مصرح لك بتعديل الطلبيات');
        }
        
        $users = User::all();
        return view('orders.edit', compact('order', 'users'));
    }

    public function update(Request $request, Order $order)
    {
        if (!auth()->user()->canEditOrders()) {
            abort(403, 'غير مصرح لك بتعديل الطلبيات');
        }
        
        $validated = $request->validate([
            'customer_name' => 'required|string|max:255',
            'customer_phone' => 'nullable|string|max:20',
            'order_type' => 'required|string|max:255',
            'order_details' => 'required|string',
            'status' => 'required|in:new,in-progress,ready,delivered,archived,cancelled',
            'delivery_date' => 'required|date_format:Y-m-d\TH:i',
            'is_urgent' => 'boolean',
            'reviewer_name' => 'nullable|string|max:255',
            'executor_id' => 'nullable|exists:users,id',
            'items' => 'nullable|array',
            'items.*.item_name' => 'required|string|max:255',
            'items.*.quantity' => 'required|integer|min:1',
            'items.*.price' => 'required|numeric|min:0|regex:/^\d+(\.\d{1,6})?$/',
            'items.*.currency' => 'required|in:syp,usd',
        ]);

        $oldExecutorId = $order->executor_id;
        $order->update($validated);

        // تحديث المواد
        if (isset($validated['items'])) {
            $order->items()->delete();
            foreach ($validated['items'] as $item) {
                $order->items()->create($item);
            }
        }

        // إرسال إشعار عند تغيير المنفذ
        if ($oldExecutorId != $validated['executor_id']) {
            $oneSignal = new OneSignalService();
            
            // إشعار للمنفذ الجديد
            if ($validated['executor_id']) {
                // إضافة إشعار محلي
                $urgentText = $order->is_urgent ? ' (مستعجلة)' : '';
                \App\Models\Notification::create([
                    'user_id' => $validated['executor_id'],
                    'type' => 'assigned_order',
                    'title' => 'طلبية جديدة' . $urgentText,
                    'message' => "تم تعيين طلبية لك من: {$order->customer_name}" . $urgentText,
                    'data' => ['order_id' => $order->id]
                ]);
                
                $newExecutor = User::find($validated['executor_id']);
                if ($newExecutor && $newExecutor->player_id) {
                    $oneSignal->sendToUser(
                        $newExecutor->player_id,
                        'طلبية جديدة' . $urgentText,
                        "تم تعيين طلبية لك من: {$order->customer_name}" . $urgentText,
                        ['order_id' => $order->id, 'type' => 'assigned_order']
                    );
                }
            }
            
            // إشعار للمنفذ السابق
            if ($oldExecutorId) {
                // إضافة إشعار محلي
                \App\Models\Notification::create([
                    'user_id' => $oldExecutorId,
                    'type' => 'unassigned_order',
                    'title' => 'تغيير في الطلبية',
                    'message' => "تم إلغاء تعيين طلبية: {$order->customer_name}",
                    'data' => ['order_id' => $order->id]
                ]);
                
                $oldExecutor = User::find($oldExecutorId);
                if ($oldExecutor && $oldExecutor->player_id) {
                    $oneSignal->sendToUser(
                        $oldExecutor->player_id,
                        'تغيير في الطلبية',
                        "تم إلغاء تعيين طلبية: {$order->customer_name}",
                        ['order_id' => $order->id, 'type' => 'unassigned_order']
                    );
                }
            }
        }

        return redirect()->route('orders.show', $order)->with('success', 'تم تحديث الطلبية بنجاح');
    }

    public function destroy(Order $order)
    {
        if (!auth()->user()->canDeleteOrders()) {
            abort(403, 'غير مصرح لك بحذف الطلبيات');
        }
        
        // تحقق من عدم وجود مقبوضات
        // if ($order->receipts()->count() > 0) {
        //     return redirect()->route('orders.index')
        //         ->with('error', 'لا يمكن حذف الطلبية لوجود مقبوضات مرتبطة بها');
        // }

        // تحقق من عدم كون الطلبية مسلمة
        // if ($order->status === 'delivered') {
        //     return redirect()->route('orders.index')
        //         ->with('error', 'لا يمكن حذف طلبية مسلمة');
        // }

        // حذف المقبوضات المرتبطة
        $order->receipts()->delete();

        // حذف المرفقات والتسجيلات الصوتية
        foreach ($order->attachments as $attachment) {
            if (file_exists(public_path($attachment->file_path))) {
                unlink(public_path($attachment->file_path));
            }
        }
        
        foreach ($order->audioRecordings as $audio) {
            if (file_exists(public_path($audio->file_path))) {
                unlink(public_path($audio->file_path));
            }
        }

        $order->delete();
        return redirect()->route('orders.index')->with('success', 'تم حذف الطلبية بنجاح');
    }

    public function debts(Request $request)
    {
        $query = Order::with(['receipts', 'items']);

        if ($request->has('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('order_number', 'like', "%{$search}%")
                    ->orWhere('customer_name', 'like', "%{$search}%")
                    ->orWhere('order_type', 'like', "%{$search}%");
            });
        }

        $orders = $query->get()
            ->filter(function($order) {
                return $order->remaining_amount_syp > 0 || $order->remaining_amount_usd > 0;
            });

        $totalUsd = $orders->sum('remaining_amount_usd');
        $totalSyp = $orders->sum('remaining_amount_syp');

        return view('orders.debts', compact('orders', 'totalUsd', 'totalSyp'));
    }

    public function uploadAttachment(Request $request, Order $order)
    {
        if (!auth()->user()->canEditOrders()) {
            abort(403, 'غير مصرح لك بتعديل الطلبيات');
        }
        
        $request->validate([
            'attachments.*' => 'required|file|max:10240'
        ]);

        $uploaded = [];

        if ($request->hasFile('attachments')) {
            foreach ($request->file('attachments') as $file) {
                $fileName = time() . '_' . $file->getClientOriginalName();
                $orderPath = 'attachments/' . $order->id;
                
                if (!file_exists(public_path($orderPath))) {
                    mkdir(public_path($orderPath), 0755, true);
                }
                
                $file->move(public_path($orderPath), $fileName);
                
                $attachment = $order->attachments()->create([
                    'file_name' => $file->getClientOriginalName(),
                    'file_path' => $orderPath . '/' . $fileName,
                    'file_type' => $file->getClientMimeType(),
                    'file_size' => filesize(public_path($orderPath . '/' . $fileName)),
                ]);
                $uploaded[] = [
                    'id' => $attachment->id,
                    'file_name' => $attachment->file_name,
                    'file_path' => $attachment->file_path,
                    'file_size' => $attachment->file_size
                ];
            }
        }

        if ($request->expectsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'تم رفع المرفقات بنجاح',
                'attachments' => $uploaded
            ]);
        }

        return redirect()->route('orders.show', $order)->with('success', 'تم رفع المرفقات بنجاح');
    }

    public function deleteAttachment(Attachment $attachment)
    {
        if (!auth()->user()->canEditOrders()) {
            abort(403, 'غير مصرح لك بحذف المرفقات');
        }
        
        if (file_exists(public_path($attachment->file_path))) {
            unlink(public_path($attachment->file_path));
        }
        $attachment->delete();

        return back()->with('success', 'تم حذف المرفق بنجاح');
    }

    public function uploadAudio(Request $request, Order $order)
    {
        if (!auth()->user()->canEditOrders()) {
            abort(403, 'غير مصرح لك بتعديل الطلبيات');
        }
        
        $request->validate([
            'audio' => 'required|file|mimes:wav,mp3,m4a,webm|max:5120'
        ]);

        if ($request->hasFile('audio')) {
            $file = $request->file('audio');
            $fileName = time() . '_' . $file->getClientOriginalName();
            $orderPath = 'audio/' . $order->id;

            if (!file_exists(public_path($orderPath))) {
                mkdir(public_path($orderPath), 0755, true);
            }

            $file->move(public_path($orderPath), $fileName);

            $audio = $order->audioRecordings()->create([
                'file_name' => $file->getClientOriginalName(),
                'file_path' => $orderPath . '/' . $fileName,
                'file_size' => filesize(public_path($orderPath . '/' . $fileName)),
            ]);

            return response()->json([
                'success' => true,
                'audio' => [
                    'id' => $audio->id,
                    'file_name' => $audio->file_name,
                    'filename' => $fileName,
                    'file_path' =>  $audio->file_path

                ]
            ]);
        }

        return response()->json(['success' => false]);
    }

    public function deleteAudio(AudioRecording $audio)
    {
        if (!auth()->user()->canEditOrders()) {
            abort(403, 'غير مصرح لك بحذف التسجيلات');
        }
        
        if (file_exists(public_path($audio->file_path))) {
            unlink(public_path($audio->file_path));
        }
        $audio->delete();

        return back()->with('success', 'تم حذف التسجيل بنجاح');
    }

    public function print(Order $order)
    {
        $order->load(['executor', 'receipts', 'items']);
        return view('orders.print', compact('order'));
    }

    public function publicPrint(Order $order)
    {
        $order->load(['executor', 'receipts', 'items']);
        return view('orders.print', compact('order'));
    }

    public function archive(Order $order)
    {
        if (!auth()->user()->canEditOrders()) {
            abort(403, 'غير مصرح لك بأرشفة الطلبيات');
        }
        
        if (!$order->canBeArchived()) {
            return back()->with('error', 'يمكن أرشفة الطلبيات المسلمة فقط');
        }

        $order->update(['status' => 'archived']);
        
        return back()->with('success', 'تم أرشفة الطلبية بنجاح');
    }

    public function archives(Request $request)
    {
        $query = Order::with(['executor'])->where('status', 'archived');

        if (!auth()->user()->isAdmin()) {
            $query->where('executor_id', auth()->id());
        }

        if ($request->has('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('order_number', 'like', "%{$search}%")
                    ->orWhere('customer_name', 'like', "%{$search}%")
                    ->orWhere('order_type', 'like', "%{$search}%");
            });
        }

        $archives = $query->latest()->get()->groupBy('archive_folder');
        
        return view('orders.archives', compact('archives'));
    }

    public function updateStatus(Request $request, Order $order)
    {
        $request->validate([
            'status' => 'required|in:new,in-progress,ready,delivered,archived,cancelled'
        ]);

        $order->update(['status' => $request->status]);

        return redirect()->route('orders.index')->with('success', 'تم تحديث حالة الطلبية بنجاح');
    }
}
