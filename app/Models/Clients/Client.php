<?php

declare(strict_types=1);

namespace App\Models\Clients;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Client extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'phone',
        'email',
        'instagram',
        'telegram',
        'client_type_id',
        'notes',
        'birth_date',
        'is_active'
    ];

    protected $casts = [
        'birth_date' => 'date',
        'is_active' => 'boolean'
    ];

    public function clientType()
    {
        return $this->belongsTo(ClientType::class);
    }

    public function sales()
    {
        return $this->hasMany(Sale::class);
    }

    public function appointments()
    {
        return $this->hasMany(Appointment::class);
    }

    public function getDiscountAttribute()
    {
        return $this->clientType ? $this->clientType->discount : 0;
    }
}
