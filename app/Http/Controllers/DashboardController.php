<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Appointment;
use App\Models\Client;
use App\Models\Expense;
use App\Models\Purchase;
use App\Models\SaleItem;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index()
    {
        // Услуги: только завершённые записи
        $servicesCount = Appointment::where('status', 'completed')->count();

        // Продажи услуг: сумма всех цен из записей
        $servicesRevenue = Appointment::sum('price');

        // Продажи товаров: общая прибыль (сумма розничных цен - сумма оптовых цен)
        $totalRetail = SaleItem::sum(\DB::raw('retail_price * quantity'));
        $totalWholesale = SaleItem::sum(\DB::raw('wholesale_price * quantity'));
        $productsRevenue = $totalRetail - $totalWholesale;

        // Расходы: сумма всех расходов + сумма всех закупок
        $expensesSum = Expense::sum('amount');
        $purchasesSum = Purchase::sum('total_amount');
        $totalExpenses = $expensesSum + $purchasesSum;

        // Общая прибыль: продажи товаров + продажи услуг - расходы
        $totalProfit = $productsRevenue + $servicesRevenue - $totalExpenses;

        // Записи: все, кроме "Перенесено" и "Ожидается"
        $appointmentsCount = Appointment::whereNotIn('status', ['Перенесено', 'Ожидается'])->count();

        // Клиенты: все
        $clientsCount = Client::count();

        // Логика отображения процентов: после 15 дней и после 2-го месяца работы
        $today = Carbon::now();
        
        // Находим дату первой записи в системе
        $firstAppointment = Appointment::orderBy('created_at', 'asc')->first();
        
        if ($firstAppointment) {
            $firstDate = Carbon::parse($firstAppointment->created_at);
            $monthsSinceStart = $today->diffInMonths($firstDate);
            $daysSinceStart = $today->diffInDays($firstDate);
            
            // Показываем проценты только после 15 дней и после 2-го месяца работы
            $showDynamics = $daysSinceStart >= 15 && $monthsSinceStart >= 1;
        } else {
            $showDynamics = false;
        }

        return view('dashboard.index', compact('servicesCount', 'clientsCount', 'appointmentsCount', 'totalExpenses', 'showDynamics', 'servicesRevenue', 'productsRevenue', 'totalProfit'));
    }
}
