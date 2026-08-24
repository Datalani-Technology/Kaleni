@extends('admin.layout')

@section('title', 'Order ' . $order->order_number)
@section('sidebar_active', 'orders')

@section('content')
<div class="mb-4">
    <h1 class="mb-3">Order {{ $order->order_number }}</h1>
    <div class="d-flex flex-wrap gap-2 admin-page-actions">
        <a href="{{ route('admin.orders.index') }}" class="btn btn-outline-secondary"><i class="bi bi-arrow-left"></i> All Orders</a>
        @php
            $wa = 'https://wa.me/' . preg_replace('/[^0-9]/', '', $order->customer_phone);
        @endphp
        <a href="{{ $wa }}" target="_blank" rel="noopener" class="btn btn-success"><i class="bi bi-whatsapp"></i> WhatsApp</a>
        <a href="tel:{{ $order->customer_phone }}" class="btn btn-outline-primary"><i class="bi bi-telephone"></i> Call</a>
        <button type="button" onclick="openAdminDocument('{{ route('admin.orders.invoice', $order) }}')" class="btn btn-outline-dark"><i class="bi bi-receipt"></i> Invoice</button>
    </div>
</div>

<div class="row g-4">
    <div class="col-lg-8">
        <div class="card mb-4">
            <div class="card-header bg-light d-flex justify-content-between align-items-center">
                <span class="fw-semibold">Order items</span>
                <span class="badge bg-primary">{{ $order->items->count() }} item(s)</span>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover mb-0 admin-table-cards">
                        <thead>
                            <tr>
                                <th>Product</th>
                                <th class="text-end">Qty</th>
                                <th class="text-end">Price</th>
                                <th class="text-end">Subtotal</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($order->items as $item)
                                <tr>
                                    <td data-label="Product">
                                        @if($item->product)
                                            {{ $item->product->name }}
                                        @else
                                            <span class="text-muted">Product #{{ $item->product_id }} (deleted)</span>
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
                <strong>Total: N$ {{ number_format($order->total_amount, 2) }}</strong>
            </div>
        </div>

        <div class="card mb-4">
            <div class="card-header bg-light fw-semibold">Customer & delivery</div>
            <div class="card-body">
                <p class="mb-2"><strong>Name:</strong> {{ $order->customer_name }}</p>
                <p class="mb-2"><strong>Email:</strong> <a href="mailto:{{ $order->customer_email }}">{{ $order->customer_email }}</a></p>
                <p class="mb-2"><strong>Phone:</strong> <a href="tel:{{ $order->customer_phone }}">{{ $order->customer_phone }}</a></p>
                <hr>
                <p class="mb-2"><strong>Recipient:</strong> {{ $order->recipient_name ?: $order->customer_name }}</p>
                <p class="mb-2"><strong>Recipient phone:</strong> <a href="tel:{{ $order->recipient_phone ?: $order->customer_phone }}">{{ $order->recipient_phone ?: $order->customer_phone }}</a></p>
                <p class="mb-2"><strong>Delivery:</strong> {{ $order->delivery_date?->format('D, j M Y') ?: 'To be confirmed' }} · {{ ucfirst($order->delivery_window ?: 'anytime') }}</p>
                <p class="mb-2"><strong>Delivery address:</strong><br>{{ nl2br(e($order->delivery_address)) }}</p>
                @if($order->delivery_instructions)<p class="mb-2"><strong>Instructions:</strong><br>{{ nl2br(e($order->delivery_instructions)) }}</p>@endif
                @if($order->gift_message)<p class="mb-0"><strong>Card message:</strong><br>{{ nl2br(e($order->gift_message)) }}</p>@endif
            </div>
        </div>
    </div>

    <div class="col-lg-4">
        <div class="card mb-4">
            <div class="card-header bg-light fw-semibold">Update order</div>
            <div class="card-body">
                <form action="{{ route('admin.orders.update', $order) }}" method="POST" id="orderUpdateForm" data-original-status="{{ $order->order_status }}" data-confirm-mode="order-status">
                    @csrf
                    <div class="mb-3">
                        <label class="form-label">Order status</label>
                        <select name="order_status" class="form-select" id="orderStatusSelect">
                            <option value="pending" {{ $order->order_status === 'pending' ? 'selected' : '' }}>Pending</option>
                            <option value="processing" {{ $order->order_status === 'processing' ? 'selected' : '' }}>Processing</option>
                            <option value="completed" {{ $order->order_status === 'completed' ? 'selected' : '' }}>Completed</option>
                            <option value="cancelled" {{ $order->order_status === 'cancelled' ? 'selected' : '' }}>Cancelled</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Payment status</label>
                        <select name="payment_status" class="form-select">
                            <option value="pending" {{ $order->payment_status === 'pending' ? 'selected' : '' }}>Pending</option>
                            <option value="completed" {{ $order->payment_status === 'completed' ? 'selected' : '' }}>Completed</option>
                            <option value="failed" {{ $order->payment_status === 'failed' ? 'selected' : '' }}>Failed</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Admin notes</label>
                        <textarea name="admin_notes" class="form-control" rows="3" placeholder="Delivery instructions, gate code, etc.">{{ old('admin_notes', $order->admin_notes) }}</textarea>
                        <div class="form-text">Internal only. Not sent to customer.</div>
                    </div>
                    <button type="submit" class="btn btn-primary w-100"><i class="bi bi-check2"></i> Save changes</button>
                </form>
            </div>
        </div>

        <div class="card">
            <div class="card-body">
                <p class="mb-1 small text-muted">Payment: {{ ucfirst($order->payment_method) }}</p>
                <p class="mb-1 small text-muted">Placed: {{ $order->created_at->format('M j, Y H:i') }}</p>
                @if($order->updated_at != $order->created_at)
                    <p class="mb-0 small text-muted">Updated: {{ $order->updated_at->format('M j, Y H:i') }}</p>
                @endif
            </div>
        </div>
    </div>
</div>

@endsection
