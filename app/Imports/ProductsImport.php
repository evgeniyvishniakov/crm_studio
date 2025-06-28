<?php

namespace App\Imports;

use App\Models\Product;
use App\Models\ProductCategory;
use App\Models\ProductBrand;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;
use Maatwebsite\Excel\Concerns\SkipsOnError;
use Maatwebsite\Excel\Concerns\SkipsErrors;
use Maatwebsite\Excel\Concerns\WithCustomCsvSettings;
use Maatwebsite\Excel\Concerns\WithCalculatedFormulas;
use Maatwebsite\Excel\Concerns\WithCustomValueBinder;
use PhpOffice\PhpSpreadsheet\Cell\Cell;
use PhpOffice\PhpSpreadsheet\Cell\DataType;
use Illuminate\Support\Facades\Storage;
use PhpOffice\PhpSpreadsheet\Cell\DefaultValueBinder;

class ProductsImport extends DefaultValueBinder implements ToModel, WithHeadingRow, WithValidation, SkipsOnError, WithCalculatedFormulas, WithCustomCsvSettings, WithCustomValueBinder
{
    use SkipsErrors;

    public $currentRow = 0;
    public $updatedCount = 0;
    public $createdCount = 0;
    public $headerMap = null;
    public $headerFound = false;

    /**
     * Вытаскиваем URL из гиперссылки Excel
     */
    public function bindValue(Cell $cell, $value)
    {
        if ($cell->hasHyperlink() && $cell->getHyperlink()->getUrl()) {
            $value = $cell->getHyperlink()->getUrl();
        }
        $cell->setValueExplicit($value, DataType::TYPE_STRING);
        return true;
    }

    /**
     * @param array $row
     *
     * @return \Illuminate\Database\Eloquent\Model|null
     */
    public function model(array $row)
    {
        $this->currentRow++;
        
        \Log::info('Обрабатываем строку ' . $this->currentRow . ': ' . json_encode($row, JSON_UNESCAPED_UNICODE));
        
        // Если заголовки ещё не найдены, ищем строку с "Название"
        if (!$this->headerFound) {
            // Проверяем среди ключей
            foreach ($row as $key => $value) {
                if (mb_strtolower(trim($key)) === 'название' || mb_strtolower(trim($value)) === 'название') {
                    $this->headerMap = $row;
                    $this->headerFound = true;
                    \Log::info('Найдена строка с заголовками: ' . json_encode($row, JSON_UNESCAPED_UNICODE));
                    return null;
                }
            }
            // Если не нашли — пропускаем строку
            \Log::info('Пропущена строка без заголовков: ' . json_encode($row, JSON_UNESCAPED_UNICODE));
            return null;
        }

        // Если headerMap заполнен, переопределяем ключи
        if ($this->headerMap) {
            $row = array_combine(array_values($this->headerMap), array_values($row));
        }

        // Обрабатываем гиперссылки и формулы в значениях
        $row = $this->processRowValues($row);

        // Универсальный поиск ключа по подстроке
        $findKeyBySubstring = function($row, $needle) {
            foreach ($row as $key => $value) {
                $normalized = mb_strtolower(preg_replace('/\s+/', '', $key));
                if (mb_strpos($normalized, $needle) !== false) {
                    return $key;
                }
            }
            return null;
        };

        // Поиск ключей для разных полей
        $nameKey = $findKeyBySubstring($row, 'название') ?? $findKeyBySubstring($row, 'name');
        $purchasePriceKey = $findKeyBySubstring($row, 'опт') ?? $findKeyBySubstring($row, 'purchase');
        $retailPriceKey = $findKeyBySubstring($row, 'розн') ?? $findKeyBySubstring($row, 'retail');
        $categoryKey = $findKeyBySubstring($row, 'катег') ?? $findKeyBySubstring($row, 'category');
        $brandKey = $findKeyBySubstring($row, 'бренд') ?? $findKeyBySubstring($row, 'brand');
        $articleKey = $findKeyBySubstring($row, 'артикул') ?? $findKeyBySubstring($row, 'sku') ?? $findKeyBySubstring($row, 'код');

        // Получаем значения
        $productName = $nameKey ? trim($row[$nameKey]) : '';
        $purchasePrice = $purchasePriceKey ? $this->parsePrice($row[$purchasePriceKey]) : null;
        $retailPrice = $retailPriceKey ? $this->parsePrice($row[$retailPriceKey]) : null;
        $categoryName = $categoryKey ? trim($row[$categoryKey]) : '';
        $brandName = $brandKey ? trim($row[$brandKey]) : '';
        $article = $articleKey ? trim($row[$articleKey]) : '';

        // Если название пустое или это число (например, результат формулы), пропускаем строку
        if (empty($productName) || is_numeric(str_replace(',', '.', $productName))) {
            \Log::info('Пропущена строка с некорректным названием: ' . json_encode($row, JSON_UNESCAPED_UNICODE));
            return null;
        }

        \Log::info("Обработка товара: {$productName}, опт: {$purchasePrice}, розница: {$retailPrice}");

        // Если розничная цена равна 0, а оптовая больше 0, рассчитываем розничную
        if ($retailPrice == 0 && $purchasePrice > 0) {
            $retailPrice = round($purchasePrice * 1.6, 2);
            \Log::info("Рассчитана розничная цена: {$retailPrice}");
        }

        // Определяем категорию
        $categoryId = $this->determineCategory($categoryName, $productName);

        // Определяем бренд
        $brandId = $this->determineBrand($brandName, $productName);

        // Проверяем, существует ли товар с таким названием
        $existingProduct = Product::where('name', $productName)->first();
        
        if ($existingProduct) {
            \Log::info('Обновляем существующий товар: ' . $productName);
            $this->updatedCount++;
            
            $updateData = [];
            if ($categoryId !== null) $updateData['category_id'] = $categoryId;
            if ($brandId !== null) $updateData['brand_id'] = $brandId;
            if ($purchasePrice !== null) $updateData['purchase_price'] = $purchasePrice;
            if ($retailPrice !== null) $updateData['retail_price'] = $retailPrice;
            if (!empty($article)) $updateData['article'] = $article;
            
            if (!empty($updateData)) {
                $existingProduct->update($updateData);
            }
            return null;
        } else {
            \Log::info('Создаем новый товар: ' . $productName);
            $this->createdCount++;
            // Импорт фото по URL (универсально для разных языков)
            $photoPath = $this->processImageUrl($row);
            return new Product([
                'name' => $productName,
                'category_id' => $categoryId,
                'brand_id' => $brandId,
                'purchase_price' => $purchasePrice ?? 0,
                'retail_price' => $retailPrice ?? 0,
                'photo' => $photoPath,
                'article' => $article,
            ]);
        }
    }

