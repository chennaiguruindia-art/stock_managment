@extends('layouts.app')

@section('page-title', 'Stock management')
@section('page-subtitle', 'Adjust quantities, monitor levels, and keep your catalog available.')

@section('content')
    <style>
        /* ===== Compact single-row KPI strip ===== */
        .kpi-strip {
            display: grid;
            grid-template-columns: repeat(6, minmax(0, 1fr));
            gap: 1px;
            background: var(--border, #dfe4ee);
            border: 1px solid var(--border, #dfe4ee);
            border-radius: var(--radius, 12px);
            overflow: hidden;
            box-shadow: 0 1px 2px rgba(16, 24, 40, .05);
        }
        .kpi {
            --tone: var(--accent, #1554d1);
            --tone-soft: var(--accent-soft, #e7eefe);
            background: #fff;
            padding: .8rem 1rem;
            display: flex;
            align-items: center;
            gap: .7rem;
            min-width: 0;
            transition: background .15s ease;
        }
        .kpi:hover { background: #fbfcff; }
        .kpi-icon {
            width: 34px;
            height: 34px;
            flex: 0 0 34px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            border-radius: 9px;
            background: var(--tone-soft);
            color: var(--tone);
            font-size: .95rem;
        }
        .kpi-body { min-width: 0; }
        .kpi-label {
            font-size: .6rem;
            text-transform: uppercase;
            letter-spacing: .09em;
            font-weight: 700;
            color: var(--muted, #66748b);
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }
        .kpi-value {
            font-size: 1.4rem;
            font-weight: 800;
            line-height: 1.25;
            color: var(--text, #0e1726);
            font-variant-numeric: tabular-nums;
            white-space: nowrap;
        }
        .kpi-value .kpi-note {
            display: block;
            font-size: .63rem;
            font-weight: 600;
            color: var(--muted, #66748b);
            margin-top: .05rem;
        }
        .kpi-types    { --tone: #7c3aed; --tone-soft: #f1eafd; }
        .kpi-variants { --tone: #0891b2; --tone-soft: #e2f6fb; }
        .kpi-total    { --tone: #1554d1; --tone-soft: #e7eefe; }
        .kpi-healthy  { --tone: #0f9d58; --tone-soft: #e4f7ec; }
        .kpi-low      { --tone: #d97706; --tone-soft: #fdf1e2; }
        .kpi-out      { --tone: #dc2626; --tone-soft: #fdeaea; }
        @media (max-width: 1199.98px) {
            .kpi { padding: .7rem .75rem; gap: .55rem; }
            .kpi-icon { width: 28px; height: 28px; flex-basis: 28px; font-size: .82rem; border-radius: 7px; }
            .kpi-label { font-size: .56rem; }
            .kpi-value { font-size: 1.2rem; }
        }
        @media (max-width: 767.98px) {
            .kpi-strip { grid-template-columns: repeat(3, minmax(0, 1fr)); }
        }
        @media (max-width: 575.98px) {
            .kpi-strip { grid-template-columns: repeat(2, minmax(0, 1fr)); }
        }

        .stock-stepper {
            display: inline-flex;
            align-items: center;
            border: 1px solid var(--border-strong, #c5cede);
            border-radius: 8px;
            overflow: hidden;
            background: #fff;
        }
        .stock-stepper button {
            border: 0;
            background: var(--surface-2, #f7f9fc);
            width: 30px;
            height: 32px;
            font-size: 1rem;
            font-weight: 700;
            color: var(--accent, #1554d1);
            cursor: pointer;
        }
        .stock-stepper button:hover { background: var(--accent-soft, #e7eefe); }
        .stock-stepper input {
            width: 56px;
            border: 0;
            border-left: 1px solid var(--border, #dfe4ee);
            border-right: 1px solid var(--border, #dfe4ee);
            text-align: center;
            font-weight: 700;
            font-size: .88rem;
            height: 32px;
            background: #fff;
            font-variant-numeric: tabular-nums;
        }
        .stock-stepper input:focus { outline: none; }

        .filter-chip {
            border-radius: 8px;
            border: 1px solid var(--border-strong, #c5cede);
            background: #fff;
            padding: .4rem .85rem;
            font-size: .82rem;
            font-weight: 600;
            cursor: pointer;
            color: var(--muted, #66748b);
            transition: all .15s ease;
        }
        .filter-chip:hover { border-color: var(--accent, #1554d1); color: var(--accent, #1554d1); }
        .filter-chip.active {
            background: var(--accent, #1554d1);
            border-color: var(--accent, #1554d1);
            color: #fff;
        }

        .btn-icon {
            width: 32px;
            height: 32px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            border-radius: 8px;
            border: 1px solid var(--border-strong, #c5cede);
            background: #fff;
            color: var(--accent, #1554d1);
            font-size: .95rem;
            transition: all .15s ease;
        }
        .btn-icon:hover {
            background: var(--accent, #1554d1);
            border-color: var(--accent, #1554d1);
            color: #fff;
        }

        /* Edit product modal */
        .edit-modal .modal-content {
            border: 0;
            border-radius: var(--radius, 12px);
            box-shadow: 0 18px 50px rgba(16, 24, 40, .22);
        }
        .edit-modal .modal-header {
            border-bottom: 1px solid var(--border, #dfe4ee);
            padding: 1rem 1.25rem;
        }
        .edit-modal .modal-title {
            font-weight: 700;
            font-size: 1rem;
            display: flex;
            align-items: center;
            gap: .5rem;
        }
        .edit-modal .modal-title i { color: var(--accent, #1554d1); }
        .edit-modal .modal-body { padding: 1.25rem; }
        .edit-modal .modal-footer {
            border-top: 1px solid var(--border, #dfe4ee);
            padding: .9rem 1.25rem;
        }
        .edit-modal .f-label {
            font-size: .76rem;
            font-weight: 700;
            color: var(--muted, #66748b);
            text-transform: uppercase;
            letter-spacing: .05em;
            margin-bottom: .3rem;
            display: block;
        }
        .edit-modal .form-control,
        .edit-modal .form-select {
            border-radius: 8px;
            border-color: var(--border-strong, #c5cede);
            font-size: .9rem;
        }
        .edit-modal .form-control:focus,
        .edit-modal .form-select:focus {
            border-color: var(--accent, #1554d1);
            box-shadow: 0 0 0 .18rem rgba(21, 84, 209, .15);
        }
        .edit-modal .price-group { position: relative; }
        .edit-modal .price-prefix {
            position: absolute;
            left: .75rem;
            top: 50%;
            transform: translateY(-50%);
            font-weight: 700;
            color: var(--accent, #1554d1);
            font-size: .85rem;
            pointer-events: none;
            z-index: 2;
        }
        .edit-modal .price-group input { padding-left: 1.9rem; }
        .edit-modal .read-only-tag {
            background: var(--surface-2, #f7f9fc);
            border: 1px dashed var(--border-strong, #c5cede);
            border-radius: 8px;
            padding: .5rem .7rem;
            font-family: ui-monospace, Consolas, monospace;
            font-size: .78rem;
            color: var(--muted, #66748b);
        }

        /* ===== Panel head & toolbar ===== */
        .panel-head {
            display: flex;
            align-items: center;
            justify-content: space-between;
            flex-wrap: wrap;
            gap: .75rem;
            padding-bottom: .85rem;
            margin-bottom: .9rem;
            border-bottom: 1px solid var(--border, #dfe4ee);
        }
        .panel-head .panel-title { margin: 0; font-size: 1rem; }
        .count-pill {
            display: inline-block;
            margin-left: .5rem;
            padding: .12rem .55rem;
            border-radius: 999px;
            background: var(--accent-soft, #e7eefe);
            color: var(--accent-strong, #1043a8);
            font-size: .7rem;
            font-weight: 700;
            vertical-align: middle;
            font-variant-numeric: tabular-nums;
        }
        .toolbar {
            display: flex;
            flex-wrap: wrap;
            align-items: center;
            gap: .75rem;
            margin-bottom: .9rem;
        }
        .toolbar .search-box-input { max-width: 300px; }

        /* ===== Stock table ===== */
        #stockTable thead th {
            background: transparent;
            font-size: .64rem;
            text-transform: uppercase;
            letter-spacing: .08em;
            color: var(--muted, #66748b);
            font-weight: 700;
            border-bottom: 1px solid var(--border, #dfe4ee);
            white-space: nowrap;
            padding-top: .7rem;
            padding-bottom: .7rem;
        }
        #stockTable tbody td {
            font-size: .87rem;
            padding: .65rem .6rem;
            border-color: var(--border, #dfe4ee);
            vertical-align: middle;
        }
        #stockTable tbody tr:nth-child(even) td { background: #fbfcfe; }
        #stockTable tbody tr:hover td { background: #f2f7fd; }
        .p-name { font-weight: 700; font-size: .89rem; color: var(--text, #0e1726); }
        .p-meta { font-size: .72rem; color: var(--muted, #66748b); }
        .size-tag {
            display: inline-block;
            margin-left: .4rem;
            padding: .05rem .4rem;
            border-radius: 5px;
            background: var(--surface-3, #eaeff7);
            border: 1px solid var(--border, #dfe4ee);
            color: var(--muted, #66748b);
            font-size: .62rem;
            font-weight: 700;
            letter-spacing: .04em;
            vertical-align: middle;
        }
        .stock-num {
            font-weight: 800;
            font-size: 1rem;
            font-variant-numeric: tabular-nums;
        }
        .stock-num small {
            font-weight: 600;
            font-size: .68rem;
            color: var(--muted, #66748b);
            margin-left: .15rem;
        }
        .st-badge {
            display: inline-flex;
            align-items: center;
            gap: .35rem;
            padding: .22rem .6rem;
            border-radius: 999px;
            font-size: .7rem;
            font-weight: 700;
            white-space: nowrap;
        }
        .st-badge::before {
            content: "";
            width: 6px;
            height: 6px;
            border-radius: 50%;
            background: currentColor;
        }
        .st-ok   { background: #e4f7ec; color: #0f7c45; }
        .st-warn { background: #fdf1e2; color: #b45309; }
        .st-bad  { background: #fdeaea; color: #c81e1e; }
    </style>

    @include('layouts.alerts')

    <div class="kpi-strip mb-4">
        <div class="kpi kpi-types">
            <span class="kpi-icon"><i class="bi bi-grid-1x2-fill"></i></span>
            <div class="kpi-body">
                <div class="kpi-label">Product types</div>
                <div class="kpi-value">{{ $productTypes }}<span class="kpi-note">{{ $brands }} {{ Str::plural('brand', $brands) }}</span></div>
            </div>
        </div>
        <div class="kpi kpi-variants">
            <span class="kpi-icon"><i class="bi bi-collection-fill"></i></span>
            <div class="kpi-body">
                <div class="kpi-label">Size variants</div>
                <div class="kpi-value">{{ $totalProducts }}<span class="kpi-note">size rows</span></div>
            </div>
        </div>
        <div class="kpi kpi-total">
            <span class="kpi-icon"><i class="bi bi-box-seam"></i></span>
            <div class="kpi-body">
                <div class="kpi-label">Stock units</div>
                <div class="kpi-value">{{ $totalStock }}<span class="kpi-note">pieces</span></div>
            </div>
        </div>
        <div class="kpi kpi-healthy">
            <span class="kpi-icon"><i class="bi bi-check-circle-fill"></i></span>
            <div class="kpi-body">
                <div class="kpi-label">Healthy</div>
                <div class="kpi-value">{{ $healthyStock }}<span class="kpi-note">5+ pcs</span></div>
            </div>
        </div>
        <div class="kpi kpi-low">
            <span class="kpi-icon"><i class="bi bi-exclamation-triangle"></i></span>
            <div class="kpi-body">
                <div class="kpi-label">Low stock</div>
                <div class="kpi-value">{{ $lowStock }}<span class="kpi-note">1&ndash;4 pcs</span></div>
            </div>
        </div>
        <div class="kpi kpi-out">
            <span class="kpi-icon"><i class="bi bi-bag-x"></i></span>
            <div class="kpi-body">
                <div class="kpi-label">Out of stock</div>
                <div class="kpi-value">{{ $outOfStock }}<span class="kpi-note">0 pcs</span></div>
            </div>
        </div>
    </div>

    <div class="panel">
        <div class="panel-head">
            <div class="panel-title"><i class="bi bi-sliders"></i> Inventory control <span class="count-pill" id="resultCount">{{ $products->count() }}</span></div>
            <div class="d-flex gap-2">
                <a href="{{ route('stock_export_excel') }}" class="btn btn-sm btn-outline-accent"><i class="bi bi-file-earmark-excel me-1"></i>Excel</a>
                <a href="{{ route('stock_export_pdf') }}" class="btn btn-sm btn-outline-accent"><i class="bi bi-file-earmark-pdf me-1"></i>PDF</a>
            </div>
        </div>

        <div class="toolbar">
            <input type="text" id="searchInput" class="search-box-input" placeholder="Search product, brand, SKU...">
            <div class="d-flex gap-2 flex-wrap" id="filterChips">
                <button class="filter-chip active" data-filter="all">All</button>
                <button class="filter-chip" data-filter="ok">Healthy</button>
                <button class="filter-chip" data-filter="low">Low</button>
                <button class="filter-chip" data-filter="out">Out of stock</button>
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
                        <th>Price</th>
                        <th>Stock</th>
                        <th>Status</th>
                        <th>Adjust</th>
                        <th class="text-center">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($products as $product)
                        @php
                            $status = $product->stock <= 0 ? 'out' : ($product->stock < 5 ? 'low' : 'ok');
                            $badge = $status === 'out' ? 'st-bad' : ($status === 'low' ? 'st-warn' : 'st-ok');
                            $label = $status === 'out' ? 'Out of stock' : ($status === 'low' ? 'Low' : 'Healthy');
                        @endphp
                        <tr data-status="{{ $status }}"
                            data-search="{{ strtolower($product->product_id . ' ' . $product->product_name . ' ' . $product->brand . ' ' . $product->sku . ' ' . $product->barcode) }}">
                            <td><span class="chip-sku">{{ $product->product_id ?: '-' }}</span></td>
                            <td>
                                <div class="p-name">{{ $product->product_name }}@if ($product->size)<span class="size-tag">{{ $product->size }}</span>@endif</div>
                                <div class="p-meta">{{ $product->brand }} &middot; {{ $product->product_type }}</div>
                            </td>
                            <td><span class="chip-sku">{{ $product->sku }}</span></td>
                            <td><span class="chip-barcode">{{ $product->barcode }}</span></td>
                            <td>
                                @if ($product->selling_price !== null)
                                    <div class="fw-bold">&#8377;{{ number_format((float) $product->selling_price, 2) }}</div>
                                    @if ($product->mrp !== null && (float) $product->mrp > (float) $product->selling_price)
                                        <div class="text-muted" style="font-size:.75rem; text-decoration:line-through;">&#8377;{{ number_format((float) $product->mrp, 2) }}</div>
                                    @endif
                                @else
                                    <span class="text-muted">&mdash;</span>
                                @endif
                            </td>
                            <td><span class="stock-num">{{ $product->stock }}<small>pcs</small></span></td>
                            <td><span class="st-badge {{ $badge }}">{{ $label }}</span></td>
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
                            <td class="text-center">
                                <button type="button"
                                        class="btn btn-sm btn-icon edit-product-btn"
                                        title="Edit product"
                                        data-id="{{ $product->id }}"
                                        data-sku="{{ $product->sku }}"
                                        data-barcode="{{ $product->barcode }}"
                                        data-product_id="{{ $product->product_id }}"
                                        data-product_name="{{ $product->product_name }}"
                                        data-product_type="{{ $product->product_type }}"
                                        data-color="{{ $product->color }}"
                                        data-size="{{ $product->size }}"
                                        data-stock="{{ $product->stock }}"
                                        data-original_price="{{ $product->original_price }}"
                                        data-mrp="{{ $product->mrp }}"
                                        data-selling_price="{{ $product->selling_price }}"
                                        data-has_dupatta="{{ $product->has_dupatta ? 1 : 0 }}"
                                        data-dupatta_discount="{{ $product->dupatta_discount }}">
                                    <i class="bi bi-pencil-square"></i>
                                </button>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="9" class="text-center text-muted py-4">
                                No products yet. <a href="{{ route('add_product') }}">Add your first product</a>.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    {{-- ===== Edit product modal ===== --}}
    <div class="modal fade edit-modal" id="editProductModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-centered">
            <div class="modal-content">
                <form method="POST" action="{{ route('product_update') }}" id="editProductForm">
                    @csrf
                    <input type="hidden" name="id" id="edit-id" value="">

                    <div class="modal-header">
                        <h5 class="modal-title"><i class="bi bi-pencil-square"></i> Edit product</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>

                    <div class="modal-body">
                        <div class="row g-3">
                            <div class="col-md-8">
                                <label class="f-label" for="edit-product_name"><i class="bi bi-box-seam me-1"></i>Product name</label>
                                <input type="text" class="form-control" id="edit-product_name" name="product_name" required maxlength="255">
                            </div>
                            <div class="col-md-4">
                                <label class="f-label" for="edit-product_type"><i class="bi bi-tags me-1"></i>Product type</label>
                                <select class="form-select" id="edit-product_type" name="product_type">
                                    <option value="">Choose type</option>
                                    @foreach (['1 Piece set (Top)', '2 Pcs set (kurthi &shawl)', '2 Pcs set (kurthi &Pant)', '3 Pcs Set', 'Anarkali', 'Anarkali with shawl', 'Short Kurthi ', 'Saree', 'Lehenga','Night Wear','Kids Wear','Lingerie','Bralette','Dupatta'] as $type)
                                        <option value="{{ $type }}">{{ $type }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-4">
                                <label class="f-label" for="edit-color"><i class="bi bi-droplet me-1"></i>Color</label>
                                <input type="text" class="form-control" id="edit-color" name="color" maxlength="255" placeholder="e.g. Red">
                            </div>
                            <div class="col-md-4">
                                <label class="f-label" for="edit-size"><i class="bi bi-rulers me-1"></i>Size</label>
                                <select class="form-select" id="edit-size" name="size">
                                    <option value="">Select size</option>
                                    @foreach (['XS', 'S', 'M', 'L', 'XL', 'XXL'] as $sz)
                                        <option value="{{ $sz }}">{{ $sz }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-4">
                                <label class="f-label" for="edit-stock"><i class="bi bi-box me-1"></i>Stock</label>
                                <input type="number" min="0" class="form-control" id="edit-stock" name="stock" required>
                            </div>

                            <div class="col-12"><hr class="my-1"></div>

                            <div class="col-md-3">
                                <label class="f-label" for="edit-original_price"><i class="bi bi-tag me-1"></i>Original price</label>
                                <div class="price-group">
                                    <span class="price-prefix">&#8377;</span>
                                    <input type="number" step="0.01" min="0" class="form-control" id="edit-original_price" name="original_price" placeholder="0.00">
                                </div>
                            </div>
                            <div class="col-md-3">
                                <label class="f-label" for="edit-mrp"><i class="bi bi-bookmark me-1"></i>MRP</label>
                                <div class="price-group">
                                    <span class="price-prefix">&#8377;</span>
                                    <input type="number" step="0.01" min="0" class="form-control" id="edit-mrp" name="mrp" placeholder="0.00">
                                </div>
                            </div>
                            <div class="col-md-3">
                                <label class="f-label" for="edit-selling_price"><i class="bi bi-currency-rupee me-1"></i>Selling price</label>
                                <div class="price-group">
                                    <span class="price-prefix">&#8377;</span>
                                    <input type="number" step="0.01" min="0" class="form-control" id="edit-selling_price" name="selling_price" placeholder="0.00">
                                </div>
                            </div>
                            <div class="col-md-3">
                                <label class="f-label" for="edit-dupatta_discount"><i class="bi bi-scissors me-1"></i>Without dupatta, reduce</label>
                                <div class="price-group">
                                    <span class="price-prefix">&#8377;</span>
                                    <input type="number" step="1" min="0" class="form-control" id="edit-dupatta_discount" name="dupatta_discount" placeholder="350">
                                </div>
                            </div>

                            <div class="col-12">
                                <div class="form-check form-switch">
                                    <input class="form-check-input" type="checkbox" role="switch" id="edit-has_dupatta" name="has_dupatta" value="1">
                                    <label class="form-check-label fw-semibold" for="edit-has_dupatta">
                                        <i class="bi bi-scissors me-1" style="color:var(--accent);"></i> This product comes with a dupatta
                                    </label>
                                </div>
                            </div>

                            <div class="col-12 d-flex gap-2 flex-wrap">
                                <div class="read-only-tag">SKU: <span id="edit-sku-tag">-</span></div>
                                <div class="read-only-tag">Barcode: <span id="edit-barcode-tag">-</span></div>
                                <div class="read-only-tag">Product ID: <span id="edit-pid-tag">-</span></div>
                            </div>
                        </div>
                    </div>

                    <div class="modal-footer">
                        <button type="button" class="btn btn-sm px-3 py-2 fw-semibold" style="border:1px solid var(--border-strong,#c5cede); border-radius:8px; background:#fff;" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-sm px-4 py-2 fw-semibold text-white" style="border-radius:8px; background:var(--accent,#1554d1); border:none;">
                            <i class="bi bi-check2-circle me-1"></i>Save changes
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        @php
            $editOldPayload = old('id') ? [
                'id' => old('id'),
                'product_name' => old('product_name'),
                'product_type' => old('product_type'),
                'color' => old('color'),
                'size' => old('size'),
                'stock' => old('stock'),
                'original_price' => old('original_price'),
                'mrp' => old('mrp'),
                'selling_price' => old('selling_price'),
                'has_dupatta' => old('has_dupatta'),
                'dupatta_discount' => old('dupatta_discount'),
            ] : null;
        @endphp

        const searchInput = document.getElementById('searchInput');
        const chips = document.querySelectorAll('.filter-chip');
        const rows = document.querySelectorAll('#stockTable tbody tr[data-status]');
        const resultCount = document.getElementById('resultCount');

        function applyFilters() {
            const q = searchInput.value.trim().toLowerCase();
            const active = document.querySelector('.filter-chip.active').dataset.filter;
            let visible = 0;
            rows.forEach(row => {
                const statusOk = active === 'all' || row.dataset.status === active;
                const searchOk = q === '' || row.dataset.search.includes(q);
                const show = statusOk && searchOk;
                row.style.display = show ? '' : 'none';
                if (show) visible++;
            });
            if (resultCount) resultCount.textContent = visible + ' / ' + rows.length;
        }
        applyFilters();

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

        // ===== Edit product modal =====
        const editModalEl = document.getElementById('editProductModal');
        const editForm = document.getElementById('editProductForm');
        const editModal = bootstrap.Modal.getOrCreateInstance(editModalEl);

        const setField = (name, value) => {
            const el = editForm.elements[name];
            if (!el) return;
            if (el.type === 'checkbox') {
                el.checked = value === '1' || value === 1 || value === true;
            } else {
                el.value = (value === null || value === undefined || value === 'null') ? '' : value;
            }
        };

        function fillEditModal(d) {
            editForm.elements['id'].value = d.id || '';
            setField('product_name', d.product_name);
            setField('product_type', d.product_type);
            setField('color', d.color);
            setField('size', d.size);
            setField('stock', d.stock);
            setField('original_price', d.original_price);
            setField('mrp', d.mrp);
            setField('selling_price', d.selling_price);
            setField('has_dupatta', d.has_dupatta);
            setField('dupatta_discount', d.dupatta_discount);
            document.getElementById('edit-sku-tag').textContent = d.sku || '-';
            document.getElementById('edit-barcode-tag').textContent = d.barcode || '-';
            document.getElementById('edit-pid-tag').textContent = d.product_id || '-';
        }

        document.querySelectorAll('.edit-product-btn').forEach(btn => {
            btn.addEventListener('click', () => {
                fillEditModal(btn.dataset);
                editModal.show();
            });
        });

        // Reopen the modal with the values just submitted if validation failed.
        @if (old('id'))
            fillEditModal(@json($editOldPayload));
            editModal.show();
        @endif
    </script>
@endpush
