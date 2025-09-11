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
     * Format amount with conditional decimal places (up to 6)
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
        // إذا كان يحتوي على كسور أظهر حتى 6 أرقام بعد الفاصلة مع إزالة الأصفار الزائدة
        return rtrim(rtrim(number_format($amount, 6, '.', ','), '0'), '.');
    }

    /**
     * Format amount for input fields (up to 6 decimal places)
     *
     * @param float $amount
     * @return string
     */
    public static function formatAmountForInput($amount)
    {
        return rtrim(rtrim(number_format($amount, 6, '.', ''), '0'), '.');
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

    /**
     * Convert number to written amount in Arabic
     *
     * @param float $amount
     * @param string $currency 'SYP' for Syrian Pound, 'USD' for US Dollar
     * @return string
     */
    public static function numberToWords($amount, $currency = 'SYP')
    {
        $ones = ['', 'واحد', 'اثنان', 'ثلاثة', 'أربعة', 'خمسة', 'ستة', 'سبعة', 'ثمانية', 'تسعة'];
        $tens = ['', '', 'عشرون', 'ثلاثون', 'أربعون', 'خمسون', 'ستون', 'سبعون', 'ثمانون', 'تسعون'];
        $teens = ['عشرة', 'أحد عشر', 'اثنا عشر', 'ثلاثة عشر', 'أربعة عشر', 'خمسة عشر', 'ستة عشر', 'سبعة عشر', 'ثمانية عشر', 'تسعة عشر'];
        $hundreds = ['', 'مائة', 'مائتان', 'ثلاثمائة', 'أربعمائة', 'خمسمائة', 'ستمائة', 'سبعمائة', 'ثمانمائة', 'تسعمائة'];
        $thousands = ['', 'ألف', 'مليون', 'مليار'];

        if ($amount == 0) return 'صفر';
        
        $integerPart = floor($amount);
        $decimalPart = round(($amount - $integerPart) * 100);
        
        $result = self::convertInteger($integerPart, $ones, $tens, $teens, $hundreds, $thousands);
        
        if ($decimalPart > 0) {
            $decimalUnit = $currency === 'USD' ? 'سنت' : 'قرش';
            $result .= ' و ' . self::convertInteger($decimalPart, $ones, $tens, $teens, $hundreds, $thousands) . ' ' . $decimalUnit;
        }
        
        return $result;
    }

    private static function convertInteger($number, $ones, $tens, $teens, $hundreds, $thousands)
    {
        if ($number == 0) return '';
        if ($number < 10) return $ones[$number];
        if ($number < 20) return $teens[$number - 10];
        if ($number < 100) {
            $tensDigit = floor($number / 10);
            $onesDigit = $number % 10;
            if ($onesDigit == 0) {
                return $tens[$tensDigit];
            }
            return $ones[$onesDigit] . ' و' . $tens[$tensDigit];
        }
        if ($number < 1000) {
            $hundredsDigit = floor($number / 100);
            $remainder = $number % 100;
            $result = $hundreds[$hundredsDigit];
            if ($remainder > 0) {
                $result .= ' ' . self::convertInteger($remainder, $ones, $tens, $teens, $hundreds, $thousands);
            }
            return $result;
        }
        
        $result = '';
        $scale = 0;
        
        while ($number > 0) {
            $chunk = $number % 1000;
            if ($chunk != 0) {
                $chunkText = self::convertInteger($chunk, $ones, $tens, $teens, $hundreds, $thousands);
                if ($scale > 0) {
                    $chunkText .= ' ' . $thousands[$scale];
                }
                $result = $chunkText . ($result ? ' ' . $result : '');
            }
            $number = floor($number / 1000);
            $scale++;
        }
        
        return $result;
    }
}