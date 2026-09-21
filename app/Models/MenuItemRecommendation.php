<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class MenuItemRecommendation extends Model
{
    protected $fillable = [
        'menu_item_id',
        'recommended_menu_item_id',
        'score',
    ];

    protected $casts = [
        'score' => 'integer',
    ];

    public function menuItem(): BelongsTo
    {
        return $this->belongsTo(MenuItem::class, 'menu_item_id');
    }

    public function recommendedMenuItem(): BelongsTo
    {
        return $this->belongsTo(MenuItem::class, 'recommended_menu_item_id');
    }
}
