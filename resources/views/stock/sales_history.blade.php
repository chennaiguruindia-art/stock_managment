@extends('layouts.app')

@section('page-title', 'Sales history')
@section('page-subtitle', 'Every order, revenue and item sold — searchable in one place.')

@section('content')
    <style>
        .sh-stat {
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
        .sh-stat::before {
            content: "";
            position: absolute;
            left: 0; top: 0; bottom: 0;
            width: 3px;
            background: var(--tone);
        }
        .sh-stat .icon {
            position: absolute;
            right: 14px;
            top: 14px;
            width: 32px;
            height: 32px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            font-size: .95rem;
            border-radius: 8px;
            background: var(--tone-soft);
            color: var(--tone);
            opacity: 1;
            line-height: 1;
        }
        .sh-stat .label {
            font-size: .66rem;
            text-transform: uppercase;
            letter-spacing: .1em;
            font-weight: 700;
            color: var(--muted, #66748b);
        }
        .sh-stat .value {
            font-size: 1.6rem;
            font-weight: 800;
            line-height: 1.2;
            color: var(--text, #0e1726);
            margin-top: .25rem;
            font-variant-numeric: tabular-nums;
        }
        .sh-stat .sub {
            font-size: .7rem;
            color: var(--muted, #66748b);
            margin-top: .3rem;
            line-height: 1.3;
            font-variant-numeric: tabular-nums;
        }
        .sh-stat .sub .neg { color: #c62828; font-weight: 700; }
        .sh-stat .sub .strike { text-decoration: line-through; opacity: .75; }
        .sh-orders { --tone: #1554d1; --tone-soft: #e7eefe; }
        .sh-revenue{ --tone: #0f9d58; --tone-soft: #e4f7ec; }
        .sh-avg    { --tone: #7c3aed; --tone-soft: #f0eafe; }
        .sh-items  { --tone: #d97706; --tone-soft: #fdf1e2; }

        .order-chip {
            font-family: ui-monospace, "Cascadia Code", Consolas, monospace;
            font-weight: 700;
            font-size: .78rem;
            color: var(--accent-strong, #1043a8);
            background: var(--accent-soft, #e7eefe);
            border: 1px solid #cdddfb;
            border-radius: 6px;
            padding: .2rem .5rem;
        }

        .expand-btn {
            border: 0;
            background: var(--surface-3, #eaeff7);
            color: var(--accent, #1554d1);
            cursor: pointer;
            font-size: .8rem;
            width: 24px;
            height: 24px;
            border-radius: 6px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
        }
        .expand-btn:hover { background: var(--accent-soft, #e7eefe); }

        .amt-orig {
            font-size: .78rem;
            font-weight: 500;
            color: var(--muted, #66748b);
            text-decoration: line-through;
        }
        .amt-ret {
            display: block;
            font-size: .72rem;
            font-weight: 700;
            color: #c62828;
        }
        .ret-mark {
            display: inline-block;
            margin-top: .2rem;
            font-size: .68rem;
            font-weight: 700;
            color: #c62828;
        }
        .gst-pill {
            display: inline-flex;
            align-items: center;
            gap: 4px;
            font-size: .72rem;
            padding: .2rem .55rem;
            border-radius: 6px;
            background: #eef2ff;
            color: #3730a3;
            font-weight: 600;
        }
        .gst-table th {
            font-size: .74rem;
            text-transform: uppercase;
            letter-spacing: .04em;
            color: #475569;
            font-weight: 700;
        }
        .gst-table td {
            font-variant-numeric: tabular-nums;
        }
    </style>

    @include('layouts.alerts')

    {{-- Filter & Action Bar --}}
    <div class="card border-0 shadow-sm mb-4" style="border-radius: 12px; background: #ffffff; border: 1px solid #dfe4ee !important;">
        <div class="card-body p-3">
            <div class="d-flex flex-wrap justify-content-between align-items-center gap-3">
                <div class="d-flex align-items-center gap-2 flex-wrap">
                    <span class="fw-bold text-dark" style="font-size: .88rem;">
                        <i class="bi bi-calendar-month text-primary me-1"></i> Filter Month:
                    </span>
                    <select class="form-select form-select-sm" style="width: auto; min-width: 190px; font-weight: 600;" onchange="window.location.href = this.value;">
                        <option value="{{ route('sales_history', ['month' => 'all', 'gst_rate' => $gstRate]) }}" {{ $selectedMonth === 'all' ? 'selected' : '' }}>
                            All Months (Overall)
                        </option>
                        @foreach ($availableMonths as $mKey => $mLabel)
                            <option value="{{ route('sales_history', ['month' => $mKey, 'gst_rate' => $gstRate]) }}" {{ $selectedMonth === $mKey ? 'selected' : '' }}>
                                {{ $mLabel }} ({{ $monthlySummaries[$mKey]['orders_count'] ?? 0 }} orders)
                            </option>
                        @endforeach
                    </select>

                    @if ($selectedMonth !== 'all')
                        <span class="badge bg-primary-subtle text-primary border border-primary-subtle px-2 py-1" style="font-size: .78rem;">
                            Showing: {{ $monthlySummaries[$selectedMonth]['month_label'] ?? $selectedMonth }}
                        </span>
                        <a href="{{ route('sales_history', ['month' => 'all', 'gst_rate' => $gstRate]) }}" class="btn btn-sm btn-link text-decoration-none p-0 text-muted" style="font-size: .78rem;">
                            <i class="bi bi-x-circle"></i> Clear filter
                        </a>
                    @endif

                    <span class="gst-pill ms-lg-2">
                        <i class="bi bi-shield-check"></i> GST: {{ $gstRate }}% Inclusive
                    </span>
                </div>

                <div class="d-flex align-items-center gap-2">
                    <a href="{{ route('sales_history_export_gst', ['month' => $selectedMonth, 'gst_rate' => $gstRate]) }}" class="btn btn-sm btn-success d-inline-flex align-items-center gap-1 shadow-sm px-3" style="font-weight: 600;">
                        <i class="bi bi-file-earmark-excel"></i> Export GST Report (Excel)
                    </a>
                </div>
            </div>
        </div>
    </div>

    {{-- Stats Cards --}}
    <div class="row g-4 mb-4">
        <div class="col-lg-3 col-sm-6">
            <div class="sh-stat sh-orders">
                <i class="bi bi-receipt icon"></i>
                <div class="label">Total orders {{ $selectedMonth !== 'all' ? '(' . ($monthlySummaries[$selectedMonth]['month_label'] ?? '') . ')' : '' }}</div>
                <div class="value">{{ $totalOrders }}</div>
            </div>
        </div>
        <div class="col-lg-3 col-sm-6">
            <div class="sh-stat sh-revenue">
                <i class="bi bi-currency-rupee icon"></i>
                <div class="label">Total revenue (Net Sales)</div>
                <div class="value">₹{{ number_format($totalRevenue, 2) }}</div>
                @if ($totalReturned > 0)
                    <div class="sub">
                        Gross ₹{{ number_format($totalRevenue + $totalReturned, 2) }}
                        &middot; <span class="neg">− ₹{{ number_format($totalReturned, 2) }} returned</span>
                    </div>
                @endif
                <div class="sub mt-2 pt-1 border-top" style="font-size:.68rem;">
                    Taxable: <b class="text-primary">₹{{ number_format($totalTaxable, 2) }}</b> &middot; 
                    GST: <b class="text-success">₹{{ number_format($totalGst, 2) }}</b>
                </div>
            </div>
        </div>
        <div class="col-lg-3 col-sm-6">
            <div class="sh-stat sh-avg">
                <i class="bi bi-graph-up-arrow icon"></i>
                <div class="label">Avg order value</div>
                <div class="value">₹{{ number_format($avgOrder, 2) }}</div>
                <div class="sub mt-2 pt-1 border-top" style="font-size:.68rem;">
                    CGST: ₹{{ number_format($totalCgst, 2) }} &middot; SGST: ₹{{ number_format($totalSgst, 2) }}
                </div>
            </div>
        </div>
        <div class="col-lg-3 col-sm-6">
            <div class="sh-stat sh-items">
                <i class="bi bi-box-seam icon"></i>
                <div class="label">Items sold</div>
                <div class="value">{{ $totalItems }}</div>
                @if ($returnedItems > 0)
                    <div class="sub">
                        <span class="strike">{{ $totalItems + $returnedItems }} gross</span>
                        &middot; <span class="neg">{{ $returnedItems }} returned</span>
                        in {{ $ordersWithReturns }} order(s)
                    </div>
                @endif
            </div>
        </div>
    </div>

    {{-- Month-wise GST Sales Summary Table --}}
    <div class="panel mb-4">
        <div class="d-flex justify-content-between align-items-center mb-3 flex-wrap gap-2">
            <div>
                <div class="panel-title mb-0">
                    <i class="bi bi-file-earmark-spreadsheet text-success"></i> Month-wise GST Sales Summary (GSTR-3B)
                </div>
                <div class="text-muted mt-1" style="font-size:.78rem;">
                    Prices inclusive of 5% GST (Taxable Turnover + CGST 2.5% + SGST 2.5%)
                </div>
            </div>
            <div>
                <a href="{{ route('sales_history_export_gst', ['month' => 'all', 'gst_rate' => $gstRate]) }}" class="btn btn-sm btn-outline-success" style="font-size:.76rem; font-weight:600;">
                    <i class="bi bi-download"></i> Download All Months Excel
                </a>
            </div>
        </div>

        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0 gst-table">
                <thead class="table-light">
                    <tr>
                        <th>Month / Period</th>
                        <th class="text-center">Orders</th>
                        <th class="text-center">Units Sold</th>
                        <th class="text-end">Gross Sales</th>
                        <th class="text-end">Returns</th>
                        <th class="text-end fw-bold">Net Sales (Turnover)</th>
                        <th class="text-end text-primary fw-bold">Taxable Value</th>
                        <th class="text-end">CGST (2.5%)</th>
                        <th class="text-end">SGST (2.5%)</th>
                        <th class="text-end text-success fw-bold">Total GST (5%)</th>
                        <th class="text-center">Action</th>
                    </tr>
                </thead>
                <tbody>
                    @php
                        $grandOrders = 0;
                        $grandUnits = 0;
                        $grandGross = 0;
                        $grandReturns = 0;
                        $grandNet = 0;
                        $grandTaxable = 0;
                        $grandCgst = 0;
                        $grandSgst = 0;
                        $grandGst = 0;
                    @endphp

                    @forelse ($monthlySummaries as $mKey => $ms)
                        @php
                            $grandOrders += $ms['orders_count'];
                            $grandUnits += $ms['items_sold'];
                            $grandGross += $ms['gross_total'];
                            $grandReturns += $ms['returned_total'];
                            $grandNet += $ms['net_total'];
                            $grandTaxable += $ms['taxable_value'];
                            $grandCgst += $ms['cgst'];
                            $grandSgst += $ms['sgst'];
                            $grandGst += $ms['total_gst'];
                            $isSelected = ($selectedMonth === $mKey);
                        @endphp
                        <tr style="{{ $isSelected ? 'background: #f0f7ff; font-weight: 600;' : '' }}">
                            <td>
                                <span class="fw-bold">{{ $ms['month_label'] }}</span>
                                @if ($isSelected)
                                    <span class="badge bg-primary ms-1" style="font-size:.65rem;">Active Filter</span>
                                @endif
                            </td>
                            <td class="text-center">{{ $ms['orders_count'] }}</td>
                            <td class="text-center">{{ $ms['items_sold'] }}</td>
                            <td class="text-end">₹{{ number_format($ms['gross_total'], 2) }}</td>
                            <td class="text-end text-danger">
                                @if ($ms['returned_total'] > 0)
                                    − ₹{{ number_format($ms['returned_total'], 2) }}
                                @else
                                    —
                                @endif
                            </td>
                            <td class="text-end fw-bold">₹{{ number_format($ms['net_total'], 2) }}</td>
                            <td class="text-end text-primary fw-bold">₹{{ number_format($ms['taxable_value'], 2) }}</td>
                            <td class="text-end">₹{{ number_format($ms['cgst'], 2) }}</td>
                            <td class="text-end">₹{{ number_format($ms['sgst'], 2) }}</td>
                            <td class="text-end text-success fw-bold">₹{{ number_format($ms['total_gst'], 2) }}</td>
                            <td class="text-center">
                                <div class="btn-group btn-group-sm">
                                    <a href="{{ route('sales_history', ['month' => $mKey, 'gst_rate' => $gstRate]) }}" class="btn {{ $isSelected ? 'btn-primary' : 'btn-outline-primary' }}" style="font-size:.72rem;" title="Filter orders for this month">
                                        <i class="bi bi-eye"></i> View
                                    </a>
                                    <a href="{{ route('sales_history_export_gst', ['month' => $mKey, 'gst_rate' => $gstRate]) }}" class="btn btn-outline-success" style="font-size:.72rem;" title="Download Excel for this month">
                                        <i class="bi bi-file-earmark-excel"></i>
                                    </a>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="11" class="text-center text-muted py-3">No monthly sales data found.</td>
                        </tr>
                    @endforelse
                </tbody>
                @if (count($monthlySummaries) > 0)
                    <tfoot style="background:#f8fafc; font-weight:700; border-top: 2px solid #cbd5e1;">
                        <tr>
                            <td>CONSOLIDATED TOTAL</td>
                            <td class="text-center">{{ $grandOrders }}</td>
                            <td class="text-center">{{ $grandUnits }}</td>
                            <td class="text-end">₹{{ number_format($grandGross, 2) }}</td>
                            <td class="text-end text-danger">
                                @if ($grandReturns > 0)
                                    − ₹{{ number_format($grandReturns, 2) }}
                                @else
                                    ₹0.00
                                @endif
                            </td>
                            <td class="text-end text-dark">₹{{ number_format($grandNet, 2) }}</td>
                            <td class="text-end text-primary">₹{{ number_format($grandTaxable, 2) }}</td>
                            <td class="text-end">₹{{ number_format($grandCgst, 2) }}</td>
                            <td class="text-end">₹{{ number_format($grandSgst, 2) }}</td>
                            <td class="text-end text-success">₹{{ number_format($grandGst, 2) }}</td>
                            <td class="text-center">
                                <a href="{{ route('sales_history_export_gst', ['month' => 'all', 'gst_rate' => $gstRate]) }}" class="btn btn-sm btn-success" style="font-size:.72rem;">
                                    <i class="bi bi-download"></i> All Excel
                                </a>
                            </td>
                        </tr>
                    </tfoot>
                @endif
            </table>
        </div>
    </div>

    {{-- Order History List --}}
    <div class="panel">
        <div class="d-flex justify-content-between align-items-center mb-3 flex-wrap gap-2">
            <div class="panel-title mb-0">
                <i class="bi bi-clock-history"></i> 
                @if ($selectedMonth !== 'all')
                    Order History for {{ $monthlySummaries[$selectedMonth]['month_label'] ?? $selectedMonth }} ({{ $totalOrders }} orders)
                @else
                    Complete Order History (All Months)
                @endif
            </div>

            <div class="d-flex align-items-center gap-2">
                <input type="text" id="salesSearchInput" class="search-box-input" style="max-width:320px;" placeholder="Search order ID, customer, mobile...">
            </div>
        </div>

        <div class="table-responsive">
            <table class="table align-middle" id="ordersTable">
                <thead>
                    <tr>
                        <th></th>
                        <th>Order</th>
                        <th>Date</th>
                        <th>Customer</th>
                        <th>Items</th>
                        <th>Total & GST (5%)</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($orders as $order)
                        @php
                            $returnedUnits = $order->returnedQty();
                            $returnedAmount = $order->returnedTotal();
                            $netAmount = $order->netTotal();
                            $netUnits = $order->netQty();
                            $state = $order->returnState();
                            $orderTaxable = $netAmount > 0 ? round($netAmount / (1 + ($gstRate / 100)), 2) : 0;
                            $orderGst = round($netAmount - $orderTaxable, 2);
                        @endphp
                        <tr data-search="{{ strtolower($order->order_id . ' ' . ($order->customer_name ?? '') . ' ' . ($order->customer_mobile ?? '')) }}">
                            <td>
                                <button class="expand-btn" data-toggle="items-{{ $order->id }}">
                                    <i class="bi bi-chevron-down"></i>
                                </button>
                            </td>
                            <td><span class="order-chip">{{ $order->order_id }}</span></td>
                            <td>{{ $order->created_at ? $order->created_at->format('d M Y, h:i A') : '—' }}</td>
                            <td>
                                <div class="fw-semibold">{{ $order->customer_name ?: 'Walk-in' }}</div>
                                <div class="text-muted" style="font-size:.78rem;">{{ $order->customer_mobile ?: '—' }}</div>
                            </td>
                            <td>
                                {{ $netUnits }}
                                @if ($returnedUnits > 0)
                                    <span class="ret-mark">−{{ $returnedUnits }} returned</span>
                                @endif
                            </td>
                            <td class="fw-bold">
                                @if ($returnedAmount > 0)
                                    <span class="amt-orig">₹{{ number_format($order->total, 2) }}</span>
                                    ₹{{ number_format($netAmount, 2) }}
                                    <span class="amt-ret">− ₹{{ number_format($returnedAmount, 2) }} returned</span>
                                @else
                                    ₹{{ number_format($order->total, 2) }}
                                @endif
                                @if ($netAmount > 0)
                                    <div class="text-muted" style="font-size: .68rem; font-weight: normal;">
                                        Taxable: ₹{{ number_format($orderTaxable, 2) }} &middot; GST: ₹{{ number_format($orderGst, 2) }}
                                    </div>
                                @endif
                            </td>
                            <td>
                                @if ($state === 'full')
                                    <span class="badge bg-danger rounded-pill">Returned</span>
                                @elseif ($state === 'partial')
                                    <span class="badge bg-warning text-dark rounded-pill">Partly returned</span>
                                @else
                                    <span class="badge bg-success rounded-pill">Paid</span>
                                @endif
                            </td>
                        </tr>
                        <tr class="d-none" id="items-{{ $order->id }}">
                            <td colspan="7" class="bg-light" style="border-radius:0 0 16px 16px;">
                                <div class="p-3">
                                    @foreach ($order->items as $item)
                                        @php
                                            $lineReturned = $item->returnedAmount();
                                            $itemNet = $item->netAmount();
                                            $itemTaxable = $itemNet > 0 ? round($itemNet / (1 + ($gstRate / 100)), 2) : 0;
                                            $itemGst = round($itemNet - $itemTaxable, 2);
                                        @endphp
                                        <div class="row py-2 align-items-center border-bottom border-secondary-subtle" style="font-size:.86rem;">
                                            <div class="col-4 fw-semibold">
                                                {{ $item->product_name }}
                                                @if ($item->returnedQty() > 0)
                                                    <span class="ret-mark">{{ $item->returnedQty() }} of {{ $item->qty }} returned</span>
                                                @endif
                                            </div>
                                            <div class="col-4 text-muted">{{ $item->sku }} &middot; {{ $item->barcode }}</div>
                                            <div class="col-2 text-end">{{ $item->qty }} × ₹{{ number_format($item->price, 2) }}</div>
                                            <div class="col-2 text-end fw-bold">
                                                @if ($lineReturned > 0)
                                                    <span class="amt-orig">₹{{ number_format($item->total, 2) }}</span>
                                                    ₹{{ number_format($itemNet, 2) }}
                                                    <span class="amt-ret">− ₹{{ number_format($lineReturned, 2) }}</span>
                                                @else
                                                    ₹{{ number_format($item->total, 2) }}
                                                @endif
                                                @if ($itemNet > 0)
                                                    <div class="text-muted" style="font-size:.66rem; font-weight:normal;">
                                                        Taxable ₹{{ number_format($itemTaxable, 2) }} | GST ₹{{ number_format($itemGst, 2) }}
                                                    </div>
                                                @endif
                                            </div>
                                        </div>
                                    @endforeach
                                    @if ($returnedAmount > 0)
                                        <div class="row py-2 align-items-center" style="font-size:.86rem;">
                                            <div class="col-10 text-end text-danger fw-bold">Order total after returns</div>
                                            <div class="col-2 text-end text-danger fw-bold">₹{{ number_format($netAmount, 2) }}</div>
                                        </div>
                                    @endif
                                    <div class="row py-1 align-items-center" style="font-size:.78rem;">
                                        <div class="col-10 text-end text-muted">GST Breakup (5% Inclusive): Taxable ₹{{ number_format($orderTaxable, 2) }} + CGST ₹{{ number_format(round($orderGst/2, 2), 2) }} + SGST ₹{{ number_format(round($orderGst - round($orderGst/2, 2), 2), 2) }}</div>
                                        <div class="col-2 text-end text-success fw-bold">GST ₹{{ number_format($orderGst, 2) }}</div>
                                    </div>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="text-center text-muted py-4">
                                No sales found for this period.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        // Sales History Expand & Search
        document.querySelectorAll('.expand-btn').forEach(btn => {
            btn.addEventListener('click', () => {
                const row = document.getElementById(btn.dataset.toggle);
                if (row) {
                    row.classList.toggle('d-none');
                    const icon = btn.querySelector('i');
                    icon.classList.toggle('bi-chevron-down');
                    icon.classList.toggle('bi-chevron-up');
                }
            });
        });

        const salesSearchInput = document.getElementById('salesSearchInput');
        if (salesSearchInput) {
            salesSearchInput.addEventListener('input', e => {
                const q = e.target.value.trim().toLowerCase();
                document.querySelectorAll('#ordersTable tbody tr[data-search]').forEach(row => {
                    row.style.display = row.dataset.search.includes(q) ? '' : 'none';
                });
            });
        }
    </script>
@endpush
