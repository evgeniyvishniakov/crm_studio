<?php

declare(strict_types=1);

namespace App\Http\Controllers\Client;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Clients\Sale;
use App\Models\Clients\Purchase;
use App\Models\Clients\Appointment;
use App\Models\Clients\ProductCategory;
use App\Models\Clients\ProductBrand;
use App\Models\Clients\Supplier;
use App\Models\Clients\SaleItem;
use App\Models\Clients\PurchaseItem;
use App\Models\Clients\Product;

class TurnoverReportController extends Controller
{
    public function index()
    {
        return view('client.reports.turnover');
    }

    public function getDynamicAnalyticsData(Request $request)
    {
        $currentProjectId = auth()->user()->project_id;
        $startDate = $request->input('start_date');
        $endDate = $request->input('end_date');

        // Корректируем startDate для "За полгода" и "За год":
        $period = $request->input('period');
        if (in_array($period, [__('messages.for_half_year'), __('messages.for_year')])) {
            $months = $period === __('messages.for_half_year') ? 6 : 12;
            $dateFrom = now()->copy()->subMonths($months - 1)->startOfMonth();
            $firstSale = Sale::where('project_id', $currentProjectId)->whereDate('date', '>=', $dateFrom->toDateString())
                ->orderBy('date', 'asc')
                ->first();
            if ($firstSale) {
                $startDate = \Carbon\Carbon::parse($firstSale->date)->startOfMonth()->toDateString();
            } else {
                $startDate = $dateFrom->toDateString();
            }
        }

        // Фильтр по дате для продаж и закупок
        $salesQuery = Sale::query()->where('project_id', $currentProjectId);
        $purchasesQuery = Purchase::query()->where('project_id', $currentProjectId);
        $appointmentsQuery = Appointment::query()->where('project_id', $currentProjectId);
        if ($startDate) {
            $salesQuery->whereDate('date', '>=', $startDate);
            $purchasesQuery->whereDate('date', '>=', $startDate);
            $appointmentsQuery->whereDate('date', '>=', $startDate);
        }
        if ($endDate) {
            $salesQuery->whereDate('date', '<=', $endDate);
            $purchasesQuery->whereDate('date', '<=', $endDate);
            $appointmentsQuery->whereDate('date', '<=', $endDate);
        }

        // Категории (количество и сумма проданных товаров)
        $categoryData = ProductCategory::with(['products' => function($q) use ($currentProjectId) {
            $q->where('project_id', $currentProjectId);
        }, 'products.saleItems.sale' => function($q) use ($currentProjectId) {
            $q->where('project_id', $currentProjectId);
        }])->where('project_id', $currentProjectId)->get();
        $categoryCounts = $categoryData->map(function($cat) use ($startDate, $endDate) {
            return $cat->products->flatMap->saleItems
                ->filter(function($item) use ($startDate, $endDate) {
                    if (!$item->sale) return false;
                    $date = $item->sale->date;
                    return (!$startDate || $date >= $startDate) && (!$endDate || $date <= $endDate);
                })
                ->sum('quantity');
        })->toArray();
        $categorySums = $categoryData->map(function($cat) use ($startDate, $endDate) {
            return $cat->products->flatMap->saleItems
                ->filter(function($item) use ($startDate, $endDate) {
                    if (!$item->sale) return false;
                    $date = $item->sale->date;
                    return (!$startDate || $date >= $startDate) && (!$endDate || $date <= $endDate);
                })
                ->sum('total');
        })->toArray();
        $categoryLabels = $categoryData->pluck('name')->toArray();
        // Фильтрация по количеству > 0 (без unzip)
        $filteredCategories = [];
        foreach ($categoryLabels as $i => $label) {
            if ($categoryCounts[$i] > 0) {
                $filteredCategories[] = ['label' => $label, 'count' => $categoryCounts[$i], 'sum' => $categorySums[$i]];
            }
        }
        // Сортировка по количеству (или по сумме, если нужно)
        usort($filteredCategories, function($a, $b) {
            return $b['count'] <=> $a['count'];
        });
        $topCategories = array_slice($filteredCategories, 0, 5);
        $otherCategories = array_slice($filteredCategories, 5);
        if (count($otherCategories) > 0) {
            $otherCount = array_sum(array_column($otherCategories, 'count'));
            $otherSum = array_sum(array_column($otherCategories, 'sum'));
            $topCategories[] = ['label' => 'Остальные', 'count' => $otherCount, 'sum' => $otherSum];
        }
        $categoryLabels = array_column($topCategories, 'label');
        $categoryCounts = array_column($topCategories, 'count');
        $categorySums = array_column($topCategories, 'sum');

        // Бренды
        $brandData = ProductBrand::with(['products' => function($q) use ($currentProjectId) {
            $q->where('project_id', $currentProjectId);
        }, 'products.saleItems.sale' => function($q) use ($currentProjectId) {
            $q->where('project_id', $currentProjectId);
        }])->where('project_id', $currentProjectId)->get();
        $brandCounts = $brandData->map(function($brand) use ($startDate, $endDate) {
            return $brand->products->flatMap->saleItems
                ->filter(function($item) use ($startDate, $endDate) {
                    if (!$item->sale) return false;
                    $date = $item->sale->date;
                    return (!$startDate || $date >= $startDate) && (!$endDate || $date <= $endDate);
                })
                ->sum('quantity');
        })->toArray();
        $brandSums = $brandData->map(function($brand) use ($startDate, $endDate) {
            return $brand->products->flatMap->saleItems
                ->filter(function($item) use ($startDate, $endDate) {
                    if (!$item->sale) return false;
                    $date = $item->sale->date;
                    return (!$startDate || $date >= $startDate) && (!$endDate || $date <= $endDate);
                })
                ->sum('total');
        })->toArray();
        $brandLabels = $brandData->pluck('name')->toArray();
        // Фильтрация по количеству > 0 (без unzip)
        $filteredBrands = [];
        foreach ($brandLabels as $i => $label) {
            if ($brandCounts[$i] > 0) {
                $filteredBrands[] = ['label' => $label, 'count' => $brandCounts[$i], 'sum' => $brandSums[$i]];
            }
        }
        // Сортировка по количеству (или по сумме, если нужно)
        usort($filteredBrands, function($a, $b) {
            return $b['count'] <=> $a['count'];
        });
        $topBrands = array_slice($filteredBrands, 0, 5);
        $otherBrands = array_slice($filteredBrands, 5);
        if (count($otherBrands) > 0) {
            $otherCount = array_sum(array_column($otherBrands, 'count'));
            $otherSum = array_sum(array_column($otherBrands, 'sum'));
            $topBrands[] = ['label' => 'Остальные', 'count' => $otherCount, 'sum' => $otherSum];
        }
        $brandLabels = array_column($topBrands, 'label');
        $brandCounts = array_column($topBrands, 'count');
        $brandSums = array_column($topBrands, 'sum');

        // Поставщики
        $supplierData = Supplier::with(['purchases' => function($q) use ($startDate, $endDate, $currentProjectId) {
            $q->where('project_id', $currentProjectId);
            if ($startDate) $q->whereDate('date', '>=', $startDate);
            if ($endDate) $q->whereDate('date', '<=', $endDate);
        }])->where('project_id', $currentProjectId)->get();
        
        $allSuppliers = $supplierData->map(function($sup) {
            return [
                'label' => $sup->name,
                'sum' => $sup->purchases->sum('total_amount'),
                'count' => $sup->purchases->flatMap->items->sum('quantity')
            ];
        })->filter(function($sup) {
            return $sup['sum'] > 0;
        })->sortByDesc('sum')->values();

        if ($allSuppliers->count() > 5) {
            $topSuppliers = $allSuppliers->slice(0, 5);
            $otherSuppliers = $allSuppliers->slice(5);

            $otherSum = $otherSuppliers->sum('sum');
            $otherCount = $otherSuppliers->sum('count');

            $topSuppliers->push([
                'label' => 'Остальные',
                'sum' => $otherSum,
                'count' => $otherCount
            ]);
            $filteredSuppliers = $topSuppliers;
        } else {
            $filteredSuppliers = $allSuppliers;
            }

        $supplierLabels = $filteredSuppliers->pluck('label')->toArray();
        $supplierSums = $filteredSuppliers->pluck('sum')->toArray();
        $supplierCounts = $filteredSuppliers->pluck('count')->toArray();

        // Типы (Товары/Услуги) — считаем сумму
        $allSaleItems = $salesQuery->with('items')->get()->flatMap->items;
        $retailSum = $allSaleItems->sum(function($item) {
            return $item->retail_price * $item->quantity;
        });
        $wholesaleSum = $allSaleItems->sum(function($item) {
            return $item->wholesale_price * $item->quantity;
        });
        $goodsSum = $retailSum - $wholesaleSum;
        $servicesTotalSum = $appointmentsQuery->sum('price');
        $typeLabels = [__('messages.products'), __('messages.services')];
        $typeCounts = [null, null]; // Не используем количество, только суммы
        $typeSums = [$goodsSum, $servicesTotalSum];

        // Определяем интервал группировки в зависимости от периода
        $groupInterval = 'day'; // По умолчанию по дням
        if ($period === __('messages.for_half_year')) {
            $groupInterval = 'week'; // За полгода - по неделям
        } elseif ($period === __('messages.for_year')) {
            $groupInterval = 'biweek'; // За год - по 2 недели
        }

        // Функция для группировки дат
        $dateGroupFunction = function($date) use ($groupInterval) {
            $carbonDate = \Carbon\Carbon::parse($date);
            switch ($groupInterval) {
                case 'week':
                    // Группируем по неделям (понедельник каждой недели)
                    return $carbonDate->startOfWeek()->format('Y-m-d');
                case 'biweek':
                    // Группируем по 2 недели
                    $weekNumber = $carbonDate->weekOfYear;
                    $biweekNumber = ceil($weekNumber / 2);
                    return $carbonDate->startOfYear()->addWeeks(($biweekNumber - 1) * 2)->startOfWeek()->format('Y-m-d');
                default:
                    // По дням
                    return $carbonDate->format('Y-m-d');
            }
        };

        // Динамика с учетом интервала
        $salesByPeriod = $salesQuery->with('items')->get()->groupBy(function($sale) use ($dateGroupFunction) {
            return $dateGroupFunction($sale->date->format('Y-m-d'));
        })->map(function($sales) {
            return $sales->flatMap->items->sum('total');
        });

        $purchasesByPeriod = $purchasesQuery->get()->groupBy(function($purchase) use ($dateGroupFunction) {
            return $dateGroupFunction($purchase->date->format('Y-m-d'));
        })->map(function($purchases) {
            return $purchases->sum('total_amount');
        });

        // Расчет валовой прибыли с учетом интервала
        $grossProfitByPeriod = $salesQuery->with('items')->get()->groupBy(function ($sale) use ($dateGroupFunction) {
            return $dateGroupFunction($sale->date->format('Y-m-d'));
        })->map(function ($sales) {
            return $sales->flatMap->items->sum(function ($item) {
                // Валовая прибыль = (Розничная цена - Закупочная цена) * Количество
                return ($item->retail_price - ($item->wholesale_price ?? 0)) * $item->quantity;
            });
        });

        // Собираем все периоды и фильтруем по диапазону
        $allPeriods = collect($salesByPeriod->keys())->merge($purchasesByPeriod->keys())
            ->unique()
            ->filter(function($period) use ($startDate, $endDate) {
                return (!$startDate || $period >= $startDate) && (!$endDate || $period <= $endDate);
            })
            ->sort()
            ->values();

        // Лейблы для отображения и оригинальные даты для парсинга
        $dynamicLabels = $allPeriods->map(function($period) use ($groupInterval) {
            $date = \Carbon\Carbon::parse($period);
            switch ($groupInterval) {
                case 'week':
                case 'biweek':
                    // Простой формат: 26.05.25
                    return $date->format('d.m.y');
                default:
                    return $date->format('d.m.y');
            }
        })->toArray();
        
        // Оригинальные даты для JavaScript парсинга
        $dynamicDatesForJS = $allPeriods->toArray();

        $dynamicSales = $allPeriods->map(fn($p) => $salesByPeriod[$p] ?? 0)->toArray();
        $dynamicPurchases = $allPeriods->map(fn($p) => $purchasesByPeriod[$p] ?? 0)->toArray();
        $dynamicGrossProfit = $allPeriods->map(fn($p) => $grossProfitByPeriod[$p] ?? 0)->toArray();


        // Если нет данных за период — возвращаем пустые массивы для графиков
        if ($allPeriods->isEmpty()) {
            return response()->json([
                'category' => [ 'labels' => [], 'data' => [], 'sums' => [] ],
                'brand'    => [ 'labels' => [], 'data' => [], 'sums' => [] ],
                'supplier' => [ 'labels' => [], 'data' => [], 'sums' => [] ],
                'type'     => [ 'labels' => [], 'data' => [], 'sums' => [] ],
                'dynamic'  => [ 'labels' => [], 'sales' => [], 'purchases' => [], 'gross_profit' => [] ],
            ]);
        }

        $data = [
            'category' => [
                'labels' => $categoryLabels,
                'data' => $categoryCounts,
                'sums' => $categorySums,
            ],
            'brand' => [
                'labels' => $brandLabels,
                'data' => $brandCounts,
                'sums' => $brandSums,
            ],
            'supplier' => [
                'labels' => $supplierLabels,
                'data' => $supplierSums,
                'counts' => $supplierCounts,
            ],
            'type' => [
                'labels' => $typeLabels,
                'data' => $typeSums, // теперь data = суммы
                'sums' => $typeSums,
            ],
            'dynamic' => [
                'labels' => $dynamicLabels,
                'dates' => $dynamicDatesForJS,
                'sales' => $dynamicSales,
                'purchases' => $dynamicPurchases,
                'gross_profit' => $dynamicGrossProfit,
            ],
            // Добавляем реальные даты периода
            'actual_start_date' => $allPeriods->first() ?? $startDate,
            'actual_end_date' => $allPeriods->last() ?? $endDate,
        ];
        return response()->json($data);
    }

