@extends('admin.layout')

@section('title', 'Booking ' . $booking->booking_number)
@section('sidebar_active', 'bookings')

@section('content')
<div class="mb-4">
    <h1 class="mb-3">Booking {{ $booking->booking_number }}</h1>
    <div class="d-flex flex-wrap gap-2 admin-page-actions">
        <a href="{{ route('admin.bookings.index') }}" class="btn btn-outline-secondary"><i class="bi bi-arrow-left"></i> All Bookings</a>
        @php
            $wa = 'https://wa.me/' . preg_replace('/[^0-9]/', '', $booking->customer_phone);
        @endphp
        <a href="{{ $wa }}" target="_blank" rel="noopener" class="btn btn-success"><i class="bi bi-whatsapp"></i> WhatsApp</a>
        <a href="tel:{{ $booking->customer_phone }}" class="btn btn-outline-primary"><i class="bi bi-telephone"></i> Call</a>
        <button type="button" onclick="openAdminDocument('{{ route('admin.bookings.invoice', $booking) }}')" class="btn btn-outline-dark"><i class="bi bi-receipt"></i> Invoice</button>
    </div>
</div>

<div class="row g-4">
    <div class="col-lg-8">
        <div class="card mb-4">
            <div class="card-header bg-light d-flex justify-content-between align-items-center">
                <span class="fw-semibold">Booking items</span>
                <span class="badge bg-primary">{{ $booking->items->count() }} item(s)</span>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover mb-0 admin-table-cards">
                        <thead>
                            <tr>
                                <th>Menu item</th>
                                <th class="text-end">Qty</th>
                                <th class="text-end">Price</th>
                                <th class="text-end">Subtotal</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($booking->items as $item)
                                <tr>
                                    <td data-label="Menu item">
                                        @if($item->menuItem)
                                            {{ $item->menuItem->name }}
                                        @else
                                            <span class="text-muted">Menu item #{{ $item->menu_item_id }} (deleted)</span>
                                        @endif
                                    </td>
                                    <td data-label="Qty" class="text-end">{{ $item->quantity }}</td>
                                    <td data-label="Price" class="text-end">N$ {{ number_format($item->price, 2) }}</td>
                                    <td data-label="Subtotal" class="text-end">N$ {{ number_format($item->subtotal, 2) }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="card-footer d-flex justify-content-end">
                <strong>Total: N$ {{ number_format($booking->total_amount, 2) }}</strong>
            </div>
        </div>

        <div class="card mb-4">
            <div class="card-header bg-light fw-semibold">Customer &amp; event</div>
            <div class="card-body">
                <p class="mb-2"><strong>Name:</strong> {{ $booking->customer_name }}</p>
                <p class="mb-2"><strong>Email:</strong> <a href="mailto:{{ $booking->customer_email }}">{{ $booking->customer_email }}</a></p>
                <p class="mb-2"><strong>Phone:</strong> <a href="tel:{{ $booking->customer_phone }}">{{ $booking->customer_phone }}</a></p>
                <hr>
                <p class="mb-2"><strong>On-site contact:</strong> {{ $booking->onsite_contact_name ?: $booking->customer_name }}</p>
                <p class="mb-2"><strong>On-site contact phone:</strong> <a href="tel:{{ $booking->onsite_contact_phone ?: $booking->customer_phone }}">{{ $booking->onsite_contact_phone ?: $booking->customer_phone }}</a></p>
                <p class="mb-2"><strong>Schedule:</strong> {{ $booking->schedule_summary }} · {{ ucfirst(str_replace('_', ' ', $booking->serving_period ?: 'custom')) }} service</p>
                <p class="mb-2"><strong>Event type:</strong> {{ $booking->event_type ?: 'N/A' }}</p>
                <p class="mb-2"><strong>Guests:</strong> {{ $booking->guest_count ?: 'N/A' }}</p>
                <p class="mb-2"><strong>Event address:</strong><br>{{ nl2br(e($booking->event_address)) }}</p>
                @if($booking->event_notes)<p class="mb-2"><strong>Event notes:</strong><br>{{ nl2br(e($booking->event_notes)) }}</p>@endif
                @if($booking->special_message)<p class="mb-0"><strong>Special message:</strong><br>{{ nl2br(e($booking->special_message)) }}</p>@endif
            </div>
        </div>
    </div>

    <div class="col-lg-4">
        <div class="card mb-4">
            <div class="card-header bg-light fw-semibold">Update booking</div>
            <div class="card-body">
                <form action="{{ route('admin.bookings.update', $booking) }}" method="POST" id="bookingUpdateForm" data-original-status="{{ $booking->booking_status }}" data-confirm-mode="booking-status">
                    @csrf
                    <div class="mb-3">
                        <label class="form-label">Booking status</label>
                        <select name="booking_status" class="form-select" id="bookingStatusSelect">
                            <option value="pending" {{ $booking->booking_status === 'pending' ? 'selected' : '' }}>Pending</option>
                            <option value="processing" {{ $booking->booking_status === 'processing' ? 'selected' : '' }}>Processing</option>
                            <option value="completed" {{ $booking->booking_status === 'completed' ? 'selected' : '' }}>Completed</option>
                            <option value="cancelled" {{ $booking->booking_status === 'cancelled' ? 'selected' : '' }}>Cancelled</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Payment status</label>
                        <select name="payment_status" class="form-select">
                            <option value="pending" {{ $booking->payment_status === 'pending' ? 'selected' : '' }}>Pending</option>
                            <option value="completed" {{ $booking->payment_status === 'completed' ? 'selected' : '' }}>Completed</option>
                            <option value="failed" {{ $booking->payment_status === 'failed' ? 'selected' : '' }}>Failed</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Admin notes</label>
                        <textarea name="admin_notes" class="form-control" rows="3" placeholder="Setup instructions, gate code, etc.">{{ old('admin_notes', $booking->admin_notes) }}</textarea>
                        <div class="form-text">Internal only. Not sent to customer.</div>
                    </div>
                    <button type="submit" class="btn btn-primary w-100"><i class="bi bi-check2"></i> Save changes</button>
                </form>
            </div>
        </div>

        <div class="card">
            <div class="card-body">
                <p class="mb-1 small text-muted">Payment: {{ ucfirst($booking->payment_method) }}</p>
                <p class="mb-1 small text-muted">Placed: {{ $booking->created_at->format('M j, Y H:i') }}</p>
                @if($booking->updated_at != $booking->created_at)
                    <p class="mb-0 small text-muted">Updated: {{ $booking->updated_at->format('M j, Y H:i') }}</p>
                @endif
            </div>
        </div>
    </div>
</div>

@endsection
