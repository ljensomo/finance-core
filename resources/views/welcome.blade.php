<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name') }} - Personal Finance Manager</title>

    <!-- Fonts -->
    <link rel="dns-prefetch" href="//fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=Nunito" rel="stylesheet">

    <!-- Scripts -->
    @vite(['resources/sass/app.scss', 'resources/js/app.js'])

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" integrity="sha512-9usAa10IRO0HhonpyAIVpjrylPvoDwiPUiKdWk5t3PyolY1cOd4DSE0Ga+ri4AuTroPR5aQvXU9xC6qOPnzFeg==" crossorigin="anonymous" referrerpolicy="no-referrer" />
    <link rel="icon" href="{{ asset('finance_core_logo.png') }}" type="image/png">
</head>

<body>
    <div class="container py-5">
        <div class="row justify-content-center">
            <div class="col-md-6 col-lg-4">
                <div class="card border-1 shadow-lg rounded-3 mt-5 overflow-hidden">
                    <div class="card-header bg-primary text-white text-center py-3">
                        <h5 class="mb-0">
                            <i class="fa-solid fa-lock me-2"></i>Welcome to Finance Core
                        </h5>
                    </div>
                    
                    <div class="card-body p-4">
                        <p class="text-secondary mb-4">
                            A personal web application designed for straightforward financial management. Track expenses, monitor income, and plan for your future with a clean, private, and efficient interface.
                        </p>
                        
                        <div class="d-grid">
                            <a href="/dashboard" class="btn btn-primary btn-lg px-4">
                                <i class="fa-solid fa-arrow-right-to-bracket me-2"></i> Get Started
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
