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
        'is_urgent',
        'delivery_date',
        'reviewer_name',
        'executor_id',
    ];

    protected $casts = [
        'order_date' => 'date',
        'delivery_date' => 'datetime',
        'is_urgent' => 'boolean',
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

    public function discounts()
    {
        return $this->hasMany(OrderDiscount::class);
    }

    // الحالات التي يُمنع فيها تعديل الخصومات
    public const DISCOUNT_LOCKED_STATUSES = ['archived', 'cancelled'];

    public function discountsLocked(): bool
    {
        return in_array($this->status, self::DISCOUNT_LOCKED_STATUSES, true);
    }

    public function notifications()
    {
        return $this->hasMany(\App\Models\Notification::class, 'data->order_id', 'id');
    }

    public function getTotalPaidSypAttribute()
    {
        return $this->receipts()->where('currency', 'syp')->sum('amount');
    }
    
    public function getTotalPaidUsdAttribute()
    {
        return $this->receipts()->where('currency', 'usd')->sum('amount');
    }
    


    public function getRemainingAmountSypAttribute()
    {
        return $this->total_cost_syp - $this->total_paid_syp;
    }
    
    public function getRemainingAmountUsdAttribute()
    {
        return $this->total_cost_usd - $this->total_paid_usd;
    }
    

    
    // مجموع المواد قبل الخصم
    public function getSubtotalSypAttribute()
    {
        return $this->items->where('currency', 'syp')->sum(fn($item) => $item->quantity * $item->price);
    }

    public function getSubtotalUsdAttribute()
    {
        return $this->items->where('currency', 'usd')->sum(fn($item) => $item->quantity * $item->price);
    }

    // مجموع الخصومات لكل عملة
    public function getTotalDiscountSypAttribute()
    {
        return $this->discounts->where('currency', 'syp')->sum('amount');
    }

    public function getTotalDiscountUsdAttribute()
    {
        return $this->discounts->where('currency', 'usd')->sum('amount');
    }

    // الصافي بعد الخصم: هو المبلغ المستحق على الزبون
    public function getTotalCostSypAttribute()
    {
        return $this->subtotal_syp - $this->total_discount_syp;
    }

    public function getTotalCostUsdAttribute()
    {
        return $this->subtotal_usd - $this->total_discount_usd;
    }
    

    


    public function getStatusColorAttribute()
    {
        return match($this->status) {
            'new' => 'blue',
            'in-progress' => 'yellow',
            'ready' => 'orange',
            'delivered' => 'green',
            'archived' => 'purple',
            'cancelled' => 'red',
            default => 'gray'
        };
    }

    public function canBeArchived()
    {
        return $this->status === 'delivered';
    }

    public function getArchiveFolderAttribute()
    {
        return $this->order_date->format('Y-m');
    }
}