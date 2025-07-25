<?php

declare(strict_types=1);

namespace App\Models\Clients;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Storage;
use App\Models\Clients\SaleItem;
use App\Models\Clients\PurchaseItem;

class Product extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'name',
        'category_id',
        'brand_id',
        'photo',
        'purchase_price',
        'retail_price',
        'project_id',
    ];

    protected $appends = ['photo_url'];

    public function getPhotoUrlAttribute()
    {
        if ($this->photo && Storage::disk('public')->exists($this->photo)) {
            return Storage::url($this->photo);
        }
        return null;
    }

    public function deletePhoto()
    {
        if ($this->photo) {
            Storage::disk('public')->delete($this->photo);
        }
    }

    protected static function boot()
    {
        parent::boot();

        // Удаляем фото только при принудительном удалении (forceDelete), а не при soft delete
        static::forceDeleting(function ($product) {
            $product->deletePhoto();
        });

        static::updating(function ($product) {
            if ($product->isDirty('photo') && $product->getOriginal('photo')) {
                Storage::disk('public')->delete($product->getOriginal('photo'));
            }
        });
    }

    // Добавляем связь со складом
    public function warehouse()
    {
        return $this->hasOne(Warehouse::class);
    }

    public function category()
    {
        return $this->belongsTo(ProductCategory::class);
    }

    public function brand()
    {
        return $this->belongsTo(ProductBrand::class);
    }

    public function saleItems()
    {
        return $this->hasMany(SaleItem::class, 'product_id');
    }

    public function inventoryItem()
    {
        return $this->hasOne(InventoryItem::class);
    }

    public function purchaseItems()
    {
        return $this->hasMany(PurchaseItem::class, 'product_id');
    }

    // Метод для восстановления товара
    public function restoreProduct()
    {
        return $this->restore();
    }

    // Метод для принудительного удаления (включая связанные данные)
    public function forceDeleteProduct()
    {
        // Удаляем связанные записи
        $this->warehouse()->delete();
        $this->saleItems()->delete();
        $this->purchaseItems()->delete();
        
        // Принудительно удаляем сам товар
        return parent::forceDelete();
    }
}
