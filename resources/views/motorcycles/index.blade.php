@extends('layouts.marketplace')

@section('title', 'Browse motorcycles | MotoMarket')

@section('content')
<section class="bg-light py-5"><div class="container"><div class="eyebrow">The marketplace</div><h1 class="display-6 fw-bold">Browse motorcycles</h1><p class="text-muted mb-0">Find a ride from sellers across the Philippines.</p></div></section>
<section class="container py-5">
    <div class="row g-4">
        <aside class="col-lg-3">
            <form class="filter-panel" method="GET" action="{{ route('motorcycles.index') }}">
                <div class="d-flex justify-content-between align-items-center mb-3"><h2 class="h5 mb-0">Filters</h2><a class="small link-danger" href="{{ route('motorcycles.index') }}">Clear all</a></div>
                <label for="catalogSearch" class="form-label">Keyword</label><input id="catalogSearch" class="form-control mb-3" name="q" value="{{ $filters['q'] ?? '' }}" placeholder="Brand or model">
                <label for="category" class="form-label">Category</label><select id="category" class="form-select mb-3" name="category"><option value="">All categories</option>@foreach ($categories as $category)<option value="{{ $category->slug }}" @selected(($filters['category'] ?? '') === $category->slug)>{{ $category->name }}</option>@endforeach</select>
                <label for="condition" class="form-label">Condition</label><select id="condition" class="form-select mb-3" name="condition"><option value="">Any condition</option><option value="new" @selected(($filters['condition'] ?? '') === 'new')>New</option><option value="used" @selected(($filters['condition'] ?? '') === 'used')>Used</option></select>
                <div class="row g-2 mb-3"><div class="col-6"><label for="minPrice" class="form-label">Min price</label><input id="minPrice" class="form-control" name="min_price" type="number" min="0" value="{{ $filters['min_price'] ?? '' }}" placeholder="&#8369;0"></div><div class="col-6"><label for="maxPrice" class="form-label">Max price</label><input id="maxPrice" class="form-control" name="max_price" type="number" min="0" value="{{ $filters['max_price'] ?? '' }}" placeholder="No max"></div></div>
                <label for="year" class="form-label">Year</label><input id="year" class="form-control mb-3" name="year" type="number" min="1950" max="{{ now()->year + 1 }}" value="{{ $filters['year'] ?? '' }}" placeholder="Any year">
                <label for="location" class="form-label">Location</label><input id="location" class="form-control mb-3" name="location" value="{{ $filters['location'] ?? '' }}" placeholder="City or province">
                <label for="sort" class="form-label">Sort by</label><select id="sort" class="form-select mb-3" name="sort"><option value="newest" @selected(($filters['sort'] ?? 'newest') === 'newest')>Newest</option><option value="oldest" @selected(($filters['sort'] ?? '') === 'oldest')>Oldest</option><option value="price_asc" @selected(($filters['sort'] ?? '') === 'price_asc')>Lowest price</option><option value="price_desc" @selected(($filters['sort'] ?? '') === 'price_desc')>Highest price</option></select>
                <button class="btn btn-danger w-100" type="submit">Apply filters</button>
            </form>
        </aside>
        <div class="col-lg-9">
            <div class="d-flex justify-content-between align-items-center mb-3"><p class="text-muted mb-0">{{ $motorcycles->total() }} {{ Str::plural('motorcycle', $motorcycles->total()) }} found</p></div>
            @if ($motorcycles->isEmpty())
                <div class="text-center border rounded-2 py-5 px-3"><h2 class="h5 fw-bold">No motorcycles match those filters</h2><p class="text-muted">Try broadening your search or clearing some filters.</p><a href="{{ route('motorcycles.index') }}" class="btn btn-outline-danger">Clear filters</a></div>
            @else
                <div class="row row-cols-1 row-cols-sm-2 row-cols-xl-3 g-4">@foreach ($motorcycles as $motorcycle)<div class="col"><x-motorcycle-card :motorcycle="$motorcycle" /></div>@endforeach</div>
                <div class="mt-4">{{ $motorcycles->links() }}</div>
            @endif
        </div>
    </div>
</section>
@endsection