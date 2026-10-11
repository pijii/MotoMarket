@extends('layouts.marketplace')
@section('title', 'Become a seller | MotoMarket')
@section('content')
<section class="container py-5" style="max-width:650px"><div class="eyebrow">Seller account</div><h1 class="h2 fw-bold">Start selling on MotoMarket</h1><p class="text-muted">Create listings, connect with buyers, and manage your motorcycles from one place.</p><form method="POST" action="{{ route('seller.register.store') }}" class="border rounded-2 p-4 mt-4">@csrf<p class="mb-4">Your existing buyer profile stays intact. You can update your contact and location details anytime.</p><button class="btn btn-danger" type="submit">Become a seller</button></form></section>
@endsection