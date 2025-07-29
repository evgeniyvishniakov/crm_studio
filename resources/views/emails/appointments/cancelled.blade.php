@component('mail::message')
# Запись отменена

Здравствуйте, {{ $client->name }}!

К сожалению, ваша запись в **{{ $project->project_name }}** была отменена.

## 📋 Детали отмененной записи:

**Услуга:** {{ $service->name }}  
**Мастер:** {{ $master->name ?? 'Не назначен' }}  
**Дата:** {{ \Carbon\Carbon::parse($appointment->date)->format('d.m.Y') }}  
**Время:** {{ $appointment->time }}

@if($reason)
**Причина отмены:** {{ $reason }}
@endif

---

## 📞 Свяжитесь с нами:

**Телефон:** {{ $project->phone ?? 'Не указан' }}  
**Адрес:** {{ $project->address ?? 'Не указан' }}

@if($project->working_hours)
**Время работы:** {{ $project->working_hours }}
@endif

---

**Приносим извинения за неудобства.**

Вы можете записаться на новое время, позвонив нам или воспользовавшись онлайн-записью на нашем сайте.

С уважением,<br>
Команда {{ $project->project_name }}

@component('mail::button', ['url' => config('app.url')])
Записаться снова
@endcomponent
@endcomponent