<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\SystemLog;
use Illuminate\Support\Facades\View;

class LogController extends Controller
{
    public function __construct()
    {
        // Критические ошибки за последние сутки
        $criticalLogs = \App\Models\SystemLog::where('level', 'error')
            ->where('created_at', '>=', now()->subDay())
            ->orderByDesc('created_at')
            ->take(3)
            ->get();
        View::share('criticalLogs', $criticalLogs);
    }

    public function index(Request $request)
    {
        $query = SystemLog::query();
        $status = $request->input('status', 'new');
        if ($status) {
            $query->where('status', $status);
        }
        if ($level = $request->input('level')) {
            $query->where('level', $level);
        }
        if ($date = $request->input('date')) {
            $query->whereDate('created_at', $date);
        }
        if ($search = $request->input('search')) {
            $query->where(function($q) use ($search) {
                $q->where('message', 'like', "%$search%")
                  ->orWhere('module', 'like', "%$search%")
                  ->orWhere('user_email', 'like', "%$search%")
                  ->orWhere('ip', 'like', "%$search%")
                  ->orWhere('action', 'like', "%$search%")
                  ->orWhere('context', 'like', "%$search%")
                ;
            });
        }
        // Фильтрация по project_id (поиск по context->project_id)
        if ($request->filled('project_id')) {
            $query->where(function($q) use ($request) {
                $q->whereJsonContains('context->project_id', (int)$request->project_id)
                  ->orWhere('context', 'like', '"project_id":' . (int)$request->project_id . '%'); // для старых логов
            });
        }
        $logs = $query->orderByDesc('created_at')->paginate(20);
        // Получить список проектов для фильтра
        $projects = \DB::table('projects')->pluck('project_name', 'id');
        return view('admin.logs.index', compact('logs', 'projects'));
    }

    public function show($id)
    {
        $log = SystemLog::findOrFail($id);
        return response()->json($log);
    }

    public function fix($id)
    {
        $log = \App\Models\SystemLog::findOrFail($id);
        if ($log->level === 'error' && $log->status === 'new') {
            $log->status = 'fixed';
            $log->save();
            return response()->json(['success' => true]);
        }
        return response()->json(['success' => false, 'message' => 'Нельзя изменить статус.'], 400);
    }
} 