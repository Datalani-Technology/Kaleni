<?php

namespace App\Http\Requests;

use App\Models\SpecialRequest;
use Illuminate\Foundation\Http\FormRequest;

/**
 * Validates a client's ask for a bespoke item/menu that isn't on the
 * standing Kaleni menu.
 */
class StoreSpecialRequestRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => 'required|string|min:2|max:255',
            'email' => 'required|email|max:255',
            'phone' => 'required|string|max:20',
            'event_date' => 'required|date|after_or_equal:today',
            'is_recurring' => 'nullable|boolean',
            'recurring_end_date' => [
                'nullable',
                'date',
                'required_if:is_recurring,1',
                'after:event_date',
                function ($attribute, $value, $fail) {
                    if (!$value || !$this->filled('event_date')) {
                        return;
                    }
                    $start = \Illuminate\Support\Carbon::parse($this->input('event_date'));
                    $end = \Illuminate\Support\Carbon::parse($value);
                    if ($start->diffInDays($end) > 90) {
                        $fail('Recurring requests can span at most 90 days. For a longer arrangement, please contact us directly.');
                    }
                },
            ],
            'guest_count' => 'required|integer|min:1|max:5000',
            'occasion' => 'required|string|max:255',
            'details' => 'required|string|min:10|max:2000',
            'budget_range' => 'required|string|max:100',
            'source' => 'nullable|in:' . SpecialRequest::SOURCE_SPECIAL_REQUEST_PAGE . ',' . SpecialRequest::SOURCE_HOME_QUOTE_FORM,
        ];
    }

    public function messages(): array
    {
        return [
            'details.required' => 'Please describe the special item or menu you have in mind.',
            'details.min' => 'A few more details will help us quote you accurately (at least 10 characters).',
            'event_date.required' => 'Please tell us the date you need this.',
            'recurring_end_date.required_if' => 'Please tell us the last day you need this.',
            'recurring_end_date.after' => 'The last day must be after the start date.',
            'guest_count.required' => 'Please tell us how many guests you are catering for.',
            'occasion.required' => 'Please tell us the occasion.',
            'budget_range.required' => 'Please tell us your budget range.',
        ];
    }
}
