<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    use HasFactory;

    protected $fillable = [
        'order_number',
        'order_date',
        'customer_name',
        'customer_phone',
        'order_type',
        'order_details',
        'status',
        'delivery_date',
        'reviewer_name',
        'executor_id',
    ];

    protected $casts = [
        'order_date' => 'date',
        'delivery_date' => 'date',
    ];



    public function executor()
    {
        return $this->belongsTo(User::class, 'executor_id');
    }

    public function receipts()
    {
        return $this->hasMany(Receipt::class);
    }

    public function attachments()
    {
        return $this->morphMany(Attachment::class, 'attachable');
    }

    public function audioRecordings()
    {
        return $this->hasMany(AudioRecording::class);
    }

    public function items()
    {
        return $this->hasMany(OrderItem::class);
    }

    public function notifications()
    {
        return $this->hasMany(\App\Models\Notification::class, 'data->order_id', 'id');
    }

    public function getTotalPaidAttribute()
    {
        if ($this->currency === 'mixed') {
            return $this->receipts()->sum('amount');
        }
        return $this->receipts()->where('currency', $this->currency)->sum('amount');
    }

    public function getRemainingAmountAttribute()
    {
        return $this->total_cost - $this->total_paid;
    }
    
    public function getTotalCostAttribute()
    {
        if ($this->items->isEmpty()) {
            return $this->cost ?? 0;
        }
        
        $totalSyp = $this->items->where('currency', 'syp')->sum(function($item) {
            return $item->quantity * $item->price;
        });
        
        $totalUsd = $this->items->where('currency', 'usd')->sum(function($item) {
            return $item->quantity * $item->price;
        });
        
        // إرجاع المبلغ الأكبر أو الوحيد
        if ($totalSyp > 0 && $totalUsd > 0) {
            return max($totalSyp, $totalUsd);
        }
        
        return $totalSyp + $totalUsd;
    }
    
    public function getCurrencyAttribute()
    {
        if ($this->items->isEmpty()) {
            return $this->attributes['currency'] ?? 'syp';
        }
        
        $hasSyp = $this->items->where('currency', 'syp')->count() > 0;
        $hasUsd = $this->items->where('currency', 'usd')->count() > 0;
        
        if ($hasSyp && $hasUsd) {
            return 'mixed';
        }
        
        return $hasUsd ? 'usd' : 'syp';
    }

    public function getStatusColorAttribute()
    {
        return match($this->status) {
            'new' => 'blue',
            'in-progress' => 'yellow',
            'delivered' => 'green',
            'cancelled' => 'red',
            default => 'gray'
        };
    }
}