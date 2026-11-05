<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>@yield('title', 'PHM HCC')</title>

    <link rel="stylesheet" href="{{ asset('/css/style.css') }}">

    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600;700&display=swap"
        rel="stylesheet">

    <link rel="stylesheet"
        href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">

    @stack('styles')
</head>

<body class="{{ request()->routeIs('announcements') ? 'announcements-page' : '' }} {{ request()->routeIs('gallery') ? 'gallery-page' : '' }}">

    @unless(request()->routeIs('login', 'password.*'))
    <header>
        <nav class="navbar">
            <div class="logo">
                <img src="{{ asset('Images/images.jpg') }}"
                    alt="PHM HCC logo">
                <h2>PHM HCC</h2>
            </div>

            <ul class="nav-links" id="primaryNavigation">
                <li>
                    <a href="{{ route('home') }}"
                        class="{{ request()->routeIs('home') ? 'active' : '' }}">
                        {{ __('Home') }}
                    </a>
                </li>

                <li>
                    <a href="{{ route('about') }}"
                        class="{{ request()->routeIs('about') ? 'active' : '' }}">
                        {{ __('About') }}
                    </a>
                </li>

                <li>
                    <a href="{{ route('leadership') }}"
                        class="{{ request()->routeIs('leadership') ? 'active' : '' }}">
                        {{ __('Leadership') }}
                    </a>
                </li>

                <li>
                    <a href="{{ route('ministries') }}"
                        class="{{ request()->routeIs('ministries') ? 'active' : '' }}">
                        {{ __('Ministries') }}
                    </a>
                </li>

                <li>
                    <a href="{{ route('gallery') }}"
                        class="{{ request()->routeIs('gallery') ? 'active' : '' }}">
                        {{ __('Gallery') }}
                    </a>
                </li>


                <li>
                    <a href="{{ route('announcements') }}"
                        class="{{ request()->routeIs('announcements') ? 'active' : '' }}">
                        {{ __('Announcements') }}
                    </a>
                </li>

                <li>
                    <a href="{{ route('contact') }}"
                        class="{{ request()->routeIs('contact') ? 'active' : '' }}">
                        {{ __('Contact') }}
                    </a>
                </li>

                <li>
                    @auth
                    <a href="{{ route('dashboard') }}"
                        class="{{ request()->routeIs('dashboard*') ? 'active' : '' }}">
                        {{ __('Dashboard') }}
                    </a>
                    @else
                    <a href="{{ route('login') }}"
                        class="{{ request()->routeIs('login') ? 'active' : '' }}">
                        {{ __('Sign in') }}
                    </a>
                    @endauth
                </li>
                <li class="nav-language-switch">
                    @include('layouts.partials.language-switch', ['id' => 'public-language-switch'])
                </li>
            </ul>

            <button class="menu" type="button"
                aria-label="{{ __('Open navigation menu') }}"
                aria-controls="primaryNavigation"
                aria-expanded="false">

                <i class="fas fa-bars" aria-hidden="true"></i>
            </button>

        </nav>
    </header>
    @else
    <div class="auth-language-switch">
        @include('layouts.partials.language-switch', ['id' => 'auth-language-switch'])
    </div>
    @endunless

    <main>
        @if(request()->routeIs('dashboard*'))
            @include('layouts.partials.breadcrumbs')
        @endif
        @yield('content')
    </main>

    @unless(request()->routeIs('login', 'password.*'))
    @include('layouts.partials.site-footer')
    @endunless

    <script src="{{ asset('/js/script.js') }}"></script>

</body>

</html>