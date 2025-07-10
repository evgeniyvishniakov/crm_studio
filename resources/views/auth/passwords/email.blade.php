@extends('client.layouts.app')

@section('title', 'Восстановление пароля')

@section('content')
<div class="container py-5 d-flex justify-content-center align-items-center" style="min-height: 80vh;">
    <div class="card shadow-sm p-4" style="max-width: 400px; width: 100%;">
        <div class="text-center mb-4">
            <img src="{{ asset('favicon.ico') }}" alt="CRM Studio" style="width:48px;">
            <h2 class="mt-2 mb-0" style="font-weight:700;">CRM Studio</h2>
            <div class="text-muted mb-2">Восстановление доступа</div>
        </div>
        @if (session('status'))
            <div class="alert alert-success">{{ session('status') }}</div>
        @endif
        <form method="POST" action="{{ route('password.email') }}">
            @csrf
            <div class="mb-3">
                <label for="email" class="form-label">Email</label>
                <input id="email" type="email" class="form-control @error('email') is-invalid @enderror" name="email" value="{{ old('email') }}" required autocomplete="email" autofocus>
                @error('email')
                    <span class="invalid-feedback" role="alert"><strong>{{ $message }}</strong></span>
                @enderror
            </div>
            <div class="d-flex justify-content-center">
                <button type="submit" class="btn btn-primary">Отправить ссылку для сброса</button>
            </div>
        </form>
    </div>
</div>
@endsection
