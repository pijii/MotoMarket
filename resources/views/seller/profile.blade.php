@extends('layouts.marketplace')
@section('title', 'Seller profile | MotoMarket')
@section('content')
<section class="container py-5" style="max-width:720px"><div class="eyebrow">Seller workspace</div><h1 class="h2 fw-bold">Seller profile</h1><x-dashboard-nav /><form method="POST" action="{{ route('seller.profile.update') }}" class="border rounded-2 p-4">@csrf @method('PATCH')
    <div class="mb-3"><label class="form-label" for="name">Name</label><input class="form-control" id="name" name="name" value="{{ old('name', $user->name) }}" required>@error('name')<div class="text-danger small">{{ $message }}</div>@enderror</div>
    <div class="mb-3"><label class="form-label" for="email">Email</label><input class="form-control" id="email" value="{{ $user->email }}" readonly></div>
    <div class="mb-3"><label class="form-label" for="phone">Phone</label><input class="form-control" id="phone" name="phone" value="{{ old('phone', $user->phone) }}" autocomplete="tel">@error('phone')<div class="text-danger small">{{ $message }}</div>@enderror</div>
    <div class="mb-3"><label class="form-label" for="location">Location</label><input class="form-control" id="location" name="location" value="{{ old('location', $user->location) }}" placeholder="City, province">@error('location')<div class="text-danger small">{{ $message }}</div>@enderror</div>
    <button class="btn btn-danger" type="submit">Save profile</button>
</form></section>
@endsection