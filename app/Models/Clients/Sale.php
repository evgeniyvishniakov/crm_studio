<?php

declare(strict_types=1);

namespace App\Models\Clients;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Sale extends Model
{
    use HasFactory;

    protected $fillable = [
        'date',
        'client_id',
        'appointment_id',
        'total_amount',
        'notes',
        'project_id',
        'employee_id',
    ];

    public function client()
    {
        return $this->belongsTo(Client::class);
    }

    public function items()
    {
        return $this->hasMany(SaleItem::class);
    }
    public function appointment()
    {
        return $this->belongsTo(Appointment::class);
    }

    protected $casts = [
        'date' => 'date',
    ];
}
