<?php

namespace App\Http\Controllers;

use App\Models\Motorcycle;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class FavoriteController extends Controller
{
    public function index(): View
    {
        return view('buyer.favorites', [
            'motorcycles' => auth()->user()->favorites()->publiclyAvailable()->with(['category', 'seller', 'images'])->latest('favorites.created_at')->paginate(12),
        ]);
    }

    public function toggle(Motorcycle $motorcycle): RedirectResponse
    {
        abort_unless($motorcycle->status === 'approved' && $motorcycle->seller()->where('is_active', true)->exists(), 404);
        auth()->user()->favorites()->toggle($motorcycle->id);

        return back()->with('status', 'Saved motorcycles updated.');
    }
}