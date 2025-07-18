<?php

declare(strict_types=1);

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Clients\SupportTicket;
use App\Models\Clients\SupportTicketMessage;
use App\Models\Admin\User;
use App\Models\Notification;

class TicketController extends Controller
{
    // Список всех тикетов
    public function index(Request $request)
    {
        $tickets = SupportTicket::with(['user'])
            ->orderByDesc('created_at')
            ->paginate(20);
        return view('admin.tickets.index', compact('tickets'));
    }

    // Чат по тикету
    public function show($id)
    {
        $ticket = SupportTicket::with(['user'])->findOrFail($id);
        return view('admin.tickets.chat', compact('ticket'));
    }

    // Получить все сообщения тикета (AJAX)
    public function messages($ticketId)
    {
        $ticket = SupportTicket::findOrFail($ticketId);
        $messages = $ticket->messages()->with('user')->orderBy('created_at', 'asc')->get();
        return response()->json(['messages' => $messages]);
    }

    // Отправить сообщение от админа (AJAX)
    public function sendMessage(Request $request, $ticketId)
    {
        $request->validate([
            'message' => 'required|string',
        ]);
        $ticket = SupportTicket::findOrFail($ticketId);
        $admin = auth()->user();
        $msg = $ticket->messages()->create([
            'admin_id' => $admin->id,
            'message' => $request->message,
            'is_admin' => true,
        ]);
        if (!$request->input('is_admin')) {
            Notification::create([
                'type' => 'ticket',
                'title' => 'Новое сообщение в тикете',
                'body' => $ticket->subject,
                'url' => route('admin.tickets.index') . '#ticket-' . $ticket->id,
            ]);
        }
        return response()->json(['success' => true, 'message' => $msg]);
    }

    // Сменить статус тикета (AJAX)
    public function updateStatus(Request $request, $ticketId)
    {
        $request->validate([
            'status' => 'required|string|in:open,pending,closed',
        ]);
        $ticket = SupportTicket::findOrFail($ticketId);
        $ticket->status = $request->status;
        $ticket->save();
        return response()->json(['success' => true, 'status' => $ticket->status]);
    }

    public function destroy($id)
    {
        $ticket = SupportTicket::findOrFail($id);
        $ticket->delete();
        return redirect()->route('admin.tickets.index')->with('success', 'Тикет удалён');
    }
} 