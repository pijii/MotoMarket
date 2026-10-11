<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link rel="icon" type="image/svg+xml" href="{{ asset('favicon.svg') }}">
    <title>{{ config('app.name', 'MotoMarket') }}</title>
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=instrument-sans:400,500,600,700,800&display=swap" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body>
<nav class="navbar navbar-expand-lg bg-white mm-nav"><div class="container py-2">
    <a class="mm-brand" href="{{ route('home') }}" aria-label="MotoMarket home"><img class="mm-brand-logo" src="{{ asset('images/motomarket-logo.svg') }}" alt="MotoMarket"></a>
    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#accountNavigation" aria-label="Toggle navigation"><span class="navbar-toggler-icon"></span></button>
    <div class="collapse navbar-collapse" id="accountNavigation"><div class="navbar-nav ms-auto align-items-lg-center gap-lg-3">
        <a class="nav-link" href="{{ route('motorcycles.index') }}">Browse Motorcycles</a><a class="nav-link" href="{{ route('dashboard') }}">Dashboard</a>
        <form method="POST" action="{{ route('logout') }}">@csrf<button class="btn btn-outline-danger" type="submit">Log out</button></form>
    </div></div>
</div></nav>
@isset($header)<header class="bg-light py-4"><div class="container">{{ $header }}</div></header>@endisset
<main class="container py-5">{{ $slot }}</main>
</body>
</html>
