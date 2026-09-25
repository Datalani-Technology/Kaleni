<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

/**
 * Validates the details a client submits to order food directly for
 * pickup/delivery, without booking a full catered event (see
 * StoreBookingRequest for that flow).
 */
class StoreQuickOrderRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'customer_name' => 'required|string|min:2|max:255',
            'customer_email' => 'required|email|max:255',
            'customer_phone' => 'required|string|max:20',
            'fulfillment_method' => 'required|in:pickup,delivery',
            'event_address' => 'nullable|required_if:fulfillment_method,delivery|string|min:10|max:2000',
            'event_notes' => 'nullable|string|max:1000',
            'payment_method' => 'required|in:' . (config('services.dpo.company_token') ? 'dpo,whatsapp' : 'whatsapp'),
        ];
    }

    public function messages(): array
    {
        return [
            'event_address.required_if' => 'Please give us a delivery address.',
            'event_address.min' => 'Please give a full delivery address (at least 10 characters).',
        ];
    }
}
