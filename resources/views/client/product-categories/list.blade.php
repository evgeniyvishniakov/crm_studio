@extends('client.layouts.app')

@section('content')
<div class="dashboard-container">
    <div class="services-container">
        <div class="services-header">
            <h1>{{ __('messages.product_categories') }}</h1>
        
            <div class="categories-header-actions">
                <button class="btn-add-service" onclick="openModal()">
                    <svg class="icon" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd" d="M10 3a1 1 0 011 1v5h5a1 1 0 110 2h-5v5a1 1 0 11-2 0v-5H4a1 1 0 110-2h5V4a1 1 0 011-1z" clip-rule="evenodd" />
                    </svg>
                    {{ __('messages.add_category') }}
                </button>

                <div class="search-box">
                    <svg class="search-icon" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd" d="M8 4a4 4 0 100 8 4 4 0 000-8zM2 8a6 6 0 1110.89 3.476l4.817 4.817a1 1 0 01-1.414 1.414l-4.816-4.816A6 6 0 012 8z" clip-rule="evenodd" />
                    </svg>
                    <input type="text" id="searchInput" placeholder="{{ __('messages.search_placeholder') }}" onkeyup="handleSearch()">
                </div>
            </div>
        </div>

        <!-- Десктопная таблица -->
        <div class="table-wrapper">
            <table class="table-striped services-table">
                <thead>
                <tr>
                    <th>{{ __('messages.category_name') }}</th>
                    <th>{{ __('messages.category_description') }}</th>
                    <th>{{ __('messages.category_status') }}</th>
                    <th class="actions-column">{{ __('messages.actions') }}</th>
                </tr>
                </thead>
                <tbody id="servicesTableBody">
                @foreach($categories ?? [] as $category)
                    <tr id="category-{{ $category->id }}">
                        <td>{{ $category->name }}</td>
                        <td>{{ $category->description ?? '—' }}</td>
                        <td>
                            <span class="status-badge {{ $category->status ? 'active' : 'inactive' }}">
                                {{ $category->status ? __('messages.category_active') : __('messages.category_inactive') }}
                            </span>
                        </td>
                        <td class="actions-cell">
                            <button class="btn-edit">
                                <svg class="icon" viewBox="0 0 20 20" fill="currentColor">
                                    <path d="M13.586 3.586a2 2 0 112.828 2.828l-.793.793-2.828-2.828.793-.793zM11.379 5.793L3 14.172V17h2.828l8.38-8.379-2.83-2.828z" />
                                </svg>
                                {{ __('messages.edit') }}
                            </button>
                            <button class="btn-delete">
                                <svg class="icon" viewBox="0 0 20 20" fill="currentColor">
                                    <path fill-rule="evenodd" d="M9 2a1 1 0 00-.894.553L7.382 4H4a1 1 0 000 2v10a2 2 0 002 2h8a2 2 0 002-2V6a1 1 0 100-2h-3.382l-.724-1.447A1 1 0 0011 2H9zM7 8a1 1 0 012 0v6a1 1 0 11-2 0V8zm5-1a1 1 0 00-1 1v6a1 1 0 102 0V8a1 1 0 00-1-1z" clip-rule="evenodd" />
                                </svg>
                                {{ __('messages.delete') }}
                            </button>
                        </td>
                    </tr>
                @endforeach
                </tbody>
            </table>
            
            <!-- Пагинация будет добавлена через JavaScript -->
            <div id="categoriesPagination"></div>
        </div>

        <!-- Мобильные карточки категорий -->
        <div class="categories-cards" id="categoriesCards" style="display: none;">
            <!-- Карточки будут добавлены через JavaScript -->
        </div>

        <!-- Пагинация для мобильных карточек -->
        <div id="mobileCategoriesPagination" style="display: none;"></div>
    </div>

    <!-- Модальное окно для добавления категории -->
    <div id="addServiceModal" class="modal">
        <div class="modal-content">
            <div class="modal-header">
                <h2>{{ __('messages.add_new_category') }}</h2>
                <span class="close" onclick="closeModal()">&times;</span>
            </div>
            <div class="modal-body">
                <form id="addServiceForm">
                    @csrf
                    <div class="form-group">
                        <label for="serviceName">{{ __('messages.category_name') }} *</label>
                        <input type="text" id="serviceName" name="name" required>
                    </div>
                    <div class="form-group">
                        <label for="serviceDescription">{{ __('messages.category_description') }}</label>
                        <textarea id="serviceDescription" name="description" rows="3"></textarea>
                    </div>
                    <div class="form-group">
                        <label for="serviceStatus">{{ __('messages.category_status') }}</label>
                        <select id="serviceStatus" name="status">
                            <option value="1">{{ __('messages.category_active') }}</option>
                            <option value="0">{{ __('messages.category_inactive') }}</option>
                        </select>
                    </div>
                    <div class="form-actions">
                        <button type="button" class="btn-cancel" onclick="closeModal()">{{ __('messages.cancel') }}</button>
                        <button type="submit" class="btn-submit">{{ __('messages.add') }}</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Модальное окно для редактирования категории -->
    <div id="editServiceModal" class="modal">
        <div class="modal-content">
            <div class="modal-header">
                <h2>{{ __('messages.edit_category') }}</h2>
                <span class="close" onclick="closeEditModal()">&times;</span>
            </div>
            <div class="modal-body">
                <form id="editServiceForm">
                    @csrf
                    <input type="hidden" id="editServiceId" name="id">
                    <div class="form-group">
                        <label for="editServiceName">{{ __('messages.category_name') }} *</label>
                        <input type="text" id="editServiceName" name="name" required>
                    </div>
                    <div class="form-group">
                        <label for="editServiceDescription">{{ __('messages.category_description') }}</label>
                        <textarea id="editServiceDescription" name="description" rows="3"></textarea>
                    </div>
                    <div class="form-group">
                        <label for="editServiceStatus">{{ __('messages.category_status') }}</label>
                        <select id="editServiceStatus" name="status">
                            <option value="1">{{ __('messages.category_active') }}</option>
                            <option value="0">{{ __('messages.category_inactive') }}</option>
                        </select>
                    </div>
                    <div class="form-actions">
                        <button type="button" class="btn-cancel" onclick="closeEditModal()">{{ __('messages.cancel') }}</button>
                        <button type="submit" class="btn-submit">{{ __('messages.save') }}</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Модальное окно подтверждения удаления -->
    <div id="confirmationModal" class="modal">
        <div class="modal-content">
            <div class="modal-header">
                <h2>{{ __('messages.confirm_delete') }}</h2>
                <span class="close" onclick="document.getElementById('confirmationModal').style.display='none'">&times;</span>
            </div>
            <div class="modal-body">
                <p>{{ __('messages.delete_category_confirmation') }}</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn-cancel" id="cancelDelete">{{ __('messages.cancel') }}</button>
                <button type="button" class="btn-delete" id="confirmDelete">{{ __('messages.delete') }}</button>
            </div>
        </div>
    </div>
</div>

@push('scripts')
    <script src="{{ asset('client/js/categories.js') }}"></script>
@endpush
@endsection
