<?php

namespace App\Http\Controllers\Client;

use Illuminate\Http\Request;
use App\Models\Client;
use App\Models\Appointment;
use App\Models\ClientType;
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
        // Определяем дату первого клиента, чтобы не строить пустой график вначале.
        $firstClientDate = Client::query()->min('created_at');
        $firstClientDate = $firstClientDate ? Carbon::parse($firstClientDate) : Carbon::now();
        // Начало для графика динамики - не раньше первого клиента.
        $clientDynamicsStartDate = $periodRange[0]->copy()->max($firstClientDate);
        $clientDynamicsPeriodRange = [$clientDynamicsStartDate, $periodRange[1]];
        $clientDynamics = $this->getClientDynamics($periodName, $clientDynamicsPeriodRange);
        $topClientsByVisits = $this->getTopClientsByVisits($periodRange);
        $newVsReturning = $this->getNewVsReturningClients($periodRange);
        $clientTypesDistribution = $this->getClientTypesDistribution($periodRange);
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
    private function getClientDynamics(string $periodName, array $periodRange): array
    {
        if ($periodRange[0]->gt($periodRange[1])) {
            return ['labels' => [], 'data' => []];
        }

        $newTypeId = DB::table('client_types')->where('name', 'Новый клиент')->value('id');
        if (!$newTypeId) {
            return ['labels' => [], 'data' => []];
        }

        $dateGroupRaw = '';
        $periodIterator = null;
        $dateGroupFormatter = null;
        $labelFormatter = null;

        switch ($periodName) {
            case 'half_year':
                // Группировка по неделям, только с данными
                $dateGroupRaw = 'YEARWEEK(created_at, 1)'; // ISO week
                $rawData = Client::query()
                    ->whereBetween('created_at', $periodRange)
                    ->where('client_type_id', $newTypeId)
                    ->select(DB::raw($dateGroupRaw . ' as date_group'), DB::raw('COUNT(*) as count'))
                    ->groupBy('date_group')
                    ->orderBy('date_group')
                    ->get();
                $labels = $rawData->pluck('date_group')->map(function($week) {
                    $year = substr($week, 0, 4);
                    $w = (int)substr($week, 4);
                    $date = Carbon::now()->setISODate($year, $w)->startOfWeek();
                    return $date->format('d.m') . ' - ' . $date->copy()->endOfWeek()->format('d.m');
                });
                $values = $rawData->pluck('count');
                return ['labels' => $labels, 'data' => $values];
            case 'year':
                // Группировка по месяцам, только с данными
                $dateGroupRaw = 'DATE_FORMAT(created_at, "%Y-%m")';
                $rawData = Client::query()
                    ->whereBetween('created_at', $periodRange)
                    ->where('client_type_id', $newTypeId)
                    ->select(DB::raw($dateGroupRaw . ' as date_group'), DB::raw('COUNT(*) as count'))
                    ->groupBy('date_group')
                    ->orderBy('date_group')
                    ->get();
                $labels = $rawData->pluck('date_group')->map(fn($d) => Carbon::parse($d.'-01')->translatedFormat('M Y'));
                $values = $rawData->pluck('count');
                return ['labels' => $labels, 'data' => $values];
            case 'month':
                $dateGroupRaw = 'DATE(created_at)';
                $periodIterator = CarbonPeriod::create($periodRange[0], '1 day', $periodRange[1]);
                $dateGroupFormatter = fn(Carbon $date) => $date->format('Y-m-d');
                $labelFormatter = fn($dateGroup) => $dateGroup;
                break;
            case '2weeks':
            case 'week':
            default:
                $dateGroupRaw = 'DATE(created_at)';
                $periodIterator = CarbonPeriod::create($periodRange[0], '1 day', $periodRange[1]);
                $dateGroupFormatter = fn(Carbon $date) => $date->format('Y-m-d');
                $labelFormatter = fn($dateGroup) => $dateGroup;
                break;
        }

        $data = Client::query()
            ->whereBetween('created_at', $periodRange)
            ->where('client_type_id', $newTypeId)
            ->select(DB::raw($dateGroupRaw . ' as date_group'), DB::raw('COUNT(*) as count'))
            ->groupBy('date_group')
            ->get()
            ->keyBy('date_group');

        $scaffold = [];
        foreach ($periodIterator as $date) {
            $key = $dateGroupFormatter($date);
            $scaffold[$key] = $data->get($key)->count ?? 0;
        }

        $labels = collect(array_keys($scaffold))->map($labelFormatter);
        $values = array_values($scaffold);

        return ['labels' => $labels, 'data' => $values];
    }

    /**
     * Собирает данные по топ-5 клиентам по количеству визитов.
     */
    private function getTopClientsByVisits(array $periodRange): array
    {
        $data = Appointment::query()
            ->with('client')
            ->whereBetween('date', [$periodRange[0]->toDateString(), $periodRange[1]->toDateString()])
            ->select('client_id', DB::raw('COUNT(*) as visits_count'))
            ->groupBy('client_id')
            ->orderByDesc('visits_count')
            ->limit(5)
            ->get();

        $labels = $data->map(function ($item) {
            return $item->client ? $item->client->name : 'Удаленный клиент';
        });
        $values = $data->pluck('visits_count');

        return ['labels' => $labels, 'data' => $values];
    }

    /**
     * Собирает данные по новым и вернувшимся клиентам.
     */
    private function getNewVsReturningClients(array $periodRange): array
    {
        // Получаем количество визитов для каждого клиента за период
        $visits = Appointment::query()
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
    private function getClientTypesDistribution(array $periodRange): array
    {
        $data = Client::query()
            ->join('client_types', 'clients.client_type_id', '=', 'client_types.id')
            ->whereIn('clients.id', function ($query) use ($periodRange) {
                $query->select('client_id')
                    ->from('appointments')
                    ->whereBetween('date', [$periodRange[0]->toDateString(), $periodRange[1]->toDateString()]);
            })
            ->select('client_types.name', DB::raw('COUNT(DISTINCT clients.id) as count'))
            ->groupBy('client_types.name')
            ->get();
            
        return [
            'labels' => $data->pluck('name'),
            'data' => $data->pluck('count')
        ];
    }
} 