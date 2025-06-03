<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Purchase extends Model
{
    protected $fillable = [
        'date',
        'supplier',
        'total_amount',
        'notes'
    ];

    // Option 1: Using $dates (for Laravel < 7)
    protected $dates = ['date'];

    // Option 2: Using $casts (preferred for Laravel 7+)
    protected $casts = [
        'date' => 'date'
    ];

    public function items()
    {
        return $this->hasMany(PurchaseItem::class);
    }

    public function getFormattedDateAttribute()
    {
        return $this->date ? $this->date->format('d.m.Y') : null;
    }
}
