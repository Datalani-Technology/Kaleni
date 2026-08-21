@if(isset($breadcrumbs) && count($breadcrumbs) > 0)
<nav aria-label="breadcrumb" class="breadcrumb-nav">
    <div class="breadcrumb-inner">
        <ol class="breadcrumb mb-0 breadcrumb-list">
            @foreach($breadcrumbs as $index => $breadcrumb)
                @if($loop->last)
                    <li class="breadcrumb-item active" aria-current="page">{{ $breadcrumb['name'] }}</li>
                @else
                    <li class="breadcrumb-item">
                        <a href="{{ $breadcrumb['url'] }}" class="breadcrumb-link">{{ $breadcrumb['name'] }}</a>
                    </li>
                @endif
            @endforeach
        </ol>
    </div>
</nav>
<style>
    .breadcrumb-nav { background: #f8f9fa; padding: 15px 0; margin-bottom: 20px; }
    .breadcrumb-inner {
        max-width: 1200px;
        margin: 0 auto;
        padding: 0 20px;
    }
    .breadcrumb-list { background: transparent; flex-wrap: wrap; }
    .breadcrumb-link { color: var(--primary-color); text-decoration: none; }
    .breadcrumb-link:hover { color: var(--primary-color); text-decoration: underline; }
    @media (max-width: 400px) {
        .breadcrumb-nav { padding: 10px 0; margin-bottom: 16px; }
        .breadcrumb-inner { padding: 0 12px; }
        .breadcrumb-list { font-size: 0.9rem; }
        .breadcrumb-item { word-break: break-word; }
    }
</style>
@endif
