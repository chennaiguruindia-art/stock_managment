@extends('layouts.app')

@section('page-title', 'Dashboard')
@section('page-subtitle', 'All-in-one terminal for inventory overview, POS billing, returns & sales history.')

@section('content')
    <style>
        /* Overview Hero & Cards */
        .dash-hero {
            background: linear-gradient(120deg, #2a000a 0%, #5c1522 55%, #6b1f2a 100%);
            border-radius: 24px;
            padding: 1.75rem 2rem;
            color: #fff;
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 1.5rem;
            flex-wrap: wrap;
            box-shadow: 0 20px 45px rgba(39, 31, 47, 0.22);
            margin-bottom: 1.5rem;
        }
        .dash-hero h2 { font-weight: 800; margin: 0 0 .35rem; letter-spacing: -.01em; }
        .dash-hero p { margin: 0; color: #e7dde3; font-size: .95rem; }
        .dash-hero .hero-badge {
            background: rgba(255,255,255,.14);
            border: 1px solid rgba(255,255,255,.22);
            border-radius: 999px;
            padding: .5rem 1rem;
            font-weight: 600;
            font-size: .88rem;
        }

        .stat-card {
            position: relative;
            border-radius: 20px;
            padding: 1.3rem 1.4rem;
            color: #fff;
            overflow: hidden;
            box-shadow: 0 14px 30px rgba(15, 14, 15, .1);
            min-height: 140px;
        }
        .stat-card .icon {
            position: absolute;
            right: -14px;
            bottom: -14px;
            font-size: 4.4rem;
            opacity: .18;
            line-height: 1;
        }
        .stat-card .label { font-size: .78rem; text-transform: uppercase; letter-spacing: .12em; opacity: .85; }
        .stat-card .value { font-size: 2.2rem; font-weight: 800; line-height: 1.15; }
        .stat-card .foot { font-size: .78rem; opacity: .9; margin-top: .35rem; }
        .stat-total   { background: linear-gradient(135deg, #2e2740, #4a3b68); }
        .stat-stock   { background: linear-gradient(135deg, #16794c, #2bb673); }
        .stat-low     { background: linear-gradient(135deg, #c07d10, #e6a23c); }
        .stat-out     { background: linear-gradient(135deg, #b3373f, #e05c5c); }

        .health-row { margin-bottom: 1.1rem; }
        .health-row:last-child { margin-bottom: 0; }
        .health-row .d-flex { font-size: .88rem; margin-bottom: .3rem; }
        .health-row .d-flex b { font-weight: 700; }
        .progress { height: 9px; border-radius: 999px; background: #efece7; }
        .progress-bar-ok    { background: linear-gradient(90deg, #2bb673, #46d68f); }
        .progress-bar-low   { background: linear-gradient(90deg, #e6a23c, #f2c069); }
        .progress-bar-out   { background: linear-gradient(90deg, #e05c5c, #f08080); }

        .quick-btn {
            display: flex;
            align-items: center;
            gap: .9rem;
            padding: .85rem 1rem;
            border-radius: 14px;
            border: 1px solid #e8e5e0;
            background: #fbfaf8;
            color: #272327;
            text-decoration: none;
            font-weight: 600;
            transition: all .18s ease;
            cursor: pointer;
        }
        .quick-btn:hover {
            background: #6b1f2a;
            border-color: #6b1f2a;
            color: #fff;
            transform: translateY(-2px);
        }
        .quick-btn i {
            width: 36px;
            height: 36px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            border-radius: 10px;
            background: #f3e2e3;
            color: #6b1f2a;
            font-size: 1.05rem;
        }
        .quick-btn:hover i { background: rgba(255,255,255,.18); color: #fff; }

        .stock-meter { width: 100px; height: 6px; border-radius: 999px; background: #efece7; }
        .stock-meter > div { height: 100%; border-radius: 999px; }
        .meter-ok  { background: #2bb673; }
        .meter-low { background: #e6a23c; }
        .meter-out { background: #e05c5c; }
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
