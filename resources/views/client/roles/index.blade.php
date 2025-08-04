@extends('client.layouts.app')

@section('content')

<div class="dashboard-container">
    <div class="roles-container">
        <div class="roles-header">
            <div class="header-top">
        <h1>{{ __('messages.roles_and_permissions') }}</h1>
        <div class="header-actions">
                    <button class="btn-add-role" id="btnAddRole">
                        <svg class="icon" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd" d="M10 3a1 1 0 011 1v5h5a1 1 0 110 2h-5v5a1 1 0 11-2 0v-5H4a1 1 0 110-2h5V4a1 1 0 011-1z" clip-rule="evenodd" />
                        </svg>
                        {{ __('messages.add_role') }}
                    </button>
                    <div class="search-box">
                        <svg class="search-icon" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd" d="M8 4a4 4 0 100 8 4 4 0 000-8zM2 8a6 6 0 1110.89 3.476l4.817 4.817a1 1 0 01-1.414 1.414l-4.816-4.816A6 6 0 012 8z" clip-rule="evenodd" />
                        </svg>
                        <input type="text" placeholder="{{ __('messages.search') }}..." id="searchInput">
                    </div>
                </div>
            </div>
            
            <!-- Мобильная версия заголовка -->
            <div class="mobile-header">
                <h1 class="mobile-title">{{ __('messages.roles_and_permissions') }}</h1>
                <div class="mobile-header-actions">
                    <button class="btn-add-role" id="btnAddRoleMobile">
                <svg class="icon" viewBox="0 0 20 20" fill="currentColor">
                    <path fill-rule="evenodd" d="M10 3a1 1 0 011 1v5h5a1 1 0 110 2h-5v5a1 1 0 11-2 0v-5H4a1 1 0 110-2h5V4a1 1 0 011-1z" clip-rule="evenodd" />
                </svg>
                {{ __('messages.add_role') }}
            </button>
                    <div class="search-box">
                        <svg class="search-icon" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd" d="M8 4a4 4 0 100 8 4 4 0 000-8zM2 8a6 6 0 1110.89 3.476l4.817 4.817a1 1 0 01-1.414 1.414l-4.816-4.816A6 6 0 012 8z" clip-rule="evenodd" />
                        </svg>
                        <input type="text" placeholder="{{ __('messages.search') }}..." id="searchInputMobile">
                    </div>
                </div>
            </div>
        </div>
        <div id="notification"></div>
    <!-- Десктопная таблица -->
    <div class="table-wrapper">
        <table class=" table-striped clients-table">
            <thead>
                <tr>
                    <th>{{ __('messages.table_name') }}</th>
                    <th>{{ __('messages.table_permissions') }}</th>
                    <th>{{ __('messages.table_actions') }}</th>
                </tr>
            </thead>
            <tbody>
                @forelse($roles as $role)
                    <tr id="role-{{ $role->id }}">
                        <td>{{ __('messages.role_' . $role->name) }}</td>
                        <td>
                            @php
                                // Получаем список permissions для роли
                                $rolePerms = isset($role->permissions) ? $role->permissions : (\DB::table('role_permission')->where('role_id', $role->id)->pluck('permission_id')->toArray());
                                $permNames = [];
                                if (!empty($rolePerms)) {
                                    $allPerms = isset($permissions) ? $permissions : \DB::table('permissions')->get();
                                    foreach ($allPerms as $perm) {
                                        if (is_object($perm) && in_array($perm->id, $rolePerms)) {
                                            $permKey = str_replace(['-', '.'], '_', $perm->name);
                                            $permNames[] = __('messages.permission_' . $permKey);
                                        }
                                    }
                                }
                            @endphp
                            {{ $permNames ? implode(', ', $permNames) : '—' }}
                        </td>
                        <td class="actions-cell" style="vertical-align: middle;">
                            @if($role->name !== 'admin' && !$role->is_system)
                                                            <button class="btn-edit" data-id="{{ $role->id }}" title="{{ __('messages.edit') }}">
                                <svg class="icon" viewBox="0 0 20 20" fill="currentColor">
                                    <path d="M13.586 3.586a2 2 0 112.828 2.828l-.793.793-2.828-2.828.793-.793zM11.379 5.793L3 14.172V17h2.828l8.38-8.379-2.83-2.828.793-.793z" />
                                </svg>
                            </button>
                            <button class="btn-delete" data-id="{{ $role->id }}" title="{{ __('messages.delete') }}">
                                <svg class="icon" viewBox="0 0 20 20" fill="currentColor">
                                    <path fill-rule="evenodd" d="M9 2a1 1 0 00-.894.553L7.382 4H4a1 1 0 000 2v10a2 2 0 002 2h8a2 2 0 002-2V6a1 1 0 100-2h-3.382l-.724-1.447A1 1 0 0011 2H9zM7 8a1 1 0 012 0v6a1 1 0 11-2 0V8zm5-1a1 1 0 00-1 1v6a1 1 0 102 0V8a1 1 0 00-1-1z" clip-rule="evenodd" />
                                </svg>
                            </button>
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="3" style="text-align:center; color:#888; padding:40px 0;">{{ __('messages.no_data_yet_add_first_role') }}</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <!-- Мобильные карточки ролей -->
    <div class="roles-cards" id="rolesCards" style="display: none;">
        @forelse($roles as $role)
            @php
                // Получаем список permissions для роли
                $rolePerms = isset($role->permissions) ? $role->permissions : (\DB::table('role_permission')->where('role_id', $role->id)->pluck('permission_id')->toArray());
                $permNames = [];
                if (!empty($rolePerms)) {
                    $allPerms = isset($permissions) ? $permissions : \DB::table('permissions')->get();
                    foreach ($allPerms as $perm) {
                        if (is_object($perm) && in_array($perm->id, $rolePerms)) {
                            $permKey = str_replace(['-', '.'], '_', $perm->name);
                            $permNames[] = __('messages.permission_' . $permKey);
                        }
                    }
                }
            @endphp
            <div class="role-card" id="role-card-{{ $role->id }}">
                <div class="role-card-header">
                    <div class="role-main-info">
                        <div class="role-icon">
                            <svg class="icon" viewBox="0 0 20 20" fill="currentColor">
                                <path fill-rule="evenodd" d="M10 9a3 3 0 100-6 3 3 0 000 6zm-7 9a7 7 0 1114 0H3z" clip-rule="evenodd" />
                            </svg>
                        </div>
                        <div class="role-name" data-role-name="{{ $role->name }}">{{ __('messages.role_' . $role->name) }}</div>
                    </div>
                    <div class="role-type">
                        @if($role->name === 'admin')
                            <span class="role-badge admin">{{ __('messages.system_role') }}</span>
                        @elseif($role->is_system)
                            <span class="role-badge system">{{ __('messages.system_role') }}</span>
                        @else
                            <span class="role-badge custom">{{ __('messages.custom_role') }}</span>
                        @endif
                    </div>
                </div>
                <div class="role-info">
                    <div class="role-info-item">
                        <div class="role-info-label">
                            <svg class="icon" viewBox="0 0 20 20" fill="currentColor">
                                <path fill-rule="evenodd" d="M6 2a1 1 0 00-1 1v1H4a2 2 0 00-2 2v10a2 2 0 002 2h12a2 2 0 002-2V6a2 2 0 00-2-2h-1V3a1 1 0 00-1-1H6zm5 5a1 1 0 10-2 0v3.586l-1.293-1.293a1 1 0 10-1.414 1.414l3 3a1 1 0 001.414 0l3-3a1 1 0 00-1.414-1.414L11 10.586V7z" clip-rule="evenodd" />
                            </svg>
                            {{ __('messages.table_permissions') }}:
                        </div>
                        <div class="role-info-value">
                            @if($permNames)
                                <div class="permissions-tags">
                                    @foreach($permNames as $permName)
                                        <span class="permission-tag">{{ $permName }}</span>
                                    @endforeach
                                </div>
                            @else
                                <span class="no-permissions">{{ __('messages.no_permissions') }}</span>
                            @endif
                        </div>
                    </div>
                </div>
                @if($role->name !== 'admin' && !$role->is_system)
                <div class="role-actions">
                    <button class="btn-edit" title="{{ __('messages.edit') }}" onclick="openEditRoleModalFromCard({{ $role->id }})">
                        <svg class="icon" viewBox="0 0 20 20" fill="currentColor">
                            <path d="M13.586 3.586a2 2 0 112.828 2.828l-.793.793-2.828-2.828.793-.793zM11.379 5.793L3 14.172V17h2.828l8.38-8.379-2.83-2.828z" />
                        </svg>
                        {{ __('messages.edit') }}
                    </button>
                    <button class="btn-delete" title="{{ __('messages.delete') }}" onclick="showDeleteConfirmation({{ $role->id }})">
                        <svg class="icon" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd" d="M9 2a1 1 0 00-.894.553L7.382 4H4a1 1 0 000 2v10a2 2 0 002 2h8a2 2 0 002-2V6a1 1 0 100-2h-3.382l-.724-1.447A1 1 0 0011 2H9zM7 8a1 1 0 012 0v6a1 1 0 11-2 0V8zm5-1a1 1 0 00-1 1v6a1 1 0 102 0V8a1 1 0 00-1-1z" clip-rule="evenodd" />
                        </svg>
                        {{ __('messages.delete') }}
                    </button>
                </div>
                @endif
            </div>
        @empty
            <div class="no-roles-message">
                <div class="no-roles-icon">
                    <svg class="icon" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd" d="M10 9a3 3 0 100-6 3 3 0 000 6zm-7 9a7 7 0 1114 0H3z" clip-rule="evenodd" />
                    </svg>
                </div>
                <div class="no-roles-text">{{ __('messages.no_data_yet_add_first_role') }}</div>
            </div>
        @endforelse
    </div>

    <!-- Мобильная пагинация -->
    <div id="mobileRolesPagination" class="mobile-pagination" style="display: none;">
        <!-- Пагинация будет добавлена через JavaScript -->
    </div>
    <!-- Модальное окно подтверждения удаления -->
    <div id="roleConfirmationModal" class="confirmation-modal" style="display:none;">
        <div class="confirmation-content">
            <h3>{{ __('messages.confirmation_delete') }}</h3>
            <p>{{ __('messages.are_you_sure_you_want_to_delete_this_role') }}</p>
            <div class="confirmation-buttons">
                <button id="cancelDeleteRole" class="cancel-btn">{{ __('messages.cancel') }}</button>
                <button id="confirmDeleteRole" class="confirm-btn">{{ __('messages.delete') }}</button>
            </div>
        </div>
    </div>
