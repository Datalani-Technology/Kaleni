<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreSpecialRequestRequest;
use App\Mail\NewSpecialRequestAlertMail;
use App\Mail\SpecialRequestReceivedMail;
use App\Models\SpecialRequest;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class SpecialRequestController extends Controller
{
    public function create()
    {
        return view('special-requests.create');
    }

    public function store(StoreSpecialRequestRequest $request)
    {
        $validated = $request->validated();

        $validated['name'] = strip_tags($validated['name']);
        $validated['phone'] = strip_tags($validated['phone']);
        $validated['details'] = strip_tags($validated['details']);
        $validated['occasion'] = isset($validated['occasion']) ? strip_tags($validated['occasion']) : null;
        $validated['budget_range'] = isset($validated['budget_range']) ? strip_tags($validated['budget_range']) : null;

        $specialRequest = SpecialRequest::create($validated);

        try {
            Mail::to(config('contact.email_orders'))->send(new NewSpecialRequestAlertMail($specialRequest));
        } catch (\Throwable $e) {
            Log::warning('New special request alert email failed', ['id' => $specialRequest->id, 'error' => $e->getMessage()]);
        }

        try {
            Mail::to($specialRequest->email)->send(new SpecialRequestReceivedMail($specialRequest));
        } catch (\Throwable $e) {
            Log::warning('Special request confirmation email failed', ['id' => $specialRequest->id, 'error' => $e->getMessage()]);
        }

        return redirect()->route('special-requests.create')
            ->with('success', "Thanks, we've received your request and will follow up with a quote by email or phone shortly.");
    }
}
