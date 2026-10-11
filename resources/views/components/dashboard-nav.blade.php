<nav class="d-flex flex-wrap gap-2 mb-4" aria-label="Account navigation">
    @if (auth()->user()->isAdmin())
        <a class="btn btn-sm btn-outline-dark" href="{{ route('admin.dashboard') }}">Overview</a>
        <a class="btn btn-sm btn-outline-dark" href="{{ route('admin.motorcycles.index') }}">Listings</a>
        <a class="btn btn-sm btn-outline-dark" href="{{ route('admin.users.index') }}">Users</a>
        <a class="btn btn-sm btn-outline-dark" href="{{ route('admin.reports.index') }}">Reports</a>
    @elseif (auth()->user()->isSeller())
        <a class="btn btn-sm btn-outline-dark" href="{{ route('seller.dashboard') }}">Overview</a>
        <a class="btn btn-sm btn-outline-dark" href="{{ route('seller.motorcycles.index') }}">My listings</a>
        <a class="btn btn-sm btn-outline-dark" href="{{ route('seller.motorcycles.create') }}">Add motorcycle</a>
        <a class="btn btn-sm btn-outline-dark" href="{{ route('conversations.index') }}">Inquiries</a>
        <a class="btn btn-sm btn-outline-dark" href="{{ route('seller.profile.edit') }}">Seller profile</a>
    @else
        <a class="btn btn-sm btn-outline-dark" href="{{ route('dashboard') }}">Overview</a>
        <a class="btn btn-sm btn-outline-dark" href="{{ route('favorites.index') }}">Saved motorcycles</a>
        <a class="btn btn-sm btn-outline-dark" href="{{ route('conversations.index') }}">Conversations</a>
    @endif
</nav>