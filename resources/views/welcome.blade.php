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
</head>

<body>
    <div id="container">
        <div class="row justify-content-center">
            <div class="col-md-4">
                <div class="card border-primary mt-5">
                    <div class="card-header bg-primary text-white text-center">
                        <i class="fa-solid fa-lock welcome-icon me-2"></i>Welcome to Finance Core
                    </div>
                    <div class="card-body">
                        <p class="text-muted mb-4">
                            A personal web application designed for straightforward financial management. It provides a clean interface to track daily expenses, monitor income, plan for future purchases, and digitally store receipts. The goal is to offer a simple, private, and efficient way to see where your money is going.
                        </p>
                        <div class="d-grid">
                            <a href="/dashboard" class="btn btn-secondary btn-lg btn-block">
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
