@extends('client.layouts.app')

@section('content')
<div class="dashboard-container">
    <div class="services-container">
        <div class="services-header">
            <h1>{{ __('messages.suppliers') }}</h1>
            <div class="suppliers-header-actions">
                <button class="btn-add-service" onclick="openModal()">
                    <svg class="icon" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd" d="M10 3a1 1 0 011 1v5h5a1 1 0 110 2h-5v5a1 1 0 11-2 0v-5H4a1 1 0 110-2h5V4a1 1 0 011-1z" clip-rule="evenodd" />
                    </svg>
                    {{ __('messages.add_supplier') }}
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
                    <th>{{ __('messages.supplier_name') }}</th>
                    <th>{{ __('messages.supplier_contact_person') }}</th>
                    <th>{{ __('messages.supplier_phone') }}</th>
                    <th>{{ __('messages.supplier_email') }}</th>
                    <th>{{ __('messages.supplier_status') }}</th>
                    <th class="actions-column">{{ __('messages.actions') }}</th>
                </tr>
                </thead>
                <tbody id="servicesTableBody">
                @foreach($suppliers as $supplier)
                    <tr id="supplier-{{ $supplier->id }}">
                        <td>{{ $supplier->name }}</td>
                        <td>{{ $supplier->contact_person ?? '—' }}</td>
                        <td>
                            @if($supplier->phone)
                                <a href="tel:{{ $supplier->phone }}">{{ $supplier->phone }}</a>
                            @else
                                —
                            @endif
                        </td>
                        <td>
                            @if($supplier->email)
                                <a href="mailto:{{ $supplier->email }}">{{ $supplier->email }}</a>
                            @else
                                —
                            @endif
                        </td>
                        <td>
                            <span class="status-badge {{ $supplier->status ? 'active' : 'inactive' }}">
                                {{ $supplier->status ? __('messages.supplier_active') : __('messages.supplier_inactive') }}
                            </span>
                        </td>
                        <td class="actions-cell">
                            <button class="btn-edit">
                                <svg class="icon" viewBox="0 0 20 20" fill="currentColor">
                                    <path d="M13.586 3.586a2 2 0 112.828 2.828l-.793.793-2.828-2.828.793-.793zM11.379 5.793L3 14.172V17h2.828l8.38-8.379-2.83-2.828z" />
                                </svg>
                                {{ __('messages.edit_short') }}
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
            <div id="suppliersPagination"></div>
        </div>

        <!-- Мобильные карточки поставщиков -->
        <div class="suppliers-cards" id="suppliersCards" style="display: none;">
            <!-- Карточки будут добавлены через JavaScript -->
        </div>

        <!-- Пагинация для мобильных карточек -->
        <div id="mobileSuppliersPagination" style="display: none;"></div>
    </div>

    <!-- Модальное окно для добавления поставщика -->
    <div id="addServiceModal" class="modal">
        <div class="modal-content">
            <div class="modal-header">
                <h2>{{ __('messages.add_new_supplier') }}</h2>
                <span class="close" onclick="closeModal()">&times;</span>
            </div>
            <div class="modal-body">
                <form id="addServiceForm">
                    @csrf
                    <div class="form-group">
                        <label for="serviceName">{{ __('messages.supplier_name') }} *</label>
                        <input type="text" id="serviceName" name="name" required>
                    </div>
                    <div class="form-group">
                        <label for="serviceContactPerson">{{ __('messages.supplier_contact_person') }}</label>
                        <input type="text" id="serviceContactPerson" name="contact_person">
                    </div>
                    <div class="form-group">
                        <label for="servicePhone">{{ __('messages.supplier_phone') }}</label>
                        <input type="tel" id="servicePhone" name="phone">
                    </div>
                    <div class="form-group">
                        <label for="serviceEmail">{{ __('messages.supplier_email') }}</label>
                        <input type="email" id="serviceEmail" name="email">
                    </div>
                    <div class="form-group">
                        <label for="serviceAddress">{{ __('messages.supplier_address') }}</label>
                        <input type="text" id="serviceAddress" name="address">
                    </div>
                    <div class="form-group">
                        <label for="serviceInstagram">{{ __('messages.supplier_instagram') }}</label>
                        <input type="text" id="serviceInstagram" name="instagram" placeholder="@username">
                    </div>
                    <div class="form-group">
                        <label for="serviceInn">{{ __('messages.supplier_inn') }}</label>
                        <input type="text" id="serviceInn" name="inn">
                    </div>
                    <div class="form-group">
                        <label for="serviceNote">{{ __('messages.supplier_note') }}</label>
                        <textarea id="serviceNote" name="note" rows="3"></textarea>
                    </div>
                    <div class="form-group">
                        <label for="serviceStatus">{{ __('messages.supplier_status') }}</label>
                        <select id="serviceStatus" name="status">
                            <option value="1">{{ __('messages.supplier_active') }}</option>
                            <option value="0">{{ __('messages.supplier_inactive') }}</option>
                        </select>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn-cancel" onclick="closeModal()">{{ __('messages.cancel') }}</button>
                        <button type="submit" class="btn-submit">{{ __('messages.add_supplier') }}</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Модальное окно для редактирования поставщика -->
    <div id="editServiceModal" class="modal">
        <div class="modal-content">
            <div class="modal-header">
                <h2>{{ __('messages.edit_supplier') }}</h2>
                <span class="close" onclick="closeEditModal()">&times;</span>
            </div>
            <div class="modal-body">
                <form id="editServiceForm">
                    @csrf
                    <input type="hidden" id="editServiceId" name="supplier_id">
                    <div class="form-group">
                        <label for="editServiceName">{{ __('messages.supplier_name') }} *</label>
                        <input type="text" id="editServiceName" name="name" required>
                    </div>
                    <div class="form-group">
                        <label for="editServiceContactPerson">{{ __('messages.supplier_contact_person') }}</label>
                        <input type="text" id="editServiceContactPerson" name="contact_person">
                    </div>
                    <div class="form-group">
                        <label for="editServicePhone">{{ __('messages.supplier_phone') }}</label>
                        <input type="tel" id="editServicePhone" name="phone">
                    </div>
                    <div class="form-group">
                        <label for="editServiceEmail">{{ __('messages.supplier_email') }}</label>
                        <input type="email" id="editServiceEmail" name="email">
                    </div>
                    <div class="form-group">
                        <label for="editServiceAddress">{{ __('messages.supplier_address') }}</label>
                        <input type="text" id="editServiceAddress" name="address">
                    </div>
                    <div class="form-group">
                        <label for="editServiceInstagram">{{ __('messages.supplier_instagram') }}</label>
                        <input type="text" id="editServiceInstagram" name="instagram" placeholder="@username">
                    </div>
                    <div class="form-group">
                        <label for="editServiceInn">{{ __('messages.supplier_inn') }}</label>
                        <input type="text" id="editServiceInn" name="inn">
                    </div>
                    <div class="form-group">
                        <label for="editServiceNote">{{ __('messages.supplier_note') }}</label>
                        <textarea id="editServiceNote" name="note" rows="3"></textarea>
                    </div>
                    <div class="form-group">
                        <label for="editServiceStatus">{{ __('messages.supplier_status') }}</label>
                        <select id="editServiceStatus" name="status">
                            <option value="1">{{ __('messages.supplier_active') }}</option>
                            <option value="0">{{ __('messages.supplier_inactive') }}</option>
                        </select>
                    </div>
                    <div class="modal-footer">
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
                <p>{{ __('messages.confirm_delete_supplier') }}</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn-cancel" id="cancelDelete">{{ __('messages.cancel') }}</button>
                <button type="button" class="btn-delete" id="confirmDelete">{{ __('messages.delete') }}</button>
            </div>
        </div>
    </div>
</div>

@push('scripts')
    <script src="{{ asset('client/js/suppliers.js') }}"></script>
@endpush
@endsection
