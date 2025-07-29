@component('mail::message')
# Запись перенесена

Здравствуйте, {{ $client->name }}!

Ваша запись в **{{ $project->project_name }}** была перенесена.

## 📋 Детали записи:

**Услуга:** {{ $service->name }}  
**Мастер:** {{ $master->name ?? 'Не назначен' }}

@if($oldDate && $oldTime)
**Было:** {{ \Carbon\Carbon::parse($oldDate)->format('d.m.Y') }} в {{ $oldTime }}  
**Стало:** {{ \Carbon\Carbon::parse($appointment->date)->format('d.m.Y') }} в {{ $appointment->time }}
@else
**Новая дата:** {{ \Carbon\Carbon::parse($appointment->date)->format('d.m.Y') }}  
**Новое время:** {{ $appointment->time }}
@endif

**Стоимость:** {{ $appointment->price }} ₽

@if($reason)
**Причина переноса:** {{ $reason }}
@endif

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

**Важно:** Пожалуйста, приходите за 10-15 минут до назначенного времени.

Если новое время вам не подходит, свяжитесь с нами для изменения записи.

С уважением,<br>
Команда {{ $project->project_name }}

@component('mail::button', ['url' => config('app.url')])
Перейти на сайт
@endcomponent
@endcomponent