<?php

namespace App\Helpers;

use App\Models\SystemSetting;

class SystemHelper
{
    /**
     * Получить название сайта
     */
    public static function getSiteName()
    {
        return SystemSetting::getValue('site_name', 'CRM Studio');
    }

    /**
     * Получить описание сайта
     */
    public static function getSiteDescription()
    {
        return SystemSetting::getValue('site_description', 'Система управления клиентами и записями');
    }

    /**
     * Получить email администратора
     */
    public static function getAdminEmail()
    {
        return SystemSetting::getValue('admin_email', 'admin@example.com');
    }

    /**
     * Получить часовой пояс
     */
    public static function getTimezone()
    {
        return SystemSetting::getValue('timezone', 'Europe/Moscow');
    }

    /**
     * Получить путь к логотипу лендинга
     */
    public static function getLandingLogo()
    {
        return SystemSetting::getValue('landing_logo');
    }

    /**
     * Получить путь к фавикону
     */
    public static function getFavicon()
    {
        return SystemSetting::getValue('favicon');
    }

    /**
     * Проверить, загружен ли логотип
     */
    public static function hasLandingLogo()
    {
        return !empty(self::getLandingLogo());
    }

    /**
     * Проверить, загружен ли фавикон
     */
    public static function hasFavicon()
    {
        return !empty(self::getFavicon());
    }
}



