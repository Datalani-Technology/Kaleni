@extends('admin.layout')

@section('title', 'Quotes')
@section('sidebar_active', 'quotes')

@section('content')
<div class="d-flex flex-wrap justify-content-between align-items-center mb-4 gap-2">
    <h1 class="mb-0">Quotes</h1>
</div>
<p class="text-muted mb-4">Event-level quote requests submitted through the home page's "Get a quote" form. Bespoke item and menu requests live under <a href="{{ route('admin.special-requests.index') }}">Special Requests</a>.</p>

<form method="GET" class="row g-2 mb-4 admin-filters">
    <div class="col-12 col-sm-6 col-md-4 col-lg-3">
        <select name="status" class="form-select">
            <option value="">All statuses</option>
            @foreach(\App\Models\SpecialRequest::STATUSES as $status)
                <option value="{{ $status }}" {{ request('status') === $status ? 'selected' : '' }}>{{ ucfirst(str_replace('_', ' ', $status)) }}</option>
            @endforeach
        </select>
    </div>
    <div class="col-12 col-sm-6 col-md-4 col-lg-3">
        <select name="stage" class="form-select">
            <option value="">Any quote stage</option>
            <option value="pending" {{ request('stage') === 'pending' ? 'selected' : '' }}>Pending (not sent yet)</option>
            <option value="sent" {{ request('stage') === 'sent' ? 'selected' : '' }}>Quote sent</option>
        </select>
    </div>
    <div class="col-12 col-sm-6 col-md-4 col-lg-2">
        <button type="submit" class="btn btn-primary w-100"><i class="bi bi-search"></i> Filter</button>
    </div>
</form>

<div class="card">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover mb-0 admin-table-cards">
                <thead>
                    <tr>
                        <th>Client</th>
                        <th>Occasion</th>
                        <th>Event date</th>
                        <th>Quoted amount</th>
                        <th>Quote stage</th>
                        <th>Status</th>
                        <th>Received</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($quotes as $quote)
                        <tr>
                            <td data-label="Client">
                                {{ $quote->name }}<br>
                                <small class="text-muted">{{ $quote->email }}</small>
                            </td>
                            <td data-label="Occasion">{{ $quote->occasion ?: 'Not specified' }}</td>
                            <td data-label="Event date">{{ optional($quote->event_date)->format('M j, Y') ?: 'Not specified' }}</td>
                            <td data-label="Quoted amount">
                                @if($quote->quoted_amount !== null)
                                    <strong>N$ {{ number_format((float) $quote->quoted_amount, 2) }}</strong>
                                @else
                                    <span class="text-muted">Pending</span>
                                @endif
                            </td>
                            <td data-label="Quote stage">
                                @if($quote->quote_sent_at)
                                    <span class="text-success"><i class="bi bi-check-circle"></i> Sent {{ $quote->quote_sent_at->format('M j') }}</span>
                                @else
                                    <span class="text-muted">Not sent yet</span>
                                @endif
                            </td>
                            <td data-label="Status">
                                @php
                                    $badgeClass = match($quote->status) {
                                        'accepted' => 'bg-success',
                                        'declined', 'cancelled' => 'bg-secondary',
                                        'quoted' => 'bg-info',
                                        'in_review' => 'bg-warning text-dark',
                                        default => 'bg-primary',
                                    };
                                @endphp
                                <span class="badge {{ $badgeClass }}">{{ ucfirst(str_replace('_', ' ', $quote->status)) }}</span>
                            </td>
                            <td data-label="Received">{{ $quote->created_at->format('M j, Y H:i') }}</td>
                            <td data-label="Actions">
                                <a href="{{ route('admin.special-requests.show', $quote) }}" class="btn btn-sm btn-primary" title="View">
                                    <i class="bi bi-eye"></i> View
                                </a>
                                @if($quote->quote_sent_at)
                                    <a href="{{ route('admin.special-requests.quote', $quote) }}" target="_blank" class="btn btn-sm btn-outline-secondary" title="Quote document">
                                        <i class="bi bi-file-earmark-text"></i>
                                    </a>
                                @endif
                                <form action="{{ route('admin.special-requests.destroy', $quote) }}" method="POST" class="d-inline" data-confirm-message="This permanently deletes the quote from {{ $quote->name }}. This cannot be undone." data-confirm-label="Delete quote">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-outline-danger" title="Delete">
                                        <i class="bi bi-trash"></i>
                                    </button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="text-center py-4" data-label="">No quote requests found.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="card-footer">{{ $quotes->links() }}</div>
    </div>
</div>
@endsection
