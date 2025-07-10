@extends('admin.layouts.app')

@section('title', 'Управление клиентами - Админ')
@section('page-title', 'Управление клиентами')

@section('content')
<div class="card border-0 shadow-sm">
    <div class="card-header bg-transparent d-flex justify-content-between align-items-center">
        <h5 class="mb-0">Список клиентов</h5>
        <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#createUserModal">
            <i class="fas fa-plus me-2"></i>Добавить клиента
        </button>
    </div>
    
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Название проекта</th>
                            <th>Email администратора</th>
                            <th>Дата регистрации</th>
                            <th>Статус</th>
                            <th>Действия</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td colspan="6" class="text-center text-muted">Нет данных для отображения</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
</div>
@endsection 