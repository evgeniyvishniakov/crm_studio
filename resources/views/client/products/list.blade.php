@extends('client.layouts.app')

@section('content')
<div class="dashboard-container">
    <div class="products-container">
        <div class="products-header">
            <div class="header-top">
                <h1>{{ __('messages.products') }}</h1>
                <div class="header-actions">
                <button class="btn-export" onclick="openExportModal()">
                    <svg class="icon" viewBox="0 0 24 24" fill="currentColor">
                        <path d="M19 9h-4V3H9v6H5l7 7 7-7zM5 18v2h14v-2H5z"/>
                    </svg>
                    {{ __('messages.export') }}
                </button>
                <button class="btn-import" onclick="openImportModal()">
                    <svg class="icon" viewBox="0 0 24 24" fill="currentColor">
                        <path d="M19 13h-6v6h-2v-6H5v-2h6V5h2v6h6v2z"/>
                    </svg>
                    {{ __('messages.import') }}
                </button>
                <button class="btn-add-product" onclick="openModal()">
                    <svg class="icon" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd" d="M10 3a1 1 0 011 1v5h5a1 1 0 110 2h-5v5a1 1 0 11-2 0v-5H4a1 1 0 110-2h5V4a1 1 0 011-1z" clip-rule="evenodd" />
                    </svg>
                    {{ __('messages.add_product') }}
                </button>

                <div class="search-box">
                    <svg class="search-icon" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd" d="M8 4a4 4 0 100 8 4 4 0 000-8zM2 8a6 6 0 1110.89 3.476l4.817 4.817a1 1 0 01-1.414 1.414l-4.816-4.816A6 6 0 012 8z" clip-rule="evenodd" />
                    </svg>
                    <input type="text" id="searchInput" placeholder="{{ __('messages.search') }}..." onkeyup="handleSearch()">
                </div>
                
                <!-- Кнопка удаленных товаров -->
                <button id="deletedProductsBtn" class="btn-trash" onclick="showTrashedProducts()" title="{{ __('messages.show_deleted_products') }}" style="display: none;">
                    <svg class="icon" viewBox="0 0 24 24" fill="currentColor">
                        <path d="M6 19c0 1.1.9 2 2 2h8c1.1 0 2-.9 2-2V7H6v12zM19 4h-3.5l-1-1h-5l-1 1H5v2h14V4z"/>
                    </svg>
                    {{ __('messages.deleted_products') }}
                </button>
            </div>
            </div>
            
            <!-- Мобильная версия заголовка -->
            <div class="mobile-header">
                <h1 class="mobile-title">{{ __('messages.products') }}</h1>
                <div class="mobile-header-actions">
                    <button class="btn-export" onclick="openExportModal()">
                        <svg class="icon" viewBox="0 0 24 24" fill="currentColor">
                            <path d="M19 9h-4V3H9v6H5l7 7 7-7zM5 18v2h14v-2H5z"/>
                        </svg>
                        {{ __('messages.export') }}
                    </button>
                    <button class="btn-import" onclick="openImportModal()">
                        <svg class="icon" viewBox="0 0 24 24" fill="currentColor">
                            <path d="M19 13h-6v6h-2v-6H5v-2h6V5h2v6h6v2z"/>
                        </svg>
                        {{ __('messages.import') }}
                    </button>
                    <button class="btn-add-product" onclick="openModal()">
                        <svg class="icon" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd" d="M10 3a1 1 0 011 1v5h5a1 1 0 110 2h-5v5a1 1 0 11-2 0v-5H4a1 1 0 110-2h5V4a1 1 0 011-1z" clip-rule="evenodd" />
                        </svg>
                        {{ __('messages.add_product') }}
                    </button>

                    <div class="search-box">
                        <svg class="search-icon" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd" d="M8 4a4 4 0 100 8 4 4 0 000-8zM2 8a6 6 0 1110.89 3.476l4.817 4.817a1 1 0 01-1.414 1.414l-4.816-4.816A6 6 0 012 8z" clip-rule="evenodd" />
                        </svg>
                        <input type="text" id="searchInputMobile" placeholder="{{ __('messages.search') }}..." onkeyup="handleSearch()">
                    </div>
                    
                    <!-- Кнопка удаленных товаров -->
                    <button id="deletedProductsBtnMobile" class="btn-trash" onclick="showTrashedProducts()" title="{{ __('messages.show_deleted_products') }}" style="display: none;">
                        <svg class="icon" viewBox="0 0 24 24" fill="currentColor">
                            <path d="M6 19c0 1.1.9 2 2 2h8c1.1 0 2-.9 2-2V7H6v12zM19 4h-3.5l-1-1h-5l-1 1H5v2h14V4z"/>
                        </svg>
                        {{ __('messages.deleted_products') }}
                    </button>
                </div>
            </div>
        </div>

        <!-- Десктопная таблица -->
        <div class="table-wrapper">
            <table class="table-striped products-table">
                <thead>
                <tr>
                    <th>{{ __('messages.photo') }}</th>
                    <th>{{ __('messages.name') }}</th>
                    <th>{{ __('messages.category') }}</th>
                    <th>{{ __('messages.brand') }}</th>
                    <th>{{ __('messages.purchase_price') }}</th>
                    <th>{{ __('messages.retail_price') }}</th>
                    <th class="actions-column">{{ __('messages.actions') }}</th>
                </tr>
                </thead>
                <tbody id="productsTableBody">
                    <!-- Данные будут загружены через AJAX -->
                    <tr id="loading-row">
                        <td colspan="7" class="loading-indicator">
                            <div style="text-align: center; padding: 40px;">
                                <div style="width: 32px; height: 32px; border: 3px solid #f3f4f6; border-top: 3px solid #3b82f6; border-radius: 50%; animation: spin 1s linear infinite; margin: 0 auto 16px;"></div>
                                <p style="color: #6c7280; margin: 0;">Загрузка товаров...</p>
                            </div>
                        </td>
                    </tr>
                </tbody>
            </table>
            
            <!-- Пагинация будет добавлена через JavaScript -->
            <div id="productsPagination"></div>
        </div>

        <!-- Мобильные карточки товаров -->
        <div class="products-cards" id="productsCards" style="display: none;">
            <!-- Карточки будут добавлены через JavaScript -->
        </div>

        <!-- Пагинация для мобильных карточек -->
        <div id="mobileProductsPagination" style="display: none;"></div>
    </div>

    <!-- Модальное окно для добавления товара -->
    <div id="addProductModal" class="modal">
        <div class="modal-content">
            <div class="modal-header">
                <h2>{{ __('messages.add_new_product') }}</h2>
                <span class="close" onclick="closeModal()">&times;</span>
            </div>
            <div class="modal-body">
                <form id="addProductForm" enctype="multipart/form-data">
                    @csrf
                    <div class="form-group">
                        <label for="productName">{{ __('messages.name') }} *</label>
                        <input type="text" id="productName" name="name" required>
                    </div>
                    <div class="form-group">
                        <label for="productCategory">{{ __('messages.category') }} *</label>
                        <select id="productCategory" name="category_id" required class="form-control">
                            <option value="">{{ __('messages.select_category') }}</option>
                            @foreach($categories ?? [] as $category)
                                <option value="{{ $category->id }}">{{ $category->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="productBrand">{{ __('messages.brand') }} *</label>
                        <select id="productBrand" name="brand_id" required class="form-control">
                            <option value="">{{ __('messages.select_brand') }}</option>
                            @foreach($brands as $brand)
                                <option value="{{ $brand->id }}">{{ $brand->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="productPhoto">{{ __('messages.photo') }}</label>
                        <div class="logo-upload-controls">
                            <label for="productPhoto" class="btn btn-outline-secondary" style="cursor:pointer;display:inline-block;">{{ __('messages.choose_file') }}</label>
                            <input type="file" id="productPhoto" name="photo" accept="image/jpeg,image/png,image/jpg" style="display:none;" onchange="document.getElementById('product-filename').textContent = this.files[0]?.name || ''">
                            <span id="product-filename" style="margin-left:12px;font-size:0.95em;color:#888;"></span>
                            <small class="form-text text-muted">{{ __('messages.max_file_size_2mb') }} {{ __('messages.allowed_formats_jpeg_png_jpg') }}</small>
                        </div>
                    </div>
                    <div class="form-row">
                        <div class="form-group">
                            <label for="productPurchasePrice">{{ __('messages.purchase_price') }} *</label>
                            <input type="number" id="productPurchasePrice" name="purchase_price" min="0" step="0.01" required>
                        </div>
                        <div class="form-group">
                            <label for="productRetailPrice">{{ __('messages.retail_price') }} *</label>
                            <input type="number" id="productRetailPrice" name="retail_price" min="0" step="0.01" required>
                        </div>
                    </div>
                    <div class="form-actions">
                        <button type="button" class="btn-cancel" onclick="closeModal()">{{ __('messages.cancel') }}</button>
                        <button type="submit" class="btn-submit">{{ __('messages.add') }}</button>
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
                <p>{{ __('messages.are_you_sure_you_want_to_delete_this_product') }}</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn-cancel" id="cancelDelete">{{ __('messages.cancel') }}</button>
                <button type="button" class="btn-delete" id="confirmDelete">{{ __('messages.delete') }}</button>
            </div>
        </div>
    </div>

    <!-- Модальное окно подтверждения удаления всех товаров -->
    <div id="confirmationDeleteAllModal" class="modal">
        <div class="modal-content">
            <div class="modal-header">
                <h2>{{ __('messages.confirm_delete') }}</h2>
                <span class="close" onclick="document.getElementById('confirmationDeleteAllModal').style.display='none'">&times;</span>
            </div>
            <div class="modal-body">
                <p>{{ __('messages.are_you_sure_you_want_to_delete_all_products') }}</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn-cancel" id="cancelDeleteAll">{{ __('messages.cancel') }}</button>
                <button type="button" class="btn-delete" id="confirmDeleteAll">{{ __('messages.delete') }}</button>
            </div>
        </div>
    </div>

    <!-- Модальное окно подтверждения принудительного удаления товара -->
    <div id="confirmationForceDeleteModal" class="modal">
        <div class="modal-content">
            <div class="modal-header">
                <h2>{{ __('messages.confirm_delete') }}</h2>
                <span class="close" onclick="document.getElementById('confirmationForceDeleteModal').style.display='none'">&times;</span>
            </div>
            <div class="modal-body">
                <p>{{ __('messages.are_you_sure_you_want_to_permanently_delete_this_product') }}</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn-cancel" id="cancelForceDelete">{{ __('messages.cancel') }}</button>
                <button type="button" class="btn-delete" id="confirmForceDelete">{{ __('messages.delete') }}</button>
            </div>
        </div>
    </div>

    <!-- Модальное окно для редактирования товара -->
    <div id="editProductModal" class="modal">
        <div class="modal-content">
            <div class="modal-header">
                <h2>{{ __('messages.edit_product') }}</h2>
                <span class="close" onclick="closeEditModal()">&times;</span>
            </div>
            <div class="modal-body">
                <form id="editProductForm" enctype="multipart/form-data">
                    @csrf
                    @method('PUT')
                    <input type="hidden" id="editProductId" name="id">
                    <div class="form-group">
                        <label for="editProductName">{{ __('messages.name') }} *</label>
                        <input type="text" id="editProductName" name="name" required>
                    </div>
                    <div class="form-group">
                        <label for="editProductCategory">{{ __('messages.category') }} *</label>
                        <select id="editProductCategory" name="category_id" required class="form-control">
                            <option value="">{{ __('messages.select_category') }}</option>
                            @foreach($categories ?? [] as $category)
                                <option value="{{ $category->id }}">{{ $category->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="editProductBrand">{{ __('messages.brand') }} *</label>
                        <select id="editProductBrand" name="brand_id" required class="form-control">
                            <option value="">{{ __('messages.select_brand') }}</option>
                            @foreach($brands as $brand)
                                <option value="{{ $brand->id }}">{{ $brand->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="editProductPhoto">{{ __('messages.photo') }}</label>
                        <div class="logo-upload-controls">
                            <label for="editProductPhoto" class="btn btn-outline-secondary" style="cursor:pointer;display:inline-block;">{{ __('messages.choose_file') }}</label>
                            <input type="file" id="editProductPhoto" name="photo" accept="image/jpeg,image/png,image/jpg" style="display:none;" onchange="document.getElementById('edit-product-filename').textContent = this.files[0]?.name || ''">
                            <span id="edit-product-filename" style="margin-left:12px;font-size:0.95em;color:#888;"></span>
                            <small class="form-text text-muted">{{ __('messages.max_file_size_2mb') }} {{ __('messages.allowed_formats_jpeg_png_jpg') }}</small>
                        </div>
                        <div id="currentPhotoContainer" class="mt-2"></div>
                    </div>
                    <div class="form-row">
                        <div class="form-group">
                            <label for="editProductPurchasePrice">{{ __('messages.purchase_price') }} *</label>
                            <input type="number" id="editProductPurchasePrice" name="purchase_price" min="0" step="0.01" required>
                        </div>
                        <div class="form-group">
                            <label for="editProductRetailPrice">{{ __('messages.retail_price') }} *</label>
                            <input type="number" id="editProductRetailPrice" name="retail_price" min="0" step="0.01" required>
                        </div>
                    </div>
                    <div class="form-actions">
                        <button type="button" class="btn-cancel" onclick="closeEditModal()">{{ __('messages.cancel') }}</button>
                        <button type="submit" class="btn-submit">{{ __('messages.save') }}</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Модальное окно для импорта товаров -->
    <div id="importModal" class="modal">
        <div class="modal-content">
            <div class="modal-header">
                <h2>{{ __('messages.import_products') }}</h2>
                <span class="close" onclick="closeImportModal()">&times;</span>
            </div>
            <div class="modal-body">
                <div class="import-info">
                    <h3>{{ __('messages.import_info') }}</h3>
                    <ul>
                        <li>{{ __('messages.file_must_contain_columns') }}</li>
                        <li><strong>{{ __('messages.multilingual_column_support') }}</strong></li>
                        <li><strong>{{ __('messages.column_names_examples') }}</strong></li>
                        <ul style="margin-left: 20px; margin-top: 5px; color: #666; font-size: 0.9em;">
                            @php 
                                $currentLocale = app()->getLocale();
                                $examples = [
                                    'ru' => [
                                        'name' => ['название', 'name', 'title', 'назва', 'nazvanie'],
                                        'category' => ['категория', 'category', 'категорія', 'kategoriia'],
                                        'brand' => ['бренд', 'brand', 'марка', 'brend'],
                                        'purchase_price' => ['оптовая цена', 'purchase_price', 'оптова ціна', 'optovaia_cena'],
                                        'retail_price' => ['розничная цена', 'retail_price', 'рознична ціна', 'roznicnaia_cena'],
                                        'photo' => ['фото', 'photo', 'зображення', 'foto'],
                                    ],
                                    'en' => [
                                        'name' => ['name', 'title', 'product_name'],
                                        'category' => ['category', 'product_category'],
                                        'brand' => ['brand', 'product_brand'],
                                        'purchase_price' => ['purchase_price', 'wholesale_price', 'cost_price'],
                                        'retail_price' => ['retail_price', 'selling_price', 'price'],
                                        'photo' => ['photo', 'image', 'picture'],
                                    ],
                                    'ua' => [
                                        'name' => ['назва', 'name', 'title', 'название', 'nazvanie'],
                                        'category' => ['категорія', 'category', 'категория', 'kategoriia'],
                                        'brand' => ['бренд', 'brand', 'марка', 'brend'],
                                        'purchase_price' => ['оптова ціна', 'purchase_price', 'оптовая цена', 'optovaia_cena'],
                                        'retail_price' => ['рознична ціна', 'retail_price', 'розничная цена', 'roznicnaia_cena'],
                                        'photo' => ['зображення', 'photo', 'фото', 'foto'],
                                    ],
                                ];
                                $currentExamples = $examples[$currentLocale] ?? $examples['en'];
                            @endphp
                            <li>{{ __('messages.column_name_name') }}: {{ implode(', ', $currentExamples['name']) }}</li>
                            <li>{{ __('messages.column_name_category') }}: {{ implode(', ', $currentExamples['category']) }}</li>
                            <li>{{ __('messages.column_name_brand') }}: {{ implode(', ', $currentExamples['brand']) }}</li>
                            <li>{{ __('messages.column_name_purchase_price') }}: {{ implode(', ', $currentExamples['purchase_price']) }}</li>
                            <li>{{ __('messages.column_name_retail_price') }}: {{ implode(', ', $currentExamples['retail_price']) }}</li>
                            <li>{{ __('messages.column_name_photo') }}: {{ implode(', ', $currentExamples['photo']) }}</li>
                        </ul>
                        <li>{{ __('messages.if_category_or_brand_not_specified') }}</li>
                        <li>{{ __('messages.photo_column') }}</li>
                        <ul style="margin-left: 20px; margin-top: 5px;">
                            <li>{{ __('messages.insert_image_link_in_photo_column') }}</li>
                            <li>{{ __('messages.supported_link_formats_http_https_jpg_jpeg_png') }}</li>
                            <li>{{ __('messages.can_simply_paste_hyperlink') }}</li>
                        </ul>
                        <li>{{ __('messages.supported_file_formats_excel_xlsx_xls_csv_csv') }}</li>
                        <li>{{ __('messages.max_file_size_5mb') }}</li>
                    </ul>
                </div>
                
                <form id="importForm" enctype="multipart/form-data">
                    <div class="form-group">
                        <label for="importFile">{{ __('messages.select_file') }}:</label>
                        <div class="logo-upload-controls">
                            <label for="importFile" class="btn btn-outline-secondary" style="cursor:pointer;display:inline-block;">{{ __('messages.choose_file') }}</label>
                            <input type="file" id="importFile" name="file" accept=".xlsx,.xls,.csv" required style="display:none;" onchange="document.getElementById('import-filename').textContent = this.files[0]?.name || ''">
                            <span id="import-filename" style="margin-left:12px;font-size:0.95em;color:#888;"></span>
                            <small class="form-text text-muted">{{ __('messages.excel_xlsx_xls_csv_max_size_5mb') }}</small>
                        </div>
                    </div>
                    
                    <div class="form-actions">
                        <button type="button" class="btn-cancel" onclick="closeImportModal()">{{ __('messages.cancel') }}</button>
                        <button type="submit" class="btn-submit">{{ __('messages.import') }}</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Модальное окно экспорта -->
    <div id="exportModal" class="modal">
        <div class="modal-content">
            <div class="modal-header">
                <h2>{{ __('messages.export_products') }}</h2>
                <span class="close" onclick="closeExportModal()">&times;</span>
            </div>
            <div class="modal-body">
                <div class="import-info">
                    <h3>{{ __('messages.export_to_excel') }}</h3>
                    <ul>
                        <li>{{ __('messages.select_filters_for_export') }}</li>
                        <li>{{ __('messages.file_will_be_downloaded_in_xlsx_format') }}</li>
                        <li>{{ __('messages.will_open_in_excel_without_problems_with_cyrillic_and_separators') }}</li>
                    </ul>
                </div>
                <form id="exportForm" onsubmit="event.preventDefault(); exportProducts();">
                    <div class="export-filters-row">
                        <div class="form-group">
                            <label for="exportCategory">{{ __('messages.category') }}:</label>
                            <select id="exportCategory" name="category_id">
                                <option value="">{{ __('messages.all') }}</option>
                                @foreach($categories ?? [] as $cat)
                                    <option value="{{ $cat->id }}">{{ $cat->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="exportBrand">{{ __('messages.brand') }}:</label>
                            <select id="exportBrand" name="brand_id">
                                <option value="">{{ __('messages.all') }}</option>
                                @foreach($brands as $brand)
                                    <option value="{{ $brand->id }}">{{ $brand->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="exportPhoto">{{ __('messages.photo') }}:</label>
                            <select id="exportPhoto" name="photo">
                                <option value="all">{{ __('messages.all_products') }}</option>
                                <option value="with">{{ __('messages.only_with_photo') }}</option>
                                <option value="without">{{ __('messages.only_without_photo') }}</option>
                            </select>
                        </div>
                    </div>
                    <div class="form-actions">
                        <button type="button" class="btn-cancel" onclick="closeExportModal()">{{ __('messages.cancel') }}</button>
                        <button type="submit" class="btn-submit">{{ __('messages.download_excel') }}</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Модальное окно для увеличенного просмотра фото -->
    <div id="imageModal" class="modal" style="display:none;position:fixed;z-index:9999;left:0;top:0;width:100vw;height:100vh;background:rgba(0,0,0,0.7);align-items:center;justify-content:center;">
        <span id="closeImageModal" style="position:absolute;top:30px;right:50px;font-size:40px;color:#fff;cursor:pointer;z-index:10001;">&times;</span>
        <img id="modalImage" src="" style="max-width:90vw;max-height:90vh;box-shadow:0 0 20px #000;border-radius:8px;z-index:10000;">
    </div>

    <!-- Модальное окно для удаленных товаров -->
    <div id="trashedModal" class="modal">
        <div class="modal-content">
            <div class="modal-header">
                <h2>{{ __('messages.deleted_products') }}</h2>
                <span class="close" onclick="closeTrashedModal()">&times;</span>
            </div>
            <div class="modal-body">
                <div id="trashedProductsList">
                    <div class="loading">{{ __('messages.loading') }}...</div>
                </div>
                <div class="modal-footer" style="margin-top: 20px; text-align: center; display: none;" id="trashedModalFooter">
                                            <button onclick="showDeleteAllConfirmation()" class="btn-force-delete-all">
                        {{ __('messages.delete_all_products') }}
                    </button>
                </div>
            </div>
        </div>
    </div>

@endsection

@push('scripts')
    <script>
        // Инициализация глобальных переменных
        window.allCategories = @json($categories ?? []);
        window.allBrands = @json($brands);
        
        // Переводы для JavaScript
        window.translations = {
            no_photo: '{{ __("messages.no_photo") }}',
            photo: '{{ __("messages.photo") }}',
            name: '{{ __("messages.name") }}',
            category: '{{ __("messages.category") }}',
            brand: '{{ __("messages.brand") }}',
            purchase_price: '{{ __("messages.purchase_price") }}',
            retail_price: '{{ __("messages.retail_price") }}',
            edit: '{{ __("messages.edit") }}',
            delete: '{{ __("messages.delete") }}',
            cancel: '{{ __("messages.cancel") }}',
            save: '{{ __("messages.save") }}',
            add: '{{ __("messages.add") }}',
            confirm_delete: '{{ __("messages.confirm_delete") }}',
            are_you_sure_you_want_to_delete_this_product: '{{ __("messages.are_you_sure_you_want_to_delete_this_product") }}',
            are_you_sure_you_want_to_delete_all_products: '{{ __("messages.are_you_sure_you_want_to_delete_all_products") }}',
            are_you_sure_you_want_to_permanently_delete_this_product: '{{ __("messages.are_you_sure_you_want_to_permanently_delete_this_product") }}',
            select_category: '{{ __("messages.select_category") }}',
            select_brand: '{{ __("messages.select_brand") }}',
            choose_file: '{{ __("messages.choose_file") }}',
            max_file_size_2mb: '{{ __("messages.max_file_size_2mb") }}',
            allowed_formats_jpeg_png_jpg: '{{ __("messages.allowed_formats_jpeg_png_jpg") }}',
            import_products: '{{ __("messages.import_products") }}',
            export_products: '{{ __("messages.export_products") }}',
            import: '{{ __("messages.import") }}',
            export: '{{ __("messages.export") }}',
            download_excel: '{{ __("messages.download_excel") }}',
            export_to_excel: '{{ __("messages.export_to_excel") }}',
            select_filters_for_export: '{{ __("messages.select_filters_for_export") }}',
            file_will_be_downloaded_in_xlsx_format: '{{ __("messages.file_will_be_downloaded_in_xlsx_format") }}',
            will_open_in_excel_without_problems_with_cyrillic_and_separators: '{{ __("messages.will_open_in_excel_without_problems_with_cyrillic_and_separators") }}',
            all: '{{ __("messages.all") }}',
            all_products: '{{ __("messages.all_products") }}',
            only_with_photo: '{{ __("messages.only_with_photo") }}',
            only_without_photo: '{{ __("messages.only_without_photo") }}',
            deleted_products: '{{ __("messages.deleted_products") }}',
            loading: '{{ __("messages.loading") }}',
            delete_all_products: '{{ __("messages.delete_all_products") }}',
            excel_xlsx_xls_csv_max_size_5mb: '{{ __("messages.excel_xlsx_xls_csv_max_size_5mb") }}'
        };
    </script>
    <script src="{{ asset('client/js/products.js') }}"></script>
@endpush
