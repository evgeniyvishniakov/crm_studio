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

    /**
     * API: Получить динамику прибыли для графика (по дням/неделям/месяцам)
     */
    public function profitChartData(Request $request)
    {
        $period = $request->input('period', 7); // 7, 30, 90
        $now = now();
        $labels = [];
        $data = [];
        if ($period == 7) {
            // За 7 дней: по дням
            for ($i = 6; $i >= 0; $i--) {
                $date = $now->copy()->subDays($i)->toDateString();
                $labels[] = $now->copy()->subDays($i)->format('d M');
                $profit = $this->getProfitForDate($date);
                $data[] = round($profit, 2);
            }
        } elseif ($period == 30) {
            // За месяц: по дням (30 точек)
            for ($i = 29; $i >= 0; $i--) {
                $date = $now->copy()->subDays($i)->toDateString();
                $labels[] = $now->copy()->subDays($i)->format('d M');
                $profit = $this->getProfitForDate($date);
                $data[] = round($profit, 2);
            }
        } elseif ($period == 90) {
            // За 3 месяца: по дням (90 точек)
            for ($i = 89; $i >= 0; $i--) {
                $date = $now->copy()->subDays($i)->toDateString();
                $labels[] = $now->copy()->subDays($i)->format('d M');
                $profit = $this->getProfitForDate($date);
                $data[] = round($profit, 2);
            }
        }
        return response()->json([
            'labels' => $labels,
            'data' => $data
        ]);
    }

    /**
     * Получить прибыль за конкретную дату (день)
     */
    private function getProfitForDate($date)
    {
        $productsProfit = \App\Models\SaleItem::whereDate('created_at', $date)
            ->selectRaw('SUM((retail_price - wholesale_price) * quantity) as profit')
            ->value('profit') ?? 0;
        $servicesProfit = \App\Models\Appointment::whereDate('date', $date)
            ->sum('price');
        $expenses = \App\Models\Expense::whereDate('date', $date)->sum('amount');
        $purchases = \App\Models\Purchase::whereDate('date', $date)->sum('total_amount');
        return $productsProfit + $servicesProfit - $expenses - $purchases;
    }

    /**
     * Получить прибыль за период (от и до)
     */
    private function getProfitForPeriod($start, $end)
    {
        $productsProfit = \App\Models\SaleItem::whereBetween('created_at', [$start, $end])
            ->selectRaw('SUM((retail_price - wholesale_price) * quantity) as profit')
            ->value('profit') ?? 0;
        $servicesProfit = \App\Models\Appointment::whereBetween('date', [$start, $end])
            ->sum('price');
        $expenses = \App\Models\Expense::whereBetween('date', [$start, $end])->sum('amount');
        $purchases = \App\Models\Purchase::whereBetween('date', [$start, $end])->sum('total_amount');
        return $productsProfit + $servicesProfit - $expenses - $purchases;
    }

    /**
     * API: Получить динамику продаж товаров для графика (по дням/неделям/месяцам)
     */
    public function salesChartData(Request $request)
    {
        $period = $request->input('period', 7); // 7, 30, 90
        $now = now();
        $labels = [];
        $data = [];
        if ($period == 7) {
            // За 7 дней: по дням
            for ($i = 6; $i >= 0; $i--) {
                $date = $now->copy()->subDays($i)->toDateString();
                $labels[] = $now->copy()->subDays($i)->format('d M');
                $sales = $this->getSalesForDate($date);
                $data[] = round($sales, 2);
            }
        } elseif ($period == 30) {
            // За месяц: по дням (30 точек)
            for ($i = 29; $i >= 0; $i--) {
                $date = $now->copy()->subDays($i)->toDateString();
                $labels[] = $now->copy()->subDays($i)->format('d M');
                $sales = $this->getSalesForDate($date);
                $data[] = round($sales, 2);
            }
        } elseif ($period == 90) {
            // За 3 месяца: по дням (90 точек)
            for ($i = 89; $i >= 0; $i--) {
                $date = $now->copy()->subDays($i)->toDateString();
                $labels[] = $now->copy()->subDays($i)->format('d M');
                $sales = $this->getSalesForDate($date);
                $data[] = round($sales, 2);
            }
        }
        return response()->json([
            'labels' => $labels,
            'data' => $data
        ]);
    }

    /**
     * Получить выручку по продажам товаров за конкретную дату (день)
     */
    private function getSalesForDate($date)
    {
        return \App\Models\SaleItem::whereHas('sale', function($q) use ($date) {
                $q->whereDate('date', $date);
            })
            ->selectRaw('SUM(retail_price * quantity) as sales')
            ->value('sales') ?? 0;
    }

    /**
     * Получить выручку по продажам товаров за период (от и до)
     */
    private function getSalesForPeriod($start, $end)
    {
        return \App\Models\SaleItem::whereHas('sale', function($q) use ($start, $end) {
                $q->whereBetween('date', [$start, $end]);
            })
            ->selectRaw('SUM(retail_price * quantity) as sales')
            ->value('sales') ?? 0;
    }

    /**
     * API: Получить динамику продаж услуг для графика (по дням/неделям/месяцам)
     */
    public function servicesChartData(Request $request)
    {
        $period = $request->input('period', 7); // 7, 30, 90
        $now = now();
        $labels = [];
        $data = [];
        if ($period == 7) {
            for ($i = 6; $i >= 0; $i--) {
                $date = $now->copy()->subDays($i)->toDateString();
                $labels[] = $now->copy()->subDays($i)->format('d M');
                $sum = \App\Models\Appointment::whereDate('date', $date)
                    ->where('status', 'completed')
                    ->sum('price');
                $data[] = round($sum, 2);
            }
        } elseif ($period == 30) {
            for ($i = 29; $i >= 0; $i--) {
                $date = $now->copy()->subDays($i)->toDateString();
                $labels[] = $now->copy()->subDays($i)->format('d M');
                $sum = \App\Models\Appointment::whereDate('date', $date)
                    ->where('status', 'completed')
                    ->sum('price');
                $data[] = round($sum, 2);
            }
        } elseif ($period == 90) {
            for ($i = 89; $i >= 0; $i--) {
                $date = $now->copy()->subDays($i)->toDateString();
                $labels[] = $now->copy()->subDays($i)->format('d M');
                $sum = \App\Models\Appointment::whereDate('date', $date)
                    ->where('status', 'completed')
                    ->sum('price');
                $data[] = round($sum, 2);
            }
        }
        return response()->json([
            'labels' => $labels,
            'data' => $data
        ]);
    }
}
