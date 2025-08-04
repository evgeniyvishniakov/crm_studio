@extends('client.layouts.app')

@section('content')
    <div class="dashboard-container">
        <div class="users-container">
            <div class="users-header">
                <div class="header-top">
            <h1>{{ __('messages.users') }}</h1>
            <div class="header-actions">
                        <button class="btn-add-user" onclick="openUserModal()">
                    <svg class="icon" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd" d="M10 3a1 1 0 011 1v5h5a1 1 0 110 2h-5v5a1 1 0 11-2 0v-5H4a1 1 0 110-2h5V4a1 1 0 011-1z" clip-rule="evenodd" />
                    </svg>
                    {{ __('messages.add_user') }}
                </button>
                <div class="search-box">
                    <svg class="search-icon" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd" d="M8 4a4 4 0 100 8 4 4 0 000-8zM2 8a6 6 0 1110.89 3.476l4.817 4.817a1 1 0 01-1.414 1.414l-4.816-4.816A6 6 0 012 8z" clip-rule="evenodd" />
                    </svg>
                    <input type="text" placeholder="{{ __('messages.search') }}..." autocomplete="off" id="userSearchInput">
                </div>
            </div>
        </div>
                
                <!-- Мобильная версия заголовка -->
                <div class="mobile-header">
                    <h1 class="mobile-title">{{ __('messages.users') }}</h1>
                    <div class="mobile-header-actions">
                        <button class="btn-add-user" onclick="openUserModal()">
                            <svg class="icon" viewBox="0 0 20 20" fill="currentColor">
                                <path fill-rule="evenodd" d="M10 3a1 1 0 011 1v5h5a1 1 0 110 2h-5v5a1 1 0 11-2 0v-5H4a1 1 0 110-2h5V4a1 1 0 011-1z" clip-rule="evenodd" />
                            </svg>
                            {{ __('messages.add_user') }}
                        </button>
                        <div class="search-box">
                            <svg class="search-icon" viewBox="0 0 20 20" fill="currentColor">
                                <path fill-rule="evenodd" d="M8 4a4 4 0 100 8 4 4 0 000-8zM2 8a6 6 0 1110.89 3.476l4.817 4.817a1 1 0 01-1.414 1.414l-4.816-4.816A6 6 0 012 8z" clip-rule="evenodd" />
                            </svg>
                            <input type="text" placeholder="{{ __('messages.search') }}..." autocomplete="off" id="userSearchInputMobile">
                        </div>
                    </div>
                </div>
            </div>


        <!-- Десктопная таблица -->
        <div class="table-wrapper">
            <table class="table-striped clients-table">
                <thead>
                <tr></tr>
                    <th>{{ __('messages.name') }}</th>
                    <th>{{ __('messages.email_login') }}</th>
                    <th>{{ __('messages.role') }}</th>
                    <th>{{ __('messages.status') }}</th>
                    <th>{{ __('messages.registration_date') }}</th>
                    <th class="actions-column">{{ __('messages.actions') }}</th>
                </tr>
                </thead>
                <tbody id="usersTableBody">
                @foreach($users as $user)
                    <tr id="user-{{ $user->id }}">
                        <td>
                            <div class="client-info">
                                <div class="client-avatar">
                                    @if($user->avatar)
                                        <img src="{{ asset('storage/' . $user->avatar) }}" alt="{{ $user->name }}" class="user-avatar">
                                    @else
                                        <div class="user-avatar-placeholder">{{ substr($user->name, 0, 1) }}</div>
                                    @endif
                                </div>
                                <div class="client-details">
                                    <div class="client-name">{{ $user->name }}</div>
                                </div>
                            </div>
                        </td>
                        <td>{{ $user->email ?: $user->username }}</td>
                        <td>{{ $roles[$user->role] ?? $user->role }}</td>
                        <td><span class="status-badge {{ $user->status === 'active' ? 'status-completed' : 'status-cancelled' }}">{{ $user->status === 'active' ? __('messages.active') : __('messages.inactive') }}</span></td>
                        <td>{{ $user->registered_at ? \Carbon\Carbon::parse($user->registered_at)->format('d.m.Y H:i') : '' }}</td>
                        <td class="actions-cell" style="vertical-align: middle;">
                            @if($user->role !== 'admin')
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
                            @endif
                        </td>
                    </tr>
                @endforeach
                </tbody>
            </table>
        </div>

        <!-- Мобильные карточки пользователей -->
        <div class="users-cards" id="usersCards" style="display: none;">
            @foreach($users as $user)
                <div class="user-card" id="user-card-{{ $user->id }}">
                    <div class="user-card-header">
                        <div class="user-main-info">
                            <div class="user-avatar">
                                @if($user->avatar)
                                    <img src="{{ asset('storage/' . $user->avatar) }}" alt="{{ $user->name }}" class="user-avatar-img">
                                @else
                                    <div class="user-avatar-placeholder">{{ substr($user->name, 0, 1) }}</div>
                                @endif
                            </div>
                            <div class="user-name">{{ $user->name }}</div>
                        </div>
                        <div class="user-status">
                            <span class="status-badge {{ $user->status === 'active' ? 'status-completed' : 'status-cancelled' }}">{{ $user->status === 'active' ? __('messages.active') : __('messages.inactive') }}</span>
                        </div>
                    </div>
                    <div class="user-info">
                        <div class="user-info-item">
                            <div class="user-info-label">
                                <svg class="icon" viewBox="0 0 20 20" fill="currentColor">
                                    <path d="M2.003 5.884L10 9.882l7.997-3.998A2 2 0 0016 4H4a2 2 0 00-1.997 1.884z" />
                                    <path d="M18 8.118l-8 4-8-4V14a2 2 0 002 2h12a2 2 0 002-2V8.118z" />
                                </svg>
                                {{ __('messages.email_login') }}:
                            </div>
                            <div class="user-info-value">{{ $user->email ?: $user->username }}</div>
                        </div>
                        <div class="user-info-item">
                            <div class="user-info-label">
                                <svg class="icon" viewBox="0 0 20 20" fill="currentColor">
                                    <path fill-rule="evenodd" d="M10 9a3 3 0 100-6 3 3 0 000 6zm-7 9a7 7 0 1114 0H3z" clip-rule="evenodd" />
                                </svg>
                                {{ __('messages.role') }}:
                            </div>
                            <div class="user-info-value">{{ $roles[$user->role] ?? $user->role }}</div>
                        </div>
                        <div class="user-info-item">
                            <div class="user-info-label">
                                <svg class="icon" viewBox="0 0 20 20" fill="currentColor">
                                    <path fill-rule="evenodd" d="M6 2a1 1 0 00-1 1v1H4a2 2 0 00-2 2v10a2 2 0 002 2h12a2 2 0 002-2V6a2 2 0 00-2-2h-1V3a1 1 0 00-1-1H6zm5 5a1 1 0 10-2 0v3.586l-1.293-1.293a1 1 0 10-1.414 1.414l3 3a1 1 0 001.414 0l3-3a1 1 0 00-1.414-1.414L11 10.586V7z" clip-rule="evenodd" />
                                </svg>
                                {{ __('messages.registration_date') }}:
                            </div>
                            <div class="user-info-value">{{ $user->registered_at ? \Carbon\Carbon::parse($user->registered_at)->format('d.m.Y H:i') : '' }}</div>
                        </div>
                    </div>
                    @if($user->role !== 'admin')
                    <div class="user-actions">
                        <button class="btn-edit" title="{{ __('messages.edit') }}" onclick="openEditUserModalFromCard({{ $user->id }})">
                            <svg class="icon" viewBox="0 0 20 20" fill="currentColor">
                                <path d="M13.586 3.586a2 2 0 112.828 2.828l-.793.793-2.828-2.828.793-.793zM11.379 5.793L3 14.172V17h2.828l8.38-8.379-2.83-2.828z" />
                            </svg>
                            {{ __('messages.edit') }}
                        </button>
                        <button class="btn-delete" title="{{ __('messages.delete') }}" onclick="showDeleteConfirmation({{ $user->id }})">
                            <svg class="icon" viewBox="0 0 20 20" fill="currentColor">
                                <path fill-rule="evenodd" d="M9 2a1 1 0 00-.894.553L7.382 4H4a1 1 0 000 2v10a2 2 0 002 2h8a2 2 0 002-2V6a1 1 0 100-2h-3.382l-.724-1.447A1 1 0 0011 2H9zM7 8a1 1 0 012 0v6a1 1 0 11-2 0V8zm5-1a1 1 0 00-1 1v6a1 1 0 102 0V8a1 1 0 00-1-1z" clip-rule="evenodd" />
                            </svg>
                            {{ __('messages.delete') }}
                        </button>
                    </div>
                    @endif
                </div>
            @endforeach
        </div>

        <!-- Мобильная пагинация -->
        <div id="mobileUsersPagination" class="mobile-pagination" style="display: none;">
            <!-- Пагинация будет добавлена через JavaScript -->
        </div>
    </div>

