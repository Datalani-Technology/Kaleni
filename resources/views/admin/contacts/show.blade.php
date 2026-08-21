@extends('admin.layout')

@section('title', 'Enquiry: ' . Str::limit($contact->subject, 40))
@section('sidebar_active', 'contacts')

@section('content')
<div class="d-flex flex-wrap justify-content-between align-items-center mb-4 gap-2">
    <h1 class="mb-0">Enquiry</h1>
    <div class="d-flex flex-wrap gap-2">
        <a href="{{ route('admin.contacts.index') }}" class="btn btn-outline-secondary"><i class="bi bi-arrow-left"></i> All Enquiries</a>
        <a href="mailto:{{ $contact->email }}?subject=Re: {{ rawurlencode($contact->subject) }}" class="btn btn-primary"><i class="bi bi-reply"></i> Reply</a>
    </div>
</div>

<div class="card">
    <div class="card-body">
        <p class="mb-2"><strong>From:</strong> {{ $contact->name }} &lt;<a href="mailto:{{ $contact->email }}">{{ $contact->email }}</a>&gt;</p>
        <p class="mb-2"><strong>Subject:</strong> {{ $contact->subject }}</p>
        <p class="mb-2 text-muted small">Received {{ $contact->created_at->format('M j, Y \a\t H:i') }}</p>
        <hr>
        <div class="message-body" style="white-space: pre-wrap;">{{ $contact->message }}</div>
    </div>
</div>
@endsection
