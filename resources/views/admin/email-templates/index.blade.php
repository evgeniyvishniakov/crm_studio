@extends('admin.layouts.app')

@section('title', 'Email шаблоны - Админ')
@section('page-title', 'Email шаблоны')

@section('content')
<div class="card border-0 shadow-sm">
    <div class="card-header bg-transparent d-flex justify-content-between align-items-center">
        <h5 class="mb-0">Email шаблоны</h5>
        <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#createTemplateModal">
            <i class="fas fa-plus me-2"></i>Добавить шаблон
        </button>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-hover">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Название</th>
                        <th>Тема</th>
                        <th>Тип</th>
                        <th>Статус</th>
                        <th>Действия</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td>1</td>
                        <td>Приветственное письмо</td>
                        <td>Добро пожаловать в CRM Studio</td>
                        <td>Регистрация</td>
                        <td><span class="badge bg-success">Активен</span></td>
                        <td>
                            <div class="btn-group" role="group">
                                <button class="btn btn-sm btn-outline-primary">
                                    <i class="fas fa-edit"></i>
                                </button>
                                <button class="btn btn-sm btn-outline-info">
                                    <i class="fas fa-eye"></i>
                                </button>
                                <button class="btn btn-sm btn-outline-danger">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </div>
                        </td>
                    </tr>
                    <tr>
                        <td>2</td>
                        <td>Напоминание о записи</td>
                        <td>Напоминание о вашей записи</td>
                        <td>Записи</td>
                        <td><span class="badge bg-success">Активен</span></td>
                        <td>
                            <div class="btn-group" role="group">
                                <button class="btn btn-sm btn-outline-primary">
                                    <i class="fas fa-edit"></i>
                                </button>
                                <button class="btn btn-sm btn-outline-info">
                                    <i class="fas fa-eye"></i>
                                </button>
                                <button class="btn btn-sm btn-outline-danger">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </div>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Модальное окно создания шаблона -->
<div class="modal fade" id="createTemplateModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Добавить email шаблон</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form>
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="template_name" class="form-label">Название шаблона</label>
                        <input type="text" class="form-control" id="template_name" required>
                    </div>
                    <div class="mb-3">
                        <label for="template_subject" class="form-label">Тема письма</label>
                        <input type="text" class="form-control" id="template_subject" required>
                    </div>
                    <div class="mb-3">
                        <label for="template_type" class="form-label">Тип шаблона</label>
                        <select class="form-select" id="template_type" required>
                            <option value="">Выберите тип</option>
                            <option value="registration">Регистрация</option>
                            <option value="appointment">Записи</option>
                            <option value="reminder">Напоминания</option>
                            <option value="notification">Уведомления</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="template_content" class="form-label">Содержимое шаблона</label>
                        <textarea class="form-control" id="template_content" rows="10" placeholder="Введите содержимое email шаблона..."></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Отмена</button>
                    <button type="submit" class="btn btn-primary">Создать</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection 