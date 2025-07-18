<?php

declare(strict_types=1);

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Clients\SupportTicket;
use Illuminate\Support\Facades\Auth;

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
        if ($request->ajax()) {
            return response()->json(['success' => true, 'ticket' => $ticket]);
        }
        return redirect()->route('support-tickets.index')->with('success', 'Тикет создан');
    }
} 