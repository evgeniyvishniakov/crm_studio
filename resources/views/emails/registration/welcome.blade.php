@component('mail::message')
# Добро пожаловать в CRM BeautyFlow!

Спасибо за регистрацию.

**Ваши данные:**
- Email: {{ $email }}
- @if($phone)Телефон: {{ $phone }}<br>@endif
- Название салона или Имя: {{ $salon }}

---

@isset($token)
@component('mail::button', ['url' => $resetUrl])
Создать пароль
@endcomponent
@endisset

В ближайшее время с вами свяжется наш менеджер или вы получите дальнейшие инструкции для активации аккаунта.

Если вы не регистрировались на нашем сайте — просто проигнорируйте это письмо.

@component('mail::button', ['url' => config('app.url')])
Перейти на сайт
@endcomponent

С уважением,<br>
Команда BeautyFlow
@endcomponent
