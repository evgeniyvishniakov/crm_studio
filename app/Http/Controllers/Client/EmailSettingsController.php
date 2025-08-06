<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Validator;

class EmailSettingsController extends Controller
{
    public function index()
    {
        $user = auth()->user();
        $project = $user->project;
        
        // Получаем или создаем настройки email
        $emailSettings = $project->getOrCreateEmailSettings();
        
        return view('client.email-settings.index', compact('project', 'emailSettings'));
    }

    public function update(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email_host' => 'required|string|max:255',
            'email_port' => 'required|integer|min:1|max:65535',
            'email_username' => 'required|email',
            'email_password' => 'required|string|min:1',
            'email_encryption' => 'required|in:tls,ssl,none',
            'email_from_name' => 'required|string|max:255',
            'email_notifications_enabled' => 'boolean',
        ]);

        if ($validator->fails()) {
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => __('messages.validation_errors'),
                    'errors' => $validator->errors()
                ], 422);
            }
            
            return back()->withErrors($validator)->withInput();
        }

        $user = auth()->user();
        $project = $user->project;

        // Получаем или создаем настройки email
        $emailSettings = $project->getOrCreateEmailSettings();

        // Обновляем настройки email
        $emailSettings->update([
            'email_host' => $request->email_host,
            'email_port' => $request->email_port,
            'email_username' => $request->email_username,
            'email_password' => $request->email_password,
            'email_encryption' => $request->email_encryption,
            'email_from_name' => $request->email_from_name,
            'email_notifications_enabled' => $request->has('email_notifications_enabled'),
        ]);

        if ($request->ajax() || $request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => __('messages.email_settings_saved')
            ]);
        }

        return back()->with('success', __('messages.email_settings_saved'));
    }

    public function testConnection(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email_host' => 'required|string|max:255',
            'email_port' => 'required|integer|min:1|max:65535',
            'email_username' => 'required|email',
            'email_password' => 'required|string|min:1',
            'email_encryption' => 'required|in:tls,ssl,none',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => __('messages.email_fields_required_test')
            ], 422);
        }

        try {
            // Временно настраиваем конфигурацию для теста
            config([
                'mail.mailers.smtp.host' => $request->email_host,
                'mail.mailers.smtp.port' => $request->email_port,
                'mail.mailers.smtp.username' => $request->email_username,
                'mail.mailers.smtp.password' => $request->email_password,
                'mail.mailers.smtp.encryption' => $request->email_encryption,
            ]);

            // Отправляем тестовое письмо
            Mail::raw(__('messages.email_test_message'), function ($message) use ($request) {
                $message->to($request->email_username)
                        ->subject(__('messages.email_test_subject'));
            });

            return response()->json([
                'success' => true,
                'message' => __('messages.email_test_success')
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => __('messages.email_test_error') . ': ' . $e->getMessage()
            ]);
        }
    }

    public function getInstructions()
    {
        return response()->json([
            'instructions' => [
                'step1' => __('messages.email_instructions_step1'),
                'step2' => __('messages.email_instructions_step2'),
                'step3' => __('messages.email_instructions_step3'),
                'step4' => __('messages.email_instructions_step4'),
                'step5' => __('messages.email_instructions_step5'),
                'step6' => __('messages.email_instructions_step6'),
                'step7' => __('messages.email_instructions_step7'),
                'step8' => __('messages.email_instructions_step8'),
            ]
        ]);
    }
} 