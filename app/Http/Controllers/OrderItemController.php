<?php

namespace App\Http\Controllers;

use App\Models\OrderItem;
use Illuminate\Http\Request;

class OrderItemController extends Controller
{
    public function store(Request $request)
    {
        $request->validate([
            'order_id' => 'required|exists:orders,id',
            'item_name' => 'required|string|max:255',
            'quantity' => 'required|integer|min:1',
            'price' => 'required|numeric|min:0',
            'currency' => 'required|in:syp,usd'
        ]);

        OrderItem::create($request->all());

        return redirect()->back()->with('success', 'تم إضافة المادة بنجاح');
    }

    public function update(Request $request, OrderItem $orderItem)
    {
        $request->validate([
            'item_name' => 'required|string|max:255',
            'quantity' => 'required|integer|min:1',
            'price' => 'required|numeric|min:0',
            'currency' => 'required|in:syp,usd'
        ]);

        $orderItem->update($request->only(['item_name', 'quantity', 'price', 'currency']));

        return redirect()->back()->with('success', 'تم تحديث المادة بنجاح');
    }

    public function destroy(OrderItem $orderItem)
    {
        $orderItem->delete();
        return redirect()->back()->with('success', 'تم حذف المادة بنجاح');
    }
}