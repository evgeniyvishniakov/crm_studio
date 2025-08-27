@extends('admin.layouts.app')

@section('title', 'Тест настроек - Админ')
@section('page-title', 'Тест системных настроек')

@section('content')
<div class="row g-4">
    <div class="col-12">
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-transparent">
                <h5 class="mb-0">Текущие настройки системы</h5>
            </div>
            <div class="card-body">
                @php
                    $settings = \App\Models\SystemSetting::getSettings();
                @endphp
                
                <div class="row">
                    <div class="col-md-6">
                        <h6>Основные настройки:</h6>
                        <ul class="list-unstyled">
                            <li><strong>Название сайта:</strong> {{ $settings->site_name }}</li>
                            <li><strong>Описание:</strong> {{ $settings->site_description }}</li>
                            <li><strong>Email админа:</strong> {{ $settings->admin_email }}</li>
                            <li><strong>Часовой пояс:</strong> {{ $settings->timezone }}</li>
                        </ul>
                    </div>
                    <div class="col-md-6">
                        <h6>Изображения:</h6>
                        <ul class="list-unstyled">
                            <li><strong>Логотип:</strong> 
                                @if($settings->landing_logo)
                                    <span class="text-success">Загружен</span>
                                    <br><small class="text-muted">{{ $settings->landing_logo }}</small>
                                @else
                                    <span class="text-muted">Не загружен</span>
                                @endif
                            </li>
                            <li><strong>Фавикон:</strong> 
                                @if($settings->favicon)
                                    <span class="text-success">Загружен</span>
                                    <br><small class="text-muted">{{ $settings->favicon }}</small>
                                @else
                                    <span class="text-muted">Не загружен</span>
                                @endif
                            </li>
                        </ul>
                    </div>
                </div>
                
                <hr>
                
                <h6>Тест хелпера:</h6>
                <ul class="list-unstyled">
                    <li><strong>SystemHelper::getSiteName():</strong> {{ \App\Helpers\SystemHelper::getSiteName() }}</li>
                    <li><strong>SystemHelper::getSiteDescription():</strong> {{ \App\Helpers\SystemHelper::getSiteDescription() }}</li>
                    <li><strong>SystemHelper::hasLandingLogo():</strong> {{ \App\Helpers\SystemHelper::hasLandingLogo() ? 'Да' : 'Нет' }}</li>
                    <li><strong>SystemHelper::hasFavicon():</strong> {{ \App\Helpers\SystemHelper::hasFavicon() ? 'Да' : 'Нет' }}</li>
                </ul>
                
                <div class="mt-3">
                    <a href="{{ route('admin.settings.index') }}" class="btn btn-primary">
                        <i class="fas fa-arrow-left me-2"></i>Вернуться к настройкам
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection



