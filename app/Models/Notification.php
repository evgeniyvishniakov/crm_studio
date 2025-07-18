<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Notification extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id', 'type', 'title', 'body', 'url', 'is_read'
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(\App\Models\Admin\User::class, 'user_id');
    }
} 