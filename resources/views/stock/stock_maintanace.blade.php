@extends('layouts.app')

@section('page-title', 'Stock management')
@section('page-subtitle', 'Adjust quantities, monitor levels, and keep your catalog available.')

@section('content')
    <style>
        .stock-stat {
            border-radius: 18px;
            padding: 1.2rem 1.4rem;
            color: #fff;
            position: relative;
            overflow: hidden;
            box-shadow: 0 12px 26px rgba(15, 14, 15, .12);
        }
        .stock-stat .icon {
            position: absolute;
            right: -12px;
            bottom: -12px;
            font-size: 4rem;
            opacity: .18;
            line-height: 1;
        }
        .stock-stat .label { font-size: .78rem; text-transform: uppercase; letter-spacing: .12em; opacity: .85; }
        .stock-stat .value { font-size: 2rem; font-weight: 800; line-height: 1.2; }
        .st-total { background: linear-gradient(135deg, #2e2740, #4a3b68); }
        .st-healthy { background: linear-gradient(135deg, #16794c, #2bb673); }
        .st-low { background: linear-gradient(135deg, #c07d10, #e6a23c); }
        .st-out { background: linear-gradient(135deg, #b3373f, #e05c5c); }

        .stock-stepper {
            display: inline-flex;
            align-items: center;
            border: 1px solid #e5e2dc;
            border-radius: 10px;
            overflow: hidden;
            background: #fbfaf8;
        }
        .stock-stepper button {
            border: 0;
            background: transparent;
            width: 30px;
            height: 32px;
            font-size: 1rem;
            font-weight: 700;
            color: #6b1f2a;
            cursor: pointer;
        }
        .stock-stepper button:hover { background: #f3e2e3; }
        .stock-stepper input {
            width: 56px;
            border: 0;
            text-align: center;
            font-weight: 700;
            font-size: .9rem;
            height: 32px;
            background: #fff;
        }
        .stock-stepper input:focus { outline: none; }

        .filter-chip {
            border-radius: 999px;
            border: 1px solid #e5e2dc;
            background: #fbfaf8;
            padding: .45rem .95rem;
            font-size: .85rem;
            font-weight: 600;
            cursor: pointer;
            color: #6f6d6a;
        }
        .filter-chip.active {
            background: #6b1f2a;
            border-color: #6b1f2a;
            color: #fff;
        }
    </style>

    @include('layouts.alerts')

    <div class="row g-4 mb-4">
        <div class="col-lg-3 col-sm-6">
            <div class="stock-stat st-total">
                <i class="bi bi-grid-1x2-fill icon"></i>
                <div class="label">Total products</div>
                <div class="value">{{ $totalProducts }}</div>
            </div>
        </div>
        <div class="col-lg-3 col-sm-6">
            <div class="stock-stat st-healthy">
                <i class="bi bi-box-seam icon"></i>
                <div class="label">Items in stock</div>
                <div class="value">{{ $totalStock }}</div>
            </div>
        </div>
        <div class="col-lg-3 col-sm-6">
            <div class="stock-stat st-low">
                <i class="bi bi-exclamation-triangle icon"></i>
                <div class="label">Low stock</div>
                <div class="value">{{ $lowStock }}</div>
            </div>
        </div>
        <div class="col-lg-3 col-sm-6">
            <div class="stock-stat st-out">
                <i class="bi bi-bag-x icon"></i>
                <div class="label">Out of stock</div>
                <div class="value">{{ $outOfStock }}</div>
            </div>
        </div>
    </div>

    <div class="panel">
        <div class="panel-title"><i class="bi bi-sliders"></i> Inventory control</div>

        <div class="d-flex flex-wrap gap-3 align-items-center mb-3">
            <input type="text" id="searchInput" class="search-box-input" style="max-width:260px;" placeholder="Search product, brand, SKU...">
            <div class="d-flex gap-2 flex-wrap" id="filterChips">
                <button class="filter-chip active" data-filter="all">All</button>
                <button class="filter-chip" data-filter="ok">Healthy</button>
                <button class="filter-chip" data-filter="low">Low</button>
                <button class="filter-chip" data-filter="out">Out of stock</button>
            </div>
            <div class="ms-auto d-flex gap-2">
                <a href="{{ route('stock_export_excel') }}" class="btn btn-sm btn-outline-accent"><i class="bi bi-file-earmark-excel me-1"></i>Excel</a>
                <a href="{{ route('stock_export_pdf') }}" class="btn btn-sm btn-outline-accent"><i class="bi bi-file-earmark-pdf me-1"></i>PDF</a>
            </div>
        </div>

        <div class="table-responsive">
            <table class="table align-middle" id="stockTable">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Product</th>
                        <th>SKU</th>
                        <th>Barcode</th>
                        <th>Stock</th>
                        <th>Status</th>
                        <th>Adjust</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($products as $product)
                        @php
                            $status = $product->stock <= 0 ? 'out' : ($product->stock < 5 ? 'low' : 'ok');
                            $badge = $status === 'out' ? 'bg-danger' : ($status === 'low' ? 'bg-warning text-dark' : 'bg-success');
                            $label = $status === 'out' ? 'Out of stock' : ($status === 'low' ? 'Low' : 'Healthy');
                        @endphp
                        <tr data-status="{{ $status }}"
                            data-search="{{ strtolower($product->product_id . ' ' . $product->product_name . ' ' . $product->brand . ' ' . $product->sku . ' ' . $product->barcode) }}">
                            <td><span class="chip-sku">{{ $product->product_id ?: '-' }}</span></td>
                            <td>
                                <div class="fw-semibold">{{ $product->product_name }}</div>
                                <div class="text-muted" style="font-size:.78rem;">{{ $product->brand }} &middot; {{ $product->product_type }}</div>
                            </td>
                            <td><span class="chip-sku">{{ $product->sku }}</span></td>
                            <td><span class="chip-barcode">{{ $product->barcode }}</span></td>
                            <td class="fw-bold">{{ $product->stock }}</td>
                            <td><span class="badge {{ $badge }} rounded-pill">{{ $label }}</span></td>
                            <td>
                                <form method="POST" action="{{ route('stock_update') }}" class="d-inline-flex align-items-center gap-2">
                                    @csrf
                                    <input type="hidden" name="id" value="{{ $product->id }}">
                                    <div class="stock-stepper">
                                        <button type="button" data-step="-1" data-target="stock-{{ $product->id }}">-</button>
                                        <input type="number" name="stock" id="stock-{{ $product->id }}" value="{{ $product->stock }}" min="0">
                                        <button type="button" data-step="1" data-target="stock-{{ $product->id }}">+</button>
                                    </div>
                                    <button type="submit" class="btn btn-sm btn-outline-accent">Update</button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="text-center text-muted py-4">
                                No products yet. <a href="{{ route('add_product') }}">Add your first product</a>.
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
        const searchInput = document.getElementById('searchInput');
        const chips = document.querySelectorAll('.filter-chip');
        const rows = document.querySelectorAll('#stockTable tbody tr[data-status]');

        function applyFilters() {
            const q = searchInput.value.trim().toLowerCase();
            const active = document.querySelector('.filter-chip.active').dataset.filter;
            rows.forEach(row => {
                const statusOk = active === 'all' || row.dataset.status === active;
                const searchOk = q === '' || row.dataset.search.includes(q);
                row.style.display = statusOk && searchOk ? '' : 'none';
            });
        }

        searchInput.addEventListener('input', applyFilters);
        chips.forEach(chip => {
            chip.addEventListener('click', () => {
                chips.forEach(c => c.classList.remove('active'));
                chip.classList.add('active');
                applyFilters();
            });
        });

        document.querySelectorAll('.stock-stepper button[data-step]').forEach(btn => {
            btn.addEventListener('click', () => {
                const input = document.getElementById(btn.dataset.target);
                let val = parseInt(input.value || '0', 10) + parseInt(btn.dataset.step, 10);
                if (val < 0) val = 0;
                input.value = val;
            });
        });
    </script>
@endpush
