@extends('admin.layout')

@section('title', 'Special Request from ' . $specialRequest->name)
@section('sidebar_active', 'special-requests')

@section('content')
<div class="mb-4">
    <h1 class="mb-3">Special Request from {{ $specialRequest->name }}</h1>
    <div class="d-flex flex-wrap gap-2 admin-page-actions">
        <a href="{{ route('admin.special-requests.index') }}" class="btn btn-outline-secondary"><i class="bi bi-arrow-left"></i> All Requests</a>
        @php $wa = 'https://wa.me/' . preg_replace('/[^0-9]/', '', $specialRequest->phone); @endphp
        <a href="{{ $wa }}" target="_blank" rel="noopener" class="btn btn-success"><i class="bi bi-whatsapp"></i> WhatsApp</a>
        <a href="tel:{{ $specialRequest->phone }}" class="btn btn-outline-primary"><i class="bi bi-telephone"></i> Call</a>
        <a href="{{ route('admin.special-requests.quote', $specialRequest) }}" target="_blank" class="btn btn-outline-primary"><i class="bi bi-file-earmark-text"></i> View Quote</a>
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
                <p class="mb-2"><strong>Event date:</strong> {{ optional($specialRequest->event_date)->format('D, j M Y') ?: 'Not specified' }}</p>
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
                    <button type="submit" formaction="{{ route('admin.special-requests.send-quote', $specialRequest) }}" class="btn btn-success w-100"><i class="bi bi-check-circle"></i> Send Quote to Client</button>
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
@endsection
