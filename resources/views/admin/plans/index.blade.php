@extends('admin.layouts.app')

@section('title', 'Управление тарифами')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h3 class="card-title">Тарифы</h3>
                    <a href="{{ route('admin.plans.create') }}" class="btn btn-primary">
                        <i class="fas fa-plus"></i> Создать тариф
                    </a>
                </div>
                <div class="card-body">
                    @if(session('success'))
                        <div class="alert alert-success alert-dismissible">
                            <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
                            {{ session('success') }}
                        </div>
                    @endif

                    @if(session('error'))
                        <div class="alert alert-danger alert-dismissible">
                            <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
                            {{ session('error') }}
                        </div>
                    @endif

                    <div class="table-responsive">
                        <table class="table table-bordered table-striped">
                            <thead>
                                <tr>
                                    <th style="width: 50px;">#</th>
                                    <th>Название</th>
                                    <th>Slug</th>
                                    <th>Макс. сотрудников</th>
                                    <th>Цена/месяц</th>
                                    <th>Статус</th>
                                    <th>Порядок</th>
                                    <th style="width: 200px;">Действия</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($plans as $plan)
                                    <tr>
                                        <td>{{ $plan->id }}</td>
                                        <td>
                                            <strong>{{ $plan->name }}</strong>
                                            @if($plan->description)
                                                <br><small class="text-muted">{{ Str::limit($plan->description, 50) }}</small>
                                            @endif
                                        </td>
                                        <td><code>{{ $plan->slug }}</code></td>
                                        <td>
                                            @if($plan->max_employees)
                                                <span class="badge badge-info">{{ $plan->max_employees }}</span>
                                            @else
                                                <span class="badge badge-success">Без лимита</span>
                                            @endif
                                        </td>
                                        <td>
                                            <strong>{{ number_format($plan->price_monthly, 0, ',', ' ') }}₴</strong>
                                            <br>
                                            <small class="text-muted">
                                                3 мес: {{ number_format($plan->getPriceForPeriod('quarterly'), 0, ',', ' ') }}₴
                                                <br>
                                                6 мес: {{ number_format($plan->getPriceForPeriod('semiannual'), 0, ',', ' ') }}₴
                                                <br>
                                                Год: {{ number_format($plan->getPriceForPeriod('yearly'), 0, ',', ' ') }}₴
                                            </small>
                                        </td>
                                        <td>
                                            @if($plan->is_active)
                                                <span class="badge badge-success">Активен</span>
                                            @else
                                                <span class="badge badge-secondary">Неактивен</span>
                                            @endif
                                        </td>
                                        <td>{{ $plan->sort_order }}</td>
                                        <td>
                                            <div class="btn-group" role="group">
                                                <a href="{{ route('admin.plans.show', $plan) }}" 
                                                   class="btn btn-sm btn-info" 
                                                   title="Просмотр">
                                                    <i class="fas fa-eye"></i>
                                                </a>
                                                <a href="{{ route('admin.plans.edit', $plan) }}" 
                                                   class="btn btn-sm btn-warning" 
                                                   title="Редактировать">
                                                    <i class="fas fa-edit"></i>
                                                </a>
                                                <form action="{{ route('admin.plans.destroy', $plan) }}" 
                                                      method="POST" 
                                                      style="display: inline;"
                                                      onsubmit="return confirm('Вы уверены, что хотите удалить этот тариф?')">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" 
                                                            class="btn btn-sm btn-danger" 
                                                            title="Удалить">
                                                        <i class="fas fa-trash"></i>
                                                    </button>
                                                </form>
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="8" class="text-center text-muted">
                                            <i class="fas fa-info-circle"></i> Тарифы не найдены
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
$(document).ready(function() {
    // Автоматическое скрытие уведомлений через 5 секунд
    setTimeout(function() {
        $('.alert').fadeOut('slow');
    }, 5000);
});
</script>
@endpush
