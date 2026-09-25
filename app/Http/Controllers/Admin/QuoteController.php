<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\SpecialRequest;
use Illuminate\Http\Request;

/**
 * Event-level quote requests submitted through the home page's "Get a
 * quote" form ("Tell us about your event"). Bespoke item/menu requests
 * from the dedicated "Request a special item" page live on their own
 * board — see Admin\SpecialRequestController — even though both share
 * this same model and the same pricing workflow (status, quoted_amount,
 * sending a quote all go through Admin\SpecialRequestController still,
 * since that's the same record either way).
 */
class QuoteController extends Controller
{
    public function index(Request $request)
    {
        $query = SpecialRequest::where('source', SpecialRequest::SOURCE_HOME_QUOTE_FORM)->latest();

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->get('stage') === 'pending') {
            $query->whereNull('quote_sent_at');
        } elseif ($request->get('stage') === 'sent') {
            $query->whereNotNull('quote_sent_at');
        }

        $quotes = $query->paginate(20)->withQueryString();

        return view('admin.quotes.index', compact('quotes'));
    }
}
