<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link rel="icon" type="image/svg+xml" href="{{ asset('favicon.svg') }}">
    <title>@yield('title', 'MotoMarket | Motorcycles, made easier')</title>
    <meta name="description" content="Discover new and used motorcycles from riders and dealerships across the Philippines.">
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=instrument-sans:400,500,600,700,800&display=swap" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body>
<nav class="navbar navbar-expand-lg bg-white mm-nav sticky-top">
    <div class="container py-2">
        <a class="mm-brand" href="{{ route('home') }}" aria-label="MotoMarket home"><img class="mm-brand-logo" src="{{ asset('images/motomarket-logo.svg') }}" alt="MotoMarket"></a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#mainNavigation" aria-controls="mainNavigation" aria-expanded="false" aria-label="Toggle navigation"><span class="navbar-toggler-icon"></span></button>
        <div class="collapse navbar-collapse" id="mainNavigation">
            <div class="navbar-nav ms-auto align-items-lg-center gap-lg-3">
                <a class="nav-link" href="{{ route('home') }}">Home</a>
                <a class="nav-link" href="{{ route('motorcycles.index') }}">Browse Motorcycles</a>
                @auth
                    @if (auth()->user()->role === 'seller' || auth()->user()->isAdmin())
                        <a class="nav-link" href="{{ route('dashboard') }}">Dashboard</a>
                    @else
                        <a class="nav-link" href="{{ route('seller.register') }}">Sell a Motorcycle</a>
                    @endif
                    <div class="nav-item dropdown">
                        <button class="btn btn-link nav-link dropdown-toggle text-decoration-none" data-bs-toggle="dropdown" aria-expanded="false">{{ auth()->user()->name }}</button>
                        <ul class="dropdown-menu dropdown-menu-end">
                            <li><a class="dropdown-item" href="{{ route('dashboard') }}">My dashboard</a></li>
                            <li><a class="dropdown-item" href="{{ auth()->user()->isSeller() ? route('seller.profile.edit') : route('profile.edit') }}">Profile</a></li>
                            @if (Route::has('favorites.index'))<li><a class="dropdown-item" href="{{ route('favorites.index') }}">Saved motorcycles</a></li>@endif
                            <li><hr class="dropdown-divider"></li>
                            <li><form method="POST" action="{{ route('logout') }}">@csrf<button class="dropdown-item" type="submit">Log out</button></form></li>
                        </ul>
                    </div>
                @else
                    <a class="nav-link" href="{{ route('register') }}">Sell a Motorcycle</a>
                    <a class="nav-link" href="{{ route('login') }}">Log in</a>
                    <a class="btn btn-danger px-4" href="{{ route('register') }}">Create account</a>
                @endauth
            </div>
        </div>
    </div>
</nav>

@if (session('status'))<div class="container pt-3"><div class="alert alert-success mb-0" role="status">{{ session('status') }}</div></div>@endif
@if ($errors->any())<div class="container pt-3"><div class="alert alert-danger mb-0" role="alert">Please review the highlighted fields and try again.</div></div>@endif

<main>@yield('content')</main>

<footer class="mm-footer mt-5 py-5">
    <div class="container">
        <div class="row gy-4">
            <div class="col-md-6"><a class="mm-brand fs-4 text-white" href="{{ route('home') }}"><span class="mm-brand-mark">M</span>otoMarket</a><p class="mt-3 mb-0 text-white-50">A better way to find your next ride, from trusted sellers across the Philippines.</p></div>
            <div class="col-6 col-md-3"><h2 class="h6 text-white">Explore</h2><a class="d-block link-light link-underline-opacity-0 mb-2" href="{{ route('motorcycles.index') }}">Browse motorcycles</a><a class="d-block link-light link-underline-opacity-0" href="{{ route('register') }}">Sell with us</a></div>
            <div class="col-6 col-md-3"><h2 class="h6 text-white">Your account</h2><a class="d-block link-light link-underline-opacity-0 mb-2" href="{{ route('login') }}">Log in</a><a class="d-block link-light link-underline-opacity-0" href="{{ route('register') }}">Create account</a></div>
        </div>
        <hr class="border-secondary my-4"><p class="small text-white-50 mb-0">&copy; {{ date('Y') }} MotoMarket. All rights reserved.</p>
    </div>
</footer>
</body>
</html>