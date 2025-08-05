@extends('client.layouts.app')

@section('content')
<div class="dashboard-container">
    <!-- Модальное окно для увеличенного изображения -->
    <div id="imageModal" class="modal image-modal" onclick="closeImageModal()">
        <img id="modalImage" class="modal-image-content" onclick="event.stopPropagation()">
    </div>

    <div class="warehouse-container">
        <div class="warehouse-header">
            <div class="header-top">
        <h1>{{ __('messages.warehouse') }}</h1>
        <div class="header-actions">
            <button class="btn-add-product" onclick="openModal()">
                <svg class="icon" viewBox="0 0 24 24" fill="currentColor">
                    <path d="M19 13h-6v6h-2v-6H5v-2h6V5h2v6h6v2z"/>
                </svg>
                {{ __('messages.add_to_warehouse') }}
            </button>
            <div class="search-box">
                <svg class="search-icon" viewBox="0 0 24 24" fill="currentColor">
                    <path d="M15.5 14h-.79l-.28-.27a6.5 6.5 0 0 0 1.48-5.34c-.47-2.78-2.79-5-5.59-5.34a6.505 6.505 0 0 0-7.27 7.27c.34 2.8 2.56 5.12 5.34 5.59a6.5 6.5 0 0 0 5.34-1.48l.27.28v.79l4.25 4.25c.41.41 1.08.41 1.49 0 .41-.41.41-1.08 0-1.49L15.5 14zm-6 0C7.01 14 5 11.99 5 9.5S7.01 5 9.5 5 14 7.01 14 9.5 11.99 14 9.5 14z"/>
                </svg>
                <input type="text" placeholder="{{ __('messages.search') }}..." id="searchInput">
                    </div>
                </div>
            </div>
            
            <!-- Мобильная версия заголовка -->
            <div class="mobile-header">
                <h1 class="mobile-title">{{ __('messages.warehouse') }}</h1>
                <div class="mobile-header-actions">
                    <button class="btn-add-product" onclick="openModal()">
                        <svg class="icon" viewBox="0 0 24 24" fill="currentColor">
                            <path d="M19 13h-6v6h-2v-6H5v-2h6V5h2v6h6v2z"/>
                        </svg>
                        {{ __('messages.add_to_warehouse') }}
                    </button>
                    <div class="search-box">
                        <svg class="search-icon" viewBox="0 0 24 24" fill="currentColor">
                            <path d="M15.5 14h-.79l-.28-.27a6.5 6.5 0 0 0 1.48-5.34c-.47-2.78-2.79-5-5.59-5.34a6.505 6.505 0 0 0-7.27 7.27c.34 2.8 2.56 5.12 5.34 5.59a6.5 6.5 0 0 0 5.34-1.48l.27.28v.79l4.25 4.25c.41.41 1.08.41 1.49 0 .41-.41.41-1.08 0-1.49L15.5 14zm-6 0C7.01 14 5 11.99 5 9.5S7.01 5 9.5 5 14 7.01 14 9.5 11.99 14 9.5 14z"/>
                        </svg>
                        <input type="text" placeholder="{{ __('messages.search') }}..." id="searchInputMobile">
                    </div>
            </div>
        </div>
    </div>

    <div class="table-wrapper">
        <table class="table-striped warehouse-table">
            <thead>
            <tr>
                <th>{{ __('messages.photo') }}</th>
                <th>{{ __('messages.product') }}</th>
                <th>{{ __('messages.purchase_price') }}</th>
                <th>{{ __('messages.retail_price') }}</th>
                <th>{{ __('messages.stock') }}</th>
                <th>{{ __('messages.actions') }}</th>
            </tr>
            </thead>
            <tbody id="warehouseTableBody">
            <!-- Данные будут загружаться через AJAX -->
            </tbody>
        </table>
    </div>

    <!-- Карточки для мобильной версии -->
    <div class="warehouse-cards" id="warehouseCards">
        <!-- Карточки будут загружаться через AJAX -->
    </div>

    <!-- Пагинация -->
    <div id="warehousePagination"></div>
    
    <!-- Пагинация для мобильных карточек -->
    <div id="mobileWarehousePagination"></div>

    <!-- Модальное окно добавления -->
    <div id="addModal" class="modal">
        <div class="modal-content">
            <div class="modal-header">
                <h2>{{ __('messages.add_product_to_warehouse') }}</h2>
                <span class="close" onclick="closeModal()">&times;</span>
            </div>
            <div class="modal-body">
                <form id="addForm">
                    @csrf
                    <div class="form-group">
                        <label>{{ __('messages.product') }} *</label>
                        <div class="product-search-container">
                            <input type="text" class="product-search-input form-control"
                                   placeholder="{{ __('messages.start_typing_product_name') }}..."
                                   oninput="searchProducts(this)"
                                   onfocus="showProductDropdown(this)"
                                   autocomplete="off">
                            <div class="product-dropdown" style="display: none;">
                                <div class="product-dropdown-list"></div>
                            </div>
                            <select id="productSelect" name="product_id" class="form-control product-select" style="display: none;" required>
                                <option value="">{{ __('messages.select_product') }}</option>
                                @foreach($products as $product)
                                    <option value="{{ $product->id }}" data-purchase="{{ $product->purchase_price }}" data-retail="{{ $product->retail_price }}">{{ $product->name }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="form-group">
                        <label>{{ __('messages.purchase_price') }} *</label>
                        <input type="number" step="0.01" name="purchase_price" required>
                    </div>
                    <div class="form-group">
                        <label>{{ __('messages.retail_price') }} *</label>
                        <input type="number" step="0.01" name="retail_price" required>
                    </div>
                    <div class="form-group">
                        <label>{{ __('messages.quantity') }} *</label>
                        <input type="number" name="quantity" required>
                    </div>
                    <div class="form-actions">
                        <button type="button" class="btn-cancel" onclick="closeModal()">{{ __('messages.cancel') }}</button>
                        <button type="submit" class="btn-submit">{{ __('messages.add') }}</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Модальное окно редактирования -->
    <div id="editModal" class="modal">
        <div class="modal-content">
            <div class="modal-header">
                <h2>{{ __('messages.edit_product_on_warehouse') }}</h2>
                <span class="close" onclick="closeEditModal()">&times;</span>
            </div>
            <div class="modal-body">
                <form id="editForm">
                    @csrf
                    @method('PUT')
                    <input type="hidden" id="editItemId" name="id">
                    <div class="form-group">
                        <label>{{ __('messages.product') }}</label>
                        <input type="text" id="editProductName" readonly>
                    </div>
                    <div class="form-group">
                        <label>{{ __('messages.purchase_price') }} *</label>
                        <input type="number" step="0.01" id="editPurchasePrice" name="purchase_price" required>
                    </div>
                    <div class="form-group">
                        <label>{{ __('messages.retail_price') }} *</label>
                        <input type="number" step="0.01" id="editRetailPrice" name="retail_price" required>
                    </div>
                    <div class="form-group">
                        <label>{{ __('messages.quantity') }} *</label>
                        <input type="number" id="editQuantity" name="quantity" required>
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
                <h2>{{ __('messages.delete_confirmation') }}</h2>
                <span class="close" onclick="closeConfirmationModal()">&times;</span>
            </div>
            <div class="modal-body">
            <p>{{ __('messages.confirm_delete_product_from_warehouse') }}</p>
            </div>
            <div class="modal-footer">
                <button id="cancelDelete" class="btn-cancel">{{ __('messages.cancel') }}</button>
                <button id="confirmDeleteBtn" class="btn-delete">{{ __('messages.delete') }}</button>
            </div>
        </div>
    </div>

    @push('scripts')
    <script>
        // Инициализация глобальных переменных
        window.allProducts = @json($products);
        
        // Переводы для JavaScript
        window.translations = {
            product: '{{ __("messages.product") }}',
            purchase_price: '{{ __("messages.purchase_price") }}',
            retail_price: '{{ __("messages.retail_price") }}',
            quantity: '{{ __("messages.quantity") }}',
            cancel: '{{ __("messages.cancel") }}',
            save: '{{ __("messages.save") }}'
        };
    </script>
    <script src="{{ asset('client/js/warehouse.js') }}"></script>
    @endpush
@endsection
            