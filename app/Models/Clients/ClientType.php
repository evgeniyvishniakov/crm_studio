<?php

declare(strict_types=1);

namespace App\Models\Clients;

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
        'discount' => 'decimal:2',
        'status' => 'boolean'
    ];

    public const FIXED_TYPES = [
        'Постоянный клиент',
        'Новый клиент',
    ];

    public function clients()
    {
        return $this->hasMany(Client::class);
    }
}
