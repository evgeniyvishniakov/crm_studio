@extends('landing.layouts.app')

@section('title', 'Вход в личный кабинет')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-6 col-lg-5">
            <div class="card shadow-lg border-0 rounded-3 mt-5">
                <div class="card-body p-5">
                    <div class="text-center mb-4">
                        <h2 class="fw-bold text-dark mb-2">Вход в личный кабинет</h2>
                        <p class="text-muted">Вход только для руководителей проектов</p>
                    </div>

                    @if($errors->any())
                        <div class="alert alert-danger">
                            <ul class="mb-0">
                                @foreach($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <form method="POST" action="{{ route('landing.account.login.post') }}">
                        @csrf
                        
                        <div class="mb-3">
                            <label for="email" class="form-label">Email руководителя</label>
                            <input type="email" class="form-control @error('email') is-invalid @enderror" 
                                   id="email" name="email" value="{{ old('email') }}" required>
                            @error('email')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="password" class="form-label">Пароль руководителя</label>
                            <input type="password" class="form-control @error('password') is-invalid @enderror" 
                                   id="password" name="password" required>
                            @error('password')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="d-grid">
                            <button type="submit" class="btn btn-primary btn-lg">
                                <i class="fas fa-sign-in-alt me-2"></i>Войти
                            </button>
                        </div>
                    </form>

                    <hr class="my-4">

                    <div class="text-center">
                        <p class="text-muted mb-0">Нет проекта?</p>
                        <a href="{{ route('beautyflow.index') }}#register" class="btn btn-outline-primary btn-sm">
                            Зарегистрировать проект
                        </a>
                    </div>

                    <div class="text-center mt-3">
                        <a href="{{ route('beautyflow.index') }}" class="text-muted text-decoration-none">
                            <i class="fas fa-arrow-left me-1"></i>Вернуться на главную
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
