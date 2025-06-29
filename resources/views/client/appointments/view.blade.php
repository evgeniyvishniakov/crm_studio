<div class="modal-body">
    <div class="row">
        <div class="col-md-12">
            <h4>Товары клиента на эту дату</h4>
            <div class="table-responsive">
                <table class="table table-bordered" id="productsTable">
                    <thead>
                        <tr>
                            <th>Название</th>
                            <th>Количество</th>
                            <th>Цена</th>
                            <th>Сумма</th>
                            <th>Действия</th>
                        </tr>
                    </thead>
                    <tbody>
                        <!-- Товары будут добавлены через JavaScript -->
                    </tbody>
                </table>
            </div>
            <button class="btn-add-product" id="showAddProductFormBtn">
                <svg class="icon" viewBox="0 0 24 24" fill="currentColor">
                    <path d="M19 13h-6v6h-2v-6H5v-2h6V5h2v6h6v2z"/>
                </svg>
                Добавить товар
            </button>
            <div id="addProductForm" style="display: none; margin-top: 20px;">
                <div class="form-row">
                    <div class="form-group" style="flex: 2;">
                        <label for="productSelect">Товар</label>
                        <select id="productSelect" class="form-control">
                            <option value="">Выберите товар</option>
                        </select>
                    </div>
                </div>
                <div class="form-row">
                    <div class="form-group">
                        <label for="productQuantity">Количество</label>
                        <input type="number" id="productQuantity" class="form-control" min="1" value="1">
                    </div>
                    <div class="form-group">
                        <label for="productPrice">Розничная цена</label>
                        <input type="number" id="productPrice" class="form-control" step="0.01">
                    </div>
                    <div class="form-group">
                        <label for="productPurchasePrice">Оптовая цена</label>
                        <input type="number" id="productPurchasePrice" class="form-control" step="0.01" readonly>
                    </div>
                </div>
                <div class="form-actions">
                    <button type="button" class="btn-submit" id="submitAddProduct">Добавить</button>
                    <button type="button" class="btn-cancel" id="cancelAddProduct">Отмена</button>
                </div>
            </div>
        </div>
    </div>
</div> 
