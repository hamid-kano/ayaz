<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AudioRecording extends Model
{
    use HasFactory;

    protected $fillable = [
        'order_id',
        'file_name',
        'file_path',
        'duration',
    ];

    public function order()
    {
        return $this->belongsTo(Order::class);
    }

    public function getDurationFormattedAttribute()
    {
        if (!$this->duration) return '00:00';
        
        $minutes = floor($this->duration / 60);
        $seconds = $this->duration % 60;
        
        return sprintf('%02d:%02d', $minutes, $seconds);
    }
}