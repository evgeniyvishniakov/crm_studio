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
        $project = Project::where('id', $user->project_id ?? null)->first();
        // Можно добавить Gate::authorize('view-settings', $project); для ограничения доступа
        return view('client.settings.index', compact('project'));
    }

    public function update(Request $request)
    {
        $user = Auth::user();
        $project = Project::where('id', $user->project_id ?? null)->firstOrFail();

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
            'language' => 'nullable|string|in:ru,en,ua',
            'currency' => 'nullable|string|in:UAH,USD,EUR',
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
                'currency' => $project->currency
            ]);
        }

        return redirect()->back()->with('success', 'Настройки успешно обновлены.');
    }

    public function updateLanguageCurrency(Request $request)
    {
        try {
            $user = Auth::user();
            $project = Project::where('id', $user->project_id ?? null)->firstOrFail();

            $validated = $request->validate([
                'language' => 'nullable|string|in:ru,en,ua',
                'currency' => 'nullable|string|in:UAH,USD,EUR',
            ]);

            // Обновляем только язык и валюту
            $project->fill($validated);
            $project->save();

            // Устанавливаем валюту в сессию
            if (isset($validated['currency'])) {
                CurrencyHelper::setCurrency($validated['currency']);
            }

            return response()->json([
                'success' => true,
                'message' => 'Настройки языка и валюты успешно обновлены.',
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
