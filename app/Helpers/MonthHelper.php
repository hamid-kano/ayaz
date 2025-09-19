<?php

namespace App\Helpers;

class MonthHelper
{
    public static function getArabicMonthName($monthNumber)
    {
        $months = [
            1 => 'كانون الثاني',
            2 => 'شباط',
            3 => 'آذار',
            4 => 'نيسان',
            5 => 'أيار',
            6 => 'حزيران',
            7 => 'تموز',
            8 => 'آب',
            9 => 'أيلول',
            10 => 'تشرين الأول',
            11 => 'تشرين الثاني',
            12 => 'كانون الأول'
        ];

        return $months[$monthNumber] ?? '';
    }

    public static function formatMonthYear($yearMonth)
    {
        [$year, $month] = explode('-', $yearMonth);
        $arabicMonth = self::getArabicMonthName((int)$month);
        return $arabicMonth . ' ' . $year;
    }
}