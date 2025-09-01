<?php

declare(strict_types=1);

namespace App\Models\Admin;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Auth\Passwords\CanResetPassword;

class User extends Authenticatable implements \Illuminate\Contracts\Auth\MustVerifyEmail
{
    use HasApiTokens, HasFactory, Notifiable, CanResetPassword;

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
        'avatar',
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

    /**
     * Связь с услугами мастера
     */
    public function userServices()
    {
        return $this->hasMany(\App\Models\Clients\UserService::class);
    }

    /**
     * Получить активные услуги мастера для веб-записи
     */
    public function activeBookingServices()
    {
        return $this->userServices()->where('is_active_for_booking', true)->with('service');
    }

    /**
     * Получить все услуги мастера
     */
    public function allServices()
    {
        return $this->userServices()->with('service');
    }

    /**
     * Связь с настройками зарплаты
     */
    public function salarySettings()
    {
        return $this->hasMany(\App\Models\Clients\SalarySetting::class);
    }

    /**
     * Связь с расчетами зарплаты
     */
    public function salaryCalculations()
    {
        return $this->hasMany(\App\Models\Clients\SalaryCalculation::class);
    }

    /**
     * Связь с выплатами зарплаты
     */
    public function salaryPayments()
    {
        return $this->hasMany(\App\Models\Clients\SalaryPayment::class);
    }

    /**
     * Связь с продажами как сотрудник
     */
    public function sales()
    {
        return $this->hasMany(\App\Models\Clients\Sale::class, 'employee_id');
    }

    /**
     * Связь с записями как мастер
     */
    public function appointments()
    {
        return $this->hasMany(\App\Models\Clients\Appointment::class, 'user_id');
    }

    /**
     * Получить email для сброса пароля
     */
    public function getEmailForPasswordReset()
    {
        return $this->email;
    }
}