</div>
<!-- Модальное окно для добавления/редактирования роли -->
<div id="roleModal" class="modal" style="display:none;">
    <div class="modal-content">
        <div class="modal-header">
            <h2 id="roleModalTitle">{{ __('messages.add_role') }}</h2>
            <span class="close" onclick="closeRoleModal()">&times;</span>
        </div>
        <div class="modal-body">
            <form id="roleForm">
                @csrf
                <div class="form-group">
                    <label for="roleSelect">{{ __('messages.role') }}</label>
                    <select id="roleSelect" name="name" required onchange="onRoleSelectChange()">
                        <option value="">{{ __('messages.select_role') }}</option>
                        @foreach(config('roles') as $key => $label)
                            @if($key !== 'admin')
                                <option value="{{ $key }}">{{ __('messages.role_' . $key) }}</option>
                            @endif
                        @endforeach
                    </select>
                </div>
            
                <div class="form-group">
                    <label>{{ __('messages.permissions') }}</label>
                    <div class="permissions-list">
                        @foreach($permissions as $perm)
                            <label>
                                <input type="checkbox" name="permissions[]" value="{{ $perm->name }}">
                                <span>{{ __('messages.permission_' . str_replace(['-', '.'], '_', $perm->name)) }}</span>
                            </label>
                        @endforeach
                    </div>
                </div>
                <div class="form-actions">
                    <button type="button" class="btn-cancel" onclick="closeRoleModal()">{{ __('messages.cancel') }}</button>
                    <button type="submit" class="btn-submit">{{ __('messages.save') }}</button>
                </div>
            </form>
        </div>
    </div>
</div>
@push('scripts')
    <script src="{{ asset('client/js/roles.js') }}"></script>
@endpush
@endsection 