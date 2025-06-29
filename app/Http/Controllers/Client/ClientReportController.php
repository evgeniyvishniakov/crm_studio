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
        $endDate = Carbon::today()->endOfDay();
        switch ($period) {
            case '2weeks':
                $startDate = Carbon::now()->subWeeks(2);
                break;
            case 'month':
                $startDate = Carbon::now()->subMonth();
                break;
            case 'half_year':
                $startDate = Carbon::now()->subMonths(6);
                break;
            case 'year':
                $startDate = Carbon::now()->subYear();
                break;
            case 'week':
            default:
                $startDate = Carbon::now()->subWeek();
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

        // Получаем id типа "Новый клиент"
        $newTypeId = DB::table('client_types')->where('name', 'Новый клиент')->value('id');
        if (!$newTypeId) {
            // Если типа "Новый клиент" нет, возвращаем пустой график
            return ['labels' => [], 'data' => []];
        }

        $dateGroupRaw = '';
        $periodIterator = null;
        $dateGroupFormatter = null;
        $labelFormatter = null;

        switch ($periodName) {
            case 'half_year': // Группировка по неделям для полугода
                $dateGroupRaw = 'DATE_FORMAT(created_at, "%x-%v")'; // ISO Year-Week
                $periodIterator = CarbonPeriod::create($periodRange[0], '1 week', $periodRange[1]);
                $dateGroupFormatter = fn(Carbon $date) => $date->isoFormat('GGGG-WW');
                $labelFormatter = function ($dateGroup) {
                    [$year, $week] = explode('-', $dateGroup);
                    $date = Carbon::now()->setISODate($year, $week)->startOfWeek();
                    return 'Нед. ' . $date->format('d.m');
                };
                break;

            case 'year': // Группировка по месяцам для года
                $dateGroupRaw = 'DATE_FORMAT(created_at, "%Y-%m")';
                $periodIterator = CarbonPeriod::create($periodRange[0]->startOfMonth(), '1 month', $periodRange[1]);
                $dateGroupFormatter = fn(Carbon $date) => $date->format('Y-m');
                $labelFormatter = fn($dateGroup) => Carbon::parse($dateGroup)->translatedFormat('M Y');
                break;

            case 'month': // Группировка по дням для месяца
            default: // 'week', '2weeks' - по дням
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
        $allClientsInPeriod = Appointment::query()
            ->whereBetween('date', [$periodRange[0]->toDateString(), $periodRange[1]->toDateString()])
            ->distinct('client_id')
            ->pluck('client_id');

        $newClientsCount = Client::query()
            ->whereIn('id', $allClientsInPeriod)
            ->where('created_at', '>=', $periodRange[0])
            ->count();

        $returningClientsCount = count($allClientsInPeriod) - $newClientsCount;
        
        return [
            'labels' => ['Первичные визиты', 'Повторные визиты'],
            'data' => [$newClientsCount, $returningClientsCount]
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