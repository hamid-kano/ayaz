<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class OrderController extends Controller
{
    public function index(Request $request)
    {
        $query = Order::with(['reviewer', 'executor']);
        
        if ($request->has('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('order_number', 'like', "%{$search}%")
                  ->orWhere('customer_name', 'like', "%{$search}%")
                  ->orWhere('order_type', 'like', "%{$search}%");
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
            'order_type' => 'required|string',
            'order_details' => 'required|string',
            'cost' => 'required|numeric|min:0',
            'currency' => 'required|in:syp,usd',
            'delivery_date' => 'required|date',
            'reviewer_id' => 'nullable|exists:users,id',
            'executor_id' => 'nullable|exists:users,id',
        ]);

        $validated['order_number'] = 'ORD-' . time();
        $validated['order_date'] = now()->toDateString();

        $order = Order::create($validated);

        // Handle file uploads
        if ($request->hasFile('attachments')) {
            foreach ($request->file('attachments') as $file) {
                $path = $file->store('attachments', 'public');
                $order->attachments()->create([
                    'file_name' => $file->getClientOriginalName(),
                    'file_path' => $path,
                    'file_type' => $file->getClientMimeType(),
                    'file_size' => $file->getSize(),
                ]);
            }
        }

        return redirect()->route('orders.index')->with('success', 'تم إنشاء الطلبية بنجاح');
    }

    public function show(Order $order)
    {
        $order->load(['reviewer', 'executor', 'attachments', 'audioRecordings', 'receipts']);
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
            'order_type' => 'required|string',
            'order_details' => 'required|string',
            'cost' => 'required|numeric|min:0',
            'currency' => 'required|in:syp,usd',
            'status' => 'required|in:new,in-progress,delivered,cancelled',
            'delivery_date' => 'required|date',
            'reviewer_id' => 'nullable|exists:users,id',
            'executor_id' => 'nullable|exists:users,id',
        ]);

        $order->update($validated);

        return redirect()->route('orders.show', $order)->with('success', 'تم تحديث الطلبية بنجاح');
    }

    public function destroy(Order $order)
    {
        $order->delete();
        return redirect()->route('orders.index')->with('success', 'تم حذف الطلبية بنجاح');
    }

    public function debts()
    {
        $orders = Order::with('receipts')
            ->whereHas('receipts', function($query) {
                $query->havingRaw('SUM(amount) < orders.cost');
            })
            ->orWhereDoesntHave('receipts')
            ->get();

        $totalUsd = $orders->where('currency', 'usd')->sum('remaining_amount');
        $totalSyp = $orders->where('currency', 'syp')->sum('remaining_amount');

        return view('orders.debts', compact('orders', 'totalUsd', 'totalSyp'));
    }
}