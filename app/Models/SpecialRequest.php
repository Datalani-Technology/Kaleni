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

    /**
     * Which public form the client used. The two forms ask for slightly
     * different things (a bespoke menu item vs. a whole event) and get
     * separate admin boards — Admin\SpecialRequestController for one,
     * Admin\QuoteController for the other — even though both are stored
     * here, since the pricing workflow (status, quoted_amount, sending a
     * quote) is identical either way.
     */
    public const SOURCE_SPECIAL_REQUEST_PAGE = 'special_request_page';
    public const SOURCE_HOME_QUOTE_FORM = 'home_quote_form';

    protected $fillable = [
        'name',
        'email',
        'phone',
        'event_date',
        'is_recurring',
        'recurring_end_date',
        'guest_count',
        'occasion',
        'details',
        'budget_range',
        'source',
        'status',
        'admin_notes',
        'quoted_amount',
        'responded_at',
        'quote_sent_at',
    ];

    protected $casts = [
        'event_date' => 'date',
        'is_recurring' => 'boolean',
        'recurring_end_date' => 'date',
        'guest_count' => 'integer',
        'quoted_amount' => 'decimal:2',
        'responded_at' => 'datetime',
        'quote_sent_at' => 'datetime',
    ];
}
