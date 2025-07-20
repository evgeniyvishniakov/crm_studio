<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SystemLog extends Model
{
    protected $fillable = [
        'level', 'module', 'user_email', 'user_id', 'ip', 'action', 'message', 'context'
    ];
} 