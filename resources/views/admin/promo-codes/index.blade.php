@extends('admin.layout')

@section('title', 'Promo Codes')
@section('sidebar_active', 'promo-codes')

@section('content')
<div class="d-flex flex-wrap justify-content-between align-items-center mb-4 gap-2">
    <h1 class="mb-0"><i class="bi bi-tags"></i> Promo codes</h1>
    <a href="{{ route('admin.promo-codes.create') }}" class="btn btn-primary"><i class="bi bi-plus-circle"></i> New code</a>
</div>

<div class="card">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover mb-0 admin-table-cards">
                <thead>
                    <tr>
                        <th>Code</th>
                        <th>Discount</th>
                        <th>Scope</th>
                        <th>Window</th>
                        <th>Usage</th>
                        <th>Status</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($promoCodes as $p)
                        <tr>
                            <td data-label="Code"><strong>{{ $p->code }}</strong>@if($p->description)<br><span class="text-muted small">{{ $p->description }}</span>@endif</td>
                            <td data-label="Discount">{{ $p->type === 'percent' ? number_format($p->value, 0) . '%' : 'N$ ' . number_format($p->value, 2) }}</td>
                            <td data-label="Scope">{{ $p->scope === 'all' ? 'All products' : $p->products_count . ' product(s)' }}</td>
                            <td data-label="Window">
                                @if($p->starts_at || $p->ends_at)
                                    {{ $p->starts_at?->format('d M Y') ?: 'now' }} &rarr; {{ $p->ends_at?->format('d M Y') ?: 'no end' }}
                                @else
                                    <span class="text-muted">Always on</span>
                                @endif
                            </td>
                            <td data-label="Usage">{{ $p->times_used }}{{ $p->usage_limit ? ' / ' . $p->usage_limit : '' }}</td>
                            <td data-label="Status">
                                @if($p->is_active && $p->isCurrentlyValid())
                                    <span class="badge bg-success">Active</span>
                                @elseif($p->is_active)
                                    <span class="badge bg-secondary">Out of window</span>
                                @else
                                    <span class="badge bg-secondary">Disabled</span>
                                @endif
                            </td>
                            <td data-label="">
                                <div class="d-flex gap-1">
                                    <a href="{{ route('admin.promo-codes.edit', $p) }}" class="btn btn-sm btn-outline-primary" title="Edit"><i class="bi bi-pencil"></i></a>
                                    <form action="{{ route('admin.promo-codes.destroy', $p) }}" method="POST">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-outline-danger" title="Delete"><i class="bi bi-trash"></i></button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="7" class="text-center text-muted py-4" data-label="">No promo codes yet.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="card-footer">{{ $promoCodes->links() }}</div>
    </div>
</div>
@endsection
