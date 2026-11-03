@extends('layouts.app')

@section('title', __('Forgot Password | PHM HCC'))

@push('styles')
    @include('auth.partials.form-styles')
@endpush

@section('content')
    <section class="auth-section">
        <div class="auth-card">
            <img class="auth-logo" src="{{ asset('Images/images.jpg') }}" alt="PHM HCC logo">
            <h1>{{ __('Reset your password') }}</h1>
            <p>{{ __('Enter the email address associated with your church account. If it matches an account, we will send a reset link.') }}</p>

            @if (session('status'))
                <p class="auth-message" role="status">{{ session('status') }}</p>
            @endif

            <form class="auth-form" method="POST" action="{{ route('password.email') }}">
                @csrf
                <label for="email">{{ __('Email address') }}</label>
                <input id="email" name="email" type="email" value="{{ old('email') }}" autocomplete="username" required autofocus>
                @error('email')
                    <p class="auth-error" role="alert">{{ $message }}</p>
                @enderror
                <button type="submit">{{ __('Send reset link') }}</button>
            </form>

            <a href="{{ route('login') }}">{{ __('Back to sign in') }}</a>
        </div>
    </section>
@endsection
