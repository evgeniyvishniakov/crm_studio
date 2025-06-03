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
        $item->save();
        return $item;
    }

    // Метод для увеличения количества на складе
    public static function increaseQuantity($productId, $quantity)
    {
        $item = self::where('product_id', $productId)->firstOrFail();
        $item->quantity += $quantity;
        $item->save();
        return $item;
    }

}
