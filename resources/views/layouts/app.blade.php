<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Laravel') }}</title>

    <!-- Fonts -->
    <link rel="dns-prefetch" href="//fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=Nunito" rel="stylesheet">

    <!-- Scripts -->
    @vite(['resources/sass/app.scss', 'resources/js/app.js'])

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" integrity="sha512-9usAa10IRO0HhonpyAIVpjrylPvoDwiPUiKdWk5t3PyolY1cOd4DSE0Ga+ri4AuTroPR5aQvXU9xC6qOPnzFeg==" crossorigin="anonymous" referrerpolicy="no-referrer" />
    <link rel="icon" href="{{ asset('finance_core_logo.png') }}" type="image/png">
    <style>
        .sub-item {
            padding-left: 2rem;
        }
        .sidebar{
            width: 250px;
            min-height: 100vh;
            position: fixed;
            top: 0;
            left: 0;
            z-index: 1000;
        }
        .sidebar .nav{
            margin-top: 60px;
        }
        /* Parent Link Styling */
        .nav-link {
        color: #adb5bd;
        padding: 0.8rem 1rem;
        transition: all 0.3s ease;
        border-radius: 8px;
        margin-bottom: 2px;
        }

        .nav-link:hover, .nav-link.active {
        background-color: rgba(13, 110, 253, 0.1);
        color: #0d6efd;
        }

        /* Chevron Rotation Logic */
        .nav-link[aria-expanded="true"] .chevron-icon {
        transform: rotate(90deg);
        }

        .chevron-icon {
        transition: transform 0.3s ease;
        }

        /* Submenu container */
        .submenu-list {
        padding-left: 1.5rem; /* Moves sub-items to the right */
        margin-left: 1rem;
        border-left: 1px solid #dee2e6; /* The connector line */
        }

        /* Sub-item styling */
        .sub-item {
        font-size: 0.9rem;
        padding: 0.5rem 1rem;
        opacity: 0.8;
        }

        .sub-item:hover {
        opacity: 1;
        background: transparent !important; /* Keep it clean */
        color: #0d6efd;
        }

        .tiny-icon {
        font-size: 0.5rem;
        vertical-align: middle;
        }

        .navbar-nav .nav-link {
            transition: opacity 0.3s ease;
        }

        .navbar-nav .nav-link:hover {
            opacity: 0.8;
        }
    </style>
</head>

<body>
    <div id="app">
<nav class="navbar navbar-expand-lg navbar-dark bg-primary shadow-sm fixed-top">
    <div class="container-fluid">
        <a class="navbar-brand ms-lg-4 fw-bold text-white" href="{{ url('/') }}">
            {{ config('app.name', 'Laravel') }}
        </a>

        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarText"
            aria-controls="navbarText" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>

        <div class="collapse navbar-collapse" id="navbarText">
            <ul class="navbar-nav me-auto mb-2 mb-lg-0"></ul>
            
            <ul class="navbar-nav ms-auto">
                @guest
                    @if (Route::has('login'))
                        <li class="nav-item">
                            <a class="nav-link text-white" href="{{ route('login') }}">{{ __('Login') }}</a>
                        </li>
                    @endif
                    @if (Route::has('register'))
                        <li class="nav-item">
                            <a class="nav-link text-white" href="{{ route('register') }}">{{ __('Register') }}</a>
                        </li>
                    @endif
                @else
                    <li class="nav-item dropdown">
                        <a id="navbarDropdown" class="nav-link dropdown-toggle text-white" href="#" role="button"
                            data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                            {{ Auth::user()->name }}
                        </a>

                        <div class="dropdown-menu dropdown-menu-end" aria-labelledby="navbarDropdown">
                            <a class="dropdown-item" href="{{ route('logout') }}"
                                onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                                {{ __('Logout') }}
                            </a>

                            <form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">
                                @csrf
                            </form>
                        </div>
                    </li>
                @endguest
            </ul>
        </div>
    </div>
