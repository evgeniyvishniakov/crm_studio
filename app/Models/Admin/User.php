<?php

declare(strict_types=1);

namespace App\Models\Admin;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable implements \Illuminate\Contracts\Auth\MustVerifyEmail
{
    use HasApiTokens, HasFactory, Notifiable;

    protected $table = 'admin_users';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'username',
        'email',
        'password',
        'project_id',
        'role',
        'status',
        'registered_at',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
    ];

    // Системные роли, которые нельзя редактировать или удалять
    public const FIXED_ROLES = ['admin'];

    public function project()
    {
        return $this->belongsTo(Project::class);
    }

    public function roleModel()
    {
        return $this->hasOne(Role::class, 'name', 'role');
    }

    public function permissions()
    {
        $role = $this->roleModel()->first();
        return $role ? $role->permissions() : collect();
    }
}
