<?php

namespace App\Http\Controllers;

use App\Models\Receipt;
use App\Models\Order;
use Illuminate\Http\Request;

class ReceiptController extends Controller
{
    public function index(Request $request)
    {
        $query = Receipt::with('order');

        if ($request->has('order_id')) {
            $query->where('order_id', $request->order_id);
        }

        if ($request->has('search')) {
            $search = $request->search;
            $query->whereHas('order', function ($q) use ($search) {
                $q->where('order_number', 'like', "%{$search}%")
                    ->orWhere('customer_name', 'like', "%{$search}%");
            })->orWhere('notes', 'like', "%{$search}%");
        }

        $receipts = $query->latest()->get();
        
        return view('receipts.index', compact('receipts'));
    }

    public function create(Request $request)
    {
        $orders = Order::whereIn('status', ['new', 'in-progress'])
            ->with(['receipts', 'items'])
            ->get()
            ->filter(function($order) {
                return $order->remaining_amount_syp > 0 || $order->remaining_amount_usd > 0;
            });
        
        $selectedOrderId = $request->get('order_id');
            
        return view('receipts.create', compact('orders', 'selectedOrderId'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'order_id' => 'required|exists:orders,id',
            'amount' => 'required|numeric|min:0.000001|regex:/^\d+(\.\d{1,6})?$/',
            'currency' => 'required|in:syp,usd',
            'receipt_date' => 'required|date',
            'notes' => 'nullable|string',
        ], [
            'amount.min' => 'يجب أن يكون المبلغ أكبر من صفر',
            'currency.required' => 'يجب اختيار العملة',
            'order_id.required' => 'يجب اختيار رقم الطلبية'
        ]);

        $order = Order::with('items')->findOrFail($validated['order_id']);
        
        // Check if amount doesn't exceed remaining for the specific currency
        $remainingAmount = $validated['currency'] === 'syp' ? $order->remaining_amount_syp : $order->remaining_amount_usd;
        
        if ($validated['amount'] > $remainingAmount) {
            return back()->withErrors(['amount' => 'المبلغ يتجاوز المبلغ المتبقي بهذه العملة']);
        }
        
        if ($remainingAmount <= 0) {
            return back()->withErrors(['currency' => 'لا يوجد مبلغ متبقي بهذه العملة']);
        }

        Receipt::create($validated);

        return redirect()->route('receipts.index')->with('success', 'تم إنشاء سند القبض بنجاح');
    }

    public function show(Receipt $receipt)
    {
        $receipt->load('order');
        return view('receipts.show', compact('receipt'));
    }

    public function edit(Receipt $receipt)
    {
        $receipt->load('order.items');
        return view('receipts.edit', compact('receipt'));
    }

    public function update(Request $request, Receipt $receipt)
    {
        $validated = $request->validate([
            'amount' => 'required|numeric|min:0.000001|regex:/^\d+(\.\d{1,6})?$/',
            'receipt_date' => 'required|date',
            'notes' => 'nullable|string',
        ], [
            'amount.min' => 'يجب أن يكون المبلغ أكبر من صفر'
        ]);

        $order = $receipt->order;
        $order->load('items');
        $oldAmount = $receipt->amount;
        $newAmount = $validated['amount'];
        $currency = $receipt->currency;
        
        // حساب المبلغ المتبقي مع استثناء المقبوض الحالي
        $remainingAmount = $currency === 'syp' ? $order->remaining_amount_syp : $order->remaining_amount_usd;
        $availableAmount = $remainingAmount + $oldAmount;
        
        if ($newAmount > $availableAmount) {
            return back()->withErrors(['amount' => 'المبلغ يتجاوز المبلغ المتبقي']);
        }

        $receipt->update($validated);

        return redirect()->route('receipts.index')->with('success', 'تم تحديث سند القبض بنجاح');
    }

    public function destroy(Receipt $receipt)
    {
        $receipt->delete();
        return redirect()->route('receipts.index')->with('success', 'تم حذف سند القبض بنجاح');
    }
}