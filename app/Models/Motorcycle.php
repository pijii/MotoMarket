<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Motorcycle extends Model
{
    use HasFactory;

    protected $fillable = [
        'seller_id', 'category_id', 'brand', 'model', 'year', 'price', 'condition',
        'mileage', 'description', 'location', 'status', 'rejection_reason',
    ];

    protected function casts(): array
    {
        return ['price' => 'decimal:2', 'year' => 'integer', 'mileage' => 'integer'];
    }

    public function seller(): BelongsTo
    {
        return $this->belongsTo(User::class, 'seller_id');
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    public function images(): HasMany
    {
        return $this->hasMany(MotorcycleImage::class)->orderByDesc('is_primary')->orderBy('sort_order');
    }

    public function primaryImage(): HasMany
    {
        return $this->hasMany(MotorcycleImage::class)->where('is_primary', true);
    }

    public function conversations(): HasMany
    {
        return $this->hasMany(Conversation::class);
    }

    public function scopePubliclyAvailable(Builder $query): Builder
    {
        return $query->where('status', 'approved')->whereHas('seller', fn (Builder $seller) => $seller->where('is_active', true));
    }

    public function getDisplayNameAttribute(): string
    {
        return trim("{$this->year} {$this->brand} {$this->model}");
    }
}