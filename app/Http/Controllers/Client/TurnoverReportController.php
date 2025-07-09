<?php

declare(strict_types=1);

namespace App\Http\Controllers\Client;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class TurnoverReportController extends Controller
{
    public function index()
    {
        return view('client.reports.turnover');
    }

    public function getDynamicAnalyticsData(Request $request)
    {
        $startDate = $request->input('start_date');
        $endDate = $request->input('end_date');

        // Фильтр по дате для продаж и закупок
        $salesQuery = \App\Models\Sale::query();
        $purchasesQuery = \App\Models\Purchase::query();
        $appointmentsQuery = \App\Models\Appointment::query();
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
        $categoryData = \App\Models\ProductCategory::with(['products.saleItems.sale'])->get();
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
        $brandData = \App\Models\ProductBrand::with(['products.saleItems.sale'])->get();
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
        $supplierData = \App\Models\Supplier::with(['purchases' => function($q) use ($startDate, $endDate) {
            if ($startDate) $q->whereDate('date', '>=', $startDate);
            if ($endDate) $q->whereDate('date', '<=', $endDate);
        }])->get();
        $supplierSums = $supplierData->map(function($sup) {
            return $sup->purchases->sum('total_amount');
        })->toArray();
        $supplierCounts = $supplierData->map(function($sup) {
            return $sup->purchases->flatMap->items->sum('quantity');
        })->toArray();
        $supplierLabels = $supplierData->pluck('name')->toArray();
        // Фильтрация по сумме > 0 (без unzip)
        $filteredSuppliers = [];
        foreach ($supplierLabels as $i => $label) {
            if ($supplierSums[$i] > 0) {
                $filteredSuppliers[] = [
                    'label' => $label,
                    'sum' => $supplierSums[$i],
                    'count' => $supplierCounts[$i]
                ];
            }
        }
        $supplierLabels = array_column($filteredSuppliers, 'label');
        $supplierSums = array_column($filteredSuppliers, 'sum');
        $supplierCounts = array_column($filteredSuppliers, 'count');

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
        // Собираем только те даты, где есть хотя бы продажи или закупки
        $allDates = collect($salesByDay->keys())->merge($purchasesByDay->keys())->unique()->sort()->values();
        $dynamicLabels = $allDates->toArray();
        $dynamicSales = array_map(fn($d) => $salesByDay[$d] ?? 0, $dynamicLabels);
        $dynamicPurchases = array_map(fn($d) => $purchasesByDay[$d] ?? 0, $dynamicLabels);

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
        ];
        return response()->json($data);
    }

    public function getTopsAnalyticsData(Request $request)
    {
        $startDate = $request->input('start_date');
        $endDate = $request->input('end_date');

        // Топ-6 товаров по продажам
        $topSales = \App\Models\SaleItem::query()
            ->when($startDate, fn($q) => $q->whereHas('sale', fn($sq) => $sq->whereDate('date', '>=', $startDate)))
            ->when($endDate, fn($q) => $q->whereHas('sale', fn($sq) => $sq->whereDate('date', '<=', $endDate)))
            ->selectRaw('product_id, SUM(quantity) as total_qty')
            ->groupBy('product_id')
            ->orderByDesc('total_qty')
            ->with('product')
            ->limit(6)
            ->get();
        $topSalesLabels = $topSales->map(fn($item) => $item->product?->name ?? '—')->toArray();
        $topSalesData = $topSales->map(fn($item) => is_numeric($item->total_qty) ? (int)floatval($item->total_qty) : 0)->toArray();

        // Топ-6 товаров по закупкам
        $topPurchases = \App\Models\PurchaseItem::query()
            ->when($startDate, fn($q) => $q->whereHas('purchase', fn($sq) => $sq->whereDate('date', '>=', $startDate)))
            ->when($endDate, fn($q) => $q->whereHas('purchase', fn($sq) => $sq->whereDate('date', '<=', $endDate)))
            ->selectRaw('product_id, SUM(quantity) as total_qty')
            ->groupBy('product_id')
            ->orderByDesc('total_qty')
            ->with('product')
            ->limit(6)
            ->get();
        $topPurchasesLabels = $topPurchases->map(fn($item) => $item->product?->name ?? '—')->toArray();
        $topPurchasesData = $topPurchases->map(fn($item) => is_numeric($item->total_qty) ? (int)floatval($item->total_qty) : 0)->toArray();

        // Топ-6 клиентов по объёму покупок
        $topClients = \App\Models\Sale::query()
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
        $startDate = $request->input('start_date');
        $endDate = $request->input('end_date');

        // Топ-6 поставщиков по объёму закупок
        $topSuppliers = \App\Models\Supplier::with(['purchases' => function($q) use ($startDate, $endDate) {
            if ($startDate) $q->whereDate('date', '>=', $startDate);
            if ($endDate) $q->whereDate('date', '<=', $endDate);
        }])->get()->map(function($sup) {
            return [
                'label' => $sup->name,
                'sum' => (float)$sup->purchases->sum('total_amount')
            ];
        })->sortByDesc('sum')->take(6)->values();

        // Остатки по категориям
        $categories = \App\Models\ProductCategory::with(['products.warehouse'])->get();
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
        $slowMovingProducts = \App\Models\Product::with('inventoryItem')
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
        $turnoverDays = \App\Models\Product::with('saleItems')->get()->map(function($p) {
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