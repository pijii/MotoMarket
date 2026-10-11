<?php

namespace App\Http\Controllers;

use App\Models\Motorcycle;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class ReportController extends Controller
{
    public function store(Request $request, Motorcycle $motorcycle): RedirectResponse
    {
        abort_unless($motorcycle->status === 'approved', 404);
        abort_if($motorcycle->seller_id === $request->user()->id, 403);
        $data = $request->validate([
            'reason' => ['required', 'in:fraudulent,inaccurate,unsafe,other'],
            'description' => ['nullable', 'string', 'max:2000'],
        ]);
        $request->user()->reportedListings()->create([
            'motorcycle_id' => $motorcycle->id,
            'reported_user_id' => $motorcycle->seller_id,
            'reason' => $data['reason'],
            'description' => $data['description'] ?? null,
        ]);

        return back()->with('status', 'Thank you. Your report has been sent to the moderation team.');
    }
}