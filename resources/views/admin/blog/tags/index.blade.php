@extends('admin.layouts.app')

@section('title', 'Теги блога - Админ панель')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h1 class="h3 mb-0">Теги блога</h1>
                <a href="{{ route('admin.blog-tags.create') }}" class="btn btn-primary">
                    <i class="fas fa-plus me-2"></i>Добавить тег
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

            <!-- Таблица тегов -->
            <div class="card">
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>Название</th>
                                    <th>Цвет</th>
                                    <th>Статус</th>
                                    <th>Статей</th>
                                    <th>Переводы</th>
                                    <th>Действия</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($tags as $tag)
                                    <tr>
                                        <td>
                                            <div class="d-flex align-items-center">
                                                <span class="badge me-2" style="background-color: {{ $tag->color }}; width: 20px; height: 20px; border-radius: 50%;"></span>
                                                <strong>{{ $tag->name }}</strong>
                                            </div>
                                        </td>
                                        <td>
                                            <span class="badge" style="background-color: {{ $tag->color }}">{{ $tag->color }}</span>
                                        </td>
                                        <td>
                                            <span class="badge {{ $tag->is_active ? 'bg-success' : 'bg-secondary' }}">
                                                {{ $tag->is_active ? 'Активен' : 'Неактивен' }}
                                            </span>
                                        </td>
                                        <td>
                                            <span class="badge bg-primary">{{ $tag->articles->count() }}</span>
                                        </td>
                                        <td>
                                            <div class="d-flex flex-wrap gap-1">
                                                @foreach(['ru', 'en', 'ua'] as $langCode)
                                                    @php
                                                        $translation = $tag->translation($langCode);
                                                        $hasTranslation = $translation && !empty(trim($translation->name));
                                                        $statusClass = $hasTranslation ? 'bg-success' : 'bg-secondary';
                                                        $langNames = ['ru' => 'RU', 'en' => 'EN', 'ua' => 'UA'];
                                                        $langFullNames = ['ru' => 'Русский', 'en' => 'English', 'ua' => 'Українська'];
                                                    @endphp
                                                    <span class="badge {{ $statusClass }} translation-badge" 
                                                          data-tag-id="{{ $tag->id }}" 
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
                                                <a href="{{ route('admin.blog-tags.edit', $tag->id) }}" class="btn btn-sm btn-outline-warning" title="Редактировать">
                                                    <i class="fas fa-edit"></i>
                                                </a>
                                                <button type="button" class="btn btn-sm btn-outline-danger" onclick="deleteTag({{ $tag->id }})" title="Удалить">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="6" class="text-center py-4">
                                            <div class="text-muted">
                                                <i class="fas fa-hashtag fa-3x mb-3"></i>
                                                <p>Теги не найдены</p>
                                                <a href="{{ route('admin.blog-tags.create') }}" class="btn btn-primary">Создать первый тег</a>
                                            </div>
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <!-- Пагинация -->
                    @if($tags->hasPages())
                        <div class="d-flex justify-content-center mt-4">
                            {{ $tags->links() }}
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
                Вы уверены, что хотите удалить этот тег? Это действие нельзя отменить.
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
                <h5 class="modal-title" id="translationModalLabel">Перевод тега</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="translationForm">
                    <div class="mb-3">
                        <label for="translation_name" class="form-label">Название тега <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="translation_name" name="name" required>
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
// Удаление тега
function deleteTag(tagId) {
    const modal = new bootstrap.Modal(document.getElementById('deleteModal'));
    const form = document.getElementById('deleteForm');
    
    form.action = `/panel/blog-tags/${tagId}`;
    modal.show();
}

// Переводы тегов
let currentTagId = null;
let currentLanguage = null;

// Обработчик клика по значкам языков
document.addEventListener('DOMContentLoaded', function() {
    document.querySelectorAll('.translation-badge').forEach(badge => {
        badge.addEventListener('click', function() {
            currentTagId = this.getAttribute('data-tag-id');
            currentLanguage = this.getAttribute('data-language');
            const languageName = this.getAttribute('data-language-name');
            
            // Обновляем заголовок модального окна
            document.getElementById('translationModalLabel').textContent = `Перевод тега - ${languageName}`;
            
            // Загружаем данные перевода
            loadTranslationData(currentTagId, currentLanguage);
            
            // Показываем модальное окно
            const modal = new bootstrap.Modal(document.getElementById('translationModal'));
            modal.show();
        });
    });
});

// Загрузка данных перевода
function loadTranslationData(tagId, language) {
    fetch(`/panel/blog-tags/${tagId}/translations/${language}`)
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                document.getElementById('translation_name').value = data.translation.name || '';
            } else {
                // Если перевода нет, очищаем поля
                document.getElementById('translation_name').value = '';
            }
        })
        .catch(error => {
            console.error('Ошибка загрузки перевода:', error);
            document.getElementById('translation_name').value = '';
        });
}

// Сохранение перевода
document.getElementById('saveTranslationBtn').addEventListener('click', function() {
    if (!currentTagId || !currentLanguage) {
        alert('Ошибка: не выбран язык или тег');
        return;
    }
    
    const formData = new FormData();
    formData.append('_token', document.querySelector('meta[name="csrf-token"]').getAttribute('content'));
    formData.append('language_code', currentLanguage);
    formData.append('name', document.getElementById('translation_name').value);
    
    fetch(`/panel/blog-tags/${currentTagId}/save-translation`, {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            // Обновляем значок языка
            const badge = document.querySelector(`[data-tag-id="${currentTagId}"][data-language="${currentLanguage}"]`);
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
            
            // Показываем уведомление об успехе
            alert('Перевод сохранен успешно!');
        } else {
            alert('Ошибка сохранения перевода: ' + (data.message || 'Неизвестная ошибка'));
        }
    })
    .catch(error => {
        console.error('Ошибка сохранения перевода:', error);
        alert('Ошибка сохранения перевода');
    });
});
</script>
@endpush
