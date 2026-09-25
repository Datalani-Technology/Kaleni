@extends('admin.layout')

@php
    $isQuoteSource = $specialRequest->source === \App\Models\SpecialRequest::SOURCE_HOME_QUOTE_FORM;
@endphp
@section('title', ($isQuoteSource ? 'Quote Request from ' : 'Special Request from ') . $specialRequest->name)
@section('sidebar_active', $isQuoteSource ? 'quotes' : 'special-requests')

@section('content')
<div class="mb-4">
    <h1 class="mb-3">{{ $isQuoteSource ? 'Quote Request from' : 'Special Request from' }} {{ $specialRequest->name }}</h1>
    <div class="d-flex flex-wrap gap-2 admin-page-actions">
        @if($isQuoteSource)
            <a href="{{ route('admin.quotes.index') }}" class="btn btn-outline-secondary"><i class="bi bi-arrow-left"></i> All Quotes</a>
        @else
            <a href="{{ route('admin.special-requests.index') }}" class="btn btn-outline-secondary"><i class="bi bi-arrow-left"></i> All Special Requests</a>
        @endif
        @php $wa = 'https://wa.me/' . preg_replace('/[^0-9]/', '', $specialRequest->phone); @endphp
        <a href="{{ $wa }}" target="_blank" rel="noopener" class="btn btn-success"><i class="bi bi-whatsapp"></i> WhatsApp</a>
        <a href="tel:{{ $specialRequest->phone }}" class="btn btn-outline-primary"><i class="bi bi-telephone"></i> Call</a>
        <a href="{{ route('admin.special-requests.quote', $specialRequest) }}" target="_blank" class="btn btn-outline-primary"><i class="bi bi-file-earmark-text"></i> View Quote</a>
        <form action="{{ route('admin.special-requests.destroy', $specialRequest) }}" method="POST" class="d-inline" data-confirm-message="This permanently deletes this {{ $isQuoteSource ? 'quote' : 'special request' }} from {{ $specialRequest->name }}. This cannot be undone." data-confirm-label="Delete {{ $isQuoteSource ? 'quote' : 'special request' }}">
            @csrf
            @method('DELETE')
            <button type="submit" class="btn btn-outline-danger"><i class="bi bi-trash"></i> Delete {{ $isQuoteSource ? 'Quote' : 'Special Request' }}</button>
        </form>
    </div>
</div>

