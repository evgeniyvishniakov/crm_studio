<?php

declare(strict_types=1);

namespace App\Models\Clients;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Admin\User;
use App\Models\Clients\Client;
use App\Models\Clients\Sale;

class Appointment extends Model
{
    use HasFactory;

    protected $fillable = [
        'client_id',
        'service_id',
        'date',
        'time',
        'price',
        'duration',
        'notes',
        'status',
        'project_id',
        'user_id'
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

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
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
