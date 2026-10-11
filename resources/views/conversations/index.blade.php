@extends('layouts.marketplace')
@section('title', 'Conversations | MotoMarket')
@section('content')
<section class="container py-5"><div class="eyebrow">Your account</div><h1 class="h2 fw-bold mb-3">Conversations</h1><x-dashboard-nav />
    @if ($conversations->isEmpty())<div class="border-top border-bottom py-5 text-center"><h2 class="h5 fw-bold">No conversations yet</h2><p class="text-muted">When you contact a seller or receive an inquiry, it will appear here.</p><a class="btn btn-outline-danger" href="{{ route('motorcycles.index') }}">Browse motorcycles</a></div>@else
        <div class="list-group">@foreach ($conversations as $conversation)@php($other = auth()->id() === $conversation->buyer_id ? $conversation->seller : $conversation->buyer) <a class="list-group-item list-group-item-action p-3" href="{{ route('conversations.show', $conversation) }}"><div class="d-flex justify-content-between gap-3"><div><div class="fw-bold">{{ $conversation->motorcycle->display_name }}</div><div class="small text-muted">{{ $other->name }}@if ($conversation->messages->first()) &middot; {{ \Illuminate\Support\Str::limit($conversation->messages->first()->body, 100) }}@endif</div></div><span class="small text-muted text-nowrap">{{ $conversation->updated_at->diffForHumans() }}</span></div></a>@endforeach</div><div class="mt-4">{{ $conversations->links() }}</div>
    @endif
</section>
@endsection