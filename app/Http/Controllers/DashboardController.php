<?php

namespace App\Http\Controllers;

use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function __invoke(): View|RedirectResponse
    {
        $user = auth()->user();

        if ($user->isAdmin()) return redirect()->route('admin.dashboard');
        if ($user->isSeller()) return redirect()->route('seller.dashboard');

        return view('buyer.dashboard', [
            'favoritesCount' => $user->favorites()->count(),
            'conversationsCount' => $user->buyerConversations()->count(),
        ]);
    }
}