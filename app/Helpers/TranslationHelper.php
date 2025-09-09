<?php

namespace App\Helpers;

use Illuminate\Support\Facades\Lang;

class TranslationHelper
{
    /**
     * Get translated validation message
     *
     * @param string $rule
     * @param string $attribute
     * @param array $parameters
     * @return string
     */
    public static function getValidationMessage($rule, $attribute, $parameters = [])
    {
        $attributeName = __("validation.attributes.{$attribute}", [], 'ar');
        
        if ($attributeName === "validation.attributes.{$attribute}") {
            $attributeName = $attribute;
        }
        
        $parameters['attribute'] = $attributeName;
        
        return __("validation.{$rule}", $parameters, 'ar');
    }

    /**
     * Get translated auth message
     *
     * @param string $key
     * @param array $parameters
     * @return string
     */
    public static function getAuthMessage($key, $parameters = [])
    {
        return __("auth.{$key}", $parameters, 'ar');
    }

    /**
     * Get translated general message
     *
     * @param string $key
     * @param array $parameters
     * @return string
     */
    public static function getMessage($key, $parameters = [])
    {
        return __("messages.{$key}", $parameters, 'ar');
    }

    /**
     * Get translated page content
     *
     * @param string $key
     * @param array $parameters
     * @return string
     */
    public static function getPageContent($key, $parameters = [])
    {
        return __("pages.{$key}", $parameters, 'ar');
    }

    /**
     * Get translated date content
     *
     * @param string $key
     * @param array $parameters
     * @return string
     */
    public static function getDateContent($key, $parameters = [])
    {
        return __("dates.{$key}", $parameters, 'ar');
    }

    /**
     * Format validation errors for Arabic display
     *
     * @param \Illuminate\Support\MessageBag $errors
     * @return array
     */
    public static function formatValidationErrors($errors)
    {
        $formattedErrors = [];
        
        foreach ($errors->all() as $error) {
            $formattedErrors[] = $error;
        }
        
        return $formattedErrors;
    }

    /**
     * Get all available translations for a given key
     *
     * @param string $key
     * @return array
     */
    public static function getAllTranslations($key)
    {
        return [
            'ar' => __($key, [], 'ar'),
            'en' => __($key, [], 'en'),
        ];
    }

    /**
     * Format amount with conditional decimal places
     *
     * @param float $amount
     * @return string
     */
    public static function formatAmount($amount)
    {
        // إذا كان الرقم صحيح (بدون كسور) أظهره بدون فاصلة
        if (floor($amount) == $amount) {
            return number_format($amount, 0, '', ',');
        }
        // إذا كان يحتوي على كسور أظهر رقمين بعد الفاصلة
        return number_format($amount, 2, '.', ',');
    }

    /**
     * Format amount for input fields (always 2 decimal places)
     *
     * @param float $amount
     * @return string
     */
    public static function formatAmountForInput($amount)
    {
        return number_format($amount, 2, '.', '');
    }

    /**
     * Parse amount from input (remove formatting)
     *
     * @param string $amount
     * @return float
     */
    public static function parseAmount($amount)
    {
        return floatval(str_replace(',', '', $amount));
    }
}