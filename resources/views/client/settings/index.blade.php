@extends('client.layouts.app')

@section('title', __('messages.settings'))
@section('content')

<div class="dashboard-container">
    <div class="settings-header">
        <h1>{{ __('messages.settings') }}</h1>
        <div id="notification"></div>
    </div>
    <div class="dashboard-tabs" style="margin-bottom:28px;">
        <button class="tab-button active" data-tab="profile"><i class="fa fa-user" style="margin-right:8px;"></i>{{ __('messages.profile') }}</button>
        <button class="tab-button" data-tab="security"><i class="fa fa-shield-alt" style="margin-right:8px;"></i>{{ __('messages.security') }}</button>
        <button class="tab-button" data-tab="notifications"><i class="fa fa-bell" style="margin-right:8px;"></i>{{ __('messages.notifications') }}</button>
        <button class="tab-button" data-tab="language"><i class="fa fa-globe" style="margin-right:8px;"></i>{{ __('messages.language_and_currency') }}</button>
        <button class="tab-button" data-tab="subscription"><i class="fa fa-credit-card" style="margin-right:8px;"></i>{{ __('messages.subscription') }}</button>
        <button class="tab-button" data-tab="delete"><i class="fa fa-trash" style="margin-right:8px;"></i>{{ __('messages.delete') }}</button>
    </div>
    <div class="settings-content">
        <!-- Профиль -->
        <div class="settings-pane" id="tab-profile">
            <form method="POST" action="{{ route('client.settings.update') }}" enctype="multipart/form-data">
                @csrf
                <h5>{{ __('messages.profile') }}</h5>
                <div class="form-row form-row--2col">
                    <div class="form-col">
                        <div class="form-group mb-3">
                            <label>{{ __('messages.name') }}</label>
                            <input type="text" name="name" class="form-control" value="{{ old('name', $project->name ?? '') }}">
                        </div>
                    </div>
                    <div class="form-col">
                        <div class="form-group mb-3">
                            <label>{{ __('messages.project_name') }}</label>
                            <input type="text" name="project_name" class="form-control" value="{{ old('project_name', $project->project_name ?? '') }}">
                        </div>
                    </div>
                </div>
                <div class="form-row form-row--2col">
                    <div class="form-col">
                        <div class="form-group mb-3">
                            <label>{{ __('messages.email') }}</label>
                            <input type="email" name="email" class="form-control" value="{{ old('email', $project->email ?? '') }}" readonly>
                        </div>
                    </div>
                    <div class="form-col">
                        <div class="form-group mb-3">
                            <label>{{ __('messages.phone') }}</label>
                            <input type="text" name="phone" class="form-control" value="{{ old('phone', $project->phone ?? '') }}">
                        </div>
                    </div>
                </div>
                <div class="form-row form-row--2col">
                    <div class="form-col">
                        <div class="form-group mb-3">
                            <label>{{ __('messages.address') }}</label>
                            <input type="text" name="address" class="form-control" value="{{ old('address', $project->address ?? '') }}">
                        </div>
                    </div>
                    <div class="form-col">
                        <div class="form-group mb-3">
                            <label>{{ __('messages.website') }}</label>
                            <input type="url" name="website" class="form-control" value="{{ old('website', $project->website ?? '') }}">
                        </div>
                    </div>
                </div>
                <div class="form-row form-row--2col">
                    <div class="form-col">
                        <div class="form-group mb-3">
                            <label>{{ __('messages.instagram') }}</label>
                            <input type="url" name="instagram" class="form-control" value="{{ old('instagram', $project->instagram ?? '') }}" placeholder="https://instagram.com/yourpage">
                        </div>
                    </div>
                    <div class="form-col">
                        <div class="form-group mb-3">
                            <label>{{ __('messages.facebook') }}</label>
                            <input type="url" name="facebook" class="form-control" value="{{ old('facebook', $project->facebook ?? '') }}" placeholder="https://facebook.com/yourpage">
                        </div>
                    </div>
                </div>
                <div class="form-row form-row--2col">
                    <div class="form-col">
                        <div class="form-group mb-3">
                            <label>{{ __('messages.tiktok') }}</label>
                            <input type="url" name="tiktok" class="form-control" value="{{ old('tiktok', $project->tiktok ?? '') }}" placeholder="https://tiktok.com/@yourpage">
                        </div>
                    </div>
                    <div class="form-col"></div>
                </div>

                <!-- Описание и логотип в один ряд -->
                <div class="form-row form-row--2col">
                    <div class="form-col">
                        <div class="form-group mb-4">
                            <label>{{ __('messages.about_us') }}</label>
                            <textarea name="about" class="form-control" rows="4" placeholder="{{ __('messages.about_us_placeholder') }}">{{ old('about', $project->about ?? '') }}</textarea>
                            <small class="form-text text-muted">{{ __('messages.about_us_hint') }}</small>
                        </div>
                    </div>
                    <div class="form-col">
                        <div class="form-group mb-4">
                            <label>{{ __('messages.company_logo') }}</label>
                            <div class="logo-upload-row">
                                <div class="logo-preview">
                                    @if(!empty($project->logo))
                                        <img src="{{ $project->logo }}" alt="logo">
                                    @else
                                        <div class="logo-placeholder">?</div>
                                    @endif
                                </div>
                                <div class="logo-upload-controls">
                                    <label for="logo-input" class="btn btn-outline-secondary" style="cursor:pointer;display:inline-block;">{{ __('messages.select_file') }}</label>
                                    <input type="file" id="logo-input" name="logo" accept="image/*" style="display:none;" onchange="document.getElementById('logo-filename').textContent = this.files[0]?.name || ''">
                                    <span id="logo-filename" style="margin-left:12px;font-size:0.95em;color:#888;"></span>
                                    <small class="form-text text-muted">{{ __('messages.logo_upload_requirements') }}</small>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Поля для карты и описания -->
                <div class="form-row">
                    <div class="form-group mb-4">
                        <h6 style="margin-bottom: 15px; color: #333; font-weight: 600;">
                            <i class="fas fa-map-marker-alt" style="margin-right: 8px; color: #dc3545;"></i>
                            {{ __('messages.map_settings') }}
                        </h6>
                    </div>
                </div>

                <div class="form-row form-row--2col">
                    <div class="form-col">
                        <div class="form-group mb-3">
                            <label>{{ __('messages.map_url') }}</label>
                            <input type="url" name="map_url" class="form-control" id="map_url" 
                                   value="{{ old('map_url', '') }}" 
                                   placeholder="{{ __('messages.map_url_placeholder') }}">
                            <small class="form-text text-muted">
                                {{ __('messages.map_url_help') }}
                            </small>
                        </div>
                    </div>
                    <div class="form-col">
                        <div class="form-group mb-3">
                            <label>{{ __('messages.map_zoom') }}</label>
                            <input type="number" name="map_zoom" class="form-control" id="map_zoom" 
                                   value="{{ old('map_zoom', $project->map_zoom ?? 15) }}" min="1" max="20">
                            <small class="form-text text-muted">{{ __('messages.map_zoom_placeholder') }}</small>
                        </div>
                    </div>
                </div>

                <!-- Скрытые поля для координат -->
                <input type="hidden" name="map_latitude" id="map_latitude" value="{{ old('map_latitude', $project->map_latitude ?? '') }}">
                <input type="hidden" name="map_longitude" id="map_longitude" value="{{ old('map_longitude', $project->map_longitude ?? '') }}">

                <!-- Предварительный просмотр карты -->
                <div class="form-row">
                    <div class="form-group mb-4">
                        <label>{{ __('messages.map_preview') }}</label>
                        <div id="map_preview" style="width: 100%; height: 300px; border: 1px solid #ddd; border-radius: 8px; background: #f8f9fa; display: flex; align-items: center; justify-content: center;">
                            <div class="text-center text-muted">
                                <i class="fas fa-map fa-3x mb-3"></i>
                                <p>{{ __('messages.map_preview_placeholder') }}</p>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="form-row">
                    <div class="form-group mb-4">
                        <label>{{ __('messages.registration_date') }}</label>
                        <input type="text" class="form-control" value="{{ $project->registered_at ? \Carbon\Carbon::parse($project->registered_at)->format('d.m.Y H:i') : ($project->created_at ? \Carbon\Carbon::parse($project->created_at)->format('d.m.Y H:i') : '') }}" disabled style="max-width:220px; display:inline-block;">
                    </div>
                </div>
                <button type="submit" class="btn btn-primary">{{ __('messages.save') }}</button>
            </form>
        </div>
        <!-- Безопасность -->
        <div class="settings-pane" id="tab-security" style="display:none;">
            <h5>{{ __('messages.change_password') }}</h5>
            <a href="{{ route('password.request') }}" class="btn btn-primary mb-4">{{ __('messages.forgot_password') }}</a>
            <hr>
            <!-- Смена почты -->
            <form method="POST" action="{{ route('client.security.email') }}" class="mb-4" id="change-email-form">
                @csrf
                <h5>{{ __('messages.change_email') }}</h5>
                <div class="form-row form-row--2col">
                    <div class="form-col">
                        <div class="form-group mb-3">
                            <label>{{ __('messages.new_email') }}</label>
                            <input type="email" name="new_email" class="form-control" required>
                        </div>
                    </div>
                    <div class="form-col">
                        <div class="form-group mb-3">
                            <label>{{ __('messages.current_password') }}</label>
                            <input type="password" name="current_password" class="form-control" required>
                        </div>
                    </div>
                </div>
                <button type="submit" class="btn btn-primary">{{ __('messages.change_email') }}</button>
                <div id="change-email-notification" style="margin-top:16px;"></div>
            </form>
            <script>
