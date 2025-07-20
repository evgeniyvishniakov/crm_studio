<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Clients\Appointment;
use App\Models\Clients\Client;
use App\Models\Clients\Expense;
use App\Models\Clients\Purchase;
use App\Models\Clients\SaleItem;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Carbon\CarbonPeriod;

class DashboardController extends Controller
{
    public function index()
    {
        $currentProjectId = auth()->user()->project_id;
        // Услуги: только завершённые записи
        $servicesCount = Appointment::where('project_id', $currentProjectId)->where('status', 'completed')->count();

        // Продажи услуг: сумма цен только из завершённых записей
        $servicesRevenue = Appointment::where('project_id', $currentProjectId)->where('status', 'completed')->sum('price');

        // Продажи товаров: общая прибыль (сумма розничных цен - сумма оптовых цен)
        $totalRetail = SaleItem::whereHas('sale', function($q) use ($currentProjectId) {
            $q->where('project_id', $currentProjectId);
        })->sum(\DB::raw('retail_price * quantity'));
        $totalWholesale = SaleItem::whereHas('sale', function($q) use ($currentProjectId) {
            $q->where('project_id', $currentProjectId);
        })->sum(\DB::raw('wholesale_price * quantity'));
        $productsRevenue = $totalRetail - $totalWholesale;

        // Расходы: сумма всех расходов + сумма всех закупок
        $expensesSum = Expense::where('project_id', $currentProjectId)->sum('amount');
        $purchasesSum = Purchase::where('project_id', $currentProjectId)->sum('total_amount');
        $totalExpenses = $expensesSum + $purchasesSum;

        // Общая прибыль: продажи товаров + продажи услуг - расходы
        $totalProfit = $productsRevenue + $servicesRevenue - $totalExpenses;

        // Записи: все, кроме "Перенесено" и "Ожидается"
        $appointmentsCount = Appointment::where('project_id', $currentProjectId)->whereNotIn('status', ['Перенесено', 'Ожидается'])->count();

        // Клиенты: все
        $clientsCount = Client::where('project_id', $currentProjectId)->count();

        // Логика отображения процентов: после 15 дней и после 2-го месяца работы
        $today = Carbon::now();
        
        // Находим дату первой записи в системе
        $firstAppointment = Appointment::where('project_id', $currentProjectId)->orderBy('created_at', 'asc')->first();
        
        if ($firstAppointment) {
            $firstDate = Carbon::parse($firstAppointment->created_at);
            $monthsSinceStart = $today->diffInMonths($firstDate);
            $daysSinceStart = $today->diffInDays($firstDate);
            
            // Показываем проценты только после 15 дней и после 2-го месяца работы
            $showDynamics = $daysSinceStart >= 15 && $monthsSinceStart >= 1;
        } else {
            $showDynamics = false;
        }

        // Получаем ближайшие 5 записей на сегодня и завтра
        $today = Carbon::today();
        $tomorrow = Carbon::tomorrow();

        $upcomingAppointments = Appointment::with(['client', 'service'])
            ->where('project_id', $currentProjectId)
            ->whereIn('date', [$today, $tomorrow])
            ->where('status', 'pending')
            ->orderBy('date', 'asc')
            ->orderBy('time', 'asc')
            ->take(5)
            ->get();

        // Данные для виджета "Краткий отчёт за сегодня"
        $todayDate = Carbon::today();

        // 1. Прибыль с услуг за сегодня
        $todayServicesProfit = Appointment::where('project_id', $currentProjectId)
            ->where('status', 'completed')
            ->whereDate('date', $todayDate)
            ->sum('price');

        // 2. Прибыль с товаров за сегодня
        $todayProductsProfit = SaleItem::whereHas('sale', function($q) use ($todayDate, $currentProjectId) {
                $q->where('project_id', $currentProjectId)->whereDate('date', $todayDate);
            })
            ->selectRaw('SUM((retail_price - wholesale_price) * quantity) as profit')
            ->value('profit') ?? 0;

        // 3. Услуг оказано сегодня
        $todayCompletedServices = Appointment::where('project_id', $currentProjectId)
            ->where('status', 'completed')
            ->whereDate('date', $todayDate)
            ->count();
        
        // 4. Товаров продано сегодня (сумма всех quantity из SaleItem)
        $todaySoldProducts = SaleItem::whereHas('sale', function($q) use ($todayDate, $currentProjectId) {
                $q->where('project_id', $currentProjectId)->whereDate('date', $todayDate);
            })
            ->sum('quantity');

        return view('client.dashboard.index', compact(
            'servicesCount', 'clientsCount', 'appointmentsCount', 'totalExpenses', 
            'showDynamics', 'servicesRevenue', 'productsRevenue', 'totalProfit', 
            'upcomingAppointments', 'todayServicesProfit', 'todayProductsProfit',
            'todayCompletedServices', 'todaySoldProducts'
        ));
    }

