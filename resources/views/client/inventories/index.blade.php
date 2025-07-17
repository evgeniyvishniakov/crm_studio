@extends('client.layouts.app')

@section('content')
<div class="dashboard-container">
    <div class="inventories-container">
        <div class="inventories-header">
            <h1>Инвентаризация</h1>
            <div id="notification" class="notification">
                <!-- Уведомления будут появляться здесь -->
            </div>
            <div class="header-actions">
                <button class="btn-add-inventory" onclick="openInventoryModal()">
                    <svg class="icon" viewBox="0 0 24 24" fill="currentColor">
                        <path d="M19 13h-6v6h-2v-6H5v-2h6V5h2v6h6v2z"/>
                    </svg>
                    Новая инвентаризация
                </button>
                <div class="search-box">
                    <svg class="search-icon" viewBox="0 0 24 24" fill="currentColor">
                        <path d="M15.5 14h-.79l-.28-.27a6.5 6.5 0 0 0 1.48-5.34c-.47-2.78-2.79-5-5.59-5.34a6.505 6.505 0 0 0-7.27 7.27c.34 2.8 2.56 5.12 5.34 5.59a6.5 6.5 0 0 0 5.34-1.48l.27.28v.79l4.25 4.25c.41.41 1.08.41 1.49 0 .41-.41.41-1.08 0-1.49L15.5 14zm-6 0C7.01 14 5 11.99 5 9.5S7.01 5 9.5 5 14 7.01 14 9.5 11.99 14 9.5 14z"/>
                    </svg>
                    <input type="text" placeholder="Поиск..." id="searchInput">
                </div>
            </div>
        </div>
        <table class="table-striped analysis-table products-table inventories-table">
            <thead>
                <tr>
                    <th>Дата</th>
                    <th>Ответственный</th>
                    <th>Расхождения</th>
                    <th>Примечания</th>
                    <th>Действия</th>
                </tr>
            </thead>
            <tbody id="inventoriesListBody">
                @foreach($inventories as $inventory)
                    <tr class="inventory-summary-row" id="inventory-row-{{ $inventory->id }}" onclick="toggleInventoryDetailsRow({{ $inventory->id }})">
                        <td>{{ $inventory->formatted_date }}</td>
                        <td>{{ $inventory->user->name ?? '—' }}</td>
                        <td>{{ $inventory->discrepancies_count }}
                            @if($inventory->shortages_count > 0)
                                ({{ $inventory->shortages_count }} нехватка)
                            @endif
                            @if($inventory->overages_count > 0)
                                ({{ $inventory->overages_count }} излишек)
                            @endif
                        </td>
                        <td title="{{ $inventory->notes }}">{{ $inventory->notes ? Str::limit($inventory->notes, 30) : '—' }}</td>
                        <td>
                            <div class="inventory-actions">
                                <button class="btn-edit" onclick="editInventory(event, {{ $inventory->id }})">
                                    <svg class="icon" viewBox="0 0 20 20" fill="currentColor">
                                        <path d="M13.586 3.586a2 2 0 112.828 2.828l-.793.793-2.828-2.828.793-.793zM11.379 5.793L3 14.172V17h2.828l8.38-8.379-2.83-2.828z"/>
                                    </svg>
                                    Ред.
                                </button>
                                <button class="btn-delete" onclick="confirmDeleteInventory(event, {{ $inventory->id }})">
                                    <svg class="icon" viewBox="0 0 20 20" fill="currentColor">
                                        <path fill-rule="evenodd" d="M9 2a1 1 0 00-.894.553L7.382 4H4a1 1 0 000 2v10a2 2 0 002 2h8a2 2 0 002-2V6a1 1 0 100-2h-3.382l-.724-1.447A1 1 0 0011 2H9zM7 8a1 1 0 012 0v6a1 1 0 11-2 0V8zm5-1a1 1 0 00-1 1v6a1 1 0 102 0V8a1 1 0 00-1-1z" clip-rule="evenodd"/>
                                    </svg>
                                    Удалить
                                </button>
                                <button class="btn-pdf" onclick="downloadInventoryPdf(event, {{ $inventory->id }})">
                                    <svg class="icon" viewBox="0 0 20 20" fill="currentColor">
                                        <path d="M6 2a2 2 0 00-2 2v12a2 2 0 002 2h8a2 2 0 002-2V7.414A2 2 0 0015.414 6L13 3.586A2 2 0 0011.586 3H6zm2 2h3v3a1 1 0 001 1h3v9a1 1 0 01-1 1H6a1 1 0 01-1-1V4a1 1 0 011-1zm5 3.414V8h-2V6h.586L13 5.414zM8 10a1 1 0 100 2h4a1 1 0 100-2H8zm0 4a1 1 0 100 2h4a1 1 0 100-2H8z"/>
                                    </svg>
                                    PDF
                                </button>
                            </div>
                        </td>
                    </tr>
                    <tr class="inventory-details-row" id="details-row-{{ $inventory->id }}" style="display: none;">
                        <td colspan="5">
                            <div class="inventory-notes">{{ $inventory->notes }}</div>
                            <table class="table-striped analysis-table products-table">
                                <thead>
                                <tr>
                                    <th>Фото</th>
                                    <th class="large-col">Товар</th>
                                    <th class="small-col">Склад</th>
                                    <th class="small-col">Кол</th>
                                    <th>Разница</th>
                                    <th>Статус</th>
                                </tr>
                                </thead>
                                <tbody>
                                @foreach($inventory->items->where('difference', '!=', 0) as $item)
                                    <tr>
                                        <td>
                                            @if($item->product->photo)
                                                <img src="{{ Storage::url($item->product->photo) }}" class="product-photo" alt="{{ $item->product->name }}">
                                            @else
                                                <div class="no-photo">Нет фото</div>
                                            @endif
                                        </td>
                                        <td class="large-col">{{ $item->product->name }}</td>
                                        <td class="small-col">{{ $item->warehouse_qty }} шт</td>
                                        <td class="small-col">{{ $item->actual_qty }} шт</td>
                                        <td class="{{ $item->difference > 0 ? 'text-success' : 'text-danger' }}">
                                            {{ $item->difference > 0 ? '+' : '' }}{{ $item->difference }} шт
                                        </td>
                                        <td>
                                            @if($item->difference == 0)
                                                <span class="status-success">✅ Совпадает</span>
                                            @elseif($item->difference > 0)
                                                <span class="status-warning">⚠️ Лишнее</span>
                                            @else
                                                <span class="status-danger">❌ Не хватает</span>
                                            @endif
                                        </td>
                                    </tr>
                                @endforeach
                                </tbody>
                            </table>
                            <div class="view-all-items">
                                <button class="btn-view-all" onclick="viewAllInventoryItems({{ $inventory->id }})">
                                    Просмотреть весь список
                                </button>
                            </div>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    <!-- Модальное окно новой инвентаризации -->
    <div id="inventoryModal" class="modal">
        <div class="modal-content" style="width: 80%; max-width: 900px;">
            <div class="modal-header">
                <h2>Новая инвентаризация</h2>
                <span class="close" onclick="closeInventoryModal()">&times;</span>
            </div>
            <div class="modal-body">
                <form id="inventoryForm">
                    @csrf
                    <div class="form-row">
                        <div class="form-group">
                            <label>Дата </label>
                            <input type="date" name="date" required class="form-control">
                        </div>
                        <div class="form-group">
                            <label>Ответственный </label>
                            <select name="user_id" required class="form-control">
                                <option value="">Выберите ответственного</option>
                                @foreach($users as $user)
                                    <option value="{{ $user->id }}" {{ $user->id == $adminUserId ? 'selected' : '' }}>
                                        {{ $user->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="form-group">
                        <label>Примечания</label>
                        <textarea name="notes" rows="2" class="form-control"></textarea>
                    </div>

                    <div class="items-container" id="itemsContainer">
                        <h3>Товары</h3>
                        <div class="item-row template" style="display: none;">
                            <div class="form-row">
                                <div class="form-group product-group large-col"> <!-- Добавлен класс product-group -->
                                    <label>Товар</label>
                                    <div class="product-search-container">
                                        <input type="text" class="product-search-input form-control large-col"
                                               placeholder="Начните вводить название товара..."
                                               oninput="searchProducts(this)"
                                               onfocus="showProductDropdown(this)" autocomplete="off">
                                        <div class="product-dropdown" style="display: none;">
                                            <div class="product-dropdown-list"></div>
                                        </div>
                                        <select name="items[0][product_id]" class="form-control product-select large-col" style="display: none;">
                                            <option value="">Выберите товар</option>
                                            @foreach($products as $product)
                                                <option value="{{ $product->id }}" data-stock="{{ $product->stock }}">
                                                    {{ $product->name }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                                <div class="form-group small-col">
                                    <label>Кол</label>
                                    <input type="number" name="items[0][actual_qty]" required class="form-control small-col" min="0" value="0">
                                </div>
                                <div class="form-group small-col">
                                    <label>Склад</label>
                                    <input type="number" name="items[0][warehouse_qty]" class="form-control small-col" value="0" readonly>
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
                            Добавить товар
                        </button>
                        <button type="button" class="btn-cancel" onclick="closeInventoryModal()">Отмена</button>
                        <button type="button" class="btn-submit" onclick="analyzeInventory()">Провести инвентаризацию</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Модальное окно анализа инвентаризации -->
    <div id="analysisModal" class="modal">
        <div class="modal-content" style="width: 80%; max-width: 900px;">
            <div class="modal-header">
                <h2>Анализ инвентаризации</h2>
                <span class="close" onclick="confirmCloseAnalysisModal()">&times;</span>
            </div>
            <div class="modal-body">
                <div class="analysis-summary">
                    <h3>Итоги инвентаризации</h3>
                    <div class="summary-stats">
                        <div class="stat-item">
                            <span class="stat-label">Всего товаров:</span>
                            <span class="stat-value" id="totalItems">0</span>
                        </div>
                        <div class="stat-item">
                            <span class="stat-label">Совпадает:</span>
                            <span class="stat-value" id="matchedItems">0</span>
                        </div>
                        <div class="stat-item">
                            <span class="stat-label">Не хватает:</span>
                            <span class="stat-value" id="shortageItems">0</span>
                        </div>
                        <div class="stat-item">
                            <span class="stat-label">Излишки:</span>
                            <span class="stat-value" id="overageItems">0</span>
                        </div>
                    </div>
                </div>
                <table class="table-striped analysis-table products-table">
                    <thead>
                    <tr>
                        <th>Фото</th>
                        <th class="large-col">Товар</th>
                        <th class="small-col">Склад</th>
                        <th class="small-col">Кол</th>
                        <th>Разница</th>
                        <th>Статус</th>
                    </tr>
                    </thead>
                    <tbody id="analysisTableBody">
                    <!-- Строки будут добавлены динамически -->
                    </tbody>
                </table>
                <div class="form-actions">
                    <button type="button" class="btn-cancel" onclick="confirmCloseAnalysisModal()">Назад</button>
                    <button type="button" class="btn-submit" onclick="saveInventory()">Сохранить инвентаризацию</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Модальное окно просмотра всех товаров инвентаризации -->
    <div id="viewAllItemsModal" class="modal">
        <div class="modal-content" style="width: 80%; max-width: 900px;">
            <div class="modal-header">
                <h2>Все товары инвентаризации</h2>
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
                <h2>Редактировать инвентаризацию</h2>
                <span class="close" onclick="closeEditInventoryModal()">&times;</span>
            </div>
            <div class="modal-body" id="editInventoryModalBody">
                <!-- Контент будет загружен динамически -->
            </div>
        </div>
    </div>

    <!-- Модальное окно подтверждения удаления -->
    <div id="confirmationModal" class="confirmation-modal">
        <div class="confirmation-content">
            <h3>Подтверждение удаления</h3>
            <p>Вы уверены, что хотите удалить эту инвентаризацию?</p>
            <div class="confirmation-buttons">
                <button class="cancel-btn" id="cancelDelete">Отмена</button>
                <button class="confirm-btn" id="confirmDeleteBtn">Удалить</button>
            </div>
        </div>
    </div>

    <!-- Модальное окно подтверждения отмены инвентаризации -->
    <div id="cancelInventoryModal" class="confirmation-modal">
        <div class="confirmation-content">
            <h3>Подтверждение отмены</h3>
            <p>Вы уверены, что хотите отменить инвентаризацию? Все несохранённые данные будут потеряны.</p>
            <div class="confirmation-buttons">
                <button class="cancel-btn" id="cancelCancelInventory">Отмена</button>
                <button class="confirm-btn" id="confirmCancelInventoryBtn">Да, отменить</button>
            </div>
        </div>
    </div>

    <!-- Модальное окно подтверждения отмены редактирования инвентаризации -->
    <div id="cancelEditInventoryModal" class="confirmation-modal">
        <div class="confirmation-content">
            <h3>Подтверждение отмены</h3>
            <p>Вы уверены, что хотите отменить редактирование? Все несохранённые данные будут потеряны.</p>
            <div class="confirmation-buttons">
                <button class="cancel-btn" id="cancelCancelEditInventory">Отмена</button>
                <button class="confirm-btn" id="confirmCancelEditInventoryBtn">Да, отменить</button>
            </div>
        </div>
    </div>

    <!-- Модальное окно для увеличенного фото -->
    <div id="zoomImageModal" class="modal" style="display:none; z-index: 9999; background: rgba(0,0,0,0.7);">
        <span class="close" id="closeZoomImageModal" style="position:absolute;top:10px;right:20px;font-size:2em;color:#fff;cursor:pointer;">&times;</span>
        <img id="zoomedImage" src="" alt="Фото товара" style="display:block;max-width:90vw;max-height:90vh;margin:40px auto;box-shadow:0 0 20px #000;border-radius:8px;">
    </div>

    <script>
        // --- Глобальные переменные ---
        let currentDeleteId = null;
        let itemCounter = 1;
        // window.inventoryData больше не используется

        // Функции для работы с модальными окнами
        function openInventoryModal() {
            // Полный сброс формы
            resetInventoryForm();
            // Устанавливаем текущую дату
            const today = new Date().toISOString().split('T')[0];
            document.querySelector('#inventoryForm [name="date"]').value = today;
            document.getElementById('inventoryModal').style.display = 'block';
        }

        function closeInventoryModal(force = false) {
            if (force) {
                resetInventoryModal();
                return;
            }
            // Проверяем, есть ли введенные данные
            const hasData = Array.from(document.querySelectorAll('#inventoryForm input, #inventoryForm select, #inventoryForm textarea'))
                .some(el => {
                    if (el.name === 'items[0][product_id]' || el.name === 'items[0][actual_qty]') {
                        return el.value !== '' && el.value !== '0';
                    }
                    return el.value !== '';
                });

            if (hasData) {
                document.getElementById('cancelInventoryModal').style.display = 'block';
            } else {
                resetInventoryModal();
            }
        }

        function resetInventoryModal() {
            // window.inventoryData = null; // Удалено
            document.getElementById('inventoryModal').style.display = 'none';
            clearErrors('inventoryForm');
            resetInventoryForm();
        }

        function openAnalysisModal() {
            document.getElementById('analysisModal').style.display = 'block';
        }

        function closeAnalysisModal() {
            document.getElementById('analysisModal').style.display = 'none';
        }

        function closeEditInventoryModal(force = false) {
            // window.inventoryData = null; // Удалено
            if (force) {
                document.getElementById('editInventoryModal').style.display = 'none';
                return;
            }
            // Проверяем, есть ли введенные данные
            const hasData = Array.from(document.querySelectorAll('#editInventoryForm input, #editInventoryForm select, #editInventoryForm textarea'))
                .some(el => el.value && el.value !== '' && el.value !== '0');
            if (hasData) {
                document.getElementById('cancelEditInventoryModal').style.display = 'block';
            } else {
                document.getElementById('editInventoryModal').style.display = 'none';
            }
        }

        function closeViewAllItemsModal() {
            document.getElementById('viewAllItemsModal').style.display = 'none';
        }

        // Закрытие модальных окон при клике вне их
        window.onclick = function(event) {
            if (event.target == document.getElementById('analysisModal')) {
                closeAnalysisModal();
            }
            if (event.target == document.getElementById('editInventoryModal')) {
                closeEditInventoryModal();
            }
            if (event.target == document.getElementById('viewAllItemsModal')) {
                closeViewAllItemsModal();
            }
            if (event.target == document.getElementById('confirmationModal')) {
                document.getElementById('confirmationModal').style.display = 'none';
            }
            if (event.target == document.getElementById('cancelInventoryModal')) {
                document.getElementById('cancelInventoryModal').style.display = 'none';
            }
            if (event.target == document.getElementById('cancelEditInventoryModal')) {
                document.getElementById('cancelEditInventoryModal').style.display = 'none';
            }
            if (event.target == document.getElementById('zoomImageModal')) {
                document.getElementById('zoomImageModal').style.display = 'none';
                document.getElementById('zoomedImage').src = '';
            }
        }

        // Логика для модального окна отмены инвентаризации
        document.getElementById('cancelCancelInventory').addEventListener('click', function() {
            document.getElementById('cancelInventoryModal').style.display = 'none';
        });
        document.getElementById('confirmCancelInventoryBtn').addEventListener('click', function() {
            document.getElementById('cancelInventoryModal').style.display = 'none';
            resetInventoryModal();
            window.showNotification('error', 'Инвентаризация отменена пользователем');
        });

        // Логика для модального окна подтверждения отмены редактирования инвентаризации
        document.getElementById('cancelCancelEditInventory').addEventListener('click', function() {
            document.getElementById('cancelEditInventoryModal').style.display = 'none';
        });
        document.getElementById('confirmCancelEditInventoryBtn').addEventListener('click', function() {
            // window.inventoryData = null; // Очищаем данные инвентаризации
            document.getElementById('cancelEditInventoryModal').style.display = 'none';
            document.getElementById('editInventoryModal').style.display = 'none';
            window.showNotification('error', 'Редактирование отменено пользователем');
        });

        // Функции для работы с товарами в инвентаризации
        function addItemRow(containerId = 'itemsContainer') {
            const container = document.getElementById(containerId);
            const template = container.querySelector('.template');
            const newRow = template.cloneNode(true);

            newRow.style.display = 'block';
            newRow.classList.remove('template');

            // Определяем текущий индекс
            let index = itemCounter;
            if (containerId === 'editItemsContainer') {
                // Для редактирования считаем количество уже добавленных рядов
                index = container.querySelectorAll('.item-row:not(.template)').length;
            }

            // Обновляем индексы в именах полей
            const inputs = newRow.querySelectorAll('input, select');
            inputs.forEach(input => {
                const name = input.name.replace(/\[\d+\]/, `[${index}]`);
                input.name = name;
                if (input.tagName === 'SELECT') {
                    input.selectedIndex = 0;
                } else {
                    input.value = input.name.includes('actual_qty') ? '0' : '';
                }
            });

            // Инициализируем поиск для нового ряда
            const searchContainer = newRow.querySelector('.product-search-container');
            if (searchContainer) {
                const searchInput = searchContainer.querySelector('.product-search-input');
                searchInput.id = `product-search-${index}`;
                searchInput.value = '';

                const select = searchContainer.querySelector('.product-select');
                select.name = `items[${index}][product_id]`;
                select.selectedIndex = 0;
            }

            container.insertBefore(newRow, container.querySelector('.form-actions'));
            if (containerId === 'itemsContainer') itemCounter++;
        }

        function removeItemRow(button) {
            const row = button.closest('.item-row');
            if (document.querySelectorAll('.item-row:not(.template)').length > 1) {
                row.remove();
            } else {
                // Если это последний ряд, просто очищаем его
                const inputs = row.querySelectorAll('input, select');
                inputs.forEach(input => {
                    if (input.tagName === 'SELECT') {
                        input.selectedIndex = 0;
                    } else {
                        input.value = input.name.includes('actual_qty') ? '0' : '';
                    }
                });
            }
        }

        // --- Обновлённая функция сброса формы ---
        function resetInventoryForm() {
            itemCounter = 1;
            const form = document.getElementById('inventoryForm');
            form.reset();

            // Удаляем все ряды товаров, кроме шаблона
            const container = document.getElementById('itemsContainer');
            const rows = container.querySelectorAll('.item-row:not(.template)');
            rows.forEach(row => row.remove());

            // Добавляем первый пустой ряд
            addItemRow();
        }

        // Функция для показа/скрытия деталей инвентаризации
        function toggleInventoryDetails(id) {
            const details = document.getElementById(`details-${id}`);
            details.style.display = details.style.display === 'none' ? 'block' : 'none';
        }

        // Функция для просмотра всех товаров инвентаризации
        function viewAllInventoryItems(id) {
            fetch(`/inventories/${id}/items`)
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        const modalBody = document.getElementById('viewAllItemsModalBody');

                        let html = `
                            <table class="table-striped analysis-table products-table">
                                <thead>
                                    <tr>
                                        <th>Фото</th>
                                        <th class="large-col">Товар</th>
                                        <th class="small-col">Склад</th>
                                        <th class="small-col">Кол</th>
                                        <th>Разница</th>
                                        <th>Статус</th>
                                    </tr>
                                </thead>
                                <tbody>
                        `;

                        data.items.forEach(item => {
                            const status = item.difference == 0 ? '✅ Совпадает' :
                                item.difference > 0 ? '⚠️ Лишнее' : '❌ Не хватает';

                            html += `
                                <tr>
                                    <td>
                                        ${item.product.photo ?
                                `<img src="/storage/${item.product.photo}" class="product-photo" alt="${item.product.name}">` :
                                `<div class="no-photo">Нет фото</div>`}
                                    </td>
                                    <td class="large-col">${item.product.name}</td>
                                    <td class="small-col">${item.warehouse_qty} шт</td>
                                    <td class="small-col">${item.actual_qty} шт</td>
                                    <td class="${item.difference > 0 ? 'text-success' : 'text-danger'}">
                                        ${item.difference > 0 ? '+' : ''}${item.difference} шт
                                    </td>
                                    <td>${status}</td>
                                </tr>
                            `;
                        });

                        html += `
                                </tbody>
                            </table>
                        `;

                        modalBody.innerHTML = html;
                        document.getElementById('viewAllItemsModal').style.display = 'block';
                    } else {
                        window.showNotification('error', data.message || 'Ошибка загрузки данных');
                    }
                })
                .catch(error => {
                    window.showNotification('error', 'Ошибка загрузки данных');
                });
        }

        // --- Обновлённая функция анализа инвентаризации ---
        function analyzeInventory() {
            clearErrors('inventoryForm');
            const formData = {
                date: document.querySelector('#inventoryForm [name="date"]').value,
                user_id: document.querySelector('#inventoryForm [name="user_id"]').value,
                notes: document.querySelector('#inventoryForm [name="notes"]').value,
                items: []
            };

            // Проверка обязательных полей
            if (!formData.date || !formData.user_id) {
                window.showNotification('error', 'Заполните все обязательные поля');
                return;
            }

            const seenProducts = new Set();
            let hasErrors = false;

            document.querySelectorAll('#itemsContainer .item-row:not(.template)').forEach(row => {
                const productId = row.querySelector('[name*="product_id"]').value;
                const actualQty = row.querySelector('[name*="actual_qty"]').value;
                const warehouseQty = row.querySelector('[name*="warehouse_qty"]').value || 0;
                const productName = row.querySelector('.product-search-input').value;

                if (!productId) {
                    showError(row.querySelector('[name*="product_id"]'), 'Выберите товар');
                    hasErrors = true;
                    return;
                }
                if (seenProducts.has(productId)) {
                    showError(row.querySelector('[name*="product_id"]'), 'Этот товар уже добавлен');
                    hasErrors = true;
                    return;
                }
                seenProducts.add(productId);

                formData.items.push({
                    product_id: productId,
                    product_name: productName,
                    warehouse_qty: parseInt(warehouseQty) || 0,
                    actual_qty: parseInt(actualQty) || 0,
                    difference: (parseInt(actualQty) || 0) - (parseInt(warehouseQty) || 0)
                });
            });

            if (hasErrors) return;

            if (formData.items.length === 0) {
                window.showNotification('error', 'Добавьте хотя бы один товар');
                return;
            }

            // Сохраняем данные в data-атрибут модалки анализа
            document.getElementById('analysisModal').dataset.inventory = JSON.stringify(formData);
            console.log('Inventory Data after analyze:', formData);

            updateAnalysisTable(formData);
            closeInventoryModal(true);
            openAnalysisModal();
        }

        // --- Новая функция для обновления таблицы анализа ---
        function updateAnalysisTable(data) {
            const tableBody = document.getElementById('analysisTableBody');
            tableBody.innerHTML = '';

            const totalItems = data.items.length;
            const matchedItems = data.items.filter(item => item.difference === 0).length;
            const shortageItems = data.items.filter(item => item.difference < 0).length;
            const overageItems = data.items.filter(item => item.difference > 0).length;

            data.items.forEach(item => {
                const status = item.difference == 0 ? '✅ Совпадает' :
                    item.difference > 0 ? '⚠️ Лишнее' : '❌ Не хватает';
                let product = null;
                if (window.allProducts) {
                    product = window.allProducts.find(p => p.id == item.product_id);
                }
                let photoHtml = '<div class="no-photo">Нет фото</div>';
                if (product && product.photo) {
                    photoHtml = `<a href="#" class="zoomable-image" data-img="/storage/${product.photo}">
                        <img src="/storage/${product.photo}" alt="${item.product_name}" class="product-photo">
                    </a>`;
                }
                const row = document.createElement('tr');
                row.innerHTML = `
                    <td>${photoHtml}</td>
                    <td>${item.product_name}</td>
                    <td class="small-col">${item.warehouse_qty} шт</td>
                    <td class="small-col">${item.actual_qty} шт</td>
                    <td class="${item.difference > 0 ? 'text-success' : 'text-danger'}">
                        ${item.difference > 0 ? '+' : ''}${item.difference} шт
                    </td>
                    <td>${status}</td>
                `;
                tableBody.appendChild(row);
            });

            document.getElementById('totalItems').textContent = totalItems;
            document.getElementById('matchedItems').textContent = matchedItems;
            document.getElementById('shortageItems').textContent = shortageItems;
            document.getElementById('overageItems').textContent = overageItems;
        }

        // --- Обновлённая функция сохранения инвентаризации ---
        function saveInventory() {
            // Берём данные из data-атрибута модалки анализа
            const data = document.getElementById('analysisModal').dataset.inventory;
            const inventoryData = data ? JSON.parse(data) : null;
            console.log('Inventory Data before save:', inventoryData);

            if (!inventoryData) {
                window.showNotification('error', 'Данные инвентаризации не найдены');
                return;
            }
            if (!inventoryData.items || inventoryData.items.length === 0) {
                window.showNotification('error', 'Нет товаров для сохранения');
                return;
            }
            if (!inventoryData.date || !inventoryData.user_id) {
                window.showNotification('error', 'Заполните все обязательные поля');
                return;
            }

            const saveBtn = document.querySelector('#analysisModal .btn-submit');
            const originalText = saveBtn.innerHTML;
            saveBtn.innerHTML = '<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Сохранение...';
            saveBtn.disabled = true;
            fetch('/inventories', {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify(inventoryData)
            })
            .then(response => {
                if (!response.ok) {
                    return response.json().then(err => Promise.reject(err));
                }
                return response.json();
            })
            .then(data => {
                if (data.success) {
                    window.showNotification('success', 'Инвентаризация успешно сохранена');
                    closeAnalysisModal();
                    addInventoryToDOM(data.inventory);
                    resetInventoryForm();
                    // Очищаем data-атрибут
                    document.getElementById('analysisModal').dataset.inventory = '';
                } else {
                    window.showNotification('error', data.message || 'Ошибка при сохранении');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                window.showNotification('error', error.message || 'Ошибка при сохранении инвентаризации');
            })
            .finally(() => {
                saveBtn.innerHTML = originalText;
                saveBtn.disabled = false;
            });
        }
        // Функция для редактирования инвентаризации
        function editInventory(event, id) {
            event.stopPropagation();
            fetch(`/inventories/${id}/edit`)
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        const inventory = data.inventory;
                        const modalBody = document.getElementById('editInventoryModalBody');

                        // Создаем форму редактирования
                        const formHtml = `
                            <form id="editInventoryForm" data-project-id="${inventory.project_id}">
                                @csrf
                        @method('PUT')
                        <div class="form-row">
                            <div class="form-group">
                                <label>Дата </label>
                                <input type="date" name="date" required class="form-control" value="${inventory.date}">
                                    </div>
                                    <div class="form-group">
                                        <label>Ответственный </label>
                                        <select name="user_id" required class="form-control">
                                            <option value="">Выберите ответственного</option>
                                            @foreach($users as $user)
                        <option value="{{ $user->id }}" ${inventory.user_id == {{ $user->id }} ? 'selected' : ''}>
                                                    {{ $user->name }}
                        </option>
@endforeach
                        </select>
                    </div>
                </div>
                <div class="form-group">
                    <label>Примечания</label>
                    <textarea name="notes" rows="2" class="form-control">${inventory.notes || ''}</textarea>
                                </div>
                                <div class="items-container" id="editItemsContainer">
                                    <h3>Товары</h3>
                                    <div class="item-row template" style="display: none;">
                                        <div class="form-row">
                                            <div class="form-group product-group large-col">
                                                <label>Товар</label>
                                                <div class="product-search-container">
                                                    <input type="text" class="product-search-input form-control large-col"
                                                           placeholder="Начните вводить название товара..."
                                                           oninput="searchProducts(this)"
                                                           onfocus="showProductDropdown(this)" autocomplete="off">
                                                    <div class="product-dropdown" style="display: none;">
                                                        <div class="product-dropdown-list"></div>
                                                    </div>
                                                    <select name="items[0][product_id]" class="form-control product-select large-col" style="display: none;">
                                                        <option value="">Выберите товар</option>
                                                        @foreach($products as $product)
                                                            <option value="{{ $product->id }}" data-stock="{{ $product->stock }}">
                                                                {{ $product->name }}
                                                            </option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                            </div>
                                            <div class="form-group small-col">
                                                <label>Склад</label>
                                                <input type="number" name="items[0][warehouse_qty]" class="form-control small-col" value="0" readonly>
                                            </div>
                                            <div class="form-group small-col">
                                                <label>Кол</label>
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
                                    ${inventory.items.map((item, index) => `
                                        <div class="item-row">
                                            <div class="form-row">
                                                <div class="form-group product-group large-col">
                                                    <label>Товар</label>
                                                    <div class="product-search-container">
                                                        <input type="text" class="product-search-input form-control large-col" value="${item.product.name}" readonly>
                                                        <select name="items[${index}][product_id]" class="form-control product-select large-col" style="display: none;">
                                                            <option value="${item.product_id}" selected>${item.product.name}</option>
                                                        </select>
                                                    </div>
                                                </div>
                                                <div class="form-group small-col">
                                                    <label>Склад</label>
                                                    <input type="number" name="items[${index}][warehouse_qty]" class="form-control small-col" value="${item.warehouse_qty}" readonly>
                                                </div>
                                                <div class="form-group small-col">
                                                    <label>Кол</label>
                                                    <input type="number" name="items[${index}][actual_qty]" required class="form-control small-col" min="0" value="${item.actual_qty}">
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
                                    `).join('')}
                                </div>
                                <div class="form-actions">
                                    <button type="button" class="btn-add-item" onclick="addItemRow('editItemsContainer')">
                                        <svg class="icon" viewBox="0 0 24 24" fill="currentColor">
                                            <path d="M19 13h-6v6h-2v-6H5v-2h6V5h2v6h6v2z"/>
                                        </svg>
                                        Добавить товар
                                    </button>
                                    <button type="button" class="btn-cancel" onclick="closeEditInventoryModal()">Отмена</button>
                                    <button type="button" class="btn-submit" onclick="analyzeEditInventory(${inventory.id})">Провести инвентаризацию</button>
                                </div>
                            </form>
                        `;

                        modalBody.innerHTML = formHtml;
                        document.getElementById('editInventoryModal').style.display = 'block';
                    } else {
                        window.showNotification('error', data.message || 'Ошибка загрузки данных инвентаризации');
                    }
                })
                .catch(error => {
                    window.showNotification('error', 'Ошибка загрузки данных инвентаризации');
                });
        }

        // Функция для анализа при редактировании инвентаризации
        function analyzeEditInventory(id) {
            clearErrors('editInventoryForm');

            // Собираем данные из формы
            const form = document.getElementById('editInventoryForm');
            const date = form.querySelector('[name="date"]').value;
            const user_id = form.querySelector('[name="user_id"]').value;
            const notes = form.querySelector('[name="notes"]').value;

            const items = [];
            const seenProducts = new Set();
            let hasErrors = false;

            form.querySelectorAll('#editItemsContainer .item-row').forEach(row => {
                if (row.classList.contains('template')) return;
                const productId = row.querySelector('[name*="product_id"]').value;
                const actualQty = row.querySelector('[name*="actual_qty"]').value;
                const warehouseQty = row.querySelector('[name*="warehouse_qty"]').value || 0;
                const productName = row.querySelector('.product-search-input').value;

                if (!productId) {
                    showError(row.querySelector('[name*="product_id"]'), 'Выберите товар');
                    hasErrors = true;
                    return;
                }
                if (seenProducts.has(productId)) {
                    showError(row.querySelector('[name*="product_id"]'), 'Этот товар уже добавлен');
                    hasErrors = true;
                    return;
                }
                seenProducts.add(productId);

                items.push({
                    product_id: productId,
                    product_name: productName,
                    warehouse_qty: parseInt(warehouseQty) || 0,
                    actual_qty: parseInt(actualQty) || 0,
                    difference: (parseInt(actualQty) || 0) - (parseInt(warehouseQty) || 0)
                });
            });

            if (hasErrors) return;
            if (items.length === 0) {
                window.showNotification('error', 'Добавьте хотя бы один товар');
                return;
            }
            if (!date || !user_id) {
                window.showNotification('error', 'Заполните все обязательные поля');
                return;
            }

            // Формируем объект для отправки
            const data = {
                _method: 'PUT',
                date: date,
                user_id: user_id,
                notes: notes,
                items: items
            };
            console.log('Данные для обновления:', data);

            // Отправляем на сервер
            fetch(`/inventories/${id}`, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    'Accept': 'application/json',
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify(data)
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    window.showNotification('success', 'Инвентаризация успешно обновлена');
                    closeEditInventoryModal(true);
                    updateInventoryInDOM(data.inventory);
                } else {
                    window.showNotification('error', data.message || 'Ошибка обновления инвентаризации');
                }
            })
            .catch(error => {
                console.error('Server error:', error);
                window.showNotification('error', 'Ошибка обновления инвентаризации');
            });
        }

        // Функции для подтверждения удаления
        function confirmDeleteInventory(event, id) {
            event.stopPropagation();
            currentDeleteId = id;
            document.getElementById('confirmationModal').style.display = 'block';
        }

        document.getElementById('cancelDelete').addEventListener('click', function() {
            document.getElementById('confirmationModal').style.display = 'none';
            currentDeleteId = null;
        });

        document.getElementById('confirmDeleteBtn').addEventListener('click', function() {
            if (currentDeleteId) {
                deleteInventory(currentDeleteId);
            }
            document.getElementById('confirmationModal').style.display = 'none';
            currentDeleteId = null;
        });

        // Функция для удаления инвентаризации
        function deleteInventory(id) {
            fetch(`/inventories/${id}`, {
                method: 'DELETE',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    'Accept': 'application/json',
                    'Content-Type': 'application/json'
                }
            })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        window.showNotification('success', 'Инвентаризация успешно удалена');
                        const summaryRow = document.getElementById(`inventory-row-${id}`);
                        const detailsRow = document.getElementById(`details-row-${id}`);
                        if (summaryRow) summaryRow.remove();
                        if (detailsRow) detailsRow.remove();
                    } else {
                        window.showNotification('error', data.message || 'Ошибка при удалении инвентаризации');
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    window.showNotification('error', 'Ошибка при удалении инвентаризации');
                });
        }

        // Функции для работы с DOM
        function addInventoryToDOM(inventory) {
            // Форматируем дату
            const formattedDate = inventory.date
                ? new Date(inventory.date).toLocaleDateString('ru-RU', {
                    day: '2-digit',
                    month: '2-digit',
                    year: 'numeric'
                }).replace(/\./g, '.')
                : 'Нет даты';

            // Генерируем HTML для товаров с расхождениями
            const itemsWithDiscrepancies = inventory.items.filter(item => item.difference !== 0);

            const itemsHTML = itemsWithDiscrepancies.map(item => {
                const status = item.difference == 0 ? '✅ Совпадает' :
                    item.difference > 0 ? '⚠️ Лишнее' : '❌ Не хватает';

                return `
                    <tr>
                        <td>
                            ${item.product.photo
                    ? `<img src="/storage/${item.product.photo}" class="product-photo" alt="${item.product.name}">`
                    : `<div class="no-photo">Нет фото</div>`
                }
                        </td>
                        <td>${item.product.name}</td>
                        <td class="small-col">${item.warehouse_qty} шт</td>
                        <td class="small-col">${item.actual_qty} шт</td>
                        <td class="${item.difference > 0 ? 'text-success' : 'text-danger'}">
                            ${item.difference > 0 ? '+' : ''}${item.difference} шт
                        </td>
                        <td>${status}</td>
                    </tr>
                `;
            }).join('');

            // Создаём HTML инвентаризации
            const inventoryHTML = `
                <tr class="inventory-summary-row" id="inventory-row-${inventory.id}" onclick="toggleInventoryDetailsRow(${inventory.id})">
                    <td>${formattedDate}</td>
                    <td>${inventory.user.name}</td>
                    <td>${inventory.discrepancies_count}
                        ${inventory.shortages_count > 0 ? `(${inventory.shortages_count} нехватка)` : ''}
                        ${inventory.overages_count > 0 ? `(${inventory.overages_count} излишек)` : ''}
                    </td>
                    <td title="${inventory.notes}">${inventory.notes ? (inventory.notes.length > 30 ? inventory.notes.substring(0, 30) + '…' : inventory.notes) : '—'}</td>
                    <td>
                        <div class="inventory-actions">
                            <button class="btn-edit" onclick="editInventory(event, ${inventory.id})">
                                <svg class="icon" viewBox="0 0 20 20" fill="currentColor">
                                    <path d="M13.586 3.586a2 2 0 112.828 2.828l-.793.793-2.828-2.828.793-.793zM11.379 5.793L3 14.172V17h2.828l8.38-8.379-2.83-2.828z"/>
                                </svg>
                                Ред.
                            </button>
                            <button class="btn-delete" onclick="confirmDeleteInventory(event, ${inventory.id})">
                                <svg class="icon" viewBox="0 0 20 20" fill="currentColor">
                                    <path fill-rule="evenodd" d="M9 2a1 1 0 00-.894.553L7.382 4H4a1 1 0 000 2v10a2 2 0 002 2h8a2 2 0 002-2V6a1 1 0 100-2h-3.382l-.724-1.447A1 1 0 0011 2H9zM7 8a1 1 0 012 0v6a1 1 0 11-2 0V8zm5-1a1 1 0 00-1 1v6a1 1 0 102 0V8a1 1 0 00-1-1z" clip-rule="evenodd"/>
                                </svg>
                                Удалить
                            </button>
                            <button class="btn-pdf" onclick="downloadInventoryPdf(event, ${inventory.id})">
                                <svg class="icon" viewBox="0 0 20 20" fill="currentColor">
                                    <path d="M6 2a2 2 0 00-2 2v12a2 2 0 002 2h8a2 2 0 002-2V7.414A2 2 0 0015.414 6L13 3.586A2 2 0 0011.586 3H6zm2 2h3v3a1 1 0 001 1h3v9a1 1 0 01-1 1H6a1 1 0 01-1-1V4a1 1 0 011-1zm5 3.414V8h-2V6h.586L13 5.414zM8 10a1 1 0 100 2h4a1 1 0 100-2H8zm0 4a1 1 0 100 2h4a1 1 0 100-2H8z"/>
                                </svg>
                                PDF
                            </button>
                        </div>
                    </td>
                </tr>
                <tr class="inventory-details-row" id="details-row-${inventory.id}" style="display: none;">
                    <td colspan="5">
                        <div class="inventory-notes">${inventory.notes || ''}</div>
                        <table class="table-striped analysis-table products-table">
                            <thead>
                                <tr>
                                    <th>Фото</th>
                                    <th class="large-col">Товар</th>
                                    <th class="small-col">Склад</th>
                                    <th class="small-col">Кол</th>
                                    <th>Разница</th>
                                    <th>Статус</th>
                                </tr>
                            </thead>
                            <tbody>
                                ${itemsHTML}
                            </tbody>
                        </table>
                        <div class="view-all-items">
                            <button class="btn-view-all" onclick="viewAllInventoryItems(${inventory.id})">
                                Просмотреть весь список
                            </button>
                        </div>
                    </td>
                </tr>
            `;

            // Вставляем в DOM
            const inventoriesListBody = document.getElementById('inventoriesListBody');
            inventoriesListBody.insertAdjacentHTML('beforeend', inventoryHTML);
        }

        function updateInventoryInDOM(inventory) {
            const formattedDate = new Date(inventory.date).toLocaleDateString('ru-RU', {
                day: '2-digit',
                month: '2-digit',
                year: 'numeric'
            }).replace(/\./g, '.');

            // Создаём новую основную строку
            const summaryRow = document.createElement('tr');
            summaryRow.className = 'inventory-summary-row';
            summaryRow.id = `inventory-row-${inventory.id}`;
            summaryRow.setAttribute('onclick', `toggleInventoryDetailsRow(${inventory.id})`);
            summaryRow.innerHTML = `
                <td>${formattedDate}</td>
                <td>${inventory.user.name}</td>
                <td>${inventory.discrepancies_count}
                    ${inventory.shortages_count > 0 ? `(${inventory.shortages_count} нехватка)` : ''}
                    ${inventory.overages_count > 0 ? `(${inventory.overages_count} излишек)` : ''}
                </td>
                <td title="${inventory.notes}">${inventory.notes ? (inventory.notes.length > 30 ? inventory.notes.substring(0, 30) + '…' : inventory.notes) : '—'}</td>
                <td>
                    <div class="inventory-actions">
                        <button class="btn-edit" onclick="editInventory(event, ${inventory.id})">
                            <svg class="icon" viewBox="0 0 20 20" fill="currentColor">
                                <path d="M13.586 3.586a2 2 0 112.828 2.828l-.793.793-2.828-2.828.793-.793zM11.379 5.793L3 14.172V17h2.828l8.38-8.379-2.83-2.828z"/>
                            </svg>
                            Ред.
                        </button>
                        <button class="btn-delete" onclick="confirmDeleteInventory(event, ${inventory.id})">
                            <svg class="icon" viewBox="0 0 20 20" fill="currentColor">
                                <path fill-rule="evenodd" d="M9 2a1 1 0 00-.894.553L7.382 4H4a1 1 0 000 2v10a2 2 0 002 2h8a2 2 0 002-2V6a1 1 0 100-2h-3.382l-.724-1.447A1 1 0 0011 2H9zM7 8a1 1 0 012 0v6a1 1 0 11-2 0V8zm5-1a1 1 0 00-1 1v6a1 1 0 102 0V8a1 1 0 00-1-1z" clip-rule="evenodd"/>
                            </svg>
                            Удалить
                        </button>
                        <button class="btn-pdf" onclick="downloadInventoryPdf(event, ${inventory.id})">
                            <svg class="icon" viewBox="0 0 20 20" fill="currentColor">
                                <path d="M6 2a2 2 0 00-2 2v12a2 2 0 002 2h8a2 2 0 002-2V7.414A2 2 0 0015.414 6L13 3.586A2 2 0 0011.586 3H6zm2 2h3v3a1 1 0 001 1h3v9a1 1 0 01-1 1H6a1 1 0 01-1-1V4a1 1 0 011-1zm5 3.414V8h-2V6h.586L13 5.414zM8 10a1 1 0 100 2h4a1 1 0 100-2H8zm0 4a1 1 0 100 2h4a1 1 0 100-2H8z"/>
                            </svg>
                            PDF
                        </button>
                    </div>
                </td>
            `;

            // Создаём новую детальную строку
            const detailsRow = document.createElement('tr');
            detailsRow.className = 'inventory-details-row';
            detailsRow.id = `details-row-${inventory.id}`;
            detailsRow.style.display = 'none';
            const itemsWithDiscrepancies = inventory.items.filter(item => item.difference !== 0);
            const itemsHTML = itemsWithDiscrepancies.map(item => {
                const status = item.difference == 0 ? '✅ Совпадает' :
                    item.difference > 0 ? '⚠️ Лишнее' : '❌ Не хватает';
                return `
                    <tr>
                        <td>
                            ${item.product.photo
                                ? `<img src="/storage/${item.product.photo}" class="product-photo" alt="${item.product.name}">`
                                : `<div class="no-photo">Нет фото</div>`
                            }
                        </td>
                        <td>${item.product.name}</td>
                        <td class="small-col">${item.warehouse_qty} шт</td>
                        <td class="small-col">${item.actual_qty} шт</td>
                        <td class="${item.difference > 0 ? 'text-success' : 'text-danger'}">
                            ${item.difference > 0 ? '+' : ''}${item.difference} шт
                        </td>
                        <td>${status}</td>
                    </tr>
                `;
            }).join('');
            detailsRow.innerHTML = `
                <td colspan="5">
                    <div class="inventory-notes">${inventory.notes || ''}</div>
                    <table class="table-striped analysis-table products-table">
                        <thead>
                            <tr>
                                <th>Фото</th>
                                <th class="large-col">Товар</th>
                                <th class="small-col">Склад</th>
                                <th class="small-col">Кол</th>
                                <th>Разница</th>
                                <th>Статус</th>
                            </tr>
                        </thead>
                        <tbody>
                            ${itemsHTML}
                        </tbody>
                    </table>
                    <div class="view-all-items">
                        <button class="btn-view-all" onclick="viewAllInventoryItems(${inventory.id})">
                            Просмотреть весь список
                        </button>
                    </div>
                </td>
            `;

            // Заменяем обе строки в DOM
            const oldSummaryRow = document.getElementById(`inventory-row-${inventory.id}`);
            const oldDetailsRow = document.getElementById(`details-row-${inventory.id}`);
            if (oldSummaryRow && oldDetailsRow) {
                oldSummaryRow.parentNode.replaceChild(summaryRow, oldSummaryRow);
                oldDetailsRow.parentNode.replaceChild(detailsRow, oldDetailsRow);
            }
        }

        // Вспомогательные функции
        function showError(input, message) {
            input.classList.add('is-invalid');
            const errorElement = document.createElement('div');
            errorElement.className = 'error-message';
            errorElement.textContent = message;
            input.parentNode.insertBefore(errorElement, input.nextSibling);
        }

        // Универсальная функция для очистки ошибок формы
        function clearErrors(formId = null) {
            let form;
            if (formId) {
                form = document.getElementById(formId);
            } else {
                form = document;
            }
            if (!form) return;
            // Удаляем все сообщения об ошибках
            form.querySelectorAll('.error-message').forEach(el => el.remove());
            // Убираем класс is-invalid у всех полей
            form.querySelectorAll('.is-invalid').forEach(el => el.classList.remove('is-invalid'));
        }

        // Поиск инвентаризаций
        document.getElementById('searchInput').addEventListener('input', function() {
            const searchTerm = this.value.toLowerCase();
            const inventories = document.querySelectorAll('.inventory-summary-row');

            inventories.forEach(inventory => {
                const header = inventory.querySelector('.inventory-header');
                const textContent = header.textContent.toLowerCase();
                if (textContent.includes(searchTerm)) {
                    inventory.style.display = 'table-row';
                } else {
                    inventory.style.display = 'none';
                }
            });
        });

        // Глобальная переменная для хранения всех продуктов
        let allProducts = @json($products);

        // Функция для поиска товаров
        function searchProducts(input) {
            const searchTerm = input.value.toLowerCase();
            const dropdown = input.nextElementSibling;
            const dropdownList = dropdown.querySelector('.product-dropdown-list');
            const select = input.parentNode.querySelector('.product-select');

            if (searchTerm.length === 0) {
                dropdown.style.display = 'none';
                return;
            }

            const filteredProducts = allProducts.filter(product =>
                product.name.toLowerCase().includes(searchTerm)
            );

            if (filteredProducts.length === 0) {
                dropdownList.innerHTML = '<div class="product-dropdown-item">Товары не найдены</div>';
            } else {
                dropdownList.innerHTML = filteredProducts.map(product => `
                    <div class="product-dropdown-item"
                         data-id="${product.id}"
                         onclick="selectProduct(this, '${product.name}', ${product.id}, '${input.id}')">
                        ${product.name}
                    </div>
                `).join('');
            }

            dropdown.style.display = 'block';
        }

        // Показать выпадающий список
        function showProductDropdown(input) {
            if (input.value.length > 0) {
                searchProducts(input);
            } else {
                const dropdown = input.nextElementSibling;
                const dropdownList = dropdown.querySelector('.product-dropdown-list');
                dropdownList.innerHTML = allProducts.map(product => `
                    <div class="product-dropdown-item"
                         data-id="${product.id}"
                         onclick="selectProduct(this, '${product.name}', ${product.id}, '${input.id}')">
                        ${product.name}
                    </div>
                `).join('');
                dropdown.style.display = 'block';
            }
        }

        // Выбрать товар из списка
        function selectProduct(element, productName, productId, inputId) {
            const container = element.closest('.product-search-container');
            const input = container.querySelector('.product-search-input');
            const select = container.querySelector('.product-select');
            const dropdown = container.querySelector('.product-dropdown');

            input.value = productName;
            select.value = productId;
            dropdown.style.display = 'none';

            // Убираем выделение у всех элементов
            container.querySelectorAll('.product-dropdown-item').forEach(item => {
                item.classList.remove('selected');
            });

            // Выделяем выбранный элемент
            element.classList.add('selected');

            // Автоматически подставлять остаток на складе в warehouse_qty
            const warehouseQtyInput = container.closest('.item-row').querySelector('[name*="warehouse_qty"]');
            if (warehouseQtyInput && window.allProducts) {
                const product = window.allProducts.find(p => p.id == productId);
                let stock = 0;
                if (product && product.stock !== undefined && !isNaN(parseInt(product.stock))) {
                    stock = parseInt(product.stock);
                }
                warehouseQtyInput.value = stock;
            }
        }


        // Закрытие выпадающего списка при клике вне его
        document.addEventListener('click', function(e) {
            if (!e.target.closest('.product-search-container')) {
                document.querySelectorAll('.product-dropdown').forEach(dropdown => {
                    dropdown.style.display = 'none';
                });
            }
        });

        // --- Автоочистка поля при фокусе и возврат 0 при blur ---
        document.addEventListener('focusin', function(e) {
            if (e.target.matches('input[name*="actual_qty"], input[name*="warehouse_qty"]')) {
                if (e.target.value === '0') {
                    e.target.value = '';
                }
            }
        });
        document.addEventListener('focusout', function(e) {
            if (e.target.matches('input[name*="actual_qty"], input[name*="warehouse_qty"]')) {
                if (e.target.value === '' || e.target.value === null) {
                    e.target.value = '0';
                }
            }
        });

        // Подтверждение закрытия анализа инвентаризации
        function confirmCloseAnalysisModal() {
            const data = document.getElementById('analysisModal').dataset.inventory;
            const inventoryData = data ? JSON.parse(data) : null;
            if (inventoryData && inventoryData.items && inventoryData.items.length > 0) {
                document.getElementById('cancelInventoryModal').style.display = 'block';
                // Обновляем обработчик подтверждения отмены
                document.getElementById('confirmCancelInventoryBtn').onclick = function() {
                    document.getElementById('cancelInventoryModal').style.display = 'none';
                    closeAnalysisModal();
                    resetInventoryModal();
                    // Очищаем data-атрибут
                    document.getElementById('analysisModal').dataset.inventory = '';
                    window.showNotification('info', 'Изменения не сохранены');
                };
            } else {
                closeAnalysisModal();
                resetInventoryModal();
                document.getElementById('analysisModal').dataset.inventory = '';
            }
        }
        // Сохраняем стандартный обработчик для других случаев отмены
        const defaultCancelInventoryHandler = document.getElementById('confirmCancelInventoryBtn').onclick;

        // --- Увеличение фото товара при клике ---
        document.addEventListener('click', function(e) {
            if (e.target.closest('.zoomable-image')) {
                e.preventDefault();
                const imgSrc = e.target.closest('.zoomable-image').getAttribute('data-img');
                const zoomModal = document.getElementById('zoomImageModal');
                const zoomedImage = document.getElementById('zoomedImage');
                zoomedImage.src = imgSrc;
                zoomModal.style.display = 'block';
            }
            if (e.target.id === 'closeZoomImageModal' || (e.target.id === 'zoomImageModal' && e.target === document.getElementById('zoomImageModal'))) {
                document.getElementById('zoomImageModal').style.display = 'none';
                document.getElementById('zoomedImage').src = '';
            }
        });

        function downloadInventoryPdf(event, id) {
            event.stopPropagation();
            window.open(`/inventories/${id}/pdf`, '_blank');
        }

        // Функция для показа/скрытия деталей инвентаризации (для таблицы)
        function toggleInventoryDetailsRow(id) {
            const detailsRow = document.getElementById(`details-row-${id}`);
            if (detailsRow) {
                detailsRow.style.display = detailsRow.style.display === 'none' ? 'table-row' : 'none';
            }
        }
    </script>
@endsection
