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
        $query = Order::with(['executor']);

        // إذا كان المستخدم عادي، إظهار طلبياته فقط
        if (!auth()->user()->isAdmin()) {
            $query->where('executor_id', auth()->id());
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

        return view('orders.index', compact('orders'));
    }

    public function create()
    {
        $users = User::all();
        return view('orders.create', compact('users'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'customer_name' => 'required|string|max:255',
            'customer_phone' => 'nullable|string|max:20',
            'order_type' => 'required|string|max:255',
            'order_details' => 'required|string',
            'cost' => 'required|numeric|min:0',
            'currency' => 'required|in:syp,usd',
            'delivery_date' => 'required|date',
            'reviewer_name' => 'nullable|string|max:255',
            'executor_id' => 'nullable|exists:users,id',
        ]);

        // إنشاء رقم طلبية تلقائي
        $lastOrder = Order::orderBy('id', 'desc')->first();
        $nextNumber = $lastOrder ? ($lastOrder->id + 10001) : 10001;
        $validated['order_number'] = 'ORD-' . $nextNumber;
        $validated['order_date'] = now()->toDateString();

        $order = Order::create($validated);

        // إرسال إشعار للمنفذ عند تعيين طلبية جديدة
        if ($order->executor_id) {
            // إضافة إشعار محلي
            \App\Models\Notification::create([
                'user_id' => $order->executor_id,
                'type' => 'new_order',
                'title' => 'طلبية جديدة',
                'message' => "تم تعيين طلبية جديدة لك: {$order->order_number}",
                'data' => ['order_id' => $order->id]
            ]);
            
            // إرسال push notification
            if ($order->executor->player_id) {
                $oneSignal = new OneSignalService();
                $oneSignal->sendToUser(
                    $order->executor->player_id,
                    'طلبية جديدة',
                    "تم تعيين طلبية جديدة لك: {$order->order_number}",
                    ['order_id' => $order->id, 'type' => 'new_order']
                );
            }
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
        $order->load(['executor', 'attachments', 'audioRecordings', 'receipts']);
        return view('orders.show', compact('order'));
    }

    public function edit(Order $order)
    {
        $users = User::all();
        return view('orders.edit', compact('order', 'users'));
    }

    public function update(Request $request, Order $order)
    {
        $validated = $request->validate([
            'customer_name' => 'required|string|max:255',
            'customer_phone' => 'nullable|string|max:20',
            'order_type' => 'required|string|max:255',
            'order_details' => 'required|string',
            'cost' => 'required|numeric|min:0',
            'currency' => 'required|in:syp,usd',
            'status' => 'required|in:new,in-progress,delivered,cancelled',
            'delivery_date' => 'required|date',
            'reviewer_name' => 'nullable|string|max:255',
            'executor_id' => 'nullable|exists:users,id',
        ]);

        $oldExecutorId = $order->executor_id;
        $order->update($validated);

        // إرسال إشعار عند تغيير المنفذ
        if ($oldExecutorId != $validated['executor_id']) {
            $oneSignal = new OneSignalService();
            
            // إشعار للمنفذ الجديد
            if ($validated['executor_id']) {
                // إضافة إشعار محلي
                \App\Models\Notification::create([
                    'user_id' => $validated['executor_id'],
                    'type' => 'assigned_order',
                    'title' => 'طلبية جديدة',
                    'message' => "تم تعيين طلبية لك: {$order->order_number}",
                    'data' => ['order_id' => $order->id]
                ]);
                
                $newExecutor = User::find($validated['executor_id']);
                if ($newExecutor && $newExecutor->player_id) {
                    $oneSignal->sendToUser(
                        $newExecutor->player_id,
                        'طلبية جديدة',
                        "تم تعيين طلبية لك: {$order->order_number}",
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
                    'message' => "تم إلغاء تعيين الطلبية: {$order->order_number}",
                    'data' => ['order_id' => $order->id]
                ]);
                
                $oldExecutor = User::find($oldExecutorId);
                if ($oldExecutor && $oldExecutor->player_id) {
                    $oneSignal->sendToUser(
                        $oldExecutor->player_id,
                        'تغيير في الطلبية',
                        "تم إلغاء تعيين الطلبية: {$order->order_number}",
                        ['order_id' => $order->id, 'type' => 'unassigned_order']
                    );
                }
            }
        }

        return redirect()->route('orders.show', $order)->with('success', 'تم تحديث الطلبية بنجاح');
    }

    public function destroy(Order $order)
    {
        // تحقق من عدم وجود مقبوضات
        if ($order->receipts()->count() > 0) {
            return redirect()->route('orders.index')
                ->with('error', 'لا يمكن حذف الطلبية لوجود مقبوضات مرتبطة بها');
        }

        // تحقق من عدم كون الطلبية مسلمة
        if ($order->status === 'delivered') {
            return redirect()->route('orders.index')
                ->with('error', 'لا يمكن حذف طلبية مسلمة');
        }

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

    public function debts()
    {
        $orders = Order::with('receipts')
            ->get()
            ->filter(function($order) {
                return $order->remaining_amount > 0;
            });

        $totalUsd = $orders->where('currency', 'usd')->sum('remaining_amount');
        $totalSyp = $orders->where('currency', 'syp')->sum('remaining_amount');

        return view('orders.debts', compact('orders', 'totalUsd', 'totalSyp'));
    }

    public function uploadAttachment(Request $request, Order $order)
    {
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
        if (file_exists(public_path($attachment->file_path))) {
            unlink(public_path($attachment->file_path));
        }
        $attachment->delete();

        return back()->with('success', 'تم حذف المرفق بنجاح');
    }

    public function uploadAudio(Request $request, Order $order)
    {
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
        if (file_exists(public_path($audio->file_path))) {
            unlink(public_path($audio->file_path));
        }
        $audio->delete();

        return back()->with('success', 'تم حذف التسجيل بنجاح');
    }

    public function print(Order $order)
    {
        $order->load(['executor', 'receipts']);
        return view('orders.print', compact('order'));
    }
}
