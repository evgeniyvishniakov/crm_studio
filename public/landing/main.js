// Современный JavaScript для CRM Studio Landing
document.addEventListener('DOMContentLoaded', function() {
    
    // Инициализация всех анимаций и эффектов
    initAnimations();
    initScrollEffects();
    initInteractiveElements();
    initNavbarEffects();
    initCounterAnimations();
    
    // Анимации появления элементов
    function initAnimations() {
        const observerOptions = {
            threshold: 0.1,
            rootMargin: '0px 0px -50px 0px'
        };
        
        const observer = new IntersectionObserver((entries) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    entry.target.classList.add('animate-fade-in-up');
                    observer.unobserve(entry.target);
                }
            });
        }, observerOptions);
        
        // Наблюдаем за всеми карточками и секциями
        document.querySelectorAll('.card, .stat-card, .feature-item').forEach(el => {
            observer.observe(el);
        });
    }
    
    // Эффекты при скролле
    function initScrollEffects() {
        let ticking = false;
        let lastScrollY = window.pageYOffset;
        
        function updateOnScroll() {
            const scrolled = window.pageYOffset;
            const navbar = document.querySelector('.navbar');
            
            // Добавляем класс только при изменении состояния
            if (scrolled > 100 && !navbar.classList.contains('scrolled')) {
                navbar.classList.add('scrolled');
            } else if (scrolled <= 100 && navbar.classList.contains('scrolled')) {
                navbar.classList.remove('scrolled');
            }
            
            lastScrollY = scrolled;
            ticking = false;
        }
        
        function requestTick() {
            if (!ticking) {
                requestAnimationFrame(updateOnScroll);
                ticking = true;
            }
        }
        
        // Используем throttle для оптимизации
        let scrollTimeout;
        window.addEventListener('scroll', () => {
            if (scrollTimeout) {
                clearTimeout(scrollTimeout);
            }
            scrollTimeout = setTimeout(requestTick, 16); // ~60fps
        });
    }
    
    // Интерактивные элементы
    function initInteractiveElements() {
        // Эффект свечения для кнопок (исключаем кнопки в слайдере)
        document.querySelectorAll('.btn:not(.hero-buttons .btn)').forEach(btn => {
            btn.addEventListener('mouseenter', function() {
                this.style.transform = 'translateY(-3px) scale(1.02)';
            });
            
            btn.addEventListener('mouseleave', function() {
                this.style.transform = 'translateY(0) scale(1)';
            });
        });
        
        // Интерактивные карточки
        document.querySelectorAll('.card, .stat-card').forEach(card => {
            card.addEventListener('mouseenter', function() {
                this.style.transform = 'translateY(-10px) scale(1.02)';
                this.style.boxShadow = '0 20px 60px rgba(0, 0, 0, 0.15)';
            });
            
            card.addEventListener('mouseleave', function() {
                this.style.transform = 'translateY(0) scale(1)';
                this.style.boxShadow = '0 10px 30px rgba(0, 0, 0, 0.1)';
            });
        });
        
        // Магнитный эффект для карточек
        document.querySelectorAll('.magnetic').forEach(card => {
            card.addEventListener('mousemove', function(e) {
                const rect = this.getBoundingClientRect();
                const x = e.clientX - rect.left - rect.width / 2;
                const y = e.clientY - rect.top - rect.height / 2;
                
                const rotateX = (y / rect.height) * -10;
                const rotateY = (x / rect.width) * 10;
                
                this.style.transform = `perspective(1000px) rotateX(${rotateX}deg) rotateY(${rotateY}deg) scale3d(1.05, 1.05, 1.05)`;
            });
            
            card.addEventListener('mouseleave', function() {
                this.style.transform = 'perspective(1000px) rotateX(0deg) rotateY(0deg) scale3d(1, 1, 1)';
            });
        });
    }
    
    // Эффекты для навбара
    function initNavbarEffects() {
        const navbar = document.querySelector('.navbar');
        if (!navbar) return;
        
        // Добавляем эффект при скролле
        window.addEventListener('scroll', () => {
            if (window.pageYOffset > 100) {
                navbar.classList.add('scrolled');
            } else {
                navbar.classList.remove('scrolled');
            }
        });
        
        // Плавное появление при загрузке
        setTimeout(() => {
            navbar.style.opacity = '1';
            navbar.style.transform = 'translateY(0)';
        }, 100);
    }
    
    // Анимация счетчиков
    function initCounterAnimations() {
        const counters = document.querySelectorAll('.counter');
        if (counters.length === 0) return;
        
        const observerOptions = {
            threshold: 0.5,
            rootMargin: '0px 0px -100px 0px'
        };
        
        const observer = new IntersectionObserver((entries) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    const counter = entry.target;
                    const target = parseInt(counter.getAttribute('data-target'));
                    const duration = 2000; // 2 секунды
                    const step = target / (duration / 16); // 60fps
                    let current = 0;
                    
                    const timer = setInterval(() => {
                        current += step;
                        if (current >= target) {
                            current = target;
                            clearInterval(timer);
                        }
                        counter.textContent = Math.floor(current);
                    }, 16);
                    
                    observer.unobserve(counter);
                }
            });
        }, observerOptions);
        
        counters.forEach(counter => observer.observe(counter));
    }
});