document.addEventListener('DOMContentLoaded', function() {
    var form = document.getElementById('change-email-form');
    if (form) {
        form.addEventListener('submit', function(e) {
            e.preventDefault();
            var formData = new FormData(form);
            fetch(form.action, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                    'Accept': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest'
                },
                body: formData
            })
            .then(function(response) {
                if (response.ok) return response.json();
                return response.json().then(function(data) { throw data; });
            })
            .then(function(data) {
                form.reset();
                window.showNotification('success', '{{ __('messages.email_confirmation_sent') }}');
            })
            .catch(function(error) {
                var msg = '{{ __('messages.send_error') }}';
                if (error && error.errors) {
                    msg = Object.values(error.errors).join('<br>');
                }
                window.showNotification('error', msg);
            });
        });
    }
});
            </script>
            <hr>
            <!-- Двухфакторная аутентификация -->
            <div class="mb-4">
                <h5>{{ __('messages.two_factor_auth') }}</h5>
                <p>{{ __('messages.two_factor_auth_desc') }}</p>
                <form method="POST" action="{{ route('client.security.2fa.enable') }}" style="display:inline-block;">
                    @csrf
                    <button type="submit" class="btn btn-outline-primary">{{ __('messages.enable_2fa') }}</button>
                </form>
                <form method="POST" action="{{ route('client.security.2fa.disable') }}" style="display:inline-block;margin-left:10px;">
                    @csrf
                    <button type="submit" class="btn btn-outline-danger">{{ __('messages.disable_2fa') }}</button>
                </form>
            </div>
        </div>
        <!-- Уведомления -->
        <div class="settings-pane" id="tab-notifications" style="display:none;">
            <form>
                <h5>{{ __('messages.notifications') }}</h5>
                <div class="form-check mb-3">
                    <input class="form-check-input" type="checkbox" id="notif1" checked>
                    <label class="form-check-label" for="notif1">{{ __('messages.get_email_notifications') }}</label>
                </div>
                <div class="form-check mb-4">
                    <input class="form-check-input" type="checkbox" id="notif2">
                    <label class="form-check-label" for="notif2">{{ __('messages.get_appointment_reminders') }}</label>
                </div>
                <button type="submit" class="btn btn-primary">{{ __('messages.save') }}</button>
            </form>
        </div>
        <!-- Язык и Валюта -->
        <div class="settings-pane" id="tab-language" style="display:none;">
            <form id="language-currency-form">
                <h5>{{ __('messages.language_and_currency') }}</h5>
                <div class="form-row form-row--3col">
                    <div class="form-col">
                        <div class="form-group mb-4">
                            <label>{{ __('messages.interface_language') }}</label>
                            <select class="form-control" name="language_id" data-language-selector>
                                @php
                                    use App\Models\Language;
                                    $languages = Language::where('is_active', true)->get();
                                    if ($languages->isEmpty()) {
                                        // Fallback если языки не загружены
                                        $languages = collect([
                                            (object)['id' => 1, 'name' => 'Русский', 'native_name' => 'Русский'],
                                            (object)['id' => 2, 'name' => 'English', 'native_name' => 'English'],
                                            (object)['id' => 3, 'name' => 'Українська', 'native_name' => 'Українська']
                                        ]);
                                    }
                                    
                                    // Определяем выбранный язык
                                    $selectedLanguageId = $project->language_id ?? 1;
                                    

                                @endphp
                                @foreach($languages as $language)
                                    @php
                                        $isSelected = $selectedLanguageId == $language->id;
                                    @endphp
                                    <option value="{{ $language->id }}" {{ $isSelected ? 'selected="selected"' : '' }}>
                                        {{ $language->name }} ({{ $language->native_name }})
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="form-col">
                        <div class="form-group mb-4">
                            <label>{{ __('messages.web_booking_language') }}</label>
                            <select class="form-control" name="booking_language_id">
                                @php
                                    // Определяем выбранный язык веб-записи
                                    $selectedBookingLanguageId = $project->booking_language_id ?? $project->language_id ?? 1;
                                @endphp
                                @foreach($languages as $language)
                                    @php
                                        $isSelected = $selectedBookingLanguageId == $language->id;
                                    @endphp
                                    <option value="{{ $language->id }}" {{ $isSelected ? 'selected="selected"' : '' }}>
                                        {{ $language->name }} ({{ $language->native_name }})
                                    </option>
                                @endforeach
                            </select>
                            <small class="form-text text-muted">{{ __('messages.web_booking_language_hint') }}</small>
                        </div>
                    </div>
                    <div class="form-col">
                        <div class="form-group mb-4">
                            <label>{{ __('messages.currency') }}</label>
                            <select class="form-control" name="currency_id" id="currency-selector">
                                @php
                                    $currencies = \App\Models\Currency::where('is_active', true)->get();
                                    if ($currencies->isEmpty()) {
                                        // Fallback если валюты не загружены
                                        $currencies = collect([
                                            (object)['id' => 1, 'code' => 'UAH', 'symbol' => '₴'],
                                            (object)['id' => 2, 'code' => 'USD', 'symbol' => '$'],
                                            (object)['id' => 3, 'code' => 'EUR', 'symbol' => '€']
                                        ]);
                                    }
                                    
                                    // Определяем выбранную валюту
                                    $selectedCurrencyId = $project->currency_id ?? 1;
                                @endphp
                                @foreach($currencies as $currency)
                                    @php
                                        $isSelected = $selectedCurrencyId == $currency->id;
                                    @endphp
                                    <option value="{{ $currency->id }}" {{ $isSelected ? 'selected' : '' }}>
                                        {{ $currency->code }} ({{ $currency->symbol }})
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                </div>
                <button type="submit" class="btn btn-primary">{{ __('messages.save') }}</button>
                
                <script>
                // Обработчик формы языка и валюты
                document.getElementById('language-currency-form').addEventListener('submit', async function(e) {
                    e.preventDefault(); // Предотвращаем обычную отправку формы
                    
                    const formData = new FormData(this);
                    const languageId = formData.get('language_id');
                    const bookingLanguageId = formData.get('booking_language_id');
                    const currencyId = formData.get('currency_id');
                    
                    // Показываем индикатор загрузки
                    const submitButton = this.querySelector('button[type="submit"]');
                    const originalText = submitButton.textContent;
                    submitButton.textContent = '{{ __('messages.saving') }}';
                    submitButton.disabled = true;
                    
                    try {
                        // Отправляем запрос на сервер
                        const response = await fetch('/api/settings/update', {
                            method: 'POST',
                            headers: {
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content || '',
                                'Accept': 'application/json',
                                'Content-Type': 'application/json',
                            },
                            body: JSON.stringify({
                                language_id: languageId,
                                booking_language_id: bookingLanguageId,
                                currency_id: currencyId
                            })
                        });
                        
                        const data = await response.json();
                        
                        if (data.success) {
                            // Показываем уведомление об успешном сохранении
                            // if (window.showNotification) {
                            //     let message = 'Настройки успешно сохранены!';
                            //     if (data.language && data.currency) {
                            //         message = `Язык изменен на ${data.language.name}, валюта изменена на ${data.currency.code}`;
                            //     } else if (data.language) {
                            //         message = `Язык изменен на ${data.language.name}`;
                            //     } else if (data.currency) {
                            //         message = `Валюта изменена на ${data.currency.code}`;
                            //     }
                            //     window.showNotification('success', message);
                            // }
                            
                            // Обновляем менеджеры
                            if (window.LanguageManager) {
                                window.LanguageManager.refresh();
                            }
                            if (window.CurrencyManager) {
                                window.CurrencyManager.refresh();
                            }
                            
                            // НЕ перезагружаем страницу - просто обновляем селекторы
                            
                        } else {
                            throw new Error(data.message || 'Ошибка сохранения');
                        }
                        
                    } catch (error) {
                        
                        // Используем существующую систему уведомлений
                        if (window.showNotification) {
                            window.showNotification('error', '{{ __('messages.error_saving_settings') }}: ' + error.message);
                        }
                    } finally {
                        // Восстанавливаем кнопку
                        submitButton.textContent = originalText;
                        submitButton.disabled = false;
                    }
                });
                
                // Отключаем автоматическое обновление селекторов менеджерами
                document.addEventListener('DOMContentLoaded', function() {
                    // Отключаем обновление селекторов в LanguageManager
                    if (window.LanguageManager) {
                        const originalUpdateLanguageSelectors = window.LanguageManager.updateLanguageSelectors;
                        window.LanguageManager.updateLanguageSelectors = function() {
                            return;
                        };
                    }
                    
                    // Отключаем обновление селекторов в CurrencyManager
                    if (window.CurrencyManager) {
                        const originalUpdateCurrencySelectors = window.CurrencyManager.updateCurrencySelectors;
                        window.CurrencyManager.updateCurrencySelectors = function() {
                            return;
                        };
                    }
                });
                </script>
            </form>
        </div>
        <!-- Подписки -->
        <div class="settings-pane" id="tab-subscription" style="display:none;">
            <h5>{{ __('messages.subscription') }}</h5>
            <div class="alert alert-info">
                {{ __('messages.subscription_info') }}
            </div>
        </div>
        <!-- Удаление аккаунта -->
        <div class="settings-pane" id="tab-delete" style="display:none;">
            <h5>{{ __('messages.delete_account') }}</h5>
            <div class="alert alert-danger mb-4" style="font-size:1rem;">
                <b>{{ __('messages.warning') }}!</b> {{ __('messages.delete_account_warning') }}
            </div>
            <button class="btn btn-danger">{{ __('messages.delete_account_button') }}</button>
        </div>
    </div>
