<?php

declare(strict_types=1);

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Notification;

class NotificationController extends Controller
{
    public function index(Request $request)
    {
        $user = auth()->user();
        
        // Админы видят все уведомления проекта, мастера - только свои
        if ($user->role === 'admin') {
            $query = Notification::where('project_id', $user->project_id);
            $typesQuery = Notification::where('project_id', $user->project_id);
        } else {
            $query = Notification::where('project_id', $user->project_id)
                ->where('user_id', $user->id);
            $typesQuery = Notification::where('project_id', $user->project_id)
                ->where('user_id', $user->id);
        }
        
        // Добавляем поиск как в товарах
        if ($request->has('search') && $request->search !== '') {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('title', 'like', "%$search%")
                  ->orWhere('type', 'like', "%$search%");
            });
        }
        
        if ($request->filled('type')) {
            $query->where('type', $request->input('type'));
        }
        if ($request->filled('status')) {
            $query->where('is_read', $request->input('status') === 'read');
        }
        
        if ($request->ajax()) {
            $notifications = $query->orderByDesc('created_at')->paginate(20);
            return response()->json([
                'data' => $notifications->items(),
                'meta' => [
                    'current_page' => $notifications->currentPage(),
                    'last_page' => $notifications->lastPage(),
                    'per_page' => $notifications->perPage(),
                    'total' => $notifications->total(),
                ],
            ]);
        }
        
        $notifications = $query->orderByDesc('created_at')->paginate(20);
        $types = $typesQuery->select('type')->distinct()->pluck('type');
        
        return view('client.notifications.index', compact('notifications', 'types'));
    }

    public function markAsRead($id)
    {
        $user = auth()->user();
        
        // Админы могут отмечать как прочитанные любые уведомления проекта, мастера - только свои
        if ($user->role === 'admin') {
            $notification = Notification::where('id', $id)
                ->where('project_id', $user->project_id)
                ->firstOrFail();
        } else {
            $notification = Notification::where('id', $id)
                ->where('project_id', $user->project_id)
                ->where('user_id', $user->id)
                ->firstOrFail();
        }
        
        $notification->is_read = true;
        $notification->save();
        return $notification->url ? redirect($notification->url) : back();
    }
} 