    public function getTopsAnalyticsData(Request $request)
    {
        $currentProjectId = auth()->user()->project_id;
        $startDate = $request->input('start_date');
        $endDate = $request->input('end_date');

        // Топ-6 товаров по продажам
        $topSales = SaleItem::query()
            ->whereHas('sale', function($q) use ($currentProjectId, $startDate, $endDate) {
                $q->where('project_id', $currentProjectId);
                if ($startDate) $q->whereDate('date', '>=', $startDate);
                if ($endDate) $q->whereDate('date', '<=', $endDate);
            })
            ->selectRaw('product_id, SUM(quantity) as total_qty')
            ->groupBy('product_id')
            ->orderByDesc('total_qty')
            ->with('product')
            ->limit(6)
            ->get();
        $topSalesLabels = $topSales->map(fn($item) => $item->product?->name ?? '—')->toArray();
        $topSalesData = $topSales->map(fn($item) => is_numeric($item->total_qty) ? (int)floatval($item->total_qty) : 0)->toArray();

        // Топ-6 товаров по закупкам
        $topPurchases = PurchaseItem::query()
            ->whereHas('purchase', function($q) use ($currentProjectId, $startDate, $endDate) {
                $q->where('project_id', $currentProjectId);
                if ($startDate) $q->whereDate('date', '>=', $startDate);
                if ($endDate) $q->whereDate('date', '<=', $endDate);
            })
            ->selectRaw('product_id, SUM(quantity) as total_qty')
            ->groupBy('product_id')
            ->orderByDesc('total_qty')
            ->with('product')
            ->limit(6)
            ->get();
        $topPurchasesLabels = $topPurchases->map(fn($item) => $item->product?->name ?? '—')->toArray();
        $topPurchasesData = $topPurchases->map(fn($item) => is_numeric($item->total_qty) ? (int)floatval($item->total_qty) : 0)->toArray();

        // Топ-6 клиентов по объёму покупок
        $topClients = Sale::query()
            ->where('project_id', $currentProjectId)
            ->when($startDate, fn($q) => $q->whereDate('date', '>=', $startDate))
            ->when($endDate, fn($q) => $q->whereDate('date', '<=', $endDate))
            ->selectRaw('client_id, SUM(total_amount) as total_sum')
            ->groupBy('client_id')
            ->orderByDesc('total_sum')
            ->with('client')
            ->limit(6)
            ->get();
        $topClientsLabels = $topClients->map(fn($item) => $item->client?->name ?? '—')->toArray();
        $topClientsData = $topClients->map(fn($item) => (float)$item->total_sum)->toArray();

        return response()->json([
            'topSales' => [
                'labels' => $topSalesLabels,
                'data' => $topSalesData,
            ],
            'topPurchases' => [
                'labels' => $topPurchasesLabels,
                'data' => $topPurchasesData,
            ],
            'topClients' => [
                'labels' => $topClientsLabels,
                'data' => $topClientsData,
            ],
        ]);
    }

