<?php

namespace App\Helpers;

class TimeHelper
{
    /**
     * Форматировать длительность в минутах в читаемый формат
     */
    public static function formatDuration($minutes)
    {
        if ($minutes < 60) {
            return __('messages.duration_prefix') . $minutes . ' ' . __('messages.minute');
        } else {
            $hours = intval($minutes / 60);
            $remainingMinutes = $minutes % 60;
            
            if ($remainingMinutes == 0) {
                return __('messages.duration_prefix') . $hours . ' ' . self::getHoursText($hours);
            } else {
                $hourText = $hours . ' ' . self::getHoursText($hours);
                $minuteText = $remainingMinutes . ' ' . self::getMinutesText($remainingMinutes);
                return __('messages.duration_prefix') . $hourText . ' ' . $minuteText;
            }
        }
    }

    /**
     * Получить правильное склонение для часов
     */
    private static function getHoursText($hours)
    {
        $locale = app()->getLocale();
        
        if ($locale === 'ru') {
            // Русские правила склонения
            if ($hours % 10 === 1 && $hours % 100 !== 11) {
                return __('messages.hour');
            } elseif (in_array($hours % 10, [2, 3, 4]) && !in_array($hours % 100, [12, 13, 14])) {
                return __('messages.hours');
            } else {
                return __('messages.hours_many');
            }
        } else {
            // Украинские правила склонения
            if ($hours == 1) {
                return __('messages.hour');
            } elseif ($hours >= 2 && $hours <= 4) {
                return __('messages.hours');
            } else {
                return __('messages.hours_many');
            }
        }
    }

    /**
     * Получить правильное склонение для минут
     */
    private static function getMinutesText($minutes)
    {
        return __('messages.minute');
    }

    /**
     * Форматировать время в формате H:i
     */
    public static function formatTime($time)
    {
        if (is_string($time)) {
            return date('H:i', strtotime($time));
        }
        return $time;
    }

    /**
     * Форматировать дату в читаемый формат
     */
    public static function formatDate($date, $format = 'd.m.Y')
    {
        if (is_string($date)) {
            return date($format, strtotime($date));
        }
        return $date->format($format);
    }
} 