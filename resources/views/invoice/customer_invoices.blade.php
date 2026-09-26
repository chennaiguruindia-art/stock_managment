<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=5.0">
    <title>Customer Invoices | Zyra Billing</title>

    <link rel="icon" href="{{ asset('logo/Zyra.jpeg') }}" type="image/jpeg">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css">

    <style>
        :root {
            --bg: #f4f6fb;
            --surface: #ffffff;
            --surface-subtle: #f8fafc;
            --border: #e2e8f0;
            --border-focus: #1554d1;
            --text-main: #0f172a;
            --text-muted: #64748b;
            --accent: #1554d1;
            --accent-hover: #1043a8;
            --accent-soft: #eff4fe;
            --ok: #059669;
            --ok-soft: #ecfdf5;
            --card-shadow: 0 4px 20px -2px rgba(15, 23, 42, 0.06), 0 2px 6px -1px rgba(15, 23, 42, 0.04);
            --radius-lg: 16px;
            --radius-md: 12px;
            --radius-sm: 8px;
        }

        body {
            font-family: 'Inter', system-ui, -apple-system, sans-serif;
            background-color: var(--bg);
            color: var(--text-main);
            min-height: 100vh;
            display: flex;
            flex-direction: column;
            margin: 0;
            padding: 0;
            -webkit-font-smoothing: antialiased;
        }

        /* Top Header */
        .portal-header {
            background: var(--surface);
            border-bottom: 1px solid var(--border);
            padding: 0.85rem 1.25rem;
            position: sticky;
            top: 0;
            z-index: 50;
            box-shadow: 0 1px 3px rgba(0,0,0,0.03);
        }

        .portal-brand {
            display: flex;
            align-items: center;
            gap: 0.75rem;
            text-decoration: none;
            color: inherit;
        }

        .portal-logo {
            width: 40px;
            height: 40px;
            border-radius: var(--radius-sm);
            object-fit: cover;
            border: 1px solid var(--border);
        }

        .portal-title {
            font-weight: 800;
            font-size: 1.15rem;
            line-height: 1.2;
            color: var(--text-main);
            letter-spacing: -0.02em;
        }

        .portal-badge {
            font-size: 0.72rem;
            text-transform: uppercase;
            letter-spacing: 0.08em;
            color: var(--accent);
            font-weight: 700;
        }

        /* Main Container */
        .portal-container {
            width: 100%;
            max-width: 820px;
            margin: 0 auto;
            padding: 1.75rem 1rem 3rem;
            flex: 1;
        }

        /* Search Card */
        .search-card {
            background: var(--surface);
            border: 1px solid var(--border);
            border-radius: var(--radius-lg);
            padding: 1.75rem 1.5rem;
            box-shadow: var(--card-shadow);
            margin-bottom: 1.5rem;
        }

        .search-title {
            font-size: 1.25rem;
            font-weight: 700;
            letter-spacing: -0.02em;
            margin-bottom: 0.35rem;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        .search-subtitle {
            font-size: 0.88rem;
            color: var(--text-muted);
            margin-bottom: 1.25rem;
        }

        .search-input-group {
            position: relative;
            display: flex;
            align-items: stretch;
            gap: 0.65rem;
        }

        @media (max-width: 640px) {
            .search-input-group {
                flex-direction: column;
                gap: 0.75rem;
            }
        }

        .mobile-input-wrapper {
            position: relative;
            flex: 1;
        }

        .mobile-input-icon {
            position: absolute;
            left: 1rem;
            top: 50%;
            transform: translateY(-50%);
            color: var(--text-muted);
            font-size: 1.15rem;
            pointer-events: none;
        }

        .mobile-input {
            width: 100%;
            padding: 0.85rem 1rem 0.85rem 2.85rem;
            font-size: 1.05rem;
            font-weight: 600;
            border: 2px solid var(--border);
            border-radius: var(--radius-md);
            background: var(--surface);
            color: var(--text-main);
            transition: all 0.2s ease;
        }

        .mobile-input:focus {
            outline: none;
            border-color: var(--accent);
            box-shadow: 0 0 0 4px rgba(21, 84, 209, 0.12);
        }

        .mobile-input::placeholder {
            color: #94a3b8;
            font-weight: 400;
            font-size: 0.95rem;
        }

        .btn-search {
            background: var(--accent);
            color: #ffffff;
            font-weight: 600;
            font-size: 0.98rem;
            padding: 0.85rem 1.6rem;
            border-radius: var(--radius-md);
            border: none;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 0.5rem;
            cursor: pointer;
            transition: background 0.15s ease, transform 0.05s ease;
            white-space: nowrap;
        }

        .btn-search:hover {
            background: var(--accent-hover);
            color: #ffffff;
        }

        .btn-search:active {
            transform: scale(0.98);
        }

        .btn-clear {
            background: var(--surface-subtle);
            color: var(--text-muted);
            border: 1px solid var(--border);
            border-radius: var(--radius-md);
            padding: 0.85rem 1.1rem;
            font-weight: 600;
            font-size: 0.92rem;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            transition: all 0.15s ease;
        }

        .btn-clear:hover {
            background: #e2e8f0;
            color: var(--text-main);
        }

        /* Customer Profile Summary */
        .profile-summary {
            background: linear-gradient(135deg, #1554d1, #0d3896);
            color: #ffffff;
            border-radius: var(--radius-lg);
            padding: 1.5rem;
            margin-bottom: 1.5rem;
            box-shadow: 0 8px 24px -4px rgba(21, 84, 209, 0.3);
        }

        .profile-top {
            display: flex;
            align-items: center;
            justify-content: space-between;
            flex-wrap: wrap;
            gap: 1rem;
            margin-bottom: 1.25rem;
        }

        .profile-meta-title {
            font-size: 0.78rem;
            text-transform: uppercase;
            letter-spacing: 0.1em;
            color: #bfdbfe;
            margin-bottom: 0.2rem;
        }

        .profile-name {
            font-size: 1.35rem;
            font-weight: 800;
            letter-spacing: -0.01em;
        }

        .profile-phone-pill {
            background: rgba(255, 255, 255, 0.18);
            backdrop-filter: blur(8px);
            padding: 0.4rem 0.9rem;
            border-radius: 999px;
            font-size: 0.88rem;
            font-weight: 600;
            display: inline-flex;
            align-items: center;
            gap: 0.4rem;
        }

        .profile-stats {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(140px, 1fr));
            gap: 0.85rem;
            border-top: 1px solid rgba(255, 255, 255, 0.15);
            padding-top: 1rem;
        }

        .stat-box {
            background: rgba(255, 255, 255, 0.1);
            padding: 0.75rem 1rem;
            border-radius: var(--radius-md);
        }

        .stat-label {
            font-size: 0.72rem;
            text-transform: uppercase;
            letter-spacing: 0.08em;
            color: #bfdbfe;
            margin-bottom: 0.25rem;
        }

        .stat-value {
            font-size: 1.25rem;
            font-weight: 800;
            line-height: 1.1;
        }

        /* Invoice Card Item */
        .invoice-card {
            background: var(--surface);
            border: 1px solid var(--border);
            border-radius: var(--radius-lg);
            padding: 1.35rem;
            margin-bottom: 1.15rem;
            box-shadow: var(--card-shadow);
            transition: transform 0.15s ease, box-shadow 0.15s ease, border-color 0.15s ease;
        }

        .invoice-card:hover {
            border-color: #cbd5e1;
            box-shadow: 0 8px 24px -2px rgba(15, 23, 42, 0.09);
        }

        .invoice-head {
            display: flex;
            align-items: center;
            justify-content: space-between;
            flex-wrap: wrap;
            gap: 0.65rem;
            margin-bottom: 1rem;
            padding-bottom: 0.85rem;
            border-bottom: 1px solid #f1f5f9;
        }

        .inv-chip {
            font-family: ui-monospace, SFMono-Regular, Menlo, Monaco, Consolas, monospace;
            font-weight: 700;
            font-size: 0.88rem;
            color: var(--accent);
            background: var(--accent-soft);
            border: 1px solid #c7d7fd;
            padding: 0.3rem 0.65rem;
            border-radius: var(--radius-sm);
            display: inline-flex;
            align-items: center;
            gap: 0.35rem;
        }

        .inv-date {
            font-size: 0.82rem;
            color: var(--text-muted);
            display: inline-flex;
            align-items: center;
            gap: 0.35rem;
        }

        .inv-body {
            margin-bottom: 1rem;
        }

        .items-preview-list {
            background: var(--surface-subtle);
            border: 1px solid #f1f5f9;
            border-radius: var(--radius-md);
            padding: 0.75rem 1rem;
            margin-bottom: 0.85rem;
        }

        .item-row {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 0.35rem 0;
            font-size: 0.86rem;
            border-bottom: 1px dashed #e2e8f0;
        }

        .item-row:last-child {
            border-bottom: none;
            padding-bottom: 0;
        }

        .item-row:first-child {
            padding-top: 0;
        }

        .item-name {
            font-weight: 500;
            color: var(--text-main);
            overflow: hidden;
            text-overflow: ellipsis;
            white-space: nowrap;
            max-width: 70%;
        }

        .item-qty {
            font-size: 0.78rem;
            color: var(--text-muted);
            margin-left: 0.35rem;
        }

        .item-price {
            font-weight: 600;
            color: #334155;
            font-variant-numeric: tabular-nums;
        }

        .inv-foot {
            display: flex;
            align-items: center;
            justify-content: space-between;
            flex-wrap: wrap;
            gap: 0.85rem;
            padding-top: 0.5rem;
        }

        .inv-total-label {
            font-size: 0.75rem;
            text-transform: uppercase;
            letter-spacing: 0.06em;
            color: var(--text-muted);
        }

        .inv-total-amount {
            font-size: 1.45rem;
            font-weight: 800;
            color: var(--text-main);
            line-height: 1.1;
        }

        .payment-pill {
            font-size: 0.75rem;
            font-weight: 600;
            padding: 0.25rem 0.6rem;
            border-radius: 999px;
            background: #e2e8f0;
            color: #475569;
            display: inline-block;
            margin-left: 0.5rem;
            vertical-align: middle;
        }

        .btn-view-invoice {
            background: var(--accent);
            color: #ffffff;
            font-weight: 600;
            font-size: 0.88rem;
            padding: 0.6rem 1.15rem;
            border-radius: var(--radius-md);
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 0.45rem;
            transition: all 0.15s ease;
        }

        .btn-view-invoice:hover {
            background: var(--accent-hover);
            color: #ffffff;
            box-shadow: 0 3px 10px rgba(21, 84, 209, 0.25);
        }

        /* Empty / Not Found States */
        .state-card {
            background: var(--surface);
            border: 1px dashed var(--border);
            border-radius: var(--radius-lg);
            padding: 3rem 1.5rem;
            text-align: center;
            box-shadow: var(--card-shadow);
        }

        .state-icon {
            width: 64px;
            height: 64px;
            border-radius: 50%;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            font-size: 1.9rem;
            margin-bottom: 1rem;
        }

        .state-icon.info {
            background: var(--accent-soft);
            color: var(--accent);
        }

        .state-icon.warning {
            background: #fef3c7;
            color: #d97706;
        }

        .state-title {
            font-size: 1.15rem;
            font-weight: 700;
            margin-bottom: 0.4rem;
        }

        .state-desc {
            font-size: 0.88rem;
            color: var(--text-muted);
            max-width: 420px;
            margin: 0 auto;
            line-height: 1.5;
        }

        /* Footer */
        .portal-footer {
            text-align: center;
            padding: 1.5rem 1rem;
            font-size: 0.78rem;
            color: var(--text-muted);
            border-top: 1px solid var(--border);
            background: var(--surface);
            margin-top: auto;
        }
    </style>
</head>
<body>

    <!-- Simple Responsive Navbar -->
    <header class="portal-header">
        <div class="d-flex align-items-center justify-content-between max-w-100" style="max-width: 820px; margin: 0 auto;">
            <a href="{{ route('customer_invoices') }}" class="portal-brand">
                <img src="{{ asset('logo/Zyra.jpeg') }}" alt="Zyra" class="portal-logo">
                <div>
                    <div class="portal-title">Zyra</div>
                    <div class="portal-badge"><i class="bi bi-receipt me-1"></i>Customer Invoices</div>
                </div>
            </a>
            @if ($searched && $mobile)
                <a href="{{ route('customer_invoices') }}" class="btn btn-sm btn-outline-secondary rounded-pill px-3" style="font-size: 0.82rem; font-weight: 600;">
                    <i class="bi bi-arrow-clockwise me-1"></i> New Search
                </a>
            @endif
        </div>
    </header>

    <!-- Main Content Area -->
    <main class="portal-container">

        <!-- Search Form Card -->
        <div class="search-card">
            <h1 class="search-title">
                <i class="bi bi-phone text-primary"></i>
                Find Invoices by Mobile Number
            </h1>
            <p class="search-subtitle">
                Enter any customer mobile number to view all previous bills, receipts, and order histories.
            </p>

            <form method="GET" action="{{ route('customer_invoices') }}" id="searchForm">
                <div class="search-input-group">
                    <div class="mobile-input-wrapper">
                        <i class="bi bi-telephone mobile-input-icon"></i>
                        <input
                            type="tel"
                            inputmode="numeric"
                            autocomplete="tel"
                            name="mobile"
                            id="mobileInput"
                            class="mobile-input"
                            placeholder="Enter 10-digit mobile number (e.g. 9361888174)"
                            value="{{ $mobile }}"
                            required
                            autofocus
                        >
                    </div>
                    <button type="submit" class="btn-search">
                        <i class="bi bi-search"></i>
                        Search Invoices
                    </button>
                    @if ($searched && $mobile)
                        <a href="{{ route('customer_invoices') }}" class="btn-clear" title="Clear search">
                            <i class="bi bi-x-lg me-1"></i> Clear
                        </a>
                    @endif
                </div>
            </form>
        </div>

        @if ($searched)
            @if ($orders->isNotEmpty())
                <!-- Customer Profile & Summary -->
                <div class="profile-summary">
                    <div class="profile-top">
                        <div>
                            <div class="profile-meta-title"><i class="bi bi-person-badge me-1"></i>Customer Details</div>
                            <div class="profile-name">
                                {{ $customerName ?: 'Customer' }}
                            </div>
                        </div>
                        <div class="profile-phone-pill">
                            <i class="bi bi-telephone-fill"></i>
                            +91 {{ $mobile }}
                        </div>
                    </div>

                    <div class="profile-stats">
                        <div class="stat-box">
                            <div class="stat-label">Total Invoices</div>
                            <div class="stat-value">{{ $totalOrders }}</div>
                        </div>
                        <div class="stat-box">
                            <div class="stat-label">Total Amount</div>
                            <div class="stat-value">₹{{ number_format($totalSpend, 2) }}</div>
                        </div>
                        <div class="stat-box">
                            <div class="stat-label">Latest Bill</div>
                            <div class="stat-value" style="font-size: 1.05rem;">
                                {{ $orders->first()?->created_at ? $orders->first()->created_at->format('d M Y') : '—' }}
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Invoice Results Header -->
                <div class="d-flex align-items-center justify-content-between mb-3 px-1">
                    <div class="fw-bold" style="font-size: 1rem; color: var(--text-main);">
                        All Invoices ({{ $orders->count() }})
                    </div>
                    <div class="text-muted" style="font-size: 0.8rem;">
                        Sorted by most recent
                    </div>
                </div>

                <!-- Invoices List -->
                @foreach ($orders as $order)
                    <div class="invoice-card">
                        <div class="invoice-head">
                            <div class="d-flex align-items-center gap-2 flex-wrap">
                                <span class="inv-chip">
                                    <i class="bi bi-receipt"></i>
                                    {{ $order->order_id }}
                                </span>
                                @if (!empty($order->payment_mode))
                                    <span class="payment-pill">
                                        <i class="bi bi-credit-card me-1"></i>{{ ucfirst($order->payment_mode) }}
                                    </span>
                                @endif
                            </div>
                            <span class="inv-date">
                                <i class="bi bi-calendar3"></i>
                                {{ $order->created_at ? $order->created_at->format('d M Y, h:i A') : '—' }}
                            </span>
                        </div>

                        <div class="inv-body">
                            @if ($order->items && $order->items->isNotEmpty())
                                <div class="items-preview-list">
                                    @foreach ($order->items as $item)
                                        <div class="item-row">
                                            <div class="item-name" title="{{ $item->product_name }}">
                                                <i class="bi bi-box me-1 text-muted"></i>
                                                {{ $item->product_name }}
                                                <span class="item-qty">&times; {{ $item->qty }}</span>
                                            </div>
                                            <div class="item-price">
                                                ₹{{ number_format($item->total ?: ($item->price * $item->qty), 2) }}
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            @endif
                        </div>

                        <div class="inv-foot">
                            <div>
                                <div class="inv-total-label">Grand Total</div>
                                <div class="inv-total-amount">
                                    ₹{{ number_format($order->total, 2) }}
                                </div>
                            </div>
                            <a
                                href="{{ route('view_order_invoice', $order->order_id) }}"
                                target="_blank"
                                class="btn-view-invoice"
                            >
                                <i class="bi bi-printer"></i>
                                View &amp; Print Bill
                            </a>
                        </div>
                    </div>
                @endforeach

            @else
                <!-- No Invoices Found State -->
                <div class="state-card">
                    <div class="state-icon warning">
                        <i class="bi bi-exclamation-triangle"></i>
                    </div>
                    <div class="state-title">No Invoices Found</div>
                    <p class="state-desc">
                        We couldn't find any bills registered with mobile number <strong>"{{ $mobile }}"</strong>.
                        Please check the number or verify with the store counter.
                    </p>
                    <div class="mt-3">
                        <a href="{{ route('customer_invoices') }}" class="btn btn-outline-primary btn-sm rounded-pill px-3">
                            <i class="bi bi-arrow-repeat me-1"></i> Try Another Mobile Number
                        </a>
                    </div>
                </div>
            @endif

        @else
            <!-- Initial State (Before Search) -->
            <div class="state-card">
                <div class="state-icon info">
                    <i class="bi bi-receipt-cutoff"></i>
                </div>
                <div class="state-title">Instant Bill Lookup</div>
                <p class="state-desc">
                    Enter your 10-digit mobile number above to search and download all past invoices, receipts, and order histories directly on any device.
                </p>
            </div>
        @endif

    </main>

    <!-- Simple Responsive Footer -->
    <footer class="portal-footer">
        <div>Zyra Billing &middot; Fast, Simple &amp; Responsive Invoicing Portal</div>
    </footer>

</body>
</html>
