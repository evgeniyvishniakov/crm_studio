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
                'types' => $typesQuery->select('type')->distinct()->pluck('type'),
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

    public function markAllAsRead(Request $request)
    {
        $user = auth()->user();
        
        // Админы могут отмечать как прочитанные любые уведомления проекта, мастера - только свои
        if ($user->role === 'admin') {
            $query = Notification::where('project_id', $user->project_id)
                ->where('is_read', false);
        } else {
            $query = Notification::where('project_id', $user->project_id)
                ->where('user_id', $user->id)
                ->where('is_read', false);
        }
        
        // Применяем фильтры, если они переданы
        if ($request->filled('type')) {
            $query->where('type', $request->input('type'));
        }
        if ($request->filled('status')) {
            $query->where('is_read', $request->input('status') === 'read');
        }
        
        $updatedCount = $query->update(['is_read' => true]);
        
        if ($request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => __('messages.all_notifications_marked_as_read'),
                'updated_count' => $updatedCount
            ]);
        }
        
        return back()->with('success', __('messages.all_notifications_marked_as_read'));
    }

    public function destroy($id)
    {
        $user = auth()->user();
        
        // Админы могут удалять любые уведомления проекта, мастера - только свои
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
        
        $notification->delete();
        
        if (request()->ajax()) {
            return response()->json([
                'success' => true,
                'message' => __('messages.notification_deleted_successfully')
            ]);
        }
        
        return back()->with('success', __('messages.notification_deleted_successfully'));
    }
} 