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

<body>

    <header>
        <nav class="navbar">
            <div class="logo">
                <img src="{{ asset('Images/images.jpg') }}"
                    alt="PHM HCC logo">
                <h2>PHM HCC</h2>
            </div>

            <ul class="nav-links">
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
                    <a href="{{ route('events') }}"
                        class="{{ request()->routeIs('events') ? 'active' : '' }}">
                        Events
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

            <div class="menu" aria-label="Open navigation menu">
                <i class="fas fa-bars"></i>
            </div>
        </nav>
    </header>

    <main>
        @yield('content')
    </main>

    <footer>
        <h2>PHM HCC</h2>

        <p>Pentecostal Holiness Mission Hope Christian Church</p>

        <div class="social">
            <a href="https://www.facebook.com/share/14kCi4Q2iXW/"
                target="_blank" rel="noopener noreferrer"
                aria-label="Facebook">
                <i class="fab fa-facebook"></i>
            </a>

            <a href="https://youtube.com/@pastor-happymacxmkomekwa?si=2UKZ1chgQAFkT2FI"
                target="_blank" rel="noopener noreferrer"
                aria-label="YouTube">
                <i class="fab fa-youtube"></i>
            </a>

            <a href="https://www.instagram.com/pastor_mkomekwa?igsh=eHI1Y3VtbnQ5dTEz"
                target="_blank" rel="noopener noreferrer"
                aria-label="Instagram">
                <i class="fab fa-instagram"></i>
            </a>

            <a href="https://wa.me/255754970421"
                target="_blank" rel="noopener noreferrer"
                aria-label="WhatsApp">
                <i class="fab fa-whatsapp"></i>
            </a>
        </div>

        <p>&copy; {{ date('Y') }} PHM HCC. All Rights Reserved.</p>
    </footer>

    <script src="{{ asset('/js/script.js') }}"></script>

</body>

</html>