    public function suppliersAnalyticsData(Request $request)
    {
        $currentProjectId = auth()->user()->project_id;
        $startDate = $request->input('start_date');
        $endDate = $request->input('end_date');

        // Получаем данные по всем поставщикам
        $allSuppliersData = Supplier::where('project_id', $currentProjectId)
            ->with(['purchases' => function($q) use ($startDate, $endDate) {
                if ($startDate) $q->whereDate('date', '>=', $startDate);
                if ($endDate) $q->whereDate('date', '<=', $endDate);
            }])->get();

        $allSuppliers = $allSuppliersData->map(function($sup) {
                return [
                    'label' => $sup->name,
                    'sum' => (float)$sup->purchases->sum('total_amount')
                ];
        })->filter(fn($s) => $s['sum'] > 0)->sortByDesc('sum')->values();

        // Данные для столбчатой диаграммы (Топ-6)
        $topSuppliersForBar = $allSuppliers->take(6);

        // Данные для круговой диаграммы (Топ-5 + Остальные)
        $supplierStructureForPie = $allSuppliers;
        if ($allSuppliers->count() > 5) {
            $topPart = $allSuppliers->slice(0, 5);
            $otherSum = $allSuppliers->slice(5)->sum('sum');
            $topPart->push(['label' => 'Остальные', 'sum' => $otherSum]);
            $supplierStructureForPie = $topPart;
        }

        // Остатки по категориям
        $categories = ProductCategory::where('project_id', $currentProjectId)
            ->with(['products' => function($q) use ($currentProjectId) {
                $q->where('project_id', $currentProjectId)->with(['warehouse']);
            }])->get();
        $stockByCategory = $categories->map(function($cat) {
            $qty = $cat->products->sum(fn($p) => optional($p->warehouse)->quantity ?? 0);
            $wholesale = $cat->products->sum(fn($p) => (optional($p->warehouse)->quantity ?? 0) * ((float)optional($p->warehouse)->purchase_price ?? 0));
            $retail = $cat->products->sum(fn($p) => (optional($p->warehouse)->quantity ?? 0) * ((float)optional($p->warehouse)->retail_price ?? 0));
            return [
                'label' => $cat->name,
                'qty' => (int)$qty,
                'wholesale' => round($wholesale),
                'retail' => round($retail)
            ];
        })->filter(fn($row) => $row['qty'] > 0)->sortByDesc('qty')->values();

        // Оставляем только топ-5 категорий, остальные объединяем в "Остальные"
        if ($stockByCategory->count() > 5) {
            $topCategories = $stockByCategory->slice(0, 5);
            $other = $stockByCategory->slice(5);
            $otherQty = $other->sum('qty');
            $otherWholesale = $other->sum('wholesale');
            $otherRetail = $other->sum('retail');
            $topCategories->push([
                'label' => 'Остальные',
                'qty' => $otherQty,
                'wholesale' => $otherWholesale,
                'retail' => $otherRetail
            ]);
            $stockByCategory = $topCategories->values();
        }

        $stockTotalQty = $stockByCategory->sum('qty');

        // Средний срок оборачиваемости (по всем товарам с продажами)
        $turnoverDays = Product::where('project_id', $currentProjectId)
            ->with('saleItems')
            ->get()->map(function($p) {
                $firstPurchase = $p->purchaseItems()->orderBy('created_at')->first();
                $firstSale = $p->saleItems()->orderBy('created_at')->first();
                if ($firstPurchase && $firstSale) {
                    return $firstSale->created_at->diffInDays($firstPurchase->created_at);
                }
                return null;
            })->filter()->avg();
        $avgTurnoverDays = $turnoverDays ? round($turnoverDays) : null;

        return response()->json([
            'topSuppliers' => $topSuppliersForBar,
            'supplierStructure' => $supplierStructureForPie,
            'stockByCategory' => $stockByCategory,
            'stockTotalQty' => $stockTotalQty,
            'stockTotalWholesale' => $stockByCategory->sum('wholesale'),
            'stockTotalRetail' => $stockByCategory->sum('retail'),
            'avgTurnoverDays' => $avgTurnoverDays,
        ]);
    }

