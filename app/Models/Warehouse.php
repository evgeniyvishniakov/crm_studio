<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Warehouse extends Model
{
    protected $table = 'warehouse';
    protected $fillable = [
        'product_id',
        'purchase_price',
        'retail_price',
        'quantity'
    ];

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    // Метод для проверки доступного количества
    public static function checkAvailability($productId, $quantity)
    {
        $item = self::where('product_id', $productId)->first();

        if (!$item) {
            throw new \Exception('Товар отсутствует на складе');
        }

        if ($item->quantity < $quantity) {
            throw new \Exception('Недостаточно товара на складе. Доступно: '.$item->quantity);
        }

        return true;
    }

    // Метод для уменьшения количества на складе
    public static function decreaseQuantity($productId, $quantity)
    {
        $item = self::where('product_id', $productId)->firstOrFail();
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
    public static function increaseQuantity($productId, $quantity)
    {
        $item = self::where('product_id', $productId)->first();
        if (!$item) {
            // Если склада нет — получаем цены из Product
            $product = \App\Models\Product::find($productId);
            $purchasePrice = $product ? $product->purchase_price : 0;
            $retailPrice = $product ? $product->retail_price : 0;
            $item = self::create([
                'product_id' => $productId,
                'quantity' => $quantity,
                'purchase_price' => $purchasePrice,
                'retail_price' => $retailPrice,
            ]);
        } else {
            $item->quantity += $quantity;
            $item->save();
        }
        return $item;
    }

}
