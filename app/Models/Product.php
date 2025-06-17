<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class Product extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'category_id',
        'brand_id',
        'photo'
    ];

    protected $appends = ['photo_url'];

    public function getPhotoUrlAttribute()
    {
        return $this->photo ? Storage::url($this->photo) : null;
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

        static::deleting(function ($product) {
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
}
