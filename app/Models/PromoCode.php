<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;

class PromoCode extends Model
{
    protected $fillable = [
        'code',
        'description',
        'type',
        'value',
        'scope',
        'min_order_amount',
        'max_discount_amount',
        'usage_limit',
        'times_used',
        'starts_at',
        'ends_at',
        'is_active',
    ];

    protected $casts = [
        'value' => 'decimal:2',
        'min_order_amount' => 'decimal:2',
        'max_discount_amount' => 'decimal:2',
        'starts_at' => 'datetime',
        'ends_at' => 'datetime',
        'is_active' => 'boolean',
    ];

    public function products(): BelongsToMany
    {
        return $this->belongsToMany(Product::class, 'promo_code_product');
    }

    public static function findUsable(string $code): ?self
    {
        return static::where('code', strtoupper(trim($code)))->first();
    }

    public function isCurrentlyValid(): bool
    {
        if (!$this->is_active) {
            return false;
        }
        $now = Carbon::now();
        if ($this->starts_at && $now->lt($this->starts_at)) {
            return false;
        }
        if ($this->ends_at && $now->gt($this->ends_at)) {
            return false;
        }
        if ($this->usage_limit !== null && $this->times_used >= $this->usage_limit) {
            return false;
        }
        return true;
    }

    /**
     * @param  Collection  $cartItems  Items with ->product (price, id) and ->quantity
     * @return array{eligible_subtotal: float, discount: float}
     */
    public function calculateDiscount(Collection $cartItems, float $cartSubtotal): array
    {
        $eligibleSubtotal = $this->scope === 'all'
            ? $cartSubtotal
            : (float) $cartItems->sum(function ($item) {
                $productId = $item->product->id ?? $item->product_id;
                return $this->products->contains('id', $productId)
                    ? ((float) $item->product->price * $item->quantity)
                    : 0.0;
            });

        if ($eligibleSubtotal <= 0) {
            return ['eligible_subtotal' => 0.0, 'discount' => 0.0];
        }

        $discount = $this->type === 'percent'
            ? $eligibleSubtotal * ((float) $this->value / 100)
            : min((float) $this->value, $eligibleSubtotal);

        if ($this->max_discount_amount !== null) {
            $discount = min($discount, (float) $this->max_discount_amount);
        }

        return ['eligible_subtotal' => $eligibleSubtotal, 'discount' => round($discount, 2)];
    }
}