    /**
     * Обрабатываем значения в строке (гиперссылки, формулы)
     */
    private function processRowValues($row)
    {
        foreach ($row as $key => $value) {
            if (is_string($value)) {
                // Обрабатываем формулы Excel
                if (strpos($value, '=') === 0) {
                    // Убираем формулу и оставляем только результат
                    $row[$key] = preg_replace('/^=.*/', '', $value);
                    \Log::info("Обработана формула Excel для ключа '{$key}': {$value} -> {$row[$key]}");
                }
                
                // Очищаем URL от лишних символов
                $row[$key] = $this->cleanUrl($row[$key]);
            }
        }
        return $row;
    }

    /**
     * Парсим цену, обрабатывая формулы Excel
     */
    private function parsePrice($price)
    {
        if (empty($price)) {
            return 0;
        }

        // Если это строка с формулой Excel, пытаемся извлечь число
        if (is_string($price) && (strpos($price, '=') === 0 || strpos($price, '*') !== false || strpos($price, '+') !== false)) {
            // Убираем все символы кроме цифр, точки и запятой
            $price = preg_replace('/[^0-9.,]/', '', $price);
        }

        // Заменяем запятую на точку
        $price = str_replace(',', '.', $price);
        
        // Убираем все символы кроме цифр и точки
        $price = preg_replace('/[^0-9.]/', '', $price);
        
        return (float) $price;
    }

    /**
     * Определяем категорию
     */
    private function determineCategory($categoryName, $productName)
    {
        // Если категория указана в файле
        if (!empty($categoryName) && $categoryName !== '-' && $categoryName !== 'прочерк') {
            $category = ProductCategory::where('name', $categoryName)->first();
            if ($category) {
                return $category->id;
            }
        }

        // Ищем категорию в названии товара
        $productNameLower = mb_strtolower($productName);
        $categories = ProductCategory::all();
        
        foreach ($categories as $category) {
            $categoryNameLower = mb_strtolower($category->name);
            if (strpos($productNameLower, $categoryNameLower) !== false) {
                return $category->id;
            }
        }

        return null;
    }

    /**
     * Определяем бренд
     */
    private function determineBrand($brandName, $productName)
    {
        // Если бренд указан в файле
        if (!empty($brandName) && $brandName !== '-' && $brandName !== 'прочерк') {
            $brand = ProductBrand::where('name', $brandName)->first();
            if ($brand) {
                return $brand->id;
            }
        }

        // Ищем бренд в названии товара
        $productNameLower = mb_strtolower($productName);
        $brands = ProductBrand::all();
        
        foreach ($brands as $brand) {
            $brandNameLower = mb_strtolower($brand->name);
            if (strpos($productNameLower, $brandNameLower) !== false) {
                return $brand->id;
            }
        }

        return null;
    }

