<?php

namespace App\Exports;

use App\Models\Product;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class ProductsExport implements FromCollection, WithHeadings, WithMapping
{
    /**
    * @return \Illuminate\Support\Collection
    */
    public function collection()
    {
        return Product::with(['category', 'brand'])->get();
    }

    /**
     * @return array
     */
    public function headings(): array
    {
        return [
            'Название',
            'Категория',
            'Бренд',
            'Оптовая цена',
            'Розничная цена',
            'Фото'
        ];
    }

    /**
     * @param Product $product
     * @return array
     */
    public function map($product): array
    {
        return [
            $product->name,
            $product->category ? $product->category->name : '',
            $product->brand ? $product->brand->name : '',
            $product->purchase_price,
            $product->retail_price,
            $product->photo ? url('/storage/' . $product->photo) : '',
        ];
    }
}
