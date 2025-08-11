@extends('landing.layouts.app')

@section('title', 'Контакты - Trimora')
@section('description', 'Свяжитесь с нами для получения поддержки или консультации по Trimora')

@section('content')
<!-- Hero Section -->
<section class="bg-light py-5">
    <div class="container">
        <div class="row">
            <div class="col-lg-8 mx-auto text-center">
                <h1 class="display-4 fw-bold mb-4">Свяжитесь с нами</h1>
                <p class="lead text-muted">Мы всегда готовы помочь и ответить на ваши вопросы</p>
            </div>
        </div>
    </div>
</section>

<!-- Contact Content -->
<section class="py-5">
    <div class="container">
        <div class="row">
            <div class="col-lg-6">
                <h2 class="fw-bold mb-4">Напишите нам</h2>
                <form>
                    <div class="mb-3">
                        <label for="name" class="form-label">Имя</label>
                        <input type="text" class="form-control" id="name" required>
                    </div>
                    <div class="mb-3">
                        <label for="email" class="form-label">Email</label>
                        <input type="email" class="form-control" id="email" required>
                    </div>
                    <div class="mb-3">
                        <label for="subject" class="form-label">Тема</label>
                        <input type="text" class="form-control" id="subject" required>
                    </div>
                    <div class="mb-3">
                        <label for="message" class="form-label">Сообщение</label>
                        <textarea class="form-control" id="message" rows="5" required></textarea>
                    </div>
                    <button type="submit" class="btn btn-primary">Отправить сообщение</button>
                </form>
            </div>
            
            <div class="col-lg-6">
                <h2 class="fw-bold mb-4">Контактная информация</h2>
                
                <div class="card border-0 shadow-sm mb-4">
                    <div class="card-body">
                        <div class="d-flex align-items-center mb-3">
                            <div class="bg-primary text-white rounded-circle d-flex align-items-center justify-content-center me-3" style="width: 50px; height: 50px;">
                                <i class="fas fa-phone"></i>
                            </div>
                            <div>
                                <h5 class="mb-1">Телефон</h5>
                                <p class="text-muted mb-0">+7 (999) 123-45-67</p>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="card border-0 shadow-sm mb-4">
                    <div class="card-body">
                        <div class="d-flex align-items-center mb-3">
                            <div class="bg-success text-white rounded-circle d-flex align-items-center justify-content-center me-3" style="width: 50px; height: 50px;">
                                <i class="fas fa-envelope"></i>
                            </div>
                            <div>
                                <h5 class="mb-1">Email</h5>
                                <p class="text-muted mb-0">info@crmstudio.ru</p>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="card border-0 shadow-sm">
                    <div class="card-body">
                        <div class="d-flex align-items-center mb-3">
                            <div class="bg-info text-white rounded-circle d-flex align-items-center justify-content-center me-3" style="width: 50px; height: 50px;">
                                <i class="fas fa-map-marker-alt"></i>
                            </div>
                            <div>
                                <h5 class="mb-1">Адрес</h5>
                                <p class="text-muted mb-0">Москва, ул. Примерная, 123</p>
                            </div>
                        </div>
                    </div>
                </div>
                
                <h3 class="fw-bold mt-4 mb-3">Время работы</h3>
                <ul class="list-unstyled">
                    <li class="mb-2"><strong>Понедельник - Пятница:</strong> 9:00 - 18:00</li>
                    <li class="mb-2"><strong>Суббота:</strong> 10:00 - 16:00</li>
                    <li class="mb-2"><strong>Воскресенье:</strong> Выходной</li>
                </ul>
            </div>
        </div>
    </div>
</section>
@endsection 
