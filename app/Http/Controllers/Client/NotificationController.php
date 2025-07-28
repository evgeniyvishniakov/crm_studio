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
        
        if ($request->filled('type')) {
            $query->where('type', $request->input('type'));
        }
        if ($request->filled('status')) {
            $query->where('is_read', $request->input('status') === 'read');
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