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
        'order_type',
        'order_details',
        'cost',
        'currency',
        'status',
        'delivery_date',
        'reviewer_name',
        'executor_id',
    ];

    protected $casts = [
        'order_date' => 'date',
        'delivery_date' => 'date',
        'cost' => 'decimal:2',
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

    public function notifications()
    {
        return $this->hasMany(\App\Models\Notification::class, 'data->order_id', 'id');
    }

    public function getTotalPaidAttribute()
    {
        return $this->receipts()->where('currency', $this->currency)->sum('amount');
    }

    public function getRemainingAmountAttribute()
    {
        return $this->cost - $this->total_paid;
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