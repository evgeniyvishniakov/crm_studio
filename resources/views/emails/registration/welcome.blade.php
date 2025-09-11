@component('mail::message')
# {{ __('emails.registration.welcome.title') }}

{{ __('emails.registration.welcome.thanks') }}

**{{ __('emails.registration.welcome.your_data') }}**
- {{ __('emails.registration.welcome.email') }} {{ $email }}
- @if($phone){{ __('emails.registration.welcome.phone') }} {{ $phone }}<br>@endif
- {{ __('emails.registration.welcome.salon_name') }} {{ $salon }}

---

@isset($token)
@component('mail::button', ['url' => $resetUrl])
{{ __('emails.registration.welcome.create_password') }}
@endcomponent
@endisset

{{ __('emails.registration.welcome.manager_contact') }}

{{ __('emails.registration.welcome.ignore_if_not_registered') }}

@component('mail::button', ['url' => config('app.url')])
{{ __('emails.registration.welcome.go_to_site') }}
@endcomponent

{{ __('emails.registration.welcome.regards') }}<br>
{{ __('emails.registration.welcome.team') }}
@endcomponent
