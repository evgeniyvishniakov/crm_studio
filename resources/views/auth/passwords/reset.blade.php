@extends('client.layouts.auth')

@section('title', __('emails.password_reset.title'))

@section('content')
<div class="container py-5 d-flex justify-content-center align-items-center" style="min-height: 60vh;">
    <div class="card shadow-sm p-4" style="max-width: 400px; width: 100%;">
        <div class="text-center mb-4">
            <div class="text-muted mb-2">{{ __('emails.password_reset.title') }}</div>
        </div>
        <form method="POST" action="{{ route('password.update') }}">
            @csrf
            <input type="hidden" name="token" value="{{ $token }}">
            <input type="hidden" name="email" value="{{ request('email') }}">
            <div class="mb-3">
                <label for="password" class="form-label">{{ __('emails.password_reset.new_password') }}</label>
                <input id="password" type="password" class="form-control @error('password') is-invalid @enderror" name="password" required autocomplete="new-password" autofocus>
                @error('password')
                    <span class="invalid-feedback" role="alert"><strong>{{ $message }}</strong></span>
                @enderror
            </div>
            <div class="mb-3">
                <label for="password-confirm" class="form-label">{{ __('emails.password_reset.confirm_password') }}</label>
                <input id="password-confirm" type="password" class="form-control" name="password_confirmation" required autocomplete="new-password">
            </div>
            <div class="d-flex justify-content-center">
                <button type="submit" class="btn btn-primary">{{ __('emails.password_reset.create_button') }}</button>
            </div>
        </form>
    </div>
</div>
@endsection
