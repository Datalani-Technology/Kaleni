@extends('admin.layout')

@section('title', 'Special Requests')
@section('sidebar_active', 'special-requests')

@section('content')
<div class="d-flex flex-wrap justify-content-between align-items-center mb-4 gap-2">
    <h1 class="mb-0">Special Requests</h1>
</div>

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
                        <th>Name</th>
                        <th>Event date</th>
                        <th>Occasion</th>
                        <th>Status</th>
                        <th>Quote</th>
                        <th>Received</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($specialRequests as $specialRequest)
                        <tr>
                            <td data-label="Name">
                                {{ $specialRequest->name }}<br>
                                <small class="text-muted">{{ $specialRequest->email }}</small>
                            </td>
                            <td data-label="Event date">{{ optional($specialRequest->event_date)->format('M j, Y') ?: 'N/A' }}</td>
                            <td data-label="Occasion">{{ $specialRequest->occasion ?: 'N/A' }}</td>
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
                            <td data-label="Quote">
                                @if($specialRequest->quote_sent_at)
                                    <span class="text-success"><i class="bi bi-check-circle"></i> N$ {{ number_format((float) $specialRequest->quoted_amount, 2) }}</span>
                                    <small class="d-block text-muted">Sent {{ $specialRequest->quote_sent_at->format('M j') }}</small>
                                @elseif($specialRequest->quoted_amount !== null)
                                    <span class="text-muted">N$ {{ number_format((float) $specialRequest->quoted_amount, 2) }}</span>
                                    <small class="d-block text-muted">Not sent yet</small>
                                @else
                                    <span class="text-muted">Not quoted</span>
                                @endif
                            </td>
                            <td data-label="Received">{{ $specialRequest->created_at->format('M j, Y H:i') }}</td>
                            <td data-label="Actions">
                                <a href="{{ route('admin.special-requests.show', $specialRequest) }}" class="btn btn-sm btn-primary" title="View">
                                    <i class="bi bi-eye"></i> View
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="text-center py-4" data-label="">No special requests found.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="card-footer">{{ $specialRequests->links() }}</div>
    </div>
</div>
@endsection
