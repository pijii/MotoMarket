<?php

namespace App\Http\Controllers\Seller;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Motorcycle;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class MotorcycleController extends Controller
{
    public function index(): View
    {
        return view('seller.motorcycles.index', [
            'motorcycles' => auth()->user()->motorcycles()->with('images')->latest()->paginate(10),
        ]);
    }

    public function create(): View
    {
        return view('seller.motorcycles.form', ['motorcycle' => null, 'categories' => Category::orderBy('name')->get()]);
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $this->validatedData($request);
        $motorcycle = auth()->user()->motorcycles()->create($this->listingFields($data));
        $this->storeImages($motorcycle, $data['images'] ?? []);

        return redirect()->route('seller.motorcycles.index')->with('status', 'Your motorcycle was submitted for review.');
    }

    public function edit(Motorcycle $motorcycle): View
    {
        $this->authorize('update', $motorcycle);
        abort_if($motorcycle->status === 'sold' || $motorcycle->status === 'removed', 403);

        return view('seller.motorcycles.form', [
            'motorcycle' => $motorcycle->load('images'),
            'categories' => Category::orderBy('name')->get(),
        ]);
    }

    public function update(Request $request, Motorcycle $motorcycle): RedirectResponse
    {
        $this->authorize('update', $motorcycle);
        abort_if($motorcycle->status === 'sold' || $motorcycle->status === 'removed', 403);
        $data = $this->validatedData($request, $motorcycle);
        $motorcycle->update($this->listingFields($data) + ['status' => 'pending', 'rejection_reason' => null]);
        $this->storeImages($motorcycle, $data['images'] ?? []);

        return redirect()->route('seller.motorcycles.index')->with('status', 'Your changes were submitted for review.');
    }

    public function destroy(Motorcycle $motorcycle): RedirectResponse
    {
        $this->authorize('delete', $motorcycle);
        abort_if($motorcycle->status === 'sold', 403);
        $motorcycle->update(['status' => 'removed']);

        return back()->with('status', 'The listing was removed from the marketplace.');
    }

    public function markSold(Motorcycle $motorcycle): RedirectResponse
    {
        $this->authorize('update', $motorcycle);
        abort_unless($motorcycle->status === 'approved', 403);
        $motorcycle->update(['status' => 'sold']);

        return back()->with('status', 'The motorcycle has been marked as sold.');
    }

    private function validatedData(Request $request, ?Motorcycle $motorcycle = null): array
    {
        $data = $request->validate([
            'category_id' => ['required', 'exists:categories,id'],
            'brand' => ['required', 'string', 'max:80'],
            'model' => ['required', 'string', 'max:120'],
            'year' => ['required', 'integer', 'between:1950,'.(now()->year + 1)],
            'condition' => ['required', 'in:new,used'],
            'price' => ['required', 'numeric', 'min:1', 'max:9999999999.99'],
            'mileage' => ['nullable', 'integer', 'min:0', 'max:2000000'],
            'location' => ['required', 'string', 'max:255'],
            'description' => ['required', 'string', 'min:30', 'max:10000'],
            'images' => ['nullable', 'array', 'max:6'],
            'images.*' => ['image', 'mimes:jpg,jpeg,png,webp', 'max:5120', 'dimensions:min_width=300,min_height=200'],
        ]);

        if ($motorcycle && $motorcycle->images()->count() + count($data['images'] ?? []) > 6) {
            throw ValidationException::withMessages(['images' => 'A listing can have up to 6 photos.']);
        }

        return $data;
    }

    private function listingFields(array $data): array
    {
        return [
            'category_id' => $data['category_id'],
            'brand' => $data['brand'],
            'model' => $data['model'],
            'year' => $data['year'],
            'condition' => $data['condition'],
            'price' => $data['price'],
            'mileage' => $data['mileage'] ?? null,
            'location' => $data['location'],
            'description' => $data['description'],
        ];
    }

    private function storeImages(Motorcycle $motorcycle, array $images): void
    {
        foreach ($images as $image) {
            $motorcycle->images()->create([
                'image_path' => $image->store('motorcycles', 'public'),
                'is_primary' => ! $motorcycle->images()->exists(),
                'sort_order' => $motorcycle->images()->count(),
            ]);
        }
    }
}