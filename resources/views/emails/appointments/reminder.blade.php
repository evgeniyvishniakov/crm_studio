@component('mail::message')
# Напоминание о записи

Здравствуйте, {{ $client->name }}!

Напоминаем о вашей записи в **{{ $project->project_name }}** завтра.

## 📋 Детали записи:

**Услуга:** {{ $service->name }}  
**Мастер:** {{ $master->name ?? 'Не назначен' }}  
**Дата:** {{ \Carbon\Carbon::parse($appointment->date)->format('d.m.Y') }}  
**Время:** {{ $appointment->time }}  
**Стоимость:** {{ $appointment->price }} ₽

@if($appointment->notes)
**Примечания:** {{ $appointment->notes }}
@endif

---

## 📞 Контакты салона:

**Телефон:** {{ $project->phone ?? 'Не указан' }}  
**Адрес:** {{ $project->address ?? 'Не указан' }}

@if($project->working_hours)
**Время работы:** {{ $project->working_hours }}
@endif

---

**Важно:** 
- Пожалуйста, приходите за 10-15 минут до назначенного времени
- Если не можете прийти, позвоните нам заранее
- При себе иметь документы для оплаты

Ждем вас!

С уважением,<br>
Команда {{ $project->project_name }}

@component('mail::button', ['url' => config('app.url')])
Перейти на сайт
@endcomponent
@endcomponent