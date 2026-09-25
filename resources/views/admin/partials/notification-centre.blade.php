<div class="offcanvas offcanvas-end admin-notification-panel" tabindex="-1" id="adminNotificationPanel" aria-labelledby="adminNotificationTitle">
    <div class="offcanvas-header">
        <div>
            <span class="notification-eyebrow">Live operations</span>
            <h2 class="offcanvas-title" id="adminNotificationTitle">Notifications</h2>
        </div>
        <button type="button" class="btn-close" data-bs-dismiss="offcanvas" aria-label="Close"></button>
    </div>
    <div class="offcanvas-body">
        @if($adminNotifications['total'] === 0)
            <div class="notification-clear-state">
                <i class="bi bi-check2-circle"></i>
                <h3>Everything is clear.</h3>
                <p>No pending bookings/orders, new requests, low-stock menu items, or unread enquiries need attention.</p>
            </div>
        @else
            <section class="notification-group">
                <a href="{{ route('admin.bookings.index', ['status' => 'pending']) }}" class="notification-group-head">
                    <span><i class="bi bi-bag-check"></i> Pending bookings</span>
                    @if($adminNotifications['pending_order_count'] > 0)
                        <span class="badge text-bg-dark">{{ $adminNotifications['pending_order_count'] }}</span>
                    @endif
                </a>
                @forelse($adminNotifications['orders'] as $notificationBooking)
                    <a href="{{ route('admin.bookings.show', $notificationBooking) }}" class="notification-item">
                        <span class="notification-dot notification-dot-order"></span>
                        <span><strong>{{ $notificationBooking->booking_number }}</strong><small>{{ $notificationBooking->customer_name }} · N$ {{ number_format($notificationBooking->total_amount, 2) }} · {{ $notificationBooking->created_at->diffForHumans() }}</small></span>
                    </a>
                @empty
                    <p class="notification-empty">No bookings waiting.</p>
                @endforelse
            </section>

            <section class="notification-group">
                <a href="{{ route('admin.orders.index', ['status' => 'pending']) }}" class="notification-group-head">
                    <span><i class="bi bi-basket"></i> Pending orders</span>
                    @if($adminNotifications['pending_quick_order_count'] > 0)
                        <span class="badge text-bg-dark">{{ $adminNotifications['pending_quick_order_count'] }}</span>
                    @endif
                </a>
                @forelse($adminNotifications['quick_orders'] as $notificationOrder)
                    <a href="{{ route('admin.bookings.show', $notificationOrder) }}" class="notification-item">
                        <span class="notification-dot notification-dot-order"></span>
                        <span><strong>{{ $notificationOrder->booking_number }}</strong><small>{{ $notificationOrder->customer_name }} · N$ {{ number_format($notificationOrder->total_amount, 2) }} · {{ $notificationOrder->created_at->diffForHumans() }}</small></span>
                    </a>
                @empty
                    <p class="notification-empty">No orders waiting.</p>
                @endforelse
            </section>

            <section class="notification-group">
                <a href="{{ route('admin.stock.index', ['filter' => 'low']) }}" class="notification-group-head">
                    <span><i class="bi bi-box-seam"></i> Low stock</span>
                    @if($adminNotifications['low_stock_count'] > 0)
                        <span class="badge text-bg-warning">{{ $adminNotifications['low_stock_count'] }}</span>
                    @endif
                </a>
                @forelse($adminNotifications['products'] as $notificationMenuItem)
                    <a href="{{ route('admin.stock.index', ['search' => $notificationMenuItem->name]) }}" class="notification-item">
                        <span class="notification-dot notification-dot-stock"></span>
                        <span><strong>{{ $notificationMenuItem->name }}</strong><small>{{ $notificationMenuItem->stock }} unit(s) remaining</small></span>
                    </a>
                @empty
                    <p class="notification-empty">Stock levels look healthy.</p>
                @endforelse
            </section>

            <section class="notification-group">
                <a href="{{ route('admin.contacts.index', ['unread' => 1]) }}" class="notification-group-head">
                    <span><i class="bi bi-envelope"></i> New enquiries</span>
                    @if($adminNotifications['unread_contact_count'] > 0)
                        <span class="badge text-bg-primary">{{ $adminNotifications['unread_contact_count'] }}</span>
                    @endif
                </a>
                @forelse($adminNotifications['contacts'] as $notificationContact)
                    <a href="{{ route('admin.contacts.show', $notificationContact) }}" class="notification-item">
                        <span class="notification-dot notification-dot-contact"></span>
                        <span><strong>{{ $notificationContact->subject }}</strong><small>{{ $notificationContact->name }} · {{ $notificationContact->created_at->diffForHumans() }}</small></span>
                    </a>
                @empty
                    <p class="notification-empty">No unread enquiries.</p>
                @endforelse
            </section>

            <section class="notification-group">
                <a href="{{ route('admin.special-requests.index', ['status' => 'new']) }}" class="notification-group-head">
                    <span><i class="bi bi-heart"></i> New special requests</span>
                    @if($adminNotifications['new_special_request_count'] > 0)
                        <span class="badge text-bg-primary">{{ $adminNotifications['new_special_request_count'] }}</span>
                    @endif
                </a>
                @forelse($adminNotifications['special_requests'] as $notificationSpecialRequest)
                    <a href="{{ route('admin.special-requests.show', $notificationSpecialRequest) }}" class="notification-item">
                        <span class="notification-dot notification-dot-special"></span>
                        <span><strong>{{ $notificationSpecialRequest->name }}</strong><small>{{ $notificationSpecialRequest->occasion ?: 'No occasion given' }} · {{ $notificationSpecialRequest->created_at->diffForHumans() }}</small></span>
                    </a>
                @empty
                    <p class="notification-empty">No new special requests.</p>
                @endforelse
            </section>

            <section class="notification-group">
                <a href="{{ route('admin.quotes.index', ['status' => 'new']) }}" class="notification-group-head">
                    <span><i class="bi bi-file-earmark-text"></i> New quote requests</span>
                    @if($adminNotifications['new_quote_request_count'] > 0)
                        <span class="badge text-bg-primary">{{ $adminNotifications['new_quote_request_count'] }}</span>
                    @endif
                </a>
                @forelse($adminNotifications['quote_requests'] as $notificationQuoteRequest)
                    <a href="{{ route('admin.special-requests.show', $notificationQuoteRequest) }}" class="notification-item">
                        <span class="notification-dot notification-dot-special"></span>
                        <span><strong>{{ $notificationQuoteRequest->name }}</strong><small>{{ $notificationQuoteRequest->occasion ?: 'No occasion given' }} · {{ $notificationQuoteRequest->created_at->diffForHumans() }}</small></span>
                    </a>
                @empty
                    <p class="notification-empty">No new quote requests.</p>
                @endforelse
            </section>
        @endif
    </div>
</div>
