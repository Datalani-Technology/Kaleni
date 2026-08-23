<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Order extends Model
{
    protected $fillable = [
        'order_number',
        'customer_id',
        'customer_name',
        'customer_email',
        'customer_phone',
        'recipient_name',
        'recipient_phone',
        'delivery_address',
        'delivery_date',
        'delivery_window',
        'gift_message',
        'delivery_instructions',
        'total_amount',
        'promo_code',
        'discount_amount',
        'payment_method',
        'payment_status',
        'reminder_sent_at',
        'order_status',
        'dpo_token',
        'whatsapp_number',
        'admin_notes',
    ];

    protected $casts = [
        'total_amount' => 'decimal:2',
        'discount_amount' => 'decimal:2',
        'delivery_date' => 'date',
        'reminder_sent_at' => 'datetime',
    ];

    public function items(): HasMany
    {
        return $this->hasMany(OrderItem::class);
    }

    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class);
    }

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($order) {
            if (empty($order->order_number)) {
                $order->order_number = 'ORD-' . strtoupper(uniqid());
            }
        });
    }
}
