@php
    $user = auth()->user();
    $isAdmin = $user && $user->role === 'admin';
    $active = $active ?? '';
    $adminLogoPath = \App\Models\Setting::get('logo_path');
    $adminLogoText = \App\Models\Setting::get('logo_text', 'Kaleni Catering Services');
    $userInitial = $user ? strtoupper(substr($user->name ?: $user->email, 0, 1)) : 'K';
@endphp
<nav class="sidebar-nav" aria-label="Administration">
    <div class="sidebar-brand">
        <div class="sidebar-brand-main">
            @if($adminLogoPath)
                <img src="{{ asset('storage/' . $adminLogoPath) }}" alt="{{ $adminLogoText }}" class="admin-sidebar-logo">
            @else
                <img src="{{ asset('images/kaleni/brand/kaleni-logo.jpg') }}" alt="{{ $adminLogoText }}" class="admin-sidebar-logo">
            @endif
            <span class="sidebar-brand-name">{{ $adminLogoText }}</span>
        </div>
        <span class="sidebar-brand-meta">Business console</span>
    </div>

    <div class="sidebar-section-label">Overview</div>
    <a href="{{ route('admin.dashboard') }}" class="{{ $active === 'dashboard' ? 'active' : '' }}">
        <i class="bi bi-grid-1x2"></i><span>Dashboard</span>
    </a>
    <a href="{{ route('admin.analytics.index') }}" class="{{ $active === 'analytics' ? 'active' : '' }}">
        <i class="bi bi-graph-up"></i><span>Analytics</span>
    </a>

    <div class="sidebar-section-label">Sales &amp; operations</div>
    <a href="{{ route('admin.bookings.index') }}" class="{{ $active === 'bookings' ? 'active' : '' }}">
        <i class="bi bi-bag-check"></i><span>Bookings</span>
    </a>
    <a href="{{ route('admin.menu-items.index') }}" class="{{ $active === 'menu-items' ? 'active' : '' }}">
        <i class="bi bi-egg-fried"></i><span>Menu Items</span>
    </a>
    <a href="{{ route('admin.food-of-the-day.index') }}" class="{{ $active === 'food-of-the-day' ? 'active' : '' }}">
        <i class="bi bi-stars"></i><span>Food of the Day</span>
    </a>
    <a href="{{ route('admin.special-requests.index') }}" class="{{ $active === 'special-requests' ? 'active' : '' }}">
        <i class="bi bi-heart"></i><span>Special Requests</span>
    </a>
    <a href="{{ route('admin.stock.index') }}" class="{{ $active === 'stock' ? 'active' : '' }}">
        <i class="bi bi-box-seam"></i><span>Inventory</span>
    </a>
    <a href="{{ route('admin.customers.index') }}" class="{{ $active === 'customers' ? 'active' : '' }}">
        <i class="bi bi-people"></i><span>Customers</span>
    </a>
    <a href="{{ route('admin.contacts.index') }}" class="{{ $active === 'contacts' ? 'active' : '' }}">
        <i class="bi bi-chat-square-text"></i><span>Enquiries</span>
    </a>
    <a href="{{ route('admin.expenses.index') }}" class="{{ $active === 'expenses' ? 'active' : '' }}">
        <i class="bi bi-receipt"></i><span>Expenses</span>
    </a>
    <a href="{{ route('admin.promo-codes.index') }}" class="{{ $active === 'promo-codes' ? 'active' : '' }}">
        <i class="bi bi-tags"></i><span>Promo codes</span>
    </a>
    <a href="{{ route('admin.reports.index') }}" class="{{ $active === 'reports' ? 'active' : '' }}">
        <i class="bi bi-file-earmark-bar-graph"></i><span>Reports</span>
    </a>

    @if($isAdmin)
        <div class="sidebar-section-label">Content &amp; access</div>
        <a href="{{ route('admin.promotion.edit') }}" class="{{ $active === 'promotion' ? 'active' : '' }}">
            <i class="bi bi-megaphone"></i><span>Promotions</span>
        </a>
        <a href="{{ route('admin.gallery.index') }}" class="{{ $active === 'gallery' ? 'active' : '' }}">
            <i class="bi bi-images"></i><span>Gallery</span>
        </a>
        <a href="{{ route('admin.logo.edit') }}" class="{{ $active === 'logo' ? 'active' : '' }}">
            <i class="bi bi-palette"></i><span>Brand settings</span>
        </a>
        <a href="{{ route('admin.users.index') }}" class="{{ $active === 'users' ? 'active' : '' }}">
            <i class="bi bi-person-badge"></i><span>Team members</span>
        </a>
        <a href="{{ route('admin.audit-log.index') }}" class="{{ $active === 'audit-log' ? 'active' : '' }}">
            <i class="bi bi-clock-history"></i><span>Audit log</span>
        </a>
    @endif

    <div class="sidebar-section-label">Account</div>
    <a href="{{ route('home') }}" target="_blank" rel="noopener noreferrer">
        <i class="bi bi-box-arrow-up-right"></i><span>View storefront</span>
    </a>

    @if($user)
        <div class="sidebar-user-card">
            <span class="sidebar-user-avatar">{{ $userInitial }}</span>
            <span style="min-width: 0;">
                <span class="sidebar-user-name">{{ $user->name ?: $user->email }}</span>
                <span class="sidebar-user-role">{{ $user->role }}</span>
            </span>
        </div>
    @endif

    <form method="POST" action="{{ route('admin.logout') }}" class="sidebar-logout">
        @csrf
        <button type="submit" class="btn btn-outline-light w-100">
            <i class="bi bi-box-arrow-right"></i> Log out
        </button>
    </form>
</nav>
