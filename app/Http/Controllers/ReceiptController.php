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

        if ($request->has('search')) {
            $search = $request->search;
            $query->whereHas('order', function ($q) use ($search) {
                $q->where('order_number', 'like', "%{$search}%")
                    ->orWhere('customer_name', 'like', "%{$search}%");
            })->orWhere('notes', 'like', "%{$search}%");
        }

        $receipts = $query->latest()->get();
        $totalAmount = $receipts->sum('amount');
        
        return view('receipts.index', compact('receipts', 'totalAmount'));
    }

    public function create(Request $request)
    {
        $orders = Order::whereIn('status', ['new', 'in-progress'])
            ->with('receipts')
            ->get()
            ->filter(function($order) {
                return $order->remaining_amount > 0;
            });
        
        $selectedOrderId = $request->get('order_id');
            
        return view('receipts.create', compact('orders', 'selectedOrderId'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'order_id' => 'required|exists:orders,id',
            'amount' => 'required|numeric|min:0.01',
            'currency' => 'required|in:syp,usd',
            'receipt_date' => 'required|date',
            'notes' => 'nullable|string',
        ], [
            'amount.min' => 'يجب أن يكون المبلغ أكبر من صفر',
            'currency.required' => 'يجب اختيار العملة',
            'order_id.required' => 'يجب اختيار رقم الطلبية'
        ]);

        $order = Order::findOrFail($validated['order_id']);
        
        // Check currency matches order currency
        if ($validated['currency'] !== $order->currency) {
            return back()->withErrors(['currency' => 'يجب أن تكون العملة مطابقة لعملة الطلبية']);
        }
        
        // Check if amount doesn't exceed remaining
        if ($validated['amount'] > $order->remaining_amount) {
            return back()->withErrors(['amount' => 'المبلغ يتجاوز المبلغ المتبقي']);
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
        $orders = Order::whereIn('status', ['new', 'in-progress'])
            ->with('receipts')
            ->get()
            ->filter(function($order) use ($receipt) {
                return $order->id === $receipt->order_id || $order->remaining_amount > 0;
            });
            
        return view('receipts.edit', compact('receipt', 'orders'));
    }

    public function update(Request $request, Receipt $receipt)
    {
        $validated = $request->validate([
            'amount' => 'required|numeric|min:0.01',
            'receipt_date' => 'required|date',
            'notes' => 'nullable|string',
        ], [
            'amount.min' => 'يجب أن يكون المبلغ أكبر من صفر'
        ]);

        $order = $receipt->order;
        $oldAmount = $receipt->amount;
        $newAmount = $validated['amount'];
        
        // حساب المبلغ المتبقي مع استثناء المقبوض الحالي
        $availableAmount = $order->remaining_amount + $oldAmount;
        
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