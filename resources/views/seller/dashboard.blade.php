@extends('layouts.marketplace')
@section('title', 'Seller dashboard | MotoMarket')
@section('content')
<section class="container py-5">
    <div class="eyebrow">Seller workspace</div><div class="d-flex flex-wrap justify-content-between align-items-end gap-3 mb-4"><h1 class="h2 fw-bold mb-0">Welcome back, {{ auth()->user()->name }}</h1><a class="btn btn-danger" href="{{ route('seller.motorcycles.create') }}">Add motorcycle</a></div>
    <x-dashboard-nav />
    <div class="row row-cols-2 row-cols-lg-4 g-3 mb-5">
        @foreach (['Total listings' => $listingCounts->sum(), 'Pending review' => $listingCounts->get('pending', 0), 'Approved' => $listingCounts->get('approved', 0), 'Inquiries' => $inquiriesCount] as $label => $value)
            <div class="col"><div class="p-3 border rounded-2 h-100"><div class="small text-muted">{{ $label }}</div><div class="fs-3 fw-bold mt-1">{{ $value }}</div></div></div>
        @endforeach
    </div>
    <div class="d-flex justify-content-between align-items-center mb-3"><h2 class="h4 fw-bold mb-0">Recent listings</h2><a class="link-danger" href="{{ route('seller.motorcycles.index') }}">View all</a></div>
    @if ($recentListings->isEmpty())<p class="border-top border-bottom py-4 text-muted">Your listings will appear here. Start with your first motorcycle.</p>@else
        <div class="table-responsive"><table class="table align-middle"><thead><tr><th>Motorcycle</th><th>Price</th><th>Status</th><th>Listed</th></tr></thead><tbody>@foreach ($recentListings as $listing)<tr><td>{{ $listing->display_name }}</td><td>&#8369;{{ number_format((float) $listing->price) }}</td><td><span class="badge text-bg-secondary">{{ ucfirst($listing->status) }}</span></td><td>{{ $listing->created_at->format('M j, Y') }}</td></tr>@endforeach</tbody></table></div>
    @endif
</section>
@endsection