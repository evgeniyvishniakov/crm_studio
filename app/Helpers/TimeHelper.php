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
            return '~' . $minutes . ' мин';
        } else {
            $hours = intval($minutes / 60);
            $remainingMinutes = $minutes % 60;
            
            if ($remainingMinutes == 0) {
                return '~' . $hours . ' ' . self::getHoursText($hours);
            } else {
                $hourText = $hours . ' ' . self::getHoursText($hours);
                $minuteText = $remainingMinutes . ' ' . self::getMinutesText($remainingMinutes);
                return '~' . $hourText . ' ' . $minuteText;
            }
        }
    }

    /**
     * Получить правильное склонение для часов
     */
    private static function getHoursText($hours)
    {
        if ($hours == 1) {
            return 'час';
        } elseif ($hours < 5) {
            return 'часа';
        } else {
            return 'часов';
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