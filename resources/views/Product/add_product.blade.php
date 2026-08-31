@extends('layouts.app')

@section('page-title', 'Add Product')
@section('page-subtitle', 'Create a new product entry for stock management.')

@section('content')
    <style>
        .page-form {
            --accent-2: #7a2b3a;
            --soft: #f7f4f1;
            --line: #ece6e0;
            --ink-muted: #8a7f7a;
        }

        /* ===== Import / bulk upload ===== */
        .import-card {
            background: linear-gradient(180deg, #fffdfb 0%, #ffffff 100%);
            border: 1px solid var(--line);
            border-radius: 20px;
            overflow: hidden;
            box-shadow: 0 14px 40px rgba(64, 0, 0, 0.08);
        }
        .import-head {
            display: flex;
            align-items: center;
            gap: 1rem;
            padding: 1.25rem 1.5rem;
            border-bottom: 1px solid var(--line);
            flex-wrap: wrap;
        }
        .import-head .im-icon {
            width: 46px;
            height: 46px;
            flex-shrink: 0;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            border-radius: 14px;
            background: linear-gradient(135deg, #f3e2e3 0%, #fbe9de 100%);
            color: var(--accent);
            font-size: 1.35rem;
        }
        .import-head h2 {
            font-size: 1.15rem;
            font-weight: 700;
            margin: 0;
        }
        .import-head p {
            margin: 0;
            color: var(--ink-muted);
            font-size: .86rem;
        }
        .import-body {
            padding: 1.5rem;
        }
        .dropzone {
            border: 1.6px dashed #d8cdc4;
            border-radius: 16px;
            background: #fdfbf9;
            padding: 1.5rem;
            text-align: center;
            transition: border-color .18s ease, background .18s ease;
            cursor: pointer;
        }
        .dropzone:hover, .dropzone.has-file {
            border-color: var(--accent);
            background: #fbf3f1;
        }
        .dropzone .dz-icon {
            font-size: 1.8rem;
            color: var(--accent);
            margin-bottom: .4rem;
        }
        .dropzone .dz-title {
            font-weight: 700;
            color: var(--text);
        }
        .dropzone .dz-sub {
            font-size: .82rem;
            color: var(--ink-muted);
        }
        .dropzone .dz-file {
            font-weight: 700;
            color: var(--accent);
        }
        .chips {
            display: flex;
            flex-wrap: wrap;
            gap: .45rem;
        }
        .chips .chip {
            font-family: ui-monospace, Consolas, monospace;
            font-size: .74rem;
            font-weight: 600;
            color: var(--accent);
            background: var(--accent-soft);
            border-radius: 8px;
            padding: .28rem .55rem;
        }
        .btn-sample {
            border: 1px solid var(--line);
            background: #fff;
            color: var(--text);
            font-weight: 600;
            border-radius: 12px;
            padding: .55rem 1.1rem;
            transition: background .15s ease, border-color .15s ease;
        }
        .btn-sample:hover {
            background: var(--soft);
            border-color: #d6cbc2;
            color: var(--text);
        }
        .btn-sample i { color: var(--accent); }

        /* ===== Manual form ===== */
        .form-card {
            background: #fff;
            border: 1px solid var(--line);
            border-radius: 20px;
            box-shadow: 0 14px 40px rgba(64, 0, 0, 0.06);
            padding: 1.6rem 1.7rem;
        }
        .section-block {
            margin-bottom: 1.6rem;
        }
        .section-block:last-of-type {
            margin-bottom: 0;
        }
        .section-head {
            display: flex;
            align-items: center;
            gap: .7rem;
            margin-bottom: 1rem;
        }
        .section-head .num {
            width: 30px;
            height: 30px;
            flex-shrink: 0;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            border-radius: 10px;
            background: var(--accent);
            color: #fff;
            font-size: .82rem;
            font-weight: 700;
        }
        .section-head .title {
            font-weight: 700;
            font-size: .98rem;
            margin: 0;
        }
        .section-head .line {
            flex: 1;
            height: 1px;
            background: var(--line);
        }

        .f-label {
            font-weight: 600;
            font-size: .84rem;
            margin-bottom: .4rem;
            color: #4a3f3c;
            display: flex;
            align-items: center;
            gap: .4rem;
        }
        .f-label i { color: var(--accent); font-size: .8rem; }
        .f-control {
            border: 1px solid #e4dcd5;
            border-radius: 11px;
            padding: .6rem .8rem;
            font-size: .92rem;
            background: #fdfcfb;
            transition: border-color .15s ease, box-shadow .15s ease;
        }
        .f-control:focus {
            border-color: var(--accent);
            box-shadow: 0 0 0 3px rgba(107, 31, 42, .10);
            background: #fff;
        }
        .f-group { position: relative; }
        .f-group > i {
            position: absolute;
            left: .85rem;
            top: 50%;
            transform: translateY(-50%);
            color: #b8aba3;
            font-size: .95rem;
            pointer-events: none;
        }
        .f-group > i + .f-control,
        .f-group > i + select.f-control { padding-left: 2.4rem; }

        .variant-panel {
            background: var(--soft);
            border: 1px solid var(--line);
            border-radius: 14px;
            padding: 1.1rem;
        }

        .auto-field {
            background: #f6f2ee !important;
            border-style: dashed !important;
            color: var(--muted);
            font-family: ui-monospace, Consolas, monospace;
        }
        .auto-tag {
            position: absolute;
            top: -9px;
            right: 12px;
            background: var(--accent-soft);
            color: var(--accent);
            font-size: .64rem;
            font-weight: 700;
            letter-spacing: .06em;
            text-transform: uppercase;
            padding: .14rem .5rem;
            border-radius: 999px;
            z-index: 2;
        }
        .barcode-msg { display: block; margin-top: .32rem; font-size: .76rem; font-weight: 600; }
        .barcode-msg.ok { color: #16794c; }
        .barcode-msg.bad { color: #b3373f; }

        .price-prefix {
            position: absolute;
            left: .85rem;
            top: 50%;
            transform: translateY(-50%);
            font-weight: 700;
            color: var(--accent);
            font-size: .9rem;
            pointer-events: none;
            z-index: 2;
        }
        .price-group input { padding-left: 2.2rem; }

        .btn-save {
            background: linear-gradient(135deg, var(--accent) 0%, var(--accent-2) 100%);
            border: none;
            color: #fff;
            font-weight: 600;
            padding: .66rem 2rem;
            border-radius: 12px;
            box-shadow: 0 10px 22px rgba(107, 31, 42, .24);
            transition: transform .15s ease, box-shadow .15s ease;
        }
        .btn-save:hover {
            color: #fff;
            transform: translateY(-1px);
            box-shadow: 0 14px 28px rgba(107, 31, 42, .30);
        }
        .btn-reset {
            border: 1px solid var(--line);
            background: #fff;
            color: var(--text);
            font-weight: 600;
            padding: .66rem 1.5rem;
            border-radius: 12px;
        }
        .btn-reset:hover { background: var(--soft); }

        /* ===== Preview panel ===== */
        .preview-panel {
            position: sticky;
            top: 90px;
            background: #fff;
            border: 1px solid var(--line);
            border-radius: 20px;
            box-shadow: 0 14px 40px rgba(64, 0, 0, 0.06);
            padding: 1.4rem 1.5rem;
        }
        .pv-head {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-bottom: .3rem;
        }
        .pv-head .pv-title {
            font-weight: 700;
            font-size: 1rem;
            margin: 0;
            display: flex;
            align-items: center;
            gap: .5rem;
        }
        .pv-head .pv-title i { color: var(--accent); }
        .pv-badge {
            background: var(--accent-soft);
            color: var(--accent);
            font-size: .64rem;
            font-weight: 700;
            letter-spacing: .06em;
            text-transform: uppercase;
            border-radius: 999px;
            padding: .18rem .55rem;
        }
        .pv-hint {
            color: var(--ink-muted);
            font-size: .8rem;
            margin-bottom: .9rem;
            border-bottom: 1px solid var(--line);
            padding-bottom: .7rem;
        }
        .pv-section-title {
            font-size: .68rem;
            font-weight: 700;
            letter-spacing: .1em;
            text-transform: uppercase;
            color: #b0a49c;
            margin: .8rem 0 .2rem;
        }
        .pv-row {
            display: flex;
            justify-content: space-between;
            align-items: baseline;
            padding: .34rem 0;
            font-size: .88rem;
        }
        .pv-row span { color: var(--ink-muted); }
        .pv-row strong {
            text-align: right;
            word-break: break-all;
            font-family: ui-monospace, Consolas, monospace;
            color: var(--text);
        }
        .pv-row strong.local { color: var(--accent); font-family: inherit; font-weight: 700; }
    </style>

    <div class="page-form">

        @include('layouts.alerts')

        {{-- ===== Bulk import ===== --}}
        <div class="import-card mb-4">
            <div class="import-head">
                <span class="im-icon"><i class="bi bi-file-earmark-spreadsheet"></i></span>
                <div class="me-auto">
                    <h2>Bulk import from Excel / CSV</h2>
                    <p>Upload a spreadsheet to add many products at once. Grab a sample below, fill it in, then upload it back.</p>
                </div>
                <div class="d-flex flex-wrap gap-2">
                    <a href="{{ route('add_product_sample') }}" class="btn btn-sample">
                        <i class="bi bi-file-earmark-excel me-1"></i> Sample .xlsx
                    </a>
                    <a href="{{ route('add_product_sample_csv') }}" class="btn btn-sample">
                        <i class="bi bi-filetype-csv me-1"></i> Sample .csv
                    </a>
                </div>
            </div>
            <div class="import-body">
                <form action="{{ route('add_product_import') }}" method="POST" enctype="multipart/form-data" id="importForm">
                    @csrf
                    <input type="file" id="importFile" name="import_file" accept=".xlsx,.xls,.csv" class="d-none" required>
                    <label for="importFile" class="dropzone mb-3 d-block" id="dropzone">
                        <div class="dz-icon"><i class="bi bi-cloud-arrow-up"></i></div>
                        <div class="dz-title">Click to choose a file</div>
                        <div class="dz-sub" id="dzSub">Supported: .xlsx, .xls, .csv (max 10 MB)</div>
                    </label>
                    <div class="row g-3 align-items-end">
                        <div class="col-md-6">
                            <div class="f-label">Required columns</div>
                            <div class="chips">
                                <span class="chip">brand</span>
                                <span class="chip">product_name</span>
                                <span class="chip">product_type</span>
                                <span class="chip">color</span>
                                <span class="chip">size</span>
                                <span class="chip">stock</span>
                                <span class="chip">mrp</span>
                                <span class="chip">selling_price</span>
                            </div>
                        </div>
                        <div class="col-md-6 d-flex flex-wrap align-items-end justify-content-md-end gap-2">
                            <span class="form-hint" style="font-size:.78rem;color:var(--ink-muted);">
                                <i class="bi bi-info-circle me-1"></i>Product ID, SKU &amp; barcode auto-generate.
                            </span>
                            <button type="submit" class="btn btn-save"><i class="bi bi-upload me-1"></i> Import products</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>

        {{-- ===== Manual form : left form + right preview ===== --}}
        <div class="row g-4">
            <div class="col-lg-8">
                <div class="form-card">
                    <form id="productForm" action="{{ route('add_product') }}" method="POST">
                        @csrf

                        {{-- Basic details --}}
                        <div class="section-block">
                            <div class="section-head">
                                <span class="num">1</span><h5 class="title">Basic details</h5><span class="line"></span>
                            </div>
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <label class="f-label"><i class="bi bi-box-seam"></i> Product name</label>
                                    <input type="text" class="form-control f-control" placeholder="e.g. Chudi" name="product_name" value="{{ old('product_name') }}">
                                </div>
                                <div class="col-md-6">
                                    <label class="f-label"><i class="bi bi-stars"></i> Brand</label>
                                    <select id="brandInput" class="form-select f-control" name="brand">
                                        <option value="">Select brand</option>
                                        @foreach ($brands as $brand)
                                            <option value="{{ $brand->name }}" {{ old('brand') === $brand->name ? 'selected' : '' }}>{{ $brand->name }}</option>
                                        @endforeach
                                        <option value="__new" {{ old('brand') === '__new' ? 'selected' : '' }}>+ Add new brand</option>
                                    </select>
                                    <input id="newBrandInput" type="text" class="form-control f-control mt-2" placeholder="Enter new brand name" name="new_brand_name" value="{{ old('new_brand_name') }}" style="display: none;">
                                </div>
                            </div>
                        </div>

                        {{-- Variant --}}
                        <div class="section-block">
                            <div class="section-head">
                                <span class="num">2</span><h5 class="title">Variant</h5><span class="line"></span>
                            </div>
                            <div class="variant-panel">
                                <div class="row g-3">
                                    <div class="col-md-3">
                                        <label class="f-label"><i class="bi bi-tags"></i> Product type</label>
                                        <select class="form-select f-control" name="product_type">
                                            <option value="" {{ old('product_type') === '' ? 'selected' : '' }}>Choose type</option>
                                            @foreach (['1 Piece set (Top)', '2 Pcs set (kurthi &shawl)', '2 Pcs set (kurthi &Pant)', '3 Pcs Set', 'Anarkali', 'Anarkali with shawl', 'Short Kurthi ', 'Saree', 'Lehenga','Night Wear','Kids Wear','Lingerie','Bralette'] as $type)
                                                <option {{ old('product_type') === $type ? 'selected' : '' }}>{{ $type }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="col-md-3">
                                        <label class="f-label"><i class="bi bi-droplet"></i> Color</label>
                                        <input type="text" class="form-control f-control" placeholder="e.g. Red" name="color" value="{{ old('color') }}">
                                    </div>
                                    <div class="col-md-3">
                                        <label class="f-label"><i class="bi bi-rulers"></i> Size</label>
                                        <select class="form-select f-control" name="size">
                                            <option value="" {{ old('size') === '' ? 'selected' : '' }}>Select size</option>
                                            @foreach (['XS', 'S', 'M', 'L', 'XL', 'XXL'] as $sz)
                                                <option value="{{ $sz }}" {{ old('size') === $sz ? 'selected' : '' }}>{{ $sz }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="col-md-3">
                                        <label class="f-label"><i class="bi bi-box"></i> Stock</label>
                                        <input type="number" min="0" class="form-control f-control" placeholder="0" name="stock" value="{{ old('stock', 0) }}">
                                    </div>
                                </div>
                            </div>
                        </div>

                        {{-- Identifiers --}}
                        <div class="section-block">
                            <div class="section-head">
                                <span class="num">3</span><h5 class="title">Auto-generated identifiers</h5><span class="line"></span>
                            </div>
                            <div class="row g-3">
                                <div class="col-md-4">
                                    <label class="f-label"><i class="bi bi-hash"></i> Product ID</label>
                                    <div class="position-relative">
                                        <span class="auto-tag">auto</span>
                                        <input id="productIdInput" type="text" class="form-control f-control auto-field" placeholder="—" name="product_id" readonly>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <label class="f-label"><i class="bi bi-upc-scan"></i> SKU</label>
                                    <div class="position-relative">
                                        <span class="auto-tag">auto</span>
                                        <input id="skuInput" type="text" class="form-control f-control auto-field" placeholder="—" name="sku" readonly>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <label class="f-label"><i class="bi bi-barcode"></i> Barcode</label>
                                    <div class="position-relative">
                                        <span class="auto-tag">auto</span>
                                        <input id="barcodeInput" type="text" class="form-control f-control auto-field" placeholder="—" name="barcode" value="{{ old('barcode') }}">
                                        <small class="barcode-msg" id="barcodeMsg"></small>
                                    </div>
                                </div>
                            </div>
                        </div>

                        {{-- Pricing --}}
                        <div class="section-block">
                            <div class="section-head">
                                <span class="num">4</span><h5 class="title">Pricing</h5><span class="line"></span>
                            </div>
                            <div class="row g-3">
                                <div class="col-md-3">
                                    <label class="f-label"><i class="bi bi-tag"></i> Original price</label>
                                    <div class="f-group price-group position-relative">
                                        <span class="price-prefix">₹</span>
                                        <input id="originalPrice" type="number" step="0.01" class="form-control f-control" placeholder="0.00" name="original_price" value="{{ old('original_price') }}">
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <label class="f-label"><i class="bi bi-bookmark"></i> MRP</label>
                                    <div class="f-group price-group position-relative">
                                        <span class="price-prefix">₹</span>
                                        <input id="mrpPrice" type="number" step="0.01" class="form-control f-control" placeholder="0.00" name="mrp" value="{{ old('mrp') }}">
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <label class="f-label"><i class="bi bi-currency-rupee"></i> Selling price</label>
                                    <div class="f-group price-group position-relative">
                                        <span class="price-prefix">₹</span>
                                        <input id="sellingPrice" type="number" step="0.01" class="form-control f-control" placeholder="0.00" name="selling_price" value="{{ old('selling_price') }}">
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <label class="f-label"><i class="bi bi-percent"></i> Discount</label>
                                    <div class="f-group price-group position-relative">
                                        <span class="price-prefix">₹</span>
                                        <input id="discountAmount" type="text" class="form-control f-control" readonly placeholder="₹0.00" name="discount_amount">
                                        <span id="discountPercent" class="input-group-text" style="position:absolute; right:.6rem; top:50%; transform:translateY(-50%); background:transparent; border:none; font-size:.78rem; font-weight:700; color:var(--accent);">0%</span>
                                    </div>
                                </div>
                            </div>
                        </div>

                        {{-- Description --}}
                        <div class="section-block">
                            <div class="section-head">
                                <span class="num">5</span><h5 class="title">Description</h5><span class="line"></span>
                            </div>
                            <textarea class="form-control f-control" rows="3" placeholder="Example: Lightweight chiffon kurthi for women, floral print" name="description">{{ old('description') }}</textarea>
                        </div>

                        <div class="d-flex flex-wrap gap-2 mt-4 pt-3 border-top" style="border-color: var(--line)!important;">
                            <button type="submit" class="btn btn-save"><i class="bi bi-check-lg me-1"></i> Save product</button>
                            <a href="{{ route('add_product') }}" class="btn btn-reset"><i class="bi bi-arrow-counterclockwise me-1"></i> Reset</a>
                        </div>
                    </form>
                </div>
            </div>

            <div class="col-lg-4">
                <div class="preview-panel">
                    <div class="pv-head">
                        <h5 class="pv-title"><i class="bi bi-magic"></i> Live preview</h5>
                        <span class="pv-badge"><i class="bi bi-lightning-charge-fill me-1"></i>auto</span>
                    </div>
                    <div class="pv-hint">Generated IDs and pricing update as you type.</div>

                    <div class="pv-section-title">Product</div>
                    <div class="pv-row"><span>Name</span><strong id="pvName">—</strong></div>
                    <div class="pv-row"><span>Brand</span><strong id="pvBrand">—</strong></div>
                    <div class="pv-row"><span>Variant</span><strong id="pvVariant">—</strong></div>

                    <div class="pv-section-title">Identifiers</div>
                    <div class="pv-row"><span>Product ID</span><strong id="pvPid">—</strong></div>
                    <div class="pv-row"><span>SKU</span><strong id="pvSku">—</strong></div>
                    <div class="pv-row"><span>Barcode</span><strong id="pvBarcode">—</strong></div>

                    <div class="pv-section-title">Pricing &amp; stock</div>
                    <div class="pv-row"><span>Selling price</span><strong id="pvPrice" class="local">—</strong></div>
                    <div class="pv-row"><span>Discount</span><strong id="pvDiscount" class="local">—</strong></div>
                    <div class="pv-row"><span>Stock</span><strong id="pvStock" class="local">—</strong></div>
                </div>
            </div>
        </div>

    </div>

    @push('scripts')
        <script>
            // --- Import dropzone ---
            const importFile = document.getElementById('importFile');
            const dropzone = document.getElementById('dropzone');
            const dzSub = document.getElementById('dzSub');
            importFile.addEventListener('change', () => {
                if (importFile.files.length) {
                    dropzone.classList.add('has-file');
                    dzSub.textContent = 'Selected: ' + importFile.files[0].name;
                } else {
                    dropzone.classList.remove('has-file');
                    dzSub.textContent = 'Supported: .xlsx, .xls, .csv (max 10 MB)';
                }
            });

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
