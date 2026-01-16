{{-- resources/views/welcome.blade.php --}}

<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>{{ config('app.name', 'Laravel') }}</title>

    {{-- Fonts --}}
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=instrument-sans:400,500,600&display=swap" rel="stylesheet" />

    {{-- Bootstrap 5 (CDN) --}}
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

    <style>
        body { font-family: "Instrument Sans", system-ui, -apple-system, Segoe UI, Roboto, "Helvetica Neue", Arial, "Noto Sans", "Liberation Sans", sans-serif; }
        .hero {
            background: radial-gradient(1200px circle at 10% 10%, rgba(13,110,253,.08), transparent 55%),
                        radial-gradient(1200px circle at 90% 20%, rgba(220,53,69,.08), transparent 55%),
                        #f8f9fa;
        }
    </style>
</head>
<body class="hero min-vh-100 d-flex flex-column">

    {{-- Top Navbar --}}
    <nav class="navbar navbar-expand-lg bg-white border-bottom">
        <div class="container">
            <a class="navbar-brand fw-semibold" href="{{ url('/') }}">
                {{ config('app.name', 'Laravel') }}
            </a>

            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#welcomeNavbar"
                aria-controls="welcomeNavbar" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>

            <div class="collapse navbar-collapse" id="welcomeNavbar">
                <div class="ms-auto d-flex gap-2 py-2 py-lg-0">
                    @if (Route::has('login'))
                        @auth
                            <a href="{{ url('/dashboard') }}" class="btn btn-primary">
                                Dashboard
                            </a>
                        @else
                            <a href="{{ route('login') }}" class="btn btn-outline-secondary">
                                Log in
                            </a>

                            @if (Route::has('register'))
                                <a href="{{ route('register') }}" class="btn btn-primary">
                                    Register
                                </a>
                            @endif
                        @endauth
                    @endif
                </div>
            </div>
        </div>
    </nav>

    {{-- Page --}}
    <main class="flex-grow-1 d-flex align-items-center">
        <div class="container py-5">
            <div class="row g-4 align-items-stretch">
                {{-- Left / Content --}}
                <div class="col-lg-7">
                    <div class="p-4 p-md-5 bg-white border rounded-4 shadow-sm h-100">
                        <div class="text-uppercase small text-secondary fw-semibold mb-2">
                            Welcome
                        </div>

                        <h1 class="display-6 fw-semibold mb-3">
                            Let’s get started
                        </h1>

                        <p class="text-secondary mb-4">
                            Laravel ha un ecosistema enorme. Qui sotto trovi due risorse utili per partire velocemente.
                        </p>

                        <div class="list-group mb-4">
                            <a href="https://laravel.com/docs" target="_blank" class="list-group-item list-group-item-action d-flex justify-content-between align-items-center">
                                <span class="fw-medium">Documentation</span>
                                <span class="text-secondary">→</span>
                            </a>
                            <a href="https://laracasts.com" target="_blank" class="list-group-item list-group-item-action d-flex justify-content-between align-items-center">
                                <span class="fw-medium">Laracasts</span>
                                <span class="text-secondary">→</span>
                            </a>
                        </div>

                        <div class="d-flex flex-wrap gap-2">
                            <a href="https://cloud.laravel.com" target="_blank" class="btn btn-dark">
                                Deploy now
                            </a>
                            <a href="https://laravel.com" target="_blank" class="btn btn-outline-secondary">
                                Learn more
                            </a>
                        </div>
                    </div>
                </div>

                {{-- Right / Visual --}}
                <div class="col-lg-5">
                    <div class="p-4 p-md-5 border rounded-4 shadow-sm h-100 bg-white">
                        <div class="d-flex align-items-center justify-content-center h-100">
                            <div class="text-center">
                                <div class="mb-3">
                                    {{-- Se vuoi, qui puoi mettere il tuo logo --}}
                                    <span class="badge text-bg-danger px-3 py-2">Laravel</span>
                                </div>
                                <h2 class="h4 fw-semibold mb-2">Project area</h2>
                                <p class="text-secondary mb-0">
                                    Accedi per gestire preventivi, ordini e progetti.
                                </p>
                            </div>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </main>

    {{-- Bootstrap JS --}}
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>