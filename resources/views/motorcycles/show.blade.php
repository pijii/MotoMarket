@extends('layouts.marketplace')

@section('title', $motorcycle->display_name.' | MotoMarket')

@section('content')
<section class="container py-5">
    <div class="mb-4"><a class="link-danger text-decoration-none" href="{{ route('motorcycles.index') }}">&larr; Browse motorcycles</a></div>
    <div class="row g-5">
        <div class="col-lg-7">
            @if ($motorcycle->images->isNotEmpty())
                <img class="detail-image rounded-2" src="{{ \Illuminate\Support\Facades\Storage::url($motorcycle->images->first()->image_path) }}" alt="{{ $motorcycle->display_name }}">
                @if ($motorcycle->images->count() > 1)<div class="row g-2 mt-2">@foreach ($motorcycle->images->skip(1) as $image)<div class="col-4"><img class="w-100 rounded-2" style="aspect-ratio:4/3;object-fit:cover" src="{{ \Illuminate\Support\Facades\Storage::url($image->image_path) }}" alt="Additional view of {{ $motorcycle->display_name }}"></div>@endforeach</div>@endif
            @else
                <div class="motorcycle-placeholder rounded-2" style="aspect-ratio:4/3"><span>No photo available</span></div>
            @endif
            <div class="mt-5"><h2 class="h4 fw-bold">About this motorcycle</h2><p class="text-muted" style="white-space:pre-line">{{ $motorcycle->description }}</p></div>
        </div>
        <div class="col-lg-5">
            <div class="eyebrow">{{ $motorcycle->category->name }} &middot; {{ ucfirst($motorcycle->condition) }}</div>
            <h1 class="h2 fw-bold mt-2">{{ $motorcycle->display_name }}</h1>
            <div class="price display-6 mb-4">&#8369;{{ number_format((float) $motorcycle->price, 0) }}</div>
            <div class="border-top border-bottom py-3 mb-4"><div class="row g-3"><div class="col-6"><span class="text-muted d-block small">Year</span><strong>{{ $motorcycle->year }}</strong></div><div class="col-6"><span class="text-muted d-block small">Mileage</span><strong>{{ $motorcycle->mileage ? number_format($motorcycle->mileage).' km' : 'Not specified' }}</strong></div><div class="col-6"><span class="text-muted d-block small">Location</span><strong>{{ $motorcycle->location }}</strong></div><div class="col-6"><span class="text-muted d-block small">Listed</span><strong>{{ $motorcycle->created_at->format('M j, Y') }}</strong></div></div></div>
            <div class="p-3 border rounded-2 mb-4"><div class="small text-muted">Seller</div><div class="fw-bold">{{ $motorcycle->seller->name }}</div><div class="small text-muted">{{ $motorcycle->seller->location ?: $motorcycle->location }}</div></div>
            @auth
                @if (auth()->id() !== $motorcycle->seller_id)
                    <div class="d-grid gap-2"><form method="POST" action="{{ route('conversations.start', $motorcycle) }}">@csrf<button class="btn btn-danger btn-lg" type="submit">Contact seller</button></form><form method="POST" action="{{ route('favorites.toggle', $motorcycle) }}">@csrf<button class="btn btn-outline-danger" type="submit">{{ auth()->user()->favorites()->whereKey($motorcycle->id)->exists() ? 'Remove from saved' : 'Save motorcycle' }}</button></form></div>
                @endif
            @else
                <div class="d-grid gap-2"><a class="btn btn-danger btn-lg" href="{{ route('login', ['redirect' => request()->fullUrl()]) }}">Log in to contact seller</a><a class="btn btn-outline-danger" href="{{ route('login') }}">Log in to save</a></div>
            @endauth
            @auth
                @if (auth()->id() !== $motorcycle->seller_id)
                    <details class="mt-4"><summary class="small text-muted">Report this listing</summary><form method="POST" action="{{ route('reports.store', $motorcycle) }}" class="border rounded-2 p-3 mt-2">@csrf<label class="form-label" for="reportReason">Reason</label><select class="form-select mb-2" name="reason" id="reportReason" required><option value="fraudulent">Potential fraud</option><option value="inaccurate">Inaccurate information</option><option value="unsafe">Unsafe listing</option><option value="other">Other</option></select><label class="form-label" for="reportDescription">Details (optional)</label><textarea class="form-control mb-2" id="reportDescription" name="description" rows="3" maxlength="2000"></textarea><button class="btn btn-sm btn-outline-danger" type="submit">Send report</button></form></details>
                @endif
            @endauth
        </div>
    </div>
</section>
@if ($similar->isNotEmpty())<section class="container py-4"><h2 class="h3 fw-bold mb-4">Similar motorcycles</h2><div class="row row-cols-1 row-cols-sm-2 row-cols-lg-3 g-4">@foreach ($similar as $item)<div class="col"><x-motorcycle-card :motorcycle="$item" /></div>@endforeach</div></section>@endif
@endsection