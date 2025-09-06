@extends('admin.layouts.app')

@section('title', 'Категории блога - Админ панель')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h1 class="h3 mb-0">Категории блога</h1>
                <a href="{{ route('admin.blog-categories.create') }}" class="btn btn-primary">
                    <i class="fas fa-plus me-2"></i>Добавить категорию
                </a>
            </div>

            @if(session('success'))
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    {{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif

            @if(session('error'))
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    {{ session('error') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif

            <!-- Таблица категорий -->
            <div class="card">
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>Название</th>
                                    <th>Цвет</th>
                                    <th>Статус</th>
                                    <th>Порядок</th>
                                    <th>Статей</th>
                                    <th>Переводы</th>
                                    <th>Действия</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($categories as $category)
                                    <tr>
                                        <td>
                                            <div class="d-flex align-items-center">
                                                <span class="badge me-2" style="background-color: {{ $category->color }}; width: 20px; height: 20px; border-radius: 50%;"></span>
                                                <strong>{{ $category->name }}</strong>
                                            </div>
                                            @if($category->description)
                                                <br><small class="text-muted">{{ Str::limit($category->description, 100) }}</small>
                                            @endif
                                        </td>
                                        <td>
                                            <span class="badge" style="background-color: {{ $category->color }}">{{ $category->color }}</span>
                                        </td>
                                        <td>
                                            <span class="badge {{ $category->is_active ? 'bg-success' : 'bg-secondary' }}">
                                                {{ $category->is_active ? 'Активна' : 'Неактивна' }}
                                            </span>
                                        </td>
                                        <td>
                                            <span class="badge bg-info">{{ $category->sort_order }}</span>
                                        </td>
                                        <td>
                                            <span class="badge bg-primary">{{ $category->articles->count() }}</span>
                                        </td>
                                        <td>
                                            <div class="d-flex flex-wrap gap-1">
                                                @foreach(['ru', 'en', 'ua'] as $langCode)
                                                    @php
                                                        $translation = $category->translation($langCode);
                                                        $hasTranslation = $translation && !empty(trim($translation->name));
                                                        $statusClass = $hasTranslation ? 'bg-success' : 'bg-secondary';
                                                        $langNames = ['ru' => 'RU', 'en' => 'EN', 'ua' => 'UA'];
                                                        $langFullNames = ['ru' => 'Русский', 'en' => 'English', 'ua' => 'Українська'];
                                                    @endphp
                                                    <span class="badge {{ $statusClass }} translation-badge" 
                                                          data-category-id="{{ $category->id }}" 
                                                          data-language="{{ $langCode }}" 
                                                          data-language-name="{{ $langNames[$langCode] }}"
                                                          style="cursor: pointer;" 
                                                          title="{{ $langFullNames[$langCode] }} - {{ $hasTranslation ? 'Есть перевод' : 'Нет перевода' }}">
                                                        {{ $langNames[$langCode] }} {{ $hasTranslation ? '✓' : '✗' }}
                                                    </span>
                                                @endforeach
                                            </div>
                                        </td>
                                        <td>
                                            <div class="btn-group" role="group">
                                                <a href="{{ route('admin.blog-categories.edit', $category->id) }}" class="btn btn-sm btn-outline-warning" title="Редактировать">
                                                    <i class="fas fa-edit"></i>
                                                </a>
                                                <button type="button" class="btn btn-sm btn-outline-danger" onclick="deleteCategory({{ $category->id }})" title="Удалить">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="7" class="text-center py-4">
                                            <div class="text-muted">
                                                <i class="fas fa-tags fa-3x mb-3"></i>
                                                <p>Категории не найдены</p>
                                                <a href="{{ route('admin.blog-categories.create') }}" class="btn btn-primary">Создать первую категорию</a>
                                            </div>
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <!-- Пагинация -->
                    @if($categories->hasPages())
                        <div class="d-flex justify-content-center mt-4">
                            {{ $categories->links() }}
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Модальное окно подтверждения удаления -->
<div class="modal fade" id="deleteModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Подтверждение удаления</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                Вы уверены, что хотите удалить эту категорию? Это действие нельзя отменить.
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Отмена</button>
                <form id="deleteForm" method="POST" style="display: inline;">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-danger">Удалить</button>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Модальное окно для переводов -->
<div class="modal fade" id="translationModal" tabindex="-1" aria-labelledby="translationModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="translationModalLabel">Перевод категории</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="translationForm">
                    <div class="mb-3">
                        <label for="translation_name" class="form-label">Название категории <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="translation_name" name="name" required>
                    </div>
                    
                    <div class="mb-3">
                        <label for="translation_description" class="form-label">Описание</label>
                        <textarea class="form-control" id="translation_description" name="description" rows="3"></textarea>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Закрыть</button>
                <button type="button" class="btn btn-primary" id="saveTranslationBtn">
                    <i class="fas fa-save me-2"></i>Сохранить перевод
                </button>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
// Удаление категории
function deleteCategory(categoryId) {
    const modal = new bootstrap.Modal(document.getElementById('deleteModal'));
    const form = document.getElementById('deleteForm');
    
    form.action = `/panel/blog-categories/${categoryId}`;
    modal.show();
}

// Переводы категорий
let currentCategoryId = null;
let currentLanguage = null;

// Обработчик клика по значкам языков
document.addEventListener('DOMContentLoaded', function() {
    document.querySelectorAll('.translation-badge').forEach(badge => {
        badge.addEventListener('click', function() {
            currentCategoryId = this.getAttribute('data-category-id');
            currentLanguage = this.getAttribute('data-language');
            const languageName = this.getAttribute('data-language-name');
            
            // Обновляем заголовок модального окна
            document.getElementById('translationModalLabel').textContent = `Перевод категории - ${languageName}`;
            
            // Загружаем данные перевода
            loadTranslationData(currentCategoryId, currentLanguage);
            
            // Показываем модальное окно
            const modal = new bootstrap.Modal(document.getElementById('translationModal'));
            modal.show();
        });
    });
});

// Загрузка данных перевода
function loadTranslationData(categoryId, language) {
    fetch(`/panel/blog-categories/${categoryId}/translations/${language}`)
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                document.getElementById('translation_name').value = data.translation.name || '';
                document.getElementById('translation_description').value = data.translation.description || '';
            } else {
                // Если перевода нет, очищаем поля
                document.getElementById('translation_name').value = '';
                document.getElementById('translation_description').value = '';
            }
        })
        .catch(error => {
            console.error('Ошибка загрузки перевода:', error);
            document.getElementById('translation_name').value = '';
            document.getElementById('translation_description').value = '';
        });
}

// Сохранение перевода
document.getElementById('saveTranslationBtn').addEventListener('click', function() {
    if (!currentCategoryId || !currentLanguage) {
        alert('Ошибка: не выбран язык или категория');
        return;
    }
    
    const formData = new FormData();
    formData.append('_token', document.querySelector('meta[name="csrf-token"]').getAttribute('content'));
    formData.append('language_code', currentLanguage);
    formData.append('name', document.getElementById('translation_name').value);
    formData.append('description', document.getElementById('translation_description').value);
    
    fetch(`/panel/blog-categories/${currentCategoryId}/save-translation`, {
        method: 'POST',
        body: formData
    })
    .then(response => {
        if (!response.ok) {
            throw new Error(`HTTP error! status: ${response.status}`);
        }
        return response.json();
    })
    .then(data => {
        if (data.success) {
            // Обновляем значок языка
            const badge = document.querySelector(`[data-category-id="${currentCategoryId}"][data-language="${currentLanguage}"]`);
            if (badge) {
                badge.classList.remove('bg-secondary');
                badge.classList.add('bg-success');
                const langCode = badge.getAttribute('data-language-name');
                badge.textContent = `${langCode} ✓`;
                badge.title = `${badge.getAttribute('data-language-name')} - Есть перевод`;
            }
            
            // Закрываем модальное окно
            const modal = bootstrap.Modal.getInstance(document.getElementById('translationModal'));
            modal.hide();
            
            // Перевод сохранен
        } else {
            alert('Ошибка сохранения перевода: ' + (data.message || 'Неизвестная ошибка'));
        }
    })
    .catch(error => {
        console.error('Ошибка сохранения перевода:', error);
        alert('Ошибка сохранения перевода: ' + error.message);
    });
});
</script>
@endpush
