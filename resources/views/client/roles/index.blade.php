@extends('client.layouts.app')

@section('content')
<div class="dashboard-container">
    <div class="clients-header">
        <h1>Роли и Доступы</h1>
        <div class="header-actions">
            <button class="btn-add-client" id="btnAddRole">
                <svg class="icon" viewBox="0 0 20 20" fill="currentColor">
                    <path fill-rule="evenodd" d="M10 3a1 1 0 011 1v5h5a1 1 0 110 2h-5v5a1 1 0 11-2 0v-5H4a1 1 0 110-2h5V4a1 1 0 011-1z" clip-rule="evenodd" />
                </svg>
                Добавить роль
            </button>
        </div>
    </div>
    <div class="table-wrapper">
        <!-- Здесь будет таблица ролей и доступов -->
        <div style="text-align:center; color:#888; padding:40px 0;">Пока нет данных. Добавьте первую роль.</div>
    </div>
</div>
<!-- Модальное окно для добавления/редактирования роли -->
<div id="roleModal" class="modal" style="display:none;">
    <div class="modal-content">
        <div class="modal-header">
            <h2 id="roleModalTitle">Добавить роль</h2>
            <span class="close" onclick="closeRoleModal()">&times;</span>
        </div>
        <div class="modal-body">
            <form id="roleForm">
                @csrf
                <div class="form-group">
                    <label for="roleSelect">Роль</label>
                    <select id="roleSelect" name="role" required>
                        @foreach($roles as $key => $label)
                            <option value="{{ $key }}">{{ $label }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="form-group">
                    <label>Доступы</label>
                    <div class="permissions-list">
                        
                            <label><input type="checkbox" name="permissions[]" value="dashboard"> <span>Панель управления (дешборд)</span></label>
                            <label><input type="checkbox" name="permissions[]" value="clients"> <span>Клиенты</span></label>
                            <label><input type="checkbox" name="permissions[]" value="appointments"> <span>Записи</span></label>
                            <label><input type="checkbox" name="permissions[]" value="analytics"> <span>Аналитика</span></label>
                            <label><input type="checkbox" name="permissions[]" value="warehouse"> <span>Склад</span></label>
                            <label><input type="checkbox" name="permissions[]" value="purchases"> <span>Закупки</span></label>
                            <label><input type="checkbox" name="permissions[]" value="sales"> <span>Продажи</span></label>
                            <label><input type="checkbox" name="permissions[]" value="expenses"> <span>Расходы</span></label>
                            <label><input type="checkbox" name="permissions[]" value="inventory"> <span>Инвентаризация</span></label>
                            <label><input type="checkbox" name="permissions[]" value="reference"> <span>Справочник</span></label>
                            <label><input type="checkbox" name="permissions[]" value="settings"> <span>Настройки</span></label>
                    </div>
                </div>
                <div class="form-actions">
                    <button type="button" class="btn-cancel" onclick="closeRoleModal()">Отмена</button>
                    <button type="submit" class="btn-submit">Сохранить</button>
                </div>
            </form>
        </div>
    </div>
</div>
<script>
function openRoleModal() {
    document.getElementById('roleModal').style.display = 'block';
}
function closeRoleModal() {
    document.getElementById('roleModal').style.display = 'none';
}
document.getElementById('btnAddRole').onclick = openRoleModal;
</script>
<style>
    .permissions-list {
        display: flex;
        flex-direction: column;
        gap: 8px;
        align-items: flex-start;
    }
    .permissions-list label {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        margin-bottom: 0;
        white-space: nowrap;
        font-size: 16px;
    }
    .permissions-list span {
        white-space: nowrap;
    }
</style>
@endsection 