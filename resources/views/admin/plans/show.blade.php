@extends('admin.layouts.app')

@section('title', 'Просмотр тарифа')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h3 class="card-title">
                        <i class="fas fa-tag"></i> {{ $plan->name }}
                    </h3>
                    <div class="card-tools">
                        <a href="{{ route('admin.plans.edit', $plan) }}" class="btn btn-warning">
                            <i class="fas fa-edit"></i> Редактировать
                        </a>
                        <a href="{{ route('admin.plans.index') }}" class="btn btn-secondary">
                            <i class="fas fa-arrow-left"></i> Назад к списку
                        </a>
                    </div>
                </div>
                <div class="card-body">
                    <div class="row">
                        <!-- Основная информация -->
                        <div class="col-md-6">
                            <div class="card">
                                <div class="card-header">
                                    <h5 class="mb-0">Основная информация</h5>
                                </div>
                                <div class="card-body">
                                    <table class="table table-borderless">
                                        <tr>
                                            <td><strong>ID:</strong></td>
                                            <td>{{ $plan->id }}</td>
                                        </tr>
                                        <tr>
                                            <td><strong>Slug:</strong></td>
                                            <td><code>{{ $plan->slug }}</code></td>
                                        </tr>
                                        <tr>
                                            <td><strong>Статус:</strong></td>
                                            <td>
                                                @if($plan->is_active)
                                                    <span class="badge badge-success">Активен</span>
                                                @else
                                                    <span class="badge badge-secondary">Неактивен</span>
                                                @endif
                                            </td>
                                        </tr>
                                        <tr>
                                            <td><strong>Порядок:</strong></td>
                                            <td>{{ $plan->sort_order }}</td>
                                        </tr>
                                        <tr>
                                            <td><strong>Создан:</strong></td>
                                            <td>{{ $plan->created_at->format('d.m.Y H:i') }}</td>
                                        </tr>
                                        <tr>
                                            <td><strong>Обновлен:</strong></td>
                                            <td>{{ $plan->updated_at->format('d.m.Y H:i') }}</td>
                                        </tr>
                                    </table>
                                </div>
                            </div>

                            <!-- Лимиты и цены -->
                            <div class="card mt-3">
                                <div class="card-header">
                                    <h5 class="mb-0">Лимиты и цены</h5>
                                </div>
                                <div class="card-body">
                                    <div class="row text-center">
                                        <div class="col-6">
                                            <h6>Макс. сотрудников</h6>
                                            @if($plan->max_employees)
                                                <div class="h4 text-info">{{ $plan->max_employees }}</div>
                                            @else
                                                <div class="h4 text-success">Без лимита</div>
                                            @endif
                                        </div>
                                        <div class="col-6">
                                            <h6>Цена за месяц</h6>
                                            <div class="h4 text-primary">{{ number_format($plan->price_monthly, 0, ',', ' ') }}₴</div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Описание и возможности -->
                        <div class="col-md-6">
                            <div class="card">
                                <div class="card-header">
                                    <h5 class="mb-0">Описание</h5>
                                </div>
                                <div class="card-body">
                                    @if($plan->description)
                                        <p>{{ $plan->description }}</p>
                                    @else
                                        <p class="text-muted">Описание не указано</p>
                                    @endif
                                </div>
                            </div>

                            <div class="card mt-3">
                                <div class="card-header">
                                    <h5 class="mb-0">Возможности тарифа</h5>
                                </div>
                                <div class="card-body">
                                    @if($plan->features && count($plan->features) > 0)
                                        <ul class="list-unstyled">
                                            @foreach($plan->features as $feature)
                                                <li class="mb-2">
                                                    <i class="fas fa-check text-success"></i> {{ $feature }}
                                                </li>
                                            @endforeach
                                        </ul>
                                    @else
                                        <p class="text-muted">Возможности не указаны</p>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Цены по периодам -->
                    <div class="row mt-4">
                        <div class="col-12">
                            <div class="card">
                                <div class="card-header">
                                    <h5 class="mb-0">Цены по периодам</h5>
                                </div>
                                <div class="card-body">
                                    <div class="row">
                                        <div class="col-md-3">
                                            <div class="text-center p-3 border rounded">
                                                <h6 class="text-primary">Месяц</h6>
                                                <div class="h3 text-primary">{{ number_format($plan->price_monthly, 0, ',', ' ') }}₴</div>
                                                <small class="text-muted">Базовая цена</small>
                                                <br>
                                                <span class="badge badge-light">Скидка: 0%</span>
                                            </div>
                                        </div>
                                        <div class="col-md-3">
                                            <div class="text-center p-3 border rounded">
                                                <h6 class="text-success">3 месяца</h6>
                                                <div class="h3 text-success">{{ number_format($plan->getPriceForPeriod('quarterly'), 0, ',', ' ') }}₴</div>
                                                <small class="text-muted">Экономия: {{ number_format($plan->price_monthly * 3 - $plan->getPriceForPeriod('quarterly'), 0, ',', ' ') }}₴</small>
                                                <br>
                                                <span class="badge badge-success">Скидка: 10%</span>
                                            </div>
                                        </div>
                                        <div class="col-md-3">
                                            <div class="text-center p-3 border rounded">
                                                <h6 class="text-info">6 месяцев</h6>
                                                <div class="h3 text-info">{{ number_format($plan->getPriceForPeriod('semiannual'), 0, ',', ' ') }}₴</div>
                                                <small class="text-muted">Экономия: {{ number_format($plan->price_monthly * 6 - $plan->getPriceForPeriod('semiannual'), 0, ',', ' ') }}₴</small>
                                                <br>
                                                <span class="badge badge-info">Скидка: 15%</span>
                                            </div>
                                        </div>
                                        <div class="col-md-3">
                                            <div class="text-center p-3 border rounded">
                                                <h6 class="text-warning">Год</h6>
                                                <div class="h3 text-warning">{{ number_format($plan->getPriceForPeriod('yearly'), 0, ',', ' ') }}₴</div>
                                                <small class="text-muted">Экономия: {{ number_format($plan->price_monthly * 12 - $plan->getPriceForPeriod('yearly'), 0, ',', ' ') }}₴</small>
                                                <br>
                                                <span class="badge badge-warning">Скидка: 25%</span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Статистика подписок -->
                    @if($plan->subscriptions && $plan->subscriptions->count() > 0)
                    <div class="row mt-4">
                        <div class="col-12">
                            <div class="card">
                                <div class="card-header">
                                    <h5 class="mb-0">
                                        <i class="fas fa-chart-bar"></i> Статистика подписок
                                    </h5>
                                </div>
                                <div class="card-body">
                                    <div class="row">
                                        <div class="col-md-3">
                                            <div class="text-center">
                                                <h6>Всего подписок</h6>
                                                <div class="h4 text-primary">{{ $plan->subscriptions->count() }}</div>
                                            </div>
                                        </div>
                                        <div class="col-md-3">
                                            <div class="text-center">
                                                <h6>Активные</h6>
                                                <div class="h4 text-success">{{ $plan->subscriptions->where('status', 'active')->count() }}</div>
                                            </div>
                                        </div>
                                        <div class="col-md-3">
                                            <div class="text-center">
                                                <h6>Пробные</h6>
                                                <div class="h4 text-info">{{ $plan->subscriptions->where('status', 'trial')->count() }}</div>
                                            </div>
                                        </div>
                                        <div class="col-md-3">
                                            <div class="text-center">
                                                <h6>Истекшие</h6>
                                                <div class="h4 text-danger">{{ $plan->subscriptions->where('status', 'expired')->count() }}</div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    @endif

                    <!-- Последние подписки -->
                    @if($plan->subscriptions && $plan->subscriptions->count() > 0)
                    <div class="row mt-4">
                        <div class="col-12">
                            <div class="card">
                                <div class="card-header">
                                    <h5 class="mb-0">
                                        <i class="fas fa-list"></i> Последние подписки на этот тариф
                                    </h5>
                                </div>
                                <div class="card-body">
                                    <div class="table-responsive">
                                        <table class="table table-sm">
                                            <thead>
                                                <tr>
                                                    <th>Проект</th>
                                                    <th>Статус</th>
                                                    <th>Дата начала</th>
                                                    <th>Дата окончания</th>
                                                    <th>Сумма</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @foreach($plan->subscriptions->take(5) as $subscription)
                                                <tr>
                                                    <td>
                                                        @if($subscription->project)
                                                            <a href="{{ route('admin.projects.show', $subscription->project) }}">
                                                                {{ $subscription->project->name }}
                                                            </a>
                                                        @else
                                                            <span class="text-muted">Проект удален</span>
                                                        @endif
                                                    </td>
                                                    <td>
                                                        @switch($subscription->status)
                                                            @case('active')
                                                                <span class="badge badge-success">Активна</span>
                                                                @break
                                                            @case('trial')
                                                                <span class="badge badge-info">Пробная</span>
                                                                @break
                                                            @case('expired')
                                                                <span class="badge badge-danger">Истекла</span>
                                                                @break
                                                            @case('cancelled')
                                                                <span class="badge badge-secondary">Отменена</span>
                                                                @break
                                                            @default
                                                                <span class="badge badge-light">{{ $subscription->status }}</span>
                                                        @endswitch
                                                    </td>
                                                    <td>{{ $subscription->starts_at ? $subscription->starts_at->format('d.m.Y') : '-' }}</td>
                                                    <td>{{ $subscription->expires_at ? $subscription->expires_at->format('d.m.Y') : '-' }}</td>
                                                    <td>{{ number_format($subscription->amount, 0, ',', ' ') }}₴</td>
                                                </tr>
                                                @endforeach
                                            </tbody>
                                        </table>
                                    </div>
                                    @if($plan->subscriptions->count() > 5)
                                        <div class="text-center mt-3">
                                            <a href="{{ route('admin.subscriptions.index') }}?plan={{ $plan->id }}" class="btn btn-outline-primary">
                                                Показать все подписки на этот тариф
                                            </a>
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