<div class="row g-4">
    <div class="col-lg-8">
        <div class="card mb-4">
            <div class="card-header bg-light fw-semibold">Request details</div>
            <div class="card-body">
                <p class="mb-2"><strong>Name:</strong> {{ $specialRequest->name }}</p>
                <p class="mb-2"><strong>Email:</strong> <a href="mailto:{{ $specialRequest->email }}">{{ $specialRequest->email }}</a></p>
                <p class="mb-2"><strong>Phone:</strong> <a href="tel:{{ $specialRequest->phone }}">{{ $specialRequest->phone }}</a></p>
                <p class="mb-2">
                    <strong>{{ $specialRequest->is_recurring ? 'Dates needed:' : 'Date needed:' }}</strong>
                    {{ optional($specialRequest->event_date)->format('D, j M Y') ?: 'Not specified' }}
                    @if($specialRequest->is_recurring && $specialRequest->recurring_end_date)
                        &ndash; {{ $specialRequest->recurring_end_date->format('D, j M Y') }}
                        <span class="badge text-bg-secondary">Recurring</span>
                    @endif
                </p>
                <p class="mb-2"><strong>Guests:</strong> {{ $specialRequest->guest_count ?: 'Not specified' }}</p>
                <p class="mb-2"><strong>Occasion:</strong> {{ $specialRequest->occasion ?: 'Not specified' }}</p>
                <p class="mb-2"><strong>Budget range:</strong> {{ $specialRequest->budget_range ?: 'Not specified' }}</p>
                <hr>
                <p class="mb-1"><strong>What they asked for:</strong></p>
                <p class="mb-0">{{ $specialRequest->details }}</p>
            </div>
        </div>
    </div>

    <div class="col-lg-4">
        <div class="card mb-4">
            <div class="card-header bg-light fw-semibold">Update request</div>
            <div class="card-body">
                <form action="{{ route('admin.special-requests.update', $specialRequest) }}" method="POST" id="specialRequestForm">
                    @csrf
                    <div class="mb-3">
                        <label class="form-label">Status</label>
                        <select name="status" class="form-select">
                            @foreach(\App\Models\SpecialRequest::STATUSES as $status)
                                <option value="{{ $status }}" {{ $specialRequest->status === $status ? 'selected' : '' }}>{{ ucfirst(str_replace('_', ' ', $status)) }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Quoted amount (N$)</label>
                        <input type="number" step="0.01" name="quoted_amount" class="form-control" value="{{ old('quoted_amount', $specialRequest->quoted_amount) }}">
                        <div class="form-text">Required to send a quote to the client.</div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Admin notes</label>
                        <textarea name="admin_notes" class="form-control" rows="3" placeholder="Internal notes about this request">{{ old('admin_notes', $specialRequest->admin_notes) }}</textarea>
                        <div class="form-text">Internal only, unless "Notify client" is checked below.</div>
                    </div>
                    <div class="mb-3 form-check">
                        <input type="checkbox" class="form-check-input" id="notify_client" name="notify_client" value="1">
                        <label class="form-check-label" for="notify_client">Email the client about this status change</label>
                    </div>
                    <button type="submit" class="btn btn-outline-primary w-100 mb-2"><i class="bi bi-check2"></i> Save changes</button>
                    <button type="submit" formaction="{{ route('admin.special-requests.send-quote', $specialRequest) }}" class="btn btn-success w-100 mb-2"><i class="bi bi-envelope"></i> Send Quote by Email</button>
                    <button type="button" id="sendQuoteWhatsappBtn" data-url="{{ route('admin.special-requests.send-quote-whatsapp', $specialRequest) }}" class="btn btn-success w-100"><i class="bi bi-whatsapp"></i> Send Quote via WhatsApp</button>
                </form>
            </div>
        </div>

        <div class="card">
            <div class="card-body">
                <p class="mb-1 small text-muted">Received: {{ $specialRequest->created_at->format('M j, Y H:i') }}</p>
                @if($specialRequest->quote_sent_at)
                    <p class="mb-1 small text-success"><i class="bi bi-check-circle"></i> Quote sent: {{ $specialRequest->quote_sent_at->format('M j, Y H:i') }}</p>
                @endif
                @if($specialRequest->responded_at)
                    <p class="mb-0 small text-muted">Responded: {{ $specialRequest->responded_at->format('M j, Y H:i') }}</p>
                @endif
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
document.getElementById('sendQuoteWhatsappBtn')?.addEventListener('click', function () {
    var btn = this;
    var form = document.getElementById('specialRequestForm');
    // Open the tab synchronously, in direct response to the click, so the
    // browser doesn't treat it as an unsolicited popup once the fetch below
    // resolves — we fill in its location once we know the wa.me URL.
    var waTab = window.open('', '_blank');

    btn.disabled = true;
    fetch(btn.dataset.url, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'Accept': 'application/json',
            'X-CSRF-TOKEN': form.querySelector('input[name="_token"]').value,
        },
        body: JSON.stringify({
            quoted_amount: form.querySelector('[name="quoted_amount"]').value,
            admin_notes: form.querySelector('[name="admin_notes"]').value,
        }),
    })
        .then(function (r) { return r.json().then(function (data) { return { ok: r.ok, data: data }; }); })
        .then(function (res) {
            if (res.ok && res.data.wa_url) {
                waTab.location = res.data.wa_url;
                location.reload();
            } else {
                if (waTab) waTab.close();
                var message = (res.data && res.data.message) || (res.data && res.data.errors && Object.values(res.data.errors)[0][0]) || 'Could not prepare the quote.';
                alert(message);
                btn.disabled = false;
            }
        })
        .catch(function () {
            if (waTab) waTab.close();
            alert('Something went wrong. Please try again.');
            btn.disabled = false;
        });
});
</script>
@endpush
@endsection
