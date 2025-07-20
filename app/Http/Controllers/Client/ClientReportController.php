<?php

namespace App\Http\Controllers\Client;

use Illuminate\Http\Request;
use App\Models\Clients\Client;
use App\Models\Clients\Appointment;
use App\Models\Clients\ClientType;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Carbon\CarbonPeriod;
use App\Http\Controllers\Controller;

class ClientReportController extends Controller
{
    /**
     * Отображает страницу отчетов по клиентам.
     *
     * @return \Illuminate\View\View
     */
    public function index()
    {
        // Пока мы просто возвращаем представление.
        // Позже здесь будет логика для сбора данных для отчетов.
        return view('client.reports.clients');
    }

    /**
     * Предоставляет данные для аналитики по клиентам.
     * Теперь поддерживает диапазон дат (start_date, end_date) или стандартный период.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function getClientAnalyticsData(Request $request)
    {
        $currentProjectId = auth()->user()->project_id;
        $startDate = $request->input('start_date');
        $endDate = $request->input('end_date');
        if ($startDate && $endDate) {
            $start = Carbon::parse($startDate)->startOfDay();
            $end = Carbon::parse($endDate)->endOfDay();
            $periodName = 'custom';
            $periodRange = [$start, $end];
        } else {
            $periodName = $request->input('period', 'week');
            $periodRange = $this->getDateRange($periodName);
        }
        $firstClientDate = Client::query()->where('project_id', $currentProjectId)->min('created_at');
        $firstClientDate = $firstClientDate ? Carbon::parse($firstClientDate) : Carbon::now();
        $clientDynamicsStartDate = $periodRange[0]->copy()->max($firstClientDate);
        $clientDynamicsPeriodRange = [$clientDynamicsStartDate, $periodRange[1]];
        $clientDynamics = $this->getClientDynamics($periodName, $clientDynamicsPeriodRange, $currentProjectId);
        $topClientsByVisits = $this->getTopClientsByVisits($periodRange, $currentProjectId);
        $newVsReturning = $this->getNewVsReturningClients($periodRange, $currentProjectId);
        $clientTypesDistribution = $this->getClientTypesDistribution($periodRange, $currentProjectId);
        return response()->json([
            'clientDynamics' => $clientDynamics,
            'topClientsByVisits' => $topClientsByVisits,
            'newVsReturning' => $newVsReturning,
            'clientTypesDistribution' => $clientTypesDistribution,
        ]);
    }

    /**
     * Определяет диапазон дат на основе выбранного периода.
     */
    private function getDateRange(string $period): array
    {
        $today = Carbon::today();
        switch ($period) {
            case 'week':
                // С последнего понедельника по сегодня
                $startDate = $today->copy()->startOfWeek();
                $endDate = $today->copy()->endOfDay();
                break;
            case '2weeks':
                // С предпоследнего понедельника по сегодня
                $startDate = $today->copy()->startOfWeek()->subWeek();
                $endDate = $today->copy()->endOfDay();
                break;
            case 'month':
                // С 1-го числа текущего месяца по сегодня
                $startDate = $today->copy()->startOfMonth();
                $endDate = $today->copy()->endOfDay();
                break;
            case 'half_year':
                // С 1-го числа месяца, в котором была первая запись за последние 6 месяцев
                $sixMonthsAgo = $today->copy()->subMonths(6)->startOfMonth();
                $firstClient = Client::query()->where('created_at', '>=', $sixMonthsAgo)->orderBy('created_at')->first();
                $startDate = $firstClient ? Carbon::parse($firstClient->created_at)->startOfMonth() : $sixMonthsAgo;
                $endDate = $today->copy()->endOfDay();
                break;
            case 'year':
                // С 1-го числа месяца, в котором была первая запись за последние 12 месяцев
                $yearAgo = $today->copy()->subYear()->startOfMonth();
                $firstClient = Client::query()->where('created_at', '>=', $yearAgo)->orderBy('created_at')->first();
                $startDate = $firstClient ? Carbon::parse($firstClient->created_at)->startOfMonth() : $yearAgo;
                $endDate = $today->copy()->endOfDay();
                break;
            default:
                // По умолчанию — неделя
                $startDate = $today->copy()->startOfWeek();
                $endDate = $today->copy()->endOfDay();
                break;
        }
        return [$startDate, $endDate];
    }

