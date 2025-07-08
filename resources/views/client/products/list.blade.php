@extends('client.layouts.app')

@section('content')
<div class="dashboard-container">
    <div class="products-container">
        <div class="products-header">
            <h1>Товары</h1>
            <div id="notification" class="notification alert alert-success" role="alert">
                <svg class="notification-icon" viewBox="0 0 24 24" fill="currentColor">
                    <path d="M9 16.17L4.83 12l-1.42 1.41L9 19 21 7l-1.41-1.41L9 16.17z"/>
                </svg>
                <span class="notification-message">Товар успешно добавлен!</span>
            </div>
            <div class="header-actions">
                <button class="btn-export" onclick="openExportModal()">
                    <svg class="icon" viewBox="0 0 24 24" fill="currentColor">
                        <path d="M19 9h-4V3H9v6H5l7 7 7-7zM5 18v2h14v-2H5z"/>
                    </svg>
                    Экспорт
                </button>
                <button class="btn-import" onclick="openImportModal()">
                    <svg class="icon" viewBox="0 0 24 24" fill="currentColor">
                        <path d="M19 13h-6v6h-2v-6H5v-2h6V5h2v6h6v2z"/>
                    </svg>
                    Импорт
                </button>
                <button class="btn-add-product" onclick="openModal()">
                    <svg class="icon" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd" d="M10 3a1 1 0 011 1v5h5a1 1 0 110 2h-5v5a1 1 0 11-2 0v-5H4a1 1 0 110-2h5V4a1 1 0 011-1z" clip-rule="evenodd" />
                    </svg>
                    Добавить товар
                </button>

                <div class="search-box">
                    <svg class="search-icon" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd" d="M8 4a4 4 0 100 8 4 4 0 000-8zM2 8a6 6 0 1110.89 3.476l4.817 4.817a1 1 0 01-1.414 1.414l-4.816-4.816A6 6 0 012 8z" clip-rule="evenodd" />
                    </svg>
                    <input type="text" id="searchInput" placeholder="Поиск..." onkeyup="handleSearch()">
                </div>
            </div>
        </div>

        <div class="table-wrapper">
            <table class="table-striped products-table">
                <thead>
                <tr>
                    <th>Фото</th>
                    <th>Название</th>
                    <th>Категория</th>
                    <th>Бренд</th>
                    <th>Опт. цена</th>
                    <th>Розн. цена</th>
                    <th class="actions-column">Действия</th>
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
                                <div class="no-photo">Нет фото</div>
                            @endif
                        </td>
                        <td>{{ $product->name }}</td>
                        <td>{{ $product->category->name ?? '—' }}</td>
                        <td>{{ $product->brand->name ?? '—' }}</td>
                        <td>
                            @if(fmod($product->purchase_price, 1) == 0)
                                {{ (int)$product->purchase_price }} грн
                            @else
                                {{ number_format($product->purchase_price, 2) }} грн
                            @endif
                        </td>
                        <td>
                            @if(fmod($product->retail_price, 1) == 0)
                                {{ (int)$product->retail_price }} грн
                            @else
                                {{ number_format($product->retail_price, 2) }} грн
                            @endif
                        </td>
                        <td class="actions-cell">
                            <button class="btn-edit">
                                <svg class="icon" viewBox="0 0 20 20" fill="currentColor">
                                    <path d="M13.586 3.586a2 2 0 112.828 2.828l-.793.793-2.828-2.828.793-.793zM11.379 5.793L3 14.172V17h2.828l8.38-8.379-2.83-2.828z" />
                                </svg>
                                
                            </button>
                            <button class="btn-delete">
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
                <h2>Добавить новый товар</h2>
                <span class="close" onclick="closeModal()">&times;</span>
            </div>
            <div class="modal-body">
                <form id="addProductForm" enctype="multipart/form-data">
                    @csrf
                    <div class="form-group">
                        <label for="productName">Название *</label>
                        <input type="text" id="productName" name="name" required>
                    </div>
                    <div class="form-group">
                        <label for="productCategory">Категория *</label>
                        <select id="productCategory" name="category_id" required class="form-control">
                            <option value="">Выберите категорию</option>
                            @foreach($categories as $category)
                                <option value="{{ $category->id }}">{{ $category->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="productBrand">Бренд *</label>
                        <select id="productBrand" name="brand_id" required class="form-control">
                            <option value="">Выберите бренд</option>
                            @foreach($brands as $brand)
                                <option value="{{ $brand->id }}">{{ $brand->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="productPhoto">Фото</label>
                        <input type="file" id="productPhoto" name="photo" accept="image/jpeg,image/png,image/jpg">
                        <small class="form-text text-muted">Максимальный размер: 2MB. Допустимые форматы: JPEG, PNG, JPG</small>
                    </div>
                    <div class="form-group">
                        <label for="productPurchasePrice">Оптовая цена *</label>
                        <input type="number" id="productPurchasePrice" name="purchase_price" min="0" step="0.01" required>
                    </div>
                    <div class="form-group">
                        <label for="productRetailPrice">Розничная цена *</label>
                        <input type="number" id="productRetailPrice" name="retail_price" min="0" step="0.01" required>
                    </div>
                    <div class="form-actions">
                        <button type="button" class="btn-cancel" onclick="closeModal()">Отмена</button>
                        <button type="submit" class="btn-submit">Добавить</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Модальное окно подтверждения удаления -->
    <div id="confirmationModal" class="confirmation-modal">
        <div class="confirmation-content">
            <h3>Подтверждение удаления</h3>
            <p>Вы уверены, что хотите удалить этот товар?</p>
            <div class="confirmation-buttons">
                <button id="cancelDelete" class="cancel-btn">Отмена</button>
                <button id="confirmDelete" class="confirm-btn">Удалить</button>
            </div>
        </div>
    </div>

    <!-- Модальное окно для редактирования товара -->
    <div id="editProductModal" class="modal">
        <div class="modal-content">
            <div class="modal-header">
                <h2>Редактировать товар</h2>
                <span class="close" onclick="closeEditModal()">&times;</span>
            </div>
            <div class="modal-body">
                <form id="editProductForm" enctype="multipart/form-data">
                    @csrf
                    @method('PUT')
                    <input type="hidden" id="editProductId" name="id">
                    <div class="form-group">
                        <label for="editProductName">Название *</label>
                        <input type="text" id="editProductName" name="name" required>
                    </div>
                    <div class="form-group">
                        <label for="editProductCategory">Категория *</label>
                        <select id="editProductCategory" name="category_id" required class="form-control">
                            <option value="">Выберите категорию</option>
                            @foreach($categories as $category)
                                <option value="{{ $category->id }}">{{ $category->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="editProductBrand">Бренд *</label>
                        <select id="editProductBrand" name="brand_id" required class="form-control">
                            <option value="">Выберите бренд</option>
                            @foreach($brands as $brand)
                                <option value="{{ $brand->id }}">{{ $brand->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="editProductPhoto">Фото</label>
                        <input type="file" id="editProductPhoto" name="photo" accept="image/jpeg,image/png,image/jpg">
                        <small class="form-text text-muted">Максимальный размер: 2MB. Допустимые форматы: JPEG, PNG, JPG</small>
                        <div id="currentPhotoContainer" class="mt-2"></div>
                    </div>
                    <div class="form-group">
                        <label for="editProductPurchasePrice">Оптовая цена *</label>
                        <input type="number" id="editProductPurchasePrice" name="purchase_price" min="0" step="0.01" required>
                    </div>
                    <div class="form-group">
                        <label for="editProductRetailPrice">Розничная цена *</label>
                        <input type="number" id="editProductRetailPrice" name="retail_price" min="0" step="0.01" required>
                    </div>
                    <div class="form-actions">
                        <button type="button" class="btn-cancel" onclick="closeEditModal()">Отмена</button>
                        <button type="submit" class="btn-submit">Сохранить</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Модальное окно для импорта товаров -->
    <div id="importModal" class="modal">
        <div class="modal-content">
            <div class="modal-header">
                <h2>Импорт товаров</h2>
                <span class="close" onclick="closeImportModal()">&times;</span>
            </div>
            <div class="modal-body">
                <div class="import-info">
                    <h3>Информация об импорте</h3>
                    <ul>
                        <li>Файл должен содержать колонки: Название, Категория, Бренд, Оптовая цена, Розничная цена, <strong>Фото</strong> или <strong>Изображение</strong></li>
                        <li>Если категория или бренд не указаны, система попытается определить их по названию товара</li>
                        <li><strong>Фото</strong></li>
                        <ul style="margin-left: 20px; margin-top: 5px;">
                            <li>Вставьте ссылку на изображение в колонку "Фото" — программа сама всё обработает</li>
                            <li>Поддерживаются форматы ссылок: http/https (JPG, JPEG, PNG)</li>
                            <li>Можно просто вставлять гиперссылку — не нужно преобразовывать в текст</li>
                        </ul>
                        <li><strong>Поддерживаемые форматы файлов: Excel (.xlsx, .xls) и CSV (.csv)</strong></li>
                        <li>Максимальный размер файла: 5MB</li>
                    </ul>
                </div>
                
                <form id="importForm" enctype="multipart/form-data">
                    <div class="form-group">
                        <label for="importFile">Выберите файл:</label>
                        <input type="file" id="importFile" name="file" accept=".xlsx,.xls,.csv" required>
                    </div>
                    
                    <div class="form-actions">
                        <button type="button" class="btn-cancel" onclick="closeImportModal()">Отмена</button>
                        <button type="submit" class="btn-submit">Импортировать</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Модальное окно экспорта -->
    <div id="exportModal" class="modal">
        <div class="modal-content">
            <div class="modal-header">
                <h2>Экспорт товаров</h2>
                <span class="close" onclick="closeExportModal()">&times;</span>
            </div>
            <div class="modal-body">
                <div class="import-info">
                    <h3>Экспорт в Excel</h3>
                    <ul>
                        <li>Выберите фильтры для экспорта (по категории, бренду, наличию фото).</li>
                        <li>Файл будет скачан в формате <b>Excel (.xlsx)</b>.</li>
                        <li>Откроется в Excel без проблем с кириллицей и разделителями.</li>
                    </ul>
                </div>
                <form id="exportForm" onsubmit="event.preventDefault(); exportProducts();">
                    <div class="export-filters-row">
                        <div class="form-group">
                            <label for="exportCategory">Категория:</label>
                            <select id="exportCategory" name="category_id">
                                <option value="">Все</option>
                                @foreach($categories as $cat)
                                    <option value="{{ $cat->id }}">{{ $cat->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="exportBrand">Бренд:</label>
                            <select id="exportBrand" name="brand_id">
                                <option value="">Все</option>
                                @foreach($brands as $brand)
                                    <option value="{{ $brand->id }}">{{ $brand->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="exportPhoto">Фото:</label>
                            <select id="exportPhoto" name="photo">
                                <option value="all">Все товары</option>
                                <option value="with">Только с фото</option>
                                <option value="without">Только без фото</option>
                            </select>
                        </div>
                    </div>
                    <div class="form-actions">
                        <button type="button" class="btn-cancel" onclick="closeExportModal()">Отмена</button>
                        <button type="submit" class="btn-submit">Скачать Excel</button>
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

        #importModal .import-info li {
            margin-bottom: 8px;
            line-height: 1.5;
            color: #495057;
            text-align: left;
        }

        #importModal .import-info ul ul {
            margin-top: 10px;
            margin-bottom: 10px;
        }

        #importModal .import-info ul ul li {
            margin-bottom: 5px;
            font-size: 13px;
            color: #6c757d;
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

        // Функция для показа уведомлений
        function showNotification(type, message) {
            const notification = document.getElementById('notification');
            notification.className = `notification ${type} show`;

            const icon = type === 'success' ?
                '<path d="M9 16.17L4.83 12l-1.42 1.41L9 19 21 7l-1.41-1.41L9 16.17z"/>' :
                '<path d="M19 6.41L17.59 5 12 10.59 6.41 5 5 6.41 10.59 12 5 17.59 6.41 19 12 13.41 17.59 19 19 17.59 13.41 12z"/>';

            notification.innerHTML = `
                <svg class="notification-icon" viewBox="0 0 24 24" fill="currentColor">
                    ${icon}
                </svg>
                <span class="notification-message">${message}</span>
            `;

            setTimeout(() => {
                notification.className = `notification ${type}`;
            }, 3000);
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

        // Добавление нового товара
        document.getElementById('addProductForm').addEventListener('submit', function(e) {
            e.preventDefault();

            const formData = new FormData(this);
            const submitBtn = this.querySelector('button[type="submit"]');
            const originalBtnText = submitBtn.innerHTML;
            const productsTableBody = document.getElementById('productsTableBody');

            clearErrors();

            submitBtn.innerHTML = '<span class="loader"></span> Добавление...';
            submitBtn.disabled = true;

            fetch("{{ route('products.store') }}", {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Accept': 'application/json',
                },
                body: formData
            })
                .then(response => {
                    if (!response.ok) {
                        return response.json().then(err => Promise.reject(err));
                    }
                    return response.json();
                })
                .then(data => {
                    if (data.success && data.product) {
                        // Создаем новую строку для таблицы
                        const newRow = document.createElement('tr');
                        newRow.id = `product-${data.product.id}`;

                        // Форматируем цену
                        const formattedPrice = new Intl.NumberFormat('ru-RU', {
                            minimumFractionDigits: 2,
                            maximumFractionDigits: 2
                        }).format(data.product.price);

                        // Создаем HTML для фото
                        let photoHtml = '<div class="no-photo">Нет фото</div>';
                        if (data.product.photo) {
                            photoHtml = `<img src="/storage/${data.product.photo}" alt="${data.product.name}" class="product-photo">`;
                        }

                        // Создаем HTML для новой строки
                        newRow.innerHTML = `
                            <td>${photoHtml}</td>
                            <td>${data.product.name}</td>
                            <td>${data.product.category?.name ?? '—'}</td>
                            <td>${data.product.brand?.name ?? '—'}</td>
                            <td>${formatPrice(data.product.purchase_price)}</td>
                            <td>${formatPrice(data.product.retail_price)}</td>
                            <td class="actions-cell">
                                <button class="btn-edit">
                                    <svg class="icon" viewBox="0 0 20 20" fill="currentColor">
                                        <path d="M13.586 3.586a2 2 0 112.828 2.828l-.793.793-2.828-2.828.793-.793zM11.379 5.793L3 14.172V17h2.828l8.38-8.379-2.83-2.828z" />
                                    </svg>
                                    
                                </button>
                                <button class="btn-delete">
                                    <svg class="icon" viewBox="0 0 20 20" fill="currentColor">
                                        <path fill-rule="evenodd" d="M9 2a1 1 0 00-.894.553L7.382 4H4a1 1 0 000 2v10a2 2 0 002 2h8a2 2 0 002-2V6a1 1 0 100-2h-3.382l-.724-1.447A1 1 0 0011 2H9zM7 8a1 1 0 012 0v6a1 1 0 11-2 0V8zm5-1a1 1 0 00-1 1v6a1 1 0 102 0V8a1 1 0 00-1-1z" clip-rule="evenodd" />
                                    </svg>
                                    
                                </button>
                            </td>
                        `;

                        // Добавляем новую строку в начало таблицы
                        productsTableBody.insertBefore(newRow, productsTableBody.firstChild);

                        // Показываем уведомление
                        showNotification('success', `Товар ${data.product.name} успешно добавлен`);

                        // Закрываем модальное окно и очищаем форму
                        closeModal();
                        this.reset();
                    } else {
                        throw new Error('Сервер не вернул данные товара');
                    }
                })
                .catch(error => {
                    console.error('Ошибка:', error);

                    if (error.errors) {
                        showErrors(error.errors);
                        showNotification('error', 'Пожалуйста, исправьте ошибки в форме');
                    } else {
                        showNotification('error', error.message || 'Произошла ошибка при добавлении товара');
                    }
                })
                .finally(() => {
                    submitBtn.innerHTML = originalBtnText;
                    submitBtn.disabled = false;
                });
        });

        // Глобальные переменные для удаления
        let currentDeleteRow = null;
        let currentDeleteId = null;

        // Обработчик клика по кнопке удаления
        document.addEventListener('click', function(e) {
            if (e.target.closest('.btn-delete')) {
                const row = e.target.closest('tr');
                const productId = row.id.split('-')[1];

                // Сохраняем ссылку на удаляемую строку
                currentDeleteRow = row;
                currentDeleteId = productId;

                // Показываем модальное окно подтверждения
                document.getElementById('confirmationModal').style.display = 'block';
            }
        });
        // Обработчики для модального окна подтверждения
        document.getElementById('cancelDelete').addEventListener('click', function() {
            document.getElementById('confirmationModal').style.display = 'none';
            currentDeleteRow = null;
            currentDeleteId = null;
        });
        document.getElementById('confirmDelete').addEventListener('click', function() {
            if (currentDeleteRow && currentDeleteId) {
                deleteProduct(currentDeleteRow, currentDeleteId);
            }
            document.getElementById('confirmationModal').style.display = 'none';
        });

        // Функция для удаления товара
        function deleteProduct(row, productId) {
            // Добавляем класс для анимации
            row.classList.add('row-deleting');

            // Отправляем запрос на удаление
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
                        throw new Error('Ошибка при удалении');
                    }
                    return response.json();
                })
                .then(data => {
                    if (data.success) {
                        // Удаляем строку после завершения анимации
                        setTimeout(() => {
                            row.remove();
                            showNotification('success', 'Товар успешно удален');
                        }, 300);
                    }
                })
                .catch(error => {
                    console.error('Ошибка:', error);
                    row.classList.remove('row-deleting');
                    showNotification('error', 'Не удалось удалить товар');
                });
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
                        showNotification('error', data.message || 'Ошибка загрузки данных товара');
                    }
                })
                .catch(error => {
                    showNotification('error', 'Ошибка загрузки данных товара');
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
                    showNotification('success', 'Товар успешно обновлен');
                    closeEditModal();
                    // Обновляем строку в таблице
                    const row = document.getElementById(`product-${productId}`);
                    if (row) {
                        // Обновляем фото
                        const photoCell = row.querySelector('td:first-child');
                        if (data.product.photo) {
                            photoCell.innerHTML = `<img src="/storage/${data.product.photo}" alt="${data.product.name}" class="product-photo">`;
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
                    showNotification('error', data.message || 'Ошибка обновления товара');
                }
            })
            .catch(error => {
                showNotification('error', 'Ошибка обновления товара');
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
            if (price % 1 === 0) {
                return parseInt(price) + ' грн';
            } else {
                return Number(price).toFixed(2) + ' грн';
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
            
            submitBtn.textContent = 'Импортирование...';
            submitBtn.disabled = true;
            
            fetch('{{ route("products.import") }}', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    showNotification('success', data.message);
                    closeImportModal();
                    // Перезагружаем страницу для обновления списка товаров
                    setTimeout(() => {
                        window.location.reload();
                    }, 1500);
                } else {
                    showNotification('error', 'Ошибка: ' + data.message);
                }
            })
            .catch(error => {
                console.error('Error:', error);
                showNotification('error', 'Ошибка при импорте файла');
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
                    throw new Error('Ошибка загрузки данных');
                }
                return response.json();
            })
            .then(data => {
                updateTable(data.data);
                renderPagination(data.meta);
            })
            .catch(error => {
                console.error('Ошибка:', error);
                showNotification('error', 'Ошибка загрузки данных');
            });
        }

        function updateTable(products) {
            const tbody = document.getElementById('productsTableBody');
            tbody.innerHTML = '';

            products.forEach(product => {
                const row = document.createElement('tr');
                row.id = `product-${product.id}`;
                
                const photoHtml = product.photo 
                    ? `<a href="/storage/${product.photo}" class="zoomable-image" data-img="/storage/${product.photo}"><img src="/storage/${product.photo}" alt="${product.name}" class="product-photo"></a>`
                    : '<div class="no-photo">Нет фото</div>';
                
                row.innerHTML = `
                    <td>${photoHtml}</td>
                    <td>${product.name}</td>
                    <td>${product.category?.name ?? '—'}</td>
                    <td>${product.brand?.name ?? '—'}</td>
                    <td>${formatPrice(product.purchase_price)}</td>
                    <td>${formatPrice(product.retail_price)}</td>
                    <td class="actions-cell">
                        <button class="btn-edit" onclick="editProduct(${product.id})">
                            <svg class="icon" viewBox="0 0 20 20" fill="currentColor">
                                <path d="M13.586 3.586a2 2 0 112.828 2.828l-.793.793-2.828-2.828.793-.793zM11.379 5.793L3 14.172V17h2.828l8.38-8.379-2.83-2.828z" />
                            </svg>
                        </button>
                        <button class="btn-delete" onclick="deleteProduct(${product.id})">
                            <svg class="icon" viewBox="0 0 20 20" fill="currentColor">
                                <path fill-rule="evenodd" d="M9 2a1 1 0 00-.894.553L7.382 4H4a1 1 0 000 2v10a2 2 0 002 2h8a2 2 0 002-2V6a1 1 0 100-2h-3.382l-.724-1.447A1 1 0 0011 2H9zM7 8a1 1 0 012 0v6a1 1 0 11-2 0V8zm5-1a1 1 0 00-1 1v6a1 1 0 102 0V8a1 1 0 00-1-1z" clip-rule="evenodd" />
                            </svg>
                        </button>
                    </td>
                `;
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
                    
                    showNotification('success', 'Товар успешно добавлен');
                    closeModal();
                    this.reset();
                } else {
                    showNotification('error', data.message || 'Ошибка добавления товара');
                }
            })
            .catch(error => {
                showNotification('error', 'Ошибка добавления товара');
            });
        });

        // Обновляем функцию удаления товара для обновления таблицы
        function deleteProduct(id) {
            if (confirm('Вы уверены, что хотите удалить этот товар?')) {
                fetch(`/products/${id}`, {
                    method: 'DELETE',
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Accept': 'application/json',
                    }
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        // Обновляем таблицу после удаления
                        loadPage(currentPage, searchQuery);
                        showNotification('success', 'Товар успешно удален');
                    } else {
                        showNotification('error', data.message || 'Ошибка удаления товара');
                    }
                })
                .catch(error => {
                    showNotification('error', 'Ошибка удаления товара');
                });
            }
        }

        // Инициализация первой загрузки
        loadPage(1);

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

        document.addEventListener('DOMContentLoaded', function() {
            initZoomableImages();
        });
    </script>

@endsection
