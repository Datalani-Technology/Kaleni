@extends('admin.layout')

@section('title', 'Enquiries')
@section('sidebar_active', 'contacts')

@section('content')
<div class="d-flex flex-wrap justify-content-between align-items-center mb-4 gap-2">
    <h1 class="mb-0">Enquiries</h1>
    <a href="{{ route('admin.dashboard') }}" class="btn btn-outline-secondary"><i class="bi bi-arrow-left"></i> Dashboard</a>
</div>

<form method="GET" class="row g-2 mb-4 admin-filters">
    <div class="col-12 col-sm-8 col-md-6">
        <input type="text" name="q" class="form-control" placeholder="Search name, email, subject..." value="{{ request('q') }}">
    </div>
    <div class="col-12 col-sm-4 col-md-3 d-flex align-items-center">
        <label class="form-check-label mb-0 d-flex align-items-center gap-2" style="min-height: 44px; cursor: pointer;">
            <input type="checkbox" name="unread" value="1" class="form-check-input" {{ request('unread') ? 'checked' : '' }}> Unread only
        </label>
    </div>
    <div class="col-12 col-sm-6 col-md-3">
        <button type="submit" class="btn btn-primary w-100"><i class="bi bi-search"></i> Filter</button>
    </div>
</form>

<div class="card">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover mb-0 admin-table-cards">
                <thead>
                    <tr>
                        <th>From</th>
                        <th>Subject</th>
                        <th>Received</th>
                        <th>Status</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($contacts as $c)
                        <tr class="{{ !$c->is_read ? 'table-warning' : '' }}">
                            <td data-label="From">
                                <strong>{{ $c->name }}</strong><br>
                                <small class="text-muted">{{ $c->email }}</small>
                            </td>
                            <td data-label="Subject">{{ Str::limit($c->subject, 50) }}</td>
                            <td data-label="Received">{{ $c->created_at->format('M j, Y H:i') }}</td>
                            <td data-label="Status">
                                @if($c->is_read)
                                    <span class="badge bg-secondary">Read</span>
                                @else
                                    <span class="badge bg-primary">New</span>
                                @endif
                            </td>
                            <td data-label="Actions">
                                <a href="{{ route('admin.contacts.show', $c) }}" class="btn btn-sm btn-primary"><i class="bi bi-eye"></i> View</a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="text-center py-4" data-label="">No enquiries found.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="card-footer">{{ $contacts->links() }}</div>
    </div>
</div>
@endsection
