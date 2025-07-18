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

    public function project(): BelongsTo
    {
        return $this->belongsTo(\App\Models\Admin\Project::class, 'project_id');
    }

    public function messages()
    {
        return $this->hasMany(SupportTicketMessage::class, 'support_ticket_id');
    }
} 