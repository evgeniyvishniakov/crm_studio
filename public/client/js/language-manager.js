class LanguageManager {
    constructor() {
        this.languages = [];
        this.currentLanguage = 'ru';
        this.defaultLanguage = 'ru';
        this.isLoaded = false;
        this.loadingPromise = null;
        
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
            console.error('Ошибка инициализации LanguageManager:', error);
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
                    // Приоритет: текущий язык (уже определен на сервере) > язык по умолчанию
                    this.currentLanguage = data.current || data.default;
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
                } else {
                    throw new Error('Не удалось загрузить языки');
                }
            })
            .catch(error => {
                console.error('Ошибка загрузки языков:', error);
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
        const selectors = document.querySelectorAll('[data-language-selector]');
        selectors.forEach(selector => {
            if (selector.value !== this.currentLanguage) {
                selector.value = this.currentLanguage;
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
            }
        } catch (error) {
            console.error('Ошибка обновления переводов:', error);
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
                this.updateAllLanguageDisplays();
                
                // НЕ показываем уведомление - оно будет показано в форме настроек
                
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
            console.error('Ошибка сохранения настроек:', error);
            
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
}

// Создаем глобальный экземпляр
window.LanguageManager = new LanguageManager();

// Автоматическое обновление каждые 30 секунд
setInterval(() => {
    if (window.LanguageManager && window.LanguageManager.isLanguagesLoaded()) {
        window.LanguageManager.updateAllLanguageDisplays();
    }
}, 30000); 