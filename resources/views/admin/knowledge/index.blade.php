@extends('admin.layouts.app')

@section('title', 'База знаний - Админ панель')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h1 class="h3 mb-0">База знаний</h1>
                <a href="{{ route('admin.knowledge.create') }}" class="btn btn-primary">
                    <i class="fas fa-plus me-2"></i>Добавить статью
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

            <!-- Фильтры -->
            <div class="card mb-4">
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-4">
                            <label for="category-filter" class="form-label">Категория</label>
                            <select id="category-filter" class="form-select">
                                <option value="">Все категории</option>
                                @foreach($categories as $key => $name)
                                    <option value="{{ $key }}">{{ $name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label for="status-filter" class="form-label">Статус</label>
                            <select id="status-filter" class="form-select">
                                <option value="">Все статусы</option>
                                <option value="1">Опубликовано</option>
                                <option value="0">Черновик</option>
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label for="search" class="form-label">Поиск</label>
                            <input type="text" id="search" class="form-control" placeholder="Поиск по названию...">
                        </div>
                    </div>
                </div>
            </div>

            <!-- Таблица статей -->
            <div class="card">
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Заголовок</th>
                                    <th>Категория</th>
                                    <th>Автор</th>
                                    <th>Статус</th>
                                    <th>Дата создания</th>
                                    <th>Действия</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($articles as $article)
                                    <tr data-category="{{ $article->category }}" data-status="{{ $article->is_published ? '1' : '0' }}" data-title="{{ strtolower($article->title) }}">
                                        <td>{{ $article->id }}</td>
                                        <td>
                                            <div class="d-flex align-items-center">
                                                @if($article->featured_image)
                                                    <img src="{{ asset('storage/' . $article->featured_image) }}" 
                                                         alt="{{ $article->title }}" 
                                                         class="rounded me-3" 
                                                         style="width: 40px; height: 40px; object-fit: cover;">
                                                @endif
                                                <div>
                                                    <strong>{{ $article->title }}</strong>
                                                    <br>
                                                    <small class="text-muted">{{ Str::limit($article->description, 60) }}</small>
                                                </div>
                                            </div>
                                        </td>
                                        <td>
                                            <span class="badge bg-primary">{{ $categories[$article->category] ?? $article->category }}</span>
                                        </td>
                                        <td>{{ $article->author }}</td>
                                        <td>
                                            @if($article->is_published)
                                                <span class="badge bg-success">Опубликовано</span>
                                            @else
                                                <span class="badge bg-secondary">Черновик</span>
                                            @endif
                                        </td>
                                        <td>{{ $article->created_at->format('d.m.Y H:i') }}</td>
                                        <td>
                                            <div class="btn-group" role="group">
                                                <a href="{{ route('admin.knowledge.show', $article->id) }}" 
                                                   class="btn btn-sm btn-outline-primary" 
                                                   title="Просмотр">
                                                    <i class="fas fa-eye"></i>
                                                </a>
                                                <a href="{{ route('admin.knowledge.edit', $article->id) }}" 
                                                   class="btn btn-sm btn-outline-warning" 
                                                   title="Редактировать">
                                                    <i class="fas fa-edit"></i>
                                                </a>
                                                
                                                <!-- Кнопка изменения статуса публикации -->
                                                <form action="{{ route('admin.knowledge.toggle-publish', $article->id) }}" 
                                                      method="POST" 
                                                      class="d-inline">
                                                    @csrf
                                                    @if($article->is_published)
                                                        <button type="submit" 
                                                                class="btn btn-sm btn-outline-secondary" 
                                                                title="Снять с публикации"
                                                                onclick="return confirm('Снять статью с публикации?')">
                                                            <i class="fas fa-eye-slash"></i>
                                                        </button>
                                                    @else
                                                        <button type="submit" 
                                                                class="btn btn-sm btn-outline-success" 
                                                                title="Опубликовать"
                                                                onclick="return confirm('Опубликовать статью?')">
                                                            <i class="fas fa-eye"></i>
                                                        </button>
                                                    @endif
                                                </form>
                                                
                                                <form action="{{ route('admin.knowledge.destroy', $article->id) }}" 
                                                      method="POST" 
                                                      class="d-inline"
                                                      onsubmit="return confirm('Вы уверены, что хотите удалить эту статью?')">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" 
                                                            class="btn btn-sm btn-outline-danger" 
                                                            title="Удалить">
                                                        <i class="fas fa-trash"></i>
                                                    </button>
                                                </form>
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="7" class="text-center text-muted py-4">
                                            <i class="fas fa-inbox fa-3x mb-3"></i>
                                            <p>Статьи не найдены</p>
                                            <a href="{{ route('admin.knowledge.create') }}" class="btn btn-primary">
                                                Создать первую статью
                                            </a>
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <!-- Пагинация -->
                    @if($articles->hasPages())
                        <div class="d-flex justify-content-center mt-4">
                            {{ $articles->links() }}
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const categoryFilter = document.getElementById('category-filter');
    const statusFilter = document.getElementById('status-filter');
    const searchInput = document.getElementById('search');
    const tableRows = document.querySelectorAll('tbody tr');

    function filterTable() {
        const selectedCategory = categoryFilter.value;
        const selectedStatus = statusFilter.value;
        const searchTerm = searchInput.value.toLowerCase();

        tableRows.forEach(row => {
            const category = row.dataset.category;
            const status = row.dataset.status;
            const title = row.dataset.title;

            const categoryMatch = !selectedCategory || category === selectedCategory;
            const statusMatch = !selectedStatus || status === selectedStatus;
            const searchMatch = !searchTerm || title.includes(searchTerm);

            if (categoryMatch && statusMatch && searchMatch) {
                row.style.display = '';
            } else {
                row.style.display = 'none';
            }
        });
    }

    categoryFilter.addEventListener('change', filterTable);
    statusFilter.addEventListener('change', filterTable);
    searchInput.addEventListener('input', filterTable);
});
</script>
@endpush
