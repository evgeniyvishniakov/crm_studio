@extends('client.layouts.app')

@section('title', __('messages.settings'))
@section('content')

<div class="dashboard-container settings-container">
    <div class="settings-header">
        <h1>{{ __('messages.settings') }}</h1>
    
    </div>
    <div class="dashboard-tabs" style="margin-bottom:28px;">
        <button class="tab-button active" data-tab="profile"><i class="fa fa-user" style="margin-right:8px;"></i>{{ __('messages.profile') }}</button>
        <button class="tab-button" data-tab="security"><i class="fa fa-shield-alt" style="margin-right:8px;"></i>{{ __('messages.security') }}</button>
        <button class="tab-button" data-tab="notifications"><i class="fa fa-bell" style="margin-right:8px;"></i>{{ __('messages.notifications') }}</button>
        <button class="tab-button" data-tab="language"><i class="fa fa-globe" style="margin-right:8px;"></i>{{ __('messages.language_and_currency') }}</button>
    </div>
    <div class="settings-content">
        <!-- Профиль -->
        <div class="settings-pane" id="tab-profile">
            <form method="POST" action="{{ route('client.settings.update') }}" enctype="multipart/form-data" id="profileForm">
                @csrf
                <h5>{{ __('messages.profile') }}</h5>
                <div class="form-row form-row--2col">
                    <div class="form-col">
                        <div class="form-group mb-3">
                            <label>{{ __('messages.name') }}</label>
                            <input type="text" name="name" class="form-control" value="{{ old('name', $project->name ?? '') }}">
                        </div>
                    </div>
                    <div class="form-col">
                        <div class="form-group mb-3">
                            <label>{{ __('messages.project_name') }}</label>
                            <input type="text" name="project_name" class="form-control" value="{{ old('project_name', $project->project_name ?? '') }}">
                        </div>
                    </div>
                </div>
                <div class="form-row form-row--2col">
                    <div class="form-col">
                        <div class="form-group mb-3">
                            <label>{{ __('messages.email') }}</label>
                            <input type="email" name="email" class="form-control" value="{{ old('email', $project->email ?? '') }}" readonly>
                        </div>
                    </div>
                    <div class="form-col">
                        <div class="form-group mb-3">
                            <label>{{ __('messages.phone') }}</label>
                            <input type="text" name="phone" class="form-control" value="{{ old('phone', $project->phone ?? '') }}">
                        </div>
                    </div>
                </div>
                <div class="form-row form-row--2col">
                    <div class="form-col">
                        <div class="form-group mb-3">
                            <label>{{ __('messages.address') }}</label>
                            <input type="text" name="address" class="form-control" value="{{ old('address', $project->address ?? '') }}">
                        </div>
                    </div>
                    <div class="form-col">
                        <div class="form-group mb-3">
                            <label>{{ __('messages.website') }}</label>
                            <input type="url" name="website" class="form-control" value="{{ old('website', $project->website ?? '') }}">
                        </div>
                    </div>
                </div>
                <div class="form-row form-row--2col">
                    <div class="form-col">
                        <div class="form-group mb-3">
                            <label>{{ __('messages.instagram') }}</label>
                            <input type="url" name="instagram" class="form-control" value="{{ old('instagram', $project->instagram ?? '') }}" placeholder="https://instagram.com/yourpage">
                        </div>
                    </div>
                    <div class="form-col">
                        <div class="form-group mb-3">
                            <label>{{ __('messages.facebook') }}</label>
                            <input type="url" name="facebook" class="form-control" value="{{ old('facebook', $project->facebook ?? '') }}" placeholder="https://facebook.com/yourpage">
                        </div>
                    </div>
                </div>
                <div class="form-row form-row--2col">
                    <div class="form-col">
                        <div class="form-group mb-3">
                            <label>{{ __('messages.tiktok') }}</label>
                            <input type="url" name="tiktok" class="form-control" value="{{ old('tiktok', $project->tiktok ?? '') }}" placeholder="https://tiktok.com/@yourpage">
                        </div>
                    </div>
                    <div class="form-col"></div>
                </div>

                <!-- Описание и логотип в один ряд -->
                <div class="form-row form-row--2col">
                    <div class="form-col">
                        <div class="form-group mb-4">
                            <label>{{ __('messages.about_us') }}</label>
                            <textarea name="about" class="form-control" rows="4" placeholder="{{ __('messages.about_us_placeholder') }}">{{ old('about', $project->about ?? '') }}</textarea>
                            <small class="form-text text-muted">{{ __('messages.about_us_hint') }}</small>
                        </div>
                    </div>
                    <div class="form-col">
                        <div class="form-group mb-4">
                            <label>{{ __('messages.company_logo') }}</label>
                            <div class="logo-upload-row">
                                <div class="logo-preview">
                                    @if(!empty($project->logo))
                                        <img src="{{ $project->logo }}" alt="logo">
                                    @else
                                        <div class="logo-placeholder">?</div>
                                    @endif
                                </div>
                                <div class="logo-upload-controls">
                                    <label for="logo-input" class="btn btn-outline-secondary" style="cursor:pointer;display:inline-block;">{{ __('messages.select_file') }}</label>
                                    <input type="file" id="logo-input" name="logo" accept="image/*" style="display:none;" onchange="document.getElementById('logo-filename').textContent = this.files[0]?.name || ''">
                                    <span id="logo-filename" style="margin-left:12px;font-size:0.95em;color:#888;"></span>
                                    <small class="form-text text-muted">{{ __('messages.logo_upload_requirements') }}</small>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Поля для карты и описания -->
                <div class="form-row">
                    <div class="form-group mb-4">
                        <h6 style="margin-bottom: 15px; color: #333; font-weight: 600;">
                            <i class="fas fa-map-marker-alt" style="margin-right: 8px; color: #dc3545;"></i>
                            {{ __('messages.map_settings') }}
                        </h6>
                    </div>
                </div>

                <div class="form-row form-row--2col">
                    <div class="form-col">
                        <div class="form-group mb-3">
                            <label>{{ __('messages.map_url') }}</label>
                            <input type="url" name="map_url" class="form-control" id="map_url" 
                                   value="{{ old('map_url', '') }}" 
                                   placeholder="{{ __('messages.map_url_placeholder') }}">
                            <small class="form-text text-muted">
                                {{ __('messages.map_url_help') }}
                            </small>
                        </div>
                    </div>
                    <div class="form-col">
                        <div class="form-group mb-3">
                            <label>{{ __('messages.map_zoom') }}</label>
                            <input type="number" name="map_zoom" class="form-control" id="map_zoom" 
                                   value="{{ old('map_zoom', $project->map_zoom ?? 15) }}" min="1" max="20">
                            <small class="form-text text-muted">{{ __('messages.map_zoom_placeholder') }}</small>
                        </div>
                    </div>
                </div>

                <!-- Скрытые поля для координат -->
                <input type="hidden" name="map_latitude" id="map_latitude" value="{{ old('map_latitude', $project->map_latitude ?? '') }}">
                <input type="hidden" name="map_longitude" id="map_longitude" value="{{ old('map_longitude', $project->map_longitude ?? '') }}">

                <!-- Предварительный просмотр карты -->
                <div class="form-row">
                    <div class="form-group mb-4">
                        <label>{{ __('messages.map_preview') }}</label>
                        <div id="map_preview" style="width: 100%; height: 300px; border: 1px solid #ddd; border-radius: 8px; background: #f8f9fa; display: flex; align-items: center; justify-content: center;">
                            <div class="text-center text-muted">
                                <i class="fas fa-map fa-3x mb-3"></i>
                                <p>{{ __('messages.map_preview_placeholder') }}</p>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="form-row">
                    <div class="form-group mb-4">
                        <label>{{ __('messages.registration_date') }}</label>
                        <input type="text" class="form-control" value="{{ $project->registered_at ? \Carbon\Carbon::parse($project->registered_at)->format('d.m.Y H:i') : ($project->created_at ? \Carbon\Carbon::parse($project->created_at)->format('d.m.Y H:i') : '') }}" disabled style="max-width:220px; display:inline-block;">
                    </div>
                </div>
                <button type="submit" class="btn btn-primary">{{ __('messages.save') }}</button>
            </form>
            

        </div>
        <!-- Безопасность -->
        <div class="settings-pane" id="tab-security" style="display:none;">
            <h5>{{ __('messages.change_password') }}</h5>
            <a href="{{ route('password.request') }}" class="btn btn-primary mb-4">{{ __('messages.forgot_password') }}</a>
            <hr>
            <!-- Смена почты -->
            <form method="POST" action="{{ route('client.security.email') }}" class="mb-4" id="change-email-form">
                @csrf
                <h5>{{ __('messages.change_email') }}</h5>
                <div class="form-row form-row--2col">
                    <div class="form-col">
                        <div class="form-group mb-3">
                            <label>{{ __('messages.new_email') }}</label>
                            <input type="email" name="new_email" class="form-control" required>
                        </div>
                    </div>
                    <div class="form-col">
                        <div class="form-group mb-3">
                            <label>{{ __('messages.current_password') }}</label>
                            <input type="password" name="current_password" class="form-control" required>
                        </div>
                    </div>
                </div>
                <button type="submit" class="btn btn-primary">{{ __('messages.change_email') }}</button>
                <div id="change-email-notification" style="margin-top:16px;"></div>
            </form>

            <hr>
            <!-- Двухфакторная аутентификация -->
            <div class="mb-4">
                <h5>{{ __('messages.two_factor_auth') }}</h5>
                <p>{{ __('messages.two_factor_auth_desc') }}</p>
                <form method="POST" action="{{ route('client.security.2fa.enable') }}" style="display:inline-block;">
                    @csrf
                    <button type="submit" class="btn btn-outline-primary">{{ __('messages.enable_2fa') }}</button>
                </form>
                <form method="POST" action="{{ route('client.security.2fa.disable') }}" style="display:inline-block;margin-left:10px;">
                    @csrf
                    <button type="submit" class="btn btn-outline-danger">{{ __('messages.disable_2fa') }}</button>
                </form>
            </div>
        </div>
        <!-- Уведомления -->
        <div class="settings-pane" id="tab-notifications" style="display:none;">
            <form>
                <h5>{{ __('messages.notifications') }}</h5>
                <div class="form-check mb-3">
                    <input class="form-check-input" type="checkbox" id="notif1" checked>
                    <label class="form-check-label" for="notif1">{{ __('messages.get_email_notifications') }}</label>
                </div>
                <div class="form-check mb-4">
                    <input class="form-check-input" type="checkbox" id="notif2">
                    <label class="form-check-label" for="notif2">{{ __('messages.get_appointment_reminders') }}</label>
                </div>
                <button type="submit" class="btn btn-primary">{{ __('messages.save') }}</button>
            </form>
        </div>
        <!-- Язык и Валюта -->
        <div class="settings-pane" id="tab-language" style="display:none;">
            <form id="language-currency-form">
                <h5>{{ __('messages.language_and_currency') }}</h5>
                <div class="form-row form-row--3col">
                    <div class="form-col">
                        <div class="form-group mb-4">
                            <label>{{ __('messages.interface_language') }}</label>
                            <select class="form-control" name="language_id" data-language-selector>
                                @php
                                    use App\Models\Language;
                                    $languages = Language::where('is_active', true)->get();
                                    if ($languages->isEmpty()) {
                                        // Fallback если языки не загружены
                                        $languages = collect([
                                            (object)['id' => 1, 'name' => 'Русский', 'native_name' => 'Русский'],
                                            (object)['id' => 2, 'name' => 'English', 'native_name' => 'English'],
                                            (object)['id' => 3, 'name' => 'Українська', 'native_name' => 'Українська']
                                        ]);
                                    }
                                    
                                    // Определяем выбранный язык
                                    $selectedLanguageId = $project->language_id ?? 1;
                                    

                                @endphp
                                @foreach($languages as $language)
                                    @php
                                        $isSelected = $selectedLanguageId == $language->id;
                                    @endphp
                                    <option value="{{ $language->id }}" {{ $isSelected ? 'selected="selected"' : '' }}>
                                        {{ $language->name }} ({{ $language->native_name }})
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="form-col">
                        <div class="form-group mb-4">
                            <label>{{ __('messages.web_booking_language') }}</label>
                            <select class="form-control" name="booking_language_id">
                                @php
                                    // Определяем выбранный язык веб-записи
                                    $selectedBookingLanguageId = $project->booking_language_id ?? $project->language_id ?? 1;
                                @endphp
                                @foreach($languages as $language)
                                    @php
                                        $isSelected = $selectedBookingLanguageId == $language->id;
                                    @endphp
                                    <option value="{{ $language->id }}" {{ $isSelected ? 'selected="selected"' : '' }}>
                                        {{ $language->name }} ({{ $language->native_name }})
                                    </option>
                                @endforeach
                            </select>
                            <small class="form-text text-muted">{{ __('messages.web_booking_language_hint') }}</small>
                        </div>
                    </div>
                    <div class="form-col">
                        <div class="form-group mb-4">
                            <label>{{ __('messages.currency') }}</label>
                            <select class="form-control" name="currency_id" id="currency-selector">
                                @php
                                    $currencies = \App\Models\Currency::where('is_active', true)->get();
                                    if ($currencies->isEmpty()) {
                                        // Fallback если валюты не загружены
                                        $currencies = collect([
                                            (object)['id' => 1, 'code' => 'UAH', 'symbol' => '₴'],
                                            (object)['id' => 2, 'code' => 'USD', 'symbol' => '$'],
                                            (object)['id' => 3, 'code' => 'EUR', 'symbol' => '€']
                                        ]);
                                    }
                                    
                                    // Определяем выбранную валюту
                                    $selectedCurrencyId = $project->currency_id ?? 1;
                                @endphp
                                @foreach($currencies as $currency)
                                    @php
                                        $isSelected = $selectedCurrencyId == $currency->id;
                                    @endphp
                                    <option value="{{ $currency->id }}" {{ $isSelected ? 'selected' : '' }}>
                                        {{ $currency->code }} ({{ $currency->symbol }})
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                </div>
                

            </form>
        </div>
    </div>
</div>







@push('scripts')
<script src="{{ asset('client/js/settings.js') }}"></script>
@endpush 
@endsection 