    /**
     * Обрабатываем URL изображения из колонки
     */
    private function processImageUrl($row)
    {
        \Log::info('Ключи строки для фото: ' . json_encode(array_keys($row), JSON_UNESCAPED_UNICODE));
        $photoKeys = ['фото', 'photo', 'изображение', 'image', 'url', 'ссылка', 'link'];
        $imageUrl = null;
        $foundKey = null;
        foreach ($row as $key => $value) {
            $normalized = mb_strtolower(trim($key));
            if (in_array($normalized, $photoKeys) && !empty($value)) {
                $imageUrl = trim($value);
                $foundKey = $key;
                \Log::info("Найден URL в колонке '{$foundKey}': {$imageUrl}");
                break;
            }
        }
        if (empty($imageUrl)) {
            \Log::info('URL изображения не найден в строке: ' . json_encode($row, JSON_UNESCAPED_UNICODE));
            return null;
        }
        if (!filter_var($imageUrl, FILTER_VALIDATE_URL)) {
            \Log::warning('Некорректный URL изображения: ' . $imageUrl);
            return null;
        }
        // Проверяем расширение
        $extension = $this->getExtensionFromUrl($imageUrl);
        if (!in_array($extension, ['jpg', 'jpeg', 'png'])) {
            \Log::warning('Попытка загрузить неподдерживаемый формат изображения: ' . $extension . ' (' . $imageUrl . ')');
            return null;
        }
        try {
            \Log::info('Начинаем загрузку изображения: ' . $imageUrl);
            $imageContents = file_get_contents($imageUrl);
            if ($imageContents === false) {
                \Log::error('Не удалось загрузить изображение по URL: ' . $imageUrl);
                return null;
            }
            $fileName = 'products/import_url_' . time() . '_' . $this->currentRow . '.' . $extension;
            Storage::disk('public')->put($fileName, $imageContents);
            \Log::info('Изображение успешно сохранено: ' . $fileName);
            return $fileName;
        } catch (\Exception $e) {
            \Log::error('Ошибка загрузки изображения по URL для строки ' . $this->currentRow . ': ' . $e->getMessage());
            return null;
        }
    }

    /**
     * Очищаем URL от лишних символов
     */
    private function cleanUrl($url)
    {
        // Убираем лишние пробелы
        $url = trim($url);
        
        // Убираем кавычки
        $url = trim($url, '"\'');
        
        // Если URL начинается с =HYPERLINK, извлекаем URL из формулы
        if (preg_match('/=HYPERLINK\("([^"]+)"/', $url, $matches)) {
            $url = $matches[1];
        }
        
        // Если URL содержит пробелы в начале или конце, убираем их
        $url = trim($url);
        
        return $url;
    }

    /**
     * Определяем расширение файла по URL и MIME-типу
     */
    private function getExtensionFromUrl($url)
    {
        $path = parse_url($url, PHP_URL_PATH);
        $extension = strtolower(pathinfo($path, PATHINFO_EXTENSION));
        $validExtensions = ['jpg', 'jpeg', 'png'];
        if (in_array($extension, $validExtensions)) {
            return $extension;
        }
        // Если расширение не определено, возвращаем jpg по умолчанию
        return 'jpg';
    }

    /**
     * Правила валидации
     */
    public function rules(): array
    {
        return [
            'название' => 'nullable|string|max:255',
            'name' => 'nullable|string|max:255',
            'оптовая_цена' => 'nullable|numeric|min:0',
            'purchase_price' => 'nullable|numeric|min:0',
            'розничная_цена' => 'nullable|numeric|min:0',
            'retail_price' => 'nullable|numeric|min:0',
        ];
    }

    /**
     * Кастомные сообщения об ошибках
     */
    public function customValidationMessages()
    {
        return [
            'название.string' => 'Название товара должно быть текстом',
            'name.string' => 'Название товара должно быть текстом',
            'оптовая_цена.numeric' => 'Оптовая цена должна быть числом',
            'purchase_price.numeric' => 'Оптовая цена должна быть числом',
            'розничная_цена.numeric' => 'Розничная цена должна быть числом',
            'retail_price.numeric' => 'Розничная цена должна быть числом',
        ];
    }

    public function getCsvSettings(): array
    {
        return [
            'delimiter' => ';', // теперь точка с запятой
            'input_encoding' => 'UTF-8',
        ];
    }
}
