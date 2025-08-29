// Современный JavaScript для CRM Studio Landing
document.addEventListener('DOMContentLoaded', function() {
    
    // Инициализация всех анимаций и эффектов
    initAnimations();
    initScrollEffects();
    initInteractiveElements();
    initNavbarEffects();
    initCounterAnimations();
    
    // Инициализация переключения экранов с небольшой задержкой
    setTimeout(() => {
        initScreenSwitching();
    }, 1000);
    
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
                
                this.style.transform = `perspective(1000px) rotateX(${rotateX}deg) rotateY(${rotateY}deg) translateZ(20px)`;
            });
            
            card.addEventListener('mouseleave', function() {
                this.style.transform = 'perspective(1000px) rotateX(0deg) rotateY(0deg) translateZ(0px)';
            });
        });
        
        // Эффект волны для кнопок (исключаем кнопки в слайдере)
        document.querySelectorAll('.btn:not(.hero-buttons .btn)').forEach(btn => {
            btn.addEventListener('click', function(e) {
                const ripple = document.createElement('span');
                const rect = this.getBoundingClientRect();
                const size = Math.max(rect.width, rect.height);
                const x = e.clientX - rect.left - size / 2;
                const y = e.clientY - rect.top - size / 2;
                
                ripple.style.width = ripple.style.height = size + 'px';
                ripple.style.left = x + 'px';
                ripple.style.top = y + 'px';
                ripple.classList.add('ripple');
                
                this.appendChild(ripple);
                
                setTimeout(() => {
                    ripple.remove();
                }, 600);
            });
        });
    }
    
    // Эффекты навигации
    function initNavbarEffects() {
        const navbar = document.querySelector('.navbar');
        const navbarLinks = document.querySelectorAll('.navbar-nav .nav-link');
        
        // Плавное появление навигации
        setTimeout(() => {
            navbar.style.opacity = '1';
            navbar.style.transform = 'translateY(0)';
        }, 100);
        
        // Эффект для ссылок навигации
        navbarLinks.forEach(link => {
            link.addEventListener('mouseenter', function() {
                this.style.transform = 'translateY(-2px)';
            });
            
            link.addEventListener('mouseleave', function() {
                this.style.transform = 'translateY(0)';
            });
        });
    }
    
    // Анимация счетчиков
    function initCounterAnimations() {
        const counters = document.querySelectorAll('.counter');
        
        const animateCounter = (counter) => {
            const target = parseInt(counter.dataset.target);
            const duration = 2000;
            const step = target / (duration / 16);
            let current = 0;
            
            const timer = setInterval(() => {
                current += step;
                if (current >= target) {
                    current = target;
                    clearInterval(timer);
                }
                
                // Форматирование в зависимости от типа данных
                let formattedValue;
                if (counter.textContent.includes('₽')) {
                    // Для валюты
                    formattedValue = '₽' + Math.floor(current).toLocaleString();
                } else if (counter.textContent.includes('M')) {
                    // Для миллионов
                    formattedValue = (current / 1000000).toFixed(1) + 'M';
                } else if (counter.textContent.includes('+')) {
                    // Для чисел с плюсом
                    formattedValue = Math.floor(current).toLocaleString() + '+';
                } else if (counter.textContent.includes('.')) {
                    // Для десятичных чисел
                    formattedValue = current.toFixed(1);
                } else {
                    // Для обычных чисел
                    formattedValue = Math.floor(current).toLocaleString();
                }
                
                counter.textContent = formattedValue;
            }, 16);
        };
        
        const counterObserver = new IntersectionObserver((entries) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    animateCounter(entry.target);
                    counterObserver.unobserve(entry.target);
                }
            });
        }, { threshold: 0.5 });
        
        counters.forEach(counter => {
            counterObserver.observe(counter);
        });
    }
    
    // Плавная прокрутка для якорных ссылок
    document.querySelectorAll('a[href^="#"]').forEach(anchor => {
        anchor.addEventListener('click', function (e) {
            e.preventDefault();
            const target = document.querySelector(this.getAttribute('href'));
            if (target) {
                target.scrollIntoView({
                    behavior: 'smooth',
                    block: 'start'
                });
            }
        });
    });
    
    // Эффект загрузки страницы
    window.addEventListener('load', () => {
        document.body.classList.add('loaded');
        
        // Анимация появления контента
        const heroContent = document.querySelector('.hero-section .container');
        if (heroContent) {
            heroContent.style.opacity = '0';
            heroContent.style.transform = 'translateY(30px)';
            
            setTimeout(() => {
                heroContent.style.transition = 'all 1s ease';
                heroContent.style.opacity = '1';
                heroContent.style.transform = 'translateY(0)';
            }, 300);
        }
        
        // Анимация плавающих элементов
        initFloatingElements();
    });
    
    // Анимация плавающих элементов
    function initFloatingElements() {
        const floatingElements = document.querySelectorAll('.floating-element');
        floatingElements.forEach((element, index) => {
            element.style.animationDelay = `${index * 2}s`;
            element.style.animationDuration = `${6 + index}s`;
        });
    }
    
    // Убрали эффект печатающегося текста
    
    // Функция переключения экранов в телефоне
    function initScreenSwitching() {
        const bookingScreen = document.getElementById('booking-screen');
        const instagramScreen = document.getElementById('instagram-screen');
        const indicatorDots = document.querySelectorAll('.indicator-dot');
        
        console.log('Booking screen:', bookingScreen);
        console.log('Instagram screen:', instagramScreen);
        console.log('Indicator dots:', indicatorDots);
        
        if (!bookingScreen || !instagramScreen) {
            console.log('Screens not found!');
            return;
        }
        
        // Добавляем тестовое переключение через 3 секунды
        setTimeout(() => {
            console.log('Test switch to Instagram...');
            bookingScreen.classList.remove('active');
            instagramScreen.classList.add('active');
            if (indicatorDots.length >= 2) {
                indicatorDots[0].classList.remove('active');
                indicatorDots[1].classList.add('active');
            }
        }, 3000);
        
        let currentScreen = 'booking';
        
        function switchScreen() {
            console.log('Switching screen from:', currentScreen);
            if (currentScreen === 'booking') {
                bookingScreen.classList.remove('active');
                instagramScreen.classList.add('active');
                if (indicatorDots.length >= 2) {
                    indicatorDots[0].classList.remove('active');
                    indicatorDots[1].classList.add('active');
                }
                currentScreen = 'instagram';
            } else {
                instagramScreen.classList.remove('active');
                bookingScreen.classList.add('active');
                if (indicatorDots.length >= 2) {
                    indicatorDots[1].classList.remove('active');
                    indicatorDots[0].classList.add('active');
                }
                currentScreen = 'booking';
            }
            console.log('Switched to:', currentScreen);
        }
        
        // Переключаем экраны каждые 4 секунды (увеличили время)
        setInterval(switchScreen, 4000);
        console.log('Screen switching initialized');
        
        // Добавляем кнопку для ручного тестирования
        const testButton = document.querySelector('.test-switch-btn');
        if (testButton) {
            testButton.addEventListener('click', () => {
                console.log('Manual test button clicked');
                switchScreen();
            });
        }
    }
});

// Убираем проблемный код с additionalStyles 