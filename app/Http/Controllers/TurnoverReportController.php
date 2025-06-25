<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class TurnoverReportController extends Controller
{
    public function index()
    {
        return view('reports.turnover');
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

        // Категории
        $categoryData = \App\Models\ProductCategory::with(['products.saleItems' => function($q) use ($startDate, $endDate) {
            $q->whereHas('sale', function($sq) use ($startDate, $endDate) {
                if ($startDate) $sq->whereDate('date', '>=', $startDate);
                if ($endDate) $sq->whereDate('date', '<=', $endDate);
            });
        }])->get();
        $categoryCounts = $categoryData->map(function($cat) {
            return $cat->products->flatMap->saleItems->sum('quantity');
        })->toArray();
        $categoryLabels = $categoryData->pluck('name')->toArray();
        // Фильтрация по количеству > 0 (без unzip)
        $filteredCategories = [];
        foreach ($categoryLabels as $i => $label) {
            if ($categoryCounts[$i] > 0) {
                $filteredCategories[] = ['label' => $label, 'count' => $categoryCounts[$i]];
            }
        }
        $categoryLabels = array_column($filteredCategories, 'label');
        $categoryCounts = array_column($filteredCategories, 'count');

        // Бренды
        $brandData = \App\Models\ProductBrand::with(['products.saleItems' => function($q) use ($startDate, $endDate) {
            $q->whereHas('sale', function($sq) use ($startDate, $endDate) {
                if ($startDate) $sq->whereDate('date', '>=', $startDate);
                if ($endDate) $sq->whereDate('date', '<=', $endDate);
            });
        }])->get();
        $brandCounts = $brandData->map(function($brand) {
            return $brand->products->flatMap->saleItems->sum('quantity');
        })->toArray();
        $brandLabels = $brandData->pluck('name')->toArray();
        // Фильтрация по количеству > 0 (без unzip)
        $filteredBrands = [];
        foreach ($brandLabels as $i => $label) {
            if ($brandCounts[$i] > 0) {
                $filteredBrands[] = ['label' => $label, 'count' => $brandCounts[$i]];
            }
        }
        $brandLabels = array_column($filteredBrands, 'label');
        $brandCounts = array_column($filteredBrands, 'count');

        // Поставщики
        $supplierData = \App\Models\Supplier::with(['purchases' => function($q) use ($startDate, $endDate) {
            if ($startDate) $q->whereDate('date', '>=', $startDate);
            if ($endDate) $q->whereDate('date', '<=', $endDate);
        }])->get();
        $supplierLabels = $supplierData->pluck('name')->toArray();
        $supplierSums = $supplierData->map(function($sup) {
            return $sup->purchases->sum('total_amount');
        })->toArray();

        // Типы (Товары/Услуги)
        $salesTotal = $salesQuery->with('items')->get()->flatMap->items->sum('total');
        $servicesTotal = $appointmentsQuery->sum('price');
        $typeLabels = ['Товары', 'Услуги'];
        $typeSums = [$salesTotal, $servicesTotal];

        // Динамика по дням (за последние 14 дней или по периоду)
        $period = [];
        if ($startDate && $endDate) {
            $start = new \Carbon\Carbon($startDate);
            $end = new \Carbon\Carbon($endDate);
        } else {
            $end = now();
            $start = (clone $end)->subDays(13);
        }
        for ($date = $start->copy(); $date <= $end; $date->addDay()) {
            $period[] = $date->format('Y-m-d');
        }
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
        $dynamicLabels = $period;
        $dynamicSales = array_map(fn($d) => $salesByDay[$d] ?? 0, $period);
        $dynamicPurchases = array_map(fn($d) => $purchasesByDay[$d] ?? 0, $period);

        $data = [
            'category' => [
                'labels' => $categoryLabels,
                'data' => $categoryCounts,
            ],
            'brand' => [
                'labels' => $brandLabels,
                'data' => $brandCounts,
            ],
            'supplier' => [
                'labels' => $supplierLabels,
                'data' => $supplierSums,
            ],
            'type' => [
                'labels' => $typeLabels,
                'data' => $typeSums,
            ],
            'dynamic' => [
                'labels' => $dynamicLabels,
                'sales' => $dynamicSales,
                'purchases' => $dynamicPurchases,
            ],
        ];
        return response()->json($data);
    }
} 