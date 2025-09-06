<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\PanelAdmin;
use App\Models\Clients\Client;
use App\Models\Admin\Project;
use App\Models\Admin\BlogArticle;
use App\Models\Admin\BlogCategory;
use App\Models\Admin\BlogTag;
use App\Models\Clients\SupportTicket;
use App\Models\Notification;
use App\Models\SystemLog;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function index()
    {
        // Статистика пользователей
        $totalUsers = PanelAdmin::count();
        $newUsersThisMonth = PanelAdmin::where('created_at', '>=', now()->startOfMonth())->count();
        
        // Статистика клиентов
        $totalClients = Client::count();
        $newClientsThisMonth = Client::where('created_at', '>=', now()->startOfMonth())->count();
        
        // Статистика проектов
        $totalProjects = Project::count();
        $activeProjects = Project::where('status', 'active')->count();
        
        
        // Статистика поддержки
        $openTickets = SupportTicket::where('status', 'open')->count();
        $pendingTickets = SupportTicket::where('status', 'pending')->count();
        $totalTickets = SupportTicket::count();
        
        // Уведомления
        $unreadNotifications = Notification::where('is_read', false)->count();
        
        
        
        // Статистика по месяцам (последние 6 месяцев)
        $monthlyStats = [];
        for ($i = 5; $i >= 0; $i--) {
            $date = now()->subMonths($i);
            $monthlyStats[] = [
                'month' => $date->format('M Y'),
                'users' => PanelAdmin::whereYear('created_at', $date->year)
                    ->whereMonth('created_at', $date->month)
                    ->count(),
                'clients' => Client::whereYear('created_at', $date->year)
                    ->whereMonth('created_at', $date->month)
                    ->count(),
                'projects' => Project::whereYear('created_at', $date->year)
                    ->whereMonth('created_at', $date->month)
                    ->count(),
                'tickets' => SupportTicket::whereYear('created_at', $date->year)
                    ->whereMonth('created_at', $date->month)
                    ->count(),
            ];
        }
        
        return view('admin.dashboard.index', compact(
            'totalUsers',
            'newUsersThisMonth',
            'totalClients',
            'newClientsThisMonth',
            'totalProjects',
            'activeProjects',
            'openTickets',
            'pendingTickets',
            'totalTickets',
            'unreadNotifications',
            'monthlyStats'
        ));
    }
}
