<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Exports\ProductsExport;
use App\Imports\ProductsImport;
use Maatwebsite\Excel\Facades\Excel;
use App\Models\Product;
use App\Models\ProductCategory;
use App\Models\ProductBrand;
use PhpOffice\PhpSpreadsheet\IOFactory;

class ProductImportExportController extends Controller
{
    /**
     * Экспорт товаров в Excel
     */
    public function export()
    {
        return Excel::download(new ProductsExport, 'products_' . date('Y-m-d_H-i-s') . '.xlsx');
    }

    /**
     * Предварительный анализ файла импорта
     */
    public function previewImport(Request $request)
    {
        $request->validate([
            'file' => 'required|file|mimes:xlsx,xls,csv|max:51200' // 50MB в килобайтах
        ]);

        try {
            $file = $request->file('file');
            $import = new ProductsImport();
            
            // Читаем первые 10 строк для предварительного просмотра
            $previewData = Excel::toArray($import, $file)[0];
            
            // Ограничиваем количество строк для предварительного просмотра
            $previewData = array_slice($previewData, 0, 10);
            
            $categories = ProductCategory::all();
            $brands = ProductBrand::all();
            
            return response()->json([
                'success' => true,
                'data' => $previewData,
                'categories' => $categories,
                'brands' => $brands,
                'totalRows' => count(Excel::toArray($import, $file)[0])
            ]);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Ошибка при чтении файла: ' . $e->getMessage()
            ], 400);
        }
    }

    /**
     * Импорт товаров из файла
     */
    public function import(Request $request)
    {
        $request->validate([
            'file' => 'required|file|mimes:xlsx,xls,csv|max:51200' // 50MB в килобайтах
        ]);

        try {
            $file = $request->file('file');
            $extension = strtolower($file->getClientOriginalExtension());
            $importFilePath = $file->getRealPath();

            // Если это Excel-файл, обрабатываем гиперссылки
            if (in_array($extension, ['xlsx', 'xls'])) {
                $spreadsheet = IOFactory::load($importFilePath);
                $sheet = $spreadsheet->getActiveSheet();
                foreach ($sheet->getRowIterator() as $row) {
                    foreach ($row->getCellIterator() as $cell) {
                        if ($cell->hasHyperlink() && $cell->getHyperlink()->getUrl()) {
                            $cell->setValue($cell->getHyperlink()->getUrl());
                        }
                    }
                }
                // Сохраняем временный файл для импорта
                $tempPath = storage_path('app/temp_import_' . time() . '.xlsx');
                $writer = IOFactory::createWriter($spreadsheet, 'Xlsx');
                $writer->save($tempPath);
                $importFilePath = $tempPath;
            }

            $import = new ProductsImport();
            Excel::import($import, $importFilePath);

            // Формируем сообщение на основе счетчиков
            $message = '';
            if ($import->createdCount > 0 && $import->updatedCount > 0) {
                $message = "Создано {$import->createdCount} новых товаров, обновлено {$import->updatedCount} существующих товаров";
            } elseif ($import->createdCount > 0) {
                $message = "Создано {$import->createdCount} новых товаров";
            } elseif ($import->updatedCount > 0) {
                $message = "Обновлено {$import->updatedCount} существующих товаров";
            } else {
                $message = "Импорт завершен, новых товаров не добавлено";
            }

            return response()->json([
                'success' => true,
                'message' => $message,
                'createdCount' => $import->createdCount,
                'updatedCount' => $import->updatedCount,
                'totalProcessed' => $import->createdCount + $import->updatedCount
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Ошибка при импорте: ' . $e->getMessage()
            ], 400);
        }
    }

    /**
     * Анализ названия товара для определения категории и бренда
     */
    public function analyzeProductName(Request $request)
    {
        $productName = $request->input('name', '');
        
        if (empty($productName)) {
            return response()->json([
                'success' => false,
                'message' => 'Название товара не может быть пустым'
            ]);
        }

        $productNameLower = mb_strtolower($productName);

        // Ищем категорию
        $suggestedCategory = null;
        $categories = ProductCategory::all();
        foreach ($categories as $category) {
            $categoryNameLower = mb_strtolower($category->name);
            if (strpos($productNameLower, $categoryNameLower) !== false) {
                $suggestedCategory = $category;
                break;
            }
        }

        // Ищем бренд
        $suggestedBrand = null;
        $brands = ProductBrand::all();
        foreach ($brands as $brand) {
            $brandNameLower = mb_strtolower($brand->name);
            if (strpos($productNameLower, $brandNameLower) !== false) {
                $suggestedBrand = $brand;
                break;
            }
        }

        return response()->json([
            'success' => true,
            'suggestedCategory' => $suggestedCategory,
            'suggestedBrand' => $suggestedBrand,
            'confidence' => ($suggestedCategory ? 50 : 0) + ($suggestedBrand ? 50 : 0)
        ]);
    }
}
