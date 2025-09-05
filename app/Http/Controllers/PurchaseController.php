<?php

namespace App\Http\Controllers;

use App\Models\Purchase;
use Illuminate\Http\Request;

class PurchaseController extends Controller
{
    public function index()
    {
        $purchases = Purchase::with('attachments')->latest()->get();
        return view('purchases.index', compact('purchases'));
    }

    public function create()
    {
        return view('purchases.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'amount' => 'required|numeric|min:0',
            'currency' => 'required|in:syp,usd',
            'purchase_date' => 'required|date',
            'status' => 'required|in:cash,debt',
            'details' => 'required|string',
            'supplier' => 'required|string|max:255',
        ]);

        $validated['purchase_number'] = 'PUR-' . time();

        $purchase = Purchase::create($validated);

        // Handle file uploads
        if ($request->hasFile('attachments')) {
            foreach ($request->file('attachments') as $file) {
                $fileName = time() . '_' . $file->getClientOriginalName();
                $file->move(public_path('purchase_attachments'), $fileName);
                
                $purchase->attachments()->create([
                    'file_name' => $file->getClientOriginalName(),
                    'file_path' => 'purchase_attachments/' . $fileName,
                    'file_type' => $file->getClientMimeType(),
                    'file_size' => $file->getSize(),
                ]);
            }
        }

        return redirect()->route('purchases.index')->with('success', 'تم إنشاء المشترى بنجاح');
    }

    public function show(Purchase $purchase)
    {
        $purchase->load('attachments');
        return view('purchases.show', compact('purchase'));
    }

    public function edit(Purchase $purchase)
    {
        return view('purchases.edit', compact('purchase'));
    }

    public function update(Request $request, Purchase $purchase)
    {
        $validated = $request->validate([
            'amount' => 'required|numeric|min:0',
            'currency' => 'required|in:syp,usd',
            'purchase_date' => 'required|date',
            'status' => 'required|in:cash,debt',
            'details' => 'required|string',
            'supplier' => 'required|string|max:255',
        ]);

        $purchase->update($validated);

        return redirect()->route('purchases.index')->with('success', 'تم تحديث المشترى بنجاح');
    }

    public function destroy(Purchase $purchase)
    {
        $purchase->delete();
        return redirect()->route('purchases.index')->with('success', 'تم حذف المشترى بنجاح');
    }

    public function debts()
    {
        $purchases = Purchase::where('status', 'debt')->latest()->get();
        
        $totalUsd = $purchases->where('currency', 'usd')->sum('amount');
        $totalSyp = $purchases->where('currency', 'syp')->sum('amount');

        return view('purchases.debts', compact('purchases', 'totalUsd', 'totalSyp'));
    }
}