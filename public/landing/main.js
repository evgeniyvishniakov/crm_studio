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
    initAnalyticsSlider();
    initTestimonialsSlider();
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

// Слайдер для графиков в разделе "Детальна аналітика"
function initAnalyticsSlider() {
    const chartSteps = document.querySelectorAll('.chart-step');
    const chartDots = document.querySelectorAll('.chart-dot');
    let currentChartStep = 0;
    
    if (chartSteps.length === 0) {
        return;
    }
    
    // Показываем первый график
    showChartStep(0);
    
    // Автоматическое переключение каждые 4 секунды
    setInterval(() => {
        nextChartStep();
    }, 4000);
    
    // Функция показа графика
    function showChartStep(stepIndex) {
        // Скрываем все графики
        chartSteps.forEach((step, index) => {
            if (index === stepIndex) {
                // Показываем текущий график
                step.style.display = 'block';
                step.style.visibility = 'visible';
                step.style.opacity = '0';
                step.style.transform = 'translateX(20px)';
                step.classList.add('active');
                step.classList.remove('hide');
                
                // Анимация появления
                setTimeout(() => {
                    step.style.transition = 'all 0.5s ease';
                    step.style.opacity = '1';
                    step.style.transform = 'translateX(0)';
                }, 50);
            } else {
                // Скрываем остальные графики
                step.style.display = 'none';
                step.style.visibility = 'hidden';
                step.style.opacity = '0';
                step.style.transform = 'translateX(-20px)';
                step.classList.remove('active');
                step.classList.add('hide');
            }
        });
        
        // Обновляем индикатор прогресса
        chartDots.forEach((dot, index) => {
            if (index === stepIndex) {
                dot.classList.add('active');
            } else {
                dot.classList.remove('active');
            }
        });
        
        currentChartStep = stepIndex;
    }
    
    // Следующий график
    function nextChartStep() {
        const nextStepIndex = (currentChartStep + 1) % chartSteps.length;
        showChartStep(nextStepIndex);
    }
    
    // Предыдущий график
    function prevChartStep() {
        const prevStepIndex = currentChartStep === 0 ? chartSteps.length - 1 : currentChartStep - 1;
        showChartStep(prevStepIndex);
    }
    
    // Добавляем обработчики кликов на точки
    chartDots.forEach((dot, index) => {
        dot.addEventListener('click', () => {
            showChartStep(index);
        });
    });
}

// Слайдер для отзывов
function initTestimonialsSlider() {
    const testimonialSlides = document.querySelectorAll('.testimonial-slide');
    const testimonialDots = document.querySelectorAll('.testimonial-dot');
    const prevBtn = document.getElementById('testimonialPrev');
    const nextBtn = document.getElementById('testimonialNext');
    let currentTestimonialSlide = 0;
    let autoSlideInterval;
    
    if (testimonialSlides.length === 0) {
        return;
    }
    
    // Показываем первый слайд
    showTestimonialSlide(0);
    
    // Автоматическое переключение каждые 6 секунд
    startAutoSlide();
    
    // Функция показа слайда
    function showTestimonialSlide(slideIndex) {
        // Скрываем все слайды
        testimonialSlides.forEach((slide, index) => {
            if (index === slideIndex) {
                // Показываем текущий слайд
                slide.style.display = 'block';
                slide.style.visibility = 'visible';
                slide.style.opacity = '0';
                slide.style.transform = 'translateX(30px)';
                slide.classList.add('active');
                slide.classList.remove('hide');
                
                // Анимация появления
                setTimeout(() => {
                    slide.style.transition = 'all 0.6s ease';
                    slide.style.opacity = '1';
                    slide.style.transform = 'translateX(0)';
                }, 50);
            } else {
                // Скрываем остальные слайды
                slide.style.display = 'none';
                slide.style.visibility = 'hidden';
                slide.style.opacity = '0';
                slide.style.transform = 'translateX(-30px)';
                slide.classList.remove('active');
                slide.classList.add('hide');
            }
        });
        
        // Обновляем индикатор прогресса
        testimonialDots.forEach((dot, index) => {
            if (index === slideIndex) {
                dot.classList.add('active');
            } else {
                dot.classList.remove('active');
            }
        });
        
        currentTestimonialSlide = slideIndex;
    }
    
    // Следующий слайд
    function nextTestimonialSlide() {
        const nextSlideIndex = (currentTestimonialSlide + 1) % testimonialSlides.length;
        showTestimonialSlide(nextSlideIndex);
    }
    
    // Предыдущий слайд
    function prevTestimonialSlide() {
        const prevSlideIndex = currentTestimonialSlide === 0 ? testimonialSlides.length - 1 : currentTestimonialSlide - 1;
        showTestimonialSlide(prevSlideIndex);
    }
    
    // Запуск автоматического слайдера
    function startAutoSlide() {
        autoSlideInterval = setInterval(() => {
            nextTestimonialSlide();
        }, 6000);
    }
    
    // Остановка автоматического слайдера
    function stopAutoSlide() {
        if (autoSlideInterval) {
            clearInterval(autoSlideInterval);
        }
    }
    
    // Обработчики кнопок навигации
    if (prevBtn) {
        prevBtn.addEventListener('click', () => {
            stopAutoSlide();
            prevTestimonialSlide();
            startAutoSlide();
        });
    }
    
    if (nextBtn) {
        nextBtn.addEventListener('click', () => {
            stopAutoSlide();
            nextTestimonialSlide();
            startAutoSlide();
        });
    }
    
    // Добавляем обработчики кликов на точки
    testimonialDots.forEach((dot, index) => {
        dot.addEventListener('click', () => {
            stopAutoSlide();
            showTestimonialSlide(index);
            startAutoSlide();
        });
    });
    
    // Останавливаем автопрокрутку при наведении
    const sliderContainer = document.querySelector('.testimonials-slider-container');
    if (sliderContainer) {
        sliderContainer.addEventListener('mouseenter', stopAutoSlide);
        sliderContainer.addEventListener('mouseleave', startAutoSlide);
    }
    
    // Поддержка клавиатуры
    document.addEventListener('keydown', (e) => {
        if (e.key === 'ArrowLeft') {
            stopAutoSlide();
            prevTestimonialSlide();
            startAutoSlide();
        } else if (e.key === 'ArrowRight') {
            stopAutoSlide();
            nextTestimonialSlide();
            startAutoSlide();
        }
    });
} 