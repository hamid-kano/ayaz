<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Setting extends Model
{
    protected $fillable = ['key', 'value'];

    public static function get($key, $default = null)
    {
        $setting = self::where('key', $key)->first();
        if (!$setting) {
            return $default;
        }
        
        // تحويل القيم النصية إلى boolean
        if ($setting->value === 'true' || $setting->value === '1') {
            return true;
        }
        if ($setting->value === 'false' || $setting->value === '0') {
            return false;
        }
        
        return $setting->value;
    }

    public static function set($key, $value)
    {
        // تحويل boolean إلى string
        if (is_bool($value)) {
            $value = $value ? 'true' : 'false';
        }
        
        return self::updateOrCreate(
            ['key' => $key],
            ['value' => $value]
        );
    }
}