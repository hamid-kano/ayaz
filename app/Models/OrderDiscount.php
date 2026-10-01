<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OrderDiscount extends Model
{
    protected $fillable = [
        'order_id',
        'amount',
        'currency',
        'reason',
        'created_by',
    ];

    protected $casts = [
        'amount' => 'decimal:6',
    ];

    public function order()
    {
        return $this->belongsTo(Order::class);
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}
