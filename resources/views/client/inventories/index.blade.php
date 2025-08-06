@extends('client.layouts.app')

@section('content')
<div class="dashboard-container">
    <!-- Модальное окно для увеличенного изображения -->
    <div id="imageModal" class="modal image-modal" onclick="closeImageModal()">
        <img id="modalImage" class="modal-image-content" onclick="event.stopPropagation()">
    </div>

    <div class="inventories-container">
        <div class="inventories-header">
            <div class="header-top">
                <h1>{{ __('messages.inventory') }}</h1>
                <div class="header-actions">
                    <button class="btn-add-inventory" onclick="openInventoryModal()">
                        <svg class="icon" viewBox="0 0 24 24" fill="currentColor">
                            <path d="M19 13h-6v6h-2v-6H5v-2h6V5h2v6h6v2z"/>
                        </svg>
                        {{ __('messages.new_inventory') }}
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
                <h1 class="mobile-title">{{ __('messages.inventory') }}</h1>
                <div class="mobile-header-actions">
                    <button class="btn-add-inventory" onclick="openInventoryModal()">
                        <svg class="icon" viewBox="0 0 24 24" fill="currentColor">
                            <path d="M19 13h-6v6h-2v-6H5v-2h6V5h2v6h6v2z"/>
                        </svg>
                        {{ __('messages.new_inventory') }}
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
        
        <div class="inventories-list table-wrapper" id="inventoriesList">
            <table class="table-striped inventories-table" id="inventoriesTable">
                <thead>
                    <tr>
                        <th>{{ __('messages.date') }}</th>
                        <th>{{ __('messages.responsible') }}</th>
                        <th>{{ __('messages.discrepancies') }}</th>
                        <th>{{ __('messages.notes') }}</th>
                        <th>{{ __('messages.actions') }}</th>
                    </tr>
                </thead>
                <tbody id="inventoriesListBody">
                    @foreach($inventories as $inventory)
                        <tr class="inventory-summary-row" id="inventory-row-{{ $inventory->id }}" onclick="toggleInventoryDetailsRow({{ $inventory->id }})">
                            <td>{{ $inventory->formatted_date }}</td>
                            <td>{{ $inventory->user->name ?? '—' }}</td>
                            <td>
                                @php
                                    $overagesSum = $inventory->items->where('difference', '>', 0)->sum('difference');
                                    $shortagesSum = $inventory->items->where('difference', '<', 0)->sum(function($item) { return abs($item->difference); });
                                @endphp
                                @if($overagesSum > 0 && $shortagesSum == 0)
                                    <span style="color: #b78e15;">{{ $overagesSum }} {{ __('messages.overage') }}</span>
                                @elseif($shortagesSum > 0 && $overagesSum == 0)
                                    <span class="text-danger">{{ $shortagesSum }} {{ __('messages.shortage') }}</span>
                                @elseif($overagesSum > 0 && $shortagesSum > 0)
                                    <span>{{ $overagesSum }} {{ __('messages.overage') }}, <span class="text-danger">{{ $shortagesSum }} {{ __('messages.shortage') }}</span></span>
                                @else
                                    <span class="text-success">{{ __('messages.matches') }}</span>
                                @endif
                            </td>
                            <td title="{{ $inventory->notes }}">{{ $inventory->notes ? Str::limit($inventory->notes, 30) : '—' }}</td>
                            <td>
                                <div class="inventory-actions">
                                    <button class="btn-edit" onclick="editInventory(event, {{ $inventory->id }})">
                                        <svg class="icon" viewBox="0 0 20 20" fill="currentColor">
                                            <path d="M13.586 3.586a2 2 0 112.828 2.828l-.793.793-2.828-2.828.793-.793zM11.379 5.793L3 14.172V17h2.828l8.38-8.379-2.83-2.828z"/>
                                        </svg>
                                        {{ __('messages.edit_short') }}
                                    </button>
                                    <button class="btn-delete" onclick="confirmDeleteInventory(event, {{ $inventory->id }})">
                                        <svg class="icon" viewBox="0 0 20 20" fill="currentColor">
                                            <path fill-rule="evenodd" d="M9 2a1 1 0 00-.894.553L7.382 4H4a1 1 0 000 2v10a2 2 0 002 2h8a2 2 0 002-2V6a1 1 0 100-2h-3.382l-.724-1.447A1 1 0 0011 2H9zM7 8a1 1 0 012 0v6a1 1 0 11-2 0V8zm5-1a1 1 0 00-1 1v6a1 1 0 102 0V8a1 1 0 00-1-1z" clip-rule="evenodd"/>
                                        </svg>
                                        {{ __('messages.delete') }}
                                    </button>
                                                                          <button class="btn-pdf" onclick="downloadInventoryPdf(event, {{ $inventory->id }})" title="{{ __('messages.pdf') }}">
                                          <svg class="icon" viewBox="0 0 20 20" fill="currentColor">
                                              <path d="M6 2a2 2 0 00-2 2v12a2 2 0 002 2h8a2 2 0 002-2V7.414A2 2 0 0015.414 6L13 3.586A2 2 0 0011.586 3H6zm2 2h3v3a1 1 0 001 1h3v9a1 1 0 01-1 1H6a1 1 0 01-1-1V4a1 1 0 011-1zm5 3.414V8h-2V6h.586L13 5.414zM8 10a1 1 0 100 2h4a1 1 0 100-2H8zm0 4a1 1 0 100 2h4a1 1 0 100-2H8z"/>
                                          </svg>
                                          {{ __('messages.pdf') }}
                                      </button>
                                </div>
                            </td>
                        </tr>
                        <tr class="inventory-details-row" id="details-row-{{ $inventory->id }}" style="display: none;">
                            <td colspan="5">
                                <div class="inventory-notes">{{ $inventory->notes }}</div>
                                <div class="table-wrapper">
                                    <table class="table-striped analysis-table products-table">
                                        <thead>
                                        <tr>
                                            <th>{{ __('messages.photo') }}</th>
                                            <th class="large-col">{{ __('messages.product') }}</th>
                                            <th class="small-col">{{ __('messages.warehouse_short') }}</th>
                                            <th class="small-col">{{ __('messages.quantity') }}</th>
                                            <th>{{ __('messages.difference') }}</th>
                                            <th>{{ __('messages.status') }}</th>
                                        </tr>
                                        </thead>
                                        <tbody>
                                        @php
                                            $discrepancies = $inventory->items->where('difference', '!=', 0);
                                        @endphp
                                        @if($discrepancies->count())
                                            @foreach($discrepancies as $item)
                                                <tr>
                                                    <td>
                                                        @if($item->product->photo)
                                                            <img src="{{ Storage::url($item->product->photo) }}" class="product-photo" alt="{{ $item->product->name }}">
                                                        @else
                                                            <div class="no-photo">{{ __('messages.no_photo') }}</div>
                                                        @endif
                                                    </td>
                                                    <td class="large-col">{{ $item->product->name }}</td>
                                                    <td class="small-col">{{ $item->warehouse_qty }} {{ __('messages.units') }}</td>
                                                    <td class="small-col">{{ $item->actual_qty }} {{ __('messages.units') }}</td>
                                                    <td class="{{ $item->difference > 0 ? 'text-success' : ($item->difference < 0 ? 'text-danger' : '') }}">
                                                        {{ $item->difference > 0 ? '+' : '' }}{{ $item->difference }} {{ __('messages.units') }}
                                                    </td>
                                                    <td>
                                                        @if($item->difference == 0)
                                                            <span class="status-success">{{ __('messages.matches_status') }}</span>
                                                        @elseif($item->difference > 0)
                                                            <span class="status-warning">{{ __('messages.overage_status') }}</span>
                                                        @else
                                                            <span class="status-danger">{{ __('messages.shortage_status') }}</span>
                                                        @endif
                                                    </td>
                                                </tr>
                                            @endforeach
                                        @else
                                            <tr>
                                                <td colspan="6" class="text-center text-success">
                                                    {{ __('messages.all_products_match') }}
                                                </td>
                                            </tr>
                                        @endif
                                        </tbody>
                                    </table>
                                </div>
                                <div class="view-all-items">
                                    <button class="btn-view-all" onclick="viewAllInventoryItems({{ $inventory->id }})">
                                        {{ __('messages.view_all_list') }}
                                    </button>
                                </div>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        <div id="inventoriesPagination"></div>
        
        <!-- Мобильные карточки инвентаризации -->
        <div class="inventories-cards" id="inventoriesCards">
            <!-- Карточки будут загружаться через AJAX -->
        </div>
        <div id="mobileInventoriesPagination" style="display: none;"></div>
    </div>

    <!-- Модальное окно новой инвентаризации -->
    <div id="inventoryModal" class="modal">
        <div class="modal-content" style="width: 80%; max-width: 900px;">
            <div class="modal-header">
                <h2>{{ __('messages.new_inventory') }}</h2>
                <span class="close" onclick="closeInventoryModal()">&times;</span>
            </div>
            <div class="modal-body">
                <form id="inventoryForm">
                    @csrf
                    <div class="form-row">
                        <div class="form-group">
                            <label>{{ __('messages.date') }} </label>
                            <input type="date" name="date" required class="form-control"
                                   data-locale="{{ app()->getLocale() }}">
                        </div>
                        <div class="form-group">
                            <label>{{ __('messages.responsible') }} </label>
                            <select name="user_id" required class="form-control">
                                <option value="">{{ __('messages.select_responsible') }}</option>
                                @foreach($users as $user)
                                    <option value="{{ $user->id }}" {{ $user->id == $adminUserId ? 'selected' : '' }}>
                                        {{ $user->name }}
                                    </option>
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
                                <div class="form-group product-group large-col">
                                    <label>{{ __('messages.product') }}</label>
                                    <div class="product-search-container">
                                        <input type="text" class="product-search-input form-control large-col"
                                               placeholder="{{ __('messages.start_typing_product_name') }}"
                                               oninput="searchProducts(this)"
                                               onfocus="showProductDropdown(this)" autocomplete="off">
                                        <div class="product-dropdown" style="display: none;">
                                            <div class="product-dropdown-list"></div>
                                        </div>
                                        <select name="items[0][product_id]" class="form-control product-select large-col" style="display: none;">
                                            <option value="">{{ __('messages.select_product') }}</option>
                                            @foreach($products as $product)
                                                <option value="{{ $product['id'] }}" data-stock="{{ $product['stock'] }}">
                                                    {{ $product['name'] }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                                <div class="form-group small-col">
                                    <label>{{ __('messages.quantity_short') }}</label>
                                    <input type="number" name="items[0][actual_qty]" required class="form-control small-col" min="0" value="0">
                                </div>
                                <div class="form-group small-col">
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
                        <button type="button" class="btn-cancel" onclick="closeInventoryModal()">{{ __('messages.cancel') }}</button>
                        <button type="button" class="btn-submit" onclick="analyzeInventory()">{{ __('messages.conduct_inventory') }}</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Модальное окно анализа инвентаризации -->
    <div id="analysisModal" class="modal">
        <div class="modal-content" style="width: 80%; max-width: 900px;">
            <div class="modal-header">
                <h2>{{ __('messages.inventory_analysis') }}</h2>
                <span class="close" onclick="confirmCloseAnalysisModal()">&times;</span>
            </div>
            <div class="modal-body">
                <div class="analysis-summary">
                    <h3>{{ __('messages.inventory_results') }}</h3>
                    <div class="summary-stats">
                        <div class="stat-item">
                            <span class="stat-label">{{ __('messages.total_products') }}:</span>
                            <span class="stat-value" id="totalItems">0</span>
                        </div>
                        <div class="stat-item">
                            <span class="stat-label">{{ __('messages.matched_products') }}:</span>
                            <span class="stat-value" id="matchedItems">0</span>
                        </div>
                        <div class="stat-item">
                            <span class="stat-label">{{ __('messages.shortage_products') }}:</span>
                            <span class="stat-value" id="shortageItems">0</span>
                        </div>
                        <div class="stat-item">
                            <span class="stat-label">{{ __('messages.overage_products') }}:</span>
                            <span class="stat-value" id="overageItems">0</span>
                        </div>
                    </div>
                </div>
                <table class="table-striped analysis-table products-table">
                    <thead>
                    <tr>
                        <th>{{ __('messages.photo') }}</th>
                        <th class="large-col">{{ __('messages.product') }}</th>
                        <th class="small-col">{{ __('messages.warehouse_short') }}</th>
                        <th class="small-col">{{ __('messages.quantity') }}</th>
                        <th>{{ __('messages.difference') }}</th>
                        <th>{{ __('messages.status') }}</th>
                    </tr>
                    </thead>
                    <tbody id="analysisTableBody">
                    <!-- Строки будут добавлены динамически -->
                    </tbody>
                </table>
                <div class="form-actions">
                    <button type="button" class="btn-cancel" onclick="confirmCloseAnalysisModal()">{{ __('messages.back') }}</button>
                    <button type="button" class="btn-submit" onclick="saveInventory()">{{ __('messages.save_inventory') }}</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Модальное окно просмотра всех товаров инвентаризации -->
    <div id="viewAllItemsModal" class="modal">
        <div class="modal-content" style="width: 80%; max-width: 900px;">
            <div class="modal-header">
                <h2>{{ __('messages.all_inventory_products') }}</h2>
                <span class="close" onclick="closeViewAllItemsModal()">&times;</span>
            </div>
            <div class="modal-body" id="viewAllItemsModalBody">
                <!-- Контент будет загружен динамически -->
            </div>
        </div>
    </div>

    <!-- Модальное окно редактирования инвентаризации -->
    <div id="editInventoryModal" class="modal">
        <div class="modal-content" style="width: 80%; max-width: 900px;">
            <div class="modal-header">
                <h2>{{ __('messages.edit_inventory') }}</h2>
                <span class="close" onclick="closeEditInventoryModal()">&times;</span>
            </div>
            <div class="modal-body" id="editInventoryModalBody">
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
                <p>{{ __('messages.confirm_delete_inventory') }}</p>
            </div>
            <div class="modal-footer">
                <button id="cancelDelete" class="btn-cancel">{{ __('messages.cancel') }}</button>
                <button id="confirmDeleteBtn" class="btn-delete">{{ __('messages.delete') }}</button>
            </div>
        </div>
    </div>

    <!-- Модальное окно подтверждения отмены инвентаризации -->
    <div id="cancelInventoryModal" class="modal">
        <div class="modal-content">
            <div class="modal-header">
                <h2>{{ __('messages.cancel_confirmation') }}</h2>
                <span class="close" onclick="closeCancelInventoryModal()">&times;</span>
            </div>
            <div class="modal-body">
                <p>{{ __('messages.confirm_cancel_inventory') }}</p>
            </div>
            <div class="modal-footer">
                <button id="cancelCancelInventory" class="btn-cancel">{{ __('messages.cancel') }}</button>
                <button id="confirmCancelInventoryBtn" class="btn-delete">{{ __('messages.yes_cancel') }}</button>
            </div>
        </div>
    </div>

    <!-- Модальное окно подтверждения отмены редактирования инвентаризации -->
    <div id="cancelEditInventoryModal" class="modal">
        <div class="modal-content">
            <div class="modal-header">
                <h2>{{ __('messages.cancel_confirmation') }}</h2>
                <span class="close" onclick="closeCancelEditInventoryModal()">&times;</span>
            </div>
            <div class="modal-body">
                <p>{{ __('messages.confirm_cancel_edit_inventory') }}</p>
            </div>
            <div class="modal-footer">
                <button id="cancelCancelEditInventory" class="btn-cancel">{{ __('messages.cancel') }}</button>
                <button id="confirmCancelEditInventoryBtn" class="btn-delete">{{ __('messages.yes_cancel') }}</button>
            </div>
        </div>
    </div>

    <!-- Модальное окно для увеличенного фото -->
    <div id="zoomImageModal" class="modal" style="display:none; z-index: 9999; background: rgba(0,0,0,0.7);">
        <span class="close" id="closeZoomImageModal" style="position:absolute;top:10px;right:20px;font-size:2em;color:#fff;cursor:pointer;">&times;</span>
        <img id="zoomedImage" src="" alt="{{ __('messages.product_photo') }}" style="display:block;max-width:90vw;max-height:90vh;margin:40px auto;box-shadow:0 0 20px #000;border-radius:8px;">
    </div>
</div>

@push('scripts')
<script>
    // Инициализация глобальных переменных
    window.allProducts = @json($products);
    window.allUsers = @json($users);
    
    // Переводы для JavaScript
    window.messages = {
        fill_all_required_fields: '{{ __("messages.fill_all_required_fields") }}',
        select_product: '{{ __("messages.select_product") }}',
        product_already_added: '{{ __("messages.product_already_added") }}',
        add_at_least_one_product: '{{ __("messages.add_at_least_one_product") }}',
        error_loading_data: '{{ __("messages.error_loading_data") }}',
        error_saving_inventory: '{{ __("messages.error_saving_inventory") }}',
        inventory_successfully_saved: '{{ __("messages.inventory_successfully_saved") }}',
        inventory_successfully_updated: '{{ __("messages.inventory_successfully_updated") }}',
        inventory_successfully_deleted: '{{ __("messages.inventory_successfully_deleted") }}',
        error_updating_inventory: '{{ __("messages.error_updating_inventory") }}',
        error_deleting_inventory: '{{ __("messages.error_deleting_inventory") }}',
        inventory_cancelled: '{{ __("messages.inventory_cancelled") }}',
        edit_cancelled: '{{ __("messages.edit_cancelled") }}',
        changes_not_saved: '{{ __("messages.changes_not_saved") }}',
        products_not_found: '{{ __("messages.products_not_found") }}',
        no_photo: '{{ __("messages.no_photo") }}',
        matches_status: '{{ __("messages.matches_status") }}',
        overage_status: '{{ __("messages.overage_status") }}',
        shortage_status: '{{ __("messages.shortage_status") }}',
        units: '{{ __("messages.units") }}',
        edit_short: '{{ __("messages.edit_short") }}',
        delete: '{{ __("messages.delete") }}',
        view_all_list: '{{ __("messages.view_all_list") }}',
        shortage: '{{ __("messages.shortage") }}',
        overage: '{{ __("messages.overage") }}',
        matches: '{{ __("messages.matches") }}'
    };
</script>
<script src="{{ asset('client/js/inventories.js') }}"></script>
@endpush
               
@endsection
