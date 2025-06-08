<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Appointment extends Model
{
    use HasFactory;

    protected $fillable = [
        'service_id',
        'client_id',
        'date',
        'time',
        'price',
        'notes',
        'status'
    ];

    protected $casts = [
        'date' => 'date',
        'price' => 'decimal:2',
        // other casts...
    ];

    public function client()
    {
        return $this->belongsTo(Client::class);
    }

    public function service()
    {
        return $this->belongsTo(Service::class);
    }
    public function sales()
    {
        return $this->hasMany(Sale::class);
    }

    public function totalAmount()
    {
        $servicesSum = $this->price ?? 0;
        $productsSum = $this->sales->sum('total_amount') ?? 0;
        return $servicesSum + $productsSum;
    }
    protected $appends = ['total_amount'];

    public function getTotalAmountAttribute()
    {
        $total = $this->price;
        foreach ($this->sales as $sale) {
            $total += $sale->quantity * $sale->retail_price;
        }
        return $total;
    }
}
