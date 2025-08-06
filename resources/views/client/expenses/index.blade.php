@extends('client.layouts.app')

@section('content')
<div class="dashboard-container">
    <div class="expenses-container">
        <div class="expenses-header">
            <div class="header-top">
                <h1>{{ __('messages.expenses') }}</h1>
                <div class="header-actions">
                    <button class="btn-add-expense" onclick="openExpenseModal()">
                        <svg class="icon" viewBox="0 0 24 24" fill="currentColor">
                            <path d="M19 13h-6v6h-2v-6H5v-2h6V5h2v6h6v2z"/>
                        </svg>
                        {{ __('messages.add_expense') }}
                    </button>
                    <div class="search-box">
                        <svg class="search-icon" viewBox="0 0 24 24" fill="currentColor">
                            <path d="M15.5 14h-.79l-.28-.27a6.5 6.5 0 0 0 1.48-5.34c-.47-2.78-2.79-5-5.59-5.34a6.505 6.505 0 0 0-7.27 7.27c.34 2.8 2.56 5.12 5.34 5.59a6.5 6.5 0 0 0 5.34-1.48l.27.28v.79l4.25 4.25c.41.41 1.08.41 1.49 0 .41-.41.41-1.08 0-1.49L15.5 14zm-6 0C7.01 14 5 11.99 5 9.5S7.01 5 9.5 5 14 7.01 14 9.5 11.99 14 9.5 14z"/>
                        </svg>
                        <input type="text" placeholder="{{ __('messages.search') }}..." id="searchInput" autocomplete="off">
                    </div>
                </div>
            </div>
            
            <!-- Уведомления будут появляться здесь -->
        </div>

        <!-- Десктопная таблица -->
        <div class="expenses-list table-wrapper" id="expensesList">
            <table class="table-striped expenses-table" id="expensesTable">
                <thead>
                <tr>
                    <th>{{ __('messages.date') }}</th>
                    <th>{{ __('messages.category') }}</th>
                    <th>{{ __('messages.comment') }}</th>
                    <th>{{ __('messages.amount') }}</th>
                    <th>{{ __('messages.actions') }}</th>
                </tr>
                </thead>
                <tbody id="expensesTableBody">
                <!-- Данные будут загружаться через AJAX -->
                </tbody>
            </table>
        </div>
        
        <!-- Пагинация для десктопной таблицы -->
        <div id="expensesPagination"></div>

        <!-- Мобильные карточки расходов -->
        <div class="expenses-cards" id="expensesCards" style="display: none;">
            <!-- Карточки будут загружаться через AJAX -->
        </div>

        <!-- Пагинация для мобильных карточек -->
        <div id="mobileExpensesPagination" style="display: none;"></div>
    </div>

    <!-- Модальное окно добавления/редактирования расхода -->
    <div id="expenseModal" class="modal">
        <div class="modal-content">
            <div class="modal-header">
                <h2 id="modalTitle">{{ __('messages.add_expense') }}</h2>
                <span class="close" onclick="closeExpenseModal()">&times;</span>
            </div>
            <div class="modal-body">
                <form id="expenseForm">
                    @csrf
                    <input type="hidden" name="expense_id" id="expenseId">
                    <div class="form-group">
                        <label>{{ __('messages.date') }} *</label>
                        <input type="date" name="date" required class="form-control" data-locale="{{ app()->getLocale() }}"
                               data-locale="{{ app()->getLocale() }}"
                               data-month-names="{{ json_encode([
                                   __('messages.january'), __('messages.february'), __('messages.march'),
                                   __('messages.april'), __('messages.may'), __('messages.june'),
                                   __('messages.july'), __('messages.august'), __('messages.september'),
                                   __('messages.october'), __('messages.november'), __('messages.december')
                               ]) }}"
                               data-day-names="{{ json_encode([
                                   __('messages.sun'), __('messages.mon'), __('messages.tue'),
                                   __('messages.wed'), __('messages.thu'), __('messages.fri'), __('messages.sat')
                               ]) }}">
                    </div>
                    <div class="form-group">
                        <label>{{ __('messages.category') }} *</label>
                        <select name="category" required class="form-control">
                            <option value="">{{ __('messages.select_category') }}</option>
                            @foreach($categories ?? [] as $category)
                                <option value="{{ $category }}">{{ $category }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="form-group">
                        <label>{{ __('messages.comment') }}</label>
                        <textarea name="comment" class="form-control" rows="3"></textarea>
                    </div>
                    <div class="form-group">
                        <label>{{ __('messages.amount') }} *</label>
                        <input type="number" step="0.01" name="amount" required class="form-control" min="0">
                    </div>
                    <div class="form-actions">
                        <button type="button" class="btn-cancel" onclick="closeExpenseModal()">{{ __('messages.cancel') }}</button>
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
                <span class="close" onclick="closeConfirmationModal()">&times;</span>
            </div>
            <div class="modal-body">
                <p>{{ __('messages.confirm_delete_expense') }}</p>
            </div>
            <div class="modal-footer">
                    <button type="button" class="btn-cancel" onclick="closeConfirmationModal()">{{ __('messages.cancel') }}</button>
                    <button type="button" class="btn-delete" onclick="deleteExpense()">{{ __('messages.delete') }}</button>
                </div>
            </div>
        </div>
    </div>

@push('scripts')
<script src="{{ asset('client/js/expenses.js') }}"></script>
@endpush
@endsection 
