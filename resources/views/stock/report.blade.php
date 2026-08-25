@extends('layouts.app')

@section('page-title', 'Report')
@section('page-subtitle', 'Live sales, stock, and inventory performance, computed from real data.')

@section('content')
    <style>
        .rp-stat {
            border-radius: 18px;
            padding: 1.2rem 1.4rem;
            color: #fff;
            position: relative;
            overflow: hidden;
            box-shadow: 0 12px 26px rgba(15, 14, 15, .12);
        }
        .rp-stat .icon {
            position: absolute;
            right: -12px;
            bottom: -12px;
            font-size: 4rem;
            opacity: .18;
            line-height: 1;
        }
        .rp-stat .label { font-size: .78rem; text-transform: uppercase; letter-spacing: .12em; opacity: .85; }
        .rp-stat .value { font-size: 2rem; font-weight: 800; line-height: 1.2; }
        .rp-rev { background: linear-gradient(135deg, #16794c, #2bb673); }
        .rp-orders { background: linear-gradient(135deg, #2e2740, #4a3b68); }
        .rp-items { background: linear-gradient(135deg, #6b1f2a, #a44454); }
        .rp-value { background: linear-gradient(135deg, #c07d10, #e6a23c); }

        .rp-panel {
            border-radius: 20px;
            background: #fff;
            border: 1px solid #e8e5e0;
            box-shadow: 0 14px 34px rgba(36, 25, 35, .06);
            padding: 1.5rem;
            height: 100%;
        }
        .rp-panel .panel-title {
            font-size: 1.05rem;
            font-weight: 700;
            margin-bottom: 1.1rem;
            display: flex;
            align-items: center;
            gap: .55rem;
        }
        .rp-panel .panel-title i { color: #6b1f2a; }

        .bar-chart { display: flex; align-items: flex-end; gap: .8rem; height: 200px; padding-top: 1rem; }
        .bar-col { flex: 1; display: flex; flex-direction: column; align-items: center; justify-content: flex-end; height: 100%; gap: .4rem; }
        .bar {
            width: 100%;
            max-width: 46px;
            border-radius: 8px 8px 3px 3px;
            background: linear-gradient(180deg, #a44454, #6b1f2a);
            min-height: 3px;
            transition: height .3s ease;
        }
        .bar-col.empty .bar { background: #e8e5e0; }
        .bar-day { font-size: .75rem; color: #6f6d6a; }
        .bar-amt { font-size: .72rem; font-weight: 700; color: #6b1f2a; }

        .rank { display: flex; align-items: center; gap: .8rem; padding: .55rem 0; border-bottom: 1px dashed #eee; }
        .rank:last-child { border-bottom: 0; }
        .rank .pos {
            width: 30px; height: 30px; border-radius: 10px; display: inline-flex;
            align-items: center; justify-content: center; font-weight: 700; font-size: .85rem;
            background: #f3e2e3; color: #6b1f2a; flex-shrink: 0;
        }
        .rank .pos.top1 { background: linear-gradient(135deg, #e6a23c, #f2c069); color: #fff; }
        .rank .pos.top2 { background: linear-gradient(135deg, #9aa2ad, #c0c7d1); color: #fff; }
        .rank .pos.top3 { background: linear-gradient(135deg, #b3773a, #d09a63); color: #fff; }
        .rank .r-name { flex: 1; font-weight: 600; font-size: .92rem; }
        .rank .r-meta { font-size: .75rem; color: #6f6d6a; }
        .rank .r-val { text-align: right; }
        .rank .r-qty { font-weight: 700; }
        .rank .r-rev { font-size: .75rem; color: #6f6d6a; }

        .health-bar { height: 10px; border-radius: 999px; background: #efece7; overflow: hidden; display: flex; }
        .health-bar > div { height: 100%; }
        .hb-ok { background: #2bb673; }
        .hb-low { background: #e6a23c; }
        .hb-out { background: #e05c5c; }

        .legend { display: flex; gap: 1.2rem; font-size: .82rem; color: #6f6d6a; margin-top: .6rem; }
        .legend span { display: inline-flex; align-items: center; gap: .35rem; }
        .legend i { width: 10px; height: 10px; border-radius: 3px; display: inline-block; }

        .mini { border-radius: 14px; padding: 1rem 1.1rem; border: 1px solid #e8e5e0; background: #fbfaf8; }
        .mini .m-label { font-size: .75rem; text-transform: uppercase; letter-spacing: .1em; color: #6f6d6a; }
        .mini .m-value { font-size: 1.5rem; font-weight: 800; }
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
                    <span><i style="background:#2bb673;"></i> Healthy {{ $healthyStock }}</span>
                    <span><i style="background:#e6a23c;"></i> Low {{ $lowStock }}</span>
                    <span><i style="background:#e05c5c;"></i> Out of stock {{ $outOfStock }}</span>
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
