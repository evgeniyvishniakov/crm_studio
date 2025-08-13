@extends('landing.layouts.app')

@section('title', 'Профиль')

@section('content')
<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <div>
                    <h1 class="h2 mb-1">Профиль проекта</h1>
                    <p class="text-muted mb-0">Управление информацией о вашем проекте</p>
                </div>
                <a href="{{ route('landing.account.dashboard') }}" class="btn btn-outline-secondary">
                    <i class="fas fa-arrow-left me-2"></i>Назад в кабинет
                </a>
            </div>

            @if(session('success'))
                <div class="alert alert-success">
                    {{ session('success') }}
                </div>
            @endif

            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">
                        <i class="fas fa-edit me-2"></i>Редактировать профиль
                    </h5>
                </div>
                <div class="card-body">
                    <form method="POST" action="{{ route('landing.account.profile.update') }}">
                        @csrf
                        
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="name" class="form-label">Название проекта <span class="text-danger">*</span></label>
                                <input type="text" class="form-control @error('name') is-invalid @enderror" 
                                       id="name" name="name" value="{{ old('name', $project->name) }}" required>
                                @error('name')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6 mb-3">
                                <label for="phone" class="form-label">Телефон</label>
                                <input type="text" class="form-control @error('phone') is-invalid @enderror" 
                                       id="phone" name="phone" value="{{ old('phone', $project->phone) }}">
                                @error('phone')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="email" class="form-label">Email <span class="text-muted">(неизменяем)</span></label>
                            <input type="email" class="form-control" id="email" value="{{ $project->email }}" disabled>
                            <small class="text-muted">Email не может быть изменен</small>
                        </div>

                        @if($project->website)
                        <div class="mb-3">
                            <label for="website" class="form-label">Сайт</label>
                            <input type="url" class="form-control" id="website" value="{{ $project->website }}" disabled>
                            <small class="text-muted">Сайт не может быть изменен</small>
                        </div>
                        @endif

                        <div class="mb-3">
                            <label for="address" class="form-label">Адрес</label>
                            <textarea class="form-control" id="address" rows="2" disabled>{{ $project->address ?? 'Не указан' }}</textarea>
                            <small class="text-muted">Адрес не может быть изменен</small>
                        </div>

                        <div class="d-flex gap-2">
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save me-2"></i>Сохранить изменения
                            </button>
                            <a href="{{ route('landing.account.dashboard') }}" class="btn btn-outline-secondary">
                                Отмена
                            </a>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Информация о подписке -->
            @if($project->subscriptions->count() > 0)
            <div class="card mt-4">
                <div class="card-header">
                    <h5 class="card-title mb-0">
                        <i class="fas fa-credit-card me-2"></i>История подписок
                    </h5>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>Период</th>
                                    <th>Статус</th>
                                    <th>Дата начала</th>
                                    <th>Дата окончания</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($project->subscriptions->sortByDesc('created_at') as $subscription)
                                <tr>
                                    <td>
                                        @if($subscription->status === 'trial')
                                            <span class="badge bg-warning">Пробный</span>
                                        @elseif($subscription->status === 'active')
                                            <span class="badge bg-success">Активная</span>
                                        @elseif($subscription->status === 'expired')
                                            <span class="badge bg-danger">Истекла</span>
                                        @else
                                            <span class="badge bg-secondary">{{ $subscription->status }}</span>
                                        @endif
                                    </td>
                                    <td>{{ $subscription->plan_type ?? 'Не указан' }}</td>
                                    <td>{{ $subscription->starts_at ? $subscription->starts_at->format('d.m.Y') : 'Не указано' }}</td>
                                    <td>
                                        @if($subscription->status === 'trial' && $subscription->trial_ends_at)
                                            {{ $subscription->trial_ends_at->format('d.m.Y') }}
                                        @elseif($subscription->expires_at)
                                            {{ $subscription->expires_at->format('d.m.Y') }}
                                        @else
                                            Не указано
                                        @endif
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            @endif
        </div>
    </div>
</div>
@endsection