// Слайдер для веб-записи
document.addEventListener('DOMContentLoaded', function() {
    initWebBookingSlider();
});

function initWebBookingSlider() {
    const steps = document.querySelectorAll('.form-step');
    const progressDots = document.querySelectorAll('.progress-dot');
    let currentStep = 0;
    
    if (steps.length === 0) {
        return;
    }
    
    // Показываем первый шаг
    showStep(0);
    
    // Автоматическое переключение каждые 3 секунды
    setInterval(() => {
        nextStep();
    }, 3000);
    
    // Функция показа шага
    function showStep(stepIndex) {
        // Скрываем все шаги
        steps.forEach((step, index) => {
            if (index === stepIndex) {
                // Показываем текущий шаг
                step.style.display = 'block';
                step.style.visibility = 'visible';
                step.style.opacity = '0';
                step.style.transform = 'translateX(20px)';
                step.classList.add('active', 'show');
                step.classList.remove('hide');
                
                // Анимация появления
                setTimeout(() => {
                    step.style.transition = 'all 0.5s ease';
                    step.style.opacity = '1';
                    step.style.transform = 'translateX(0)';
                }, 50);
            } else {
                // Скрываем остальные шаги
                step.style.display = 'none';
                step.style.visibility = 'hidden';
                step.style.opacity = '0';
                step.style.transform = 'translateX(-20px)';
                step.classList.remove('active', 'show');
                step.classList.add('hide');
            }
        });
        
        // Обновляем индикатор прогресса
        progressDots.forEach((dot, index) => {
            if (index === stepIndex) {
                dot.classList.add('active');
            } else {
                dot.classList.remove('active');
            }
        });
        
        currentStep = stepIndex;
    }
    
    // Следующий шаг
    function nextStep() {
        const nextStepIndex = (currentStep + 1) % steps.length;
        showStep(nextStepIndex);
    }
    
    // Предыдущий шаг
    function prevStep() {
        const prevStepIndex = currentStep === 0 ? steps.length - 1 : currentStep - 1;
        showStep(prevStepIndex);
    }
    
    // Добавляем кнопки навигации если их нет
    addNavigationButtons();
    
    function addNavigationButtons() {
        const formBody = document.querySelector('.form-body');
        if (!formBody || formBody.querySelector('.slider-nav')) return;
        
        const navDiv = document.createElement('div');
        navDiv.className = 'slider-nav d-flex justify-content-between mt-3';
        navDiv.innerHTML = `
            <button class="btn btn-sm btn-outline-secondary" id="prevStep">
                <i class="fas fa-chevron-left"></i> Назад
            </button>
            <button class="btn btn-sm btn-primary" id="nextStep">
                Далі <i class="fas fa-chevron-right"></i>
            </button>
            <span class="text-muted small">Шаг ${currentStep + 1} из ${steps.length}</span>
        `;
        
        formBody.appendChild(navDiv);
        
        // Обработчики кнопок
        document.getElementById('prevStep').addEventListener('click', prevStep);
        document.getElementById('nextStep').addEventListener('click', nextStep);
    }
} 