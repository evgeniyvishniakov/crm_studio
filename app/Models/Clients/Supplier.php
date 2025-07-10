<?php

declare(strict_types=1);

namespace App\Models\Clients;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Supplier extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'contact_person',
        'phone',
        'email',
        'address',
        'instagram',
        'inn',
        'note',
        'status'
    ];

    protected $casts = [
        'status' => 'boolean'
    ];

    public function purchases()
    {
        return $this->hasMany(Purchase::class);
    }
}
