<?php

declare(strict_types=1);

namespace App\Models\Clients;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SupportTicket extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'project_id',
        'subject',
        'message',
        'status',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(\App\Models\Admin\User::class, 'user_id');
    }
} 