@extends('admin.layout')

@section('title', 'Audit Log')
@section('sidebar_active', 'audit-log')

@section('content')
<div class="d-flex flex-wrap justify-content-between align-items-center mb-4 gap-2">
    <h1 class="mb-0">Audit Log</h1>
    <a href="{{ route('admin.dashboard') }}" class="btn btn-outline-secondary"><i class="bi bi-arrow-left"></i> Dashboard</a>
</div>

<form method="GET" class="row g-2 mb-4 admin-filters">
    <div class="col-12 col-sm-6 col-md-3">
        <select name="user_id" class="form-select">
            <option value="">All users</option>
            @foreach($users as $u)
                <option value="{{ $u->id }}" {{ (string) request('user_id') === (string) $u->id ? 'selected' : '' }}>{{ $u->name }}</option>
            @endforeach
        </select>
    </div>
    <div class="col-12 col-sm-6 col-md-3">
        <select name="action" class="form-select">
            <option value="">All actions</option>
            @foreach($actions as $a)
                <option value="{{ $a }}" {{ request('action') === $a ? 'selected' : '' }}>{{ $a }}</option>
            @endforeach
        </select>
    </div>
    <div class="col-6 col-md-2">
        <input type="date" name="from" class="form-control" value="{{ request('from') }}" placeholder="From">
    </div>
    <div class="col-6 col-md-2">
        <input type="date" name="to" class="form-control" value="{{ request('to') }}" placeholder="To">
    </div>
    <div class="col-12 col-md-2">
        <button type="submit" class="btn btn-primary w-100"><i class="bi bi-search"></i> Filter</button>
    </div>
</form>

<div class="card">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover mb-0 admin-table-cards">
                <thead>
                    <tr>
                        <th>When</th>
                        <th>User</th>
                        <th>Action</th>
                        <th>IP</th>
                        <th>Details</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($logs as $log)
                        <tr>
                            <td data-label="When">{{ $log->created_at->format('d M Y, H:i:s') }}</td>
                            <td data-label="User">
                                {{ $log->user->name ?? $log->email ?? '—' }}
                            </td>
                            <td data-label="Action"><code>{{ $log->action }}</code></td>
                            <td data-label="IP">{{ $log->ip ?? '—' }}</td>
                            <td data-label="Details">
                                @if($log->meta)
                                    <small class="text-muted">{{ collect($log->meta)->except('email')->map(fn($v, $k) => "{$k}: " . (is_scalar($v) ? $v : json_encode($v)))->implode(', ') }}</small>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="text-center py-4" data-label="">No audit events found.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="card-footer">{{ $logs->links() }}</div>
    </div>
</div>
@endsection
