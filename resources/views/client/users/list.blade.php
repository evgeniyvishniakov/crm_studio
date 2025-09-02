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
                            @else
                            <button class="btn-avatar" title="{{ __('messages.change_avatar') }}" onclick="openAdminAvatarModal({{ $user->id }})">
                                <svg class="icon" viewBox="0 0 20 20" fill="currentColor">
                                    <path fill-rule="evenodd" d="M4 3a2 2 0 00-2 2v10a2 2 0 002 2h12a2 2 0 002-2V5a2 2 0 00-2-2H4zm12 12H4l4-8 3 6 2-4 3 6z" clip-rule="evenodd" />
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
                    @else
                    <div class="user-actions">
                        <button class="btn-avatar" title="{{ __('messages.change_avatar') }}" onclick="openAdminAvatarModal({{ $user->id }})">
                            <svg class="icon" viewBox="0 0 20 20" fill="currentColor">
                                <path fill-rule="evenodd" d="M4 3a2 2 0 00-2 2v10a2 2 0 002 2h12a2 2 0 002-2V5a2 2 0 00-2-2H4zm12 12H4l4-8 3 6 2-4 3 6z" clip-rule="evenodd" />
                            </svg>
                            {{ __('messages.change_avatar') }}
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
                        <input type="password" id="userPassword" name="password" required autocomplete="off" style="flex:1;">
                        <button type="button" class="btn-cancel" onclick="generateUserPassword()">{{ __('messages.generate') }}</button>
                        <button type="button" class="btn-cancel" onclick="togglePasswordVisibility('userPassword')" title="{{ __('messages.show_hide_password') }}">
                            <svg class="icon" viewBox="0 0 20 20" fill="currentColor" style="width: 16px; height: 16px;">
                                <path d="M10 12a2 20 100-4 2 2 0 000 4z"/>
                                <path fill-rule="evenodd" d="M.458 10C1.732 5.943 5.522 3 10 3s8.268 2.943 9.542 7c-1.274 4.057-5.064 7-9.542 7S1.732 14.057.458 10zM14 10a4 4 0 11-8 0 4 4 0 018 0z" clip-rule="evenodd"/>
                            </svg>
                        </button>
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
                <div class="form-group">
                    <label for="editUserPassword">{{ __('messages.new_password') }}</label>
                    <div style="display:flex;gap:8px;align-items:center;">
                        <input type="password" id="editUserPassword" name="password" autocomplete="new-password" style="flex:1;" placeholder="{{ __('messages.leave_empty_to_keep_current') }}">
                        <button type="button" class="btn-cancel" onclick="generateEditUserPassword()">{{ __('messages.generate') }}</button>
                        <button type="button" class="btn-cancel" onclick="togglePasswordVisibility('editUserPassword')" title="{{ __('messages.show_hide_password') }}">
                            <svg class="icon" viewBox="0 0 20 20" fill="currentColor" style="width: 16px; height: 16px;">
                                <path d="M10 12a2 2 0 100-4 2 2 0 000 4z"/>
                                <path fill-rule="evenodd" d="M.458 10C1.732 5.943 5.522 3 10 3s8.268 2.943 9.542 7c-1.274 4.057-5.064 7-9.542 7S1.732 14.057.458 10zM14 10a4 4 0 11-8 0 4 4 0 018 0z" clip-rule="evenodd"/>
                            </svg>
                        </button>
                    </div>
                    <small class="form-text text-muted">{{ __('messages.password_change_hint') }}</small>
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

<!-- Модальное окно для загрузки аватарки администратора -->
<div id="adminAvatarModal" class="modal">
    <div class="modal-content">
        <div class="modal-header">
            <h2>{{ __('messages.change_avatar') }}</h2>
            <span class="close" onclick="closeAdminAvatarModal()">&times;</span>
        </div>
        <div class="modal-body">
            <div id="adminAvatarErrors" class="modal-errors" style="display:none;color:#d32f2f;margin-bottom:10px;"></div>
            <form id="adminAvatarForm">
                @csrf
                <input type="hidden" id="adminUserId" name="user_id">
                
                <!-- Область предварительного просмотра -->
                <div class="avatar-preview-container" style="text-align: center; margin-bottom: 20px;">
                    <div id="currentAvatarPreview" style="display: none;">
                        <h4 style="margin-bottom: 10px; color: #666;">{{ __('messages.current_avatar') }}</h4>
                        <img id="currentAvatarImg" src="" alt="Current Avatar" style="width: 120px; height: 120px; border-radius: 50%; object-fit: cover; border: 3px solid #ddd; margin-bottom: 10px;">
                    </div>
                    <div id="newAvatarPreview" style="display: none;">
                        <h4 style="margin-bottom: 10px; color: #666;">{{ __('messages.new_avatar') }}</h4>
                        <img id="newAvatarImg" src="" alt="New Avatar" style="width: 120px; height: 120px; border-radius: 50%; object-fit: cover; border: 3px solid #10b981; margin-bottom: 10px;">
                    </div>
                    <div id="noAvatarPreview" style="display: block;">
                        <div style="width: 120px; height: 120px; border-radius: 50%; background: #f3f4f6; border: 3px solid #ddd; margin: 0 auto 10px; display: flex; align-items: center; justify-content: center;">
                            <svg width="40" height="40" viewBox="0 0 24 24" fill="currentColor" style="color: #9ca3af;">
                                <path d="M12 12c2.21 0 4-1.79 4-4s-1.79-4-4-4-4 1.79-4 4 1.79 4 4 4zm0 2c-2.67 0-8 1.34-8 4v2h16v-2c0-2.66-5.33-4-8-4z"/>
                            </svg>
                        </div>
                        <p style="color: #666; margin: 0;">{{ __('messages.no_avatar_selected') }}</p>
                    </div>
                </div>
                
                <div class="form-group">
                    <label for="adminAvatar">{{ __('messages.avatar') }}</label>
                    <input type="file" id="adminAvatar" name="avatar" accept="image/*" required style="padding: 8px; border: 1px solid #ddd; border-radius: 4px; width: 100%;">
                    <small class="form-text text-muted">{{ __('messages.avatar_hint') }}</small>
                </div>
                <div class="form-actions">
                    <button type="button" class="btn-cancel" onclick="closeAdminAvatarModal()">{{ __('messages.cancel') }}</button>
                    <button type="submit" class="btn-submit" id="uploadAvatarBtn">{{ __('messages.upload') }}</button>
                </div>
            </form>
        </div>
    </div>
</div>

@push('scripts')
<script>
    window.roles = @json($roles);
    window.translations = {
        current_avatar: '{{ __("messages.current_avatar") }}',
        new_avatar: '{{ __("messages.new_avatar") }}',
        no_avatar_selected: '{{ __("messages.no_avatar_selected") }}',
        upload: '{{ __("messages.upload") }}',
        uploading: '{{ __("messages.uploading") }}'
    };
</script>
    <script src="{{ asset('client/js/users.js') }}"></script>
@endpush


@endsection 