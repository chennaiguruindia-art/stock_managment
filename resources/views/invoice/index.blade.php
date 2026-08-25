@extends('layouts.app')

@section('page-title', 'Invoice Management')
@section('page-subtitle', 'Generate, view, and print formal tax invoices for your products and customers.')

@section('content')
    <style>
        .inv-hero {
            background: linear-gradient(120deg, #1e0007 0%, #400000 55%, #6b1f2a 100%);
            border-radius: 20px;
            padding: 1.6rem 2rem;
            color: #fff;
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 1.5rem;
            flex-wrap: wrap;
            box-shadow: 0 16px 36px rgba(40, 0, 0, 0.2);
            margin-bottom: 1.5rem;
        }
        .inv-hero h2 { font-weight: 800; margin: 0 0 .35rem; font-size: 1.5rem; }
        .inv-hero p { margin: 0; color: #f2e2e4; font-size: .92rem; }
        .inv-hero .hero-badge {
            background: rgba(255,255,255,.15);
            border: 1px solid rgba(255,255,255,.25);
            border-radius: 999px;
            padding: .45rem 1rem;
            font-weight: 600;
            font-size: .85rem;
        }

        .inv-toolbar {
            display: flex;
            align-items: center;
            gap: .75rem;
            margin-bottom: 1.2rem;
            flex-wrap: wrap;
        }

        .inv-search-input {
            flex: 1;
            min-width: 240px;
            border-radius: 999px;
            border: 1px solid #e8dfd8;
            padding: .6rem 1rem .6rem 2.4rem;
            background: #ffffff url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='16' height='16' fill='%2372686c' viewBox='0 0 16 16'%3E%3Cpath d='M11.742 10.344a6.5 6.5 0 1 0-1.397 1.398h-.001c.03.04.062.078.098.115l3.85 3.85a1 1 0 0 0 1.415-1.414l-3.85-3.85a1.007 1.007 0 0 0-.115-.1zM12 6.5a5.5 5.5 0 1 1-11 0 5.5 5.5 0 0 1 11 0z'/%3E%3C/svg%3E") no-repeat .9rem center;
            font-size: .92rem;
        }

        .inv-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(220px, 1fr));
            gap: 1rem;
        }

        .inv-card {
            background: #ffffff;
            border: 1px solid #e8dfd8;
            border-radius: 16px;
            padding: 1rem;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            text-decoration: none;
            color: #2b1f1f;
            transition: all .18s ease;
            box-shadow: 0 4px 12px rgba(0,0,0,0.03);
            min-height: 160px;
        }

        .inv-card:hover {
            border-color: #6b1f2a;
            transform: translateY(-2px);
            box-shadow: 0 10px 24px rgba(107, 31, 42, 0.14);
            color: #2b1f1f;
        }

        .inv-card.out-of-stock {
            opacity: .55;
            pointer-events: none;
            background: #fcf8f8;
        }

        .inv-card .p-type {
            font-size: .68rem;
            font-weight: 700;
            text-transform: uppercase;
            color: #6b1f2a;
            background: #f3e2e3;
            padding: .15rem .45rem;
            border-radius: 999px;
            display: inline-block;
            margin-bottom: .3rem;
        }

        .inv-card .p-name {
            font-weight: 700;
            font-size: .95rem;
            margin-bottom: .2rem;
            line-height: 1.25;
        }

        .inv-card .p-price {
            font-weight: 800;
            font-size: 1.05rem;
            color: #6b1f2a;
        }

        .btn-gen-inv {
            background: linear-gradient(135deg, #6b1f2a, #8c2e3d);
            color: #ffffff;
            border: 0;
            border-radius: 8px;
            padding: .35rem .75rem;
            font-size: .78rem;
            font-weight: 700;
            display: inline-flex;
            align-items: center;
            gap: .3rem;
        }

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

    <!-- Distinct Invoice Banner Header -->
    <div class="inv-hero">
        <div>
            <h2><i class="bi bi-file-earmark-text me-2"></i>Invoice Generator Terminal</h2>
            <p>Select any item from your catalog to generate a formal printable GST Tax Invoice.</p>
        </div>
        <div class="d-flex align-items-center gap-2">
            <span class="hero-badge"><i class="bi bi-box-seam me-1"></i> {{ $products->count() }} items available</span>
        </div>
    </div>

    <!-- Product Selection Panel -->
    <div class="card-panel mb-4" style="border-radius:20px;">
        <div class="section-title d-flex align-items-center justify-content-between">
            <span><i class="bi bi-grid-fill me-2 text-danger"></i> Select Product for Invoice</span>
            <span class="text-muted fw-normal" style="font-size:.85rem;">Click any item below to generate invoice</span>
        </div>

        <div class="inv-toolbar">
            <input type="text" id="invoiceSearch" class="inv-search-input" placeholder="Search by product name, brand, SKU or barcode...">
        </div>

        <div class="inv-grid" id="invoiceGrid">
            @forelse ($products as $product)
                @php
                    $isOut = $product->stock <= 0;
                    $isLow = $product->stock > 0 && $product->stock < 5;
                    $stockClass = $isOut ? 'text-danger fw-bold' : ($isLow ? 'text-warning fw-bold' : 'text-success');
                    $stockText = $isOut ? 'Out of stock' : ($isLow ? "Low: {$product->stock}" : "Stock: {$product->stock}");
                @endphp
                <a href="{{ route('invoice_detail', $product->id) }}" class="inv-card {{ $isOut ? 'out-of-stock' : '' }}"
                   data-search="{{ strtolower($product->product_name . ' ' . $product->brand . ' ' . $product->sku . ' ' . $product->barcode) }}">
                    <div>
                        @if ($product->product_type)
                            <span class="p-type">{{ $product->product_type }}</span>
                        @endif
                        <div class="p-name">{{ $product->product_name }}</div>
                        <div class="text-muted" style="font-size:.78rem;">
                            {{ $product->brand }}
                            @if ($product->size) &middot; {{ $product->size }} @endif
                            @if ($product->color) &middot; {{ $product->color }} @endif
                        </div>
                    </div>

                    <div class="d-flex align-items-center justify-content-between mt-3 pt-2 border-top">
                        <div>
                            <div class="p-price">₹{{ number_format($product->selling_price ?? 0, 2) }}</div>
                            <div style="font-size:.75rem;" class="{{ $stockClass }}">{{ $stockText }}</div>
                        </div>
                        <span class="btn-gen-inv"><i class="bi bi-file-earmark-plus"></i> Invoice</span>
                    </div>
                </a>
            @empty
                <div class="text-center text-muted py-5 col-span-full">
                    <i class="bi bi-box-seam fs-1 opacity-50 mb-2 d-block"></i>
                    No products available for invoice generation.
                </div>
            @endforelse
        </div>
    </div>

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
                                        <i class="bi bi-receipt me-1"></i> View Bill Copy
                                    </a>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    @endif
@endsection

@push('scripts')
    <script>
        const invoiceSearch = document.getElementById('invoiceSearch');
        if (invoiceSearch) {
            invoiceSearch.addEventListener('input', e => {
                const q = e.target.value.trim().toLowerCase();
                document.querySelectorAll('#invoiceGrid .inv-card').forEach(card => {
                    card.style.display = !q || card.dataset.search.includes(q) ? '' : 'none';
                });
            });
        }
    </script>
@endpush
