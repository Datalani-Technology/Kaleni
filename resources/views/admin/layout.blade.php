<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, viewport-fit=cover">
    <meta name="theme-color" content="#211a20">
    <title>@yield('title', 'Admin') - /Namsa Florals</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Manrope:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link href="{{ asset('vendor/bootstrap/bootstrap.min.css') }}" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('vendor/bootstrap-icons/bootstrap-icons.css') }}">
    <style>
        :root { --admin-accent: #d63384; }
        body { font-family: 'Manrope', 'Segoe UI', Arial, sans-serif; }
        h1, h2, h3, h4, h5, h6 { font-family: 'Manrope', 'Segoe UI', Arial, sans-serif; }
        .admin-sidebar {
            min-height: 100vh;
            background: linear-gradient(180deg, #1a1a1a 0%, #2d2d2d 100%);
            color: #eee;
        }
        .admin-sidebar a {
            color: #d8d3d6;
            text-decoration: none;
            padding: 12px 16px;
            display: flex;
            align-items: center;
            border-radius: 8px;
            margin: 4px 0;
            min-height: 44px;
            border-left: 3px solid transparent;
            -webkit-tap-highlight-color: transparent;
            transition: background 0.2s, border-color 0.2s, color 0.2s;
        }
        .admin-sidebar a i { margin-right: 10px; }
        .admin-sidebar a:hover { background: rgba(214, 51, 132, 0.14); color: #fff; }
        .admin-sidebar a.active {
            background: rgba(214, 51, 132, 0.18);
            border-left-color: var(--admin-accent);
            color: #fff;
        }
        .btn-primary {
            background-color: var(--admin-accent) !important;
            border-color: var(--admin-accent) !important;
        }
        .btn-primary:hover { background-color: #ad2765 !important; border-color: #ad2765 !important; }
        .btn-outline-primary {
            color: var(--admin-accent) !important;
            border-color: var(--admin-accent) !important;
        }
        .btn-outline-primary:hover { background-color: var(--admin-accent) !important; color: #fff !important; }
        a { color: var(--admin-accent); }

        /* Mobile */
        .admin-mobile-header {
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            min-height: 56px;
            padding: env(safe-area-inset-top, 0) max(12px, env(safe-area-inset-right)) 0 max(12px, env(safe-area-inset-left));
            background: linear-gradient(90deg, #1a1a1a 0%, #2d2d2d 100%);
            color: #eee;
            display: flex;
            align-items: center;
            z-index: 1040;
            box-shadow: 0 2px 8px rgba(0,0,0,0.15);
        }
        .admin-mobile-header .admin-menu-btn {
            width: 44px;
            min-width: 44px;
            height: 44px;
            border: none;
            background: rgba(255,255,255,0.15);
            color: #eee;
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.35rem;
            margin-right: 12px;
            -webkit-tap-highlight-color: transparent;
        }
        .admin-mobile-header .admin-menu-btn:hover { background: rgba(255,255,255,0.25); color: #fff; }
        .admin-mobile-header h1 {
            font-size: 1.1rem;
            font-weight: 600;
            margin: 0;
        }
        .admin-sidebar-wrap {
            position: static;
        }
        .admin-overlay {
            position: fixed;
            inset: 0;
            background: rgba(0,0,0,0.4);
            z-index: 1038;
            opacity: 0;
            visibility: hidden;
            transition: opacity 0.2s, visibility 0.2s;
        }
        .admin-overlay.show {
            opacity: 1;
            visibility: visible;
        }
        .admin-main-wrap {
            padding-top: 0;
        }
        .admin-sidebar-close {
            display: none;
        }
        /* Desktop: sidebar stays fully reachable and scrolls independently of
           long content pages instead of scrolling away with the page. */
        .admin-sidebar-wrap {
            position: sticky;
            top: 0;
            align-self: flex-start;
            max-height: 100vh;
            overflow-y: auto;
        }

        @media (max-width: 991.98px) {
            .admin-main-wrap {
                padding-top: calc(56px + env(safe-area-inset-top, 0px));
                min-height: 100vh;
                padding-left: max(12px, env(safe-area-inset-left));
                padding-right: max(12px, env(safe-area-inset-right));
                padding-bottom: env(safe-area-inset-bottom, 0);
            }
            .admin-sidebar-wrap {
                position: fixed;
                top: 0;
                left: 0;
                bottom: 0;
                width: min(300px, 85vw);
                z-index: 1050;
                transform: translateX(-100%);
                transition: transform 0.25s ease;
                overflow-y: auto;
                -webkit-overflow-scrolling: touch;
                padding-left: env(safe-area-inset-left, 0);
            }
            body.admin-sidebar-open .admin-sidebar-wrap { transform: translateX(0); }
            body.admin-sidebar-open { overflow: hidden; }
            .admin-sidebar-close {
                display: flex;
                align-items: center;
                justify-content: center;
                width: 44px;
                min-width: 44px;
                height: 44px;
                margin: 12px 16px 16px;
                border: none;
                background: rgba(255,255,255,0.15);
                color: #eee;
                border-radius: 10px;
                -webkit-tap-highlight-color: transparent;
            }
            .admin-sidebar-close:hover { background: rgba(255,255,255,0.25); color: #fff; }
            .admin-sidebar .sidebar-brand { padding: 8px 16px 12px; }
            .admin-sidebar a { padding: 14px 16px; min-height: 48px; }
            .admin-content .card-body { padding: 1rem; }
            .admin-content .table { font-size: 0.875rem; }
            .admin-content .btn { min-height: 44px; padding-top: 0.5rem; padding-bottom: 0.5rem; }
        }

        @media (max-width: 575.98px) {
            .admin-mobile-header h1 { font-size: 1rem; }
            .admin-main-wrap { padding-left: 12px; padding-right: 12px; padding-top: calc(56px + env(safe-area-inset-top, 0px)); }
            .admin-content .row.g-3 > [class*="col-"] { flex: 0 0 100%; max-width: 100%; }
            .admin-content .d-flex.flex-wrap { flex-direction: column; align-items: stretch !important; }
            .admin-content .d-flex.flex-wrap .btn { margin-bottom: 0.5rem; width: 100%; }
        }

        /* Mobile-friendly forms: prevent zoom on focus (iOS), touch-friendly inputs */
        @media (max-width: 991.98px) {
            .admin-content .form-control,
            .admin-content .form-select {
                font-size: 16px !important;
                min-height: 44px;
            }
            .admin-content .form-control::placeholder { opacity: 0.7; }
            .admin-content .btn { min-height: 44px; }
            .admin-content label.form-check-label { padding-left: 0.5rem; }
            .admin-content .form-check-input { width: 1.25em; height: 1.25em; margin-top: 0.15em; }
        }
        @media (max-width: 575.98px) {
            .admin-filters .form-control,
            .admin-filters .form-select { width: 100%; }
            .admin-page-actions .btn { flex: 1 1 100%; }
        }

        .admin-content .table-responsive { -webkit-overflow-scrolling: touch; }

        /* Compact pagination (avoid large chevrons, wrap nicely) */
        .admin-content .pagination { flex-wrap: wrap; gap: 0.25rem; }
        .admin-content .pagination .page-link { padding: 0.35rem 0.6rem; font-size: 0.875rem; }

        /* Mobile: table rows as cards */
        @media (max-width: 767.98px) {
            .admin-table-cards thead { display: none; }
            .admin-table-cards tbody tr { display: block; border: 1px solid #dee2e6; margin-bottom: 1rem; border-radius: 10px; overflow: hidden; }
            .admin-table-cards tbody td { display: block; border: none; padding: 0.6rem 1rem; word-break: break-word; }
            .admin-table-cards tbody td::before { content: attr(data-label); font-weight: 600; display: inline-block; min-width: 5.5rem; }
            .admin-table-cards tbody td[data-label=""]::before { display: none; }
            .admin-table-cards tbody td[data-label=""] { padding-top: 0.75rem; }
            .admin-table-cards .table-responsive { overflow: visible; }
            .admin-table-cards .admin-product-img-wrap,
            .admin-table-cards .admin-product-img-placeholder { min-width: 48px; min-height: 48px; }
            .admin-content .card-body.p-0 { padding: 0 !important; }
            .admin-content .card { overflow: hidden; }
            .admin-table-cards .btn { min-height: 40px; padding: 0.4rem 0.75rem; }
        }
    </style>
    <link rel="stylesheet" href="{{ asset('css/admin.css') }}">
    @stack('styles')
</head>
<body>
    <header class="admin-mobile-header d-lg-none">
        <button type="button" class="admin-menu-btn" id="adminMenuBtn" aria-label="Open menu">
            <i class="bi bi-list"></i>
        </button>
        <h1>@yield('title', 'Admin')</h1>
    </header>
    <div class="admin-overlay d-lg-none" id="adminOverlay" aria-hidden="true"></div>

    <div class="container-fluid">
        <div class="row">
            <div class="col-12 col-lg-2 admin-sidebar-wrap px-0" id="adminSidebarWrap">
                <div class="admin-sidebar p-4">
                    <button type="button" class="admin-sidebar-close d-md-none" id="adminSidebarClose" aria-label="Close menu">
                        <i class="bi bi-x-lg"></i>
                    </button>
                    @include('admin.partials.sidebar', ['active' => trim(strip_tags((string) ($__env->yieldContent('sidebar_active') ?? ''))) ?: ''])
                </div>
            </div>
            <div class="col-12 col-lg-10 admin-main-wrap">
                <main class="p-4 admin-content">
                    @if(session('success'))
                        <div class="alert alert-success alert-dismissible fade show">
                            {{ session('success') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    @endif
                    @if(session('error'))
                        <div class="alert alert-danger alert-dismissible fade show">
                            {{ session('error') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    @endif
                    @if(session('warning'))
                        <div class="alert alert-warning alert-dismissible fade show">
                            {{ session('warning') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    @endif
                    @yield('content')
                </main>
            </div>
        </div>
    </div>

    <script src="{{ asset('vendor/bootstrap/bootstrap.bundle.min.js') }}"></script>
    <script>
        // Opens a document (invoice/report) in a blank tab without ever navigating the
        // visible address bar to the real admin URL — fetched in the background and
        // written into an already-open blank window instead of following the link.
        function openAdminDocument(url) {
            var win = window.open('', '_blank');
            if (!win) { window.location.href = url; return; }
            win.document.write('<p style="font-family:sans-serif;padding:40px;color:#666;">Loading…</p>');
            fetch(url, { credentials: 'same-origin' })
                .then(function (r) { return r.text(); })
                .then(function (html) {
                    win.document.open();
                    win.document.write(html);
                    win.document.close();
                })
                .catch(function () {
                    win.document.body.innerHTML = '<p style="font-family:sans-serif;padding:40px;color:#c00;">Failed to load document.</p>';
                });
        }
        (function() {
            var btn = document.getElementById('adminMenuBtn');
            var closeBtn = document.getElementById('adminSidebarClose');
            var overlay = document.getElementById('adminOverlay');
            var sidebar = document.getElementById('adminSidebarWrap');
            function openMenu() {
                document.body.classList.add('admin-sidebar-open');
                if (overlay) overlay.classList.add('show');
            }
            function closeMenu() {
                document.body.classList.remove('admin-sidebar-open');
                if (overlay) overlay.classList.remove('show');
            }
            if (btn) btn.addEventListener('click', openMenu);
            if (closeBtn) closeBtn.addEventListener('click', closeMenu);
            if (overlay) overlay.addEventListener('click', closeMenu);
            document.querySelectorAll('.admin-sidebar a:not([target="_blank"])').forEach(function(a) {
                a.addEventListener('click', closeMenu);
            });
        })();
    </script>
    @stack('scripts')
</body>
</html>