</div>
<script>
    // Единый скрипт для управления вкладками
    function switchTab(tabName) {
        // Убираем активный класс со всех кнопок
        document.querySelectorAll('.dashboard-container .tab-button').forEach(btn => {
            btn.classList.remove('active');
        });
        
        // Скрываем все панели
        document.querySelectorAll('.dashboard-container .settings-pane').forEach(pane => {
            pane.style.display = 'none';
        });
        
        // Активируем нужную кнопку
        const activeButton = document.querySelector(`.dashboard-container .tab-button[data-tab="${tabName}"]`);
        if (activeButton) {
            activeButton.classList.add('active');
        }
        
        // Показываем нужную панель
        const activePane = document.getElementById(`tab-${tabName}`);
        if (activePane) {
            activePane.style.display = '';
        }
        
        // Обновляем hash в URL
        window.location.hash = tabName;
    }
    
    // Добавляем обработчики для всех кнопок вкладок
    document.querySelectorAll('.dashboard-container .tab-button').forEach(btn => {
        btn.addEventListener('click', function() {
            const tabName = this.dataset.tab;
            switchTab(tabName);
        });
    });
</script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    
    // Инициализация: показываем вкладку из hash или профиль по умолчанию
    const hash = window.location.hash.replace('#', '');
    if (hash) {
        switchTab(hash);
    } else {
        // По умолчанию показываем профиль
        switchTab('profile');
    }
    
    // Обработчик изменения hash
    window.addEventListener('hashchange', function() {
        const newHash = window.location.hash.replace('#', '');
        if (newHash) {
            switchTab(newHash);
        }
    });
});
</script>


