@extends('admin.layout')

@section('title', 'Analytics')
@section('sidebar_active', 'analytics')

@push('styles')
<style>
    .analytics-card { min-height: 90px; }
    @media (max-width: 575.98px) {
        .analytics-period-form { width: 100%; }
        .analytics-period-form .form-select { flex: 1; min-width: 0; }
    }
</style>
@endpush

@section('content')
<div class="d-flex flex-wrap justify-content-between align-items-center mb-4 gap-2">
    <h1 class="mb-0"><i class="bi bi-graph-up"></i> Visit analytics</h1>
    <form action="{{ route('admin.analytics.index') }}" method="GET" class="d-flex gap-2 analytics-period-form">
        <select name="period" class="form-select form-select-sm" style="min-width: 140px;" onchange="this.form.submit()">
            <option value="today" {{ $period === 'today' ? 'selected' : '' }}>Today</option>
            <option value="7d" {{ $period === '7d' ? 'selected' : '' }}>Last 7 days</option>
            <option value="30d" {{ $period === '30d' ? 'selected' : '' }}>Last 30 days</option>
            <option value="90d" {{ $period === '90d' ? 'selected' : '' }}>Last 90 days</option>
        </select>
    </form>
</div>

<div class="row g-3 mb-4">
    <div class="col-6 col-md-3">
        <div class="card border-0 bg-primary text-white analytics-card">
            <div class="card-body">
                <h6 class="card-title text-white-50">Page views</h6>
                <h3 class="mb-0">{{ number_format($totalViews) }}</h3>
                <small class="opacity-75">in period</small>
            </div>
        </div>
    </div>
    <div class="col-6 col-md-3">
        <div class="card border-0 bg-success text-white analytics-card">
            <div class="card-body">
                <h6 class="card-title text-white-50">Unique visitors</h6>
                <h3 class="mb-0">{{ number_format($uniqueVisitors) }}</h3>
                <small class="opacity-75">by session</small>
            </div>
        </div>
    </div>
    <div class="col-6 col-md-3">
        <div class="card border-0 bg-info text-white analytics-card">
            <div class="card-body">
                <h6 class="card-title text-white-50">Today · views</h6>
                <h3 class="mb-0">{{ number_format($todayViews) }}</h3>
            </div>
        </div>
    </div>
    <div class="col-6 col-md-3">
        <div class="card border-0 bg-secondary text-white analytics-card">
            <div class="card-body">
                <h6 class="card-title text-white-50">Today · unique</h6>
                <h3 class="mb-0">{{ number_format($todayUnique) }}</h3>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-lg-6 mb-4">
        <div class="card">
            <div class="card-header"><strong>Views by page type</strong></div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead><tr><th>Page</th><th class="text-end">Views</th></tr></thead>
                        <tbody>
                            @forelse($byPage as $r)
                                <tr><td>{{ $r->page_type ?: 'other' }}</td><td class="text-end">{{ number_format($r->hits) }}</td></tr>
                            @empty
                                <tr><td colspan="2" class="text-muted">No data.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
    <div class="col-lg-6 mb-4">
        <div class="card">
            <div class="card-header"><strong>Top paths</strong></div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead><tr><th>Path</th><th class="text-end">Views</th></tr></thead>
                        <tbody>
                            @forelse($topPaths as $r)
                                <tr><td><code class="small">{{ $r->path }}</code></td><td class="text-end">{{ number_format($r->hits) }}</td></tr>
                            @empty
                                <tr><td colspan="2" class="text-muted">No data.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="card">
    <div class="card-header"><strong>Recent visits</strong> <span class="text-muted small">(last 50)</span></div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead>
                    <tr><th>When</th><th>Path</th><th>Page type</th></tr>
                </thead>
                <tbody>
                    @forelse($recent as $v)
                        <tr>
                            <td class="text-nowrap">{{ $v->visited_at?->format('M j, H:i') }}</td>
                            <td><code class="small">{{ \Illuminate\Support\Str::limit($v->path, 50) }}</code></td>
                            <td>{{ $v->page_type ?? '—' }}</td>
                        </tr>
                    @empty
                        <tr><td colspan="3" class="text-muted">No visits yet.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
