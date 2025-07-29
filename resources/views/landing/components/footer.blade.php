<footer class="bg-dark text-light py-5 mt-5">
    <div class="container">
        <div class="row">
            <div class="col-lg-3 col-md-6 mb-4">
                <h5>CRM Studio</h5>
                <p class="text-muted">Профессиональная система управления салоном красоты</p>
                <div class="social-links">
                    <a href="#" class="text-light me-3"><i class="fab fa-facebook"></i></a>
                    <a href="#" class="text-light me-3"><i class="fab fa-instagram"></i></a>
                    <a href="#" class="text-light me-3"><i class="fab fa-telegram"></i></a>
                </div>
            </div>
            <div class="col-lg-3 col-md-6 mb-4">
                <h5>Продукт</h5>
                <ul class="list-unstyled">
                    <li><a href="{{ route('beautyflow.features') }}" class="text-muted text-decoration-none">Возможности</a></li>
                    <li><a href="{{ route('beautyflow.demo') }}" class="text-muted text-decoration-none">Демо</a></li>
                    <li><a href="{{ route('beautyflow.integrations') }}" class="text-muted text-decoration-none">Интеграции</a></li>
                    <li><a href="{{ route('beautyflow.pricing') }}" class="text-muted text-decoration-none">Тарифы</a></li>
                </ul>
            </div>
            <div class="col-lg-3 col-md-6 mb-4">
                <h5>Услуги</h5>
                <ul class="list-unstyled">
                    <li><a href="{{ route('beautyflow.services') }}" class="text-muted text-decoration-none">Все услуги</a></li>
                    <li><a href="#" class="text-muted text-decoration-none">Управление клиентами</a></li>
                    <li><a href="#" class="text-muted text-decoration-none">Записи и расписание</a></li>
                    <li><a href="#" class="text-muted text-decoration-none">Управление товарами</a></li>
                </ul>
            </div>
            <div class="col-lg-3 col-md-6 mb-4">
                <h5>Контакты</h5>
                <ul class="list-unstyled">
                    <li><i class="fas fa-phone me-2"></i>+7 (999) 123-45-67</li>
                    <li><i class="fas fa-envelope me-2"></i>info@crmstudio.ru</li>
                    <li><i class="fas fa-map-marker-alt me-2"></i>Москва, ул. Примерная, 123</li>
                </ul>
            </div>
        </div>
        <hr class="my-4">
        <div class="row">
            <div class="col-md-6">
                <p class="text-muted mb-0">&copy; 2024 CRM Studio. Все права защищены.</p>
            </div>
            <div class="col-md-6 text-end">
                <a href="{{ route('beautyflow.privacy') }}" class="text-muted text-decoration-none me-3">Политика конфиденциальности</a>
                <a href="{{ route('beautyflow.terms') }}" class="text-muted text-decoration-none">Условия использования</a>
            </div>
        </div>
    </div>
</footer> 
