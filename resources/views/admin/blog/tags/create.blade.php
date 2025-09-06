@extends('admin.layouts.app')

@section('title', 'Создать тег - Блог')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h1 class="h3 mb-0">Создать тег блога</h1>
                <a href="{{ route('admin.blog-tags.index') }}" class="btn btn-outline-secondary">
                    <i class="fas fa-arrow-left me-2"></i>Назад к списку
                </a>
            </div>

            @if($errors->any())
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <ul class="mb-0">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif

            <form action="{{ route('admin.blog-tags.store') }}" method="POST">
                @csrf
                
                <div class="row">
                    <!-- Основная информация -->
                    <div class="col-lg-8">
                        <div class="card mb-4">
                            <div class="card-header">
                                <h5 class="card-title mb-0">Основная информация</h5>
                            </div>
                            <div class="card-body">
                                <div class="mb-3">
                                    <label for="name" class="form-label">Название тега <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control @error('name') is-invalid @enderror" 
                                           id="name" name="name" value="{{ old('name') }}" required>
                                    @error('name')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Настройки -->
                    <div class="col-lg-4">
                        <div class="card mb-4">
                            <div class="card-header">
                                <h5 class="card-title mb-0">Настройки</h5>
                            </div>
                            <div class="card-body">
                                <div class="mb-3">
                                    <label for="color" class="form-label">Цвет</label>
                                    <div class="input-group">
                                        <input type="color" class="form-control form-control-color @error('color') is-invalid @enderror" 
                                               id="color" name="color" value="{{ old('color', '#6c757d') }}">
                                        <input type="text" class="form-control" id="color-text" value="{{ old('color', '#6c757d') }}" readonly>
                                    </div>
                                    @error('color')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="mb-3">
                                    <div class="form-check form-switch">
                                        <input class="form-check-input" type="checkbox" id="is_active" name="is_active" value="1" 
                                               {{ old('is_active', true) ? 'checked' : '' }}>
                                        <label class="form-check-label" for="is_active">
                                            Активен
                                        </label>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Превью -->
                        <div class="card mb-4">
                            <div class="card-header">
                                <h5 class="card-title mb-0">Превью</h5>
                            </div>
                            <div class="card-body">
                                <div id="tag-preview" class="text-center">
                                    <span class="badge" id="preview-badge" style="background-color: #6c757d; font-size: 1rem; padding: 8px 16px;">
                                        Название тега
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-12">
                        <div class="d-flex justify-content-end gap-2">
                            <a href="{{ route('admin.blog-tags.index') }}" class="btn btn-secondary">Отмена</a>
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save me-2"></i>Создать тег
                            </button>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const colorInput = document.getElementById('color');
    const colorText = document.getElementById('color-text');
    const previewBadge = document.getElementById('preview-badge');
    const nameInput = document.getElementById('name');

    // Синхронизация цветов
    colorInput.addEventListener('input', function() {
        colorText.value = this.value;
        updatePreview();
    });

    colorText.addEventListener('input', function() {
        if (this.value.match(/^#[0-9A-F]{6}$/i)) {
            colorInput.value = this.value;
            updatePreview();
        }
    });

    // Обновление названия в превью
    nameInput.addEventListener('input', function() {
        updatePreview();
    });

    function updatePreview() {
        const color = colorInput.value;
        const name = nameInput.value || 'Название тега';
        
        previewBadge.style.backgroundColor = color;
        previewBadge.textContent = name;
    }

    // Инициализация превью
    updatePreview();
});
</script>
@endpush
