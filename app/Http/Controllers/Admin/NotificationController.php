<?php

declare(strict_types=1);

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Notification;

class NotificationController extends Controller
{
    public function index(Request $request)
    {
        $user = auth()->user();
        $query = Notification::where(function($q) use ($user) {
            $q->whereNull('user_id')->orWhere('user_id', $user->id);
        });
        if ($request->filled('type')) {
            $query->where('type', $request->input('type'));
        }
        if ($request->filled('status')) {
            $query->where('is_read', $request->input('status') === 'read');
        }
        $notifications = $query->orderByDesc('created_at')->paginate(20);
        $types = Notification::select('type')->distinct()->pluck('type');
        return view('admin.notifications.index', compact('notifications', 'types'));
    }

    public function markAsRead($id)
    {
        $notification = Notification::findOrFail($id);
        $notification->is_read = true;
        $notification->save();
        // Если есть url — редиректим на него, иначе назад
        return $notification->url ? redirect($notification->url) : back();
    }
} 