<!-- Модальное окно для добавления пользователя -->
<div id="addUserModal" class="modal">
    <div class="modal-content">
        <div class="modal-header">
            <h2>{{ __('messages.add_user') }}</h2>
            <span class="close" onclick="closeUserModal()">&times;</span>
        </div>
        <div class="modal-body">
            <div id="addUserErrors" class="modal-errors" style="display:none;color:#d32f2f;margin-bottom:10px;"></div>
            <form id="addUserForm">
                @csrf
                <div class="form-group">
                    <label for="userName">{{ __('messages.name') }} *</label>
                    <input type="text" id="userName" name="name" required autocomplete="off">
                </div>
                <div class="form-group">
                    <label for="userUsername">{{ __('messages.login') }} *</label>
                    <input type="text" id="userUsername" name="username" required autocomplete="off">
                </div>
                <div class="form-group">
                    <label for="userEmail">{{ __('messages.email') }}</label>
                    <input type="email" id="userEmail" name="email" autocomplete="off">
                </div>
                <div class="form-group">
                    <label for="userAvatar">{{ __('messages.avatar') }}</label>
                    <input type="file" id="userAvatar" name="avatar" accept="image/*" style="padding: 8px; border: 1px solid #ddd; border-radius: 4px; width: 100%;">
                    <small class="form-text text-muted">{{ __('messages.avatar_hint') }}</small>
                </div>
                <div class="form-group">
                    <label for="userPassword">{{ __('messages.password') }} *</label>
                    <div style="display:flex;gap:8px;align-items:center;">
                        <input type="text" id="userPassword" name="password" required autocomplete="off" style="flex:1;">
                        <button type="button" class="btn-cancel" onclick="generateUserPassword()">{{ __('messages.generate') }}</button>
                    </div>
                </div>
                <div class="form-group">
                    <label for="userRole">{{ __('messages.role') }}</label>
                    <select id="userRole" name="role" required>
                        @foreach($roles as $key => $label)
                            @if($key !== 'admin')
                                <option value="{{ $key }}">{{ $label }}</option>
                            @endif
                        @endforeach
                    </select>
                </div>
                <div class="form-actions">
                    <button type="button" class="btn-cancel" onclick="closeUserModal()">{{ __('messages.cancel') }}</button>
                    <button type="submit" class="btn-submit">{{ __('messages.add') }}</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Модальное окно для редактирования пользователя -->
