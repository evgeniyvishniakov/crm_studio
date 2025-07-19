<?php

declare(strict_types=1);

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Clients\SupportTicket;
use App\Models\Clients\SupportTicketMessage;
use App\Models\Admin\User;
use App\Models\Notification;
use Illuminate\Support\Facades\Log;

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
        
        // Логируем отправку сообщения от админа
        Log::info('Admin support ticket message sent', [
            'admin_id' => $admin->id,
            'ticket_id' => $ticket->id,
            'message_id' => $msg->id,
            'ip' => $request->ip()
        ]);
        
        \Log::info('DEBUG: Попытка создать уведомление для клиента', [
            'ticket_id' => $ticket->id,
            'project_id' => $ticket->project_id,
            'subject' => $ticket->subject,
            'is_admin_param' => $request->input('is_admin'),
        ]);
        if ($request->input('is_admin')) {
            // Получаем всех админов, кроме текущего
            $adminUsers = User::where('role', 'admin')->where('id', '!=', $admin->id)->get();
            foreach ($adminUsers as $adminUser) {
                Notification::create([
                    'user_id' => $adminUser->id,
                    'type' => 'ticket',
                    'title' => 'Новое сообщение от администратора',
                    'body' => $ticket->subject,
                    'url' => route('client.support-tickets.index') . '#ticket-' . $ticket->id,
                    'project_id' => $ticket->project_id,
                ]);
            }
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
        $oldStatus = $ticket->status;
        $ticket->status = $request->status;
        $ticket->save();
        
        // Логируем смену статуса
        Log::info('Support ticket status changed', [
            'admin_id' => auth()->id(),
            'ticket_id' => $ticket->id,
            'old_status' => $oldStatus,
            'new_status' => $ticket->status,
            'ip' => $request->ip()
        ]);
        
        return response()->json(['success' => true, 'status' => $ticket->status]);
    }

    public function destroy($id)
    {
        $ticket = SupportTicket::findOrFail($id);
        
        // Логируем удаление тикета
        Log::info('Support ticket deleted', [
            'admin_id' => auth()->id(),
            'ticket_id' => $ticket->id,
            'subject' => $ticket->subject,
            'ip' => request()->ip()
        ]);
        
        $ticket->delete();
        return redirect()->route('admin.tickets.index')->with('success', 'Тикет удалён');
    }
} 