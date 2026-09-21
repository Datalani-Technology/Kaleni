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
    public function index(Request $request)
    {
        $query = SpecialRequest::query()->latest();

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

        return redirect()->route('admin.special-requests.show', $specialRequest)->with('success', 'Special request updated.');
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
}
