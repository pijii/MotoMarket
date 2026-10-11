@props(['motorcycle'])

<article class="motorcycle-card">
    <a class="text-decoration-none" href="{{ route('motorcycles.show', $motorcycle) }}" aria-label="View {{ $motorcycle->display_name }}">
        @if ($motorcycle->images->first())
            <img class="motorcycle-card-image" src="{{ \Illuminate\Support\Facades\Storage::url($motorcycle->images->first()->image_path) }}" alt="{{ $motorcycle->display_name }}" loading="lazy" onerror="this.hidden=true;this.nextElementSibling.hidden=false">
            <div class="motorcycle-placeholder" hidden><span>Photo unavailable</span></div>
        @else
            <div class="motorcycle-placeholder"><span>No photo available</span></div>
        @endif
        <div class="p-3 p-lg-4">
            <div class="small text-muted">{{ $motorcycle->brand }} &middot; {{ $motorcycle->year }}</div>
            <h3 class="h5 fw-bold mt-1 mb-2">{{ $motorcycle->model }}</h3>
            <div class="price fs-5">&#8369;{{ number_format((float) $motorcycle->price, 0) }}</div>
            <div class="d-flex justify-content-between align-items-center mt-3 small text-muted"><span>{{ ucfirst($motorcycle->condition) }}</span><span>{{ $motorcycle->location }}</span></div>
        </div>
    </a>
</article>