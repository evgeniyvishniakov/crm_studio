<?php

namespace App\Http\Controllers\Landing;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class PageController extends Controller
{
    /**
     * Главная страница
     */
    public function index($lang = null)
    {
        // Устанавливаем язык если передан параметр
        if ($lang) {
            \App\Helpers\LanguageHelper::setLanguage($lang);
        } else {
            // Для fallback маршрута устанавливаем украинский язык по умолчанию
            \App\Helpers\LanguageHelper::setLanguage('ua');
        }
        
        return view('landing.pages.index');
    }
    
    /**
     * Страница контактов
     */
    public function contact($lang = null)
    {
        // Устанавливаем язык если передан параметр
        if ($lang) {
            \App\Helpers\LanguageHelper::setLanguage($lang);
        } else {
            // Для fallback маршрута устанавливаем украинский язык по умолчанию
            \App\Helpers\LanguageHelper::setLanguage('ua');
        }
        
        return view('landing.pages.contact');
    }
    
    /**
     * Политика конфиденциальности
     */
    public function privacy($lang = null)
    {
        // Устанавливаем язык если передан параметр
        if ($lang) {
            \App\Helpers\LanguageHelper::setLanguage($lang);
        } else {
            // Для fallback маршрута устанавливаем украинский язык по умолчанию
            \App\Helpers\LanguageHelper::setLanguage('ua');
        }
        
        return view('landing.pages.privacy');
    }
    
    /**
     * Условия использования
     */
    public function terms($lang = null)
    {
        // Устанавливаем язык если передан параметр
        if ($lang) {
            \App\Helpers\LanguageHelper::setLanguage($lang);
        } else {
            // Для fallback маршрута устанавливаем украинский язык по умолчанию
            \App\Helpers\LanguageHelper::setLanguage('ua');
        }
        
        return view('landing.pages.terms');
    }
}
