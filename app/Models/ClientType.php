<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ClientType extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'description',
        'discount',
        'status'
    ];

    protected $casts = [
        'status' => 'boolean',
        'discount' => 'decimal:2'
    ];
}
