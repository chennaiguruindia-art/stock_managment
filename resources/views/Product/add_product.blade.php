@extends('layouts.app')

@section('page-title', 'Add Product')
@section('page-subtitle', 'Create a new product entry for stock management.')

@section('content')
    <style>
        .form-section {
            display: flex;
            align-items: center;
            gap: .6rem;
            margin: 1.6rem 0 .9rem;
        }

        .form-section:first-of-type {
            margin-top: 0;
        }

        .form-section .icon {
            width: 34px;
            height: 34px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            background: var(--accent-soft);
            color: var(--accent);
            border-radius: 10px;
            font-size: 1rem;
            flex-shrink: 0;
        }

        .form-section h6 {
            margin: 0;
            font-weight: 700;
        }

        .form-section .line {
            flex: 1;
            height: 1px;
            background: var(--border);
        }

        .form-label {
            font-weight: 600;
            font-size: .85rem;
            margin-bottom: .35rem;
        }

        .form-hint {
            font-size: .78rem;
            color: var(--muted);
            margin-top: .3rem;
        }

        .input-group-text {
            background: var(--surface-strong);
            border-color: var(--border);
        }

        .auto-field {
            background: #fbfaf8 !important;
            border-style: dashed !important;
            color: var(--muted);
            font-family: ui-monospace, SFMono-Regular, Menlo, Consolas, monospace;
        }

        .auto-tag {
            position: absolute;
            top: -9px;
            right: 10px;
            background: var(--accent-soft);
            color: var(--accent);
            font-size: .66rem;
            font-weight: 700;
            letter-spacing: .06em;
            text-transform: uppercase;
            padding: .15rem .5rem;
            border-radius: 999px;
        }

        .barcode-msg {
            display: block;
            margin-top: .3rem;
            font-size: .78rem;
            font-weight: 600;
        }
        .barcode-msg.ok { color: #16794c; }
        .barcode-msg.bad { color: #b3373f; }

        .preview-panel {
            position: sticky;
            top: 90px;
        }

        .preview-panel .preview-header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            font-weight: 700;
            margin-bottom: .4rem;
        }

        .preview-badge {
            background: var(--accent-soft);
            color: var(--accent);
            font-size: .68rem;
            font-weight: 700;
        }

        .preview-panel .hint {
            color: var(--muted);
            font-size: .82rem;
            margin-bottom: .8rem;
        }

        .preview-row {
            display: flex;
            justify-content: space-between;
            align-items: baseline;
            padding: .38rem 0;
            font-size: .9rem;
        }

        .preview-row span {
            color: var(--muted);
        }

        .preview-row strong {
            text-align: right;
            word-break: break-all;
            font-family: ui-monospace, SFMono-Regular, Menlo, Consolas, monospace;
        }

        .preview-divider {
            height: 1px;
            background: var(--border);
            margin: .5rem 0;
        }

        .btn-save {
            background: linear-gradient(135deg, var(--accent) 0%, #6f2b45 100%);
            border: none;
            color: #fff;
            font-weight: 600;
            padding: .7rem 2.2rem;
            border-radius: 12px;
            box-shadow: 0 10px 22px rgba(140, 55, 83, .28);
            transition: transform .15s ease, box-shadow .15s ease;
        }

        .btn-save:hover {
            color: #fff;
            transform: translateY(-1px);
            box-shadow: 0 14px 28px rgba(140, 55, 83, .34);
        }

        .btn-reset {
            border: 1px solid var(--border);
            background: var(--surface);
            color: var(--text);
            font-weight: 600;
            padding: .7rem 1.6rem;
            border-radius: 12px;
        }

        .variant-box {
            background: var(--surface-strong);
            border: 1px solid var(--border);
            border-radius: 16px;
            padding: 1.15rem 1.15rem .4rem;
        }

        .variant-box .form-label {
            display: flex;
            align-items: center;
            gap: .35rem;
        }

        .variant-box .form-label i {
            color: var(--accent);
            font-size: .8rem;
        }
    </style>

    <div class="row g-4">
        <div class="col-lg-8">
            <div class="card-panel">
                <form id="productForm" action="{{ route('add_product') }}" method="POST">
                    @csrf

                    <div class="form-section">
                        <span class="icon"><i class="bi bi-tag"></i></span>
                        <h6>Basic details</h6>
                        <span class="line"></span>
                    </div>
                    <div class="row g-3 mb-2">
                        <div class="col-md-6">
                            <label class="form-label">Product name</label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="bi bi-box-seam"></i></span>
                                <input type="text" class="form-control" placeholder="Enter product name" name="product_name" value="{{ old('product_name') }}">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Brand</label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="bi bi-stars"></i></span>
                                <select id="brandInput" class="form-select" name="brand">
                                    <option value="">Select brand</option>
                                    @foreach ($brands as $brand)
                                        <option value="{{ $brand->name }}" {{ old('brand') === $brand->name ? 'selected' : '' }}>{{ $brand->name }}</option>
                                    @endforeach
                                    <option value="__new" {{ old('brand') === '__new' ? 'selected' : '' }}>+ Add new brand</option>
                                </select>
                            </div>
                            <input id="newBrandInput" type="text" class="form-control mt-2" placeholder="Enter new brand name" name="new_brand_name" value="{{ old('new_brand_name') }}" style="display: none;">
                        </div>
                    </div>

                    <div class="form-section">
                        <span class="icon"><i class="bi bi-palette"></i></span>
                        <h6>Variant</h6>
                        <span class="line"></span>
                    </div>
                    <div class="variant-box mb-2">
                        <div class="row g-3">
                            <div class="col-md-3">
                                <label class="form-label"><i class="bi bi-tags"></i> Product type</label>
                                <select class="form-select" name="product_type">
                                    <option value="" {{ old('product_type') === '' ? 'selected' : '' }}>Choose type</option>
                                    @foreach (['Top', 'Pant', 'Kurthi', 'Churidar', 'Gown', 'Saree', 'Dress', 'Lehenga'] as $type)
                                        <option {{ old('product_type') === $type ? 'selected' : '' }}>{{ $type }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-3">
                                <label class="form-label"><i class="bi bi-droplet"></i> Color</label>
                                <input type="text" class="form-control" placeholder="Color" name="color" value="{{ old('color') }}">
                            </div>
                            <div class="col-md-3">
                                <label class="form-label"><i class="bi bi-rulers"></i> Size</label>
                                <select class="form-select" name="size">
                                    <option value="" {{ old('size') === '' ? 'selected' : '' }}>Select size</option>
                                    @foreach (['S', 'M', 'L', 'XL', 'XXL'] as $sz)
                                        <option value="{{ $sz }}" {{ old('size') === $sz ? 'selected' : '' }}>{{ $sz }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-3">
                                <label class="form-label"><i class="bi bi-box-seam"></i> Stock</label>
                                <div class="input-group">
                                    <span class="input-group-text"><i class="bi bi-box"></i></span>
                                    <input type="number" min="0" class="form-control" placeholder="0" name="stock" value="{{ old('stock', 0) }}">
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="form-section">
                        <span class="icon"><i class="bi bi-upc-scan"></i></span>
                        <h6>Auto-generated identifiers</h6>
                        <span class="line"></span>
                    </div>
                    <div class="row g-3 mb-2">
                        <div class="col-md-4">
                            <label class="form-label">Product ID</label>
                            <div class="position-relative">
                                <span class="auto-tag">auto</span>
                                <input id="productIdInput" type="text" class="form-control auto-field" placeholder="—" name="product_id" readonly>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">SKU</label>
                            <div class="position-relative">
                                <span class="auto-tag">auto</span>
                                <input id="skuInput" type="text" class="form-control auto-field" placeholder="—" name="sku" readonly>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Barcode</label>
                            <div class="position-relative">
                                <span class="auto-tag">auto</span>
                                <input id="barcodeInput" type="text" class="form-control auto-field" placeholder="—" name="barcode" value="{{ old('barcode') }}">
                                <small class="barcode-msg" id="barcodeMsg"></small>
                            </div>
                        </div>
                    </div>

                    <div class="form-section">
                        <span class="icon"><i class="bi bi-cash-coin"></i></span>
                        <h6>Pricing</h6>
                        <span class="line"></span>
                    </div>
                    <div class="row g-3 mb-2">
                        <div class="col-md-3">
                            <label class="form-label">Original price</label>
                            <div class="input-group">
                                <span class="input-group-text">₹</span>
                                <input id="originalPrice" type="number" step="0.01" class="form-control" placeholder="0.00" name="original_price" value="{{ old('original_price') }}">
                            </div>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">MRP</label>
                            <div class="input-group">
                                <span class="input-group-text">₹</span>
                                <input id="mrpPrice" type="number" step="0.01" class="form-control" placeholder="0.00" name="mrp" value="{{ old('mrp') }}">
                            </div>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">Selling price</label>
                            <div class="input-group">
                                <span class="input-group-text">₹</span>
                                <input id="sellingPrice" type="number" step="0.01" class="form-control" placeholder="0.00" name="selling_price" value="{{ old('selling_price') }}">
                            </div>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">Discount</label>
                            <div class="input-group">
                                <input id="discountAmount" type="text" class="form-control" readonly placeholder="₹0.00" name="discount_amount">
                                <span id="discountPercent" class="input-group-text">0%</span>
                            </div>
                        </div>
                    </div>

                    <div class="form-section">
                        <span class="icon"><i class="bi bi-chat-left-text"></i></span>
                        <h6>Description</h6>
                        <span class="line"></span>
                    </div>
                    <textarea class="form-control" rows="3" placeholder="Example: Lightweight chiffon kurthi for women, floral print" name="description">{{ old('description') }}</textarea>

                    <div class="d-flex flex-wrap gap-2 mt-4 pt-3 border-top">
                        <button type="submit" class="btn btn-save"><i class="bi bi-check-lg me-1"></i> Save product</button>
                        <a href="{{ route('add_product') }}" class="btn btn-reset"><i class="bi bi-arrow-counterclockwise me-1"></i> Reset</a>
                    </div>
                </form>
            </div>
        </div>

        <div class="col-lg-4">
            <div class="card-panel preview-panel">
                <div class="preview-header">
                    <span>Live preview</span>
                    <span class="badge preview-badge"><i class="bi bi-lightning-charge-fill me-1"></i>auto</span>
                </div>
                <div class="hint">Generated IDs and pricing update as you type.</div>

                <div class="preview-row"><span>Product</span><strong id="pvName">—</strong></div>
                <div class="preview-row"><span>Brand</span><strong id="pvBrand">—</strong></div>
                <div class="preview-row"><span>Variant</span><strong id="pvVariant">—</strong></div>

                <div class="preview-divider"></div>

                <div class="preview-row"><span>Product ID</span><strong id="pvPid">—</strong></div>
                <div class="preview-row"><span>SKU</span><strong id="pvSku">—</strong></div>
                <div class="preview-row"><span>Barcode</span><strong id="pvBarcode">—</strong></div>

                <div class="preview-divider"></div>

                <div class="preview-row"><span>Selling price</span><strong id="pvPrice">—</strong></div>
                <div class="preview-row"><span>Discount</span><strong id="pvDiscount">—</strong></div>
                <div class="preview-row"><span>Stock</span><strong id="pvStock">—</strong></div>
            </div>
        </div>
    </div>

    @push('scripts')
        <script>
            function formatCurrency(value) {
                return value === '' || isNaN(value) ? '₹0.00' : '₹' + parseFloat(value).toFixed(2);
            }

            const originalPrice = document.getElementById('originalPrice');
            const mrpPrice = document.getElementById('mrpPrice');
            const sellingPrice = document.getElementById('sellingPrice');
            const discountInput = document.getElementById('discountAmount');
            const percentInput = document.getElementById('discountPercent');

            function updateDiscount() {
                const original = parseFloat(originalPrice.value) || 0;
                const mrp = parseFloat(mrpPrice.value) || 0;
                const selling = parseFloat(sellingPrice.value) || 0;

                const activeMrp = mrp > 0 ? mrp : original;
                const discountValue = Math.max(0, activeMrp - selling);
                const percentValue = activeMrp > 0 ? Math.round((discountValue / activeMrp) * 100) : 0;

                discountInput.value = formatCurrency(discountValue);
                percentInput.textContent = percentValue + '%';
                document.getElementById('pvDiscount').textContent = discountInput.value;
            }

            [originalPrice, mrpPrice, sellingPrice].forEach(el => el.addEventListener('input', updateDiscount));

            const brandInput = document.getElementById('brandInput');
            const newBrandInput = document.getElementById('newBrandInput');
            const nameInput = document.querySelector('input[name="product_name"]');
            const typeInput = document.querySelector('select[name="product_type"]');
            const sizeInput = document.querySelector('select[name="size"]');
            const colorInput = document.querySelector('input[name="color"]');
            const stockInput = document.querySelector('input[name="stock"]');
            const skuInput = document.getElementById('skuInput');
            const barcodeInput = document.getElementById('barcodeInput');
            const productIdInput = document.getElementById('productIdInput');

            function getEffectiveBrand() {
                if (brandInput.value === '__new') {
                    return newBrandInput.value.trim();
                }
                return brandInput.value.trim();
            }

            function toggleNewBrandInput() {
                const showNew = brandInput.value === '__new';
                newBrandInput.style.display = showNew ? 'block' : 'none';
                if (showNew) newBrandInput.focus();
            }

            let debounceTimer = null;
            function fetchBrandInfo() {
                const brand = getEffectiveBrand();
                const size = sizeInput.value.trim();
                const color = colorInput.value.trim();
                const productType = typeInput.value.trim();
                if (!brand) return;

                const csrf = '{{ csrf_token() }}';

                fetch('{{ route("brand_info") }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': csrf,
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify({ brand, size, color, product_type: productType })
                }).then(r => r.json()).then(data => {
                    if (data.sku) skuInput.value = data.sku;
                    if (data.barcode) barcodeInput.value = data.barcode;
                    if (data.product_id) productIdInput.value = data.product_id;
                    updatePreview();
                }).catch(() => {});
            }

            function updatePreview() {
                const brand = getEffectiveBrand();
                document.getElementById('pvName').textContent = nameInput.value.trim() || '—';
                document.getElementById('pvBrand').textContent = brand || '—';
                document.getElementById('pvVariant').textContent =
                    [colorInput.value.trim(), sizeInput.value.trim()].filter(Boolean).join(' / ') || '—';
                document.getElementById('pvPid').textContent = productIdInput.value || '—';
                document.getElementById('pvSku').textContent = skuInput.value || '—';
                document.getElementById('pvBarcode').textContent = barcodeInput.value || '—';
                const selling = parseFloat(sellingPrice.value) || 0;
                document.getElementById('pvPrice').textContent = selling > 0 ? '₹' + selling.toFixed(2) : '—';
                document.getElementById('pvStock').textContent = stockInput.value || '0';
            }

            brandInput.addEventListener('change', () => {
                toggleNewBrandInput();
                clearTimeout(debounceTimer);
                debounceTimer = setTimeout(fetchBrandInfo, 100);
            });

            newBrandInput.addEventListener('input', () => {
                clearTimeout(debounceTimer);
                debounceTimer = setTimeout(fetchBrandInfo, 350);
            });

            [nameInput, sizeInput, colorInput, stockInput, sellingPrice].forEach(el => {
                el.addEventListener('input', updatePreview);
            });

            [nameInput, typeInput, sizeInput, colorInput].forEach(el => {
                el.addEventListener('input', () => {
                    clearTimeout(debounceTimer);
                    debounceTimer = setTimeout(fetchBrandInfo, 350);
                });
            });
            typeInput.addEventListener('change', () => {
                clearTimeout(debounceTimer);
                debounceTimer = setTimeout(fetchBrandInfo, 100);
            });

            toggleNewBrandInput();
            updateDiscount();
            updatePreview();
            if (getEffectiveBrand()) {
                fetchBrandInfo();
            }

            // --- Barcode: editable, last 4 digits must be unique ---
            let barcodeOk = true;
            let checkTimer = null;
            const barcodeMsg = document.getElementById('barcodeMsg');

            function checkBarcode() {
                const val = barcodeInput.value.trim();
                if (!val) {
                    barcodeOk = true;
                    barcodeMsg.textContent = '';
                    barcodeMsg.className = 'barcode-msg';
                    updatePreview();
                    return;
                }
                const csrf = '{{ csrf_token() }}';
                fetch('{{ route("barcode_check") }}', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': csrf },
                    body: JSON.stringify({ barcode: val })
                }).then(r => r.json()).then(data => {
                    barcodeOk = data.unique;
                    barcodeMsg.className = 'barcode-msg ' + (data.unique ? 'ok' : 'bad');
                    barcodeMsg.textContent = data.unique
                        ? 'Last 4 digits "' + data.last4 + '" are available.'
                        : 'Last 4 digits "' + data.last4 + '" already used.';
                    updatePreview();
                }).catch(() => {});
            }

            barcodeInput.addEventListener('input', () => {
                clearTimeout(checkTimer);
                checkTimer = setTimeout(checkBarcode, 300);
            });

            document.querySelector('#productForm').addEventListener('submit', e => {
                if (!barcodeOk) {
                    e.preventDefault();
                    barcodeMsg.className = 'barcode-msg bad';
                    barcodeMsg.textContent = 'Barcode last 4 digits already used. Please change it.';
                }
            });
        </script>
    @endpush
@endsection