    /**
     * Округлить максимум для графика прибыли: если < 50000 — 50000, иначе шаг 5000
     */
    private function roundProfitMaxValueForChart($maxValue)
    {
        if ($maxValue <= 0) return 5000;
        if ($maxValue < 50000) {
            return 50000;
        }
        return ceil($maxValue / 5000) * 5000;
    }

    /**
     * Округлить максимум для графика расходов: если < 40000 — 45000, иначе шаг 5000
     */
    private function roundExpensesMaxValueForChart($maxValue)
    {
        if ($maxValue <= 0) return 5000;
        if ($maxValue < 40000) {
            return 45000;
        }
        return ceil($maxValue / 5000) * 5000;
    }

    /**
     * API: Получить динамику прибыли для графика (по дням/неделям/месяцам)
     */
    public function profitChartData(Request $request)
    {
        $startDate = $request->input('start_date');
        $endDate = $request->input('end_date');
        $labels = [];
        $data = [];
        if ($startDate && $endDate) {
            $period = \Carbon\CarbonPeriod::create($startDate, $endDate);
            foreach ($period as $date) {
                $labels[] = $date->format('d M');
                $profit = $this->getProfitForDate($date->toDateString());
                $data[] = round($profit, 2);
            }
            $maxValue = $this->roundProfitMaxValueForChart(max($data));
            return response()->json([
                'labels' => $labels,
                'data' => $data,
                'maxValue' => $maxValue
            ]);
        }
        $period = $request->input('period', 7); // 30, 90, 180, 365
        $now = now();
        if ($period == 30) {
            $start = $now->copy()->startOfMonth();
            $end = $now;
            $periodRange = \Carbon\CarbonPeriod::create($start, $end);
            foreach ($periodRange as $date) {
                $labels[] = $date->format('d M');
                $profit = $this->getProfitForDate($date->toDateString());
                $data[] = round($profit, 2);
            }
            $maxValue = $this->roundProfitMaxValueForChart(max($data));
            return response()->json([
                'labels' => $labels,
                'data' => $data,
                'maxValue' => $maxValue
            ]);
        }
        $firstAppointment = \App\Models\Clients\Appointment::orderBy('created_at', 'asc')->first();
        $firstDate = $firstAppointment ? \Carbon\Carbon::parse($firstAppointment->created_at) : $now;
        $daysSinceStart = $now->diffInDays($firstDate);
        $days = 0;
        if ($period == 90) $days = 89;
        elseif ($period == 180) $days = 179;
        elseif ($period == 365) $days = 364;
        $maxDays = min($days, $daysSinceStart);
        for ($i = $maxDays; $i >= 0; $i--) {
            $date = $now->copy()->subDays($i)->toDateString();
            $labels[] = $now->copy()->subDays($i)->format('d M');
            $profit = $this->getProfitForDate($date);
            $data[] = round($profit, 2);
        }
        $maxValue = $this->roundProfitMaxValueForChart(max($data));
        return response()->json([
            'labels' => $labels,
            'data' => $data,
            'maxValue' => $maxValue
        ]);
    }

