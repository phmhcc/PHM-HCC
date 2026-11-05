@extends('layouts.app')

@section('title', __('Choose a New Password | PHM HCC'))

@push('styles')
    @include('auth.partials.form-styles')
@endpush

@section('content')
    <section class="auth-section">
        <div class="auth-card">
            <img class="auth-logo" src="{{ asset('Images/images.jpg') }}" alt="PHM HCC logo">
            <h1>{{ __('Choose a new password') }}</h1>
            <p>{{ __('Choose a new password for your church account.') }}</p>

            <form class="auth-form" method="POST" action="{{ route('password.store') }}">
                @csrf
                <input type="hidden" name="token" value="{{ $token }}">

                <label for="email">{{ __('Email address') }}</label>
                <input id="email" name="email" type="email" value="{{ old('email', $email) }}" autocomplete="username" required autofocus>
                @error('email')
                    <p class="auth-error" role="alert">{{ $message }}</p>
                @enderror

                <label for="password">{{ __('New password') }}</label>
                <div class="auth-password-wrapper">
                    <input id="password" name="password" type="password" autocomplete="new-password" required>
                    <button
                        type="button"
                        class="auth-password-toggle"
                        id="toggleNewPasswordButton"
                        aria-label="{{ __('Show password') }}"
                        data-show-label="{{ __('Show password') }}"
                        data-hide-label="{{ __('Hide password') }}"
                        aria-controls="password"
                        aria-pressed="false">
                        <i class="fa fa-eye" id="newPasswordEyeIcon" aria-hidden="true"></i>
                    </button>
                </div>
                @error('password')
                    <p class="auth-error" role="alert">{{ $message }}</p>
                @enderror

                <label for="password_confirmation">{{ __('Confirm new password') }}</label>
                <input id="password_confirmation" name="password_confirmation" type="password" autocomplete="new-password" required>

                <button type="submit">{{ __('Update password') }}</button>
            </form>

            <a href="{{ route('login') }}">{{ __('Back to sign in') }}</a>
        </div>
    </section>

    <script>
        (() => {
            const password = document.getElementById('password');
            const toggle = document.getElementById('toggleNewPasswordButton');
            const eyeIcon = document.getElementById('newPasswordEyeIcon');

            toggle.addEventListener('click', () => {
                const isHidden = password.type === 'password';
                password.type = isHidden ? 'text' : 'password';
                toggle.setAttribute('aria-label', isHidden ? toggle.dataset.hideLabel : toggle.dataset.showLabel);
                toggle.setAttribute('aria-pressed', String(isHidden));
                eyeIcon.classList.toggle('fa-eye', !isHidden);
                eyeIcon.classList.toggle('fa-eye-slash', isHidden);
            });
        })();
    </script>
@endsection
