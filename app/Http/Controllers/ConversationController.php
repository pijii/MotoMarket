<?php

namespace App\Http\Controllers;

use App\Models\Conversation;
use App\Models\Motorcycle;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ConversationController extends Controller
{
    public function index(): View
    {
        $user = auth()->user();
        $conversations = Conversation::query()->where(fn ($query) => $query
            ->where('buyer_id', $user->id)->orWhere('seller_id', $user->id))
            ->with(['motorcycle', 'buyer', 'seller', 'messages' => fn ($messages) => $messages->latest()->limit(1)])
            ->latest('updated_at')->paginate(15);

        return view('conversations.index', compact('conversations'));
    }

    public function start(Motorcycle $motorcycle): RedirectResponse
    {
        abort_unless($motorcycle->status === 'approved' && $motorcycle->seller()->where('is_active', true)->exists(), 404);
        abort_if($motorcycle->seller_id === auth()->id(), 403, 'You cannot contact yourself about your own listing.');

        $conversation = Conversation::firstOrCreate([
            'motorcycle_id' => $motorcycle->id,
            'buyer_id' => auth()->id(),
        ], ['seller_id' => $motorcycle->seller_id]);

        return redirect()->route('conversations.show', $conversation);
    }

    public function show(Conversation $conversation): View
    {
        abort_unless($conversation->includes(auth()->user()), 403);
        $conversation->messages()->where('sender_id', '!=', auth()->id())->whereNull('read_at')->update(['read_at' => now()]);
        $conversation->load(['motorcycle', 'buyer', 'seller', 'messages.sender']);

        return view('conversations.show', compact('conversation'));
    }

    public function store(Request $request, Conversation $conversation): RedirectResponse
    {
        abort_unless($conversation->includes($request->user()), 403);
        $data = $request->validate(['body' => ['required', 'string', 'max:3000']]);

        $conversation->messages()->create([
            'sender_id' => $request->user()->id,
            'body' => $data['body'],
        ]);
        $conversation->touch();

        return redirect()->route('conversations.show', $conversation)->with('status', 'Message sent.');
    }
}