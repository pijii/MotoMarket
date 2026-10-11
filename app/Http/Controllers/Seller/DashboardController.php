<?php

namespace App\Http\Controllers\Seller;

use App\Http\Controllers\Controller;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function __invoke(): View
    {
        $seller = auth()->user();

        return view('seller.dashboard', [
            'listingCounts' => $seller->motorcycles()->selectRaw('status, count(*) as total')->groupBy('status')->pluck('total', 'status'),
            'inquiriesCount' => $seller->sellerConversations()->count(),
            'recentListings' => $seller->motorcycles()->latest()->take(5)->get(),
        ]);
    }
}