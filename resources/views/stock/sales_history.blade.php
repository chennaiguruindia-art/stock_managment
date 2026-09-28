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
    </style>

    @include('layouts.alerts')

    <div class="row g-4 mb-4">
        <div class="col-lg-3 col-sm-6">
            <div class="sh-stat sh-orders">
                <i class="bi bi-receipt icon"></i>
                <div class="label">Total orders</div>
                <div class="value">{{ $totalOrders }}</div>
            </div>
        </div>
        <div class="col-lg-3 col-sm-6">
            <div class="sh-stat sh-revenue">
                <i class="bi bi-currency-rupee icon"></i>
                <div class="label">Total revenue</div>
                <div class="value">₹{{ number_format($totalRevenue, 2) }}</div>
                @if ($totalReturned > 0)
                    <div class="sub">
                        Gross ₹{{ number_format($totalRevenue + $totalReturned, 2) }}
                        &middot; <span class="neg">− ₹{{ number_format($totalReturned, 2) }} returned</span>
                    </div>
                @endif
            </div>
        </div>
        <div class="col-lg-3 col-sm-6">
            <div class="sh-stat sh-avg">
                <i class="bi bi-graph-up-arrow icon"></i>
                <div class="label">Avg order value</div>
                <div class="value">₹{{ number_format($avgOrder, 2) }}</div>
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

    <div class="panel">
        <div class="panel-title"><i class="bi bi-clock-history"></i> Complete Order History</div>

        <div class="mb-3">
            <input type="text" id="salesSearchInput" class="search-box-input" style="max-width:320px;" placeholder="Search order ID, customer, mobile...">
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
                        <th>Total</th>
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
                        @endphp
                        <tr data-search="{{ strtolower($order->order_id . ' ' . ($order->customer_name ?? '') . ' ' . ($order->customer_mobile ?? '')) }}">
                            <td>
                                <button class="expand-btn" data-toggle="items-{{ $order->id }}">
                                    <i class="bi bi-chevron-down"></i>
                                </button>
                            </td>
                            <td><span class="order-chip">{{ $order->order_id }}</span></td>
                            <td>{{ $order->created_at->format('d M Y, h:i A') }}</td>
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
                                                    ₹{{ number_format($item->netAmount(), 2) }}
                                                    <span class="amt-ret">− ₹{{ number_format($lineReturned, 2) }}</span>
                                                @else
                                                    ₹{{ number_format($item->total, 2) }}
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
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="text-center text-muted py-4">
                                No sales yet.
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
