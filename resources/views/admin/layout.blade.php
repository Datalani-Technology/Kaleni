<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, viewport-fit=cover">
    <meta name="theme-color" content="#29211F">
    <title>@yield('title', 'Admin') - Kaleni Catering Services</title>
    @php
        $adminFaviconLogo = \App\Models\Setting::get('logo_path');
    @endphp
    @if($adminFaviconLogo)
        <link rel="icon" href="{{ asset('storage/' . $adminFaviconLogo) }}">
    @else
        <link rel="icon" type="image/x-icon" href="{{ asset('favicon.ico') }}">
        <link rel="icon" type="image/png" sizes="32x32" href="{{ asset('favicon-32x32.png') }}">
    @endif
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Manrope:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link href="{{ asset('vendor/bootstrap/bootstrap.min.css') }}" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('vendor/bootstrap-icons/bootstrap-icons.css') }}">
    <style>
        :root { --admin-accent: #680B1C; }
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
        .admin-sidebar a:hover { background: rgba(104, 11, 28, 0.14); color: #fff; }
        .admin-sidebar a.active {
            background: rgba(104, 11, 28, 0.18);
            border-left-color: var(--admin-accent);
            color: #fff;
        }
        .btn-primary {
            background-color: var(--admin-accent) !important;
            border-color: var(--admin-accent) !important;
        }
        .btn-primary:hover { background-color: #4A0814 !important; border-color: #4A0814 !important; }
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
            flex: 1;
            font-size: 1.1rem;
            font-weight: 600;
            margin: 0;
        }
        .admin-notification-btn {
            position: relative;
            width: 44px;
            min-width: 44px;
            height: 44px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            border: 1px solid rgba(255,255,255,.14);
            border-radius: 12px;
            background: rgba(255,255,255,.1);
            color: #fff;
            font-size: 1.08rem;
        }
        .admin-notification-btn:hover { background: rgba(255,255,255,.18); color: #fff; }
        .admin-notification-badge {
            position: absolute;
            top: -5px;
            right: -5px;
            min-width: 20px;
            height: 20px;
            padding: 0 5px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            border: 2px solid #29211F;
            border-radius: 999px;
            background: #680B1C;
            color: #fff;
            font-size: .64rem;
            font-weight: 800;
        }
        .admin-desktop-toolbar {
            min-height: 66px;
            display: flex;
            align-items: center;
            justify-content: flex-end;
            padding: 10px 1.5rem;
            border-bottom: 1px solid #ebe5e8;
            background: rgba(255,255,255,.9);
            backdrop-filter: blur(14px);
        }
        .admin-desktop-toolbar .admin-notification-btn {
            border-color: #e4dce1;
            background: #fff;
            color: #2b2027;
            box-shadow: 0 8px 22px rgba(33,26,32,.08);
        }
        .admin-desktop-toolbar .admin-notification-btn:hover { border-color: #680B1C; color: #680B1C; }
        .admin-desktop-toolbar .admin-notification-badge { border-color: #fff; }
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
<body data-admin-board="{{ trim(strip_tags((string) ($__env->yieldContent('sidebar_active') ?? ''))) ?: '' }}"
      data-count-total="{{ $adminNotifications['total'] ?? 0 }}"
      data-count-pending-order="{{ $adminNotifications['pending_order_count'] ?? 0 }}"
      data-count-pending-quick-order="{{ $adminNotifications['pending_quick_order_count'] ?? 0 }}"
      data-count-new-special-request="{{ $adminNotifications['new_special_request_count'] ?? 0 }}"
      data-count-new-quote-request="{{ $adminNotifications['new_quote_request_count'] ?? 0 }}">
    <header class="admin-mobile-header d-lg-none">
        <button type="button" class="admin-menu-btn" id="adminMenuBtn" aria-label="Open menu">
            <i class="bi bi-list"></i>
        </button>
        <h1>@yield('title', 'Admin')</h1>
        <button type="button" class="admin-notification-btn" data-bs-toggle="offcanvas" data-bs-target="#adminNotificationPanel" aria-controls="adminNotificationPanel" aria-label="Open notifications">
            <i class="bi bi-bell"></i>
            @if(($adminNotifications['total'] ?? 0) > 0)
                <span class="admin-notification-badge">{{ $adminNotifications['total'] > 99 ? '99+' : $adminNotifications['total'] }}</span>
            @endif
        </button>
    </header>
    <div class="admin-overlay d-lg-none" id="adminOverlay" aria-hidden="true"></div>

    @include('admin.partials.notification-centre')

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
                <div class="admin-desktop-toolbar d-none d-lg-flex">
                    <button type="button" class="admin-notification-btn" data-bs-toggle="offcanvas" data-bs-target="#adminNotificationPanel" aria-controls="adminNotificationPanel" aria-label="Open notifications">
                        <i class="bi bi-bell"></i>
                        @if(($adminNotifications['total'] ?? 0) > 0)
                            <span class="admin-notification-badge">{{ $adminNotifications['total'] > 99 ? '99+' : $adminNotifications['total'] }}</span>
                        @endif
                    </button>
                </div>
                <main class="p-4 admin-content">
                    @yield('content')
                </main>
            </div>
        </div>
    </div>

    <div class="toast-container position-fixed top-0 end-0 p-3 admin-toast-container" aria-live="polite" aria-atomic="true">
        @foreach(['success' => ['success', 'check-circle'], 'error' => ['danger', 'exclamation-octagon'], 'warning' => ['warning', 'exclamation-triangle'], 'status' => ['dark', 'info-circle']] as $flashKey => [$flashTone, $flashIcon])
            @if(session($flashKey))
                <div class="toast admin-toast border-0 text-bg-{{ $flashTone }}" role="status" data-bs-delay="6500" data-bs-autohide="{{ in_array($flashKey, ['error', 'warning']) ? 'false' : 'true' }}">
                    <div class="d-flex">
                        <div class="toast-body"><i class="bi bi-{{ $flashIcon }} me-2"></i>{{ session($flashKey) }}</div>
                        <button type="button" class="btn-close {{ $flashTone === 'warning' ? '' : 'btn-close-white' }} me-2 m-auto" data-bs-dismiss="toast" aria-label="Close"></button>
                    </div>
                </div>
            @endif
        @endforeach
    </div>

    <div class="modal fade" id="adminConfirmModal" tabindex="-1" aria-labelledby="adminConfirmTitle" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content admin-confirm-card">
                <div class="modal-body p-4 p-md-5">
                    <div class="admin-confirm-icon" id="adminConfirmIcon"><i class="bi bi-exclamation-triangle"></i></div>
                    <span class="admin-confirm-eyebrow">Please review</span>
                    <h2 class="h4 mb-2" id="adminConfirmTitle">Confirm this action</h2>
                    <p class="text-muted mb-2" id="adminConfirmMessage">This change may be difficult or impossible to reverse.</p>
                    <p class="admin-confirm-note mb-4"><i class="bi bi-shield-exclamation"></i> Some actions cannot be reversed.</p>
                    <div class="d-flex flex-column-reverse flex-sm-row justify-content-sm-end gap-2">
                        <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Go back</button>
                        <button type="button" class="btn btn-danger" id="adminConfirmButton">Yes, continue</button>
                    </div>
                </div>
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

        (function () {
            document.querySelectorAll('.admin-toast').forEach(function (element) {
                bootstrap.Toast.getOrCreateInstance(element).show();
            });

            var modalElement = document.getElementById('adminConfirmModal');
            var confirmButton = document.getElementById('adminConfirmButton');
            if (!modalElement || !confirmButton) return;

            var modal = bootstrap.Modal.getOrCreateInstance(modalElement);
            var pendingForm = null;
            var pendingSubmitter = null;
            var confirmedForms = new WeakSet();

            function confirmationCopy(form) {
                var copy = {
                    title: form.dataset.confirmTitle || 'Confirm this action',
                    message: form.dataset.confirmMessage || 'This item will be permanently removed. This cannot be undone.',
                    label: form.dataset.confirmLabel || 'Yes, continue',
                    variant: form.dataset.confirmVariant || 'danger'
                };

                if (form.dataset.confirmMode === 'booking-status') {
                    var noun = form.dataset.recordNoun || 'booking';
                    var originalStatus = form.dataset.originalStatus;
                    var newStatus = form.querySelector('[name="booking_status"]')?.value;
                    var paymentStatus = form.querySelector('[name="payment_status"]')?.value;
                    copy.title = newStatus === 'cancelled' && originalStatus !== 'cancelled' ? 'Cancel this ' + noun + '?' : 'Save ' + noun + ' changes?';
                    copy.message = newStatus === 'cancelled' && originalStatus !== 'cancelled'
                        ? 'The ' + noun + ' will be cancelled. The customer is not notified automatically, so contact them separately.'
                        : (noun.charAt(0).toUpperCase() + noun.slice(1)) + ' status and payment status will be updated immediately to ' + newStatus + ' / ' + paymentStatus + '.';
                    copy.label = newStatus === 'cancelled' ? 'Cancel ' + noun : 'Save changes';
                    copy.variant = newStatus === 'cancelled' ? 'danger' : 'warning';
                } else if (form.dataset.confirmMode === 'user-role') {
                    var originalRole = form.dataset.originalRole;
                    var newRole = form.querySelector('[name="role"]')?.value;
                    var passwordChanged = Boolean(form.querySelector('[name="password"]')?.value);
                    if (newRole === originalRole && !passwordChanged) return null;
                    copy.title = newRole !== originalRole ? 'Change this user’s access?' : 'Change this user’s password?';
                    copy.message = newRole !== originalRole
                        ? (newRole === 'admin' ? 'This grants full access to users, security settings, orders, and store management.' : 'This removes full admin access and limits the account to editor permissions.')
                        : 'The current password will stop working as soon as this is saved.';
                    copy.label = 'Update user';
                    copy.variant = 'warning';
                } else if (form.dataset.confirmMode === 'stock-adjust') {
                    var name = form.dataset.itemName || 'this menu item';
                    var current = parseInt(form.dataset.currentStock, 10) || 0;
                    var action = form.querySelector('[name="action"]')?.value;
                    var quantity = parseInt(form.querySelector('[name="quantity"]')?.value, 10) || 0;
                    var next = action === 'set' ? quantity : (action === 'add' ? current + quantity : current - quantity);
                    copy.title = 'Update inventory?';
                    copy.message = action === 'set'
                        ? 'Set “' + name + '” from ' + current + ' to ' + next + ' units? This overwrites the current count.'
                        : (action === 'add' ? 'Add ' + quantity + ' unit(s) to “' + name + '”? New stock: ' + next + '.' : 'Subtract ' + quantity + ' unit(s) from “' + name + '”? New stock: ' + next + '.');
                    if (next <= 0) copy.message += ' The menu item will be shown as out of stock.';
                    copy.label = 'Update stock';
                    copy.variant = next <= 0 ? 'danger' : 'warning';
                } else if (form.dataset.confirmMode === 'remove-logo') {
                    if (!form.querySelector('#remove_logo')?.checked) return null;
                    copy.title = 'Remove the custom logo?';
                    copy.message = 'The current uploaded logo will be removed and the storefront will revert to its default brand mark.';
                    copy.label = 'Remove logo';
                    copy.variant = 'danger';
                }

                return copy;
            }

            document.addEventListener('submit', function (event) {
                var form = event.target;
                if (!(form instanceof HTMLFormElement) || confirmedForms.has(form)) {
                    if (form instanceof HTMLFormElement) confirmedForms.delete(form);
                    return;
                }

                var method = form.querySelector('input[name="_method"]')?.value?.toUpperCase();
                var needsConfirmation = method === 'DELETE' || form.dataset.confirmMessage || form.dataset.confirmMode;
                if (!needsConfirmation) return;

                var copy = confirmationCopy(form);
                if (!copy) return;

                event.preventDefault();
                event.stopImmediatePropagation();
                pendingForm = form;
                pendingSubmitter = event.submitter || null;
                document.getElementById('adminConfirmTitle').textContent = copy.title;
                document.getElementById('adminConfirmMessage').textContent = copy.message;
                confirmButton.textContent = copy.label;
                confirmButton.className = 'btn btn-' + copy.variant;
                modal.show();
            }, true);

            confirmButton.addEventListener('click', function () {
                if (!pendingForm) return;
                var form = pendingForm;
                var submitter = pendingSubmitter;
                pendingForm = null;
                pendingSubmitter = null;
                confirmedForms.add(form);
                modal.hide();
                if (form.requestSubmit) {
                    if (submitter) form.requestSubmit(submitter);
                    else form.requestSubmit();
                }
                else form.submit();
            });

            modalElement.addEventListener('hidden.bs.modal', function () {
                pendingForm = null;
                pendingSubmitter = null;
            });
        })();

        (function () {
            // Keeps the bell (and, if closed, its dropdown content) current
            // without the admin needing to refresh an already-open page —
            // a new booking/quote/enquiry shows up within a few seconds.
            var pollUrl = '{{ route('admin.notifications.poll') }}';
            var buttons = document.querySelectorAll('.admin-notification-btn');
            var panel = document.getElementById('adminNotificationPanel');
            if (!buttons.length || !panel) return;

            function setBadges(total) {
                buttons.forEach(function (btn) {
                    var badge = btn.querySelector('.admin-notification-badge');
                    if (total > 0) {
                        if (!badge) {
                            badge = document.createElement('span');
                            badge.className = 'admin-notification-badge';
                            btn.appendChild(badge);
                        }
                        badge.textContent = total > 99 ? '99+' : String(total);
                    } else if (badge) {
                        badge.remove();
                    }
                });
            }

            // Boards where a fresh submission (booking, order, special
            // request, quote) — or, for the dashboard, anything the bell
            // tracks — must appear without the admin manually refreshing.
            // Reusing the same poll tick/response as the bell avoids a
            // second network call just for this.
            var boardCountKey = {
                dashboard: 'total',
                bookings: 'pending_order_count',
                orders: 'pending_quick_order_count',
                'special-requests': 'new_special_request_count',
                quotes: 'new_quote_request_count',
            }[document.body.dataset.adminBoard];
            var boardCountAttr = {
                total: 'countTotal',
                pending_order_count: 'countPendingOrder',
                pending_quick_order_count: 'countPendingQuickOrder',
                new_special_request_count: 'countNewSpecialRequest',
                new_quote_request_count: 'countNewQuoteRequest',
            }[boardCountKey];
            var lastKnownBoardCount = boardCountAttr ? parseInt(document.body.dataset[boardCountAttr] || '0', 10) : null;

            function poll() {
                if (document.hidden) return;
                fetch(pollUrl, { headers: { 'Accept': 'application/json' } })
                    .then(function (r) { return r.ok ? r.json() : null; })
                    .then(function (data) {
                        if (!data) return;
                        setBadges(data.total);
                        // Only swap the panel markup while it's closed, so we
                        // never yank content out from under an admin reading it.
                        if (!panel.classList.contains('show')) {
                            var temp = document.createElement('div');
                            temp.innerHTML = data.html;
                            var fresh = temp.firstElementChild;
                            if (fresh) panel.replaceWith(fresh);
                            panel = document.getElementById('adminNotificationPanel');
                        }

                        if (boardCountKey && typeof data[boardCountKey] === 'number' && data[boardCountKey] !== lastKnownBoardCount) {
                            location.reload();
                        }
                    })
                    .catch(function () { /* silent — next tick tries again */ });
            }

            setInterval(poll, 25000);
        })();
    </script>
    @stack('scripts')
</body>
</html>
