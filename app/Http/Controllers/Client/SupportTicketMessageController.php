<?php

declare(strict_types=1);

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Clients\SupportTicket;
use App\Models\Clients\SupportTicketMessage;
use Illuminate\Support\Facades\Auth;

class SupportTicketMessageController extends Controller
{
    // Получить все сообщения по тикету (AJAX)
    public function index($ticketId)
    {
        $user = Auth::user();
        $ticket = SupportTicket::where('id', $ticketId)
            ->where('project_id', $user->project_id)
            ->firstOrFail();
        $messages = SupportTicketMessage::where('support_ticket_id', $ticket->id)
            ->orderBy('created_at', 'asc')
            ->get();
        return response()->json(['messages' => $messages]);
    }

    // Добавить сообщение к тикету (от клиента)
    public function store(Request $request, $ticketId)
    {
        $request->validate([
            'message' => 'required|string',
        ]);
        $user = Auth::user();
        $ticket = SupportTicket::where('id', $ticketId)
            ->where('project_id', $user->project_id)
            ->firstOrFail();
        $msg = SupportTicketMessage::create([
            'support_ticket_id' => $ticket->id,
            'user_id' => $user->id,
            'message' => $request->message,
            'is_admin' => false,
        ]);
        return response()->json(['success' => true, 'message' => $msg]);
    }
} 