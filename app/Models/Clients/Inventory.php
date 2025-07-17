<?php

declare(strict_types=1);

namespace App\Models\Clients;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;
use App\Models\Admin\User;

class Inventory extends Model
{
    protected $fillable = ['date', 'user_id', 'notes', 'project_id'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function items()
    {
        return $this->hasMany(InventoryItem::class);
    }

    public function getDiscrepanciesCountAttribute()
    {
        return $this->items->where('difference', '!=', 0)->count();
    }

    public function getShortagesCountAttribute()
    {
        return $this->items->where('difference', '<', 0)->count();
    }

    public function getOveragesCountAttribute()
    {
        return $this->items->where('difference', '>', 0)->count();
    }

    public function getFormattedDateAttribute()
    {
        return $this->date ? Carbon::parse($this->date)->format('d.m.Y') : '';
    }
}