    /**
     * Получить прибыль за конкретную дату (день)
     */
    private function getProfitForDate($date)
    {
        $currentProjectId = auth()->user()->project_id;
        $productsProfit = \App\Models\Clients\SaleItem::whereHas('sale', function($q) use ($date, $currentProjectId) {
            $q->whereDate('date', $date)->where('project_id', $currentProjectId);
        })
        ->selectRaw('SUM((retail_price - wholesale_price) * quantity) as profit')
        ->value('profit') ?? 0;
        $servicesProfit = \App\Models\Clients\Appointment::whereDate('date', $date)
            ->where('status', 'completed')
            ->where('project_id', $currentProjectId)
            ->sum('price');
        $expenses = \App\Models\Clients\Expense::whereDate('date', $date)
            ->where('project_id', $currentProjectId)
            ->sum('amount');
        $purchases = \App\Models\Clients\Purchase::whereDate('date', $date)
            ->where('project_id', $currentProjectId)
            ->sum('total_amount');
        return $productsProfit + $servicesProfit - $expenses - $purchases;
    }

    /**
     * Получить прибыль за период (от и до)
     */
    private function getProfitForPeriod($start, $end)
    {
        $currentProjectId = auth()->user()->project_id;
        $productsProfit = \App\Models\Clients\SaleItem::whereHas('sale', function($q) use ($start, $end, $currentProjectId) {
            $q->whereBetween('date', [$start, $end])->where('project_id', $currentProjectId);
        })
        ->selectRaw('SUM((retail_price - wholesale_price) * quantity) as profit')
        ->value('profit') ?? 0;
        $servicesProfit = \App\Models\Clients\Appointment::whereBetween('date', [$start, $end])
            ->where('project_id', $currentProjectId)
            ->sum('price');
        $expenses = \App\Models\Clients\Expense::whereBetween('date', [$start, $end])
            ->where('project_id', $currentProjectId)
            ->sum('amount');
        $purchases = \App\Models\Clients\Purchase::whereBetween('date', [$start, $end])
            ->where('project_id', $currentProjectId)
            ->sum('total_amount');
        return $productsProfit + $servicesProfit - $expenses - $purchases;
    }

