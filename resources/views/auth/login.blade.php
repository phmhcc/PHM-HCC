@extends('layouts.app')

@section('title', __('Sign In | PHM HCC'))

@push('styles')
<style>
    .login-section {
        display: grid;
        min-height: 100vh;
        place-items: center;
        padding: 50px 20px;
        background: #f4f7fb;
    }

    .login-card {
        width: min(100%, 520px);
        padding: 38px;
        border-radius: 14px;
        background: #fff;
        box-shadow: 0 8px 25px rgba(0, 0, 0, .1);
        text-align: center;
    }

    .login-card i {
        margin-bottom: 16px;
        color: #c9a900;
        font-size: 38px;
    }

    .login-logo {
        display: block;
        width: 72px;
        height: 72px;
        margin: 0 auto 16px;
        border-radius: 50%;
        object-fit: cover;
    }

    .login-card h1 {
        margin-bottom: 12px;
        color: #003366;
    }

    .login-card p {
        color: #555;
        line-height: 1.7;
    }

    .login-form {
        margin-top: 24px;
        text-align: left;
    }

    .login-form label {
        display: block;
        margin: 14px 0 6px;
        color: #003366;
        font-weight: 600;
    }

    .password-label-row {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 12px;
        margin: 14px 0 6px;
    }

    .password-label-row label {
        margin: 0;
    }

    .forgot-password-link {
        color: #536dfe;
        font-size: 14px;
        text-decoration: none;
        white-space: nowrap;
    }

    .forgot-password-link:hover {
        text-decoration: underline;
    }

    .login-form input {
        width: 100%;
        padding: 12px;
        border: 1px solid #ccd4df;
        border-radius: 6px;
        font: inherit;
    }

    .login-form select {
        width: 100%;
        padding: 12px;
        border: 1px solid #ccd4df;
        border-radius: 6px;
        background: #fff;
        font: inherit;
    }

    .login-form > button[type="submit"] {
        width: 100%;
        margin-top: 22px;
        padding: 13px;
        border: 0;
        border-radius: 6px;
        background: #003366;
        color: white;
        font: inherit;
        font-weight: 600;
        cursor: pointer;
    }

    .login-card p.login-error {
        margin-top: 6px;
        color: #b42318;
        font-size: 14px;
    }
    .password-wrapper {
    position: relative;
    width: 100%;
}

.password-wrapper input {
    width: 100%;
    padding-right: 45px;
}

.toggle-password {
    position: absolute;
    right: 12px;
    top: 50%;
    transform: translateY(-50%);
    width: 30px;
    height: 30px;
    padding: 0;
    background: transparent;
    border: none;
    cursor: pointer;
    color: #777;
    font-size: 14px;
}

.toggle-password i {
    margin: 0;
    color: inherit;
    font-size: 14px;
}

.toggle-password:focus-visible {
    outline: 2px solid #003366;
    outline-offset: 2px;
}

    @media (max-width: 600px) {
        .login-card {
            padding: 28px 22px;
        }
    }
</style>
@endpush

@section('content')
<section class="login-section">
    <div class="login-card">
        <img class="login-logo" src="{{ asset('Images/images.jpg') }}" alt="PHM HCC logo">
        <h1>{{ __('Church Team Sign In') }}</h1>
        <p>{{ __('Sign in with the account provided by your church administrator.') }}</p>
        @if (session('status'))
            <p role="status">{{ session('status') }}</p>
        @endif
        <form class="login-form" method="POST" action="{{ route('login.store') }}">
            @csrf
            <label for="email">{{ __('Email address') }}</label>
            <input id="email" name="email" type="email" value="{{ old('email') }}" autocomplete="username" required autofocus>
            @error('email')
            <p class="login-error" role="alert">{{ $message }}</p>
            @enderror

            <div class="password-label-row">
                <label for="password">{{ __('Password') }}</label>
                <a class="forgot-password-link" href="{{ route('password.request') }}">{{ __('Forgot Your Password?') }}</a>
            </div>

            <div class="password-wrapper">
                <input
                    id="password"
                    name="password"
                    type="password"
                    autocomplete="current-password"
                    required>

                <button type="button" class="toggle-password" id="togglePasswordButton" aria-label="{{ __('Show password') }}" data-show-label="{{ __('Show password') }}" data-hide-label="{{ __('Hide password') }}" aria-controls="password" aria-pressed="false">
                    <i class="fa fa-eye" id="eyeIcon" aria-hidden="true"></i>
                </button>
            </div>

            @error('password')
            <p class="login-error" role="alert">{{ $message }}</p>
            @enderror

            <label style="display: flex; align-items: center; gap: 8px; font-weight: 400;">
                <input type="checkbox" name="remember" value="1" style="width: auto;">
                {{ __('Remember me') }}
            </label>
            <button type="submit">{{ __('Sign in') }}</button>
        </form>
    </div>

    <script>
    (() => {
        const password = document.getElementById('password');
        const eyeIcon = document.getElementById('eyeIcon');
        const toggle = document.getElementById('togglePasswordButton');

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
</section>
@endsection