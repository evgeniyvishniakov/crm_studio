@extends('admin.layouts.app')

@section('title', 'Настройки системы - Админ')
@section('page-title', 'Настройки системы')

@section('content')
@if(session('success'))
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        {{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
@endif

@if($errors->any())
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
        <h6 class="alert-heading">Ошибки валидации:</h6>
        <ul class="mb-0">
            @foreach($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
@endif

@if($errors->has('general'))
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
        <strong>Ошибка:</strong> {{ $errors->first('general') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
@endif

<div class="row g-4">
    <div class="col-lg-8">
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-transparent">
                <h5 class="mb-0">Системные настройки</h5>
            </div>
            <div class="card-body">
                <form action="{{ route('admin.settings.update') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    
                    <!-- Основные настройки -->
                    <div class="mb-4">
                        <h6 class="text-primary mb-3">
                            <i class="fas fa-cog me-2"></i>Основные параметры
                        </h6>
                        
                        <div class="mb-3">
                            <label for="site_name" class="form-label">Название сайта</label>
                            <input type="text" class="form-control @error('site_name') is-invalid @enderror" 
                                   id="site_name" name="site_name" value="{{ old('site_name', $settings->site_name) }}">
                            @error('site_name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <div class="mb-3">
                            <label for="site_description" class="form-label">Описание сайта</label>
                            <textarea class="form-control @error('site_description') is-invalid @enderror" 
                                      id="site_description" name="site_description" rows="3">{{ old('site_description', $settings->site_description) }}</textarea>
                            @error('site_description')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <div class="mb-3">
                            <label for="admin_email" class="form-label">Email администратора</label>
                            <input type="email" class="form-control @error('admin_email') is-invalid @enderror" 
                                   id="admin_email" name="admin_email" value="{{ old('admin_email', $settings->admin_email) }}">
                            @error('admin_email')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <div class="mb-3">
                            <label for="timezone" class="form-label">Часовой пояс</label>
                            <select class="form-select @error('timezone') is-invalid @enderror" id="timezone" name="timezone">
                                <option value="Europe/Moscow" {{ old('timezone', $settings->timezone) == 'Europe/Moscow' ? 'selected' : '' }}>Москва (UTC+3)</option>
                                <option value="Europe/London" {{ old('timezone', $settings->timezone) == 'Europe/London' ? 'selected' : '' }}>Лондон (UTC+0)</option>
                                <option value="America/New_York" {{ old('timezone', $settings->timezone) == 'America/New_York' ? 'selected' : '' }}>Нью-Йорк (UTC-5)</option>
                            </select>
                            @error('timezone')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    
                    <!-- Настройки изображений -->
                    <div class="mb-4">
                        <h6 class="text-primary mb-3">
                            <i class="fas fa-images me-2"></i>Изображения сайта
                        </h6>
                        
                        <!-- Логотип лендинга -->
                        <div class="mb-4">
                            <label for="landing_logo" class="form-label">Логотип лендинга</label>
                            <div class="row align-items-center">
                                <div class="col-md-6">
                                    <input type="file" class="form-control @error('landing_logo') is-invalid @enderror" 
                                           id="landing_logo" name="landing_logo" accept="image/*">
                                    <div class="form-text">Рекомендуемый размер: 200x60px. Поддерживаемые форматы: JPEG, PNG, GIF, SVG. Максимум: 2MB</div>
                                    @error('landing_logo')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="col-md-6">
                                                                    @if($settings->landing_logo)
                                    <div class="text-center">
                                        <img src="{{ $settings->landing_logo }}" alt="Текущий логотип" 
                                             class="img-fluid border rounded" style="max-height: 60px;">
                                        <div class="mt-2">
                                            <small class="text-muted">Текущий логотип</small>
                                        </div>
                                        <div class="mt-2">
                                            <form action="{{ route('admin.settings.remove-image', 'logo') }}" method="POST" class="d-inline">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-sm btn-outline-danger" 
                                                        onclick="return confirm('Вы уверены, что хотите удалить логотип?')">
                                                    <i class="fas fa-trash me-1"></i>Удалить
                                                </button>
                                            </form>
                                        </div>
                                    </div>
                                @else
                                        <div class="text-center text-muted">
                                            <i class="fas fa-image fa-2x mb-2"></i>
                                            <div>Логотип не загружен</div>
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </div>
                        
                        <!-- Фавикон -->
                        <div class="mb-4">
                            <label for="favicon" class="form-label">Фавикон</label>
                            <div class="row align-items-center">
                                <div class="col-md-6">
                                    <input type="file" class="form-control @error('favicon') is-invalid @enderror" 
                                           id="favicon" name="favicon" accept="image/*">
                                    <div class="form-text">Рекомендуемый размер: 32x32px. Поддерживаемые форматы: ICO, PNG, JPG. Максимум: 1MB</div>
                                    @error('favicon')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="col-md-6">
                                                                    @if($settings->favicon)
                                    <div class="text-center">
                                        <img src="{{ $settings->favicon }}" alt="Текущий фавикон" 
                                             class="img-fluid border rounded" style="max-height: 32px;">
                                        <div class="mt-2">
                                            <small class="text-muted">Текущий фавикон</small>
                                        </div>
                                        <div class="mt-2">
                                            <form action="{{ route('admin.settings.remove-image', 'favicon') }}" method="POST" class="d-inline">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-sm btn-outline-danger" 
                                                        onclick="return confirm('Вы уверены, что хотите удалить фавикон?')">
                                                    <i class="fas fa-trash me-1"></i>Удалить
                                                </button>
                                            </form>
                                        </div>
                                    </div>
                                @else
                                        <div class="text-center text-muted">
                                            <i class="fas fa-star fa-2x mb-2"></i>
                                            <div>Фавикон не загружен</div>
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Кнопки действий -->
                    <div class="d-flex gap-2">
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save me-2"></i>Сохранить все настройки
                        </button>
                        
                        <a href="{{ route('admin.settings.test') }}" class="btn btn-outline-info">
                            <i class="fas fa-vial me-2"></i>Тест настроек
                        </a>
                    </div>
                </form>
            </div>
        </div>
    </div>
    
    <!-- JavaScript для предварительного просмотра изображений -->
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Предварительный просмотр логотипа
            const logoInput = document.getElementById('landing_logo');
            if (logoInput) {
                logoInput.addEventListener('change', function(e) {
                    const file = e.target.files[0];
                    if (file) {
                        const reader = new FileReader();
                        reader.onload = function(e) {
                            const previewContainer = logoInput.closest('.mb-4').querySelector('.col-md-6:last-child');
                            previewContainer.innerHTML = `
                                <div class="text-center">
                                    <img src="${e.target.result}" alt="Предварительный просмотр логотипа" 
                                         class="img-fluid border rounded" style="max-height: 60px;">
                                    <div class="mt-2">
                                        <small class="text-muted">Предварительный просмотр</small>
                                    </div>
                                </div>
                            `;
                        };
                        reader.readAsDataURL(file);
                    }
                });
            }
            
            // Предварительный просмотр фавикона
            const faviconInput = document.getElementById('favicon');
            if (faviconInput) {
                faviconInput.addEventListener('change', function(e) {
                    const file = e.target.files[0];
                    if (file) {
                        const reader = new FileReader();
                        reader.onload = function(e) {
                            const previewContainer = faviconInput.closest('.mb-4').querySelector('.col-md-6:last-child');
                            previewContainer.innerHTML = `
                                <div class="text-center">
                                    <img src="${e.target.result}" alt="Предварительный просмотр фавикона" 
                                         class="img-fluid border rounded" style="max-height: 32px;">
                                    <div class="mt-2">
                                        <small class="text-muted">Предварительный просмотр</small>
                                    </div>
                                </div>
                            `;
                        };
                        reader.readAsDataURL(file);
                    }
                });
            }
        });
    </script>
    
    <div class="col-lg-4">
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-transparent">
                <h5 class="mb-0">Системная информация</h5>
            </div>
            <div class="card-body">
                <div class="mb-3">
                    <strong>Версия PHP:</strong> {{ phpversion() }}
                </div>
                <div class="mb-3">
                    <strong>Версия Laravel:</strong> {{ app()->version() }}
                </div>
                <div class="mb-3">
                    <strong>База данных:</strong> {{ config('database.default') }}
                </div>
                <div class="mb-3">
                    <strong>Режим:</strong> 
                    <span class="badge bg-{{ app()->environment() === 'production' ? 'success' : 'warning' }}">
                        {{ app()->environment() }}
                    </span>
                </div>
            </div>
        </div>
        
        <!-- Инструкции по загрузке -->
        <div class="card border-0 shadow-sm mt-4">
            <div class="card-header bg-transparent">
                <h5 class="mb-0">Рекомендации</h5>
            </div>
            <div class="card-body">
                <div class="mb-3">
                    <h6><i class="fas fa-info-circle text-info me-2"></i>Логотип</h6>
                    <ul class="small text-muted mb-0">
                        <li>Используйте прозрачный фон (PNG/SVG)</li>
                        <li>Оптимальная ширина: 200-300px</li>
                        <li>Высота: 40-80px</li>
                    </ul>
                </div>
                <div class="mb-3">
                    <h6><i class="fas fa-info-circle text-info me-2"></i>Фавикон</h6>
                    <ul class="small text-muted mb-0">
                        <li>Квадратное изображение</li>
                        <li>Размер: 32x32px или 16x16px</li>
                        <li>Формат ICO предпочтителен</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row g-4 mt-2">
    <!-- Резервное копирование -->
    <div class="col-12">
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-transparent d-flex justify-content-between align-items-center">
                <h5 class="mb-0">Резервное копирование</h5>
                <button class="btn btn-success">
                    <i class="fas fa-download me-2"></i>Создать резервную копию
                </button>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>Файл</th>
                                <th>Размер</th>
                                <th>Дата создания</th>
                                <th>Действия</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td>backup_2024_01_15.sql</td>
                                <td>2.5 MB</td>
                                <td>15.01.2024 14:30</td>
                                <td>
                                    <div class="btn-group" role="group">
                                        <button class="btn btn-sm btn-outline-primary">
                                            <i class="fas fa-download"></i>
                                        </button>
                                        <button class="btn btn-sm btn-outline-danger">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                            <tr>
                                <td>backup_2024_01_14.sql</td>
                                <td>2.3 MB</td>
                                <td>14.01.2024 14:30</td>
                                <td>
                                    <div class="btn-group" role="group">
                                        <button class="btn btn-sm btn-outline-primary">
                                            <i class="fas fa-download"></i>
                                        </button>
                                        <button class="btn btn-sm btn-outline-danger">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection 
