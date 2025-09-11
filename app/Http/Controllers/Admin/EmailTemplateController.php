<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\File;

class EmailTemplateController extends Controller
{
    private $templatesPath;
    
    public function __construct()
    {
        $this->templatesPath = resource_path('views/emails/templates');
        
        // Создаем папку если не существует
        if (!File::exists($this->templatesPath)) {
            File::makeDirectory($this->templatesPath, 0755, true);
        }
    }
    
    public function index()
    {
        $templates = $this->getTemplates();
        return view('admin.email-templates.index', compact('templates'));
    }
    
    public function create()
    {
        // Возвращаем JSON для модального окна
        return response()->json(['success' => true]);
    }
    
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'subject' => 'required|string|max:255',
            'type' => 'required|string|in:registration,appointment,reminder,notification',
            'content' => 'required|string',
        ]);
        
        $template = [
            'id' => uniqid(),
            'name' => $request->name,
            'subject' => $request->subject,
            'type' => $request->type,
            'content' => $request->content,
            'status' => 'active',
            'created_at' => now()->toISOString(),
        ];
        
        $this->saveTemplate($template);
        
        return response()->json(['success' => true, 'message' => 'Email шаблон успешно создан!']);
    }
    
    public function show($id)
    {
        $template = $this->getTemplate($id);
        
        if (!$template) {
            return response()->json(['error' => 'Шаблон не найден!'], 404);
        }
        
        return response()->json(['template' => $template]);
    }
    
    public function edit($id)
    {
        $template = $this->getTemplate($id);
        
        if (!$template) {
            return response()->json(['error' => 'Шаблон не найден!'], 404);
        }
        
        return response()->json(['template' => $template]);
    }
    
    public function update(Request $request, $id)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'subject' => 'required|string|max:255',
            'type' => 'required|string|in:registration,appointment,reminder,notification',
            'content' => 'required|string',
            'status' => 'required|string|in:active,inactive',
        ]);
        
        $template = $this->getTemplate($id);
        
        if (!$template) {
            return response()->json(['error' => 'Шаблон не найден!'], 404);
        }
        
        $template['name'] = $request->name;
        $template['subject'] = $request->subject;
        $template['type'] = $request->type;
        $template['content'] = $request->content;
        $template['status'] = $request->status;
        $template['updated_at'] = now()->toISOString();
        
        $this->saveTemplate($template);
        
        return response()->json(['success' => true, 'message' => 'Email шаблон успешно обновлен!']);
    }
    
    public function destroy($id)
    {
        $template = $this->getTemplate($id);
        
        if (!$template) {
            return response()->json(['error' => 'Шаблон не найден!'], 404);
        }
        
        $this->deleteTemplate($id);
        
        return response()->json(['success' => 'Email шаблон успешно удален!']);
    }
    
    private function getTemplates()
    {
        $templatesFile = $this->templatesPath . '/templates.json';
        
        if (File::exists($templatesFile)) {
            $content = File::get($templatesFile);
            return json_decode($content, true) ?: [];
        }
        
        // Возвращаем дефолтные шаблоны
        return [
            [
                'id' => '1',
                'name' => 'Приветственное письмо',
                'subject' => 'Добро пожаловать в CRM Studio',
                'type' => 'registration',
                'content' => 'Добро пожаловать! Ваш аккаунт успешно создан.',
                'status' => 'active',
                'created_at' => now()->toISOString(),
            ],
            [
                'id' => '2',
                'name' => 'Напоминание о записи',
                'subject' => 'Напоминание о вашей записи',
                'type' => 'appointment',
                'content' => 'Напоминаем о вашей записи на {{date}} в {{time}}.',
                'status' => 'active',
                'created_at' => now()->toISOString(),
            ]
        ];
    }
    
    private function getTemplate($id)
    {
        $templates = $this->getTemplates();
        
        foreach ($templates as $template) {
            if ($template['id'] == $id) {
                return $template;
            }
        }
        
        return null;
    }
    
    private function saveTemplate($template)
    {
        $templates = $this->getTemplates();
        
        // Находим существующий шаблон или добавляем новый
        $found = false;
        foreach ($templates as $key => $existingTemplate) {
            if ($existingTemplate['id'] == $template['id']) {
                $templates[$key] = $template;
                $found = true;
                break;
            }
        }
        
        if (!$found) {
            $templates[] = $template;
        }
        
        $templatesFile = $this->templatesPath . '/templates.json';
        File::put($templatesFile, json_encode($templates, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
    }
    
    private function deleteTemplate($id)
    {
        $templates = $this->getTemplates();
        
        $templates = array_filter($templates, function($template) use ($id) {
            return $template['id'] != $id;
        });
        
        $templatesFile = $this->templatesPath . '/templates.json';
        File::put($templatesFile, json_encode(array_values($templates), JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
    }
}
