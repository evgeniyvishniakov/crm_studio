<?php

declare(strict_types=1);

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Admin\Project;
use App\Helpers\CurrencyHelper;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Gate;

class SettingsController extends Controller
{
    public function index()
    {
        // Получаем проект текущего пользователя (админ/руководитель)
        $user = Auth::user();
        // Предполагаем, что у пользователя есть project_id или связь project
        $project = Project::with(['currency', 'language'])->where('id', $user->project_id ?? null)->first();
        
        // Отладка
        \Log::info('Settings page loaded', [
            'user_id' => $user->id,
            'user_email' => $user->email,
            'project_id' => $user->project_id,
            'project_name' => $project ? $project->name : null,
            'project_currency_id' => $project ? $project->currency_id : null,
            'project_currency_code' => $project && $project->currency ? $project->currency->code : null,
            'project_language_id' => $project ? $project->language_id : null,
            'project_language_name' => $project && $project->language ? $project->language->name : null,
        ]);
        
        // Можно добавить Gate::authorize('view-settings', $project); для ограничения доступа
        return view('client.settings.index', compact('project'));
    }

    public function update(Request $request)
    {
        $user = Auth::user();
        $project = Project::with('currency')->where('id', $user->project_id ?? null)->firstOrFail();

        // Получаем доступные валюты из базы данных
        $availableCurrencyIds = \App\Models\Currency::getActive()->pluck('id')->toArray();

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'project_name' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'phone' => 'nullable|string|max:50',
            'address' => 'nullable|string|max:255',
            'website' => 'nullable|url|max:255',
            'instagram' => 'nullable|url|max:255',
            'facebook' => 'nullable|url|max:255',
            'tiktok' => 'nullable|url|max:255',
            'logo' => 'nullable|image|max:2048',
            'language_id' => 'nullable|integer|exists:languages,id',
            'booking_language_id' => 'nullable|integer|exists:languages,id',
            'currency_id' => 'nullable|integer|in:' . implode(',', $availableCurrencyIds),
            'map_url' => 'nullable|url|max:500',
            'map_latitude' => 'nullable|string|max:20',
            'map_longitude' => 'nullable|string|max:20',
            'map_zoom' => 'nullable|integer|min:1|max:20',
            'about' => 'nullable|string|max:1000',
        ], [
            'map_url.url' => __('messages.map_url_invalid'),
            'map_zoom.integer' => __('messages.map_zoom_invalid'),
            'map_zoom.min' => __('messages.map_zoom_invalid'),
            'map_zoom.max' => __('messages.map_zoom_invalid'),
            'about.max' => __('messages.about_max_length'),
        ]);

        // Обновление полей
        $project->fill($validated);

        // Обработка загрузки логотипа
        if ($request->hasFile('logo')) {
            $logoPath = $request->file('logo')->store('public/project_logos');
            $project->logo = Storage::url($logoPath);
        }

        // Обработка ссылки на карту
        if (!empty($validated['map_url'])) {
            $coordinates = $this->extractCoordinatesFromMapUrl($validated['map_url']);
            if ($coordinates) {
                $validated['map_latitude'] = $coordinates['latitude'];
                $validated['map_longitude'] = $coordinates['longitude'];
                $validated['map_zoom'] = $coordinates['zoom'] ?? 15;
            }
        }

        $project->save();

        // Если это AJAX запрос, возвращаем JSON
        if ($request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => __('messages.changes_successfully_saved'),
                'currency' => $project->currency ? $project->currency->code : null
            ]);
        }

        return redirect()->back()->with('success', __('messages.changes_successfully_saved'));
    }

    /**
     * Извлечь координаты из ссылки Google Maps
     */
    private function extractCoordinatesFromMapUrl($url)
    {
        // Поддержка различных форматов ссылок Google Maps
        
        // Формат: https://maps.app.goo.gl/UMeU52GP5ZWVxx4x5
        if (preg_match('/maps\.app\.goo\.gl\/([a-zA-Z0-9]+)/', $url, $matches)) {
            // Для коротких ссылок нужно будет сделать запрос для получения полной ссылки
            // Пока что возвращаем null, так как нужен API ключ для расшифровки
            return null;
        }
        
        // Формат: https://www.google.com/maps?q=55.7558,37.6176
        if (preg_match('/[?&]q=([^&]+)/', $url, $matches)) {
            $coords = explode(',', $matches[1]);
            if (count($coords) >= 2) {
                return [
                    'latitude' => trim($coords[0]),
                    'longitude' => trim($coords[1]),
                    'zoom' => 15
                ];
            }
        }
        
        // Формат: https://www.google.com/maps/place/.../@55.7558,37.6176,15z
        if (preg_match('/@([^,]+),([^,]+),(\d+)z/', $url, $matches)) {
            return [
                'latitude' => $matches[1],
                'longitude' => $matches[2],
                'zoom' => (int)$matches[3]
            ];
        }
        
        // Формат: https://www.google.com/maps?ll=55.7558,37.6176&z=15
        if (preg_match('/[?&]ll=([^&]+)/', $url, $matches)) {
            $coords = explode(',', $matches[1]);
            if (count($coords) >= 2) {
                $zoom = 15;
                if (preg_match('/[?&]z=(\d+)/', $url, $zoomMatch)) {
                    $zoom = (int)$zoomMatch[1];
                }
                return [
                    'latitude' => trim($coords[0]),
                    'longitude' => trim($coords[1]),
                    'zoom' => $zoom
                ];
            }
        }
        
        return null;
    }

    public function updateLanguageCurrency(Request $request)
    {
        try {
            $user = Auth::user();
            $project = Project::with(['currency', 'language'])->where('id', $user->project_id ?? null)->firstOrFail();

            // Получаем доступные валюты из базы данных
            $availableCurrencyIds = \App\Models\Currency::getActive()->pluck('id')->toArray();

            $validated = $request->validate([
                'language_id' => 'nullable|integer|exists:languages,id',
                'booking_language_id' => 'nullable|integer|exists:languages,id',
                'currency_id' => 'nullable|integer|in:' . implode(',', $availableCurrencyIds),
            ]);

            // Отладка до сохранения
            \Log::info('Before saving project', [
                'validated' => $validated,
                'old_currency_id' => $project->currency_id,
            ]);
            
            // Обновляем только язык и валюту
            $project->fill($validated);
            $project->save();

            // Отладка после сохранения
            \Log::info('After saving project', [
                'new_currency_id' => $project->currency_id,
                'project_fresh' => $project->fresh()->currency_id,
            ]);

            // Устанавливаем валюту в сессию
            if (isset($validated['currency_id'])) {
                $currency = \App\Models\Currency::find($validated['currency_id']);
                if ($currency) {
                    CurrencyHelper::setCurrency($currency->code);
                    // Также устанавливаем в сессию напрямую для надежности
                    session(['currency' => $currency->code]);
                }
            }

            // Устанавливаем язык в сессию
            if (isset($validated['language_id'])) {
                $language = \App\Models\Language::find($validated['language_id']);
                if ($language) {
                    // Используем LanguageHelper для установки языка
                    \App\Helpers\LanguageHelper::setLanguage($language);
                    // Также устанавливаем в сессию напрямую для надежности
                    session(['language' => $language->code]);
                    \Illuminate\Support\Facades\App::setLocale($language->code);
                }
            }

            // Перезагружаем проект с обновленными данными
            $project->refresh();
            $project->load(['currency', 'language']);
            
            // Определяем, что именно изменилось
            $changedData = [];
            if (isset($validated['language_id'])) {
                $changedData['language'] = $project->language;
            }
            if (isset($validated['booking_language_id'])) {
                // Получаем правильный язык веб-записи
                $bookingLanguage = \App\Models\Language::find($validated['booking_language_id']);
                $changedData['booking_language'] = $bookingLanguage;
            }
            if (isset($validated['currency_id'])) {
                $changedData['currency'] = $project->currency;
            }
            
            return response()->json([
                'success' => true,
                'currency_id' => $project->currency_id,
                'currency_code' => $project->currency ? $project->currency->code : null,
                'currency' => $project->currency,
                'language' => $project->language,
                ...$changedData
            ]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Ошибка валидации',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Произошла ошибка при сохранении настроек'
            ], 500);
        }
    }
}
