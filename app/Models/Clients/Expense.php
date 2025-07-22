<?php

declare(strict_types=1);

namespace App\Models\Clients;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Expense extends Model
{
    use HasFactory;

    protected $fillable = [
        'date',
        'comment',
        'amount',
        'category',
        'project_id', // для мультипроктности
    ];

    protected $casts = [
        'date' => 'date',
        'amount' => 'decimal:2'
    ];
} 