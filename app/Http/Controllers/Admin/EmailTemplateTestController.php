<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\EmailTemplateService;
use Illuminate\Http\Request;

class EmailTemplateTestController extends Controller
{
    public function test(Request $request)
    {
        $request->validate([
            'type' => 'required|string',
            'email' => 'required|email',
        ]);

        $templateService = new EmailTemplateService();
        
        // Тестовые данные
        $testData = [
            'date' => '15.09.2025',
            'time' => '14:30',
            'client_name' => 'Анна Иванова',
            'service_name' => 'Стрижка и укладка',
            'master_name' => 'Мария Петрова',
            'project_name' => 'Салон красоты "Элегант"',
            'price' => '2500 ₽',
            'notes' => 'Принести фото желаемой стрижки',
            'phone' => '+7 (495) 123-45-67',
            'address' => 'ул. Тверская, 15',
            'working_hours' => '9:00 - 21:00',
        ];
        
        $result = $templateService->sendEmailWithTemplate(
            $request->type, 
            $testData, 
            $request->email
        );
        
        if ($result) {
            return response()->json([
                'success' => true, 
                'message' => 'Тестовое письмо успешно отправлено!'
            ]);
        } else {
            return response()->json([
                'success' => false, 
                'message' => 'Ошибка отправки. Проверьте настройки почты и существование шаблона.'
            ], 400);
        }
    }
}
