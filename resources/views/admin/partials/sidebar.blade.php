@php
    $user = auth()->user();
    $isAdmin = $user && $user->role === 'admin';
    $active = $active ?? '';
    $adminLogoPath = \App\Models\Setting::get('logo_path');
    $adminLogoText = \App\Models\Setting::get('logo_text', '/Namsa Florals');
    $userInitial = $user ? strtoupper(substr($user->name ?: $user->email, 0, 1)) : 'N';
@endphp
<nav class="sidebar-nav" aria-label="Administration">
    <div class="sidebar-brand">
        <div class="sidebar-brand-main">
            @if($adminLogoPath)
                <img src="{{ asset('storage/' . $adminLogoPath) }}" alt="{{ $adminLogoText }}" class="admin-sidebar-logo" style="height: 34px; width: auto; max-width: 128px; object-fit: contain; filter: brightness(0) invert(1);">
            @else
                <i class="bi bi-flower1" style="color: #f4a9c9; font-size: 1.3rem;"></i>
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
    <a href="{{ route('admin.orders.index') }}" class="{{ $active === 'orders' ? 'active' : '' }}">
        <i class="bi bi-bag-check"></i><span>Orders</span>
    </a>
    <a href="{{ route('admin.products.index') }}" class="{{ $active === 'products' ? 'active' : '' }}">
        <i class="bi bi-flower2"></i><span>Products</span>
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
    <a href="{{ route('admin.2fa.manage') }}" class="{{ $active === 'security' ? 'active' : '' }}">
        <i class="bi bi-shield-check"></i><span>Security</span>
    </a>
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
