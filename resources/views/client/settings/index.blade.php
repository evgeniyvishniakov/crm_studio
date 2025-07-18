@extends('client.layouts.app')

@section('title', 'Настройки')
@section('content')
<div class="dashboard-container">
    <div class="settings-header">
        <h1>Общие настройки</h1>
        <div id="notification"></div>
    </div>
    <div class="dashboard-tabs" style="margin-bottom:28px;">
        <button class="tab-button active" data-tab="profile">Профиль</button>
        <button class="tab-button" data-tab="security">Безопасность</button>
        <button class="tab-button" data-tab="notifications">Уведомления</button>
        <button class="tab-button" data-tab="language">Язык</button>
        <button class="tab-button" data-tab="delete">Удаление</button>
    </div>
    <div class="settings-content">
        <!-- Профиль -->
        <div class="settings-pane" id="tab-profile">
            <form method="POST" action="{{ route('client.settings.update') }}" enctype="multipart/form-data">
                @csrf
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
                            <input type="email" name="email" class="form-control" value="{{ old('email', $project->email ?? '') }}">
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
                            <label>Социальные сети</label>
                            <input type="text" name="social_links" class="form-control" value="{{ old('social_links', $project->social_links ?? '') }}" placeholder="Ссылки через запятую или JSON">
                        </div>
                    </div>
                    <div class="form-col"></div>
                </div>
                <div class="form-row">
                    <div class="form-group mb-4">
                        <label>Логотип</label><br>
                        @if(!empty($project->logo))
                            <img src="{{ $project->logo }}" alt="logo" style="width:64px;height:64px;border-radius:50%;object-fit:cover;">
                        @endif
                        <input type="file" name="logo" class="form-control mt-2" style="max-width:300px;">
                    </div>
                </div>
                <div class="form-row">
                    <div class="form-group mb-4">
                        <label>Дата регистрации</label>
                        <input type="text" class="form-control" value="{{ $project->registered_at ?? ($project->created_at ?? '') }}" disabled style="max-width:220px; display:inline-block;">
                    </div>
                </div>
                <button type="submit" class="btn btn-primary">Сохранить</button>
            </form>
        </div>
        <!-- Безопасность -->
        <div class="settings-pane" id="tab-security" style="display:none;">
            <form>
                <div class="form-group mb-3">
                    <label>Текущий пароль</label>
                    <input type="password" class="form-control">
                </div>
                <div class="form-group mb-3">
                    <label>Новый пароль</label>
                    <input type="password" class="form-control">
                </div>
                <div class="form-group mb-4">
                    <label>Подтвердите новый пароль</label>
                    <input type="password" class="form-control">
                </div>
                <div class="form-group mb-4">
                    <label>Двухфакторная аутентификация</label><br>
                    <button type="button" class="btn btn-outline-secondary">Включить 2FA</button>
                </div>
                <button type="submit" class="btn btn-primary">Сменить пароль</button>
            </form>
        </div>
        <!-- Уведомления -->
        <div class="settings-pane" id="tab-notifications" style="display:none;">
            <form>
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
        <!-- Язык -->
        <div class="settings-pane" id="tab-language" style="display:none;">
            <form>
                <div class="form-group mb-4">
                    <label>Язык интерфейса</label>
                    <select class="form-control">
                        <option value="ru" selected>Русский</option>
                        <option value="en">English</option>
                    </select>
                </div>
                <button type="submit" class="btn btn-primary">Сохранить</button>
            </form>
        </div>
        <!-- Удаление аккаунта -->
        <div class="settings-pane" id="tab-delete" style="display:none;">
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
@endsection 