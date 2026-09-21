<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Carbon;

/**
 * A client's catering booking for an event/period. Formerly "Order" when
 * this codebase sold flowers for delivery.
 */
class Booking extends Model
{
    protected $fillable = [
        'booking_number',
        'customer_id',
        'customer_name',
        'customer_email',
        'customer_phone',
        'onsite_contact_name',
        'onsite_contact_phone',
        'event_address',
        'event_date',
        'serving_period',
        'start_time',
        'duration_mode',
        'duration_hours',
        'end_date',
        'guest_count',
        'event_type',
        'special_message',
        'event_notes',
        'total_amount',
        'promo_code',
        'discount_amount',
        'payment_method',
        'payment_status',
        'reminder_sent_at',
        'booking_status',
        'dpo_token',
        'whatsapp_number',
        'admin_notes',
    ];

    protected $casts = [
        'total_amount' => 'decimal:2',
        'discount_amount' => 'decimal:2',
        'event_date' => 'date',
        'end_date' => 'date',
        'duration_hours' => 'integer',
        'guest_count' => 'integer',
        'reminder_sent_at' => 'datetime',
    ];

    public function items(): HasMany
    {
        return $this->hasMany(BookingItem::class);
    }

    /**
     * The start_time column is a native TIME field, stored/returned as a
     * plain "H:i:s" string (no Eloquent datetime cast) — parsed here for
     * display so every view/email renders the same "2:00 PM" format.
     */
    public function getFormattedStartTimeAttribute(): ?string
    {
        if (!$this->start_time) {
            return null;
        }

        try {
            return Carbon::parse($this->start_time)->format('g:i A');
        } catch (\Throwable) {
            return null;
        }
    }

    /**
     * Single source of truth for "when is this booking" across the booking
     * success page, admin views, invoice, and both booking emails/WhatsApp
     * message — so a scheduling change only needs updating in one place.
     */
    public function getScheduleSummaryAttribute(): string
    {
        if (!$this->event_date) {
            return 'Date to be confirmed';
        }

        $date = $this->event_date->format('D, j M Y');
        $time = $this->formatted_start_time;

        return match ($this->duration_mode) {
            'multi_day' => $this->end_date && !$this->end_date->isSameDay($this->event_date)
                ? $date . ' to ' . $this->end_date->format('D, j M Y') . ($time ? ", from {$time} daily" : '')
                : $date . ($time ? " from {$time}" : '') . ' (multi-day event)',
            'hours' => $date
                . ($time ? " at {$time}" : '')
                . ($this->duration_hours ? ' for ' . $this->duration_hours . ' hour' . ($this->duration_hours === 1 ? '' : 's') : ''),
            default => $date . ($time ? " from {$time}" : '') . ' (full day)',
        };
    }

    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class);
    }

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($booking) {
            if (empty($booking->booking_number)) {
                $booking->booking_number = 'BKG-' . strtoupper(uniqid());
            }
        });
    }
}
