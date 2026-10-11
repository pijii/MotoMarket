<?php

namespace App\Http\Controllers\Seller;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class SellerProfileController extends Controller
{
    public function registerForm(): View
    {
        return view('seller.register');
    }

    public function register(Request $request): RedirectResponse
    {
        $request->user()->forceFill(['role' => 'seller'])->save();

        return redirect()->route('seller.dashboard')->with('status', 'Your seller account is ready.');
    }

    public function edit(): View
    {
        return view('seller.profile', ['user' => auth()->user()]);
    }

    public function update(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'phone' => ['nullable', 'string', 'max:30'],
            'location' => ['nullable', 'string', 'max:255'],
        ]);
        $request->user()->update($data);

        return back()->with('status', 'Seller profile updated.');
    }
}