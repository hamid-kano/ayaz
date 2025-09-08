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
     * Format amount without decimal places
     *
     * @param float $amount
     * @return string
     */
    public static function formatAmount($amount)
    {
        return number_format($amount, 0, '', ',');
    }
}