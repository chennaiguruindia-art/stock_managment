@extends('layouts.app')

@section('page-title', 'Product Barcodes')
@section('page-subtitle', 'Pick a product to open its printable barcode.')

@section('content')
    <style>
        .bc-card {
            background: #fff;
            border: 1px solid var(--line, #dfe4ee);
            border-radius: var(--radius, 10px);
            box-shadow: 0 1px 2px rgba(16, 24, 40, .05);
            padding: 1.15rem 1.25rem;
        }
        .bc-search {
            border: 1px solid var(--border-strong, #c5cede);
            border-radius: 8px;
            padding: .5rem .9rem .5rem 2.2rem;
            width: 100%;
            background: #fff url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='16' height='16' fill='%2366748b' viewBox='0 0 16 16'%3E%3Cpath d='M11.742 10.344a6.5 6.5 0 1 0-1.397 1.398h-.001c.03.04.062.078.098.115l3.85 3.85a1 1 0 0 0 1.415-1.414l-3.85-3.85a1.007 1.007 0 0 0-.115-.1zM12 6.5a5.5 5.5 0 1 1-11 0 5.5 5.5 0 0 1 11 0z'/%3E%3C/svg%3E") no-repeat .75rem center;
            font-size: .875rem;
        }
        .bc-search:focus { outline: none; border-color: var(--accent, #1554d1); box-shadow: 0 0 0 3px rgba(21, 84, 209, .13); }
        .bc-count {
            font-size: .82rem;
            color: var(--muted, #66748b);
            font-weight: 600;
        }
        .bc-table-wrap {
            max-height: 62vh;
            overflow-y: auto;
            border: 1px solid var(--line, #dfe4ee);
            border-radius: var(--radius, 10px);
        }
        .bc-table { margin: 0; }
        .bc-table thead th {
            position: sticky;
            top: 0;
            background: var(--surface-2, #f7f9fc);
            z-index: 1;
            font-size: .68rem;
            text-transform: uppercase;
            letter-spacing: .07em;
            color: var(--muted, #66748b);
            font-weight: 700;
            border-bottom: 1px solid var(--line, #dfe4ee);
            padding: .6rem .75rem;
        }
        .bc-table tbody td {
            padding: .6rem .75rem;
            vertical-align: middle;
            border-top: 1px solid var(--line, #dfe4ee);
            font-size: .875rem;
        }
        .bc-table tbody tr { cursor: pointer; transition: background .12s ease; }
        .bc-table tbody tr:hover { background: #f6f9fe; }
        .bc-id {
            font-family: ui-monospace, Consolas, monospace;
            font-size: .78rem;
            font-weight: 700;
            color: var(--accent-strong, #1043a8);
            background: var(--accent-soft, #e7eefe);
            border: 1px solid #cdddfb;
            border-radius: 6px;
            padding: .18rem .45rem;
        }
        .bc-barcode {
            font-family: ui-monospace, Consolas, monospace;
            font-size: .78rem;
            color: #24344f;
            background: var(--surface-3, #eaeff7);
            border: 1px solid var(--line, #dfe4ee);
            border-radius: 6px;
            padding: .18rem .45rem;
        }
        .bc-link {
            color: var(--accent, #1554d1);
            font-weight: 600;
            font-size: .82rem;
            text-decoration: none;
            white-space: nowrap;
        }
        .bc-link:hover { text-decoration: underline; }
        .bc-empty {
            text-align: center;
            color: var(--muted, #66748b);
            padding: 3rem 1rem;
        }
        .bc-empty i { font-size: 2.4rem; color: var(--border-strong, #c5cede); }
    </style>

    <div class="bc-card">
        <div class="d-flex flex-wrap align-items-center gap-3 mb-3">
            <div class="me-auto">
                <div class="section-title d-flex align-items-center gap-2 mb-1">
                    <i class="bi bi-upc-scan" style="color: var(--accent);"></i>
                    Product barcodes
                </div>
                <div class="text-muted" style="font-size: .86rem;">
                    Click a product to open and print its barcode.
                </div>
            </div>
            <div class="position-relative" style="min-width: 260px;">
                <input type="text" id="bcSearch" class="bc-search" placeholder="Search by name, brand or id...">
            </div>
            <span class="bc-count" id="bcCount">{{ count($products) }} product(s)</span>
        </div>

        @if (count($products) === 0)
            <div class="bc-empty">
                <i class="bi bi-box-seam mb-2 d-block"></i>
                No products found. Add a product first.
            </div>
        @else
            <div class="bc-table-wrap">
                <table class="table bc-table">
                    <thead>
                        <tr>
                            <th style="width: 110px;">Product ID</th>
                            <th>Product name</th>
                            <th style="width: 150px;">Brand</th>
                            <th style="width: 170px;">Barcode</th>
                            <th style="width: 90px; text-align: right;">Action</th>
                        </tr>
                    </thead>
                    <tbody id="bcBody">
                        @foreach ($products as $product)
                            <tr data-search="{{ Str::lower($product->product_name . ' ' . $product->brand . ' ' . $product->product_id . ' ' . $product->barcode) }}">
                                <td><span class="bc-id">{{ $product->product_id }}</span></td>
                                <td><strong>{{ $product->product_name }}</strong></td>
                                <td>{{ $product->brand }}</td>
                                <td><span class="bc-barcode">{{ $product->barcode }}</span></td>
                                <td class="text-end">
                                    <a class="bc-link" href="{{ route('barcode_detail', $product) }}">
                                        <i class="bi bi-upc-scan me-1"></i>Barcode
                                    </a>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    </div>

    <div class="card-panel mt-4">
        <div class="section-title">How it works</div>
        <p class="text-muted mb-0" style="font-size: .88rem;">
            Open any product's barcode, print it, and scan it with a barcode scanner (or the POS barcode lookup) to find and sell that product instantly.
        </p>
    </div>

    @push('scripts')
        <script>
            const searchInput = document.getElementById('bcSearch');
            const rows = document.querySelectorAll('#bcBody tr');
            const count = document.getElementById('bcCount');

            searchInput.addEventListener('input', () => {
                const q = searchInput.value.trim().toLowerCase();
                let visible = 0;
                rows.forEach(row => {
                    const match = !q || row.dataset.search.includes(q);
                    row.style.display = match ? '' : 'none';
                    if (match) visible++;
                });
                count.textContent = visible + ' product(s)';
            });
        </script>
    @endpush
@endsection
