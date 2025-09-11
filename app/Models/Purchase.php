<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Purchase extends Model
{
    use HasFactory;

    protected $fillable = [
        'purchase_number',
        'amount',
        'currency',
        'purchase_date',
        'status',
        'details',
        'supplier',
    ];

    protected $casts = [
        'purchase_date' => 'date',
        'amount' => 'decimal:6',
    ];

    public function attachments()
    {
        return $this->morphMany(Attachment::class, 'attachable');
    }

    public function getStatusColorAttribute()
    {
        return match($this->status) {
            'cash' => 'green',
            'debt' => 'red',
            default => 'gray'
        };
    }
}