@extends('layouts.marketplace')

@section('title', 'Create account | MotoMarket')

@section('content')
<section class="container py-5" style="max-width:620px">
    <div class="eyebrow">Join MotoMarket</div><h1 class="h2 fw-bold mb-2">Create your account</h1><p class="text-muted mb-4">Find your next motorcycle or start selling to riders across the Philippines.</p>
    <form method="POST" action="{{ route('register') }}" class="border rounded-2 p-4 p-md-5">
        @csrf
        <div class="mb-3"><label class="form-label" for="name">Full name</label><input class="form-control" id="name" name="name" value="{{ old('name') }}" required autofocus autocomplete="name">@error('name')<div class="text-danger small mt-1">{{ $message }}</div>@enderror</div>
        <div class="mb-3"><label class="form-label" for="email">Email address</label><input class="form-control" id="email" type="email" name="email" value="{{ old('email') }}" required autocomplete="username">@error('email')<div class="text-danger small mt-1">{{ $message }}</div>@enderror</div>
        <div class="mb-3"><label class="form-label" for="role">I want to</label><select class="form-select" name="role" id="role"><option value="buyer" @selected(old('role', 'buyer') === 'buyer')>Buy a motorcycle</option><option value="seller" @selected(old('role') === 'seller')>Sell a motorcycle</option></select></div>
        <div class="mb-3"><label class="form-label" for="password">Password</label><input class="form-control" id="password" type="password" name="password" required autocomplete="new-password">@error('password')<div class="text-danger small mt-1">{{ $message }}</div>@enderror</div>
        <div class="mb-4"><label class="form-label" for="password_confirmation">Confirm password</label><input class="form-control" id="password_confirmation" type="password" name="password_confirmation" required autocomplete="new-password"></div>
        <button class="btn btn-danger w-100" type="submit">Create account</button>
        <p class="text-muted small text-center mt-3 mb-0">Already registered? <a class="link-danger" href="{{ route('login') }}">Log in</a></p>
    </form>
</section>
@endsection