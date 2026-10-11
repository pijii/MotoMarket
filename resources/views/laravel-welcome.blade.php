<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Laravel</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-light">
    <div class="container min-vh-100 d-flex flex-column justify-content-center py-5" style="max-width:1080px">
        <header class="d-flex justify-content-between align-items-center mb-5">
            <a href="{{ route('landing') }}" class="text-danger fw-bold fs-4 text-decoration-none">Laravel</a>
            <nav class="d-flex gap-3 align-items-center" aria-label="Account navigation">
                @auth
                    <a class="link-secondary" href="{{ route('dashboard') }}">Dashboard</a>
                @else
                    <a class="link-secondary" href="{{ route('login') }}">Log in</a>
                    <a class="btn btn-danger" href="{{ route('register') }}">Register</a>
                @endauth
            </nav>
        </header>

        <main class="row g-4 align-items-stretch">
            <section class="col-lg-7">
                <div class="h-100 bg-white border rounded-2 p-4 p-md-5">
                    <div class="text-uppercase small fw-bold text-danger mb-3">Laravel {{ Illuminate\Foundation\Application::VERSION }}</div>
                    <h1 class="display-5 fw-bold mb-3">Let's get started</h1>
                    <p class="lead text-secondary">Laravel has an incredibly rich ecosystem. We suggest starting with the following.</p>
                    <div class="d-flex flex-column gap-3 mt-4">
                        <a class="text-danger fw-semibold" href="https://laravel.com/docs" target="_blank" rel="noreferrer">Read the documentation</a>
                        <a class="text-danger fw-semibold" href="https://laracasts.com" target="_blank" rel="noreferrer">Watch video tutorials at Laracasts</a>
                    </div>
                    <a class="btn btn-danger mt-5" href="{{ route('home') }}">Browse MotoMarket</a>
                </div>
            </section>
            <aside class="col-lg-5">
                <div class="h-100 bg-white border rounded-2 p-4 p-md-5">
                    <h2 class="h4 fw-bold">Your motorcycle marketplace</h2>
                    <p class="text-secondary">Find new and secondhand motorcycles from sellers across the Philippines.</p>
                    <a class="link-danger fw-semibold" href="{{ route('motorcycles.index') }}">Browse motorcycles &rarr;</a>
                </div>
            </aside>
        </main>

        <footer class="text-secondary small mt-5">Laravel application starter page</footer>
    </div>
</body>
</html>