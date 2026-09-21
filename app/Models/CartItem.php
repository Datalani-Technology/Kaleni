<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * A menu item a client has added to their in-progress order, before they
 * submit a booking. "Cart" is kept as a name deliberately — it's a generic
 * UI/session pattern, not NAMSA-specific content.
 */
class CartItem extends Model
{
    protected $fillable = [
        'session_id',
        'menu_item_id',
        'quantity',
    ];

    protected $casts = [
        'quantity' => 'integer',
    ];

    public function menuItem(): BelongsTo
    {
        return $this->belongsTo(MenuItem::class);
    }
}
