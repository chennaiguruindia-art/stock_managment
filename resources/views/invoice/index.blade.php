@extends('layouts.app')

@section('page-title', 'Invoice Management')
@section('page-subtitle', 'View and print bill copies for issued orders.')

@section('content')
    <style>
        .inv-recent-chip {
            font-family: ui-monospace, "Cascadia Code", Consolas, monospace;
            font-weight: 700;
            font-size: .78rem;
            color: var(--accent-strong, #1043a8);
            background: var(--accent-soft, #e7eefe);
            border: 1px solid #cdddfb;
            border-radius: 6px;
            padding: .2rem .5rem;
        }
        .inv-empty {
            border: 1px dashed var(--border-strong, #c5cede);
            border-radius: var(--radius, 10px);
            background: var(--surface-2, #f7f9fc);
        }
    </style>

    @include('layouts.alerts')

    <!-- Recent Generated Invoices Log -->
    @if ($recentInvoices->isNotEmpty())
        <div class="card-panel">
            <div class="section-title"><i class="bi bi-clock-history me-2" style="color:var(--accent);"></i> Recent issued invoices</div>
            <div class="table-responsive">
                <table class="table align-middle" style="font-size:.88rem;">
                    <thead>
                        <tr>
                            <th>Invoice / Order No.</th>
                            <th>Date</th>
                            <th>Customer Name &amp; Phone</th>
                            <th>Items</th>
                            <th class="text-end">Total</th>
                            <th>Status</th>
                            <th class="text-end">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($recentInvoices as $order)
                            <tr>
                                <td><span class="inv-recent-chip">{{ $order->order_id }}</span></td>
                                <td>{{ $order->created_at->format('d M Y, h:i A') }}</td>
                                <td>
                                    <div class="fw-semibold">{{ $order->customer_name ?: '—' }}</div>
                                    <div class="text-muted" style="font-size:.76rem;"><i class="bi bi-telephone me-1"></i>{{ $order->customer_mobile ?: '—' }}</div>
                                </td>
                                <td>{{ $order->items->sum('qty') }} unit(s)</td>
                                <td class="text-end fw-bold">
                                    ₹{{ number_format($order->total, 2) }}
                                    @if ((float)($order->discount_percent ?? 0) > 0)
                                        <div class="text-success" style="font-size:.72rem;font-weight:600;">
                                            -{{ rtrim(rtrim(number_format((float)$order->discount_percent, 2), '0'), '.') }}% off (₹{{ number_format((float)($order->discount_amount ?? 0), 2) }})
                                        </div>
                                    @endif
                                </td>
                                <td><span class="badge rounded-pill" style="background:var(--ok,#0f9d58);color:#fff;"><i class="bi bi-check-circle me-1"></i>Issued</span></td>
                                <td class="text-end">
                                    <div class="d-inline-flex gap-1">
                                        <a href="{{ route('view_order_invoice', $order->order_id) }}" class="btn btn-sm btn-outline-accent">
                                            <i class="bi bi-receipt me-1"></i> View
                                        </a>
                                        <button type="button" class="btn btn-sm btn-outline-warning text-dark edit-discount-btn"
                                            data-order-id="{{ $order->order_id }}"
                                            data-customer="{{ $order->customer_name ?: 'Walk-in' }}"
                                            data-mobile="{{ $order->customer_mobile ?: '—' }}"
                                            data-subtotal="{{ (float) $order->items->sum('total') }}"
                                            data-discount-percent="{{ (float) ($order->discount_percent ?? 0) }}"
                                            data-discount-amount="{{ (float) ($order->discount_amount ?? 0) }}"
                                            data-total="{{ (float) $order->total }}"
                                            title="Edit discount for this bill">
                                            <i class="bi bi-percent me-1"></i> Edit Discount
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    @else
        <div class="card-panel text-center py-5 inv-empty">
            <i class="bi bi-receipt fs-1 mb-2 d-block" style="color:var(--border-strong,#c5cede);"></i>
            <p class="text-muted mb-0">No invoices issued yet.</p>
        </div>
    @endif

    @include('invoice._edit_discount_modal')
@endsection
