<?php

declare(strict_types=1);

namespace App\Models\Clients;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Service extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'price',
        'duration',
        'project_id'
    ];

    // Связь с записями (appointments)
    public function appointments()
    {
        return $this->hasMany(\App\Models\Appointment::class, 'service_id');
    }
}
