@component('mail::message')
# Подтверждение записи

Здравствуйте, {{ $client->name }}!

Ваша запись в **{{ $project->project_name }}** успешно подтверждена.

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

**Важно:** Пожалуйста, приходите за 10-15 минут до назначенного времени.

Если у вас есть вопросы или нужно изменить запись, свяжитесь с нами по телефону.

С уважением,<br>
Команда {{ $project->project_name }}

@component('mail::button', ['url' => config('app.url')])
Перейти на сайт
@endcomponent
@endcomponent