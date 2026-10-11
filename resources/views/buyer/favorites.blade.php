@extends('layouts.marketplace')
@section('title', 'Saved motorcycles | MotoMarket')
@section('content')
<section class="container py-5"><div class="eyebrow">Your account</div><h1 class="h2 fw-bold mb-3">Saved motorcycles</h1><x-dashboard-nav />
    @if ($motorcycles->isEmpty())<div class="text-center border rounded-2 p-5"><h2 class="h5 fw-bold">No saved motorcycles yet</h2><p class="text-muted">Save listings you want to come back to.</p><a class="btn btn-danger" href="{{ route('motorcycles.index') }}">Browse motorcycles</a></div>@else
        <div class="row row-cols-1 row-cols-sm-2 row-cols-lg-4 g-4">@foreach ($motorcycles as $motorcycle)<div class="col"><x-motorcycle-card :motorcycle="$motorcycle" /><form method="POST" action="{{ route('favorites.toggle', $motorcycle) }}" class="mt-2">@csrf<button class="btn btn-sm btn-outline-danger" type="submit">Remove saved item</button></form></div>@endforeach</div><div class="mt-4">{{ $motorcycles->links() }}</div>
    @endif
</section>
@endsection