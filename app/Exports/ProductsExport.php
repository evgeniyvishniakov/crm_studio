<?php

namespace App\Exports;

use App\Models\Clients\Product;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class ProductsExport implements FromCollection, WithHeadings, WithMapping
{
    private $categoryId;
    private $brandId;
    private $photo;
    private $projectId;

    public function __construct($categoryId = null, $brandId = null, $photo = 'all', $projectId = null)
    {
        $this->categoryId = $categoryId;
        $this->brandId = $brandId;
        $this->photo = $photo;
        $this->projectId = $projectId;
    }

    /**
    * @return \Illuminate\Support\Collection
    */
    public function collection()
    {
        $query = Product::with(['category', 'brand']);
        if ($this->projectId) {
            $query->where('project_id', $this->projectId);
        }
        if ($this->categoryId) {
            $query->where('category_id', $this->categoryId);
        }
        if ($this->brandId) {
            $query->where('brand_id', $this->brandId);
        }
        if ($this->photo === 'with') {
            $query->whereNotNull('photo')->where('photo', '!=', '');
        } elseif ($this->photo === 'without') {
            $query->whereNull('photo')->orWhere('photo', '');
        }
        return $query->get();
    }

    /**
     * @return array
     */
    public function headings(): array
    {
        return [
            __('messages.name'),
            __('messages.category'),
            __('messages.brand'),
            __('messages.purchase_price'),
            __('messages.retail_price'),
            __('messages.photo')
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