    /**
     * Аналитика: расходы по месяцам
     */
    public function expensesByMonth(Request $request)
    {
        $currentProjectId = auth()->user()->project_id;
        $startDate = $request->input('start_date');
        $endDate = $request->input('end_date');
        $query = \App\Models\Clients\Expense::where('project_id', $currentProjectId);
        if ($startDate) $query->whereDate('date', '>=', $startDate);
        if ($endDate) $query->whereDate('date', '<=', $endDate);
        $expenses = $query->get();
        $months = $expenses->groupBy(function($item) { return \Carbon\Carbon::parse($item->date)->format('Y-m'); });
        $labels = [];
        $data = [];
        foreach ($months as $month => $items) {
            $labels[] = \Carbon\Carbon::parse($month.'-01')->format('m.Y');
            $data[] = $items->sum('amount');
        }
        return response()->json(['labels' => $labels, 'data' => $data]);
    }

    /**
     * Аналитика: структура расходов по категориям
     */
    public function expensesByCategory(Request $request)
    {
        $currentProjectId = auth()->user()->project_id;
        $startDate = $request->input('start_date');
        $endDate = $request->input('end_date');
        $query = \App\Models\Clients\Expense::where('project_id', $currentProjectId);
        if ($startDate) $query->whereDate('date', '>=', $startDate);
        if ($endDate) $query->whereDate('date', '<=', $endDate);
        $expenses = $query->get();
        $categories = config('expenses.categories', []);
        $labels = array_map(function($category) {
            return __('messages.' . $category);
        }, $categories);
        $data = [];
        foreach ($categories as $cat) {
            $data[] = $expenses->where('category', $cat)->sum('amount');
        }
        return response()->json(['labels' => $labels, 'data' => $data]);
    }

