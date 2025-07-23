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
            'currency_id' => 'nullable|integer|in:' . implode(',', $availableCurrencyIds),
        ]);

        // Обновление полей
        $project->fill($validated);

        // Обработка загрузки логотипа
        if ($request->hasFile('logo')) {
            $logoPath = $request->file('logo')->store('public/project_logos');
            $project->logo = Storage::url($logoPath);
        }

        $project->save();

        // Если это AJAX запрос, возвращаем JSON
        if ($request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'Настройки успешно обновлены.',
                'currency' => $project->currency ? $project->currency->code : null
            ]);
        }

        return redirect()->back()->with('success', 'Настройки успешно обновлены.');
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

            // Перезагружаем проект с обновленными данными
            $project->refresh();
            $project->load('currency');
            
            return response()->json([
                'success' => true,
                'currency_id' => $project->currency_id,
                'currency_code' => $project->currency ? $project->currency->code : null,
                'currency' => $project->currency,
                'language' => $project->language
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
