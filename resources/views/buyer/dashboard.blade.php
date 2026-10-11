@extends('layouts.marketplace')
@section('title', 'Your dashboard | MotoMarket')
@section('content')
<section class="container py-5"><div class="eyebrow">Your account</div><h1 class="h2 fw-bold mb-4">Welcome, {{ auth()->user()->name }}</h1><x-dashboard-nav />
    <div class="row g-3"><div class="col-md-6"><a class="d-block p-4 border rounded-2 text-decoration-none" href="{{ route('favorites.index') }}"><div class="small text-muted">Saved motorcycles</div><div class="h3 fw-bold mt-2 mb-0">{{ $favoritesCount }}</div></a></div><div class="col-md-6"><a class="d-block p-4 border rounded-2 text-decoration-none" href="{{ route('conversations.index') }}"><div class="small text-muted">Conversations</div><div class="h3 fw-bold mt-2 mb-0">{{ $conversationsCount }}</div></a></div></div>
</section>
@endsection