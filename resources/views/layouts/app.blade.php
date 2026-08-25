<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>@yield('page-title', 'Stock Management') | Zyra Stock Management</title>

    <link rel="icon" href="{{ asset('logo/Zyra.jpeg') }}" type="image/jpeg">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css">

    <style>
        :root {
            color-scheme: light;
            --bg: #faf7f3;
            --surface: #ffffff;
            --surface-strong: #f3ede9;
            --border: #e8dfd8;
            --muted: #7a6f6b;
            --text: #2b1f1f;
            --accent: #6b1f2a;
            --accent-strong: #400000;
            --accent-soft: #f3e2e3;
            --gold: #c9a86a;
            --shadow: 0 18px 45px rgba(64, 0, 0, 0.10);
        }

        * {
            box-sizing: border-box;
        }

        html,
        body {
            margin: 0;
            min-height: 100%;
            font-family: Inter, system-ui, -apple-system, BlinkMacSystemFont, "Segoe UI", sans-serif;
            background: var(--bg);
            color: var(--text);
        }

        body {
            overflow-x: hidden;
        }

        .app {
            display: flex;
            min-height: 100vh;
        }

        .sidebar {
            width: 250px;
            min-width: 250px;
            background: linear-gradient(180deg, #2a000a 0%, #400000 100%);
            color: #f6eee9;
            position: fixed;
            inset: 0 auto auto 0;
            display: flex;
            flex-direction: column;
            padding: 1.5rem 0;
            box-shadow: 4px 0 30px rgba(40, 0, 0, 0.22);
            z-index: 20;
            max-height: 100vh;
            overflow-y: auto;
            -webkit-overflow-scrolling: touch;
        }

        .sidebar .brand {
            padding: 0 1.5rem;
            margin-bottom: 1rem;
            display: flex;
            align-items: center;
            gap: .9rem;
        }

        .brand-logo {
            width: 46px;
            height: 46px;
            border-radius: 14px;
            object-fit: cover;
            border: 2px solid rgba(255,255,255,.25);
            box-shadow: 0 6px 16px rgba(0,0,0,.35);
            flex-shrink: 0;
        }

        .brand-mark {
            width: 42px;
            height: 42px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            border-radius: 14px;
            background: rgba(255,255,255,0.08);
            color: #fff;
            font-size: 1.1rem;
            margin-bottom: .8rem;
        }

        .brand-text .name {
            font-weight: 800;
            letter-spacing: .02em;
            font-size: 1.12rem;
            color: #fff;
        }

        .brand-text .tag {
            color: #d8b97f;
            font-size: .72rem;
            text-transform: uppercase;
            letter-spacing: .16em;
            margin-top: .25rem;
            font-weight: 600;
        }

        .stitch {
            height: 1px;
            margin: 1rem 0;
            background: rgba(255,255,255,0.08);
        }

        .nav-section-label {
            padding: .75rem 1.5rem .35rem;
            font-size: .7rem;
            letter-spacing: .16em;
            text-transform: uppercase;
            color: #9e94a1;
        }

        .nav-link {
            display: flex;
            align-items: center;
            gap: .75rem;
            padding: .9rem 1.5rem;
            color: #d5cdd9;
            font-weight: 500;
            text-decoration: none;
            transition: background .18s ease, color .18s ease;
        }

        .nav-link i {
            font-size: 1.05rem;
            min-width: 20px;
            text-align: center;
        }

        .nav-link:hover,
        .nav-link.active {
            background: rgba(255, 255, 255, 0.08);
            color: #fff;
        }

        .sidebar-footer {
            margin-top: auto;
            padding: 1.2rem 1.5rem;
            font-size: .78rem;
            color: #aea1b5;
            border-top: 1px solid rgba(255,255,255,0.08);
            line-height: 1.5;
        }

        .main {
            margin-left: 250px;
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
            padding: 1.05rem 1.5rem;
            background: var(--surface);
            border-bottom: 1px solid var(--border);
            position: sticky;
            top: 0;
            z-index: 10;
            min-width: 0;
        }

        .topbar > div:first-of-type {
            min-width: 0;
        }

        .mobile-menu-btn {
            display: none;
            border: 1px solid var(--border);
            background: var(--surface);
            color: var(--text);
            width: 42px;
            height: 42px;
            border-radius: 12px;
            align-items: center;
            justify-content: center;
            font-size: 1.15rem;
        }

        .page-title {
            margin: 0;
            font-size: 1.5rem;
            font-weight: 700;
        }

        .page-subtitle {
            margin: .35rem 0 0;
            color: var(--muted);
            font-size: .95rem;
        }

        .content {
            padding: 1.5rem;
            width: 100%;
            min-width: 0;
        }

        .card-panel {
            border: 1px solid var(--border);
            border-radius: 22px;
            background: var(--surface);
            box-shadow: var(--shadow);
            padding: 1.5rem;
        }

        .section-title {
            font-size: 1.05rem;
            font-weight: 700;
            margin-bottom: 1rem;
        }

        .panel {
            border-radius: 20px;
            background: var(--surface);
            border: 1px solid #e8e5e0;
            box-shadow: 0 14px 34px rgba(36, 25, 35, .05);
            padding: 1.5rem;
            height: 100%;
        }

        .panel .panel-title {
            font-size: 1.05rem;
            font-weight: 700;
            margin-bottom: 1.15rem;
            display: flex;
            align-items: center;
            gap: .55rem;
        }

        .panel .panel-title i { color: var(--accent); }

        .chip-sku {
            font-family: ui-monospace, "Cascadia Code", Consolas, monospace;
            font-size: .82rem;
            font-weight: 600;
            color: #272327;
            background: #f2f0ec;
            border-radius: 6px;
            padding: .2rem .45rem;
            white-space: nowrap;
        }

        .chip-barcode {
            font-family: ui-monospace, "Cascadia Code", Consolas, monospace;
            font-size: .8rem;
            color: var(--accent);
            background: var(--accent-soft);
            border-radius: 6px;
            padding: .2rem .45rem;
            white-space: nowrap;
        }

        .search-box-input {
            border-radius: 12px;
            border: 1px solid #e5e2dc;
            padding: .55rem .9rem .55rem 2.4rem;
            background: #fbfaf8 url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='16' height='16' fill='%236f6d6a' viewBox='0 0 16 16'%3E%3Cpath d='M11.742 10.344a6.5 6.5 0 1 0-1.397 1.398h-.001c.03.04.062.078.098.115l3.85 3.85a1 1 0 0 0 1.415-1.414l-3.85-3.85a1.007 1.007 0 0 0-.115-.1zM12 6.5a5.5 5.5 0 1 1-11 0 5.5 5.5 0 0 1 11 0z'/%3E%3C/svg%3E") no-repeat .8rem center;
            width: 100%;
        }

        .avatar-pill {
            display: inline-flex;
            gap: .4rem;
            align-items: center;
            background: var(--accent-soft);
            color: var(--accent);
            padding: .45rem .8rem;
            border-radius: 999px;
            font-size: .85rem;
            font-weight: 600;
        }

        .card-panel .table thead {
            border-bottom: 1px solid var(--border);
        }

        .card-panel .table th,
        .card-panel .table td {
            border-top: 0;
            vertical-align: middle;
        }

        .card-panel .table tr:hover {
            background: #fcfbfa;
        }

        .btn-outline-accent {
            color: var(--accent);
            border-color: var(--accent);
        }

        .btn-outline-accent:hover {
            background: var(--accent);
            color: #fff;
        }

        @media (max-width: 992px) {
            .sidebar {
                position: fixed;
                transform: translateX(-100%);
                transition: transform .22s ease;
                z-index: 30;
            }

            .sidebar.show {
                transform: translateX(0);
            }

            .main {
                margin-left: 0;
            }

            .mobile-menu-btn {
                display: inline-flex;
            }
        }

        @media (max-width: 640px) {
            .topbar {
                flex-wrap: wrap;
                justify-content: space-between;
            }
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
                    <div class="tag">Stock Management</div>
                </div>
            </div>

            <a href="{{ route('dashboard') }}" class="nav-link {{ request()->routeIs('dashboard') ? 'active' : '' }}"><i class="bi bi-speedometer2"></i> Dashboard</a>
            <a href="{{ route('add_product') }}" class="nav-link {{ request()->routeIs('add_product') ? 'active' : '' }}"><i class="bi bi-plus-circle"></i> Add product</a>
            <a href="{{ route('stock_management') }}" class="nav-link {{ request()->routeIs('stock_management') ? 'active' : '' }}"><i class="bi bi-box-seam"></i> Stock management</a>
            <a href="{{ route('return_product') }}" class="nav-link {{ request()->routeIs('return_product') ? 'active' : '' }}"><i class="bi bi-arrow-counterclockwise"></i> Return</a>
            <a href="{{ route('sell_pos') }}" class="nav-link {{ request()->routeIs('sell_pos') ? 'active' : '' }}"><i class="bi bi-currency-dollar"></i> Sell / POS</a>
            <a href="{{ route('invoice') }}" class="nav-link {{ request()->routeIs('invoice*') ? 'active' : '' }}"><i class="bi bi-receipt"></i> Invoice</a>
            <a href="{{ route('sales_history') }}" class="nav-link {{ request()->routeIs('sales_history') ? 'active' : '' }}"><i class="bi bi-clock-history"></i> Sales history</a>
            <a href="{{ route('report') }}" class="nav-link {{ request()->routeIs('report') ? 'active' : '' }}"><i class="bi bi-bar-chart-line"></i> Report</a>

            <div class="nav-section-label">Account</div>
            <a href="{{ route('logout') }}" class="nav-link {{ request()->routeIs('logout') ? 'active' : '' }}"><i class="bi bi-box-arrow-right"></i> Logout</a>

            <div class="sidebar-footer">
                Zyra Stock Management — keep inventory, sales and reports in one place.
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
                <div class="ms-auto d-flex align-items-center gap-2">
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
