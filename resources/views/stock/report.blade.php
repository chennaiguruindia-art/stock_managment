@extends('layouts.app')

@section('page-title', 'Report')
@section('page-subtitle', 'Live sales, stock, and inventory performance, computed from real data.')

@section('content')
    <style>
        .rp-stat {
            --tone: var(--accent, #1554d1);
            --tone-soft: var(--accent-soft, #e7eefe);
            background: #fff;
            border: 1px solid var(--border, #dfe4ee);
            border-radius: var(--radius, 10px);
            padding: 1rem 1.1rem;
            color: var(--text, #0e1726);
            position: relative;
            overflow: hidden;
            box-shadow: 0 1px 2px rgba(16, 24, 40, .05);
        }
        .rp-stat::before {
            content: "";
            position: absolute;
            left: 0; top: 0; bottom: 0;
            width: 3px;
            background: var(--tone);
        }
        .rp-stat .icon {
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
        .rp-stat .label {
            font-size: .66rem;
            text-transform: uppercase;
            letter-spacing: .1em;
            font-weight: 700;
            color: var(--muted, #66748b);
        }
        .rp-stat .value {
            font-size: 1.75rem;
            font-weight: 800;
            line-height: 1.2;
            color: var(--text, #0e1726);
            margin-top: .25rem;
            font-variant-numeric: tabular-nums;
        }
        .rp-rev   { --tone: #0f9d58; --tone-soft: #e4f7ec; }
        .rp-orders{ --tone: #1554d1; --tone-soft: #e7eefe; }
        .rp-items { --tone: #7c3aed; --tone-soft: #f0eafe; }
        .rp-value { --tone: #d97706; --tone-soft: #fdf1e2; }

        .rp-panel {
            border-radius: var(--radius, 10px);
            background: #fff;
            border: 1px solid var(--border, #dfe4ee);
            box-shadow: 0 1px 2px rgba(16, 24, 40, .05);
            padding: 1.15rem 1.25rem;
            height: 100%;
        }
        .rp-panel .panel-title {
            font-size: .95rem;
            font-weight: 700;
            letter-spacing: -.01em;
            color: var(--text, #0e1726);
            margin-bottom: 1rem;
            display: flex;
            align-items: center;
            gap: .5rem;
        }
        .rp-panel .panel-title i { color: var(--accent, #1554d1); }

        .bar-chart { display: flex; align-items: flex-end; gap: .8rem; height: 200px; padding-top: 1rem; }
        .bar-col { flex: 1; display: flex; flex-direction: column; align-items: center; justify-content: flex-end; height: 100%; gap: .4rem; }
        .bar {
            width: 100%;
            max-width: 46px;
            border-radius: 5px 5px 2px 2px;
            background: var(--accent, #1554d1);
            min-height: 3px;
            transition: height .3s ease;
        }
        .bar-col.empty .bar { background: var(--surface-3, #eaeff7); }
        .bar-day { font-size: .73rem; color: var(--muted, #66748b); font-weight: 600; }
        .bar-amt { font-size: .7rem; font-weight: 700; color: var(--accent, #1554d1); font-variant-numeric: tabular-nums; }

        .rank { display: flex; align-items: center; gap: .75rem; padding: .55rem 0; border-bottom: 1px solid var(--border, #dfe4ee); }
        .rank:last-child { border-bottom: 0; }
        .rank .pos {
            width: 28px; height: 28px; border-radius: 7px; display: inline-flex;
            align-items: center; justify-content: center; font-weight: 700; font-size: .8rem;
            background: var(--surface-3, #eaeff7); color: var(--muted, #66748b); flex-shrink: 0;
        }
        .rank .pos.top1 { background: #1554d1; color: #fff; }
        .rank .pos.top2 { background: #0f9d58; color: #fff; }
        .rank .pos.top3 { background: #d97706; color: #fff; }
        .rank .r-name { flex: 1; font-weight: 600; font-size: .88rem; color: var(--text, #0e1726); }
        .rank .r-meta { font-size: .74rem; color: var(--muted, #66748b); font-weight: 500; }
        .rank .r-val { text-align: right; }
        .rank .r-qty { font-weight: 700; font-size: .9rem; font-variant-numeric: tabular-nums; }
        .rank .r-rev { font-size: .72rem; color: var(--muted, #66748b); }

        .health-bar { height: 10px; border-radius: 999px; background: var(--surface-3, #eaeff7); overflow: hidden; display: flex; }
        .health-bar > div { height: 100%; }
        .hb-ok { background: #0f9d58; }
        .hb-low { background: #d97706; }
        .hb-out { background: #dc2626; }

        .legend { display: flex; gap: 1.1rem; font-size: .8rem; color: var(--muted, #66748b); margin-top: .6rem; flex-wrap: wrap; }
        .legend span { display: inline-flex; align-items: center; gap: .35rem; }
        .legend i { width: 10px; height: 10px; border-radius: 3px; display: inline-block; }

        .mini { border-radius: 8px; padding: .9rem 1rem; border: 1px solid var(--border, #dfe4ee); background: var(--surface-2, #f7f9fc); }
        .mini .m-label { font-size: .68rem; text-transform: uppercase; letter-spacing: .08em; color: var(--muted, #66748b); font-weight: 700; }
        .mini .m-value { font-size: 1.4rem; font-weight: 800; color: var(--text, #0e1726); font-variant-numeric: tabular-nums; margin-top: .2rem; }
    </style>

    <div class="row g-4 mb-4">
        <div class="col-lg-3 col-sm-6">
            <div class="rp-stat rp-rev">
                <i class="bi bi-currency-rupee icon"></i>
                <div class="label">Total revenue</div>
                <div class="value">₹{{ number_format($revenue, 2) }}</div>
            </div>
        </div>
        <div class="col-lg-3 col-sm-6">
            <div class="rp-stat rp-orders">
                <i class="bi bi-receipt icon"></i>
                <div class="label">Orders</div>
                <div class="value">{{ $ordersCount }}</div>
            </div>
        </div>
        <div class="col-lg-3 col-sm-6">
            <div class="rp-stat rp-items">
                <i class="bi bi-box-seam icon"></i>
                <div class="label">Items sold</div>
                <div class="value">{{ $itemsSold }}</div>
            </div>
        </div>
        <div class="col-lg-3 col-sm-6">
            <div class="rp-stat rp-value">
                <i class="bi bi-piggy-bank icon"></i>
                <div class="label">Stock value</div>
                <div class="value">₹{{ number_format($stockValue, 2) }}</div>
            </div>
        </div>
    </div>

    <div class="row g-4">
        <div class="col-lg-7">
            <div class="rp-panel">
                <div class="panel-title"><i class="bi bi-bar-chart-line"></i> Sales — last 7 days</div>
                <div class="bar-chart">
                    @foreach ($salesByDay as $s)
                        <div class="bar-col {{ $s['total'] == 0 ? 'empty' : '' }}">
                            <div class="bar-amt">{{ $s['total'] > 0 ? '₹' . number_format($s['total']) : '' }}</div>
                            <div class="bar" style="height: {{ max($s['total'] / $maxDay * 100, 3) }}%"></div>
                            <div class="bar-day">{{ $s['day'] }}</div>
                        </div>
                    @endforeach
                </div>
                <div class="text-muted mt-3" style="font-size:.82rem;">Daily revenue for the current week.</div>
            </div>
        </div>

        <div class="col-lg-5">
            <div class="rp-panel">
                <div class="panel-title"><i class="bi bi-trophy"></i> Top selling products</div>
                @forelse ($topProducts as $i => $tp)
                    <div class="rank">
                        <span class="pos {{ $i === 0 ? 'top1' : ($i === 1 ? 'top2' : ($i === 2 ? 'top3' : '')) }}">{{ $i + 1 }}</span>
                        <div class="r-name">
                            {{ $tp->product_name }}
                            <div class="r-meta">{{ $tp->total_qty }} unit{{ $tp->total_qty > 1 ? 's' : '' }} sold</div>
                        </div>
                        <div class="r-val">
                            <div class="r-qty">₹{{ number_format($tp->total_rev, 2) }}</div>
                            <div class="r-rev">revenue</div>
                        </div>
                    </div>
                @empty
                    <div class="text-muted py-3">No sales recorded yet. <a href="{{ route('sell_pos') }}">Open POS</a>.</div>
                @endforelse
            </div>
        </div>
    </div>

    <div class="row g-4 mt-0">
        <div class="col-lg-7">
            <div class="rp-panel">
                <div class="panel-title"><i class="bi bi-heart-pulse"></i> Inventory health</div>
                <div class="health-bar">
                    <div class="hb-ok" style="width: {{ $totalProducts ? $healthyStock / $totalProducts * 100 : 0 }}%"></div>
                    <div class="hb-low" style="width: {{ $totalProducts ? $lowStock / $totalProducts * 100 : 0 }}%"></div>
                    <div class="hb-out" style="width: {{ $totalProducts ? $outOfStock / $totalProducts * 100 : 0 }}%"></div>
                </div>
                <div class="legend">
                    <span><i style="background:#0f9d58;"></i> Healthy {{ $healthyStock }}</span>
                    <span><i style="background:#d97706;"></i> Low {{ $lowStock }}</span>
                    <span><i style="background:#dc2626;"></i> Out of stock {{ $outOfStock }}</span>
                </div>
            </div>
        </div>

        <div class="col-lg-5">
            <div class="rp-panel">
                <div class="panel-title"><i class="bi bi-grid-3x3-gap"></i> Key metrics</div>
                <div class="row g-3">
                    <div class="col-6">
                        <div class="mini">
                            <div class="m-label">Stock health</div>
                            <div class="m-value">{{ $healthPct }}%</div>
                        </div>
                    </div>
                    <div class="col-6">
                        <div class="mini">
                            <div class="m-label">Products</div>
                            <div class="m-value">{{ $totalProducts }}</div>
                        </div>
                    </div>
                    <div class="col-6">
                        <div class="mini">
                            <div class="m-label">Avg order value</div>
                            <div class="m-value" style="font-size:1.2rem;">₹{{ number_format($ordersCount ? $revenue / $ordersCount : 0, 2) }}</div>
                        </div>
                    </div>
                    <div class="col-6">
                        <div class="mini">
                            <div class="m-label">Revenue / order</div>
                            <div class="m-value" style="font-size:1.2rem;">{{ $ordersCount ? round($itemsSold / $ordersCount, 1) : 0 }} items</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
