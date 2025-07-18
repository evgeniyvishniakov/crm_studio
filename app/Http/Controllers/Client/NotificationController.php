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
        $query = Notification::where('project_id', $user->project_id);
        if ($request->filled('type')) {
            $query->where('type', $request->input('type'));
        }
        if ($request->filled('status')) {
            $query->where('is_read', $request->input('status') === 'read');
        }
        $notifications = $query->orderByDesc('created_at')->paginate(20);
        $types = Notification::where('project_id', $user->project_id)->select('type')->distinct()->pluck('type');
        return view('client.notifications.index', compact('notifications', 'types'));
    }

    public function markAsRead($id)
    {
        $notification = Notification::findOrFail($id);
        $notification->is_read = true;
        $notification->save();
        return $notification->url ? redirect($notification->url) : back();
    }
} 