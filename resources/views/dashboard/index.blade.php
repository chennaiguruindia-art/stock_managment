@extends('layouts.app')

@section('page-title', 'Dashboard')
@section('page-subtitle', 'All-in-one terminal for inventory overview, POS billing, returns & sales history.')

@section('content')
    <style>
        /* Overview Hero & Cards */
        .dash-hero {
            background: linear-gradient(100deg, #0f1f36 0%, #122b52 55%, #1554d1 130%);
            border-radius: var(--radius, 10px);
            padding: 1.2rem 1.5rem;
            color: #fff;
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 1.25rem;
            flex-wrap: wrap;
            box-shadow: 0 4px 14px rgba(16, 24, 40, .12);
            border: 1px solid #16294a;
            margin-bottom: 1.25rem;
        }
        .dash-hero h2 { font-weight: 700; font-size: 1.15rem; margin: 0 0 .25rem; letter-spacing: -.01em; }
        .dash-hero p { margin: 0; color: #a8bcd8; font-size: .86rem; }
        .dash-hero .hero-badge {
            background: rgba(255, 255, 255, .08);
            border: 1px solid rgba(255, 255, 255, .16);
            border-radius: 8px;
            padding: .42rem .75rem;
            font-weight: 600;
            font-size: .8rem;
            color: #dbe6f7;
        }

        .stat-card {
            --tone: var(--accent, #1554d1);
            --tone-soft: var(--accent-soft, #e7eefe);
            position: relative;
            background: #fff;
            border: 1px solid var(--border, #dfe4ee);
            border-radius: var(--radius, 10px);
            padding: 1rem 1.1rem 1.05rem;
            color: var(--text, #0e1726);
            overflow: hidden;
            box-shadow: 0 1px 2px rgba(16, 24, 40, .05);
            min-height: 124px;
        }
        .stat-card::before {
            content: "";
            position: absolute;
            left: 0; top: 0; bottom: 0;
            width: 3px;
            background: var(--tone);
        }
        .stat-card .icon {
            position: absolute;
            right: 14px;
            top: 14px;
            width: 34px;
            height: 34px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            font-size: 1rem;
            border-radius: 8px;
            background: var(--tone-soft);
            color: var(--tone);
            opacity: 1;
            line-height: 1;
        }
        .stat-card .label {
            font-size: .66rem;
            text-transform: uppercase;
            letter-spacing: .1em;
            font-weight: 700;
            color: var(--muted, #66748b);
        }
        .stat-card .value {
            font-size: 1.9rem;
            font-weight: 800;
            line-height: 1.15;
            color: var(--text, #0e1726);
            margin-top: .3rem;
            font-variant-numeric: tabular-nums;
        }
        .stat-card .foot { font-size: .73rem; color: var(--muted, #66748b); margin-top: .3rem; }

        .stat-total { --tone: #1554d1; --tone-soft: #e7eefe; }
        .stat-stock { --tone: #0f9d58; --tone-soft: #e4f7ec; }
        .stat-low   { --tone: #d97706; --tone-soft: #fdf1e2; }
        .stat-out   { --tone: #dc2626; --tone-soft: #fdeaea; }

        .health-row { margin-bottom: 1rem; }
        .health-row:last-child { margin-bottom: 0; }
        .health-row .d-flex { font-size: .85rem; margin-bottom: .32rem; color: var(--muted, #66748b); }
        .health-row .d-flex b { font-weight: 700; color: var(--text, #0e1726); }
        .progress { height: 8px; border-radius: 999px; background: var(--surface-3, #eaeff7); }
        .progress-bar-ok    { background: #0f9d58; }
        .progress-bar-low   { background: #d97706; }
        .progress-bar-out   { background: #dc2626; }

        .quick-btn {
            display: flex;
            align-items: center;
            gap: .75rem;
            padding: .6rem .75rem;
            border-radius: 8px;
            border: 1px solid var(--border, #dfe4ee);
            background: #fff;
            color: var(--text, #0e1726);
            text-decoration: none;
            font-weight: 600;
            font-size: .875rem;
            transition: all .15s ease;
            cursor: pointer;
        }
        .quick-btn:hover {
            background: var(--accent, #1554d1);
            border-color: var(--accent, #1554d1);
            color: #fff;
        }
        .quick-btn i {
            width: 30px;
            height: 30px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            border-radius: 7px;
            background: var(--accent-soft, #e7eefe);
            color: var(--accent, #1554d1);
            font-size: .95rem;
        }
        .quick-btn:hover i { background: rgba(255, 255, 255, .18); color: #fff; }

        .stock-meter { width: 96px; height: 6px; border-radius: 999px; background: var(--surface-3, #eaeff7); }
        .stock-meter > div { height: 100%; border-radius: 999px; }
        .meter-ok  { background: #0f9d58; }
        .meter-low { background: #d97706; }
        .meter-out { background: #dc2626; }
    </style>

    @include('layouts.alerts')

    <div class="dash-hero">
        <div>
            <h2>Welcome back, Admin <i class="bi bi-emoji-smile"></i></h2>
            <p>Here is what your inventory and sales look like today.</p>
        </div>
        <div class="d-flex align-items-center gap-2 flex-wrap">
            <span class="hero-badge"><i class="bi bi-calendar3"></i> {{ now()->format('D, d M Y') }}</span>
            <span class="hero-badge"><i class="bi bi-box-seam"></i> {{ $totalProducts }} products tracked</span>
        </div>
    </div>

    <div class="row g-4 mb-4">
        <div class="col-lg-3 col-sm-6">
            <div class="stat-card stat-total">
                <i class="bi bi-grid-1x2-fill icon"></i>
                <div class="label">Total products</div>
                <div class="value">{{ $totalProducts }}</div>
                <div class="foot">Catalog items in the system</div>
            </div>
        </div>
        <div class="col-lg-3 col-sm-6">
            <div class="stat-card stat-stock">
                <i class="bi bi-box-seam icon"></i>
                <div class="label">Items in stock</div>
                <div class="value">{{ $totalStock }}</div>
                <div class="foot">Units across all products</div>
            </div>
        </div>
        <div class="col-lg-3 col-sm-6">
            <div class="stat-card stat-low">
                <i class="bi bi-exclamation-triangle icon"></i>
                <div class="label">Low stock</div>
                <div class="value">{{ $lowStock }}</div>
                <div class="foot">Under 5 units remaining</div>
            </div>
        </div>
        <div class="col-lg-3 col-sm-6">
            <div class="stat-card stat-out">
                <i class="bi bi-bag-x icon"></i>
                <div class="label">Out of stock</div>
                <div class="value">{{ $outOfStock }}</div>
                <div class="foot">Zero stock — needs attention</div>
            </div>
        </div>
    </div>

    <div class="row g-4">
        <div class="col-lg-4">
            <div class="panel">
                <div class="panel-title"><i class="bi bi-heart-pulse"></i> Stock health</div>

                <div class="health-row">
                    <div class="d-flex justify-content-between">
                        <span>Healthy</span>
                        <b>{{ $healthyStock }} <span class="text-muted fw-normal">({{ $totalProducts ? round($healthyStock / $totalProducts * 100) : 0 }}%)</span></b>
                    </div>
                    <div class="progress">
                        <div class="progress-bar progress-bar-ok" style="width: {{ $totalProducts ? $healthyStock / $totalProducts * 100 : 0 }}%"></div>
                    </div>
                </div>

                <div class="health-row">
                    <div class="d-flex justify-content-between">
                        <span>Low stock</span>
                        <b>{{ $lowStock }} <span class="text-muted fw-normal">({{ $totalProducts ? round($lowStock / $totalProducts * 100) : 0 }}%)</span></b>
                    </div>
                    <div class="progress">
                        <div class="progress-bar progress-bar-low" style="width: {{ $totalProducts ? $lowStock / $totalProducts * 100 : 0 }}%"></div>
                    </div>
                </div>

                <div class="health-row">
                    <div class="d-flex justify-content-between">
                        <span>Out of stock</span>
                        <b>{{ $outOfStock }} <span class="text-muted fw-normal">({{ $totalProducts ? round($outOfStock / $totalProducts * 100) : 0 }}%)</span></b>
                    </div>
                    <div class="progress">
                        <div class="progress-bar progress-bar-out" style="width: {{ $totalProducts ? $outOfStock / $totalProducts * 100 : 0 }}%"></div>
                    </div>
                </div>

                <hr class="my-4">

                <div class="section-title" style="font-size:.95rem; margin-bottom:.9rem;">Quick actions</div>
                <div class="d-grid gap-2">
                    <a href="{{ route('add_product') }}" class="quick-btn"><i class="bi bi-plus-circle"></i> Add new product</a>
                    <a href="{{ route('stock_management') }}" class="quick-btn"><i class="bi bi-box-seam"></i> Manage stock</a>
                    <a href="{{ route('sell_pos') }}" class="quick-btn"><i class="bi bi-currency-dollar"></i> Open POS Terminal</a>
                    <a href="{{ route('return_product') }}" class="quick-btn"><i class="bi bi-arrow-counterclockwise"></i> Return Items</a>
                    <a href="{{ route('sales_history') }}" class="quick-btn"><i class="bi bi-clock-history"></i> Sales History</a>
                </div>
            </div>
        </div>

        <div class="col-lg-8">
            <div class="panel">
                <div class="panel-title"><i class="bi bi-list-ul"></i> Inventory status</div>
                <div class="table-responsive">
                    <table class="table align-middle">
                        <thead>
                            <tr>
                                <th>Product</th>
                                <th>SKU</th>
                                <th>Barcode</th>
                                <th>Stock</th>
                                <th>Level</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($products as $product)
                                @php
                                    $status = $product->stock <= 0 ? 'out' : ($product->stock < 5 ? 'low' : 'ok');
                                    $pct = $product->stock <= 0 ? 0 : min(100, $product->stock);
                                    $badge = $status === 'out' ? 'bg-danger' : ($status === 'low' ? 'bg-warning text-dark' : 'bg-success');
                                    $label = $status === 'out' ? 'Out of stock' : ($status === 'low' ? 'Low' : 'Healthy');
                                @endphp
                                <tr>
                                    <td>
                                        <div class="fw-semibold">{{ $product->product_name }}</div>
                                        <div class="text-muted" style="font-size:.78rem;">{{ $product->brand }} &middot; {{ $product->product_type }}</div>
                                    </td>
                                    <td><span class="chip-sku">{{ $product->sku }}</span></td>
                                    <td><span class="chip-barcode">{{ $product->barcode }}</span></td>
                                    <td class="fw-bold">{{ $product->stock }}</td>
                                    <td>
                                        <div class="stock-meter">
                                            <div class="meter-{{ $status }}" style="width: {{ $pct }}%"></div>
                                        </div>
                                    </td>
                                    <td><span class="badge {{ $badge }} rounded-pill">{{ $label }}</span></td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="text-center text-muted py-4">
                                        No products yet. <a href="{{ route('add_product') }}">Add your first product</a>.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
@endsection
