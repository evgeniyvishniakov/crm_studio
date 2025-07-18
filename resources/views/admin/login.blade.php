@extends('admin.layouts.app')

@section('title', 'Вход в админ-панель')

@section('content')
<div class="d-flex justify-content-center align-items-center" style="min-height: 100vh; margin-left: 0;">
    <div class="card shadow-sm p-4" style="max-width: 400px; width: 100%;">
        <h4 class="mb-4 text-center"><i class="fas fa-user-shield me-2"></i>Вход в админ-панель</h4>
        @if($errors->any())
            <div class="alert alert-danger">
                @foreach($errors->all() as $error)
                    <div>{{ $error }}</div>
                @endforeach
            </div>
        @endif
        <form method="POST" action="{{ route('admin.login') }}">
            @csrf
            <div class="mb-3">
                <label for="email" class="form-label">Email</label>
                <input type="email" class="form-control" id="email" name="email" required autofocus>
            </div>
            <div class="mb-3">
                <label for="password" class="form-label">Пароль</label>
                <input type="password" class="form-control" id="password" name="password" required>
            </div>
            <button type="submit" class="btn btn-primary w-100">Войти</button>
        </form>
    </div>
</div>
@endsection 