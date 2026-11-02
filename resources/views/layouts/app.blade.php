<!DOCTYPE html>
<html lang="en">

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

<body class="{{ request()->routeIs('announcements') ? 'announcements-page' : '' }}">

    @unless(request()->routeIs('login'))
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
                        Home
                    </a>
                </li>

                <li>
                    <a href="{{ route('about') }}"
                        class="{{ request()->routeIs('about') ? 'active' : '' }}">
                        About
                    </a>
                </li>

                <li>
                    <a href="{{ route('leadership') }}"
                        class="{{ request()->routeIs('leadership') ? 'active' : '' }}">
                        Leadership
                    </a>
                </li>

                <li>
                    <a href="{{ route('ministries') }}"
                        class="{{ request()->routeIs('ministries') ? 'active' : '' }}">
                        Ministries
                    </a>
                </li>

                <li>
                    <a href="{{ route('gallery') }}"
                        class="{{ request()->routeIs('gallery') ? 'active' : '' }}">
                        Gallery
                    </a>
                </li>

               
                <li>
                    <a href="{{ route('announcements') }}"
                        class="{{ request()->routeIs('announcements') ? 'active' : '' }}">
                        Announcements
                    </a>
                </li>

                <li>
                    <a href="{{ route('contact') }}"
                        class="{{ request()->routeIs('contact') ? 'active' : '' }}">
                        Contact
                    </a>
                </li>

                <li>
                    @auth
                    <a href="{{ route('dashboard') }}"
                        class="{{ request()->routeIs('dashboard*') ? 'active' : '' }}">
                        Dashboard
                    </a>
                    @else
                    <a href="{{ route('login') }}"
                        class="{{ request()->routeIs('login') ? 'active' : '' }}">
                        Sign in
                    </a>
                    @endauth
                </li>
            </ul>

            <button class="menu" type="button" aria-label="Open navigation menu"
                aria-controls="primaryNavigation" aria-expanded="false">
                <i class="fas fa-bars" aria-hidden="true"></i>
            </button>
        </nav>
    </header>
    @endunless

    <main>
        @yield('content')
    </main>

    @unless(request()->routeIs('login'))
        @include('layouts.partials.site-footer')
    @endunless

    <script src="{{ asset('/js/script.js') }}"></script>

</body>

</html>