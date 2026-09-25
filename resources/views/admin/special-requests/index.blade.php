@extends('admin.layout')

@section('title', 'Special Requests')
@section('sidebar_active', 'special-requests')

@section('content')
<div class="d-flex flex-wrap justify-content-between align-items-center mb-4 gap-2">
    <h1 class="mb-0">Special Requests</h1>
</div>
<p class="text-muted mb-4">Bespoke item and menu requests submitted through the "Request a special item" page. Event-level quote requests from the home page live under <a href="{{ route('admin.quotes.index') }}">Quotes</a>.</p>

<form method="GET" class="row g-2 mb-4 admin-filters">
    <div class="col-12 col-sm-6 col-md-4 col-lg-3">
        <select name="status" class="form-select">
            <option value="">All statuses</option>
            @foreach(\App\Models\SpecialRequest::STATUSES as $status)
                <option value="{{ $status }}" {{ request('status') === $status ? 'selected' : '' }}>{{ ucfirst(str_replace('_', ' ', $status)) }}</option>
            @endforeach
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
                    @forelse($specialRequests as $specialRequest)
                        <tr>
                            <td data-label="Client">
                                {{ $specialRequest->name }}<br>
                                <small class="text-muted">{{ $specialRequest->email }}</small>
                            </td>
                            <td data-label="Occasion">{{ $specialRequest->occasion ?: 'Not specified' }}</td>
                            <td data-label="Event date">
                                {{ optional($specialRequest->event_date)->format('M j, Y') ?: 'Not specified' }}
                                @if($specialRequest->is_recurring && $specialRequest->recurring_end_date)
                                    <br><small class="text-muted">to {{ $specialRequest->recurring_end_date->format('M j, Y') }}</small>
                                @endif
                            </td>
                            <td data-label="Quoted amount">
                                @if($specialRequest->quoted_amount !== null)
                                    <strong>N$ {{ number_format((float) $specialRequest->quoted_amount, 2) }}</strong>
                                @else
                                    <span class="text-muted">Pending</span>
                                @endif
                            </td>
                            <td data-label="Quote stage">
                                @if($specialRequest->quote_sent_at)
                                    <span class="text-success"><i class="bi bi-check-circle"></i> Sent {{ $specialRequest->quote_sent_at->format('M j') }}</span>
                                @else
                                    <span class="text-muted">Not sent yet</span>
                                @endif
                            </td>
                            <td data-label="Status">
                                @php
                                    $badgeClass = match($specialRequest->status) {
                                        'accepted' => 'bg-success',
                                        'declined', 'cancelled' => 'bg-secondary',
                                        'quoted' => 'bg-info',
                                        'in_review' => 'bg-warning text-dark',
                                        default => 'bg-primary',
                                    };
                                @endphp
                                <span class="badge {{ $badgeClass }}">{{ ucfirst(str_replace('_', ' ', $specialRequest->status)) }}</span>
                            </td>
                            <td data-label="Received">{{ $specialRequest->created_at->format('M j, Y H:i') }}</td>
                            <td data-label="Actions">
                                <a href="{{ route('admin.special-requests.show', $specialRequest) }}" class="btn btn-sm btn-primary" title="View">
                                    <i class="bi bi-eye"></i> View
                                </a>
                                @if($specialRequest->quote_sent_at)
                                    <a href="{{ route('admin.special-requests.quote', $specialRequest) }}" target="_blank" class="btn btn-sm btn-outline-secondary" title="Quote document">
                                        <i class="bi bi-file-earmark-text"></i>
                                    </a>
                                @endif
                                <form action="{{ route('admin.special-requests.destroy', $specialRequest) }}" method="POST" class="d-inline" data-confirm-message="This permanently deletes the special request from {{ $specialRequest->name }}. This cannot be undone." data-confirm-label="Delete special request">
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
                            <td colspan="8" class="text-center py-4" data-label="">No special requests found.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="card-footer">{{ $specialRequests->links() }}</div>
    </div>
</div>
@endsection
