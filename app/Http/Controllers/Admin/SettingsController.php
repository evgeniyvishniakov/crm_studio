<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\SystemSetting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class SettingsController extends Controller
{
    /**
     * Показать страницу настроек
     */
    public function index()
    {
        $settings = SystemSetting::getSettings();
        return view('admin.settings.index', compact('settings'));
    }

    /**
     * Обновить настройки
     */
    public function update(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'site_name' => 'required|string|max:255',
            'site_description' => 'nullable|string|max:1000',
            'admin_email' => 'required|email|max:255',
            'timezone' => 'required|string|max:100',
            'landing_logo' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            'favicon' => 'nullable|image|mimes:ico,png,jpg|max:1024',
        ], [
            'site_name.required' => 'Поле "Название сайта" обязательно для заполнения',
            'admin_email.required' => 'Поле "Email администратора" обязательно для заполнения',
            'admin_email.email' => 'Поле "Email администратора" должно быть корректным email адресом',
            'timezone.required' => 'Поле "Часовой пояс" обязательно для заполнения',
            'landing_logo.image' => 'Файл логотипа должен быть изображением',
            'landing_logo.mimes' => 'Логотип должен быть в формате: JPEG, PNG, GIF или SVG',
            'landing_logo.max' => 'Размер логотипа не должен превышать 2MB',
            'favicon.image' => 'Файл фавикона должен быть изображением',
            'favicon.mimes' => 'Фавикон должен быть в формате: ICO, PNG или JPG',
            'favicon.max' => 'Размер фавикона не должен превышать 1MB',
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        // Получаем текущие настройки
        $settings = SystemSetting::getSettings();
        
        // Подготавливаем данные для обновления
        $data = [
            'site_name' => $request->site_name,
            'site_description' => $request->site_description,
            'admin_email' => $request->admin_email,
            'timezone' => $request->timezone,
        ];
        
        // Обработка загрузки логотипа
        if ($request->hasFile('landing_logo')) {
            try {
                // Удаляем старый логотип, если он существует
                if ($settings->landing_logo) {
                    $this->deleteImageFile($settings->landing_logo);
                }
                
                $logoPath = $request->file('landing_logo')->store('public/logos');
                $data['landing_logo'] = Storage::url($logoPath);
            } catch (\Exception $e) {
                return back()->withErrors(['landing_logo' => 'Ошибка при загрузке логотипа: ' . $e->getMessage()])->withInput();
            }
        }
        
        // Обработка загрузки фавикона
        if ($request->hasFile('favicon')) {
            try {
                // Удаляем старый фавикон, если он существует
                if ($settings->favicon) {
                    $this->deleteImageFile($settings->favicon);
                }
                
                $faviconPath = $request->file('favicon')->store('public/favicons');
                $data['favicon'] = Storage::url($faviconPath);
            } catch (\Exception $e) {
                return back()->withErrors(['favicon' => 'Ошибка при загрузке фавикона: ' . $e->getMessage()])->withInput();
            }
        }

        try {
            $settings->update($data);
            return back()->with('success', 'Настройки успешно обновлены!');
        } catch (\Exception $e) {
            return back()->withErrors(['general' => 'Ошибка при сохранении настроек: ' . $e->getMessage()])->withInput();
        }
    }

    /**
     * Удалить изображение
     */
    public function removeImage(Request $request, $type)
    {
        $settings = SystemSetting::getSettings();
        
        if ($type === 'logo' && $settings->landing_logo) {
            try {
                $this->deleteImageFile($settings->landing_logo);
                $settings->update(['landing_logo' => null]);
                return back()->with('success', 'Логотип успешно удален!');
            } catch (\Exception $e) {
                return back()->withErrors(['general' => 'Ошибка при удалении логотипа: ' . $e->getMessage()]);
            }
        }
        
        if ($type === 'favicon' && $settings->favicon) {
            try {
                $this->deleteImageFile($settings->favicon);
                $settings->update(['favicon' => null]);
                return back()->with('success', 'Фавикон успешно удален!');
            } catch (\Exception $e) {
                return back()->withErrors(['general' => 'Ошибка при удалении фавикона: ' . $e->getMessage()]);
            }
        }
        
        return back()->withErrors(['general' => 'Изображение не найдено']);
    }

    /**
     * Удалить файл изображения из storage
     */
    private function deleteImage($imageUrl)
    {
        if (empty($imageUrl)) {
            return;
        }
        
        // Извлекаем путь к файлу из URL
        $path = str_replace('/storage/', 'public/', $imageUrl);
        
        if (Storage::exists($path)) {
            Storage::delete($path);
        }
    }
}
