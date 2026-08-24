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
                <p>No pending orders, low-stock products, or unread enquiries need attention.</p>
            </div>
        @else
            <section class="notification-group">
                <a href="{{ route('admin.orders.index', ['status' => 'pending']) }}" class="notification-group-head">
                    <span><i class="bi bi-bag-check"></i> Pending orders</span><span class="badge text-bg-dark">{{ $adminNotifications['pending_order_count'] }}</span>
                </a>
                @forelse($adminNotifications['orders'] as $notificationOrder)
                    <a href="{{ route('admin.orders.show', $notificationOrder) }}" class="notification-item">
                        <span class="notification-dot notification-dot-order"></span>
                        <span><strong>{{ $notificationOrder->order_number }}</strong><small>{{ $notificationOrder->customer_name }} · N$ {{ number_format($notificationOrder->total_amount, 2) }} · {{ $notificationOrder->created_at->diffForHumans() }}</small></span>
                    </a>
                @empty
                    <p class="notification-empty">No orders waiting.</p>
                @endforelse
            </section>

            <section class="notification-group">
                <a href="{{ route('admin.stock.index', ['filter' => 'low']) }}" class="notification-group-head">
                    <span><i class="bi bi-box-seam"></i> Low stock</span><span class="badge text-bg-warning">{{ $adminNotifications['low_stock_count'] }}</span>
                </a>
                @forelse($adminNotifications['products'] as $notificationProduct)
                    <a href="{{ route('admin.stock.index', ['search' => $notificationProduct->name]) }}" class="notification-item">
                        <span class="notification-dot notification-dot-stock"></span>
                        <span><strong>{{ $notificationProduct->name }}</strong><small>{{ $notificationProduct->stock }} unit(s) remaining</small></span>
                    </a>
                @empty
                    <p class="notification-empty">Stock levels look healthy.</p>
                @endforelse
            </section>

            <section class="notification-group">
                <a href="{{ route('admin.contacts.index', ['unread' => 1]) }}" class="notification-group-head">
                    <span><i class="bi bi-envelope"></i> New enquiries</span><span class="badge text-bg-primary">{{ $adminNotifications['unread_contact_count'] }}</span>
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
        @endif
    </div>
</div>
