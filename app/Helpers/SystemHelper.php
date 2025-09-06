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

    /**
     * Удалить h1 теги и дублирующие заголовки из контента
     */
    public static function removeH1FromContent($content, $articleTitle = null)
    {
        // Удаляем только h1 теги
        $content = preg_replace('/<h1[^>]*>.*?<\/h1>/is', '', $content);
        
        // Удаляем только strong теги с data-start атрибутами (TinyMCE выделения) в начале контента
        // Проверяем только первые 300 символов, чтобы не удалить важный контент
        $firstPart = substr($content, 0, 300);
        if (preg_match('/<strong[^>]*data-start[^>]*>.*?<\/strong>/is', $firstPart)) {
            $content = preg_replace('/<strong[^>]*data-start[^>]*>.*?<\/strong>/is', '', $content, 1); // Удаляем только первое вхождение
        }
        
        // Удаляем пустые параграфы, которые могли остаться
        $content = preg_replace('/<p[^>]*>\s*<\/p>/is', '', $content);
        
        // Удаляем пустые div'ы, которые могли остаться
        $content = preg_replace('/<div[^>]*>\s*<\/div>/is', '', $content);
        
        // Удаляем множественные переносы строк
        $content = preg_replace('/\n\s*\n\s*\n/', "\n\n", $content);
        
        return trim($content);
    }
}



