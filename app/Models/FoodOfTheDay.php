<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Carbon;

/**
 * The dish spotlighted for a given calendar date. Either points at a
 * standing MenuItem (and inherits its details) or carries its own
 * one-off title/description/price/image for a dish that isn't on the
 * regular menu at all.
 */
class FoodOfTheDay extends Model
{
    protected $table = 'food_of_the_day';

    protected $fillable = [
        'menu_item_id',
        'serve_date',
        'title',
        'description',
        'image',
        'price',
        'is_active',
    ];

    protected $casts = [
        'serve_date' => 'date',
        'price' => 'decimal:2',
        'is_active' => 'boolean',
    ];

    public function menuItem(): BelongsTo
    {
        return $this->belongsTo(MenuItem::class);
    }

    public function getEffectiveTitleAttribute(): ?string
    {
        return $this->title ?: $this->menuItem?->name;
    }

    public function getEffectiveDescriptionAttribute(): ?string
    {
        return $this->description ?: $this->menuItem?->description;
    }

    public function getEffectivePriceAttribute(): ?string
    {
        return $this->price !== null ? $this->price : $this->menuItem?->price;
    }

    public function getEffectiveImageUrlAttribute(): ?string
    {
        if ($this->image) {
            return str_starts_with($this->image, 'site:')
                ? asset('images/' . ltrim(substr($this->image, 5), '/'))
                : asset('storage/' . $this->image);
        }

        return $this->menuItem?->image_url;
    }

    public static function forToday(): ?self
    {
        // whereDate() (not a plain where()) because the 'date' cast still
        // persists a full "Y-m-d H:i:s" string — MySQL's DATE column quietly
        // truncates that on write, but SQLite stores it verbatim, so an
        // exact-string match against a bare 'Y-m-d' silently finds nothing
        // there.
        return static::query()
            ->with('menuItem')
            ->whereDate('serve_date', Carbon::today())
            ->where('is_active', true)
            ->first();
    }
}