<div id="editUserModal" class="modal">
    <div class="modal-content">
        <div class="modal-header">
            <h2>{{ __('messages.edit_user') }}</h2>
            <span class="close" onclick="closeEditUserModal()">&times;</span>
        </div>
        <div class="modal-body">
            <form id="editUserForm">
                @csrf
                @method('PUT')
                <input type="hidden" id="editUserId" name="user_id">
                <div class="form-group">
                    <label for="editUserName">{{ __('messages.name') }} *</label>
                    <input type="text" id="editUserName" name="name" required autocomplete="off">
                </div>
                <div class="form-group">
                    <label for="editUserUsername">{{ __('messages.login') }} *</label>
                    <input type="text" id="editUserUsername" name="username" required autocomplete="off">
                </div>
                <div class="form-group">
                    <label for="editUserEmail">{{ __('messages.email') }}</label>
                    <input type="email" id="editUserEmail" name="email" autocomplete="off">
                </div>
                <div class="form-group">
                    <label for="editUserAvatar">{{ __('messages.avatar') }}</label>
                    <input type="file" id="editUserAvatar" name="avatar" accept="image/*" style="padding: 8px; border: 1px solid #ddd; border-radius: 4px; width: 100%;">
                    <small class="form-text text-muted">{{ __('messages.avatar_hint') }}</small>
                    <div id="currentAvatar" style="margin-top: 10px; display: none;">
                        <img id="currentAvatarImg" src="" alt="Текущая аватарка" style="width: 50px; height: 50px; border-radius: 50%; object-fit: cover;">
                        <small>{{ __('messages.current_avatar') }}</small>
                    </div>
                </div>
                <div class="form-group">
                    <label for="editUserRole">{{ __('messages.role') }}</label>
                    <select id="editUserRole" name="role" required>
                        @foreach($roles as $key => $label)
                            @if($key !== 'admin')
                                <option value="{{ $key }}">{{ $label }}</option>
                            @endif
                        @endforeach
                    </select>
                </div>
                <div class="form-group">
                    <label for="editUserStatus">{{ __('messages.status') }}</label>
                    <select id="editUserStatus" name="status" required>
                        <option value="active">{{ __('messages.active') }}</option>
                        <option value="inactive">{{ __('messages.inactive') }}</option>
                    </select>
                </div>
                <div class="form-actions">
                    <button type="button" class="btn-cancel" onclick="closeEditUserModal()">{{ __('messages.cancel') }}</button>
                    <button type="submit" class="btn-submit">{{ __('messages.save') }}</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Модальное окно подтверждения удаления пользователя -->
<div id="userConfirmationModal" class="modal">
    <div class="modal-content">
        <div class="modal-header">
            <h2>{{ __('messages.confirm_delete') }}</h2>
            <span class="close" onclick="closeUserConfirmationModal()">&times;</span>
        </div>
        <div class="modal-body">
            <p>{{ __('messages.are_you_sure_you_want_to_delete_this_user') }}</p>
        </div>
        <div class="modal-footer">
            <button class="btn-cancel" id="cancelUserDelete">{{ __('messages.cancel') }}</button>
            <button class="btn-delete" id="confirmUserDelete">{{ __('messages.delete') }}</button>
        </div>
    </div>
</div>

@push('scripts')
<script>
    window.roles = @json($roles);
    </script>
    <script src="{{ asset('client/js/users.js') }}"></script>
@endpush


@endsection 