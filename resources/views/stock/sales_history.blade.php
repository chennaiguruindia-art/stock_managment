@extends('layouts.app')

@section('page-title', 'Sales history')
@section('page-subtitle', 'Every order, revenue and item sold — searchable in one place.')

@section('content')
    <style>
        .sh-stat {
            border-radius: 18px;
            padding: 1.1rem 1.3rem;
            color: #fff;
            position: relative;
            overflow: hidden;
            box-shadow: 0 10px 22px rgba(15, 14, 15, .1);
        }
        .sh-stat .icon {
            position: absolute;
            right: -10px;
            bottom: -10px;
            font-size: 3.8rem;
            opacity: .18;
            line-height: 1;
        }
        .sh-stat .label { font-size: .75rem; text-transform: uppercase; letter-spacing: .12em; opacity: .85; }
        .sh-stat .value { font-size: 1.85rem; font-weight: 800; line-height: 1.2; }
        .sh-orders { background: linear-gradient(135deg, #2e2740, #4a3b68); }
        .sh-revenue { background: linear-gradient(135deg, #16794c, #2bb673); }
        .sh-avg { background: linear-gradient(135deg, #6b1f2a, #a44454); }
        .sh-items { background: linear-gradient(135deg, #c07d10, #e6a23c); }

        .order-chip {
            font-family: ui-monospace, "Cascadia Code", Consolas, monospace;
            font-weight: 700;
            color: #6b1f2a;
            background: #f3e2e3;
            border-radius: 8px;
            padding: .25rem .55rem;
        }

        .expand-btn {
            border: 0;
            background: transparent;
            color: #6b1f2a;
            cursor: pointer;
            font-size: 1rem;
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
