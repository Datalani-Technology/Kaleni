<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Expense extends Model
{
    public const CATEGORIES = ['Supplies', 'Delivery', 'Marketing', 'Wages', 'Rent', 'Other'];

    protected $fillable = [
        'amount',
        'category',
        'description',
        'spent_at',
        'user_id',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'spent_at' => 'date',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
