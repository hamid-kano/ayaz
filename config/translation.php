<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Translation Configuration
    |--------------------------------------------------------------------------
    |
    | This file contains configuration options for the application's
    | translation system.
    |
    */

    'default_locale' => 'ar',
    
    'fallback_locale' => 'ar',
    
    'supported_locales' => [
        'ar' => [
            'name' => 'العربية',
            'direction' => 'rtl',
            'flag' => '🇸🇦',
        ],
        'en' => [
            'name' => 'English',
            'direction' => 'ltr',
            'flag' => '🇺🇸',
        ],
    ],

    'date_formats' => [
        'ar' => [
            'short' => 'd/m/Y',
            'medium' => 'd M Y',
            'long' => 'd F Y',
            'full' => 'l، d F Y',
            'time' => 'H:i',
            'datetime' => 'd/m/Y H:i',
        ],
        'en' => [
            'short' => 'm/d/Y',
            'medium' => 'M d, Y',
            'long' => 'F d, Y',
            'full' => 'l, F d, Y',
            'time' => 'g:i A',
            'datetime' => 'm/d/Y g:i A',
        ],
    ],

    'number_formats' => [
        'ar' => [
            'decimal_separator' => '.',
            'thousands_separator' => ',',
            'currency_symbol' => 'ر.س',
            'currency_position' => 'after',
        ],
        'en' => [
            'decimal_separator' => '.',
            'thousands_separator' => ',',
            'currency_symbol' => '$',
            'currency_position' => 'before',
        ],
    ],

    'validation_messages' => [
        'use_custom' => true,
        'custom_attributes' => true,
    ],

];