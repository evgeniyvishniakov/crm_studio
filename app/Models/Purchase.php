<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Purchase extends Model
{
    protected $fillable = [
        'date',
        'supplier_id',
        'notes',
        'total_amount'
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

    public function supplier()
    {
        return $this->belongsTo(Supplier::class);
    }

    public function getFormattedDateAttribute()
    {
        return $this->date->format('d.m.Y');
    }
}
