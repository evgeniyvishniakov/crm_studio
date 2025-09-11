<?php

namespace App\Services;

use App\Models\Clients\Appointment;
use Carbon\Carbon;

class EmailTemplateService
{
    /**
     * Заменяет плейсхолдеры в шаблоне на реальные данные
     */
    public function processTemplate(string $template, array $data = []): string
    {
        $placeholders = [
            '{{date}}' => $data['date'] ?? '',
            '{{time}}' => $data['time'] ?? '',
            '{{client_name}}' => $data['client_name'] ?? '',
            '{{service_name}}' => $data['service_name'] ?? '',
            '{{master_name}}' => $data['master_name'] ?? '',
            '{{project_name}}' => $data['project_name'] ?? '',
            '{{price}}' => $data['price'] ?? '',
            '{{notes}}' => $data['notes'] ?? '',
            '{{phone}}' => $data['phone'] ?? '',
            '{{address}}' => $data['address'] ?? '',
            '{{working_hours}}' => $data['working_hours'] ?? '',
        ];

        return str_replace(array_keys($placeholders), array_values($placeholders), $template);
    }

    /**
     * Подготавливает данные для плейсхолдеров из записи
     */
    public function prepareAppointmentData(Appointment $appointment): array
    {
        return [
            'date' => Carbon::parse($appointment->date)->format('d.m.Y'),
            'time' => $appointment->time,
            'client_name' => $appointment->client->name ?? '',
            'service_name' => $appointment->service->name ?? '',
            'master_name' => $appointment->user->name ?? 'Не назначен',
            'project_name' => $appointment->project->project_name ?? '',
            'price' => $appointment->price ?? '',
            'notes' => $appointment->notes ?? '',
            'phone' => $appointment->project->phone ?? 'Не указан',
            'address' => $appointment->project->address ?? 'Не указан',
            'working_hours' => $appointment->project->working_hours ?? '',
        ];
    }

    /**
     * Получает шаблон по типу
     */
    public function getTemplateByType(string $type): ?array
    {
        $templates = $this->getTemplates();
        
        foreach ($templates as $template) {
            if ($template['type'] === $type && $template['status'] === 'active') {
                return $template;
            }
        }
        
        return null;
    }

    /**
     * Получает все шаблоны
     */
    private function getTemplates(): array
    {
        $templatesFile = resource_path('views/emails/templates/templates.json');
        
        if (file_exists($templatesFile)) {
            $content = file_get_contents($templatesFile);
            return json_decode($content, true) ?: [];
        }
        
        return [];
    }

    /**
     * Отправляет email с использованием шаблона
     */
    public function sendEmailWithTemplate(string $type, array $data, string $to, string $subject = null): bool
    {
        $template = $this->getTemplateByType($type);
        
        if (!$template) {
            return false;
        }

        $processedContent = $this->processTemplate($template['content'], $data);
        $processedSubject = $subject ?: $this->processTemplate($template['subject'], $data);

        try {
            \Mail::raw($processedContent, function ($message) use ($to, $processedSubject) {
                $message->to($to)
                        ->subject($processedSubject);
            });
            
            return true;
        } catch (\Exception $e) {
            \Log::error('Email sending failed: ' . $e->getMessage());
            return false;
        }
    }
}
