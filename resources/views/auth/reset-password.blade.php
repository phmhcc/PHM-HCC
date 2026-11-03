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
                <input id="password" name="password" type="password" autocomplete="new-password" required>
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
@endsection
