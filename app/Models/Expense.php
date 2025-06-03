<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Expense extends Model
{
    use HasFactory;

    protected $fillable = [
        'date',
        'comment',
        'amount'
    ];

    protected $casts = [
        'date' => 'date',
        'amount' => 'decimal:2'
    ];
} 