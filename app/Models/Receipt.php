<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Receipt extends Model
{
    use HasFactory;

    protected $fillable = [
        'order_id',
        'amount',
        'currency',
        'receipt_date',
        'notes',
    ];

    protected $casts = [
        'receipt_date' => 'date',
        'amount' => 'decimal:6',
    ];

    public function order()
    {
        return $this->belongsTo(Order::class);
    }
}