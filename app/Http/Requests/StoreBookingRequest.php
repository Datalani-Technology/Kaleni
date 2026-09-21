<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

/**
 * Validates the details a client submits to turn their in-progress order
 * into a catering booking for an event/period.
 */
class StoreBookingRequest extends FormRequest
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
            'onsite_contact_name' => 'nullable|string|min:2|max:255',
            'onsite_contact_phone' => 'nullable|string|max:20',
            'event_address' => 'required|string|min:10|max:2000',
            'event_date' => 'required|date|after_or_equal:today|before_or_equal:' . now()->addDays(90)->toDateString(),
            'serving_period' => 'required|in:breakfast,lunch,dinner,full_day,custom',
            'start_time' => ['nullable', 'date_format:H:i', 'required_if:duration_mode,hours'],
            'duration_mode' => 'required|in:hours,full_day,multi_day',
            'duration_hours' => 'nullable|integer|min:1|max:24|required_if:duration_mode,hours',
            'end_date' => [
                'nullable',
                'date',
                'required_if:duration_mode,multi_day',
                'after_or_equal:event_date',
                function ($attribute, $value, $fail) {
                    if (!$value || !$this->filled('event_date')) {
                        return;
                    }
                    $start = \Illuminate\Support\Carbon::parse($this->input('event_date'));
                    $end = \Illuminate\Support\Carbon::parse($value);
                    if ($start->diffInDays($end) > 30) {
                        $fail('Multi-day bookings can span at most 30 days. For longer engagements, please contact us directly.');
                    }
                },
            ],
            'guest_count' => 'required|integer|min:1|max:5000',
            'event_type' => 'required|in:Wedding,Corporate,Birthday,Baby Shower,Funeral,Other',
            'special_message' => 'nullable|string|max:500',
            'event_notes' => 'nullable|string|max:1000',
            'payment_method' => 'required|in:' . (config('services.dpo.company_token') ? 'dpo,whatsapp' : 'whatsapp'),
        ];
    }

    public function messages(): array
    {
        return [
            'event_date.after_or_equal' => 'The event date must be today or later.',
            'guest_count.required' => 'Please tell us how many guests you are catering for.',
            'event_address.min' => 'Please give a full venue/delivery address (at least 10 characters).',
            'start_time.required_if' => 'Please tell us what time to start.',
            'duration_hours.required_if' => 'Please tell us how many hours you need.',
            'end_date.required_if' => 'Please tell us the last day of the event.',
            'end_date.after_or_equal' => 'The end date must be on or after the event date.',
        ];
    }
}
