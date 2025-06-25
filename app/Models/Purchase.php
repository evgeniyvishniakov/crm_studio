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

    protected $casts = [
        'date' => 'date',
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
        if (!$this->date) {
            return '';
        }
        return $this->date->format('d.m.Y');
    }
}
