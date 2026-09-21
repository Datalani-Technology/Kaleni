<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * A bespoke item/event request from a client that doesn't fit the standing
 * menu or booking flow — staff follow up manually with a quote.
 */
class SpecialRequest extends Model
{
    public const STATUSES = ['new', 'in_review', 'quoted', 'accepted', 'declined', 'cancelled'];

    protected $fillable = [
        'name',
        'email',
        'phone',
        'event_date',
        'guest_count',
        'occasion',
        'details',
        'budget_range',
        'status',
        'admin_notes',
        'quoted_amount',
        'responded_at',
        'quote_sent_at',
    ];

    protected $casts = [
        'event_date' => 'date',
        'guest_count' => 'integer',
        'quoted_amount' => 'decimal:2',
        'responded_at' => 'datetime',
        'quote_sent_at' => 'datetime',
    ];
}
