@extends('client.layouts.app')

@section('content')
<div class="dashboard-container">
    <!-- Модальное окно для увеличенного изображения -->
    <div id="imageModal" class="modal image-modal" onclick="closeImageModal()">
        <img id="modalImage" class="modal-image-content" onclick="event.stopPropagation()">
    </div>

    <div class="sales-container">
        <div class="sales-header">
            <div class="header-top">
                <h1>{{ __('messages.sales') }}</h1>
                <div class="header-actions">
                <button class="btn-add-sale" onclick="openSaleModal()">
                    <svg class="icon" viewBox="0 0 24 24" fill="currentColor">
                        <path d="M19 13h-6v6h-2v-6H5v-2h6V5h2v6h6v2z"/>
                    </svg>
                    {{ __('messages.add_sale') }}
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
            <h1 class="mobile-title">{{ __('messages.sales') }}</h1>
            <div class="mobile-header-actions">
                <button class="btn-add-sale" onclick="openSaleModal()">
                    <svg class="icon" viewBox="0 0 24 24" fill="currentColor">
                        <path d="M19 13h-6v6h-2v-6H5v-2h6V5h2v6h6v2z"/>
                    </svg>
                    {{ __('messages.add_sale') }}
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

        <div class="sales-list table-wrapper" id="salesList">
            <table class="table-striped sale-table" id="salesTable">
                <thead>
                <tr>
                    <th>{{ __('messages.date') }}</th>
                    <th>{{ __('messages.client') }}</th>
                    <th>{{ __('messages.product') }}</th>
                    <th>{{ __('messages.photo') }}</th>
                    <th>{{ __('messages.wholesale_price') }}</th>
                    <th>{{ __('messages.retail_price') }}</th>
                    <th>{{ __('messages.quantity') }}</th>
                    <th>{{ __('messages.sum') }}</th>
                    <th>{{ __('messages.actions') }}</th>
                </tr>
                </thead>
                <tbody id="salesTableBody">
                <!-- Данные будут загружаться через AJAX -->
                </tbody>
            </table>
        </div>
        <div id="salesPagination"></div>
        
        <!-- Мобильные карточки продаж -->
        <div class="sales-cards" id="salesCards">
            <!-- Карточки будут загружаться через AJAX -->
        </div>
        <div id="mobileSalesPagination" style="display: none;"></div>
    </div>

    <!-- Модальное окно добавления продажи -->
    <div id="saleModal" class="modal">
        <div class="modal-content" style="width: 80%; max-width: 900px;">
            <div class="modal-header">
                <h2>{{ __('messages.add_sale') }}</h2>
                <span class="close" onclick="closeSaleModal()">&times;</span>
            </div>
            <div class="modal-body">
                <form id="saleForm">
                    @csrf
                    <div class="form-row date-client-row">
                        <div class="form-group">
                            <label>{{ __('messages.client') }} *</label>
                            <div class="client-search-container">
                                <input type="text" class="client-search-input form-control"
                                       placeholder="{{ __('messages.start_typing_client_info') }}"
                                       oninput="searchClients(this)" onfocus="showClientDropdown(this)" autocomplete="off">
                                <div class="client-dropdown" style="display: none;">
                                    <div class="client-dropdown-list"></div>
                                </div>
                                <select name="client_id" class="form-control client-select" style="display: none;" required>
                                    <option value="">{{ __('messages.select_client') }}</option>
                                    @foreach($clients as $client)
                                        <option value="{{ $client->id }}">
                                            {{ $client->name }}
                                            @if($client->instagram) (@{{ $client->instagram }}) @endif
                                            @if($client->phone) - {{ $client->phone }} @endif
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="form-group">
                            <label>{{ __('messages.date') }} *</label>
                            <input type="date" name="date" required class="form-control" 
                                   data-locale="{{ app()->getLocale() }}"
                                       __('messages.october'), __('messages.november'), __('messages.december')
                                   ]) }}"
                                   data-day-names="{{ json_encode([
                                       __('messages.sun'), __('messages.mon'), __('messages.tue'),
                                       __('messages.wed'), __('messages.thu'), __('messages.fri'), __('messages.sat')
                                   ]) }}">
                        </div>
                        <div class="form-group">
                            <label>{{ __('messages.employee_master') }} *</label>
                            <select name="employee_id" class="form-control" required>
                                <option value="">{{ __('messages.select_employee') }}</option>
                                @foreach($employees as $employee)
                                    <option value="{{ $employee->id }}">{{ $employee->name }}</option>
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
                                <div class="form-group product-field">
                                    <label>{{ __('messages.product') }} *</label>
                                    <div class="product-search-container">
                                        <input type="text" class="product-search-input form-control"
                                               placeholder="{{ __('messages.start_typing_product_name') }}"
                                               oninput="searchProducts(this)"
                                               onfocus="showProductDropdown(this)" autocomplete="off">
                                        <div class="product-dropdown" style="display: none;">
                                            <div class="product-dropdown-list"></div>
                                        </div>
                                        <select name="items[0][product_id]" class="form-control product-select" style="display: none;"
                                                onchange="updateProductPrices(this)">
                                            <option value="">{{ __('messages.select_product') }}</option>
                                            @foreach($products as $product)
                                                <option value="{{ $product->id }}"
                                                        data-wholesale="{{ $product->wholesale_price }}"
                                                        data-retail="{{ $product->retail_price }}"
                                                        data-quantity="{{ $product->available_quantity }}">
                                                    {{ $product->name }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                                <div class="form-group price-field">
                                    <label>
                                        <span class="desktop-label">{{ __('messages.wholesale_price') }}</span>
                                        <span class="mobile-label">{{ __('messages.wholesale_price_mobile') }}</span>
                                        *
                                    </label>
                                    <input type="number" step="0.01" name="items[0][wholesale_price]"
                                            class="form-control wholesale-price" min="0" value="0" readonly>
                                </div>
                                <div class="form-group price-field">
                                    <label>
                                        <span class="desktop-label">{{ __('messages.retail_price') }}</span>
                                        <span class="mobile-label">{{ __('messages.retail_price_mobile') }}</span>
                                        *
                                    </label>
                                    <input type="number" step="0.01" name="items[0][retail_price]"
                                            class="form-control retail-price" min="0" value="0" >
                                </div>
                                <div class="form-group quantity-field">
                                    <label>
                                        <span class="desktop-label">{{ __('messages.quantity') }}</span>
                                        <span class="mobile-label">{{ __('messages.quantity_mobile') }}</span>
                                        *
                                    </label>
                                    <input type="number" name="items[0][quantity]"
                                           class="form-control quantity" min="1" value="1"
                                           oninput="validateQuantity(this)">
                                </div>
                                <div class="form-group remove-field">
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
                                <div class="form-group product-field">
                                    <label>{{ __('messages.product') }} *</label>
                                    <div class="product-search-container">
                                        <input type="text" class="product-search-input form-control"
                                               placeholder="{{ __('messages.start_typing_product_name') }}"
                                               oninput="searchProducts(this)"
                                               onfocus="showProductDropdown(this)" autocomplete="off">
                                        <div class="product-dropdown" style="display: none;">
                                            <div class="product-dropdown-list"></div>
                                        </div>
                                        <select name="items[0][product_id]" class="form-control product-select" style="display: none;"
                                                onchange="updateProductPrices(this)">
                                            <option value="">{{ __('messages.select_product') }}</option>
                                            @foreach($products as $product)
                                                <option value="{{ $product->id }}"
                                                        data-wholesale="{{ $product->wholesale_price }}"
                                                        data-retail="{{ $product->retail_price }}"
                                                        data-quantity="{{ $product->available_quantity }}">
                                                    {{ $product->name }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                                <div class="form-group price-field">
                                    <label>
                                        <span class="desktop-label">{{ __('messages.wholesale_price') }}</span>
                                        <span class="mobile-label">{{ __('messages.wholesale_price_mobile') }}</span>
                                        *
                                    </label>
                                    <input type="number" step="0.01" name="items[0][wholesale_price]"
                                           required class="form-control wholesale-price" min="0" value="0" readonly>
                                </div>
                                <div class="form-group price-field">
                                    <label>
                                        <span class="desktop-label">{{ __('messages.retail_price') }}</span>
                                        <span class="mobile-label">{{ __('messages.retail_price_mobile') }}</span>
                                        *
                                    </label>
                                    <input type="number" step="0.01" name="items[0][retail_price]"
                                           required class="form-control retail-price" min="0" value="0" >
                                </div>
                                <div class="form-group quantity-field">
                                    <label>
                                        <span class="desktop-label">{{ __('messages.quantity') }}</span>
                                        <span class="mobile-label">{{ __('messages.quantity_mobile') }}</span>
                                        *
                                    </label>
                                    <input type="number" name="items[0][quantity]" required
                                           class="form-control quantity" min="1" value="1"
                                           oninput="validateQuantity(this)">
                                </div>
                                <div class="form-group remove-field">
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
                        <button type="button" class="btn-add-item" onclick="addItemRow('itemsContainer')">
                            <svg class="icon" viewBox="0 0 24 24" fill="currentColor">
                                <path d="M19 13h-6v6h-2v-6H5v-2h6V5h2v6h6v2z"/>
                            </svg>
                            {{ __('messages.add_product') }}
                        </button>
                        <button type="button" class="btn-cancel" onclick="closeSaleModal()">{{ __('messages.cancel') }}</button>
                        <button type="submit" class="btn-submit">{{ __('messages.save_sale') }}</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Модальное окно редактирования продажи -->
    <div id="editSaleModal" class="modal">
        <div class="modal-content" style="width: 80%; max-width: 900px;">
            <div class="modal-header">
                <h2>{{ __('messages.edit_sale') }}</h2>
                <span class="close" onclick="closeEditSaleModal()">&times;</span>
            </div>
            <div class="modal-body" id="editSaleModalBody">
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
                <p>{{ __('messages.delete_sale_confirm') }}</p>
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
        window.allClients = @json($clients);
        window.allProducts = @json($products);
        window.allEmployees = @json($employees);
        
        // Переводы для JavaScript
        window.messages = {
            max_available_quantity: '{{ __("messages.max_available_quantity") }}',
            wholesale_price: '{{ __("messages.wholesale_price") }}',
            wholesale_price_mobile: '{{ __("messages.wholesale_price_mobile") }}',
            quantity: '{{ __("messages.quantity") }}',
            quantity_mobile: '{{ __("messages.quantity_mobile") }}',
            product: '{{ __("messages.product") }}',
            retail_price: '{{ __("messages.retail_price") }}',
            retail_price_mobile: '{{ __("messages.retail_price_mobile") }}',
            client: '{{ __("messages.client") }}',
            date: '{{ __("messages.date") }}',
            employee_master: '{{ __("messages.employee_master") }}',
            notes: '{{ __("messages.notes") }}',
            products: '{{ __("messages.products") }}',
            select_client: '{{ __("messages.select_client") }}',
            select_employee: '{{ __("messages.select_employee") }}',
            select_product: '{{ __("messages.select_product") }}',
            start_typing_product_name: '{{ __("messages.start_typing_product_name") }}'
        };
    </script>
    <script src="{{ asset('client/js/sales.js') }}"></script>
    @endpush
               
@endsection