    /**
     * Аналитика: динамика расходов по категориям
     */
    public function expensesCategoryDynamics(Request $request)
    {
        $currentProjectId = auth()->user()->project_id;
        $startDate = $request->input('start_date');
        $endDate = $request->input('end_date');
        $query = \App\Models\Clients\Expense::where('project_id', $currentProjectId);
        if ($startDate) $query->whereDate('date', '>=', $startDate);
        if ($endDate) $query->whereDate('date', '<=', $endDate);
        $expenses = $query->get();
        $categories = config('expenses.categories', []);
        $months = $expenses->groupBy(function($item) { return \Carbon\Carbon::parse($item->date)->format('Y-m'); });
        $labels = [];
        $datasets = [];
        foreach ($categories as $cat) {
            $datasets[$cat] = [];
        }
        foreach ($months as $month => $items) {
            $labels[] = \Carbon\Carbon::parse($month.'-01')->format('m.Y');
            foreach ($categories as $cat) {
                $datasets[$cat][] = $items->where('category', $cat)->sum('amount');
            }
        }
        return response()->json(['labels' => $labels, 'datasets' => $datasets]);
    }

    /**
     * Аналитика: средний расход по категориям
     */
    public function expensesAverageByCategory(Request $request)
    {
        $currentProjectId = auth()->user()->project_id;
        $startDate = $request->input('start_date');
        $endDate = $request->input('end_date');
        $query = \App\Models\Clients\Expense::where('project_id', $currentProjectId);
        if ($startDate) $query->whereDate('date', '>=', $startDate);
        if ($endDate) $query->whereDate('date', '<=', $endDate);
        $expenses = $query->get();
        $categories = config('expenses.categories', []);
        $monthCount = $expenses->groupBy(function($item) { return \Carbon\Carbon::parse($item->date)->format('Y-m'); })->count();
        $labels = array_map(function($category) {
            return __('messages.' . $category);
        }, $categories);
        $data = [];
        foreach ($categories as $cat) {
            $sum = $expenses->where('category', $cat)->sum('amount');
            $data[] = $monthCount ? round($sum / $monthCount, 2) : 0;
        }
        return response()->json(['labels' => $labels, 'data' => $data]);
    }

