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
                            <td>{{ $order->items->sum('qty') }}</td>
                            <td class="fw-bold">₹{{ number_format($order->total, 2) }}</td>
                            <td><span class="badge bg-success rounded-pill">Paid</span></td>
                        </tr>
                        <tr class="d-none" id="items-{{ $order->id }}">
                            <td colspan="7" class="bg-light" style="border-radius:0 0 16px 16px;">
                                <div class="p-3">
                                    @foreach ($order->items as $item)
                                        <div class="row py-2 align-items-center border-bottom border-secondary-subtle" style="font-size:.86rem;">
                                            <div class="col-4 fw-semibold">{{ $item->product_name }}</div>
                                            <div class="col-4 text-muted">{{ $item->sku }} &middot; {{ $item->barcode }}</div>
                                            <div class="col-2 text-end">{{ $item->qty }} × ₹{{ number_format($item->price, 2) }}</div>
                                            <div class="col-2 text-end fw-bold">₹{{ number_format($item->total, 2) }}</div>
                                        </div>
                                    @endforeach
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
