@extends('landing.layouts.app')

@section('title', 'Настройка ролей и доступов - Trimora')
@section('description', 'Пошаговая инструкция по настройке ролей и доступов в системе Trimora')
@section('keywords', 'роли, доступы, настройки, Trimora, CRM')

@section('content')
<!-- Hero Section -->
<section class="py-5 bg-light">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-lg-8 text-center">
                <nav aria-label="breadcrumb" class="mb-4">
                    <ol class="breadcrumb justify-content-center">
                        <li class="breadcrumb-item"><a href="{{ route('beautyflow.index') }}" class="text-decoration-none">Главная</a></li>
                        <li class="breadcrumb-item"><a href="{{ route('beautyflow.knowledge') }}" class="text-decoration-none">База знаний</a></li>
                        <li class="breadcrumb-item active" aria-current="page">Настройка ролей и доступов</li>
                    </ol>
                </nav>
                <h1 class="display-4 fw-bold mb-4">Настройка ролей и доступов</h1>
                <div class="d-flex align-items-center justify-content-center gap-4 text-muted">
                    <div class="d-flex align-items-center gap-2">
                        <i class="fas fa-user"></i>
                        <span>Команда Trimora</span>
                    </div>
                    <div class="d-flex align-items-center gap-2">
                        <i class="fas fa-folder"></i>
                        <span>Начало работы</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Article Content -->
<section class="py-5">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-lg-8">
                <div class="card border-0 shadow-sm">
                    <div class="card-body p-5">
                        <!-- Introduction -->
                        <div class="mb-5">
                            <p class="lead text-muted">
                                В этом руководстве вы узнаете, как настроить роли и права доступа для сотрудников вашего салона красоты в системе Trimora.
                            </p>
                        </div>

                        <!-- Step 1 -->
                        <div class="mb-5">
                            <h2 class="h3 fw-bold mb-4 text-primary">Шаг 1. Переход в раздел "Роли и доступы"</h2>
                            <div class="bg-light p-4 rounded">
                                <p class="mb-0">
                                    Откройте меню <strong>Настройки</strong> и выберите раздел <strong>Роли и доступы</strong>.
                                </p>
                            </div>
                        </div>

                        <!-- Step 2 -->
                        <div class="mb-5">
                            <h2 class="h3 fw-bold mb-4 text-primary">Шаг 2. Создание ролей</h2>
                            <div class="bg-light p-4 rounded">
                                <p class="mb-0">
                                    Нажмите кнопку <strong>Добавить роль</strong> и введите название роли (например: Администратор, Менеджер, Мастер и т.д.).
                                </p>
                            </div>
                        </div>

                        <!-- Step 3 -->
                        <div class="mb-5">
                            <h2 class="h3 fw-bold mb-4 text-primary">Шаг 3. Настройка доступов</h2>
                            <div class="bg-light p-4 rounded">
                                <p class="mb-3">
                                    Для каждой роли выберите пункты меню, к которым будет открыт доступ.
                                </p>
                                <p class="mb-0">
                                    Если доступ к определённому пункту меню закрыт, этот пункт всё равно будет отображаться в меню, но:
                                </p>
                                <ul class="mt-3 mb-0">
                                    <li>рядом с ним будет значок замка;</li>
                                    <li>он будет подсвечен тусклым цветом;</li>
                                    <li>переход по нему будет невозможен (не кликабелен).</li>
                                </ul>
                            </div>
                        </div>

                        <!-- Example -->
                        <div class="mb-5">
                            <h3 class="h4 fw-bold mb-3">Пример</h3>
                            <div class="bg-warning bg-opacity-10 p-4 rounded border-start border-warning border-4">
                                <p class="mb-0">
                                    Если у роли "Мастер" закрыт доступ к разделу <strong>Отчёты</strong>, то в меню этот пункт будет отображён с замочком и не будет активным.
                                </p>
                            </div>
                        </div>

                        <!-- Tips -->
                        <div class="bg-info bg-opacity-10 p-4 rounded">
                            <h4 class="h5 fw-bold mb-3">
                                <i class="fas fa-lightbulb text-info me-2"></i>
                                Полезные советы
                            </h4>
                            <ul class="mb-0">
                                <li>Создавайте роли с минимально необходимыми правами доступа</li>
                                <li>Регулярно пересматривайте настройки доступа</li>
                                <li>Используйте предустановленные роли как основу для создания новых</li>
                            </ul>
                        </div>
                    </div>
                </div>

                <!-- Navigation -->
                <div class="d-flex justify-content-between mt-5">
                    <a href="{{ route('beautyflow.knowledge') }}" class="btn btn-outline-primary">
                        <i class="fas fa-arrow-left me-2"></i>
                        Вернуться к базе знаний
                    </a>
                    <a href="#" class="btn btn-primary">
                        Следующая статья
                        <i class="fas fa-arrow-right ms-2"></i>
                    </a>
                </div>
            </div>
        </div>
    </div>
</section>
@endsection

@push('styles')
<style>
.breadcrumb-item + .breadcrumb-item::before {
    content: ">";
    color: var(--bs-secondary);
}

.breadcrumb-item a {
    color: var(--bs-primary);
}

.breadcrumb-item a:hover {
    color: var(--bs-primary-dark);
}

.breadcrumb-item.active {
    color: var(--bs-secondary);
}
</style>
@endpush
