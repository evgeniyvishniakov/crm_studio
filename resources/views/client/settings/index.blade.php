@extends('client.layouts.app')

@section('title', 'Настройки')
@section('content')
<div class="dashboard-container">
    <div class="settings-header">
        <h1>Общие настройки</h1>
        <div id="notification"></div>
    </div>
    <div class="dashboard-tabs" style="margin-bottom:28px;">
        <button class="tab-button active" data-tab="profile"><i class="fa fa-user" style="margin-right:8px;"></i>Профиль</button>
        <button class="tab-button" data-tab="security"><i class="fa fa-shield-alt" style="margin-right:8px;"></i>Безопасность</button>
        <button class="tab-button" data-tab="notifications"><i class="fa fa-bell" style="margin-right:8px;"></i>Уведомления</button>
        <button class="tab-button" data-tab="language"><i class="fa fa-globe" style="margin-right:8px;"></i>Язык и Валюта</button>
        <button class="tab-button" data-tab="subscription"><i class="fa fa-credit-card" style="margin-right:8px;"></i>Подписки</button>
        <button class="tab-button" data-tab="delete"><i class="fa fa-trash" style="margin-right:8px;"></i>Удаление</button>
    </div>
    <div class="settings-content">
        <!-- Профиль -->
        <div class="settings-pane" id="tab-profile">
            <form method="POST" action="{{ route('client.settings.update') }}" enctype="multipart/form-data">
                @csrf
                <h5>Профиль</h5>
                <div class="form-row form-row--2col">
                    <div class="form-col">
                        <div class="form-group mb-3">
                            <label>Имя</label>
                            <input type="text" name="name" class="form-control" value="{{ old('name', $project->name ?? '') }}">
                        </div>
                    </div>
                    <div class="form-col">
                        <div class="form-group mb-3">
                            <label>Название проекта</label>
                            <input type="text" name="project_name" class="form-control" value="{{ old('project_name', $project->project_name ?? '') }}">
                        </div>
                    </div>
                </div>
                <div class="form-row form-row--2col">
                    <div class="form-col">
                        <div class="form-group mb-3">
                            <label>Email</label>
                            <input type="email" name="email" class="form-control" value="{{ old('email', $project->email ?? '') }}" readonly>
                        </div>
                    </div>
                    <div class="form-col">
                        <div class="form-group mb-3">
                            <label>Телефон</label>
                            <input type="text" name="phone" class="form-control" value="{{ old('phone', $project->phone ?? '') }}">
                        </div>
                    </div>
                </div>
                <div class="form-row form-row--2col">
                    <div class="form-col">
                        <div class="form-group mb-3">
                            <label>Адрес</label>
                            <input type="text" name="address" class="form-control" value="{{ old('address', $project->address ?? '') }}">
                        </div>
                    </div>
                    <div class="form-col">
                        <div class="form-group mb-3">
                            <label>Веб-сайт</label>
                            <input type="url" name="website" class="form-control" value="{{ old('website', $project->website ?? '') }}">
                        </div>
                    </div>
                </div>
                <div class="form-row form-row--2col">
                    <div class="form-col">
                        <div class="form-group mb-3">
                            <label>Instagram</label>
                            <input type="url" name="instagram" class="form-control" value="{{ old('instagram', $project->instagram ?? '') }}" placeholder="https://instagram.com/yourpage">
                        </div>
                    </div>
                    <div class="form-col">
                        <div class="form-group mb-3">
                            <label>Facebook</label>
                            <input type="url" name="facebook" class="form-control" value="{{ old('facebook', $project->facebook ?? '') }}" placeholder="https://facebook.com/yourpage">
                        </div>
                    </div>
                </div>
                <div class="form-row form-row--2col">
                    <div class="form-col">
                        <div class="form-group mb-3">
                            <label>TikTok</label>
                            <input type="url" name="tiktok" class="form-control" value="{{ old('tiktok', $project->tiktok ?? '') }}" placeholder="https://tiktok.com/@yourpage">
                        </div>
                    </div>
                    <div class="form-col"></div>
                </div>
                <div class="form-row">
                    <div class="form-group mb-4">
                        <label>Логотип компании или проекта</label>
                        <div class="logo-upload-row">
                            <div class="logo-preview">
                                @if(!empty($project->logo))
                                    <img src="{{ $project->logo }}" alt="logo">
                                @else
                                    <div class="logo-placeholder">?</div>
                                @endif
                            </div>
                            <div class="logo-upload-controls">
                                <label for="logo-input" class="btn btn-outline-secondary" style="cursor:pointer;display:inline-block;">Выбрать файл</label>
                                <input type="file" id="logo-input" name="logo" accept="image/*" style="display:none;" onchange="document.getElementById('logo-filename').textContent = this.files[0]?.name || ''">
                                <span id="logo-filename" style="margin-left:12px;font-size:0.95em;color:#888;"></span>
                                <small class="form-text text-muted">PNG/JPG, до 2 МБ, квадратное изображение</small>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="form-row">
                    <div class="form-group mb-4">
                        <label>Дата регистрации</label>
                        <input type="text" class="form-control" value="{{ $project->registered_at ? \Carbon\Carbon::parse($project->registered_at)->format('d.m.Y H:i') : ($project->created_at ? \Carbon\Carbon::parse($project->created_at)->format('d.m.Y H:i') : '') }}" disabled style="max-width:220px; display:inline-block;">
                    </div>
                </div>
                <button type="submit" class="btn btn-primary">Сохранить</button>
            </form>
        </div>
        <!-- Безопасность -->
        <div class="settings-pane" id="tab-security" style="display:none;">
            <h5>Смена пароля</h5>
            <a href="{{ route('password.request') }}" class="btn btn-primary mb-4">Забыли пароль?</a>
            <hr>
            <!-- Смена почты -->
            <form method="POST" action="{{ route('client.security.email') }}" class="mb-4" id="change-email-form">
                @csrf
                <h5>Смена почты</h5>
                <div class="form-row form-row--2col">
                    <div class="form-col">
                        <div class="form-group mb-3">
                            <label>Новый email</label>
                            <input type="email" name="new_email" class="form-control" required>
                        </div>
                    </div>
                    <div class="form-col">
                        <div class="form-group mb-3">
                            <label>Текущий пароль</label>
                            <input type="password" name="current_password" class="form-control" required>
                        </div>
                    </div>
                </div>
                <button type="submit" class="btn btn-primary">Изменить email</button>
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
                window.showNotification('success', 'На новую почту отправлено письмо для подтверждения. Пожалуйста, проверьте ваш email.');
            })
            .catch(function(error) {
                var msg = 'Ошибка при отправке. Попробуйте ещё раз.';
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
                <h5>Двухфакторная аутентификация (2FA)</h5>
                <p>Для повышения безопасности вы можете включить двухфакторную аутентификацию через приложение Google Authenticator или аналогичное.</p>
                <form method="POST" action="{{ route('client.security.2fa.enable') }}" style="display:inline-block;">
                    @csrf
                    <button type="submit" class="btn btn-outline-primary">Включить 2FA</button>
                </form>
                <form method="POST" action="{{ route('client.security.2fa.disable') }}" style="display:inline-block;margin-left:10px;">
                    @csrf
                    <button type="submit" class="btn btn-outline-danger">Отключить 2FA</button>
                </form>
            </div>
        </div>
        <!-- Уведомления -->
        <div class="settings-pane" id="tab-notifications" style="display:none;">
            <form>
                <h5>Уведомления</h5>
                <div class="form-check mb-3">
                    <input class="form-check-input" type="checkbox" id="notif1" checked>
                    <label class="form-check-label" for="notif1">Получать email-уведомления о новых записях</label>
                </div>
                <div class="form-check mb-4">
                    <input class="form-check-input" type="checkbox" id="notif2">
                    <label class="form-check-label" for="notif2">Получать напоминания о предстоящих визитах</label>
                </div>
                <button type="submit" class="btn btn-primary">Сохранить</button>
            </form>
        </div>
        <!-- Язык и Валюта -->
        <div class="settings-pane" id="tab-language" style="display:none;">
            <form>
                <h5>Язык и Валюта</h5>
                <div class="form-row form-row--2col">
                    <div class="form-col">
                        <div class="form-group mb-4">
                            <label>Язык интерфейса</label>
                            <select class="form-control" name="language">
                                <option value="ru" selected>Русский</option>
                                <option value="en">Украинский</option>
                                <option value="en">English</option>
                            </select>
                        </div>
                    </div>
                    <div class="form-col">
                        <div class="form-group mb-4">
                            <label>Валюта</label>
                            <select class="form-control" name="currency">
                                <option value="UAH" selected>UAH (₴)</option>
                                <option value="USD">USD ($)</option>
                                <option value="EUR">EUR (€)</option>
                            </select>
                        </div>
                    </div>
                </div>
                <button type="submit" class="btn btn-primary">Сохранить</button>
            </form>
        </div>
        <!-- Подписки -->
        <div class="settings-pane" id="tab-subscription" style="display:none;">
            <h5>Подписки</h5>
            <div class="alert alert-info">
                Здесь будет информация о вашей подписке, тарифе и истории платежей.
            </div>
        </div>
        <!-- Удаление аккаунта -->
        <div class="settings-pane" id="tab-delete" style="display:none;">
            <h5>Удаление аккаунта</h5>
            <div class="alert alert-danger mb-4" style="font-size:1rem;">
                <b>Внимание!</b> Удаление аккаунта необратимо. Все ваши данные будут удалены.
            </div>
            <button class="btn btn-danger">Удалить аккаунт</button>
        </div>
    </div>
</div>
<script>
    // JS для переключения вкладок
    document.querySelectorAll('.dashboard-container .tab-button').forEach(btn => {
        btn.addEventListener('click', function() {
            document.querySelectorAll('.dashboard-container .tab-button').forEach(b => b.classList.remove('active'));
            this.classList.add('active');
            const tab = this.dataset.tab;
            document.querySelectorAll('.dashboard-container .settings-pane').forEach(pane => {
                pane.style.display = pane.id === 'tab-' + tab ? '' : 'none';
            });
        });
    });
</script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    function activateTabFromHash() {
        var hash = window.location.hash.replace('#', '');
        if (!hash) return;
        // Деактивируем все вкладки и панели
        document.querySelectorAll('.dashboard-tabs .tab-button').forEach(btn => btn.classList.remove('active'));
        document.querySelectorAll('.settings-pane').forEach(pane => pane.style.display = 'none');
        // Активируем нужную
        var btn = document.querySelector('.dashboard-tabs .tab-button[data-tab="' + hash + '"]');
        var pane = document.getElementById('tab-' + hash);
        if (btn && pane) {
            btn.classList.add('active');
            pane.style.display = '';
        }
    }
    activateTabFromHash();
    window.addEventListener('hashchange', activateTabFromHash);
    // Меняем hash при клике на вкладку
    document.querySelectorAll('.dashboard-tabs .tab-button').forEach(function(btn) {
        btn.addEventListener('click', function() {
            var tab = btn.getAttribute('data-tab');
            if (tab) {
                window.location.hash = tab;
            }
        });
    });
});
</script>
<script>
    // Добавить обработку вкладки Поддержка
    const supportTab = document.querySelector('.dashboard-tabs .tab-button[data-tab="support"]');
    if (supportTab) {
        supportTab.addEventListener('click', function() {
            document.querySelectorAll('.dashboard-tabs .tab-button').forEach(btn => btn.classList.remove('active'));
            supportTab.classList.add('active');
            document.querySelectorAll('.dashboard-container .settings-pane').forEach(pane => pane.style.display = 'none');
            document.getElementById('tab-support').style.display = '';
        });
    }
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
@endsection 