@extends('client.layouts.app')

@section('content')
    <div class="dashboard-container">
        <div class="purchases-container">
            <div class="purchases-header">
                <div class="header-top">
            <h1>{{ __('messages.purchases') }}</h1>
            <div class="header-actions">
                <button class="btn-add-purchase" onclick="openPurchaseModal()">
                    <svg class="icon" viewBox="0 0 24 24" fill="currentColor">
                        <path d="M19 13h-6v6h-2v-6H5v-2h6V5h2v6h6v2z"/>
                    </svg>
                    {{ __('messages.add_purchase') }}
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
                    <h1 class="mobile-title">{{ __('messages.purchases') }}</h1>
                    <div class="mobile-header-actions">
                        <button class="btn-add-purchase" onclick="openPurchaseModal()">
                            <svg class="icon" viewBox="0 0 24 24" fill="currentColor">
                                <path d="M19 13h-6v6h-2v-6H5v-2h6V5h2v6h6v2z"/>
                            </svg>
                            {{ __('messages.add_purchase') }}
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

        <!-- Десктопная таблица -->
        <div id="purchasesList" class="table-wrapper">
            <table class="table purchases-table">
                <thead>
                    <tr>
                        <th>{{ __('messages.date') }}</th>
                        <th>{{ __('messages.supplier') }}</th>
                        <th>{{ __('messages.wholesale_amount') }}</th>
                        <th>{{ __('messages.notes') }}</th>
                        <th>{{ __('messages.actions') }}</th>
                    </tr>
                </thead>
                <tbody id="purchasesListBody">
                    <!-- Данные будут загружаться через AJAX -->
                </tbody>
            </table>
        </div>
        <div id="purchasesPagination"></div>

        <!-- Мобильные карточки -->
        <div id="purchasesCards" class="purchases-cards" style="display: none;">
            <!-- Карточки будут загружаться через AJAX -->
        </div>
        <div id="mobilePurchasesPagination" style="display: none;"></div>
        </div>
    </div>

    <!-- Модальное окно добавления закупки -->
    <div id="purchaseModal" class="modal">
        <div class="modal-content" style="width: 80%; max-width: 900px;">
            <div class="modal-header">
                <h2>{{ __('messages.add_purchase') }}</h2>
                <span class="close" onclick="closePurchaseModal()">&times;</span>
            </div>
            <div class="modal-body">
                <form id="purchaseForm">
                    @csrf
                    <div class="form-row">
                        <div class="form-group">
                            <label>{{ __('messages.date') }} </label>
                            <input type="date" name="date" required class="form-control"
                                   data-locale="{{ app()->getLocale() }}">
                        </div>
                        <div class="form-group">
                            <label>{{ __('messages.supplier') }} </label>
                            <select name="supplier_id" required class="form-control">
                                <option value="">{{ __('messages.select_supplier') }}</option>
                                @foreach($suppliers as $supplier)
                                    <option value="{{ $supplier->id }}">{{ $supplier->name }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="form-group">
                        <label>{{ __('messages.notes') }}</label>
                        <textarea name="notes" rows="2" class="form-control"></textarea>
                    </div>

                    <div class="items-container" id="itemsContainer">
                        <h3>{{ __('messages.products') }}</h3>
                        <div class="item-row template" style="display: none;">
                            <div class="form-row">
                                <div class="form-group">
                                    <label>{{ __('messages.product_label') }} </label>
                                    <div class="product-search-container">
                                        <input type="text" class="product-search-input form-control" placeholder="{{ __('messages.start_typing_product_name_for_purchase') }}"
                                               oninput="searchProducts(this)"
                                               onfocus="showProductDropdown(this)" autocomplete="off">
                                        <div class="product-dropdown" style="display: none;">
                                            <div class="product-dropdown-list"></div>
                                        </div>
                                        <select name="items[0][product_id]" class="form-control product-select" style="display: none;">
                                            <option value="">{{ __('messages.select_product') }}</option>
                                            @foreach($products as $product)
                                                <option value="{{ $product->id }}" data-purchase="{{ $product->purchase_price }}" data-retail="{{ $product->retail_price }}">{{ $product->name }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label>{{ __('messages.purchase_price_label') }} </label>
                                    <input type="number"  name="items[0][purchase_price]" class="form-control" >
                                </div>
                                <div class="form-group">
                                    <label>{{ __('messages.retail_price_label') }} </label>
                                    <input type="number" name="items[0][retail_price]" class="form-control" >
                                </div>
                                <div class="form-group">
                                    <label>{{ __('messages.quantity_label') }} </label>
                                    <input type="number" name="items[0][quantity]" required class="form-control" min="1" value="1">
                                </div>
                                <div class="form-group">
                                    <button type="button" class="btn-remove-item" onclick="removeItemRow(this)">
                                        <svg class="icon" viewBox="0 0 20 20" fill="currentColor">
                                            <path fill-rule="evenodd" d="M9 2a1 1 0 00-.894.553L7.382 4H4a1 1 0 000 2v10a2 2 0 002 2h8a2 2 0 002-2V6a1 1 0 100-2h-3.382l-.724-1.447A1 1 0 0011 2H9zM7 8a1 1 0 012 0v6a1 1 0 11-2 0V8zm5-1a1 1 0 00-1 1v6a1 1 0 102 0V8a1 1 0 00-1-1z" clip-rule="evenodd"/>
                                        </svg>
                                    </button>
                                </div>
                            </div>
                        </div>
                        <div class="item-row">
                            <div class="form-row">
                                <div class="form-group">
                                    <label>{{ __('messages.product_label') }} </label>
                                    <div class="product-search-container">
                                        <input type="text" class="product-search-input form-control" placeholder="{{ __('messages.start_typing_product_name_for_purchase') }}"
                                               oninput="searchProducts(this)"
                                               onfocus="showProductDropdown(this)" autocomplete="off">
                                        <div class="product-dropdown" style="display: none;">
                                            <div class="product-dropdown-list"></div>
                                        </div>
                                        <select name="items[0][product_id]" class="form-control product-select" style="display: none;">
                                            <option value="">{{ __('messages.select_product') }}</option>
                                            @foreach($products as $product)
                                                <option value="{{ $product->id }}" data-purchase="{{ $product->purchase_price }}" data-retail="{{ $product->retail_price }}">{{ $product->name }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label>{{ __('messages.purchase_price_label') }} </label>
                                    <input type="number" step="0.01" name="items[0][purchase_price]" required class="form-control" min="0.01">
                                </div>
                                <div class="form-group">
                                    <label>{{ __('messages.retail_price_label') }} </label>
                                    <input type="number" step="0.01" name="items[0][retail_price]" required class="form-control" min="0.01">
                                </div>
                                <div class="form-group">
                                    <label>{{ __('messages.quantity_label') }} </label>
                                    <input type="number" name="items[0][quantity]" required class="form-control" min="1" value="1">
                                </div>
                                <div class="form-group">
                                    <button type="button" class="btn-remove-item" onclick="removeItemRow(this)">
                                        <svg class="icon" viewBox="0 0 20 20" fill="currentColor">
                                            <path fill-rule="evenodd" d="M9 2a1 1 0 00-.894.553L7.382 4H4a1 1 0 000 2v10a2 2 0 002 2h8a2 2 0 002-2V6a1 1 0 100-2h-3.382l-.724-1.447A1 1 0 0011 2H9zM7 8a1 1 0 012 0v6a1 1 0 11-2 0V8zm5-1a1 1 0 00-1 1v6a1 1 0 102 0V8a1 1 0 00-1-1z" clip-rule="evenodd"/>
                                        </svg>
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="form-actions">
                        <button type="button" class="btn-add-item" onclick="addItemRow()">
                            <svg class="icon" viewBox="0 0 24 24" fill="currentColor">
                                <path d="M19 13h-6v6h-2v-6H5v-2h6V5h2v6h6v2z"/>
                            </svg>
                            {{ __('messages.add_product') }}
                        </button>
                        <button type="button" class="btn-cancel" onclick="closePurchaseModal()">{{ __('messages.cancel') }}</button>
                        <button type="submit" class="btn-submit">{{ __('messages.save_purchase') }}</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Модальное окно редактирования закупки -->
    <div id="editPurchaseModal" class="modal">
        <div class="modal-content" style="width: 80%; max-width: 900px;">
            <div class="modal-header">
                <h2>{{ __('messages.edit_purchase') }}</h2>
                <span class="close" onclick="closeEditPurchaseModal()">&times;</span>
            </div>
            <div class="modal-body" id="editPurchaseModalBody">
                <!-- Контент будет загружен динамически -->
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
            <p>{{ __('messages.confirm_delete_purchase') }}</p>
            </div>
            <div class="modal-footer">
                <button id="cancelDelete" class="btn-cancel">{{ __('messages.cancel') }}</button>
                <button id="confirmDelete" class="btn-delete">{{ __('messages.delete') }}</button>
            </div>
        </div>
    </div>

    <!-- Модальное окно подтверждения отмены -->
    <div id="cancelPurchaseModal" class="confirmation-modal" style="display: none;">
        <div class="confirmation-content">
            <h3>{{ __('messages.cancel_confirmation') }}</h3>
            <p>{{ __('messages.confirm_cancel_purchase_creation') }}</p>
            <div class="confirmation-buttons">
                <button class="cancel-btn" id="cancelCancelPurchase">{{ __('messages.cancel') }}</button>
                <button class="confirm-btn" id="confirmCancelPurchaseBtn">{{ __('messages.yes_cancel') }}</button>
            </div>
        </div>
    </div>

@endsection

@push('scripts')
    <script>
        // Инициализация глобальных переменных для закупок
        window.allProducts = @json($products);
        window.suppliers = @json($suppliers);
        
        // Переводы для JavaScript
        window.translations = {
            photo: '{{ __("messages.photo") }}',
            product: '{{ __("messages.product") }}',
            purchase_price: '{{ __("messages.purchase_price") }}',
            retail_price: '{{ __("messages.retail_price") }}',
            quantity: '{{ __("messages.quantity") }}',
            sum: '{{ __("messages.sum") }}',
            no_photo: '{{ __("messages.no_photo") }}',
            pieces: '{{ __("messages.pieces") }}',
            edit: '{{ __("messages.edit") }}',
            delete: '{{ __("messages.delete") }}',
            date: '{{ __("messages.date") }}',
            supplier: '{{ __("messages.supplier") }}',
            notes: '{{ __("messages.notes") }}',
            products: '{{ __("messages.products") }}'
        };
    </script>
    <script src="{{ asset('client/js/purchases.js') }}"></script>
@endpush
