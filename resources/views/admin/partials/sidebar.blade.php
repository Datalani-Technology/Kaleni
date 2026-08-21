@php
    $user = auth()->user();
    $isAdmin = $user && $user->role === 'admin';
    $active = $active ?? '';
    $adminLogoPath = \App\Models\Setting::get('logo_path');
    $adminLogoText = \App\Models\Setting::get('logo_text', '/Namsa Florals');
@endphp
<nav class="sidebar-nav">
    <h4 class="sidebar-brand mb-4 d-flex align-items-center gap-2 flex-wrap">
        @if($adminLogoPath)
            <img src="{{ asset('storage/' . $adminLogoPath) }}" alt="{{ $adminLogoText }}" class="admin-sidebar-logo" style="height: 32px; width: auto; max-width: 140px; object-fit: contain; filter: brightness(0) invert(1);">
        @else
            <i class="bi bi-flower1" style="color: #eee;"></i>
        @endif
        <span style="color: #eee;">{{ $adminLogoText }}</span>
    </h4>
    <a href="{{ route('admin.dashboard') }}" class="{{ $active === 'dashboard' ? 'active' : '' }}">
        <i class="bi bi-speedometer2"></i> Dashboard
    </a>
    <a href="{{ route('admin.orders.index') }}" class="{{ $active === 'orders' ? 'active' : '' }}">
        <i class="bi bi-cart-check"></i> Orders
    </a>
    <a href="{{ route('admin.products.index') }}" class="{{ $active === 'products' ? 'active' : '' }}">
        <i class="bi bi-box"></i> Products
    </a>
    <a href="{{ route('admin.stock.index') }}" class="{{ $active === 'stock' ? 'active' : '' }}">
        <i class="bi bi-boxes"></i> Stock Count
    </a>
    <a href="{{ route('admin.analytics.index') }}" class="{{ $active === 'analytics' ? 'active' : '' }}">
        <i class="bi bi-graph-up"></i> Analytics
    </a>
    <a href="{{ route('admin.contacts.index') }}" class="{{ $active === 'contacts' ? 'active' : '' }}">
        <i class="bi bi-envelope"></i> Enquiries
    </a>
    @if($isAdmin)
        <a href="{{ route('admin.logo.edit') }}" class="{{ $active === 'logo' ? 'active' : '' }}">
            <i class="bi bi-image"></i> Logo
        </a>
        <a href="{{ route('admin.promotion.edit') }}" class="{{ $active === 'promotion' ? 'active' : '' }}">
            <i class="bi bi-tag"></i> Promotion Catalog
        </a>
        <a href="{{ route('admin.gallery.index') }}" class="{{ $active === 'gallery' ? 'active' : '' }}">
            <i class="bi bi-images"></i> Gallery
        </a>
        <a href="{{ route('admin.users.index') }}" class="{{ $active === 'users' ? 'active' : '' }}">
            <i class="bi bi-people"></i> Users
        </a>
    @endif
    <a href="{{ route('home') }}" target="_blank">
        <i class="bi bi-house"></i> View Site
    </a>
    <form method="POST" action="{{ route('admin.logout') }}" class="mt-4">
        @csrf
        <button type="submit" class="btn btn-outline-light w-100" style="border-color: rgba(255,255,255,0.4); color: #eee;">
            <i class="bi bi-box-arrow-right"></i> Logout
        </button>
    </form>
</nav>