    /**
     * Аналитика: топ-3 месяца по расходам
     */
    public function expensesTopMonths(Request $request)
    {
        $currentProjectId = auth()->user()->project_id;
        $startDate = $request->input('start_date');
        $endDate = $request->input('end_date');
        $query = \App\Models\Clients\Expense::where('project_id', $currentProjectId);
        if ($startDate) $query->whereDate('date', '>=', $startDate);
        if ($endDate) $query->whereDate('date', '<=', $endDate);
        $expenses = $query->get();
        $months = $expenses->groupBy(function($item) { return \Carbon\Carbon::parse($item->date)->format('Y-m'); });
        $monthSums = [];
        foreach ($months as $month => $items) {
            $monthSums[$month] = $items->sum('amount');
        }
        arsort($monthSums);
        $labels = [];
        $data = [];
        foreach (array_slice($monthSums, 0, 3, true) as $month => $sum) {
            $labels[] = \Carbon\Carbon::parse($month.'-01')->format('m.Y');
            $data[] = $sum;
        }
        return response()->json(['labels' => $labels, 'data' => $data]);
    }

    /**
     * Аналитика: доля фиксированных и переменных расходов
     */
    public function expensesFixedVariable(Request $request)
    {
        $currentProjectId = auth()->user()->project_id;
        $startDate = $request->input('start_date');
        $endDate = $request->input('end_date');
        $query = \App\Models\Clients\Expense::where('project_id', $currentProjectId);
        if ($startDate) $query->whereDate('date', '>=', $startDate);
        if ($endDate) $query->whereDate('date', '<=', $endDate);
        $expenses = $query->get();
        $fixed = ['Аренда и коммуналка', 'Зарплата', 'Налоги'];
        $variable = ['Материалы', 'Реклама', 'Прочее'];
        $fixedSum = $expenses->whereIn('category', $fixed)->sum('amount');
        $variableSum = $expenses->whereIn('category', $variable)->sum('amount');
        return response()->json([
            'labels' => [__('messages.fixed'), __('messages.variable')],
            'data' => [$fixedSum, $variableSum]
        ]);
    }