    /**
     * API: Получить динамику продаж товаров для графика (по дням/неделям/месяцам)
     */
    public function salesChartData(Request $request)
    {
        $startDate = $request->input('start_date');
        $endDate = $request->input('end_date');
        $labels = [];
        $data = [];
        if ($startDate && $endDate) {
            $period = \Carbon\CarbonPeriod::create($startDate, $endDate);
            foreach ($period as $date) {
                $labels[] = $date->format('d M');
                $sales = $this->getSalesForDate($date->toDateString());
                $data[] = round($sales, 2);
            }
            return response()->json([
                'labels' => $labels,
                'data' => $data
            ]);
        }
        $period = $request->input('period', 7); // 7, 30, 90
        $now = now();
        if ($period == 30) {
            $start = $now->copy()->startOfMonth();
            $end = $now;
            $periodRange = \Carbon\CarbonPeriod::create($start, $end);
            foreach ($periodRange as $date) {
                $labels[] = $date->format('d M');
                $sales = $this->getSalesForDate($date->toDateString());
                $data[] = round($sales, 2);
            }
            return response()->json([
                'labels' => $labels,
                'data' => $data
            ]);
        }
        $firstSale = \App\Models\Clients\Sale::orderBy('date', 'asc')->first();
        $firstDate = $firstSale ? \Carbon\Carbon::parse($firstSale->date) : $now;
        $daysSinceStart = $now->diffInDays($firstDate);
        $days = 0;
        if ($period == 7) $days = 6;
        elseif ($period == 90) $days = 89;
        elseif ($period == 180) $days = 179;
        elseif ($period == 365) $days = 364;
        $maxDays = min($days, $daysSinceStart);
        for ($i = $maxDays; $i >= 0; $i--) {
            $date = $now->copy()->subDays($i)->toDateString();
            $labels[] = $now->copy()->subDays($i)->format('d M');
            $sales = $this->getSalesForDate($date);
            $data[] = round($sales, 2);
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
        $currentProjectId = auth()->user()->project_id;
        return \App\Models\Clients\SaleItem::whereHas('sale', function($q) use ($date, $currentProjectId) {
                $q->whereDate('date', $date)->where('project_id', $currentProjectId);
            })
            ->selectRaw('SUM(retail_price * quantity) as sales')
            ->value('sales') ?? 0;
    }

    /**
     * Получить выручку по продажам товаров за период (от и до)
     */
    private function getSalesForPeriod($start, $end)
    {
        $currentProjectId = auth()->user()->project_id;
        return \App\Models\Clients\SaleItem::whereHas('sale', function($q) use ($start, $end, $currentProjectId) {
                $q->whereBetween('date', [$start, $end])->where('project_id', $currentProjectId);
            })
            ->selectRaw('SUM(retail_price * quantity) as sales')
            ->value('sales') ?? 0;
    }

    /**
     * API: Получить динамику продаж услуг для графика (по дням/неделям/месяцам)
     */
    public function servicesChartData(Request $request)
    {
        $currentProjectId = auth()->user()->project_id;
        $startDate = $request->input('start_date');
        $endDate = $request->input('end_date');
        $labels = [];
        $data = [];
        if ($startDate && $endDate) {
            $period = \Carbon\CarbonPeriod::create($startDate, $endDate);
            foreach ($period as $date) {
                $labels[] = $date->format('d M');
                $sum = \App\Models\Clients\Appointment::whereDate('date', $date->toDateString())
                    ->where('status', 'completed')
                    ->where('project_id', $currentProjectId)
                    ->sum('price');
                $data[] = round($sum, 2);
            }
            return response()->json([
                'labels' => $labels,
                'data' => $data
            ]);
        }
        $period = $request->input('period', 7); // 7, 30, 90
        $now = now();
        if ($period == 30) {
            $start = $now->copy()->startOfMonth();
            $end = $now;
            $periodRange = \Carbon\CarbonPeriod::create($start, $end);
            foreach ($periodRange as $date) {
                $labels[] = $date->format('d M');
                $sum = \App\Models\Clients\Appointment::whereDate('date', $date->toDateString())
                    ->where('status', 'completed')
                    ->where('project_id', $currentProjectId)
                    ->sum('price');
                $data[] = round($sum, 2);
            }
            return response()->json([
                'labels' => $labels,
                'data' => $data
            ]);
        }
        $firstAppointment = \App\Models\Clients\Appointment::orderBy('date', 'asc')->first();
        $firstDate = $firstAppointment ? \Carbon\Carbon::parse($firstAppointment->date) : $now;
        $daysSinceStart = $now->diffInDays($firstDate);
        $days = 0;
        if ($period == 7) $days = 6;
        elseif ($period == 90) $days = 89;
        elseif ($period == 180) $days = 179;
        elseif ($period == 365) $days = 364;
        $maxDays = min($days, $daysSinceStart);
        for ($i = $maxDays; $i >= 0; $i--) {
            $date = $now->copy()->subDays($i)->toDateString();
            $labels[] = $now->copy()->subDays($i)->format('d M');
            $sum = \App\Models\Clients\Appointment::whereDate('date', $date)
                ->where('status', 'completed')
                ->where('project_id', $currentProjectId)
                ->sum('price');
            $data[] = round($sum, 2);
        }
        return response()->json([
            'labels' => $labels,
            'data' => $data
        ]);
    }

    /**
     * API: Получить динамику расходов для графика (по дням/неделям/месяцам)
     */
    public function expensesChartData(Request $request)
    {
        $currentProjectId = auth()->user()->project_id;
        $startDate = $request->input('start_date');
        $endDate = $request->input('end_date');
        $labels = [];
        $data = [];
        if ($startDate && $endDate) {
            $period = \Carbon\CarbonPeriod::create($startDate, $endDate);
            foreach ($period as $date) {
                $labels[] = $date->format('d M');
                $sum = \App\Models\Clients\Expense::whereDate('date', $date->toDateString())
                    ->where('project_id', $currentProjectId)
                    ->sum('amount');
                $data[] = round($sum, 2);
            }
            return response()->json([
                'labels' => $labels,
                'data' => $data
            ]);
        }
        $period = $request->input('period', 7); // 7, 30, 90
        $now = now();
        if ($period == 30) {
            $start = $now->copy()->startOfMonth();
            $end = $now;
            $periodRange = \Carbon\CarbonPeriod::create($start, $end);
            foreach ($periodRange as $date) {
                $labels[] = $date->format('d M');
                $sum = \App\Models\Clients\Expense::whereDate('date', $date->toDateString())
                    ->where('project_id', $currentProjectId)
                    ->sum('amount');
                $data[] = round($sum, 2);
            }
            return response()->json([
                'labels' => $labels,
                'data' => $data
            ]);
        }
        $firstExpense = \App\Models\Clients\Expense::orderBy('date', 'asc')->first();
        $firstPurchase = \App\Models\Clients\Purchase::orderBy('date', 'asc')->first();
        $firstDate = $now;
        $dates = [];
        if ($firstExpense && $firstExpense->date) $dates[] = \Carbon\Carbon::parse($firstExpense->date);
        if ($firstPurchase && $firstPurchase->date) $dates[] = \Carbon\Carbon::parse($firstPurchase->date);
        if (count($dates)) {
            $firstDate = min($dates);
        }
        $daysSinceStart = $now->diffInDays($firstDate);
        $days = 0;
        if ($period == 7) $days = 6;
        elseif ($period == 30) $days = 29;
        elseif ($period == 90) $days = 89;
        elseif ($period == 180) $days = 179;
        elseif ($period == 365) $days = 364;
        $maxDays = min($days, $daysSinceStart);
        for ($i = $maxDays; $i >= 0; $i--) {
            $date = $now->copy()->subDays($i)->toDateString();
            $labels[] = $now->copy()->subDays($i)->format('d M');
            $expenses = \App\Models\Clients\Expense::whereDate('date', $date)
                ->where('project_id', $currentProjectId)
                ->sum('amount');
            $purchases = \App\Models\Clients\Purchase::whereDate('date', $date)
                ->where('project_id', $currentProjectId)
                ->sum('total_amount');
            $data[] = round($expenses + $purchases, 2);
        }
        $maxValue = $this->roundExpensesMaxValueForChart(max($data));
        return response()->json([
            'labels' => $labels,
            'data' => $data,
            'maxValue' => $maxValue
        ]);
    }

    /**
     * API: Получить динамику активности (услуги, клиенты, записи) для графика
     */
    public function activityChartData(Request $request)
    {
        $currentProjectId = auth()->user()->project_id;
        $startDate = $request->input('start_date');
        $endDate = $request->input('end_date');
        $labels = [];
        $servicesData = [];
        $clientsData = [];
        $appointmentsData = [];
        if ($startDate && $endDate) {
            $period = \Carbon\CarbonPeriod::create($startDate, $endDate);
            foreach ($period as $date) {
                $labels[] = $date->format('d M');
                // Услуги: завершённые записи за день
                $servicesCount = \App\Models\Clients\Appointment::whereDate('date', $date->toDateString())
                    ->where('status', 'completed')
                    ->where('project_id', $currentProjectId)
                    ->count();
                // Клиенты: новые клиенты за день
                $clientsCount = \App\Models\Clients\Client::whereDate('created_at', $date->toDateString())
                    ->where('project_id', $currentProjectId)
                    ->count();
                // Записи: все записи за день (по дате услуги, а не по дате создания)
                $appointmentsCount = \App\Models\Clients\Appointment::whereDate('date', $date->toDateString())
                    ->where('project_id', $currentProjectId)
                    ->count();
                $servicesData[] = $servicesCount;
                $clientsData[] = $clientsCount;
                $appointmentsData[] = $appointmentsCount;
            }
            return response()->json([
                'labels' => $labels,
                'services' => $servicesData,
                'clients' => $clientsData,
                'appointments' => $appointmentsData
            ]);
        }
        $period = $request->input('period', 30); // 7, 30, 90, 180, 365
        $now = now();
        if ($period == 30) {
            $start = $now->copy()->startOfMonth();
            $end = $now;
            $periodRange = \Carbon\CarbonPeriod::create($start, $end);
            foreach ($periodRange as $date) {
                $labels[] = $date->format('d M');
                // Услуги: завершённые записи за день
                $servicesCount = \App\Models\Clients\Appointment::whereDate('date', $date->toDateString())
                    ->where('status', 'completed')
                    ->where('project_id', $currentProjectId)
                    ->count();
                // Клиенты: новые клиенты за день
                $clientsCount = \App\Models\Clients\Client::whereDate('created_at', $date->toDateString())
                    ->where('project_id', $currentProjectId)
                    ->count();
                // Записи: все записи за день (по дате услуги, а не по дате создания)
                $appointmentsCount = \App\Models\Clients\Appointment::whereDate('date', $date->toDateString())
                    ->where('project_id', $currentProjectId)
                    ->count();
                $servicesData[] = $servicesCount;
                $clientsData[] = $clientsCount;
                $appointmentsData[] = $appointmentsCount;
            }
            return response()->json([
                'labels' => $labels,
                'services' => $servicesData,
                'clients' => $clientsData,
                'appointments' => $appointmentsData
            ]);
        }
        $firstService = \App\Models\Clients\Appointment::orderBy('date', 'asc')->first();
        $firstClient = \App\Models\Clients\Client::orderBy('created_at', 'asc')->first();
        $firstAppointment = \App\Models\Clients\Appointment::orderBy('created_at', 'asc')->first();
        $firstDate = $now;
        $dates = [];
        if ($firstService) $dates[] = \Carbon\Carbon::parse($firstService->date);
        if ($firstClient) $dates[] = \Carbon\Carbon::parse($firstClient->created_at);
        if ($firstAppointment) $dates[] = \Carbon\Carbon::parse($firstAppointment->created_at);
        if (count($dates)) $firstDate = min($dates);
        $daysSinceStart = $now->diffInDays($firstDate);
        $maxDays = min($days, $daysSinceStart);
        for ($i = $maxDays; $i >= 0; $i--) {
            $date = $now->copy()->subDays($i)->toDateString();
            $labels[] = $now->copy()->subDays($i)->format('d M');
            // Услуги: завершённые записи за день
            $servicesCount = \App\Models\Clients\Appointment::whereDate('date', $date)
                ->where('status', 'completed')
                ->where('project_id', $currentProjectId)
                ->count();
            // Клиенты: новые клиенты за день
            $clientsCount = \App\Models\Clients\Client::whereDate('created_at', $date)
                ->where('project_id', $currentProjectId)
                ->count();
            // Записи: все записи за день (по дате услуги, а не по дате создания)
            $appointmentsCount = \App\Models\Clients\Appointment::whereDate('date', $date)
                ->where('project_id', $currentProjectId)
                ->count();
            $servicesData[] = $servicesCount;
            $clientsData[] = $clientsCount;
            $appointmentsData[] = $appointmentsCount;
        }
        return response()->json([
            'labels' => $labels,
            'services' => $servicesData,
            'clients' => $clientsData,
            'appointments' => $appointmentsData
        ]);
    }
}
