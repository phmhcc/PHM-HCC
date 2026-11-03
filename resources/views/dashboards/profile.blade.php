@extends('layouts.dashboard')

@section('title', __('My Profile | PHM HCC'))
@section('page-heading', __('My Profile'))

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
            <label class="resource-field"><span>{{ __('Current password (required to change password)') }}</span><input type="password" name="current_password" autocomplete="current-password">@error('current_password')<small class="form-error">{{ $message }}</small>@enderror</label>
            <label class="resource-field"><span>{{ __('New password (optional)') }}</span><input type="password" name="password" autocomplete="new-password">@error('password')<small class="form-error">{{ $message }}</small>@enderror</label>
            <label class="resource-field"><span>{{ __('Confirm new password') }}</span><input type="password" name="password_confirmation" autocomplete="new-password"></label>
        </div>
        <button class="dashboard-action" type="submit">{{ __('Save profile') }}</button>
    </form>
    </section>
</div>
@endsection
