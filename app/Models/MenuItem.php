<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * A dish or package on the Kaleni Catering menu (mains, sides, platters,
 * packages, etc.). Formerly "Product" when this codebase sold flowers.
 */
class MenuItem extends Model
{
    protected $fillable = [
        'name',
        'description',
        'price',
        'unit_label',
        'serves_count',
        'image',
        'stock',
        'category',
        'is_active',
        'is_featured',
    ];

    protected $casts = [
        'price' => 'decimal:2',
        'stock' => 'integer',
        'serves_count' => 'integer',
        'is_active' => 'boolean',
        'is_featured' => 'boolean',
    ];

    public function getImageUrlAttribute(): ?string
    {
        if (!$this->image) {
            return null;
        }

        if (str_starts_with($this->image, 'site:')) {
            return asset('images/' . ltrim(substr($this->image, 5), '/'));
        }

        return asset('storage/' . $this->image);
    }

    public function hasManagedImage(): bool
    {
        return filled($this->image) && !str_starts_with($this->image, 'site:');
    }

    public function cartItems(): HasMany
    {
        return $this->hasMany(CartItem::class);
    }

    public function bookingItems(): HasMany
    {
        return $this->hasMany(BookingItem::class);
    }

    public function recommendations(): HasMany
    {
        return $this->hasMany(MenuItemRecommendation::class, 'menu_item_id');
    }

    public function recommendedMenuItems(): HasMany
    {
        return $this->hasMany(MenuItemRecommendation::class, 'recommended_menu_item_id');
    }

    public function stockMovements(): HasMany
    {
        return $this->hasMany(StockMovement::class)->latest();
    }
}
