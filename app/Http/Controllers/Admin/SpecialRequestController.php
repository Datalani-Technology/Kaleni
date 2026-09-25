<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Mail\SpecialRequestStatusUpdatedMail;
use App\Models\SpecialRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class SpecialRequestController extends Controller
{
    /**
     * Bespoke item/menu requests submitted through the dedicated
     * "Request a special item" page. Event-level quote requests from the
     * home page's "Get a quote" form live on their own board — see
     * Admin\QuoteController — even though both share this same model and
     * the same pricing workflow below.
     */
    public function index(Request $request)
    {
        $query = SpecialRequest::where('source', SpecialRequest::SOURCE_SPECIAL_REQUEST_PAGE)->latest();

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $specialRequests = $query->paginate(20)->withQueryString();

        return view('admin.special-requests.index', compact('specialRequests'));
    }

    public function show(SpecialRequest $specialRequest)
    {
        return view('admin.special-requests.show', compact('specialRequest'));
    }

    public function quote(SpecialRequest $specialRequest)
    {
        return view('admin.special-requests.quote', compact('specialRequest'));
    }

    public function update(Request $request, SpecialRequest $specialRequest)
    {
        $validated = $request->validate([
            'status' => 'required|in:' . implode(',', SpecialRequest::STATUSES),
            'admin_notes' => 'nullable|string|max:2000',
            'quoted_amount' => 'nullable|numeric|min:0',
            'notify_client' => 'nullable|boolean',
        ]);

        $statusChanged = $validated['status'] !== $specialRequest->status;

        $specialRequest->update([
            'status' => $validated['status'],
            'admin_notes' => $validated['admin_notes'] ?? null,
            'quoted_amount' => $validated['quoted_amount'] ?? null,
            'responded_at' => $statusChanged && in_array($validated['status'], ['quoted', 'accepted', 'declined'], true)
                ? now()
                : $specialRequest->responded_at,
        ]);

        if ($statusChanged && $request->boolean('notify_client') && in_array($validated['status'], ['quoted', 'accepted', 'declined'], true)) {
            try {
                Mail::to($specialRequest->email)->send(new SpecialRequestStatusUpdatedMail($specialRequest));
            } catch (\Throwable $e) {
                Log::warning('Special request status email failed', ['id' => $specialRequest->id, 'error' => $e->getMessage()]);
            }
        }

        $isQuote = $specialRequest->source === SpecialRequest::SOURCE_HOME_QUOTE_FORM;

        return redirect()->route('admin.special-requests.show', $specialRequest)
            ->with('success', $isQuote ? 'Quote updated.' : 'Special request updated.');
    }

    /**
     * Emails the client the quoted amount and marks the request as quoted.
     * Separate from update() so sending a quote is always an explicit,
     * unambiguous action rather than a side effect of a generic status save.
     */
    public function sendQuote(Request $request, SpecialRequest $specialRequest)
    {
        $validated = $request->validate([
            'admin_notes' => 'nullable|string|max:2000',
            'quoted_amount' => 'required|numeric|min:0',
        ], [
            'quoted_amount.required' => 'Please enter a quoted amount before sending a quote to the client.',
        ]);

        $specialRequest->update([
            'status' => 'quoted',
            'admin_notes' => $validated['admin_notes'] ?? null,
            'quoted_amount' => $validated['quoted_amount'],
            'responded_at' => now(),
            'quote_sent_at' => now(),
        ]);

        try {
            Mail::to($specialRequest->email)->send(new SpecialRequestStatusUpdatedMail($specialRequest));
        } catch (\Throwable $e) {
            Log::warning('Special request quote email failed', ['id' => $specialRequest->id, 'error' => $e->getMessage()]);

            return redirect()->route('admin.special-requests.show', $specialRequest)
                ->with('error', 'The quote was saved, but the email to the client failed to send. Please contact them directly.');
        }

        return redirect()->route('admin.special-requests.show', $specialRequest)
            ->with('success', 'Quote sent to ' . $specialRequest->email . '.');
    }

    /**
     * Same state changes as sendQuote(), but hands the client their quote
     * over WhatsApp instead of email — useful when email isn't the client's
     * preferred channel. The message is built from the same details the
     * email version sends, since there's no public quote page to link to.
     *
     * Always returns JSON: the button opens a blank tab synchronously (so it
     * survives the browser's popup-gesture check) and this endpoint fills in
     * that tab's location once it knows the wa.me URL. It's called via fetch
     * rather than a normal form submission for the same reason the redirect
     * itself can't point straight at wa.me — the site's CSP restricts form
     * submissions to same-origin destinations.
     */
    public function sendQuoteWhatsapp(Request $request, SpecialRequest $specialRequest)
    {
        $validated = $request->validate([
            'admin_notes' => 'nullable|string|max:2000',
            'quoted_amount' => 'required|numeric|min:0',
        ], [
            'quoted_amount.required' => 'Please enter a quoted amount before sending a quote to the client.',
        ]);

        $specialRequest->update([
            'status' => 'quoted',
            'admin_notes' => $validated['admin_notes'] ?? null,
            'quoted_amount' => $validated['quoted_amount'],
            'responded_at' => now(),
            'quote_sent_at' => now(),
        ]);

        $reference = 'SR-' . str_pad((string) $specialRequest->id, 5, '0', STR_PAD_LEFT);
        $message = "Hi {$specialRequest->name}, here's your Kaleni Catering quote for {$reference}:\n\n";
        if ($specialRequest->occasion) {
            $message .= "Occasion: {$specialRequest->occasion}\n";
        }
        if ($specialRequest->event_date) {
            $dateLine = $specialRequest->event_date->format('D, j M Y');
            if ($specialRequest->is_recurring && $specialRequest->recurring_end_date) {
                $dateLine .= ' to ' . $specialRequest->recurring_end_date->format('D, j M Y') . ' (recurring)';
            }
            $message .= ($specialRequest->is_recurring ? 'Dates: ' : 'Event date: ') . $dateLine . "\n";
        }
        if ($specialRequest->guest_count) {
            $message .= "Guests: {$specialRequest->guest_count}\n";
        }
        $message .= "What you asked for: {$specialRequest->details}\n\n";
        $message .= 'Quoted amount: N$ ' . number_format((float) $specialRequest->quoted_amount, 2) . "\n\n";
        $message .= 'This quote is an estimate and may be adjusted once final details are confirmed.';

        $phone = preg_replace('/[^0-9]/', '', $specialRequest->phone);
        $waUrl = 'https://wa.me/' . $phone . '?text=' . urlencode($message);

        return response()->json(['wa_url' => $waUrl]);
    }

    /**
     * Permanently removes a special request/quote — for clearing out test
     * or mistaken entries. No stock or payment is tied to these records, so
     * unlike a booking/order deletion there's nothing else to reverse.
     */
    public function destroy(SpecialRequest $specialRequest)
    {
        $isQuote = $specialRequest->source === SpecialRequest::SOURCE_HOME_QUOTE_FORM;
        $name = $specialRequest->name;

        $specialRequest->delete();

        return redirect()->route($isQuote ? 'admin.quotes.index' : 'admin.special-requests.index')
            ->with('success', ($isQuote ? 'Quote from ' : 'Special request from ') . $name . ' deleted.');
    }
}
