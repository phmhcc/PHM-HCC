@extends('layouts.dashboard')

@section('title', __('My Profile | PHM HCC'))

@push('styles')
<style>
    .profile-password-wrapper {
        position: relative;
    }

    .profile-password-wrapper input {
        padding-right: 44px;
    }

    .profile-password-toggle {
        position: absolute;
        top: 50%;
        right: 12px;
        padding: 4px;
        transform: translateY(-50%);
        border: 0;
        background: transparent;
        color: #777;
        cursor: pointer;
    }

    .profile-password-toggle:focus-visible {
        outline: 2px solid #3373ad;
        outline-offset: 2px;
    }
</style>
@endpush

@section('content')
<div class="profile-panel">
    <header class="profile-banner">
        <div class="profile-banner-avatar"><i class="fas fa-user" aria-hidden="true"></i></div>
        <div>
            <h1>{{ auth()->user()->name }}</h1>
            <p>{{ __('Manage your account details and password.') }}</p>
        </div>
    </header>
    <section class="content-box">
    <h2 class="resource-panel-title"><i class="fas fa-id-card"></i> {{ __('Personal information') }}</h2>
    <form method="POST" action="{{ route('dashboard.profile.update') }}">
        @csrf
        @method('PUT')
        <div class="resource-form-grid">
            <label class="resource-field"><span>{{ __('Name') }}</span><input name="name" value="{{ old('name', auth()->user()->name) }}" required>@error('name')<small class="form-error">{{ $message }}</small>@enderror</label>
            <label class="resource-field"><span>{{ __('Email') }}</span><input type="email" name="email" value="{{ old('email', auth()->user()->email) }}" required>@error('email')<small class="form-error">{{ $message }}</small>@enderror</label>
            <label class="resource-field">
                <span>{{ __('Current password (required to change password)') }}</span>
                <div class="profile-password-wrapper">
                    <input id="current_password" type="password" name="current_password" autocomplete="current-password">
                    <button type="button" class="profile-password-toggle" aria-label="{{ __('Show password') }}" data-show-label="{{ __('Show password') }}" data-hide-label="{{ __('Hide password') }}" aria-controls="current_password" aria-pressed="false">
                        <i class="fa fa-eye" aria-hidden="true"></i>
                    </button>
                </div>
                @error('current_password')<small class="form-error">{{ $message }}</small>@enderror
            </label>
            <label class="resource-field">
                <span>{{ __('New password (optional)') }}</span>
                <div class="profile-password-wrapper">
                    <input id="password" type="password" name="password" autocomplete="new-password">
                    <button type="button" class="profile-password-toggle" aria-label="{{ __('Show password') }}" data-show-label="{{ __('Show password') }}" data-hide-label="{{ __('Hide password') }}" aria-controls="password" aria-pressed="false">
                        <i class="fa fa-eye" aria-hidden="true"></i>
                    </button>
                </div>
                @error('password')<small class="form-error">{{ $message }}</small>@enderror
            </label>
            <label class="resource-field">
                <span>{{ __('Confirm new password') }}</span>
                <div class="profile-password-wrapper">
                    <input id="password_confirmation" type="password" name="password_confirmation" autocomplete="new-password">
                    <button type="button" class="profile-password-toggle" aria-label="{{ __('Show password') }}" data-show-label="{{ __('Show password') }}" data-hide-label="{{ __('Hide password') }}" aria-controls="password_confirmation" aria-pressed="false">
                        <i class="fa fa-eye" aria-hidden="true"></i>
                    </button>
                </div>
            </label>
        </div>
        <button class="dashboard-action" type="submit">{{ __('Save profile') }}</button>
    </form>
    </section>
</div>
@endsection

@push('scripts')
<script>
    document.querySelectorAll('.profile-password-toggle').forEach((toggle) => {
        const password = document.getElementById(toggle.getAttribute('aria-controls'));
        const eyeIcon = toggle.querySelector('i');

        toggle.addEventListener('click', () => {
            const isHidden = password.type === 'password';
            password.type = isHidden ? 'text' : 'password';
            toggle.setAttribute('aria-label', isHidden ? toggle.dataset.hideLabel : toggle.dataset.showLabel);
            toggle.setAttribute('aria-pressed', String(isHidden));
            eyeIcon.classList.toggle('fa-eye', !isHidden);
            eyeIcon.classList.toggle('fa-eye-slash', isHidden);
        });
    });
</script>
@endpush