<style>
.settings-accordion { margin-top: 32px; }
.accordion-item { border: 1px solid #e5e7eb; border-radius: 8px; margin-bottom: 12px; background: #fff; }
.accordion-header { padding: 14px 20px; font-weight: 600; cursor: pointer; display: flex; align-items: center; gap: 10px; }
.accordion-body { display: none; padding: 18px 24px; border-top: 1px solid #e5e7eb; }
.accordion-body.open { display: block; }
.accordion-header i { color: #3b82f6; }
</style>
<script>
document.querySelectorAll('.settings-accordion .accordion-header').forEach(header => {
    header.addEventListener('click', function() {
        const body = this.nextElementSibling;
        body.classList.toggle('open');
    });
});
</script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Обработка формы языка и валюты
    var languageCurrencyForm = document.getElementById('language-currency-form');
    if (languageCurrencyForm) {
        languageCurrencyForm.addEventListener('submit', function(e) {
            e.preventDefault();
            var formData = new FormData(languageCurrencyForm);
            fetch('{{ route("client.settings.update-language-currency") }}', {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                    'Accept': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest'
                },
                body: formData
            })
            .then(function(response) {
                if (response.ok) return response.json();
                return response.json().then(function(data) { throw data; });
            })
            .then(function(data) {
                window.showNotification('success', data.message || '{{ __('messages.language_currency_settings_saved') }}');
                
                // Обновляем валюту глобально после успешного сохранения
                                    if (window.CurrencyManager && data.currency_code) {
                        // Используем данные из сервера
                        const currencyCode = data.currency_code;
                        const currencyId = data.currency_id;
                        
                        // Обновляем CurrencyManager
                        window.CurrencyManager.currentCurrency = currencyCode;
                        window.CurrencyManager.updateAllCurrencyDisplays();
                    
                    // Обновляем селектор, чтобы показать выбранную валюту
                    setTimeout(() => {
                        const currencySelector = document.getElementById('currency-selector');
                        if (currencySelector) {
                            currencySelector.value = currencyId;
                            
                            // Принудительно обновляем отображение селектора
                            currencySelector.dispatchEvent(new Event('change', { bubbles: true }));
                            
                            // Также обновляем выбранную опцию
                            const selectedOption = currencySelector.options[currencySelector.selectedIndex];
                            
                            // Альтернативный способ обновления через jQuery (если доступен)
                            if (typeof $ !== 'undefined') {
                                $('#currency-selector').val(currencyId).trigger('change');
                            }
                            
                            // Еще один способ - через прямое обновление опций
                            Array.from(currencySelector.options).forEach(option => {
                                option.selected = (option.value == currencyId);
                            });
                        }
                    }, 100);
                }
            })
            .catch(function(error) {
                var msg = '{{ __('messages.error_saving_try_again') }}';
                if (error && error.errors) {
                    if (typeof error.errors === 'object') {
                        msg = Object.values(error.errors).flat().join('<br>');
                    } else {
                        msg = error.errors;
                    }
                } else if (error && error.message) {
                    msg = error.message;
                }
                window.showNotification('error', msg);
            });
        });
    }

    // Обработчик изменения валюты в селекторе
    const currencySelector = document.getElementById('currency-selector');
    if (currencySelector) {
        // Убираем автоматическое сохранение при изменении селектора
        // Теперь валюта будет сохраняться только при нажатии кнопки "Сохранить"
    }

    // Обработка карты
    const mapUrlInput = document.getElementById('map_url');
    const mapPreview = document.getElementById('map_preview');
    const mapLatitudeInput = document.getElementById('map_latitude');
    const mapLongitudeInput = document.getElementById('map_longitude');
    const mapZoomInput = document.getElementById('map_zoom');

    if (mapUrlInput) {
        mapUrlInput.addEventListener('input', function() {
            const url = this.value.trim();
            if (url) {
                extractCoordinatesFromUrl(url);
            } else {
                showMapPlaceholder();
            }
        });

        // Инициализация при загрузке страницы
        if (mapLatitudeInput.value && mapLongitudeInput.value) {
            showMapPreview(mapLatitudeInput.value, mapLongitudeInput.value, mapZoomInput.value);
        }
    }

    function extractCoordinatesFromUrl(url) {
        // Формат: https://maps.app.goo.gl/UMeU52GP5ZWVxx4x5
        if (url.includes('maps.app.goo.gl/')) {
            showMapPlaceholder('Короткие ссылки Google Maps пока не поддерживаются. Используйте полную ссылку.');
            return;
        }
        
        // Формат: https://www.google.com/maps?q=55.7558,37.6176
        let match = url.match(/[?&]q=([^&]+)/);
        if (match) {
            const coords = match[1].split(',');
            if (coords.length >= 2) {
                const lat = parseFloat(coords[0]);
                const lng = parseFloat(coords[1]);
                if (!isNaN(lat) && !isNaN(lng)) {
                    updateCoordinates(lat, lng, 15);
                    showMapPreview(lat, lng, 15);
                    return;
                }
            }
        }
        
        // Формат: https://www.google.com/maps/place/.../@55.7558,37.6176,15z
        match = url.match(/@([^,]+),([^,]+),(\d+)z/);
        if (match) {
            const lat = parseFloat(match[1]);
            const lng = parseFloat(match[2]);
            const zoom = parseInt(match[3]);
            if (!isNaN(lat) && !isNaN(lng)) {
                updateCoordinates(lat, lng, zoom);
                showMapPreview(lat, lng, zoom);
                return;
            }
        }
        
        // Формат: https://www.google.com/maps?ll=55.7558,37.6176&z=15
        match = url.match(/[?&]ll=([^&]+)/);
        if (match) {
            const coords = match[1].split(',');
            if (coords.length >= 2) {
                const lat = parseFloat(coords[0]);
                const lng = parseFloat(coords[1]);
                let zoom = 15;
                
                const zoomMatch = url.match(/[?&]z=(\d+)/);
                if (zoomMatch) {
                    zoom = parseInt(zoomMatch[1]);
                }
                
                if (!isNaN(lat) && !isNaN(lng)) {
                    updateCoordinates(lat, lng, zoom);
                    showMapPreview(lat, lng, zoom);
                    return;
                }
            }
        }
        
        showMapPlaceholder('Не удалось извлечь координаты из ссылки. Проверьте формат ссылки.');
    }

    function updateCoordinates(lat, lng, zoom) {
        mapLatitudeInput.value = lat;
        mapLongitudeInput.value = lng;
        mapZoomInput.value = zoom;
    }

    function showMapPreview(lat, lng, zoom) {
        const embedUrl = `https://maps.google.com/maps?q=${lat},${lng}&z=${zoom}&output=embed`;
        mapPreview.innerHTML = `
            <iframe 
                width="100%" 
                height="100%" 
                frameborder="0" 
                scrolling="no" 
                marginheight="0" 
                marginwidth="0"
                src="${embedUrl}"
                style="border: none; border-radius: 8px;">
            </iframe>
        `;
    }

    function showMapPlaceholder(message = 'Вставьте ссылку на Google Maps для предварительного просмотра') {
        mapPreview.innerHTML = `
            <div class="text-center text-muted">
                <i class="fas fa-map fa-3x mb-3"></i>
                <p>${message}</p>
            </div>
        `;
    }
});
</script>
@endsection 