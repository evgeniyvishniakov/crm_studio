<?php

declare(strict_types=1);

namespace App\Models\Clients;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Log;

class Warehouse extends Model
{
    protected $table = 'warehouse';
    protected $fillable = [
        'product_id',
        'purchase_price',
        'retail_price',
        'quantity',
        'project_id', // для мультипроктности
    ];

    public function product()
    {
        return $this->belongsTo(Product::class)->withTrashed();
    }

    // Метод для проверки доступного количества
    public static function checkAvailability($productId, $quantity, $projectId)
    {
        $item = self::where('product_id', $productId)
            ->where('project_id', $projectId)
            ->first();

        if (!$item) {
            throw new \Exception('Товар отсутствует на складе');
        }

        if ($item->quantity < $quantity) {
            throw new \Exception('Недостаточно товара на складе. Доступно: '.$item->quantity);
        }

        return true;
    }

    // Метод для уменьшения количества на складе
    public static function decreaseQuantity($productId, $quantity, $projectId)
    {
        if (!$projectId) {
            Log::error("Warehouse::decreaseQuantity called with null project_id for product {$productId}");
            return null; // или выбросить исключение
        }

        $item = self::where('product_id', $productId)
            ->where('project_id', $projectId)
            ->first();

        if (!$item) {
            // Просто ничего не делаем, если товара нет на складе
            return null;
        }

        $item->quantity -= $quantity;
        if ($item->quantity <= 0) {
            $item->delete();
            return null;
        } else {
            $item->save();
            return $item;
        }
    }

    // Метод для увеличения количества на складе
    public static function increaseQuantity($productId, $quantity, $projectId)
    {
        if (!$projectId) {
            Log::error("Warehouse::increaseQuantity called with null project_id for product {$productId}");
            return null; // или выбросить исключение
        }

        $item = self::where('product_id', $productId)
            ->where('project_id', $projectId)
            ->first();
            
        if (!$item) {
            // Если склада нет — получаем цены из Product
            $product = Product::find($productId);
            $purchasePrice = $product ? $product->purchase_price : 0;
            $retailPrice = $product ? $product->retail_price : 0;
            
            $item = self::create([
                'product_id' => $productId,
                'quantity' => $quantity,
                'purchase_price' => $purchasePrice,
                'retail_price' => $retailPrice,
                'project_id' => $projectId, 
            ]);
        } else {
            $item->quantity += $quantity;
            $item->save();
        }
        return $item;
    }
}