</nav>
        <div class="d-flex">
            @auth
                <nav class="sidebar p-3 shadow-sm">
                    <ul class="nav flex-column gap-1">
                        <li class="nav-item">
                            <a class="nav-link px-3 py-2 rounded-3 transition-all d-flex align-items-center justify-content-between {{ request()->is('dashboard*') ? 'active bg-primary text-white' : 'text-secondary' }}" 
                            data-bs-toggle="collapse" href="#dashboardSubmenu" role="button" aria-expanded="false">
                                <span class="d-flex align-items-center">
                                    <i class="fas fa-chart-line me-3" style="width: 20px; text-align: center;"></i>
                                    <span class="fw-medium">{{ __('Dashboard') }}</span>
                                </span>
                                <i class="fas fa-chevron-right small transition-all"></i>
                            </a>
                            <ul class="collapse list-unstyled ps-4 mt-1" id="dashboardSubmenu">
                                <li><a class="nav-link py-1 text-secondary" href="dashboard"><i class="far fa-circle me-2 tiny-icon"></i> {{ __('Overall View') }}</a></li>
                                <li><a class="nav-link py-1 text-secondary" href="monthly-dashboard"><i class="far fa-circle me-2 tiny-icon"></i> {{ __('Monthly Analytics') }}</a></li>
                            </ul>
                        </li>

                        @php
                            $links = [
                                ['url' => 'transactions', 'icon' => 'fa-file-invoice', 'label' => 'Transactions'],
                                ['url' => 'budgets', 'icon' => 'fa-calculator', 'label' => 'Budgets'],
                                ['url' => 'wishlists', 'icon' => 'fa-heart', 'label' => 'Wishlists'],
                                ['url' => 'import-logs', 'icon' => 'fa-file-alt', 'label' => 'Import Logs'],
                            ];
                        @endphp

                        @foreach($links as $link)
                        <li class="nav-item">
                            <a class="nav-link px-3 py-2 rounded-3 transition-all d-flex align-items-center {{ request()->is($link['url'] . '*') ? 'active bg-primary text-white' : 'text-secondary' }}" 
                            href="{{ url($link['url']) }}">
                                <i class="fa-solid {{ $link['icon'] }} me-3" style="width: 20px; text-align: center;"></i>
                                <span class="fw-medium">{{ __($link['label']) }}</span>
                            </a>
                        </li>
                        @endforeach

                        <li class="nav-item">
                            <a class="nav-link px-3 py-2 rounded-3 transition-all d-flex align-items-center justify-content-between {{ request()->is('settings*') ? 'active bg-primary text-white' : 'text-secondary' }}" 
                            data-bs-toggle="collapse" href="#settingsSubmenu" role="button" aria-expanded="false">
                                <span class="d-flex align-items-center">
                                    <i class="fas fa-chart-line me-3" style="width: 20px; text-align: center;"></i>
                                    <span class="fw-medium">{{ __('Settings') }}</span>
                                </span>
                                <i class="fas fa-chevron-right small transition-all"></i>
                            </a>
                            <ul class="collapse list-unstyled ps-4 mt-1" id="settingsSubmenu">
                                <li><a class="nav-link py-1 text-secondary" href="settings"><i class="fa-solid fa-bars me-2 tiny-icon"></i> {{ __('Categories') }}</a></li>
                                <li><a class="nav-link py-1 text-secondary" href="user-profile"><i class="fa-solid fa-bars me-2 tiny-icon"></i> {{ __('Sub Categories') }}</a></li>
                            </ul>
                        </li>
                    </ul>
                </nav>
                <main class="flex-grow-1 py-4" style="margin-top: 50px; padding-left: 250px;">
                    @yield('content')
                </main>
            @endauth
            @guest
                <main class="flex-grow-1 py-4">
                    @yield('content')
                </main>
            @endguest
        </div>
    </div>
</body>

</html>
