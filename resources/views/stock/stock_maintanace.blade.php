@extends('layouts.app')

@section('page-title', 'Stock management')
@section('page-subtitle', 'Adjust quantities, monitor levels, and keep your catalog available.')

@section('content')
    <style>
        .stock-stat {
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
        .stock-stat::before {
            content: "";
            position: absolute;
            left: 0; top: 0; bottom: 0;
            width: 3px;
            background: var(--tone);
        }
        .stock-stat .icon {
            position: absolute;
            right: 14px;
            top: 14px;
            width: 34px;
            height: 34px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            font-size: 1rem;
            border-radius: 8px;
            background: var(--tone-soft);
            color: var(--tone);
            opacity: 1;
            line-height: 1;
        }
        .stock-stat .label {
            font-size: .66rem;
            text-transform: uppercase;
            letter-spacing: .1em;
            font-weight: 700;
            color: var(--muted, #66748b);
        }
        .stock-stat .value {
            font-size: 1.75rem;
            font-weight: 800;
            line-height: 1.2;
            color: var(--text, #0e1726);
            margin-top: .25rem;
            font-variant-numeric: tabular-nums;
        }
        .st-total  { --tone: #1554d1; --tone-soft: #e7eefe; }
        .st-healthy{ --tone: #0f9d58; --tone-soft: #e4f7ec; }
        .st-low    { --tone: #d97706; --tone-soft: #fdf1e2; }
        .st-out    { --tone: #dc2626; --tone-soft: #fdeaea; }

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
                                    <input type="number" step="1" min="0" class="form-control" id="edit-dupatta_discount" name="dupatta_discount" placeholder="300">
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