    /**
     * Собирает данные по динамике клиентской базы с заполнением пропусков.
     */
    private function getClientDynamics(string $periodName, array $periodRange, $currentProjectId): array
    {
        if ($periodRange[0]->gt($periodRange[1])) {
            return ['labels' => [], 'data' => []];
        }
        $dateGroupRaw = '';
        $labels = [];
        $data = [];
        switch ($periodName) {
            case 'half_year':
                $dateGroupRaw = 'YEARWEEK(created_at, 1)';
                // Считаем недели
                $start = $periodRange[0]->copy()->startOfWeek();
                $end = $periodRange[1]->copy()->endOfWeek();
                $periodIterator = \Carbon\CarbonPeriod::create($start, '1 week', $end);
                $allWeeks = collect();
                foreach ($periodIterator as $weekStart) {
                    $week = $weekStart->format('oW');
                    $allWeeks->push($week);
                }
                $rawData = \App\Models\Clients\Client::query()
                    ->where('project_id', $currentProjectId)
                    ->whereBetween('created_at', [$start, $end])
                    ->selectRaw($dateGroupRaw.' as date_group, COUNT(*) as count')
                    ->groupBy('date_group')
                    ->orderBy('date_group')
                    ->get()
                    ->keyBy('date_group');
                foreach ($allWeeks as $week) {
                    $labels[] = \Carbon\Carbon::now()->setISODate(substr($week, 0, 4), substr($week, 4))->startOfWeek()->format('d.m') . ' - ' . \Carbon\Carbon::now()->setISODate(substr($week, 0, 4), substr($week, 4))->endOfWeek()->format('d.m');
                    $data[] = $rawData->get($week)->count ?? 0;
                }
                break;
            case 'year':
                $dateGroupRaw = 'DATE_FORMAT(created_at, "%Y-%m")';
                // Считаем месяцы
                $start = $periodRange[0]->copy()->startOfMonth();
                $end = $periodRange[1]->copy()->endOfMonth();
                $periodIterator = \Carbon\CarbonPeriod::create($start, '1 month', $end);
                $allMonths = collect();
                foreach ($periodIterator as $monthStart) {
                    $allMonths->push($monthStart->format('Y-m'));
                }
                $rawData = \App\Models\Clients\Client::query()
                    ->where('project_id', $currentProjectId)
                    ->whereBetween('created_at', [$start, $end])
                    ->selectRaw($dateGroupRaw.' as date_group, COUNT(*) as count')
                    ->groupBy('date_group')
                    ->orderBy('date_group')
                    ->get()
                    ->keyBy('date_group');
                foreach ($allMonths as $month) {
                    $labels[] = \Carbon\Carbon::parse($month.'-01')->translatedFormat('M Y');
                    $data[] = $rawData->get($month)->count ?? 0;
                }
                break;
            default:
                $dateGroupRaw = 'DATE(created_at)';
                $periodIterator = \Carbon\CarbonPeriod::create($periodRange[0], '1 day', $periodRange[1]);
                $allDates = collect();
                foreach ($periodIterator as $date) {
                    $allDates->push($date->format('Y-m-d'));
                }
                $rawData = \App\Models\Clients\Client::query()
                    ->where('project_id', $currentProjectId)
                    ->whereBetween('created_at', $periodRange)
                    ->selectRaw($dateGroupRaw.' as date_group, COUNT(*) as count')
                    ->groupBy('date_group')
                    ->orderBy('date_group')
                    ->get()
                    ->keyBy('date_group');
                foreach ($allDates as $date) {
                    $labels[] = $date;
                    $data[] = $rawData->get($date)->count ?? 0;
                }
                break;
        }
        return [
            'labels' => $labels,
            'data' => $data
        ];
    }

    /**
     * Собирает данные по топ-5 клиентам по количеству визитов.
     */
    private function getTopClientsByVisits(array $periodRange, $currentProjectId): array
    {
        $data = Appointment::query()
            ->with('client')
            ->where('project_id', $currentProjectId)
            ->whereBetween('date', [$periodRange[0]->toDateString(), $periodRange[1]->toDateString()])
            ->select('client_id', DB::raw('COUNT(*) as visits_count'))
            ->groupBy('client_id')
            ->orderByDesc('visits_count')
            ->limit(5)
            ->get();
        $labels = $data->map(function ($item) {
            return $item->client ? $item->client->name : 'Удалённый клиент';
        });
        $values = $data->pluck('visits_count');
        return ['labels' => $labels, 'data' => $values];
    }

    /**
     * Собирает данные по новым и вернувшимся клиентам.
     */
    private function getNewVsReturningClients(array $periodRange, $currentProjectId): array
    {
        $visits = Appointment::query()
            ->where('project_id', $currentProjectId)
            ->whereBetween('date', [$periodRange[0]->toDateString(), $periodRange[1]->toDateString()])
            ->select('client_id', DB::raw('COUNT(*) as cnt'))
            ->groupBy('client_id')
            ->pluck('cnt');
        $primary = $visits->filter(fn($cnt) => $cnt === 1)->count();
        $returning = $visits->filter(fn($cnt) => $cnt > 1)->count();
        return [
            'labels' => ['Первичные визиты', 'Повторные визиты'],
            'data' => [$primary, $returning]
        ];
    }

    /**
     * Собирает данные по распределению клиентов по типам.
     */
    private function getClientTypesDistribution(array $periodRange, $currentProjectId): array
    {
        // Получаем id всех типов, которые глобальные или для этого проекта
        $typeIds = \App\Models\Clients\ClientType::where('is_global', true)
            ->orWhere('project_id', $currentProjectId)
            ->pluck('id');
        // Собираем распределение только по этим типам
        $clientTypesDistribution = \App\Models\Clients\Client::join('client_types', 'clients.client_type_id', '=', 'client_types.id')
            ->select('client_types.name', \DB::raw('COUNT(clients.id) as count'))
            ->where('clients.project_id', $currentProjectId)
            ->whereIn('client_types.id', $typeIds)
            ->whereBetween('clients.created_at', $periodRange)
            // убран фильтр по appointments
            ->groupBy('client_types.name')
            ->orderByDesc('count')
            ->get();
        $labels = $clientTypesDistribution->pluck('name');
        $values = $clientTypesDistribution->pluck('count');
        return ['labels' => $labels, 'data' => $values];
    }
} 