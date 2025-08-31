class LanguageManager {
    constructor() {
        this.languages = [];
        this.currentLanguage = 'ru';
        this.defaultLanguage = 'ru';
        this.isLoaded = false;
        this.loadingPromise = null;
        
        // Проверяем localStorage для восстановления выбранного языка
        const savedLanguage = localStorage.getItem('selectedLanguage');
        if (savedLanguage) {
            this.currentLanguage = savedLanguage;
        }
        
        // Инициализация
        this.init();
    }

    /**
     * Инициализация менеджера
     */
    async init() {
        try {
            await this.loadLanguages();
            this.setupEventListeners();
            this.updateAllLanguageDisplays();
        } catch (error) {
            // Ошибка инициализации LanguageManager
        }
    }

    /**
     * Загрузка языков из API
     */
    async loadLanguages() {
        if (this.loadingPromise) {
            return this.loadingPromise;
        }

        this.loadingPromise = fetch('/api/languages')
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    this.languages = data.languages;
                    // Приоритет: сохраненный язык > текущий язык сервера > язык по умолчанию
                    const savedLanguage = localStorage.getItem('selectedLanguage');
                    this.currentLanguage = savedLanguage || data.current || data.default;
                    this.defaultLanguage = data.default;
                    this.isLoaded = true;
                    
                    // Сохраняем в localStorage для кэширования
                    localStorage.setItem('languages', JSON.stringify({
                        languages: this.languages,
                        current: this.currentLanguage,
                        default: this.defaultLanguage,
                        timestamp: Date.now()
                    }));

                    // Обновляем все отображения языков на странице
                    this.updateAllLanguageDisplays();
                    
                    // Принудительно обновляем селекторы языка на странице настроек
                    if (window.location.pathname.includes('/settings')) {
                        this.updateLanguageSelectors();
                    }
                } else {
                    throw new Error('Не удалось загрузить языки');
                }
            })
            .catch(error => {
                // Пробуем загрузить из кэша
                this.loadFromCache();
                throw error;
            });

        return this.loadingPromise;
    }

    /**
     * Загрузка из кэша
     */
    loadFromCache() {
        const cached = localStorage.getItem('languages');
        if (cached) {
            try {
                const data = JSON.parse(cached);
                const cacheAge = Date.now() - data.timestamp;
                
                // Кэш действителен 1 час
                if (cacheAge < 3600000) {
                    this.languages = data.languages || [];
                    this.currentLanguage = data.current || 'ru';
                    this.defaultLanguage = data.default || 'ru';
                    this.isLoaded = true;
                    return true;
                }
            } catch (error) {
                // Ошибка загрузки из кэша
            }
        }
        return false;
    }

    /**
     * Настройка обработчиков событий
     */
    setupEventListeners() {
        // Обработчик клика по флагам языков
        document.addEventListener('click', (e) => {
            if (e.target.matches('[data-language-flag]')) {
                const languageCode = e.target.dataset.languageCode;
                this.changeLanguage(languageCode);
            }
        });
        

    }

    /**
     * Обновить все отображения языков на странице
     */
    updateAllLanguageDisplays() {
        // НЕ обновляем селекторы языков на странице настроек
        if (window.location.pathname.includes('/settings')) {
            // Пропускаем обновление селекторов на странице настроек
        } else {
            // Обновляем селекторы языков
            this.updateLanguageSelectors();
        }
        
        // Обновляем флаги языков
        this.updateLanguageFlags();
        
        // Обновляем названия языков
        this.updateLanguageNames();
        
        // Обновляем переводы на странице
        this.updatePageTranslations();
    }

    /**
     * Обновить селекторы языков
     */
    updateLanguageSelectors() {
        // Обновляем селекторы с data-language-selector атрибутом
        const selectors = document.querySelectorAll('[data-language-selector]');
        
        selectors.forEach(selector => {
            // Находим опцию с соответствующим кодом языка
            const options = Array.from(selector.options);
            const targetOption = options.find(option => {
                const optionText = option.text.toLowerCase();
                const currentLang = this.currentLanguage.toLowerCase();
                
                // Проверяем соответствие по коду языка
                if (currentLang === 'en' && optionText.includes('english')) return true;
                if (currentLang === 'ru' && optionText.includes('русский')) return true;
                if (currentLang === 'ua' && optionText.includes('українська')) return true;
                
                return false;
            });
            
            if (targetOption) {
                if (selector.value !== targetOption.value) {
                    selector.value = targetOption.value;
                }
            }
        });
    }

    /**
     * Обновить флаги языков
     */
    updateLanguageFlags() {
        const flags = document.querySelectorAll('[data-language-flag]');
        flags.forEach(flag => {
            const languageCode = flag.dataset.languageCode;
            if (languageCode === this.currentLanguage) {
                flag.classList.add('active');
            } else {
                flag.classList.remove('active');
            }
        });
    }

    /**
     * Обновить названия языков
     */
    updateLanguageNames() {
        const currentLanguage = this.languages.find(lang => lang.code === this.currentLanguage);
        if (currentLanguage) {
            const nameElements = document.querySelectorAll('[data-language-name]');
            nameElements.forEach(element => {
                element.textContent = currentLanguage.name;
            });
            
            const nativeNameElements = document.querySelectorAll('[data-language-native-name]');
            nativeNameElements.forEach(element => {
                element.textContent = currentLanguage.native_name;
            });
        }
    }

    /**
     * Обновить переводы на странице
     */
    async updatePageTranslations() {
        try {
            const response = await fetch('/api/languages/translations');
            const data = await response.json();
            
            if (data.success && data.translations) {
                // Обновляем элементы с data-translate атрибутом
                const translateElements = document.querySelectorAll('[data-translate]');
                translateElements.forEach(element => {
                    const key = element.dataset.translate;
                    if (data.translations[key]) {
                        element.textContent = data.translations[key];
                    }
                });
                
                // Обновляем window.translations для JavaScript функций
                if (window.translations) {
                    Object.assign(window.translations, data.translations);
                }
                
                // Обновляем переводы для функций форматирования длительности
                this.updateDurationTranslations();
            }
        } catch (error) {
            // Ошибка обновления переводов
        }
    }
    
    /**
     * Обновить переводы для функций форматирования длительности
     */
    updateDurationTranslations() {
        // Проверяем, что все необходимые переводы загружены
        const requiredTranslations = ['minute', 'hour', 'hours', 'hours_many', 'duration_prefix'];
        const missingTranslations = requiredTranslations.filter(key => !window.translations?.[key]);
        
        if (missingTranslations.length > 0) {
            // Отсутствуют переводы для длительности
        }
    }

    /**
     * Получить текущий язык
     */
    getCurrentLanguage() {
        return this.currentLanguage;
    }

    /**
     * Получить объект текущего языка
     */
    getCurrentLanguageObject() {
        return this.languages.find(lang => lang.code === this.currentLanguage);
    }

    /**
     * Получить перевод по ключу
     */
    getTranslation(key) {
        // Пока что возвращаем ключ, в будущем можно добавить кэширование переводов
        return key;
    }

    /**
     * Изменить язык
     */
    async changeLanguage(code) {
        try {
            const url = `/api/languages/set/${code}`;
            
            const response = await fetch(url, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content || '',
                    'Accept': 'application/json',
                }
            });

            const data = await response.json();
            
            if (data.success) {
                this.currentLanguage = code;
                
                // Сначала обновляем переводы на текущей странице
                await this.updatePageTranslations();
                this.updateAllLanguageDisplays();
                
                // Принудительно обновляем селекторы языка перед перезагрузкой
                this.updateLanguageSelectors();
                
                // Генерируем событие о смене языка для других компонентов
                document.dispatchEvent(new CustomEvent('languageChanged', {
                    detail: { languageCode: code }
                }));
                
                // Сохраняем выбранный язык в localStorage для надежности
                localStorage.setItem('selectedLanguage', code);
                
                // Если мы находимся в CRM и есть URL лендинга, предлагаем перейти туда
                if (data.landing_urls && window.location.pathname.includes('/')) {
                    // Показываем уведомление с предложением перейти на лендинг
                    this.showLandingRedirectNotification(data.landing_urls.index, code);
                }
                
                // Небольшая задержка перед перезагрузкой для применения изменений
                setTimeout(() => {
                    window.location.reload();
                }, 100);
                
                return true;
            } else {
                throw new Error(data.message);
            }
        } catch (error) {
            // НЕ показываем уведомление об ошибке - оно будет показано в форме настроек
            return false;
        }
    }

    /**
     * Обработка формы языка и валюты
     */
    async handleLanguageCurrencyForm(form) {
        try {
            const formData = new FormData(form);
            const languageId = formData.get('language_id');
            const currencyId = formData.get('currency_id');
            

            
            // Находим язык по ID
            const language = this.languages.find(lang => lang.id == languageId);
            if (!language) {
                throw new Error('Язык не найден');
            }
            
            // Изменяем язык
            const success = await this.changeLanguage(language.code);
            
            if (success) {
                // Показываем уведомление об успехе
                const notification = document.getElementById('language-currency-notification');
                if (notification) {
                    notification.innerHTML = '<div class="alert alert-success">Настройки успешно сохранены!</div>';
                    setTimeout(() => {
                        notification.innerHTML = '';
                    }, 3000);
                }
                
                // Обновляем селектор
                this.updateLanguageSelectors();
            }
            
        } catch (error) {
            // Показываем уведомление об ошибке
            const notification = document.getElementById('language-currency-notification');
            if (notification) {
                notification.innerHTML = '<div class="alert alert-danger">Ошибка сохранения настроек: ' + error.message + '</div>';
            }
        }
    }

    /**
     * Получить все доступные языки
     */
    getLanguages() {
        return this.languages;
    }

    /**
     * Проверить, загружены ли языки
     */
    isLanguagesLoaded() {
        return this.isLoaded;
    }

    /**
     * Очистить кэш
     */
    clearCache() {
        localStorage.removeItem('languages');
        this.isLoaded = false;
        this.loadingPromise = null;
    }
    
    /**
     * Принудительное обновление языков
     */
    async refresh() {
        this.isLoaded = false;
        this.loadingPromise = null;
        localStorage.removeItem('languages');
        await this.loadLanguages();
        this.updateAllLanguageDisplays();
    }

    /**
     * Показать уведомление о переходе на лендинг
     */
    showLandingRedirectNotification(landingUrl, languageCode) {
        const notification = document.createElement('div');
        notification.className = 'alert alert-info alert-dismissible fade show';
        notification.style.position = 'fixed';
        notification.style.top = '20px';
        notification.style.right = '20px';
        notification.style.zIndex = '9999';
        notification.style.maxWidth = '400px';
        
        const languageNames = {
            'en': 'English',
            'ru': 'Русский',
            'ua': 'Українська'
        };
        
        notification.innerHTML = `
            <strong>Язык изменен на ${languageNames[languageCode] || languageCode}</strong><br>
            <small>Хотите перейти на лендинг с новым языком?</small>
            <div class="mt-2">
                <a href="${landingUrl}" class="btn btn-sm btn-primary">Перейти на лендинг</a>
                <button type="button" class="btn btn-sm btn-secondary" onclick="this.parentElement.parentElement.remove()">Остаться в CRM</button>
            </div>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        `;
        
        document.body.appendChild(notification);
        
        // Автоматически скрываем через 10 секунд
        setTimeout(() => {
            if (notification.parentElement) {
                notification.remove();
            }
        }, 10000);
    }
}

// Создаем глобальный экземпляр
window.LanguageManager = new LanguageManager();

// Автоматическое обновление каждые 30 секунд
setInterval(() => {
    if (window.LanguageManager && window.LanguageManager.isLanguagesLoaded()) {
        window.LanguageManager.updateAllLanguageDisplays();
    }
}, 30000); 