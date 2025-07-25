<?php

namespace App\Imports;

use App\Models\Clients\Product;
use App\Models\Clients\ProductCategory;
use App\Models\Clients\ProductBrand;
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

    private string $delimiter = ';'; // по умолчанию
    private $projectId;

    public function __construct($delimiter = ';', $projectId = null)
    {
        $this->delimiter = $delimiter;
        $this->projectId = $projectId;
    }

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
        
        // Обрабатываем гиперссылки и формулы в значениях
        $row = $this->processRowValues($row);

        // Универсальный поиск ключа по алиасам (поддержка русского, английского, украинского и транслита)
        $findKeyByAlias = function($row, $aliases) {
            foreach ($aliases as $alias) {
                foreach ($row as $key => $value) {
                    if (mb_strtolower(trim($key)) === mb_strtolower(trim($alias))) {
                        return $key;
                    }
                }
            }
            return null;
        };

        // Расширенная поддержка мультиязычности
        $nameKey = $findKeyByAlias($row, [
            // Русский
            'название', 'названіе', 'названиє',
            // Английский
            'name', 'title', 'product_name',
            // Украинский
            'назва', 'назва товару', 'назва продукту',
            // Транслит
            'nazvanie', 'nazva', 'nazvaniye'
        ]);

        $categoryKey = $findKeyByAlias($row, [
            // Русский
            'категория', 'категорія', 'категорија',
            // Английский
            'category', 'product_category', 'cat',
            // Украинский
            'категорія', 'категорія товару',
            // Транслит
            'kategoriia', 'kategoria', 'kategoriya'
        ]);

        $brandKey = $findKeyByAlias($row, [
            // Русский
            'бренд', 'брэнд', 'марка',
            // Английский
            'brand', 'manufacturer', 'make',
            // Украинский
            'бренд', 'марка', 'виробник',
            // Транслит
            'brend', 'marka', 'brand'
        ]);

        $purchaseKey = $findKeyByAlias($row, [
            // Русский
            'оптовая цена', 'оптовая стоимость', 'опт', 'оптовая',
            // Английский
            'purchase_price', 'wholesale_price', 'cost_price', 'wholesale',
            // Украинский
            'оптова ціна', 'оптова вартість', 'опт',
            // Транслит
            'optovaia_cena', 'optovaya_cena', 'optova_tsina'
        ]);

        $retailKey = $findKeyByAlias($row, [
            // Русский
            'розничная цена', 'розничная стоимость', 'розница', 'розничная',
            // Английский
            'retail_price', 'selling_price', 'price', 'retail',
            // Украинский
            'рознична ціна', 'рознична вартість', 'розница',
            // Транслит
            'roznicnaia_cena', 'roznichnaya_cena', 'roznichna_tsina'
        ]);

        $photoKey = $findKeyByAlias($row, [
            // Русский
            'фото', 'изображение', 'картинка', 'фотография',
            // Английский
            'photo', 'image', 'picture', 'photo_url', 'image_url',
            // Украинский
            'фото', 'зображення', 'картинка',
            // Транслит
            'foto', 'izobrazhenie', 'kartinka'
        ]);

        $productName = $nameKey ? $row[$nameKey] : null;
        $categoryName = $categoryKey ? $row[$categoryKey] : null;
        $brandName = $brandKey ? $row[$brandKey] : null;
        $purchasePrice = $purchaseKey ? $row[$purchaseKey] : null;
        $retailPrice = $retailKey ? $row[$retailKey] : null;
        $photo = $photoKey ? $row[$photoKey] : null;

        // Проверяем, что у нас есть хотя бы название товара
        if (empty($productName)) {
            \Log::warning('Пропущена строка без названия товара: ' . json_encode($row, JSON_UNESCAPED_UNICODE));
            return null;
        }

        // Определяем категорию и бренд
        $categoryId = $this->determineCategory($categoryName, $productName);
        $brandId = $this->determineBrand($brandName, $productName);

        // Проверяем, существует ли уже товар с таким названием в этом проекте
        $existingProduct = Product::where('name', $productName)
            ->where('project_id', $this->projectId)
            ->first();

        if ($existingProduct) {
            $updateData = [
                'category_id' => $categoryId,
                'brand_id' => $brandId,
                'purchase_price' => $purchasePrice ?? $existingProduct->purchase_price,
                'retail_price' => $retailPrice ?? $existingProduct->retail_price,
            ];

            // Если есть фото — скачиваем и обновляем
            if ($photo) {
                $photoPath = $this->processImageUrl($row);
                if ($photoPath) {
                    $updateData['photo'] = $photoPath;
                }
            }

            $existingProduct->update($updateData);

            $this->updatedCount++;
            \Log::info("Обновлен товар: {$productName}");
        } else {
            // Создаем новый товар
            $productData = [
                'name' => $productName,
                'category_id' => $categoryId,
                'brand_id' => $brandId,
                'purchase_price' => $purchasePrice ?? 0,
                'retail_price' => $retailPrice ?? 0,
                'project_id' => $this->projectId,
            ];

            // Обрабатываем фото, если есть
            if ($photo) {
                $photoPath = $this->processImageUrl($row);
                if ($photoPath) {
                    $productData['photo'] = $photoPath;
                }
            }

            Product::create($productData);
            $this->createdCount++;
            \Log::info("Создан новый товар: {$productName}");
        }

        return null;
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
        $photoKeys = [
            // Русский
            'фото', 'изображение', 'картинка', 'фотография', 'ссылка', 'url',
            // Английский
            'photo', 'image', 'picture', 'photo_url', 'image_url', 'url', 'link',
            // Украинский
            'фото', 'зображення', 'картинка', 'посилання',
            // Транслит
            'foto', 'izobrazhenie', 'kartinka', 'ssylka'
        ];
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
            // Название товара (мультиязычность)
            'название' => 'nullable|string|max:255',
            'name' => 'nullable|string|max:255',
            'title' => 'nullable|string|max:255',
            'product_name' => 'nullable|string|max:255',
            'назва' => 'nullable|string|max:255',
            'nazvanie' => 'nullable|string|max:255',
            'nazva' => 'nullable|string|max:255',
            
            // Оптовая цена (мультиязычность)
            'оптовая_цена' => 'nullable|numeric|min:0',
            'purchase_price' => 'nullable|numeric|min:0',
            'wholesale_price' => 'nullable|numeric|min:0',
            'cost_price' => 'nullable|numeric|min:0',
            'оптова_ціна' => 'nullable|numeric|min:0',
            'optovaia_cena' => 'nullable|numeric|min:0',
            'optovaya_cena' => 'nullable|numeric|min:0',
            
            // Розничная цена (мультиязычность)
            'розничная_цена' => 'nullable|numeric|min:0',
            'retail_price' => 'nullable|numeric|min:0',
            'selling_price' => 'nullable|numeric|min:0',
            'price' => 'nullable|numeric|min:0',
            'рознична_ціна' => 'nullable|numeric|min:0',
            'roznicnaia_cena' => 'nullable|numeric|min:0',
            'roznichnaya_cena' => 'nullable|numeric|min:0',
        ];
    }

    /**
     * Кастомные сообщения об ошибках
     */
    public function customValidationMessages()
    {
        return [
            // Название товара
            'название.string' => 'Название товара должно быть текстом',
            'name.string' => 'Название товара должно быть текстом',
            'title.string' => 'Название товара должно быть текстом',
            'product_name.string' => 'Название товара должно быть текстом',
            'назва.string' => 'Название товара должно быть текстом',
            'nazvanie.string' => 'Название товара должно быть текстом',
            'nazva.string' => 'Название товара должно быть текстом',
            
            // Оптовая цена
            'оптовая_цена.numeric' => 'Оптовая цена должна быть числом',
            'purchase_price.numeric' => 'Оптовая цена должна быть числом',
            'wholesale_price.numeric' => 'Оптовая цена должна быть числом',
            'cost_price.numeric' => 'Оптовая цена должна быть числом',
            'оптова_ціна.numeric' => 'Оптовая цена должна быть числом',
            'optovaia_cena.numeric' => 'Оптовая цена должна быть числом',
            'optovaya_cena.numeric' => 'Оптовая цена должна быть числом',
            
            // Розничная цена
            'розничная_цена.numeric' => 'Розничная цена должна быть числом',
            'retail_price.numeric' => 'Розничная цена должна быть числом',
            'selling_price.numeric' => 'Розничная цена должна быть числом',
            'price.numeric' => 'Розничная цена должна быть числом',
            'рознична_ціна.numeric' => 'Розничная цена должна быть числом',
            'roznicnaia_cena.numeric' => 'Розничная цена должна быть числом',
            'roznichnaya_cena.numeric' => 'Розничная цена должна быть числом',
        ];
    }

    public function getCsvSettings(): array
    {
        return [
            'delimiter' => $this->delimiter,
            'input_encoding' => 'UTF-8',
        ];
    }
}
