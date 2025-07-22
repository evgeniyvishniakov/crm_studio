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
        if (in_array($period, ['За полгода', 'За год'])) {
            $months = $period === 'За полгода' ? 6 : 12;
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
        $typeLabels = ['Товары', 'Услуги'];
        $typeCounts = [null, null]; // Не используем количество, только суммы
        $typeSums = [$goodsSum, $servicesTotalSum];

        // Динамика по дням (только по датам, где есть данные)
        $salesByDay = $salesQuery->with('items')->get()->groupBy(function($sale) {
            return $sale->date->format('Y-m-d');
        })->map(function($sales) {
            return $sales->flatMap->items->sum('total');
        });
        $purchasesByDay = $purchasesQuery->get()->groupBy(function($purchase) {
            return $purchase->date->format('Y-m-d');
        })->map(function($purchases) {
            return $purchases->sum('total_amount');
        });
        // Собираем только те даты, где есть хотя бы продажи или закупки, и которые попадают в диапазон
        $allDates = collect($salesByDay->keys())->merge($purchasesByDay->keys())
            ->unique()
            ->filter(function($date) use ($startDate, $endDate) {
                return (!$startDate || $date >= $startDate) && (!$endDate || $date <= $endDate);
            })
            ->sort()
            ->values();
        $dynamicLabels = $allDates->toArray();
        $dynamicSales = array_map(fn($d) => $salesByDay[$d] ?? 0, $dynamicLabels);
        $dynamicPurchases = array_map(fn($d) => $purchasesByDay[$d] ?? 0, $dynamicLabels);

        // Если нет данных за период — возвращаем пустые массивы для графиков
        if (empty($dynamicLabels)) {
            return response()->json([
                'category' => [ 'labels' => [], 'data' => [], 'sums' => [] ],
                'brand'    => [ 'labels' => [], 'data' => [], 'sums' => [] ],
                'supplier' => [ 'labels' => [], 'data' => [], 'sums' => [] ],
                'type'     => [ 'labels' => [], 'data' => [], 'sums' => [] ],
                'dynamic'  => [ 'labels' => [], 'sales' => [], 'purchases' => [] ],
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
                'sales' => $dynamicSales,
                'purchases' => $dynamicPurchases,
            ],
            // Добавляем реальные даты периода
            'actual_start_date' => $dynamicLabels[0] ?? $startDate,
            'actual_end_date' => end($dynamicLabels) ?: $endDate,
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

        // Топ-6 поставщиков по объёму закупок
        $topSuppliers = Supplier::where('project_id', $currentProjectId)
            ->with(['purchases' => function($q) use ($startDate, $endDate, $currentProjectId) {
                $q->where('project_id', $currentProjectId);
                if ($startDate) $q->whereDate('date', '>=', $startDate);
                if ($endDate) $q->whereDate('date', '<=', $endDate);
            }])->get()->map(function($sup) {
                return [
                    'label' => $sup->name,
                    'sum' => (float)$sup->purchases->sum('total_amount')
                ];
            })->sortByDesc('sum')->take(6)->values();

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
        })->filter(fn($row) => $row['qty'] > 0)->values();
        $stockTotalWholesale = $stockByCategory->sum('wholesale');
        $stockTotalRetail = $stockByCategory->sum('retail');

        // Товары с максимальным сроком без продажи (залежалые)
        $slowMovingProducts = Product::where('project_id', $currentProjectId)
            ->with('inventoryItem')
            ->get()
            ->filter(fn($p) => optional($p->inventoryItem)->quantity > 0)
            ->map(function($p) {
                $lastSale = $p->saleItems()->orderByDesc('created_at')->first();
                $days = $lastSale ? now()->diffInDays($lastSale->created_at) : null;
                return [
                    'label' => $p->name,
                    'days' => $days ?? 9999
                ];
            })
            ->sortByDesc('days')
            ->take(6)
            ->values();

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
            'topSuppliers' => $topSuppliers,
            'stockByCategory' => $stockByCategory,
            'stockTotalWholesale' => $stockTotalWholesale,
            'stockTotalRetail' => $stockTotalRetail,
            'slowMovingProducts' => $slowMovingProducts,
            'avgTurnoverDays' => $avgTurnoverDays,
        ]);
    }
} 