@extends('client.layouts.app')

@section('content')
<div class="dashboard-container">
    <div class="products-container">
        <div class="products-header">
            <h1>{{ __('messages.products') }}</h1>
            <div id="notification" class="notification alert alert-success" role="alert">
                <svg class="notification-icon" viewBox="0 0 24 24" fill="currentColor">
                    <path d="M9 16.17L4.83 12l-1.42 1.41L9 19 21 7l-1.41-1.41L9 16.17z"/>
                </svg>
                <span class="notification-message">{{ __('messages.product_successfully_added') }}</span>
            </div>
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
                <button id="deletedProductsBtn" class="btn-trash" onclick="showTrashedProducts()" title="{{ __('messages.show_deleted_products') }}" style="display: none;">
                    <svg class="icon" viewBox="0 0 24 24" fill="currentColor">
                        <path d="M6 19c0 1.1.9 2 2 2h8c1.1 0 2-.9 2-2V7H6v12zM19 4h-3.5l-1-1h-5l-1 1H5v2h14V4z"/>
                    </svg>
                    {{ __('messages.deleted_products') }}
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
            </div>
        </div>

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
                @foreach($products as $product)
                    <tr id="product-{{ $product->id }}">
                        <td>
                            @if($product->photo)
                                <a href="{{ Storage::url($product->photo) }}" class="zoomable-image" data-img="{{ Storage::url($product->photo) }}">
                                    <img src="{{ Storage::url($product->photo) }}" alt="{{ $product->name }}" class="product-photo">
                                </a>
                            @else
                                <div class="no-photo">{{ __('messages.no_photo') }}</div>
                            @endif
                        </td>
                        <td>{{ $product->name }}</td>
                        <td>{{ $product->category->name ?? '—' }}</td>
                        <td>{{ $product->brand->name ?? '—' }}</td>
                        <td class="currency-amount" data-amount="{{ $product->purchase_price }}">
                            @if(fmod($product->purchase_price, 1) == 0)
                                {!! \App\Helpers\CurrencyHelper::getSymbol() !!}{{ (int)$product->purchase_price }}
                            @else
                                {!! \App\Helpers\CurrencyHelper::getSymbol() !!}{{ number_format($product->purchase_price, 2) }}
                            @endif
                        </td>
                        <td class="currency-amount" data-amount="{{ $product->retail_price }}">
                            @if(fmod($product->retail_price, 1) == 0)
                                {!! \App\Helpers\CurrencyHelper::getSymbol() !!}{{ (int)$product->retail_price }}
                            @else
                                {!! \App\Helpers\CurrencyHelper::getSymbol() !!}{{ number_format($product->retail_price, 2) }}
                            @endif
                        </td>
                        <td class="actions-cell">
                            <button class="btn-edit" title="{{ __('messages.edit') }}">
                                <svg class="icon" viewBox="0 0 20 20" fill="currentColor">
                                    <path d="M13.586 3.586a2 2 0 112.828 2.828l-.793.793-2.828-2.828.793-.793zM11.379 5.793L3 14.172V17h2.828l8.38-8.379-2.83-2.828z" />
                                </svg>
                            </button>
                            <button class="btn-delete" title="{{ __('messages.delete') }}">
                                <svg class="icon" viewBox="0 0 20 20" fill="currentColor">
                                    <path fill-rule="evenodd" d="M9 2a1 1 0 00-.894.553L7.382 4H4a1 1 0 000 2v10a2 2 0 002 2h8a2 2 0 002-2V6a1 1 0 100-2h-3.382l-.724-1.447A1 1 0 0011 2H9zM7 8a1 1 0 012 0v6a1 1 0 11-2 0V8zm5-1a1 1 0 00-1 1v6a1 1 0 102 0V8a1 1 0 00-1-1z" clip-rule="evenodd" />
                                </svg>
                            </button>
                        </td>
                    </tr>
                @endforeach
                </tbody>
            </table>
            
            <!-- Пагинация будет добавлена через JavaScript -->
            <div id="productsPagination"></div>
        </div>
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
                        <input type="file" id="productPhoto" name="photo" accept="image/jpeg,image/png,image/jpg">
                        <small class="form-text text-muted">{{ __('messages.max_file_size_2mb') }} {{ __('messages.allowed_formats_jpeg_png_jpg') }}</small>
                    </div>
                    <div class="form-group">
                        <label for="productPurchasePrice">{{ __('messages.purchase_price') }} *</label>
                        <input type="number" id="productPurchasePrice" name="purchase_price" min="0" step="0.01" required>
                    </div>
                    <div class="form-group">
                        <label for="productRetailPrice">{{ __('messages.retail_price') }} *</label>
                        <input type="number" id="productRetailPrice" name="retail_price" min="0" step="0.01" required>
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
    <div id="confirmationModal" class="confirmation-modal">
        <div class="confirmation-content">
            <h3>{{ __('messages.confirmation_delete') }}</h3>
            <p>{{ __('messages.are_you_sure_you_want_to_delete_this_product') }}</p>
            <div class="confirmation-buttons">
                <button id="cancelDelete" class="cancel-btn">{{ __('messages.cancel') }}</button>
                <button id="confirmDelete" class="confirm-btn">{{ __('messages.delete') }}</button>
            </div>
        </div>
    </div>

    <!-- Модальное окно подтверждения удаления всех товаров -->
    <div id="confirmationDeleteAllModal" class="confirmation-modal">
        <div class="confirmation-content">
            <h3>{{ __('messages.confirmation_delete') }}</h3>
            <p>{{ __('messages.are_you_sure_you_want_to_delete_all_products') }}</p>
            <div class="confirmation-buttons">
                <button id="cancelDeleteAll" class="cancel-btn">{{ __('messages.cancel') }}</button>
                <button id="confirmDeleteAll" class="confirm-btn">{{ __('messages.delete') }}</button>
            </div>
        </div>
    </div>

    <!-- Модальное окно подтверждения принудительного удаления товара -->
    <div id="confirmationForceDeleteModal" class="confirmation-modal">
        <div class="confirmation-content">
            <h3>{{ __('messages.confirmation_delete') }}</h3>
            <p>{{ __('messages.are_you_sure_you_want_to_permanently_delete_this_product') }}</p>
            <div class="confirmation-buttons">
                <button id="cancelForceDelete" class="cancel-btn">{{ __('messages.cancel') }}</button>
                <button id="confirmForceDelete" class="confirm-btn">{{ __('messages.delete') }}</button>
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
                        <input type="file" id="editProductPhoto" name="photo" accept="image/jpeg,image/png,image/jpg">
                        <small class="form-text text-muted">{{ __('messages.max_file_size_2mb') }} {{ __('messages.allowed_formats_jpeg_png_jpg') }}</small>
                        <div id="currentPhotoContainer" class="mt-2"></div>
                    </div>
                    <div class="form-group">
                        <label for="editProductPurchasePrice">{{ __('messages.purchase_price') }} *</label>
                        <input type="number" id="editProductPurchasePrice" name="purchase_price" min="0" step="0.01" required>
                    </div>
                    <div class="form-group">
                        <label for="editProductRetailPrice">{{ __('messages.retail_price') }} *</label>
                        <input type="number" id="editProductRetailPrice" name="retail_price" min="0" step="0.01" required>
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

    <style>
        .btn-add-product {
            display: flex;
            align-items: center;
            gap: 8px;
            padding: 10px 16px;
            background: linear-gradient(135deg, #3b82f6, #60a5fa);
            color: white;
            border: 2px solid #3b82f6;
            border-radius: 12px;
            cursor: pointer;
            font-size: 14px;
            font-weight: 600;
            transition: all 0.3s ease;
            box-shadow: 0 2px 8px rgba(59, 130, 246, 0.15);
        }
        .btn-add-product:hover {
            transform: translateY(-2px);
            background: linear-gradient(135deg, #2563eb, #3b82f6);
            border-color: #2563eb;
            box-shadow: 0 4px 12px rgba(59, 130, 246, 0.3);
        }
        .btn-export {
            display: flex;
            align-items: center;
            gap: 8px;
            padding: 10px 16px;
            background: linear-gradient(135deg, #28a745, #34d399);
            color: white;
            border: 2px solid #28a745;
            border-radius: 12px;
            cursor: pointer;
            font-size: 14px;
            font-weight: 600;
            transition: all 0.3s ease;
            box-shadow: 0 2px 8px rgba(40, 167, 69, 0.15);
        }
        .btn-export:hover {
            transform: translateY(-2px);
            background: linear-gradient(135deg, #218838, #28a745);
            border-color: #218838;
            box-shadow: 0 4px 12px rgba(40, 167, 69, 0.3);
        }
        .btn-import {
            display: flex;
            align-items: center;
            gap: 8px;
            padding: 10px 16px;
            background: linear-gradient(135deg, #ffc107, #ffdb4d);
            color: #212529;
            border: 2px solid #ffc107;
            border-radius: 12px;
            cursor: pointer;
            font-size: 14px;
            font-weight: 600;
            text-decoration: none;
            transition: all 0.3s ease;
            box-shadow: 0 2px 8px rgba(255, 193, 7, 0.15);
        }
        .btn-import:hover {
            transform: translateY(-2px);
            background: linear-gradient(135deg, #e0a800, #ffc107);
            border-color: #e0a800;
            box-shadow: 0 4px 12px rgba(255, 193, 7, 0.3);
            color: #212529;
            text-decoration: none;
        }
        .btn-trash {
            display: flex;
            align-items: center;
            gap: 8px;
            padding: 10px 16px;
            background: linear-gradient(135deg, #dc3545, #e74c3c);
            color: white;
            border: 2px solid #dc3545;
            border-radius: 12px;
            cursor: pointer;
            font-size: 14px;
            font-weight: 600;
            transition: all 0.3s ease;
            box-shadow: 0 2px 8px rgba(220, 53, 69, 0.15);
        }
        .btn-trash:hover {
            transform: translateY(-2px);
            background: linear-gradient(135deg, #c0392b, #dc3545);
            border-color: #c0392b;
            box-shadow: 0 4px 12px rgba(220, 53, 69, 0.3);
        }

        /* Специальные стили для модального окна импорта */
        #importModal .modal-content {
            width: 95%;
            max-width: 800px;
            margin: 3% auto;
        }

        #importModal .modal-body {
            padding: 30px;
        }

        #importModal .import-info {
            background-color: #f8f9fa;
            border-radius: 8px;
            padding: 20px;
            margin-bottom: 25px;
            border-left: 4px solid #007bff;
        }

        #importModal .import-info h3 {
            color: #007bff;
            margin-bottom: 15px;
            font-size: 18px;
            font-weight: 600;
        }

        #importModal .import-info ul {
            margin: 0;
            padding-left: 20px;
        }

        #importModal .import-info ul ul {
            margin-top: 8px;
            margin-bottom: 8px;
            background-color: #ffffff;
            border-radius: 6px;
            padding: 12px 16px;
            border: 1px solid #e9ecef;
        }

        #importModal .import-info ul ul li {
            margin-bottom: 4px;
            font-family: 'Courier New', monospace;
            color: #495057;
        }

        #importModal .import-info ul ul li:last-child {
            margin-bottom: 0;
        }

        /* Стили для модального окна удаленных товаров */
        .trashed-product-item {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 15px;
            border: 1px solid #e9ecef;
            border-radius: 8px;
            margin-bottom: 10px;
            background-color: #f8f9fa;
        }

        .trashed-product-item .product-info {
            flex: 1;
        }

        .trashed-product-item .product-info strong {
            display: block;
            color: #495057;
            margin-bottom: 5px;
        }

        .trashed-product-item .product-info small {
            display: block;
            color: #6c757d;
            font-size: 0.85em;
            margin-bottom: 2px;
        }

        .trashed-product-item .product-actions {
            display: flex;
            gap: 10px;
        }

        .btn-restore {
            padding: 8px 16px;
            background: linear-gradient(135deg, #28a745, #34d399);
            color: white;
            border: none;
            border-radius: 6px;
            cursor: pointer;
            font-size: 12px;
            font-weight: 600;
            transition: all 0.3s ease;
        }

        .btn-restore:hover {
            background: linear-gradient(135deg, #218838, #28a745);
            transform: translateY(-1px);
        }

        .btn-force-delete {
            padding: 8px 16px;
            background: linear-gradient(135deg, #dc3545, #e74c3c);
            color: white;
            border: none;
            border-radius: 6px;
            cursor: pointer;
            font-size: 12px;
            font-weight: 600;
            transition: all 0.3s ease;
        }

        .btn-force-delete:hover {
            background: linear-gradient(135deg, #c0392b, #dc3545);
            transform: translateY(-1px);
        }

        .btn-force-delete-all {
            background: linear-gradient(135deg, #dc3545, #e74c3c);
            color: white;
            border: 2px solid #dc3545;
            border-radius: 12px;
            cursor: pointer;
            font-size: 14px;
            font-weight: 600;
            padding: 12px 24px;
            transition: all 0.3s ease;
            box-shadow: 0 2px 8px rgba(220, 53, 69, 0.15);
        }

        .btn-force-delete-all:hover {
            transform: translateY(-2px);
            background: linear-gradient(135deg, #c0392b, #dc3545);
            border-color: #c0392b;
            box-shadow: 0 4px 12px rgba(220, 53, 69, 0.3);
        }

        .no-data {
            text-align: center;
            color: #6c757d;
            font-style: italic;
            padding: 20px;
        }

        .error {
            text-align: center;
            color: #dc3545;
            padding: 20px;
        }

        .loading {
            text-align: center;
            color: #6c757d;
            padding: 20px;
        }

        #importModal .form-group {
            margin-bottom: 20px;
        }

        #importModal .form-group label {
            display: block;
            margin-bottom: 8px;
            font-weight: 500;
            color: #495057;
            text-align: left;
        }

        #importModal .form-actions {
            display: flex;
            justify-content: flex-end;
            gap: 15px;
            margin-top: 30px;
            padding-top: 20px;
            border-top: 1px solid #e9ecef;
        }

        #importModal .btn-submit {
            background: linear-gradient(135deg, #28a745, #34d399);
            color: white;
            border: 2px solid #28a745;
            border-radius: 8px;
            font-weight: 600;
            transition: all 0.3s ease;
            box-shadow: 0 2px 8px rgba(40, 167, 69, 0.15);
        }

        #importModal .btn-submit:hover {
            transform: translateY(-2px);
            background: linear-gradient(135deg, #218838, #28a745);
            border-color: #218838;
            box-shadow: 0 4px 12px rgba(40, 167, 69, 0.3);
        }

        #importModal .btn-cancel {
            background: linear-gradient(135deg, #6c757d, #9ca3af);
            color: white;
            border: 2px solid #6c757d;
            border-radius: 8px;
            font-weight: 600;
            transition: all 0.3s ease;
            box-shadow: 0 2px 8px rgba(108, 117, 125, 0.15);
        }

        #importModal .btn-cancel:hover {
            transform: translateY(-2px);
            background: linear-gradient(135deg, #5a6268, #6c757d);
            border-color: #5a6268;
            box-shadow: 0 4px 12px rgba(108, 117, 125, 0.3);
        }

        #exportModal .modal-content {
            width: 95%;
            max-width: 800px;
            margin: 3% auto;
        }
        #exportModal .modal-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            border-bottom: 1px solid #e9ecef;
            padding-bottom: 10px;
        }
        #exportModal .modal-body {
            padding: 30px;
        }
        #exportModal .import-info {
            background-color: #f8f9fa;
            border-radius: 8px;
            padding: 20px;
            margin-bottom: 25px;
            border-left: 4px solid #007bff;
        }
        #exportModal .import-info h3 {
            color: #007bff;
            margin-bottom: 15px;
            font-size: 18px;
            font-weight: 600;
        }
        #exportModal .import-info ul {
            margin: 0;
            padding-left: 20px;
        }
        #exportModal .import-info li {
            margin-bottom: 8px;
            line-height: 1.5;
            color: #495057;
            text-align: left;
        }
        #exportModal .import-info ul ul {
            margin-top: 10px;
            margin-bottom: 10px;
        }
        #exportModal .import-info ul ul li {
            margin-bottom: 5px;
            font-size: 13px;
            color: #6c757d;
        }
        #exportModal .form-group {
            margin-bottom: 20px;
        }
        #exportModal .form-group label {
            display: block;
            margin-bottom: 8px;
            font-weight: 500;
            color: #495057;
            text-align: left;
        }
        #exportModal .form-actions {
            display: flex;
            justify-content: flex-end;
            gap: 15px;
            margin-top: 30px;
            padding-top: 20px;
            border-top: 1px solid #e9ecef;
        }
        #exportModal .btn-submit {
            background: linear-gradient(135deg, #28a745, #34d399);
            color: white;
            border: 2px solid #28a745;
            border-radius: 8px;
            font-weight: 600;
            transition: all 0.3s ease;
            box-shadow: 0 2px 8px rgba(40, 167, 69, 0.15);
        }
        #exportModal .btn-submit:hover {
            transform: translateY(-2px);
            background: linear-gradient(135deg, #218838, #28a745);
            border-color: #218838;
            box-shadow: 0 4px 12px rgba(40, 167, 69, 0.3);
        }
        #exportModal .btn-cancel {
            background: linear-gradient(135deg, #6c757d, #9ca3af);
            color: white;
            border: 2px solid #6c757d;
            border-radius: 8px;
            font-weight: 600;
            transition: all 0.3s ease;
            box-shadow: 0 2px 8px rgba(108, 117, 125, 0.15);
        }
        #exportModal .btn-cancel:hover {
            transform: translateY(-2px);
            background: linear-gradient(135deg, #5a6268, #6c757d);
            border-color: #5a6268;
            box-shadow: 0 4px 12px rgba(108, 117, 125, 0.3);
        }
        .export-filters-row {
            display: flex;
            gap: 20px;
            align-items: flex-end;
            margin-bottom: 20px;
        }
        .export-filters-row .form-group {
            margin-bottom: 0;
            min-width: 180px;
        }
        .export-filters-row .form-group label {
            margin-bottom: 6px;
            display: block;
        }
    </style>

    <script>
        // Функция форматирования валюты
        function formatCurrency(value) {
            if (window.CurrencyManager) {
                return window.CurrencyManager.formatAmount(value);
            } else {
                value = parseFloat(value);
                if (isNaN(value)) return '0';
                const symbol = '{{ \App\Helpers\CurrencyHelper::getSymbol() }}';
                return (value % 1 === 0 ? Math.floor(value) : value.toFixed(2)) + ' ' + symbol;
            }
        }

        // Основные функции управления модальными окнами
        function openModal() {
            document.getElementById('addProductModal').style.display = 'block';
        }

        function closeModal() {
            document.getElementById('addProductModal').style.display = 'none';
            clearErrors();
        }

        function closeEditModal() {
            document.getElementById('editProductModal').style.display = 'none';
        }

        // Закрытие модального окна при клике вне его
        window.onclick = function(event) {
            if (event.target == document.getElementById('addProductModal')) {
                closeModal();
            }
            if (event.target == document.getElementById('editProductModal')) {
                closeEditModal();
            }
            if (event.target == document.getElementById('confirmationModal')) {
                document.getElementById('confirmationModal').style.display = 'none';
                currentDeleteRow = null;
                currentDeleteId = null;
            }
        }

        // Функция для очистки ошибок
        function clearErrors(formId = null) {
            const form = formId ? document.getElementById(formId) : document.getElementById('addProductForm');
            if (form) {
                form.querySelectorAll('.error-message').forEach(el => el.remove());
                form.querySelectorAll('.has-error').forEach(el => {
                    el.classList.remove('has-error');
                });
            }
        }

        // Функция для отображения ошибок
        function showErrors(errors, formId = 'addProductForm') {
            clearErrors(formId);

            Object.entries(errors).forEach(([field, messages]) => {
                const input = document.querySelector(`#${formId} [name="${field}"]`);
                if (input) {
                    const inputGroup = input.closest('.form-group');
                    inputGroup.classList.add('has-error');

                    const errorElement = document.createElement('div');
                    errorElement.className = 'error-message';
                    errorElement.textContent = Array.isArray(messages) ? messages[0] : messages;

                    inputGroup.appendChild(errorElement);
                }
            });
        }


        // Глобальные переменные для удаления
        let currentDeleteRow = null;
        let currentDeleteId = null;

        // Исправляю обработчик клика по кнопке удаления
        // Теперь только открываем модальное окно, а удаление происходит после подтверждения

        document.addEventListener('click', function(e) {
            if (e.target.closest('.btn-delete')) {
                const row = e.target.closest('tr');
                const productId = row.id.split('-')[1];
                currentDeleteRow = row;
                currentDeleteId = productId;
                document.getElementById('confirmationModal').style.display = 'block';
            }
        });

        // Кнопка подтверждения удаления
        // (уже реализовано, но оставляю для ясности)
        document.getElementById('confirmDelete').addEventListener('click', function() {
            if (currentDeleteRow && currentDeleteId) {
                deleteProduct(currentDeleteRow, currentDeleteId);
            }
            document.getElementById('confirmationModal').style.display = 'none';
            currentDeleteRow = null;
            currentDeleteId = null;
        });

        // Кнопка отмены удаления
        document.getElementById('cancelDelete').addEventListener('click', function() {
            document.getElementById('confirmationModal').style.display = 'none';
            currentDeleteRow = null;
            currentDeleteId = null;
        });

        // Кнопка подтверждения удаления всех товаров
        document.getElementById('confirmDeleteAll').addEventListener('click', function() {
            forceDeleteAllProducts();
            document.getElementById('confirmationDeleteAllModal').style.display = 'none';
        });

        // Кнопка отмены удаления всех товаров
        document.getElementById('cancelDeleteAll').addEventListener('click', function() {
            document.getElementById('confirmationDeleteAllModal').style.display = 'none';
        });

        // Кнопка подтверждения принудительного удаления товара
        document.getElementById('confirmForceDelete').addEventListener('click', function() {
            const productId = document.getElementById('confirmationForceDeleteModal').dataset.productId;
            forceDeleteProduct(productId);
            document.getElementById('confirmationForceDeleteModal').style.display = 'none';
        });

        // Кнопка отмены принудительного удаления товара
        document.getElementById('cancelForceDelete').addEventListener('click', function() {
            document.getElementById('confirmationForceDeleteModal').style.display = 'none';
        });

        // Функция для удаления товара
        function deleteProduct(rowOrId, id) {
            let row;
            let productId;
            if (typeof rowOrId === 'object' && rowOrId !== null && 'classList' in rowOrId) {
                // Вызов с двумя аргументами: (row, id)
                row = rowOrId;
                productId = id;
            } else {
                // Вызов с одним аргументом: (id)
                productId = rowOrId;
                row = document.getElementById('product-' + productId);
            }
            if (row) row.classList.add('row-deleting');
            fetch(`/products/${productId}`, {
                method: 'DELETE',
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Accept': 'application/json',
                    'Content-Type': 'application/json'
                }
            })
            .then(response => {
                if (!response.ok) {
                    throw new Error('{{ __('messages.error_deleting') }}');
                }
                return response.json();
            })
            .then(data => {
                if (data.success) {
                    setTimeout(() => {
                        if (row) row.remove();
                        window.showNotification('success', data.message || '{{ __('messages.product_successfully_deleted') }}');
                        checkDeletedProducts(); // Проверяем наличие удаленных товаров после удаления
                    }, 300);
                }
            })
            .catch(error => {
                if (row) row.classList.remove('row-deleting');
                window.showNotification('error', '{{ __('messages.failed_to_delete_product') }}');
            });
        }

        // Функция для показа удаленных товаров
        function showTrashedProducts() {
            document.getElementById('trashedModal').style.display = 'block';
            loadTrashedProducts();
        }

        // Функция для загрузки удаленных товаров
        function loadTrashedProducts() {
            const container = document.getElementById('trashedProductsList');
            container.innerHTML = '<div class="loading">{{ __('messages.loading') }}...</div>';

            console.log('Загружаем удаленные товары...');
            
            // Используем прямой URL без префикса
            const url = '/products/trashed';
            console.log('URL:', window.location.origin + url);

            // Получаем CSRF токен
            const token = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');

            fetch(url, {
                method: 'GET',
                headers: {
                    'Accept': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest',
                    'X-CSRF-TOKEN': token || '{{ csrf_token() }}',
                    'Content-Type': 'application/json'
                },
                credentials: 'include', // Важно! Передает cookies и сессию
                mode: 'same-origin'
            })
                .then(response => {
                    console.log('Ответ получен:', response.status, response.statusText);
                    
                    if (!response.ok) {
                        if (response.status === 302) {
                            throw new Error('Перенаправление на страницу входа - не аутентифицирован');
                        }
                        throw new Error(`HTTP error! status: ${response.status}`);
                    }
                    
                    return response.json();
                })
                .then(data => {
                    console.log('Данные получены:', data);
                    if (data.success) {
                        const footer = document.getElementById('trashedModalFooter');
                        if (data.products.length === 0) {
                            container.innerHTML = '<p class="no-data">{{ __('messages.no_deleted_products') }}</p>';
                            footer.style.display = 'none';
                        } else {
                            container.innerHTML = data.products.map(product => `
                                <div class="trashed-product-item">
                                    <div class="product-info">
                                        <strong>${product.name}</strong>
                                        <small>${product.category ? product.category.name : '—'} / ${product.brand ? product.brand.name : '—'}</small>
                                        <small>{{ __('messages.deleted_at') }}: ${new Date(product.deleted_at).toLocaleString()}</small>
                                    </div>
                                    <div class="product-actions">
                                        <button onclick="restoreProduct(${product.id})" class="btn-restore">
                                            {{ __('messages.restore') }}
                                        </button>
                                        <button onclick="showForceDeleteConfirmation(${product.id})" class="btn-force-delete">
                                            {{ __('messages.permanently_delete') }}
                                        </button>
                                    </div>
                                </div>
                            `).join('');
                            footer.style.display = 'block';
                        }
                    } else {
                        container.innerHTML = '<p class="error">{{ __('messages.error_loading_deleted_products') }}: ' + (data.message || '') + '</p>';
                        document.getElementById('trashedModalFooter').style.display = 'none';
                    }
                })
                .catch(error => {
                    console.error('Ошибка при загрузке:', error);
                    container.innerHTML = '<p class="error">{{ __('messages.error_loading_deleted_products') }}: ' + error.message + '</p>';
                });
        }

        // Функция для восстановления товара
        function restoreProduct(productId) {
            fetch(`/products/${productId}/restore`, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Accept': 'application/json',
                    'Content-Type': 'application/json'
                },
                credentials: 'include',
                mode: 'same-origin'
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    window.showNotification('success', '{{ __('messages.product_successfully_restored') }}');
                    loadTrashedProducts(); // Перезагружаем список удаленных товаров
                    loadPage(currentPage, searchQuery); // Обновляем основную таблицу товаров
                    checkDeletedProducts(); // Проверяем наличие удаленных товаров после восстановления
                } else {
                    window.showNotification('error', data.message || '{{ __('messages.error_restoring_product') }}');
                }
            })
            .catch(error => {
                window.showNotification('error', '{{ __('messages.error_restoring_product') }}');
            });
        }

        // Функция для принудительного удаления товара
        function forceDeleteProduct(productId) {
            fetch(`/products/${productId}/force`, {
                method: 'DELETE',
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Accept': 'application/json',
                    'Content-Type': 'application/json'
                },
                credentials: 'include',
                mode: 'same-origin'
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    window.showNotification('success', '{{ __('messages.product_permanently_deleted') }}');
                    loadTrashedProducts(); // Перезагружаем список удаленных товаров
                    checkDeletedProducts(); // Проверяем наличие удаленных товаров после принудительного удаления
                } else {
                    window.showNotification('error', data.message || '{{ __('messages.error_permanent_delete') }}');
                }
            })
            .catch(error => {
                window.showNotification('error', '{{ __('messages.error_permanent_delete') }}');
            });
        }

        // Функция для показа модального окна подтверждения удаления всех товаров
        function showDeleteAllConfirmation() {
            document.getElementById('confirmationDeleteAllModal').style.display = 'block';
        }

        // Функция для показа модального окна подтверждения принудительного удаления товара
        function showForceDeleteConfirmation(productId) {
            document.getElementById('confirmationForceDeleteModal').dataset.productId = productId;
            document.getElementById('confirmationForceDeleteModal').style.display = 'block';
        }

        // Функция для удаления всех товаров навсегда
        function forceDeleteAllProducts() {
            fetch('/products/force-delete-all', {
                method: 'DELETE',
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Accept': 'application/json',
                    'Content-Type': 'application/json'
                },
                credentials: 'include',
                mode: 'same-origin'
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    window.showNotification('success', '{{ __('messages.all_products_permanently_deleted') }}');
                    loadTrashedProducts(); // Перезагружаем список удаленных товаров
                    checkDeletedProducts(); // Проверяем наличие удаленных товаров после удаления всех
                } else {
                    window.showNotification('error', data.message || '{{ __('messages.error_deleting_all_products') }}');
                }
            })
            .catch(error => {
                window.showNotification('error', '{{ __('messages.error_deleting_all_products') }}');
            });
        }

        // Функция для закрытия модального окна удаленных товаров
        function closeTrashedModal() {
            document.getElementById('trashedModal').style.display = 'none';
        }

        // Обработчик клика по кнопке редактирования
        document.addEventListener('click', function(e) {
            const editBtn = e.target.closest('.btn-edit');
            if (editBtn) {
                const row = editBtn.closest('tr');
                if (row) {
                    const productId = row.id.split('-')[1];
                    if (productId) {
                        editProduct(productId);
                    }
                }
            }
        });

        // Функция для редактирования товара
        function editProduct(id) {
            fetch(`/products/${id}/edit`)
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        const product = data.product;
                        document.getElementById('editProductId').value = product.id;
                        document.getElementById('editProductName').value = product.name;
                        document.getElementById('editProductCategory').value = product.category_id;
                        document.getElementById('editProductBrand').value = product.brand_id;

                        // Показываем текущее фото
                        const photoContainer = document.getElementById('currentPhotoContainer');
                        if (product.photo) {
                            photoContainer.innerHTML = `
                                <img src="/storage/${product.photo}" alt="Текущее фото" style="max-width: 200px; margin-top: 10px;">
                            `;
                        } else {
                            photoContainer.innerHTML = '<p>Нет фото</p>';
                        }

                        // Подставляем цены
                        document.getElementById('editProductPurchasePrice').value = product.purchase_price ?? '';
                        document.getElementById('editProductRetailPrice').value = product.retail_price ?? '';

                        document.getElementById('editProductModal').style.display = 'block';
                    } else {
                        window.showNotification('error', data.message || '{{ __('messages.error_loading_product_data') }}');
                    }
                })
                .catch(error => {
                    window.showNotification('error', '{{ __('messages.error_loading_product_data') }}');
                });
        }

        // Обработчик отправки формы редактирования
        document.getElementById('editProductForm').addEventListener('submit', function(e) {
            e.preventDefault();
            const formData = new FormData(this);
            const productId = formData.get('id');

            fetch(`/products/${productId}`, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'X-HTTP-Method-Override': 'PUT',
                    'Accept': 'application/json',
                },
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    window.showNotification('success', '{{ __('messages.product_successfully_updated') }}');
                    closeEditModal();
                    // Обновляем строку в таблице
                    const row = document.getElementById(`product-${productId}`);
                    if (row) {
                        // Обновляем фото
                        const photoCell = row.querySelector('td:first-child');
                        if (data.product.photo) {
                            photoCell.innerHTML = `<a href="/storage/${data.product.photo}" class="zoomable-image" data-img="/storage/${data.product.photo}"><img src="/storage/${data.product.photo}" alt="${data.product.name}" class="product-photo"></a>`;
                        } else {
                            photoCell.innerHTML = '<div class="no-photo">Нет фото</div>';
                        }
                        // Обновляем название
                        row.querySelector('td:nth-child(2)').textContent = data.product.name;
                        // Обновляем категорию
                        row.querySelector('td:nth-child(3)').textContent = data.product.category?.name ?? '—';
                        // Обновляем бренд
                        row.querySelector('td:nth-child(4)').textContent = data.product.brand?.name ?? '—';
                        // Обновляем цены
                        row.querySelector('td:nth-child(5)').textContent = formatPrice(data.product.purchase_price);
                        row.querySelector('td:nth-child(6)').textContent = formatPrice(data.product.retail_price);
                    }
                } else {
                    window.showNotification('error', data.message || '{{ __('messages.error_updating_product') }}');
                }
            })
            .catch(error => {
                window.showNotification('error', '{{ __('messages.error_updating_product') }}');
            });
        });

        // Поиск товаров
        const searchInput = document.querySelector('.search-box input');
        searchInput.addEventListener('input', function() {
            const searchTerm = this.value.toLowerCase();
            const rows = document.querySelectorAll('#productsTableBody tr');

            rows.forEach(row => {
                const name = row.querySelector('td:nth-child(2)').textContent.toLowerCase();
                const category = row.querySelector('td:nth-child(3)').textContent.toLowerCase();
                const brand = row.querySelector('td:nth-child(4)').textContent.toLowerCase();

                if (name.includes(searchTerm) || category.includes(searchTerm) || brand.includes(searchTerm)) {
                    row.style.display = '';
                } else {
                    row.style.display = 'none';
                }
            });
        });

        // Форматирование цены как в Blade
        function formatPrice(price) {
            if (window.CurrencyManager) {
                return window.CurrencyManager.formatAmount(price);
            } else {
                const symbol = '{{ \App\Helpers\CurrencyHelper::getSymbol() }}';
                if (price % 1 === 0) {
                    return Math.floor(price) + ' ' + symbol;
                } else {
                    return Number(price).toFixed(2) + ' ' + symbol;
                }
            }
        }

        // Функции для работы с модальным окном импорта
        function openImportModal() {
            document.getElementById('importModal').style.display = 'block';
        }

        function closeImportModal() {
            document.getElementById('importModal').style.display = 'none';
            // Очищаем форму
            document.getElementById('importForm').reset();
        }

        // Обработчик отправки формы импорта
        document.getElementById('importForm').addEventListener('submit', function(e) {
            e.preventDefault();
            
            const formData = new FormData(this);
            const submitBtn = this.querySelector('button[type="submit"]');
            const originalText = submitBtn.textContent;
            
            submitBtn.textContent = '{{ __('messages.importing') }}...';
            submitBtn.disabled = true;
            
            fetch('{{ route("products.import") }}', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    window.showNotification('success', data.message);
                    closeImportModal();
                    // Перезагружаем страницу для обновления списка товаров
                    setTimeout(() => {
                        window.location.reload();
                    }, 1500);
                } else {
                    window.showNotification('error', '{{ __('messages.error') }}: ' + data.message);
                }
            })
            .catch(error => {
                window.showNotification('error', '{{ __('messages.error_importing_file') }}');
            })
            .finally(() => {
                submitBtn.textContent = originalText;
                submitBtn.disabled = false;
            });
        });

        // Закрытие модального окна при клике вне его
        window.onclick = function(event) {
            const importModal = document.getElementById('importModal');
            if (event.target === importModal) {
                closeImportModal();
            }
        }

        function openExportModal() {
            document.getElementById('exportModal').style.display = 'block';
        }

        function closeExportModal() {
            document.getElementById('exportModal').style.display = 'none';
        }

        function exportProducts() {
            const category = document.getElementById('exportCategory').value;
            const brand = document.getElementById('exportBrand').value;
            const photo = document.getElementById('exportPhoto').value;
            let url = '/products/export?format=xlsx';
            if (category) url += '&category_id=' + encodeURIComponent(category);
            if (brand) url += '&brand_id=' + encodeURIComponent(brand);
            if (photo) url += '&photo=' + encodeURIComponent(photo);
            window.location.href = url;
            closeExportModal();
        }

        // Закрытие модального окна при клике вне его
        window.onclick = function(event) {
            const exportModal = document.getElementById('exportModal');
            if (event.target === exportModal) {
                closeExportModal();
            }
        }

        // AJAX-пагинация
        let currentPage = 1;
        let searchQuery = '';

        function loadPage(page, search = '') {
            currentPage = page;
            searchQuery = search;
            
            const params = new URLSearchParams();
            if (page > 1) params.append('page', page);
            if (search) params.append('search', search);
            
            fetch(`/products?${params.toString()}`, {
                headers: {
                    'X-Requested-With': 'XMLHttpRequest'
                }
            })
            .then(response => {
                if (!response.ok) {
                    throw new Error('{{ __('messages.error_loading_data') }}');
                }
                return response.json();
            })
            .then(data => {
                updateTable(data.data);
                renderPagination(data.meta);
            })
            .catch(error => {
                window.showNotification('error', '{{ __('messages.error_loading_data') }}');
            });
        }

        function updateTable(products) {
            const tbody = document.getElementById('productsTableBody');
            tbody.innerHTML = '';

            products.forEach(product => {
                const row = document.createElement('tr');
                row.id = `product-${product.id}`;
                
                // Правильное формирование URL для изображения (с проверкой существования файла)
                let photoHtml;
                if (product.photo) {
                    const photoUrl = `/storage/${product.photo}`;
                    // Добавляем обработчик ошибки загрузки изображения
                    photoHtml = `<a href="${photoUrl}" class="zoomable-image" data-img="${photoUrl}"><img src="${photoUrl}" alt="${product.name}" class="product-photo" onerror="this.parentElement.innerHTML='<div class=\\'no-photo\\'>Нет фото</div>'"></a>`;
                } else {
                    photoHtml = '<div class="no-photo">Нет фото</div>';
                }
                
                // Создаем ячейки отдельно
                const photoCell = document.createElement('td');
                photoCell.innerHTML = photoHtml;
                
                const nameCell = document.createElement('td');
                nameCell.textContent = product.name;
                
                const categoryCell = document.createElement('td');
                categoryCell.textContent = product.category?.name ?? '—';
                
                const brandCell = document.createElement('td');
                brandCell.textContent = product.brand?.name ?? '—';
                
                const purchasePriceCell = document.createElement('td');
                purchasePriceCell.className = 'currency-amount';
                purchasePriceCell.setAttribute('data-amount', product.purchase_price);
                purchasePriceCell.textContent = formatPrice(product.purchase_price);
                
                const retailPriceCell = document.createElement('td');
                retailPriceCell.className = 'currency-amount';
                retailPriceCell.setAttribute('data-amount', product.retail_price);
                retailPriceCell.textContent = formatPrice(product.retail_price);
                
                const actionsCell = document.createElement('td');
                actionsCell.className = 'actions-cell';
                actionsCell.innerHTML = `
                    <button class="btn-edit" title="Редактировать">
                        <svg class="icon" viewBox="0 0 20 20" fill="currentColor">
                            <path d="M13.586 3.586a2 2 0 112.828 2.828l-.793.793-2.828-2.828.793-.793zM11.379 5.793L3 14.172V17h2.828l8.38-8.379-2.83-2.828z" />
                        </svg>
                    </button>
                    <button class="btn-delete" title="Удалить">
                        <svg class="icon" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd" d="M9 2a1 1 0 00-.894.553L7.382 4H4a1 1 0 000 2v10a2 2 0 002 2h8a2 2 0 002-2V6a1 1 0 100-2h-3.382l-.724-1.447A1 1 0 0011 2H9zM7 8a1 1 0 012 0v6a1 1 0 11-2 0V8zm5-1a1 1 0 00-1 1v6a1 1 0 102 0V8a1 1 0 00-1-1z" clip-rule="evenodd" />
                        </svg>
                    </button>
                `;
                
                // Добавляем ячейки в строку
                row.appendChild(photoCell);
                row.appendChild(nameCell);
                row.appendChild(categoryCell);
                row.appendChild(brandCell);
                row.appendChild(purchasePriceCell);
                row.appendChild(retailPriceCell);
                row.appendChild(actionsCell);
                tbody.appendChild(row);
            });
            // После добавления строк навешиваем обработчики
            initZoomableImages();
        }

        function renderPagination(meta) {
            let paginationHtml = '';
            if (meta.last_page > 1) {
                paginationHtml += '<div class="pagination">';
                // Кнопка "<"
                paginationHtml += `<button class="page-btn" data-page="${meta.current_page - 1}" ${meta.current_page === 1 ? 'disabled' : ''}>&lt;</button>`;

                let pages = [];
                if (meta.last_page <= 7) {
                    // Показываем все страницы
                    for (let i = 1; i <= meta.last_page; i++) pages.push(i);
                } else {
                    // Всегда показываем первую
                    pages.push(1);
                    // Если текущая страница > 4, показываем троеточие
                    if (meta.current_page > 4) pages.push('...');
                    // Показываем 2 страницы до и после текущей
                    let start = Math.max(2, meta.current_page - 2);
                    let end = Math.min(meta.last_page - 1, meta.current_page + 2);
                    for (let i = start; i <= end; i++) pages.push(i);
                    // Если текущая страница < last_page - 3, показываем троеточие
                    if (meta.current_page < meta.last_page - 3) pages.push('...');
                    // Всегда показываем последнюю
                    pages.push(meta.last_page);
                }
                pages.forEach(p => {
                    if (p === '...') {
                        paginationHtml += `<span class="page-ellipsis">...</span>`;
                    } else {
                        paginationHtml += `<button class="page-btn${p === meta.current_page ? ' active' : ''}" data-page="${p}">${p}</button>`;
                    }
                });
                // Кнопка ">"
                paginationHtml += `<button class="page-btn" data-page="${meta.current_page + 1}" ${meta.current_page === meta.last_page ? 'disabled' : ''}>&gt;</button>`;
                paginationHtml += '</div>';
            }
            let pagContainer = document.getElementById('productsPagination');
            if (!pagContainer) {
                pagContainer = document.createElement('div');
                pagContainer.id = 'productsPagination';
                document.querySelector('.table-wrapper').appendChild(pagContainer);
            }
            pagContainer.innerHTML = paginationHtml;

            // Навешиваем обработчики
            document.querySelectorAll('.page-btn').forEach(btn => {
                btn.addEventListener('click', function() {
                    const page = parseInt(this.dataset.page);
                    if (!isNaN(page) && !this.disabled) {
                        loadPage(page, searchQuery);
                    }
                });
            });
        }

        function handleSearch() {
            const searchInput = document.getElementById('searchInput');
            const query = searchInput.value.trim();
            
            // Сбрасываем на первую страницу при поиске
            loadPage(1, query);
        }

        // Обновляем функцию добавления товара для обновления таблицы
        document.getElementById('addProductForm').addEventListener('submit', function(e) {
            e.preventDefault();
            const formData = new FormData(this);

            fetch('/products', {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Accept': 'application/json',
                },
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    // Обновляем таблицу после добавления
                    loadPage(currentPage, searchQuery);
                    
                    window.showNotification('success', '{{ __('messages.product_successfully_added') }}');
                    closeModal();
                    this.reset();
                } else {
                    window.showNotification('error', data.message || '{{ __('messages.error_adding_product') }}');
                }
            })
            .catch(error => {
                window.showNotification('error', '{{ __('messages.error_adding_product') }}');
            });
        });

        // Функция для проверки наличия удаленных товаров
        function checkDeletedProducts() {
            fetch('/products/trashed', {
                method: 'GET',
                headers: {
                    'Accept': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Content-Type': 'application/json'
                },
                credentials: 'include',
                mode: 'same-origin'
            })
            .then(response => response.json())
            .then(data => {
                const deletedProductsBtn = document.getElementById('deletedProductsBtn');
                if (data.success && data.products && data.products.length > 0) {
                    deletedProductsBtn.style.display = 'inline-flex';
                } else {
                    deletedProductsBtn.style.display = 'none';
                }
            })
            .catch(error => {
                console.error('Ошибка при проверке удаленных товаров:', error);
                document.getElementById('deletedProductsBtn').style.display = 'none';
            });
        }

        // Инициализация первой загрузки
        document.addEventListener('DOMContentLoaded', function() {
            loadPage(1);
            initZoomableImages();
            checkDeletedProducts(); // Проверяем наличие удаленных товаров
        });

        function initZoomableImages() {
            const modal = document.getElementById('imageModal');
            const modalImg = document.getElementById('modalImage');
            const closeBtn = document.getElementById('closeImageModal');
            document.querySelectorAll('.zoomable-image').forEach(function(link) {
                link.onclick = function(e) {
                    e.preventDefault();
                    modal.style.display = 'flex';
                    modalImg.src = this.getAttribute('data-img');
                };
            });
            closeBtn.onclick = function() {
                modal.style.display = 'none';
                modalImg.src = '';
            };
            modal.onclick = function(e) {
                if (e.target === modal) {
                    modal.style.display = 'none';
                    modalImg.src = '';
                }
            };
        }


    </script>

@endsection
