<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Motorcycle;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\View\View;

class MotorcycleController extends Controller
{
    public function home(): View
    {
        $listings = Motorcycle::publiclyAvailable()->with(['category', 'seller', 'images'])
            ->latest()->take(8)->get();

        return view('welcome', [
            'featured' => $listings->take(4),
            'recent' => $listings->take(8),
            'categories' => Category::query()->orderBy('name')->get(),
        ]);
    }

    public function index(Request $request): View
    {
        $filters = $request->validate([
            'q' => ['nullable', 'string', 'max:100'],
            'category' => ['nullable', 'string', 'max:80'],
            'condition' => ['nullable', 'in:new,used'],
            'min_price' => ['nullable', 'numeric', 'min:0', 'max:999999999'],
            'max_price' => ['nullable', 'numeric', 'min:0', 'max:999999999'],
            'year' => ['nullable', 'integer', 'between:1950,'.(now()->year + 1)],
            'location' => ['nullable', 'string', 'max:120'],
            'sort' => ['nullable', 'in:newest,oldest,price_asc,price_desc'],
        ]);

        $query = Motorcycle::publiclyAvailable()->with(['category', 'seller', 'images']);
        if (! empty($filters['q'])) {
            $term = $filters['q'];
            $query->where(fn (Builder $items) => $items->where('brand', 'like', "%{$term}%")
                ->orWhere('model', 'like', "%{$term}%")
                ->orWhere('description', 'like', "%{$term}%"));
        }
        if (! empty($filters['category'])) {
            $query->whereHas('category', fn (Builder $category) => $category->where('slug', $filters['category']));
        }
        if (! empty($filters['condition'])) $query->where('condition', $filters['condition']);
        if (isset($filters['min_price'])) $query->where('price', '>=', $filters['min_price']);
        if (isset($filters['max_price'])) $query->where('price', '<=', $filters['max_price']);
        if (! empty($filters['year'])) $query->where('year', $filters['year']);
        if (! empty($filters['location'])) $query->where('location', 'like', '%'.$filters['location'].'%');

        match ($filters['sort'] ?? 'newest') {
            'oldest' => $query->oldest(),
            'price_asc' => $query->orderBy('price'),
            'price_desc' => $query->orderByDesc('price'),
            default => $query->latest(),
        };

        return view('motorcycles.index', [
            'motorcycles' => $query->paginate(12)->withQueryString(),
            'categories' => Category::query()->orderBy('name')->get(),
            'filters' => $filters,
        ]);
    }

    public function show(Motorcycle $motorcycle): View
    {
        abort_unless(
            ($motorcycle->status === 'approved' && $motorcycle->seller()->where('is_active', true)->exists())
            || (auth()->check() && (auth()->id() === $motorcycle->seller_id || auth()->user()->isAdmin())),
            404,
        );

        $motorcycle->load(['category', 'seller', 'images']);
        $similar = Motorcycle::publiclyAvailable()->with(['category', 'images'])
            ->where('category_id', $motorcycle->category_id)->whereKeyNot($motorcycle->id)
            ->latest()->take(3)->get();

        return view('motorcycles.show', compact('motorcycle', 'similar'));
    }
}