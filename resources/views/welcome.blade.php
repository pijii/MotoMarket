@extends('layouts.marketplace')

@section('title', 'MotoMarket | Find your next motorcycle')

@section('content')
<section class="mm-hero">
    <div class="container-fluid px-0"><div class="row g-0 align-items-stretch">
        <div class="col-md-6 d-flex align-items-center"><div class="container py-5 px-4 px-xl-5">
            <div class="eyebrow mb-3">Your next chapter starts here</div>
            <h1 class="display-4 fw-bold mb-3">Find the Motorcycle That's Right for You.</h1>
            <p class="lead text-muted mb-4">Discover new and secondhand motorcycles from individual sellers and dealerships across the Philippines.</p>
            <form action="{{ route('motorcycles.index') }}" method="GET" class="row g-2 mb-3"><div class="col-sm-8"><label class="visually-hidden" for="heroSearch">Brand or model</label><input class="form-control" id="heroSearch" name="q" placeholder="Try Honda, Yamaha, or NMAX"></div><div class="col-sm-4 d-grid"><button class="btn btn-danger" type="submit">Find Motorcycles</button></div></form>
            <div class="d-flex flex-wrap gap-3 align-items-center"><a href="{{ route('motorcycles.index') }}" class="link-danger">Explore all motorcycles</a><span class="text-muted">or</span><a href="{{ route('register') }}" class="btn btn-outline-dark">Sell Your Motorcycle</a></div>
        </div></div>
        <div class="col-md-6 mm-hero-photo" role="img" aria-label="Motorcycle ready for the open road"></div>
    </div></div>
</section>

<section class="section-space"><div class="container">
    <div class="mb-4"><div class="eyebrow">Find your fit</div><h2 class="h2 fw-bold mb-0">Browse by category</h2></div>
    <div class="row row-cols-2 row-cols-md-3 row-cols-lg-6 g-3">
        @forelse ($categories as $category)
            <div class="col"><a class="category-link" href="{{ route('motorcycles.index', ['category' => $category->slug]) }}"><span class="small text-muted d-block mb-2">MotoMarket</span><span class="fw-semibold">{{ $category->name }}</span><span class="d-block mt-2 text-danger" aria-hidden="true">&rarr;</span></a></div>
        @empty
            @foreach (['Scooter', 'Sport', 'Underbone', 'Cruiser', 'Standard', 'Adventure'] as $category)
                <div class="col"><a class="category-link" href="{{ route('motorcycles.index', ['q' => $category]) }}"><span class="small text-muted d-block mb-2">MotoMarket</span><span class="fw-semibold">{{ $category }}</span><span class="d-block mt-2 text-danger" aria-hidden="true">&rarr;</span></a></div>
            @endforeach
        @endforelse
    </div>
</div></section>

<section class="section-space bg-light"><div class="container">
    <div class="d-flex flex-wrap justify-content-between align-items-end gap-3 mb-4"><div><div class="eyebrow">Hand-picked from approved listings</div><h2 class="h2 fw-bold mb-0">Featured motorcycles</h2></div><a class="link-danger" href="{{ route('motorcycles.index') }}">Browse all &rarr;</a></div>
    @if ($featured->isEmpty())
        <div class="text-center py-5 px-3 border rounded-2 bg-white"><h3 class="h5 fw-bold">New rides are on the way</h3><p class="text-muted mb-3">There are no approved motorcycles to feature yet. Check back soon or be the first seller.</p><a href="{{ route('register') }}" class="btn btn-danger">Start selling</a></div>
    @else
        <div class="row row-cols-1 row-cols-sm-2 row-cols-lg-4 g-4">@foreach ($featured as $motorcycle)<div class="col"><x-motorcycle-card :motorcycle="$motorcycle" /></div>@endforeach</div>
    @endif
</div></section>

<section class="section-space"><div class="container">
    <div class="d-flex flex-wrap justify-content-between align-items-end gap-3 mb-4"><div><div class="eyebrow">Freshly listed</div><h2 class="h2 fw-bold mb-0">Recently added</h2></div><a class="link-danger" href="{{ route('motorcycles.index', ['sort' => 'newest']) }}">See the marketplace &rarr;</a></div>
    @if ($recent->isEmpty())
        <div class="py-4 border-top border-bottom"><p class="text-muted mb-0">Recently added motorcycles will appear here after they pass review.</p></div>
    @else
        <div class="row row-cols-1 row-cols-sm-2 row-cols-lg-4 g-4">@foreach ($recent as $motorcycle)<div class="col"><x-motorcycle-card :motorcycle="$motorcycle" /></div>@endforeach</div>
    @endif
</div></section>

<section class="py-5 bg-dark text-white"><div class="container py-3 d-flex flex-column flex-md-row align-items-md-center justify-content-between gap-4"><div><div class="eyebrow">For motorcycle owners</div><h2 class="h2 fw-bold mb-2">Your next buyer is already looking.</h2><p class="text-white-50 mb-0">Create a listing, connect directly, and sell on your terms.</p></div><a href="{{ route('register') }}" class="btn btn-danger btn-lg px-4">List your motorcycle</a></div></section>
@endsection