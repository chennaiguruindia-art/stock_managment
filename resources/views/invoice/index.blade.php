@extends('layouts.app')

@section('page-title', 'Invoice Management')
@section('page-subtitle', 'View and print bill copies for issued orders.')

@section('content')
    <style>
        .inv-recent-chip {
            font-family: ui-monospace, "Cascadia Code", Consolas, monospace;
            font-weight: 700;
            color: #6b1f2a;
            background: #f3e2e3;
            border-radius: 8px;
            padding: .25rem .55rem;
        }
    </style>

    @include('layouts.alerts')

    <!-- Recent Generated Invoices Log -->
    @if ($recentInvoices->isNotEmpty())
        <div class="card-panel" style="border-radius:20px;">
            <div class="section-title"><i class="bi bi-clock-history me-2 text-danger"></i> Recent Issued Invoices</div>
            <div class="table-responsive">
                <table class="table align-middle" style="font-size:.9rem;">
                    <thead>
                        <tr>
                            <th>Invoice / Order No.</th>
                            <th>Date</th>
                            <th>Customer Name &amp; Phone</th>
                            <th>Items</th>
                            <th>Total</th>
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
                                    <div class="fw-bold text-dark">{{ $order->customer_name ?: 'Ms. Kavitha R' }}</div>
                                    <div class="text-muted" style="font-size:.78rem;"><i class="bi bi-telephone me-1"></i>{{ $order->customer_mobile ?: '98765 43210' }}</div>
                                </td>
                                <td>{{ $order->items->sum('qty') }} unit(s)</td>
                                <td class="fw-bold text-danger">₹{{ number_format($order->total, 2) }}</td>
                                <td><span class="badge bg-success rounded-pill"><i class="bi bi-check-circle me-1"></i>Issued</span></td>
                                <td class="text-end">
                                    <a href="{{ route('view_order_invoice', $order->order_id) }}" class="btn btn-sm btn-outline-danger" style="border-radius:8px;font-weight:600;">
                                        <i class="bi bi-receipt me-1"></i> View Invoice
                                    </a>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    @else
        <div class="card-panel text-center py-5" style="border-radius:20px;">
            <i class="bi bi-receipt fs-1 text-muted opacity-50 mb-2 d-block"></i>
            <p class="text-muted mb-0">No invoices issued yet.</p>
        </div>
    @endif
@endsection
