@extends('admin.layout')

@section('title', 'Recovery Codes')
@section('sidebar_active', 'security')

@section('content')
<h1 class="mb-4">Recovery Codes</h1>

@if($codes)
    <div class="alert alert-warning">
        <i class="bi bi-exclamation-triangle"></i>
        Save these codes somewhere safe. <strong>Each one shown only once, right now.</strong>
        Use one to log in if you ever lose access to your authenticator app.
    </div>
    <div class="card mb-3" style="max-width: 480px;">
        <div class="card-body">
            <div class="row row-cols-2 g-2 font-monospace">
                @foreach($codes as $code)
                    <div class="col"><code class="user-select-all">{{ $code }}</code></div>
                @endforeach
            </div>
        </div>
    </div>
@else
    <div class="alert alert-info">
        Recovery codes are only shown once, right after they're generated. If you need a
        fresh set, regenerate them from the <a href="{{ route('admin.2fa.manage') }}">Security</a> page.
    </div>
@endif

<a href="{{ route('admin.dashboard') }}" class="btn btn-primary"><i class="bi bi-arrow-right"></i> Continue to Dashboard</a>
@endsection
