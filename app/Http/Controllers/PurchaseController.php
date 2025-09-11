<?php

namespace App\Http\Controllers;

use App\Models\Purchase;
use Illuminate\Http\Request;

class PurchaseController extends Controller
{
    public function index(Request $request)
    {
        $query = Purchase::with('attachments');

        if ($request->has('status') && $request->status != '') {
            $query->where('status', $request->status);
        }

        if ($request->has('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('purchase_number', 'like', "%{$search}%")
                    ->orWhere('supplier', 'like', "%{$search}%")
                    ->orWhere('details', 'like', "%{$search}%");
            });
        }

        $purchases = $query->latest()->get();
        return view('purchases.index', compact('purchases'));
    }

    public function create()
    {
        return view('purchases.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'amount' => 'required|numeric|min:0|regex:/^\d+(\.\d{1,6})?$/',
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
                $purchasePath = 'purchase_attachments/' . $purchase->id;
                
                if (!file_exists(public_path($purchasePath))) {
                    mkdir(public_path($purchasePath), 0755, true);
                }
                
                $file->move(public_path($purchasePath), $fileName);
                
                $purchase->attachments()->create([
                    'file_name' => $file->getClientOriginalName(),
                    'file_path' => $purchasePath . '/' . $fileName,
                    'file_type' => $file->getClientMimeType(),
                    'file_size' => filesize(public_path($purchasePath . '/' . $fileName)),
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
        $purchase->load('attachments');
        return view('purchases.edit', compact('purchase'));
    }

    public function update(Request $request, Purchase $purchase)
    {
        $validated = $request->validate([
            'amount' => 'required|numeric|min:0|regex:/^\d+(\.\d{1,6})?$/',
            'currency' => 'required|in:syp,usd',
            'purchase_date' => 'required|date',
            'status' => 'required|in:cash,debt',
            'details' => 'required|string',
            'supplier' => 'required|string|max:255',
        ]);

        $purchase->update($validated);

        // Handle new file uploads
        if ($request->hasFile('attachments')) {
            foreach ($request->file('attachments') as $file) {
                $fileName = time() . '_' . $file->getClientOriginalName();
                $purchasePath = 'purchase_attachments/' . $purchase->id;
                
                if (!file_exists(public_path($purchasePath))) {
                    mkdir(public_path($purchasePath), 0755, true);
                }
                
                $file->move(public_path($purchasePath), $fileName);
                
                $purchase->attachments()->create([
                    'file_name' => $file->getClientOriginalName(),
                    'file_path' => $purchasePath . '/' . $fileName,
                    'file_type' => $file->getClientMimeType(),
                    'file_size' => filesize(public_path($purchasePath . '/' . $fileName)),
                ]);
            }
        }

        return redirect()->route('purchases.index')->with('success', 'تم تحديث المشترى بنجاح');
    }

    public function destroy(Purchase $purchase)
    {
        // حذف المرفقات من الملفات
        foreach ($purchase->attachments as $attachment) {
            if (file_exists(public_path($attachment->file_path))) {
                unlink(public_path($attachment->file_path));
            }
        }

        $purchase->delete();
        return redirect()->route('purchases.index')->with('success', 'تم حذف المشترى بنجاح');
    }

    public function debts(Request $request)
    {
        $query = Purchase::where('status', 'debt');

        if ($request->has('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('purchase_number', 'like', "%{$search}%")
                    ->orWhere('supplier', 'like', "%{$search}%")
                    ->orWhere('details', 'like', "%{$search}%");
            });
        }

        $purchases = $query->latest()->get();
        
        $totalUsd = $purchases->where('currency', 'usd')->sum('amount');
        $totalSyp = $purchases->where('currency', 'syp')->sum('amount');

        return view('purchases.debts', compact('purchases', 'totalUsd', 'totalSyp'));
    }

    public function deleteAttachment($id)
    {
        $attachment = \App\Models\Attachment::findOrFail($id);
        
        // Delete file from storage
        if (file_exists(public_path($attachment->file_path))) {
            unlink(public_path($attachment->file_path));
        }
        
        $attachment->delete();
        
        return response()->json(['success' => true]);
    }
}