<?php

declare(strict_types=1);

namespace App\Models\Clients;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Admin\User;
use App\Models\Clients\Client;
use App\Models\Clients\Sale;

class Appointment extends Model
{
    use HasFactory;

    protected $fillable = [
        'client_id',
        'service_id',
        'date',
        'time',
        'price',
        'duration',
        'notes',
        'status',
        'project_id',
        'user_id',
        'parent_appointment_id'
    ];

    protected $casts = [
        'date' => 'date',
        'price' => 'decimal:2',
        // other casts...
    ];

    protected $appends = ['total_amount', 'childAppointments'];

    public function getChildAppointmentsAttribute()
    {
        return $this->childAppointments()->with('service')->get();
    }

    public function client()
    {
        return $this->belongsTo(Client::class);
    }

    public function service()
    {
        return $this->belongsTo(Service::class);
    }
    public function sales()
    {
        return $this->hasMany(Sale::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /**
     * Родительская запись (основная процедура)
     */
    public function parentAppointment()
    {
        return $this->belongsTo(Appointment::class, 'parent_appointment_id');
    }

    /**
     * Дочерние записи (дополнительные процедуры)
     */
    public function childAppointments()
    {
        return $this->hasMany(Appointment::class, 'parent_appointment_id');
    }

    /**
     * Проверяет, является ли запись основной (не дочерней)
     */
    public function isMainAppointment()
    {
        return is_null($this->parent_appointment_id);
    }

    /**
     * Получает основную запись (если текущая дочерняя)
     */
    public function getMainAppointment()
    {
        if ($this->isMainAppointment()) {
            return $this;
        }
        
        // Загружаем родительскую запись, если она не загружена
        if (!$this->relationLoaded('parentAppointment')) {
            $this->load('parentAppointment');
        }
        
        return $this->parentAppointment;
    }

    /**
     * Получает все связанные записи (основная + дочерние)
     */
    public function getRelatedAppointments()
    {
        if ($this->isMainAppointment()) {
            // Если это основная запись, возвращаем её + дочерние
            $children = $this->childAppointments;
            return $children->prepend($this);
        } else {
            // Если это дочерняя запись, возвращаем родительскую + все дочерние
            $parent = $this->parentAppointment;
            if ($parent) {
                $children = $parent->childAppointments;
                return $children->prepend($parent);
            }
            return collect([$this]);
        }
    }

    public function totalAmount()
    {
        $servicesSum = $this->price ?? 0;
        $productsSum = $this->sales->sum('total_amount') ?? 0;
        return $servicesSum + $productsSum;
    }

    public function getTotalAmountAttribute()
    {
        $total = $this->price ?? 0;
        
        // Добавляем стоимость товаров из продаж
        foreach ($this->sales as $sale) {
            $total += $sale->total_amount ?? 0;
        }
        
        // Добавляем стоимость дочерних записей (дополнительных услуг)
        foreach ($this->childAppointments as $child) {
            $total += $child->price ?? 0;
        }
        
        return $total;
    }
}
