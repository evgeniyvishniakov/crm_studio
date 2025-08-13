@extends('admin.layouts.app')

@section('title', 'Редактирование тарифа')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Редактирование тарифа: {{ $plan->name }}</h3>
                    <div class="card-tools">
                        <a href="{{ route('admin.plans.index') }}" class="btn btn-secondary">
                            <i class="fas fa-arrow-left"></i> Назад к списку
                        </a>
                    </div>
                </div>
                <form action="{{ route('admin.plans.update', $plan) }}" method="POST">
                    @csrf
                    @method('PUT')
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="name">Название тарифа *</label>
                                    <input type="text" 
                                           class="form-control @error('name') is-invalid @enderror" 
                                           id="name" 
                                           name="name" 
                                           value="{{ old('name', $plan->name) }}" 
                                           placeholder="Например: До 2 сотрудников"
                                           required>
                                    @error('name')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="form-group">
                                    <label for="slug">Slug *</label>
                                    <input type="text" 
                                           class="form-control @error('slug') is-invalid @enderror" 
                                           id="slug" 
                                           name="slug" 
                                           value="{{ old('slug', $plan->slug) }}" 
                                           placeholder="Например: small"
                                           required>
                                    <small class="form-text text-muted">
                                        Уникальный идентификатор тарифа (только латинские буквы, цифры и дефисы)
                                    </small>
                                    @error('slug')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="form-group">
                                    <label for="max_employees">Максимальное количество сотрудников</label>
                                    <input type="number" 
                                           class="form-control @error('max_employees') is-invalid @enderror" 
                                           id="max_employees" 
                                           name="max_employees" 
                                           value="{{ old('max_employees', $plan->max_employees) }}" 
                                           min="1"
                                           placeholder="Оставьте пустым для безлимитного тарифа">
                                    <small class="form-text text-muted">
                                        Оставьте пустым для тарифа "Без лимита"
                                    </small>
                                    @error('max_employees')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="form-group">
                                    <label for="price_monthly">Цена за месяц (₴) *</label>
                                    <input type="number" 
                                           class="form-control @error('price_monthly') is-invalid @enderror" 
                                           id="price_monthly" 
                                           name="price_monthly" 
                                           value="{{ old('price_monthly', $plan->price_monthly) }}" 
                                           min="0" 
                                           step="0.01"
                                           placeholder="490.00"
                                           required>
                                    @error('price_monthly')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="description">Описание</label>
                                    <textarea class="form-control @error('description') is-invalid @enderror" 
                                              id="description" 
                                              name="description" 
                                              rows="3"
                                              placeholder="Краткое описание тарифа">{{ old('description', $plan->description) }}</textarea>
                                    @error('description')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="form-group">
                                    <label>Возможности тарифа</label>
                                    <div id="features-container">
                                        @if($plan->features && count($plan->features) > 0)
                                            @foreach($plan->features as $feature)
                                                <div class="input-group mb-2">
                                                    <input type="text" 
                                                           class="form-control" 
                                                           name="features[]" 
                                                           value="{{ $feature }}"
                                                           placeholder="Добавить возможность">
                                                    <div class="input-group-append">
                                                        <button type="button" class="btn btn-outline-danger remove-feature">
                                                            <i class="fas fa-minus"></i>
                                                        </button>
                                                    </div>
                                                </div>
                                            @endforeach
                                        @else
                                            <div class="input-group mb-2">
                                                <input type="text" 
                                                       class="form-control" 
                                                       name="features[]" 
                                                       placeholder="Добавить возможность">
                                                <div class="input-group-append">
                                                    <button type="button" class="btn btn-outline-secondary add-feature">
                                                        <i class="fas fa-plus"></i>
                                                    </button>
                                                </div>
                                            </div>
                                        @endif
                                    </div>
                                    <small class="form-text text-muted">
                                        Нажмите + чтобы добавить еще возможности
                                    </small>
                                </div>

                                <div class="form-group">
                                    <label for="sort_order">Порядок сортировки</label>
                                    <input type="number" 
                                           class="form-control @error('sort_order') is-invalid @enderror" 
                                           id="sort_order" 
                                           name="sort_order" 
                                           value="{{ old('sort_order', $plan->sort_order) }}" 
                                           min="0">
                                    @error('sort_order')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="form-group">
                                    <div class="custom-control custom-switch">
                                        <input type="checkbox" 
                                               class="custom-control-input" 
                                               id="is_active" 
                                               name="is_active" 
                                               value="1"
                                               {{ old('is_active', $plan->is_active) ? 'checked' : '' }}>
                                        <label class="custom-control-label" for="is_active">
                                            Тариф активен
                                        </label>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Предварительный просмотр цен -->
                        <div class="row mt-4">
                            <div class="col-12">
                                <div class="card bg-light">
                                    <div class="card-header">
                                        <h5 class="mb-0">Предварительный просмотр цен</h5>
                                    </div>
                                    <div class="card-body">
                                        <div class="row">
                                            <div class="col-md-3">
                                                <div class="text-center">
                                                    <h6>Месяц</h6>
                                                    <div class="h4 text-primary" id="price-monthly">{{ number_format($plan->price_monthly, 0, ',', ' ') }}₴</div>
                                                    <small class="text-muted">Скидка: 0%</small>
                                                </div>
                                            </div>
                                            <div class="col-md-3">
                                                <div class="text-center">
                                                    <h6>3 месяца</h6>
                                                    <div class="h4 text-success" id="price-quarterly">{{ number_format($plan->getPriceForPeriod('quarterly'), 0, ',', ' ') }}₴</div>
                                                    <small class="text-muted">Скидка: 10%</small>
                                                </div>
                                            </div>
                                            <div class="col-md-3">
                                                <div class="text-center">
                                                    <h6>6 месяцев</h6>
                                                    <div class="h4 text-info" id="price-semiannual">{{ number_format($plan->getPriceForPeriod('semiannual'), 0, ',', ' ') }}₴</div>
                                                    <small class="text-muted">Скидка: 15%</small>
                                                </div>
                                            </div>
                                            <div class="col-md-3">
                                                <div class="text-center">
                                                    <h6>Год</h6>
                                                    <div class="h4 text-warning" id="price-yearly">{{ number_format($plan->getPriceForPeriod('yearly'), 0, ',', ' ') }}₴</div>
                                                    <small class="text-muted">Скидка: 25%</small>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Информация о подписках -->
                        @if($plan->subscriptions && $plan->subscriptions->count() > 0)
                        <div class="row mt-4">
                            <div class="col-12">
                                <div class="card border-warning">
                                    <div class="card-header bg-warning text-dark">
                                        <h5 class="mb-0">
                                            <i class="fas fa-exclamation-triangle"></i> 
                                            Активные подписки на этот тариф
                                        </h5>
                                    </div>
                                    <div class="card-body">
                                        <p class="mb-2">
                                            <strong>Количество активных подписок:</strong> {{ $plan->subscriptions->count() }}
                                        </p>
                                        <small class="text-muted">
                                            Внимание! Изменение тарифа может повлиять на существующие подписки.
                                        </small>
                                    </div>
                                </div>
                            </div>
                        </div>
                        @endif
                    </div>

                    <div class="card-footer">
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save"></i> Сохранить изменения
                        </button>
                        <a href="{{ route('admin.plans.index') }}" class="btn btn-secondary">
                            Отмена
                        </a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
$(document).ready(function() {
    // Добавление новых полей для возможностей
    $('.add-feature').on('click', function() {
        const newFeature = `
            <div class="input-group mb-2">
                <input type="text" class="form-control" name="features[]" placeholder="Добавить возможность">
                <div class="input-group-append">
                    <button type="button" class="btn btn-outline-danger remove-feature">
                        <i class="fas fa-minus"></i>
                    </button>
                </div>
            </div>
        `;
        $('#features-container').append(newFeature);
    });

    // Удаление полей возможностей
    $(document).on('click', '.remove-feature', function() {
        $(this).closest('.input-group').remove();
    });

    // Обновление предварительного просмотра цен
    function updatePricePreview() {
        const price = parseFloat($('#price_monthly').val()) || 0;
        
        $('#price-monthly').text(price.toFixed(0) + '₴');
        $('#price-quarterly').text((price * 3 * 0.9).toFixed(0) + '₴');
        $('#price-semiannual').text((price * 6 * 0.85).toFixed(0) + '₴');
        $('#price-yearly').text((price * 12 * 0.75).toFixed(0) + '₴');
    }

    $('#price_monthly').on('input', updatePricePreview);
});
</script>
@endpush
