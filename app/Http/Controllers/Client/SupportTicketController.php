<?php

declare(strict_types=1);

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Clients\SupportTicket;
use Illuminate\Support\Facades\Auth;
use App\Models\Notification;
use Illuminate\Support\Facades\Log;

class SupportTicketController extends Controller
{
    public function index(Request $request)
    {
        $user = Auth::user();
        $tickets = SupportTicket::where('project_id', $user->project_id)
            ->where('user_id', $user->id)
            ->orderByDesc('created_at')
            ->paginate(10);
        return view('client.support-tickets.list', compact('tickets'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'subject' => 'required|string|max:255',
            'message' => 'required|string',
        ]);
        $user = Auth::user();
        $ticket = SupportTicket::create([
            'user_id' => $user->id,
            'project_id' => $user->project_id,
            'subject' => $request->subject,
            'message' => $request->message,
            'status' => 'open',
        ]);
        // Сохраняем первое сообщение в чат тикета
        \App\Models\Clients\SupportTicketMessage::create([
            'support_ticket_id' => $ticket->id,
            'user_id' => $user->id,
            'message' => $request->message,
            'is_admin' => false,
        ]);
        // Логируем создание тикета
        Log::info('Support ticket created', [
            'user_id' => $user->id,
            'ticket_id' => $ticket->id,
            'subject' => $ticket->subject,
            'ip' => $request->ip()
        ]);
        
        // После создания тикета - уведомляем пользователей с доступом к поддержке
        $usersWithSupportAccess = \App\Models\Admin\User::where('project_id', $ticket->project_id)
            ->whereHas('roleModel.permissions', function($query) {
                $query->where('name', 'support');
            })
            ->get();
        
        foreach ($usersWithSupportAccess as $user) {
            // Проверяем, не создано ли уже уведомление для этого пользователя
            $existingNotification = Notification::where('user_id', $user->id)
                ->where('type', 'ticket')
                ->where('title', 'Новый тикет')
                ->where('project_id', $ticket->project_id)
                ->where('created_at', '>=', now()->subMinutes(1))
                ->first();
            
            if (!$existingNotification) {
                Notification::create([
                    'user_id' => $user->id,
                    'type' => 'ticket',
                    'title' => 'Новый тикет',
                    'body' => $ticket->subject,
                    'url' => route('admin.tickets.index') . '#ticket-' . $ticket->id,
                    'project_id' => $ticket->project_id,
                ]);
            }
        }
        
        if ($request->ajax()) {
            return response()->json(['success' => true, 'ticket' => $ticket]);
        }
        return redirect()->route('client.support-tickets.index')->with('success', 'Тикет создан');
    }
} 