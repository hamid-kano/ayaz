<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Validator;
use App\Helpers\TranslationHelper;

class TranslationServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        // Set default locale
        app()->setLocale('ar');
        
        // Custom validation messages
        Validator::extend('arabic_name', function ($attribute, $value, $parameters, $validator) {
            return preg_match('/^[\p{Arabic}\s]+$/u', $value);
        });

        Validator::replacer('arabic_name', function ($message, $attribute, $rule, $parameters) {
            return str_replace(':attribute', __('validation.attributes.' . $attribute), 'يجب أن يحتوي :attribute على أحرف عربية فقط');
        });

        // Add custom validation messages
        $this->addCustomValidationMessages();
    }

    /**
     * Add custom validation messages
     */
    private function addCustomValidationMessages()
    {
        // Custom validation messages are handled by the lang files
        // No need for custom replacers as Laravel will use the translation files automatically
    }
}