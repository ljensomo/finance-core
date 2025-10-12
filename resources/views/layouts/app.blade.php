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
</head>

<body>
    <div id="app">
        <nav class="navbar navbar-light navbar-expand-lg bg-primary shadow-sm fixed-top">
            <div class="container-fluid">
                <a class="navbar-brand tex-white" href="{{ url('/') }}" style="margin-left: 30px; color:#fff !important;"><strong>{{ config('app.name', 'Laravel') }}</strong></a>
                <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarText"
                    aria-controls="navbarText" aria-expanded="false" aria-label="Toggle navigation">
                    <span class="navbar-toggler-icon"></span>
                </button>
                <div class="collapse navbar-collapse" id="navbarText">
                    <ul class="navbar-nav me-auto mb-2 mb-lg-0"></ul>
                    <ul class="navbar-nav ms-auto">
                        <!-- Authentication Links -->
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
                                    data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false" v-pre>
                                    {{ Auth::user()->name }}
                                </a>

                                <div class="dropdown-menu dropdown-menu-end" aria-labelledby="navbarDropdown">
                                    <a class="dropdown-item" href="{{ route('logout') }}"
                                        onclick="event.preventDefault();
                                                     document.getElementById('logout-form').submit();">
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
                <nav class="sidebar p-3 shadow-sm" style="width: 250px; min-height: 100vh;">
                    <ul class="nav flex-column" style="margin-top: 50px;">
                        <li class="nav-item">
                            <a class="nav-link" href="dashboard">
                                <i class="fas fa-tachometer-alt me-2"></i>{{ __('Dashboard') }}
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="transactions">
                                <i class="fa-solid fa-file-invoice me-2"></i>{{ __('Transactions') }}
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="categories">
                                <i class="fa-solid fa-bars me-2"></i>{{ __('Categories') }}
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="wishlists">
                                <i class="fas fa-heart me-2"></i>{{ __('Wishlists') }}
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="reports">
                                <i class="fas fa-chart-pie me-2"></i>{{ __('Reports') }}
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="settings">
                                <i class="fas fa-sign-out-alt me-2"></i>{{ __('Settings') }}
                            </a>
                        </li>
                    </ul>
                </nav>
            @endauth
            <main class="flex-grow-1 py-4" style="margin-top: 50px;">
                @yield('content')
            </main>
        </div>
    </div>
</body>

</html>