    // --- Аналитика по сотрудникам ---
    public function employeesAnalytics(Request $request)
    {
        $currentProjectId = auth()->user()->project_id;
        $startDate = $request->input('start_date');
        $endDate = $request->input('end_date');
        
        // Получаем продажи с данными о сотрудниках
        $salesQuery = \App\Models\Clients\Sale::where('project_id', $currentProjectId);
        if ($startDate) $salesQuery->whereDate('date', '>=', $startDate);
        if ($endDate) $salesQuery->whereDate('date', '<=', $endDate);
        $sales = $salesQuery->get();

        // Получаем всех сотрудников проекта
        $employees = \App\Models\Admin\User::where('project_id', $currentProjectId)->get(['id', 'name'])->keyBy('id');

        // Топ-5 сотрудников по объему продаж
        $topEmployees = $sales->groupBy('employee_id')->map(function($sales, $id) use ($employees) {
            $name = $employees->get($id)->name ?? '—';
            return [
                'label' => $name,
                'sum' => $sales->sum('total_amount')
            ];
        })->sortByDesc('sum')->take(5)->values()->all();

        // Структура продаж по сотрудникам (доли)
        $employeesStructure = $sales->groupBy('employee_id')->map(function($sales, $id) use ($employees) {
            $name = $employees->get($id)->name ?? '—';
            return [
                'label' => $name,
                'sum' => $sales->sum('total_amount')
            ];
        })->values()->all();

        // Динамика продаж по сотрудникам (по месяцам)
        $employeesDynamics = [];
        $employeesDynamicsLabels = [];
        $byMonth = $sales->groupBy(function($sale) {
            return \Carbon\Carbon::parse($sale->date)->format('m.Y');
        });
        $allMonths = $byMonth->keys()->sort()->values()->all();
        
        // Получаем имена сотрудников из продаж
        $employeeNames = [];
        foreach ($sales->groupBy('employee_id') as $employeeId => $employeeSales) {
            $name = $employees->get($employeeId)->name ?? '—';
            $employeeNames[] = $name;
        }
        
        foreach ($employeeNames as $name) {
            $employeesDynamics[$name] = [];
            foreach ($allMonths as $month) {
                $sum = $byMonth[$month]->filter(function($sale) use ($employees, $name) {
                    $employeeId = $sale->employee_id;
                    $employeeName = $employees->get($employeeId)->name ?? '—';
                    return $employeeName === $name;
                })->sum('total_amount');
                $employeesDynamics[$name][] = $sum;
            }
        }
        $employeesDynamicsLabels = $allMonths;

        // Средний чек по сотрудникам
        $employeesAverage = [
            'labels' => [],
            'data' => []
        ];
        $avg = $sales->groupBy('employee_id')->map(function($sales, $id) use ($employees) {
            $name = $employees->get($id)->name ?? '—';
            return [
                'label' => $name,
                'avg' => $sales->avg('total_amount')
            ];
        })->values();
        foreach ($avg as $row) {
            $employeesAverage['labels'][] = $row['label'];
            $employeesAverage['data'][] = $row['avg'];
        }

        return response()->json([
            'topEmployees' => $topEmployees,
            'employeesStructure' => $employeesStructure,
            'employeesDynamics' => $employeesDynamics,
            'employeesDynamicsLabels' => $employeesDynamicsLabels,
            'employeesAverage' => $employeesAverage,
        ]);
    }
} 