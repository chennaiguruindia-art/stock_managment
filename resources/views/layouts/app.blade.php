<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>@yield('page-title', 'Stock Management') | Zyra Billing</title>

    <link rel="icon" href="{{ asset('logo/Zyra.jpeg') }}" type="image/jpeg">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css">

    <style>
        :root {
            color-scheme: light;

            --bg: #eef1f7;
            --surface: #ffffff;
            --surface-2: #f7f9fc;
            --surface-3: #eaeff7;
            --border: #dfe4ee;
            --border-strong: #c5cede;
            --muted: #66748b;
            --text: #0e1726;

            --accent: #1554d1;
            --accent-strong: #1043a8;
            --accent-soft: #e7eefe;

            --line: #dfe4ee;
            --soft: #f5f7fb;
            --ink-muted: #66748b;

            --ok: #0f9d58;
            --ok-soft: #e4f7ec;
            --warn: #d97706;
            --warn-soft: #fdf1e2;
            --bad: #dc2626;
            --bad-soft: #fdeaea;

            --navy-900: #0b1626;
            --navy-800: #0f1f36;
            --navy-700: #16294a;

            --shadow: 0 1px 2px rgba(16, 24, 40, .05);
            --shadow-md: 0 4px 14px rgba(16, 24, 40, .07);
            --radius: 10px;
        }

        * { box-sizing: border-box; }

        html, body {
            margin: 0;
            min-height: 100%;
            font-family: Inter, system-ui, -apple-system, BlinkMacSystemFont, "Segoe UI", sans-serif;
            background: var(--bg);
            color: var(--text);
        }

        body { overflow-x: hidden; }

        .num, .table, .fw-bold, b, strong { font-variant-numeric: tabular-nums; }

        .app { display: flex; min-height: 100vh; }

        /* ================= SIDEBAR ================= */
        .sidebar {
            width: 252px;
            min-width: 252px;
            background: linear-gradient(180deg, var(--navy-900) 0%, var(--navy-800) 60%, var(--navy-700) 100%);
            color: #cbd5e8;
            position: fixed;
            inset: 0 auto auto 0;
            display: flex;
            flex-direction: column;
            padding: 0 0 1rem;
            box-shadow: 2px 0 12px rgba(11, 22, 38, .18);
            z-index: 20;
            max-height: 100vh;
            overflow-y: auto;
            -webkit-overflow-scrolling: touch;
            border-right: 1px solid rgba(255, 255, 255, .06);
        }

        .sidebar .brand {
            padding: 1.1rem 1.15rem;
            margin-bottom: .35rem;
            display: flex;
            align-items: center;
            gap: .7rem;
            border-bottom: 1px solid rgba(255, 255, 255, .07);
        }

        .brand-logo {
            width: 38px;
            height: 38px;
            border-radius: 8px;
            object-fit: cover;
            border: 1px solid rgba(255, 255, 255, .18);
            flex-shrink: 0;
        }

        .brand-text .name {
            font-weight: 700;
            font-size: .98rem;
            color: #fff;
            letter-spacing: -.01em;
            line-height: 1.15;
        }

        .brand-text .tag {
            color: #7fa6e8;
            font-size: .62rem;
            text-transform: uppercase;
            letter-spacing: .14em;
            margin-top: .15rem;
            font-weight: 600;
        }

        .nav-section-label {
            padding: .95rem 1.25rem .35rem;
            font-size: .62rem;
            letter-spacing: .16em;
            text-transform: uppercase;
            color: #64789c;
            font-weight: 700;
        }

        .nav-link {
            display: flex;
            align-items: center;
            gap: .65rem;
            margin: 1px .6rem;
            padding: .5rem .7rem;
            color: #b3c1da;
            font-size: .875rem;
            font-weight: 500;
            text-decoration: none;
            border-radius: 8px;
            position: relative;
            transition: background .15s ease, color .15s ease;
        }

        .nav-link i {
            font-size: .98rem;
            min-width: 18px;
            text-align: center;
            color: #8fa6c9;
            transition: color .15s ease;
        }

        .nav-link:hover {
            background: rgba(255, 255, 255, .06);
            color: #fff;
        }

        .nav-link:hover i { color: #cbd5e8; }

        .nav-link.active {
            background: var(--accent);
            color: #fff;
            font-weight: 600;
            box-shadow: 0 2px 8px rgba(21, 84, 209, .35);
        }

        .nav-link.active i { color: #fff; }

        .sidebar-footer {
            margin-top: auto;
            padding: .9rem 1.25rem 0;
            font-size: .7rem;
            color: #7387ab;
            border-top: 1px solid rgba(255, 255, 255, .07);
            line-height: 1.5;
        }

        /* ================= MAIN / TOPBAR ================= */
        .main {
            margin-left: 252px;
            flex: 1;
            min-width: 0;
            display: flex;
            flex-direction: column;
            min-height: 100vh;
        }

        .topbar {
            display: flex;
            align-items: center;
            gap: 1rem;
            padding: .7rem 1.4rem;
            background: var(--surface);
            border-bottom: 1px solid var(--border);
            position: sticky;
            top: 0;
            z-index: 10;
            min-height: 60px;
        }

        .topbar > div:first-of-type { min-width: 0; }

        .mobile-menu-btn {
            display: none;
            border: 1px solid var(--border-strong);
            background: var(--surface);
            color: var(--text);
            width: 36px;
            height: 36px;
            border-radius: 8px;
            align-items: center;
            justify-content: center;
            font-size: 1.1rem;
        }

        .page-title {
            margin: 0;
            font-size: 1.12rem;
            font-weight: 700;
            letter-spacing: -.015em;
            color: var(--text);
            line-height: 1.2;
        }

        .page-subtitle {
            margin: .12rem 0 0;
            color: var(--muted);
            font-size: .8rem;
        }

        .topbar-meta {
            display: inline-flex;
            align-items: center;
            gap: .4rem;
            background: var(--surface-2);
            border: 1px solid var(--border);
            color: var(--muted);
            padding: .38rem .7rem;
            border-radius: 8px;
            font-size: .78rem;
            font-weight: 600;
            white-space: nowrap;
        }

        .topbar-cta {
            display: inline-flex;
            align-items: center;
            gap: .45rem;
            background: var(--accent);
            color: #fff;
            border: 0;
            padding: .45rem .95rem;
            border-radius: 8px;
            font-size: .85rem;
            font-weight: 600;
            text-decoration: none;
            white-space: nowrap;
            box-shadow: 0 2px 8px rgba(21, 84, 209, .28);
            transition: background .15s ease;
        }

        .topbar-cta:hover { background: var(--accent-strong); color: #fff; }

        .avatar-pill {
            display: inline-flex;
            gap: .4rem;
            align-items: center;
            background: var(--accent-soft);
            color: var(--accent-strong);
            border: 1px solid #cdddfb;
            padding: .38rem .7rem;
            border-radius: 8px;
            font-size: .8rem;
            font-weight: 600;
            white-space: nowrap;
        }

        .content {
            padding: 1.35rem 1.4rem 2rem;
            width: 100%;
            min-width: 0;
        }

        /* ================= PANELS ================= */
        .card-panel,
        .panel {
            border: 1px solid var(--border);
            border-radius: var(--radius);
            background: var(--surface);
            box-shadow: var(--shadow);
            padding: 1.15rem 1.25rem;
            height: 100%;
        }

        .section-title {
            font-size: .95rem;
            font-weight: 700;
            letter-spacing: -.01em;
            color: var(--text);
            margin-bottom: .9rem;
        }

        .panel .panel-title {
            font-size: .95rem;
            font-weight: 700;
            letter-spacing: -.01em;
            color: var(--text);
            margin-bottom: 1rem;
            display: flex;
            align-items: center;
            gap: .5rem;
        }

        .panel .panel-title i { color: var(--accent); }

        /* ================= TABLES ================= */
        .table {
            --bs-table-bg: transparent;
            font-size: .865rem;
            margin-bottom: 0;
        }

        .table > thead > tr > th {
            background: var(--surface-2);
            color: var(--muted);
            font-size: .68rem;
            text-transform: uppercase;
            letter-spacing: .07em;
            font-weight: 700;
            border-bottom: 1px solid var(--border);
            border-top: 0;
            padding: .6rem .7rem;
            white-space: nowrap;
            vertical-align: middle;
        }

        .table > tbody > tr > td,
        .table > tfoot > tr > td {
            border-top: 1px solid var(--border);
            border-bottom: 0;
            padding: .65rem .7rem;
            vertical-align: middle;
            color: var(--text);
        }

        .table > tbody > tr:hover > td { background: #f6f9fe; }

        .table-responsive { border: 1px solid var(--border); border-radius: var(--radius); background: var(--surface); }
        .table-responsive > .table { margin-bottom: 0; }

        /* ================= CHIPS / CODES ================= */
        .chip-sku,
        .chip-barcode,
        .order-chip,
        .inv-recent-chip,
        .bc-id,
        .bc-barcode {
            font-family: ui-monospace, "Cascadia Code", Consolas, monospace;
            font-size: .76rem;
            font-weight: 600;
            color: #24344f;
            background: var(--surface-3);
            border: 1px solid var(--border);
            border-radius: 6px;
            padding: .18rem .45rem;
            white-space: nowrap;
            display: inline-block;
        }

        .chip-barcode { color: var(--accent-strong); background: var(--accent-soft); border-color: #cdddfb; }

        /* ================= FORM CONTROLS ================= */
        .form-control,
        .form-select {
            border-radius: 8px;
            border-color: var(--border-strong);
            font-size: .875rem;
            padding: .5rem .7rem;
            background-color: var(--surface);
            color: var(--text);
        }

        .form-control::placeholder { color: #94a3b8; }

        .form-control:focus,
        .form-select:focus {
            border-color: var(--accent);
            box-shadow: 0 0 0 3px rgba(21, 84, 209, .13);
            background-color: var(--surface);
        }

        .form-label,
        .form-check-label {
            font-size: .82rem;
            font-weight: 600;
            color: #334155;
            margin-bottom: .35rem;
        }

        .input-group-text {
            border-radius: 8px;
            border-color: var(--border-strong);
            background: var(--surface-2);
            font-size: .85rem;
            color: var(--muted);
        }

        .search-box-input {
            border-radius: 8px;
            border: 1px solid var(--border-strong);
            padding: .5rem .9rem .5rem 2.2rem;
            background: var(--surface) url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='16' height='16' fill='%2366748b' viewBox='0 0 16 16'%3E%3Cpath d='M11.742 10.344a6.5 6.5 0 1 0-1.397 1.398h-.001c.03.04.062.078.098.115l3.85 3.85a1 1 0 0 0 1.415-1.414l-3.85-3.85a1.007 1.007 0 0 0-.115-.1zM12 6.5a5.5 5.5 0 1 1-11 0 5.5 5.5 0 0 1 11 0z'/%3E%3C/svg%3E") no-repeat .75rem center;
            font-size: .875rem;
            color: var(--text);
        }

        .search-box-input:focus {
            outline: none;
            border-color: var(--accent);
            box-shadow: 0 0 0 3px rgba(21, 84, 209, .13);
        }

        /* ================= BUTTONS ================= */
        .btn {
            border-radius: 8px;
            font-weight: 600;
            font-size: .865rem;
            padding: .44rem .9rem;
        }

        .btn-primary {
            background: var(--accent);
            border-color: var(--accent);
        }

        .btn-primary:hover,
        .btn-primary:focus {
            background: var(--accent-strong);
            border-color: var(--accent-strong);
        }

        .btn-outline-accent {
            color: var(--accent);
            border-color: #b9cdee;
            background: var(--surface);
        }

        .btn-outline-accent:hover {
            background: var(--accent);
            border-color: var(--accent);
            color: #fff;
        }

        /* ================= BADGES ================= */
        .badge {
            border-radius: 6px;
            font-weight: 600;
            font-size: .7rem;
            padding: .3em .55em;
            letter-spacing: .01em;
        }

        .badge.rounded-pill { border-radius: 6px !important; }

        .bg-success { background: var(--ok) !important; }
        .bg-danger { background: var(--bad) !important; }
        .bg-warning { background: var(--warn) !important; }

        /* ================= ALERTS ================= */
        .alert {
            border-radius: var(--radius);
            font-size: .875rem;
            border: 1px solid transparent;
            box-shadow: var(--shadow);
        }

        /* ================= MODALS ================= */
        .modal-content {
            border: 0;
            border-radius: 12px;
            box-shadow: 0 24px 60px rgba(16, 24, 40, .22);
            overflow: hidden;
        }

        .modal-header {
            border-bottom: 1px solid var(--border);
            background: var(--surface-2);
            padding: .9rem 1.1rem;
        }

        .modal-title { font-size: .98rem; font-weight: 700; }

        .modal-body { padding: 1.1rem; }

        .modal-footer {
            border-top: 1px solid var(--border);
            background: var(--surface-2);
            padding: .8rem 1.1rem;
        }

        /* ================= MISC ================= */
        .text-muted { color: var(--muted) !important; }

        a { color: var(--accent); }

        hr { border-color: var(--border); }

        .progress {
            height: 8px;
            border-radius: 999px;
            background: var(--surface-3);
        }

        .dropdown-menu {
            border: 1px solid var(--border);
            border-radius: 10px;
            box-shadow: var(--shadow-md);
            font-size: .875rem;
        }

        .dropdown-item { border-radius: 6px; }

        /* ================= RESPONSIVE ================= */
        @media (max-width: 992px) {
            .sidebar {
                position: fixed;
                transform: translateX(-100%);
                transition: transform .22s ease;
                z-index: 30;
            }

            .sidebar.show { transform: translateX(0); }

            .main { margin-left: 0; }

            .mobile-menu-btn { display: inline-flex; }
        }

        @media (max-width: 640px) {
            .topbar { flex-wrap: wrap; justify-content: space-between; }
            .content { padding: 1rem; }
        }

        @media print {
            .sidebar, .topbar, .mobile-menu-btn { display: none !important; }
            .main { margin-left: 0 !important; }
            .content { padding: 0 !important; }
            body { background: #fff; }
        }
    </style>
</head>
<body>
    <div class="app">
        <aside class="sidebar" id="sidebar">
            <div class="brand">
                <img src="{{ asset('logo/Zyra.jpeg') }}" alt="Zyra" class="brand-logo">
                <div class="brand-text">
                    <div class="name">Zyra</div>
                    <div class="tag">Billing Suite</div>
                </div>
            </div>

            <div class="nav-section-label">Overview</div>
            <a href="{{ route('dashboard') }}" class="nav-link {{ request()->routeIs('dashboard') ? 'active' : '' }}"><i class="bi bi-grid-1x2"></i> Dashboard</a>

            <div class="nav-section-label">Billing</div>
            <a href="{{ route('sell_pos') }}" class="nav-link {{ request()->routeIs('sell_pos') ? 'active' : '' }}"><i class="bi bi-cart3"></i> Sell / POS</a>
            <a href="{{ route('invoice') }}" class="nav-link {{ request()->routeIs('invoice*') ? 'active' : '' }}"><i class="bi bi-receipt-cutoff"></i> Invoices</a>
            <a href="{{ route('sales_history') }}" class="nav-link {{ request()->routeIs('sales_history') ? 'active' : '' }}"><i class="bi bi-clock-history"></i> Sales history</a>
            <a href="{{ route('return_product') }}" class="nav-link {{ request()->routeIs('return_product') ? 'active' : '' }}"><i class="bi bi-arrow-counterclockwise"></i> Returns</a>

            <div class="nav-section-label">Inventory</div>
            <a href="{{ route('add_product') }}" class="nav-link {{ request()->routeIs('add_product') ? 'active' : '' }}"><i class="bi bi-plus-square"></i> Add product</a>
            <a href="{{ route('stock_management') }}" class="nav-link {{ request()->routeIs('stock_management') ? 'active' : '' }}"><i class="bi bi-box-seam"></i> Stock management</a>
            <a href="{{ route('barcodes') }}" class="nav-link {{ request()->routeIs('barcodes*') || request()->routeIs('barcode_detail*') ? 'active' : '' }}"><i class="bi bi-upc-scan"></i> Product barcode</a>

            <div class="nav-section-label">Insights</div>
            <a href="{{ route('report') }}" class="nav-link {{ request()->routeIs('report') ? 'active' : '' }}"><i class="bi bi-bar-chart-line"></i> Reports</a>

            <div class="nav-section-label">Account</div>
            <a href="{{ route('logout') }}" class="nav-link {{ request()->routeIs('logout') ? 'active' : '' }}"><i class="bi bi-box-arrow-right"></i> Logout</a>

            <div class="sidebar-footer">
                Zyra Billing Suite &middot; inventory, POS &amp; invoicing in one terminal.
            </div>
        </aside>

        <div class="main">
            <header class="topbar">
                <button class="mobile-menu-btn" id="sidebarToggle"><i class="bi bi-list"></i></button>
                <div>
                    <h1 class="page-title">@yield('page-title', 'Dashboard')</h1>
                    @hasSection('page-subtitle')
                        <p class="page-subtitle">@yield('page-subtitle')</p>
                    @endif
                </div>
                <div class="ms-auto d-flex align-items-center gap-2 flex-wrap">
                    <span class="topbar-meta"><i class="bi bi-calendar3"></i> {{ now()->format('d M Y') }}</span>
                    <a href="{{ route('sell_pos') }}" class="topbar-cta"><i class="bi bi-plus-lg"></i> New bill</a>
                    <span class="avatar-pill"><i class="bi bi-person-circle"></i> Admin</span>
                </div>
            </header>

            <main class="content">
                @yield('content')
            </main>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        const sidebar = document.getElementById('sidebar');
        const toggle = document.getElementById('sidebarToggle');

        toggle?.addEventListener('click', () => {
            sidebar.classList.toggle('show');
        });

        document.addEventListener('click', event => {
            if (!sidebar.contains(event.target) && !toggle.contains(event.target) && window.innerWidth < 992) {
                sidebar.classList.remove('show');
            }
        });
    </script>
    @stack('scripts')
</body>
</html>
