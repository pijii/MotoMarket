@extends('layouts.marketplace')
@section('title', 'Admin dashboard | MotoMarket')
@section('content')
<section class="container py-5"><div class="eyebrow">Administration</div><h1 class="h2 fw-bold mb-3">Marketplace overview</h1><x-dashboard-nav />
    <div class="row row-cols-2 row-cols-lg-4 g-3 mb-5">
        @foreach (['Total users' => $usersCount, 'Sellers' => $sellersCount, 'Buyers' => $buyersCount, 'All listings' => $listingCounts->sum(), 'Pending review' => $listingCounts->get('pending', 0), 'Approved' => $listingCounts->get('approved', 0), 'Sold' => $listingCounts->get('sold', 0), 'Reports pending' => $pendingReports] as $label => $value)
            <div class="col"><div class="border rounded-2 p-3 h-100"><div class="small text-muted">{{ $label }}</div><div class="fs-3 fw-bold">{{ $value }}</div></div></div>
        @endforeach
    </div>
    <div class="d-flex justify-content-between align-items-center mb-3"><h2 class="h4 fw-bold mb-0">Recent marketplace activity</h2><a class="link-danger" href="{{ route('admin.motorcycles.index') }}">Review listings</a></div>
    <div class="table-responsive"><table class="table align-middle"><thead><tr><th>Motorcycle</th><th>Seller</th><th>Status</th><th>Submitted</th></tr></thead><tbody>@forelse ($recentListings as $listing)<tr><td>{{ $listing->display_name }}</td><td>{{ $listing->seller->name }}</td><td>{{ ucfirst($listing->status) }}</td><td>{{ $listing->created_at->format('M j, Y') }}</td></tr>@empty<tr><td colspan="4" class="text-center text-muted py-4">No marketplace activity yet.</td></tr>@endforelse</tbody></table></div>
</section>
@endsection