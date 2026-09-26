@extends('layouts.app')

@section('page-title', 'Sell / POS Terminal')
@section('page-subtitle', 'Scan barcodes or tap products to bill customers in seconds.')

@section('content')
    <style>
        /* POS Terminal Styles */
        .pos-scan-box {
            background: linear-gradient(100deg, #0f1f36 0%, #16294a 100%);
            border: 1px solid #16294a;
            border-radius: var(--radius, 10px);
            padding: .75rem .9rem;
            margin-bottom: 1rem;
            box-shadow: 0 2px 8px rgba(16, 24, 40, .12);
            display: flex;
            align-items: center;
            gap: .7rem;
        }

        .pos-scan-box input[type="text"] {
            flex: 1;
            min-width: 0;
            border: 0;
            border-radius: 8px;
            padding: .6rem .9rem;
            font-family: ui-monospace, Consolas, monospace;
            font-size: .98rem;
            font-weight: 600;
        }

        .pos-scan-box .qty-input {
            width: 70px;
            border: 0;
            border-radius: 8px;
            padding: .6rem .5rem;
            text-align: center;
            font-weight: 700;
            font-size: .95rem;
            font-variant-numeric: tabular-nums;
        }

        .pos-scan-box button {
            border: 0;
            border-radius: 8px;
            background: var(--accent, #1554d1);
            color: #fff;
            font-weight: 700;
            font-size: .875rem;
            padding: .6rem 1.1rem;
            display: inline-flex;
            align-items: center;
            gap: .4rem;
            transition: background .15s ease;
        }

        .pos-scan-box button:hover { background: var(--accent-strong, #1043a8); }

        .pos-controls {
            display: flex;
            align-items: center;
            gap: .75rem;
            margin-bottom: 1rem;
            flex-wrap: wrap;
        }

        .pos-search-input {
            flex: 1;
            min-width: 220px;
            border-radius: 8px;
            border: 1px solid var(--border-strong, #c5cede);
            padding: .5rem .9rem .5rem 2.2rem;
            background: #ffffff url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='16' height='16' fill='%2366748b' viewBox='0 0 16 16'%3E%3Cpath d='M11.742 10.344a6.5 6.5 0 1 0-1.397 1.398h-.001c.03.04.062.078.098.115l3.85 3.85a1 1 0 0 0 1.415-1.414l-3.85-3.85a1.007 1.007 0 0 0-.115-.1zM12 6.5a5.5 5.5 0 1 1-11 0 5.5 5.5 0 0 1 11 0z'/%3E%3C/svg%3E") no-repeat .8rem center;
            font-size: .875rem;
            color: var(--text, #0e1726);
        }

        .pos-search-input:focus { outline: none; border-color: var(--accent, #1554d1); box-shadow: 0 0 0 3px rgba(21, 84, 209, .13); }

        .category-pills {
            display: flex;
            align-items: center;
            gap: .4rem;
            overflow-x: auto;
            padding-bottom: .25rem;
        }

        .category-pill {
            background: #ffffff;
            border: 1px solid var(--border-strong, #c5cede);
            color: var(--muted, #66748b);
            border-radius: 8px;
            padding: .4rem .8rem;
            font-size: .8rem;
            font-weight: 600;
            cursor: pointer;
            white-space: nowrap;
            transition: all .15s ease;
        }

        .category-pill:hover { border-color: var(--accent, #1554d1); color: var(--accent, #1554d1); }

        .category-pill.active {
            background: var(--accent, #1554d1);
            color: #ffffff;
            border-color: var(--accent, #1554d1);
        }

        .pos-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(185px, 1fr));
            gap: .8rem;
            max-height: 540px;
            overflow-y: auto;
            padding-right: .3rem;
        }

        .pos-card {
            background: #ffffff;
            border: 1px solid var(--border, #dfe4ee);
            border-radius: var(--radius, 10px);
            padding: .8rem;
            cursor: pointer;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            transition: all .15s ease;
            box-shadow: 0 1px 2px rgba(16, 24, 40, .05);
            user-select: none;
            min-height: 138px;
        }

        .pos-card:hover {
            border-color: var(--accent, #1554d1);
            box-shadow: 0 4px 12px rgba(21, 84, 209, .14);
        }

        .pos-card.out-of-stock {
            opacity: .6;
            cursor: not-allowed;
            background: var(--surface-2, #f7f9fc);
        }

        .pos-card .p-type {
            font-size: .63rem;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: .06em;
            color: var(--accent-strong, #1043a8);
            background: var(--accent-soft, #e7eefe);
            padding: .16rem .45rem;
            border-radius: 6px;
            display: inline-block;
            margin-bottom: .35rem;
        }

        .pos-card .p-name {
            font-weight: 700;
            font-size: .88rem;
            margin-bottom: .2rem;
            line-height: 1.25;
            color: var(--text, #0e1726);
        }

        .pos-card .p-price {
            font-weight: 800;
            font-size: 1.02rem;
            color: var(--accent, #1554d1);
            font-variant-numeric: tabular-nums;
        }

        /* Cart Panel */
        .cart-panel-card {
            border-radius: var(--radius, 10px);
            background: #ffffff;
            border: 1px solid var(--border, #dfe4ee);
            box-shadow: var(--shadow, 0 1px 2px rgba(16, 24, 40, .05));
            display: flex;
            flex-direction: column;
            overflow: hidden;
            height: 100%;
        }

        .cart-header {
            padding: .85rem 1.05rem;
            background: linear-gradient(100deg, #0f1f36 0%, #16294a 100%);
            color: #ffffff;
            display: flex;
            align-items: center;
            justify-content: space-between;
        }

        .cart-title {
            font-size: .92rem;
            font-weight: 700;
            display: flex;
            align-items: center;
            gap: .45rem;
        }

        .btn-clear-cart {
            background: rgba(255, 255, 255, .1);
            border: 1px solid rgba(255, 255, 255, .16);
            color: #e2e9f5;
            font-size: .75rem;
            font-weight: 600;
            cursor: pointer;
            border-radius: 6px;
            padding: .28rem .6rem;
        }

        .btn-clear-cart:hover { background: rgba(220, 38, 38, .85); border-color: rgba(220, 38, 38, .9); color: #fff; }

        .cart-items-wrap {
            flex: 1;
            overflow-y: auto;
            padding: .75rem 1rem;
            min-height: 250px;
            max-height: 380px;
        }

        .cart-item-row {
            display: flex;
            align-items: center;
            gap: .6rem;
            padding: .55rem 0;
            border-bottom: 1px dashed var(--border, #dfe4ee);
        }

        .ci-name {
            font-weight: 700;
            font-size: .85rem;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
            color: var(--text, #0e1726);
        }

        .ci-qty-ctrl {
            display: flex;
            align-items: center;
            gap: .2rem;
            background: var(--surface-3, #eaeff7);
            border-radius: 7px;
            padding: .15rem;
        }

        .ci-qty-btn {
            width: 24px;
            height: 24px;
            border: 0;
            background: #ffffff;
            border-radius: 5px;
            font-weight: 800;
            color: var(--accent, #1554d1);
            cursor: pointer;
            box-shadow: var(--shadow, 0 1px 2px rgba(16, 24, 40, .05));
        }

        .ci-qty-btn:hover { background: var(--accent, #1554d1); color: #fff; }

        .ci-qty-val {
            width: 28px;
            text-align: center;
            border: 0;
            background: transparent;
            font-weight: 700;
            font-size: .85rem;
            font-variant-numeric: tabular-nums;
        }

        .cart-checkout-footer {
            border-top: 1px solid var(--border, #dfe4ee);
            background: var(--surface-2, #f7f9fc);
            padding: .9rem 1.05rem;
        }

        .summary-line {
            display: flex;
            justify-content: space-between;
            font-size: .85rem;
            margin-bottom: .3rem;
            color: var(--muted, #66748b);
        }

        .summary-line b { color: var(--text, #0e1726); font-variant-numeric: tabular-nums; }

        .summary-line.grand-total {
            font-size: 1.15rem;
            font-weight: 800;
            color: var(--text, #0e1726);
            margin-top: .4rem;
            padding-top: .5rem;
            border-top: 2px solid var(--accent, #1554d1);
        }

        .summary-line.grand-total b { color: var(--accent, #1554d1); font-size: 1.25rem; }

        .btn-checkout-pay {
            width: 100%;
            border: 0;
            border-radius: 8px;
            background: var(--accent, #1554d1);
            color: #ffffff;
            font-weight: 700;
            font-size: .95rem;
            padding: .75rem;
            margin-top: .75rem;
            box-shadow: 0 3px 10px rgba(21, 84, 209, .28);
            transition: all .15s ease;
        }

        .btn-checkout-pay:hover:not(:disabled) { background: var(--accent-strong, #1043a8); }

        .btn-checkout-pay:disabled { background: #b9c4d6; box-shadow: none; cursor: not-allowed; }

        /* Small dupatta flag shown inside a cart line */
        .ci-flag {
            display: inline-block;
            font-size: .66rem;
            font-weight: 700;
            letter-spacing: .03em;
            padding: .1rem .4rem;
            border-radius: 5px;
            margin-top: .2rem;
        }
        .ci-flag.with { background: var(--accent-soft, #e7eefe); color: var(--accent-strong, #1043a8); border: 1px solid #cdddfb; }
        .ci-flag.without { background: var(--warn-soft, #fdf1e2); color: var(--warn, #d97706); border: 1px solid #f6ddba; }

        /* POS popup shell (dupatta choice + order confirmation) */
        .pos-modal { border: 0; border-radius: var(--radius, 10px); overflow: hidden; box-shadow: 0 24px 60px rgba(11, 22, 38, .28); }
        .pos-modal-head {
            background: #0f1f36;
            color: #fff;
            padding: .85rem 1.1rem;
            display: flex;
            align-items: center;
            justify-content: space-between;
        }
        .pos-modal-head h6 { font-weight: 700; font-size: .95rem; letter-spacing: .01em; }
        .pos-modal-body { padding: 1.25rem 1.25rem 1rem; background: #fff; }
        .pos-modal-foot {
            background: var(--surface-2, #f7f9fc);
            border-top: 1px solid var(--border, #dfe4ee);
            padding: .8rem 1.1rem;
            display: flex;
            justify-content: flex-end;
            gap: .5rem;
        }
        .pos-modal-foot .btn { border-radius: 8px; font-weight: 600; font-size: .88rem; padding: .5rem .95rem; }
        .pos-modal-pname { font-size: 1.08rem; font-weight: 800; color: var(--text, #0e1726); }

        /* Dupatta choice */
        .dup-choices { display: grid; grid-template-columns: 1fr 1fr; gap: .8rem; }
        .dup-choice {
            border: 1.5px solid var(--border, #dfe4ee);
            background: #fff;
            border-radius: var(--radius, 10px);
            padding: 1rem .6rem;
            cursor: pointer;
            transition: all .15s ease;
            font-family: inherit;
        }
        .dup-choice i { font-size: 1.35rem; }
        .dup-choice .dup-label { display: block; font-weight: 700; font-size: .88rem; color: var(--text, #0e1726); }
        .dup-choice .dup-price { display: block; font-weight: 800; font-size: 1.15rem; font-variant-numeric: tabular-nums; margin-top: .25rem; }
        .dup-choice .dup-note { display: block; font-size: .7rem; color: var(--muted, #66748b); margin-top: .15rem; }
        .dup-with { border-color: #cdddfb; background: var(--accent-soft, #e7eefe); }
        .dup-with i, .dup-with .dup-price { color: var(--accent, #1554d1); }
        .dup-with:hover { border-color: var(--accent, #1554d1); box-shadow: 0 4px 14px rgba(21, 84, 209, .18); transform: translateY(-1px); }
        .dup-without { border-color: #f6ddba; background: var(--warn-soft, #fdf1e2); }
        .dup-without i, .dup-without .dup-price { color: var(--warn, #d97706); }
        .dup-without:hover { border-color: var(--warn, #d97706); box-shadow: 0 4px 14px rgba(217, 119, 6, .18); transform: translateY(-1px); }

        /* Order confirmation */
        .confirm-table-wrap { border: 1px solid var(--border, #dfe4ee); border-radius: var(--radius, 10px); overflow: hidden; }
        .confirm-table { font-size: .87rem; margin-bottom: 0; }
        .confirm-table thead th {
            background: #0f1f36;
            color: #fff;
            font-size: .68rem;
            letter-spacing: .08em;
            text-transform: uppercase;
            font-weight: 700;
            border: 0;
            padding: .6rem .7rem;
        }
        .confirm-table td { border-color: var(--border, #dfe4ee); padding: .65rem .7rem; }
        .confirm-table tbody tr:last-child td { border-bottom: 1px solid var(--border, #dfe4ee); }
        .confirm-table tfoot td { border: 0; background: var(--surface-2, #f7f9fc); font-variant-numeric: tabular-nums; }
        .confirm-table .confirm-grand td { background: var(--accent-soft, #e7eefe); font-weight: 800; font-size: 1rem; color: var(--accent-strong, #1043a8); }
        .confirm-table .cf-name { font-weight: 600; }
        .confirm-meta {
            margin-top: .9rem;
            display: flex;
            flex-wrap: wrap;
            gap: .5rem .75rem;
            font-size: .78rem;
            color: var(--muted, #66748b);
        }
        .confirm-meta span b { color: var(--text, #0e1726); font-weight: 700; }
        .btn.confirm-ok {
            background: var(--ok, #0f9d58);
            border-color: var(--ok, #0f9d58);
            color: #fff;
        }
        .btn.confirm-ok:hover { background: #0b7d47; border-color: #0b7d47; color: #fff; }

        /* Small linear shop-counter (thermal) bill */
        .thermal-receipt {
            font-family: ui-monospace, Consolas, "Courier New", monospace;
            color: #191114;
            padding: .25rem .15rem 0;
        }
        .th-store { text-align: center; font-weight: 800; font-size: 1.08rem; letter-spacing: .06em; }
        .th-muted { text-align: center; font-size: .7rem; line-height: 1.45; color: #4c4448; }
        .th-dash { border-top: 1px dashed #6b6165; margin: .5rem 0; }
        .th-meta { width: 100%; font-size: .74rem; border-collapse: collapse; }
        .th-meta td { padding: .05rem 0; vertical-align: top; }
        .th-meta td:first-child { width: 82px; white-space: nowrap; }
        .th-item-name { font-weight: 700; font-size: .78rem; margin-top: .35rem; word-break: break-word; line-height: 1.3; }
        .th-row { display: flex; justify-content: space-between; gap: .75rem; font-size: .75rem; }
        .th-totals { margin-top: .2rem; }
        .th-subrow { display: flex; justify-content: space-between; font-size: .74rem; padding: .06rem 0; color: #3d3438; }
        .th-total-row { display: flex; justify-content: space-between; font-weight: 800; font-size: 1.02rem; margin-top: .3rem; border-top: 1px dashed #6b6165; padding-top: .35rem; }
        .th-thanks { text-align: center; font-weight: 800; font-size: .82rem; letter-spacing: .12em; }
        .th-tear { text-align: center; letter-spacing: .35em; color: #8a7f83; font-size: .7rem; }

        @media print {
            @page { size: 80mm auto; margin: 3mm; }
            body * { visibility: hidden; }
            #receiptModal, #receiptModal * { visibility: visible; }
            #receiptModal {
                position: absolute !important;
                left: 0 !important;
                top: 0 !important;
                width: 100% !important;
                margin: 0 !important;
                padding: 0 !important;
            }
            #receiptModal .modal-dialog { max-width: 74mm !important; margin: 0 !important; width: 100% !important; }
            #receiptModal .modal-content {
                border: 0 !important;
                box-shadow: none !important;
                border-radius: 0 !important;
                background: #ffffff !important;
            }
            .no-print { display: none !important; }
            .thermal-receipt { font-size: 11px; padding: 0; }
        }
    </style>

    @include('layouts.alerts')

    <div class="row g-4">
        <!-- Left Catalog & Scan -->
        <div class="col-lg-7 col-xl-8">
            <!-- Barcode Scan Box -->
            <div class="pos-scan-box">
                <i class="bi bi-upc-scan text-white fs-4"></i>
                <input type="text" id="barcodeInput" placeholder="Scan barcode or type last 4 digits..." autocomplete="off">
                <input type="number" id="scanQty" class="qty-input" value="1" min="1">
                <button id="scanAddBtn" type="button"><i class="bi bi-plus-circle-fill"></i> Add</button>
            </div>

            <!-- Filters -->
            <div class="pos-controls">
                <input type="text" id="catalogSearch" class="pos-search-input" placeholder="Search by product name, brand, SKU or barcode...">

                <div class="category-pills" id="categoryPills">
                    <button class="category-pill active" data-cat="ALL">All Products</button>
                    @php
                        $types = $products->pluck('product_type')->filter()->unique()->values();
                    @endphp
                    @foreach ($types as $t)
                        <button class="category-pill" data-cat="{{ strtolower($t) }}">{{ $t }}</button>
                    @endforeach
                </div>
            </div>

            <!-- Products Grid -->
            <div class="pos-grid" id="productGrid">
                @forelse ($products as $product)
                    @php
                        $isOut = $product->stock <= 0;
                        $isLow = $product->stock > 0 && $product->stock < 5;
                        $stockClass = $isOut ? 'text-danger fw-bold' : ($isLow ? 'text-warning fw-bold' : 'text-success');
                        $stockText = $isOut ? 'Out of stock' : ($isLow ? "Low: {$product->stock}" : "Stock: {$product->stock}");
                        $mrpVal = $product->mrp ?? ($product->selling_price + 200);
                    @endphp
                    <div class="pos-card {{ $isOut ? 'out-of-stock' : '' }}"
                         data-id="{{ $product->id }}"
                         data-name="{{ $product->product_name }}"
                         data-brand="{{ $product->brand }}"
                         data-type="{{ strtolower($product->product_type ?? '') }}"
                         data-sku="{{ $product->sku }}"
                         data-barcode="{{ $product->barcode }}"
                         data-stock="{{ $product->stock }}"
                         data-price="{{ $product->selling_price ?? 0 }}"
                         data-mrp="{{ $mrpVal }}"
                         data-dupatta="{{ $product->has_dupatta ? 1 : 0 }}"
                         data-dupatta-discount="{{ $product->dupatta_discount ?? 300 }}"
                         data-last4="{{ strtolower(substr($product->barcode, -4)) }}"
                         data-search="{{ strtolower($product->product_name . ' ' . $product->brand . ' ' . $product->sku . ' ' . $product->barcode . ' ' . $product->color . ' ' . $product->size . ' ' . $product->product_type) }}">

                        <div>
                            @if ($product->product_type)
                                <span class="p-type">{{ $product->product_type }}</span>
                            @endif
                            @if ($product->has_dupatta)
                                <span class="p-type" style="background:var(--accent-soft);color:var(--accent-strong);"><i class="bi bi-scissors me-1"></i>Dupatta</span>
                            @endif
                            <div class="p-name">{{ $product->product_name }}</div>
                            <div class="text-muted" style="font-size:.78rem;">
                                {{ $product->brand }}
                                @if ($product->size) &middot; {{ $product->size }} @endif
                            </div>
                        </div>

                        <div class="d-flex align-items-center justify-content-between mt-2 pt-2 border-top">
                            <div>
                                <span class="p-price">₹{{ number_format($product->selling_price ?? 0, 2) }}</span>
                                @if ($mrpVal > ($product->selling_price ?? 0))
                                    <span class="text-muted text-decoration-line-through ms-1" style="font-size:.75rem;">₹{{ number_format($mrpVal, 2) }}</span>
                                @endif
                            </div>
                            <span style="font-size:.78rem;" class="{{ $stockClass }}">{{ $stockText }}</span>
                        </div>
                    </div>
                @empty
                    <div class="text-center text-muted py-5" style="grid-column:1/-1;">
                        <i class="bi bi-box-seam fs-1 opacity-50 mb-2 d-block"></i>
                        No products available in catalog.
                    </div>
                @endforelse
                <div id="noMatchMsg" class="text-center text-muted py-5 d-none" style="grid-column: 1 / -1;">
                    <i class="bi bi-search fs-1 opacity-50 mb-2 d-block"></i>
                    No products matching search query.
                </div>
            </div>
        </div>

        <!-- Right Billing Cart -->
        <div class="col-lg-5 col-xl-4">
            <div class="cart-panel-card">
                <div class="cart-header">
                    <div>
                        <div class="cart-title"><i class="bi bi-cart3"></i> Billing Cart</div>
                        <div style="font-size:.68rem;font-weight:700;letter-spacing:.08em;color:#8fa6c9;text-transform:uppercase;">
                            Order ID&nbsp;: <span style="color:#ffffff;">{{ $nextOrderId }}</span>
                        </div>
                    </div>
                    <button type="button" class="btn-clear-cart" id="clearCartBtn"><i class="bi bi-trash"></i> Clear</button>
                </div>

                <div class="cart-items-wrap" id="cartItemsWrap">
                    <div class="text-center text-muted py-5" id="emptyCartState">
                        <i class="bi bi-cart-x fs-1 opacity-25 d-block mb-2"></i>
                        Scan barcode or click a product card to add items to cart.
                    </div>
                </div>

                <div class="cart-checkout-footer">
                    <div class="summary-line">
                        <span>Subtotal</span>
                        <b id="subtotalVal">₹0.00</b>
                    </div>
                    <div class="summary-line grand-total">
                        <span>Total Pay</span>
                        <b id="grandTotalVal">₹0.00</b>
                    </div>

                    <form method="POST" action="{{ route('pos_checkout') }}" id="checkoutForm" class="mt-3">
                        @csrf
                        <input type="hidden" name="cart" id="cartJsonInput">

                        <div class="row g-2 mb-2">
                            <div class="col-6">
                                <input type="text" name="customer_name" class="form-control form-control-sm" placeholder="Customer Name (opt)" style="border-radius:8px;">
                            </div>
                            <div class="col-6">
                                <input type="text" name="customer_mobile" class="form-control form-control-sm" placeholder="Mobile No (opt)" style="border-radius:8px;">
                            </div>
                        </div>

                        <div class="mb-2">
                            <select name="payment_mode" class="form-select form-select-sm fw-semibold" style="border-radius:8px;">
                                <option value="Online UPI" selected>Payment Mode: Online UPI</option>
                                <option value="Counter Cash">Payment Mode: Counter Cash</option>
                                <option value="Credit / Debit Card">Payment Mode: Credit / Debit Card</option>
                            </select>
                        </div>

                        <button type="submit" class="btn-checkout-pay" id="checkoutBtn" disabled>
                            <i class="bi bi-check-circle-fill me-1"></i> Complete Order
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Dupatta choice popup: shown when a product with a dupatta is selected -->
    <div class="modal fade" id="dupattaModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" style="max-width:420px;">
            <div class="modal-content pos-modal">
                <div class="pos-modal-head">
                    <h6 class="mb-0"><i class="bi bi-scissors me-2"></i> Dupatta option</h6>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="pos-modal-body text-center">
                    <div class="pos-modal-pname" id="dupProductName">Product name</div>
                    <div class="text-muted mb-3" style="font-size:.82rem;" id="dupProductMeta"></div>

                    <div class="dup-choices">
                        <button type="button" class="dup-choice dup-with" id="dupWithBtn">
                            <i class="bi bi-check-circle-fill d-block mb-1"></i>
                            <span class="dup-label">With Dupatta</span>
                            <span class="dup-price" id="dupWithPrice">&#8377;0.00</span>
                            <span class="dup-note">Original price</span>
                        </button>
                        <button type="button" class="dup-choice dup-without" id="dupWithoutBtn">
                            <i class="bi bi-scissors d-block mb-1"></i>
                            <span class="dup-label">Without Dupatta</span>
                            <span class="dup-price" id="dupWithoutPrice">&#8377;0.00</span>
                            <span class="dup-note" id="dupSaveNote">Save &#8377;300</span>
                        </button>
                    </div>
                </div>
                <div class="pos-modal-foot">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancel</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Order confirmation popup: review every item before the order is finally completed -->
    <div class="modal fade" id="confirmOrderModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg" style="max-width:640px;">
            <div class="modal-content pos-modal">
                <div class="pos-modal-head">
                    <h6 class="mb-0"><i class="bi bi-clipboard-check me-2"></i> Confirm your order</h6>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="pos-modal-body">
                    <p class="text-muted mb-3" style="font-size:.85rem;">
                        Please check all products below. Click <b>Confirm &amp; Complete</b> to finish this order.
                    </p>

                    <div class="table-responsive confirm-table-wrap">
                        <table class="table confirm-table align-middle mb-0">
                            <thead>
                                <tr>
                                    <th style="width:34px;">#</th>
                                    <th>Product</th>
                                    <th class="text-center" style="width:70px;">Qty</th>
                                    <th class="text-end" style="width:110px;">Rate</th>
                                    <th class="text-end" style="width:120px;">Amount</th>
                                </tr>
                            </thead>
                            <tbody id="confirmItemsBody"></tbody>
                            <tfoot>
                                <tr>
                                    <td colspan="4" class="text-end text-muted" style="font-size:.82rem;">Subtotal</td>
                                    <td class="text-end" id="confirmSubtotal">&#8377;0.00</td>
                                </tr>
                                <tr class="confirm-grand">
                                    <td colspan="4" class="text-end">Total Pay</td>
                                    <td class="text-end" id="confirmTotal">&#8377;0.00</td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>

                    <div class="confirm-meta" id="confirmMeta"></div>
                </div>
                <div class="pos-modal-foot">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal"><i class="bi bi-arrow-left me-1"></i> Back to cart</button>
                    <button type="button" class="btn confirm-ok" id="confirmOrderBtn"><i class="bi bi-check-circle-fill me-1"></i> Confirm &amp; Complete</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Official Offline Dress Purchase Bill Copy Modal for POS -->
    @if ($lastOrder)
        @php
            $company = config('invoice.company');
            $invoicePrefix = config('invoice.invoice.prefix', 'ZYRA/24-25/');
            $formattedInvoiceNo = $invoicePrefix . str_pad($lastOrder->id, 6, '0', STR_PAD_LEFT);
            $orderDate = $lastOrder->created_at ? $lastOrder->created_at->format('d/m/Y') : date('d/m/Y');

            $custName = $lastOrder->customer_name ?: 'Ms. Kavitha R';
            $custPhone = $lastOrder->customer_mobile ?: '98765 43210';
            $custAddress = '1st Floor, F 200, 1st St, Block F, Annanagar East, Chennai, Greater Chennai, Tamil Nadu 600102 Tamil Nadu, India';

            $totalMrp = 0;
            $totalDiscount = 0;
            foreach ($lastOrder->items as $item) {
                $mrp = $item->product?->mrp ?? ($item->price + 200);
                $lineMrp = $mrp * $item->qty;
                $totalMrp += $lineMrp;
                $totalDiscount += ($lineMrp - $item->total);
            }
            if ($totalMrp < $lastOrder->total) {
                $totalMrp = $lastOrder->total;
                $totalDiscount = 0;
            }

            if (!function_exists('posNumberToWordsIndian')) {
                function posNumberToWordsIndian($num) {
                    $ones = ['', 'One', 'Two', 'Three', 'Four', 'Five', 'Six', 'Seven', 'Eight', 'Nine', 'Ten',
                        'Eleven', 'Twelve', 'Thirteen', 'Fourteen', 'Fifteen', 'Sixteen', 'Seventeen', 'Eighteen', 'Nineteen'];
                    $tens = ['', '', 'Twenty', 'Thirty', 'Forty', 'Fifty', 'Sixty', 'Seventy', 'Eighty', 'Ninety'];

                    $num = round($num, 2);
                    $rupees = floor($num);
                    $paise = round(($num - $rupees) * 100);

                    if ($rupees == 0) return 'Zero Rupees';

                    $words = [];
                    if ($rupees >= 10000000) {
                        $crore = floor($rupees / 10000000);
                        $rupees %= 10000000;
                        $words[] = ($crore < 20 ? $ones[$crore] : $tens[floor($crore/10)] . ' ' . $ones[$crore%10]) . ' Crore';
                    }
                    if ($rupees >= 100000) {
                        $lakh = floor($rupees / 100000);
                        $rupees %= 100000;
                        $words[] = ($lakh < 20 ? $ones[$lakh] : $tens[floor($lakh/10)] . ' ' . $ones[$lakh%10]) . ' Lakh';
                    }
                    if ($rupees >= 1000) {
                        $thousand = floor($rupees / 1000);
                        $rupees %= 1000;
                        $words[] = ($thousand < 20 ? $ones[$thousand] : $tens[floor($thousand/10)] . ' ' . $ones[$thousand%10]) . ' Thousand';
                    }
                    if ($rupees >= 100) {
                        $hundred = floor($rupees / 100);
                        $rupees %= 100;
                        $words[] = $ones[$hundred] . ' Hundred';
                    }
                    if ($rupees > 0) {
                        $words[] = ($rupees < 20 ? $ones[$rupees] : $tens[floor($rupees/10)] . ' ' . $ones[$rupees%10]);
                    }

                    $res = implode(' ', array_filter($words)) . ' Rupees';
                    if ($paise > 0) {
                        $res .= ' and ' . ($paise < 20 ? $ones[$paise] : $tens[floor($paise/10)] . ' ' . $ones[$paise%10]) . ' Paise';
                    }
                    return $res;
                }
            }

            $amountWords = posNumberToWordsIndian($lastOrder->total);
        @endphp

        <div class="modal fade" id="receiptModal" tabindex="-1">
            <div class="modal-dialog modal-dialog-centered" style="max-width:340px;">
                <div class="modal-content shadow-lg border-0" style="border-radius:10px;">
                    <div class="modal-header border-0 pb-1 no-print">
                        <h6 class="modal-title fw-bold text-danger"><i class="bi bi-check-circle-fill me-1"></i> Order Completed</h6>
                        <div class="d-flex align-items-center gap-2">
                            <button type="button" class="btn btn-sm fw-bold text-white px-3" onclick="window.print()" style="background:#1554d1;border-color:#1554d1;">
                                <i class="bi bi-printer me-1"></i> Print Bill
                            </button>
                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                        </div>
                    </div>
                    <div class="modal-body pt-0 pb-3 px-3 bg-white" style="border-bottom-left-radius:14px;border-bottom-right-radius:14px;">
                        <div class="thermal-receipt">
                            <!-- Store Header -->
                            <div class="th-store">{{ $company['name'] }}</div>
                            <div class="th-muted">{{ $company['tagline'] }}</div>
                            <div class="th-muted">{{ $company['address_line1'] }}, {{ $company['address_line2'] }}</div>
                            <div class="th-muted">{{ $company['address_line3'] }}</div>
                            <div class="th-muted">Ph : {{ $company['phone'] }} | {{ $company['instagram'] }}</div>
                            <div class="th-muted">GSTIN : {{ $company['gstin'] }}</div>

                            <div class="th-dash"></div>

                            <!-- Bill Meta -->
                            <table class="th-meta">
                                <tr><td>Bill No.</td><td>: {{ $formattedInvoiceNo }}</td></tr>
                                <tr><td>Date</td><td>: {{ $lastOrder->created_at ? $lastOrder->created_at->format('d/m/Y h:i A') : $orderDate }}</td></tr>
                                <tr><td>Order ID</td><td>: {{ $lastOrder->order_id }}</td></tr>
                                <tr><td>Customer</td><td>: {{ $custName }}</td></tr>
                                <tr><td>Mobile</td><td>: {{ $custPhone }}</td></tr>
                                <tr><td>Payment</td><td>: {{ $lastOrder->payment_mode ?? 'Counter Cash' }}</td></tr>
                                <tr><td>Sales Exe</td><td>: {{ $company['sales_exec'] }}</td></tr>
                            </table>

                            <div class="th-dash"></div>

                            <!-- Items -->
                            <div class="th-row" style="font-weight:800;border-bottom:1px solid #191114;padding-bottom:.15rem;">
                                <span>ITEM</span>
                                <span style="white-space:nowrap;">QTY x RATE &nbsp;&nbsp; AMOUNT</span>
                            </div>
                            @forelse ($lastOrder->items as $item)
                                <div class="th-item-name">{{ $item->product_name }}</div>
                                @if ($item->dupatta)
                                    <div class="th-muted" style="text-align:left;">- {{ $item->dupatta === 'with' ? 'With Dupatta' : 'Without Dupatta' }}</div>
                                @endif
                                <div class="th-row">
                                    <span>{{ $item->qty }} x &#8377;{{ number_format($item->price, 2) }}</span>
                                    <span>&#8377;{{ number_format($item->total, 2) }}</span>
                                </div>
                            @empty
                                <div class="th-muted">No items found in this order.</div>
                            @endforelse

                            <div class="th-dash"></div>

                            <!-- Totals -->
                            <div class="th-totals">
                                <div class="th-subrow"><span>Total MRP</span><span>&#8377;{{ number_format($totalMrp, 2) }}</span></div>
                                <div class="th-subrow"><span>Discount</span><span>- &#8377;{{ number_format($totalDiscount, 2) }}</span></div>
                                <div class="th-total-row"><span>TOTAL</span><span>&#8377;{{ number_format($lastOrder->total, 2) }}</span></div>
                            </div>

                            <div class="th-muted" style="margin-top:.4rem;font-style:italic;">({{ $amountWords }} Only)</div>

                            <div class="th-dash"></div>

                            <!-- Footer -->
                            <div class="th-muted">{{ config('invoice.invoice.footer_note') }} Exchange within 7 days with original tags.</div>
                            <div class="th-thanks" style="margin-top:.45rem;">THANK YOU ! VISIT AGAIN</div>
                            <div class="th-muted">{{ $company['website'] }} | {{ $company['email'] }}</div>
                            <div class="th-tear" style="margin:.5rem 0 0;">x x x x x x x x x x x</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @endif
@endsection

@push('scripts')
    <script>
        // POS Cart Logic
        let cart = [];
        const cartItemsWrap = document.getElementById('cartItemsWrap');
        const subtotalVal = document.getElementById('subtotalVal');
        const grandTotalVal = document.getElementById('grandTotalVal');
        const checkoutBtn = document.getElementById('checkoutBtn');
        const cartJsonInput = document.getElementById('cartJsonInput');

        const DUPATTA_DISCOUNT = 300;

        function dupattaPrice(basePrice, dupatta, discount) {
            if (dupatta !== 'without') return basePrice;
            const cut = (discount === undefined || discount === null || isNaN(discount))
                ? DUPATTA_DISCOUNT : discount;
            return Math.max(basePrice - cut, 0);
        }

        function renderCart() {
            if (cart.length === 0) {
                cartItemsWrap.innerHTML = `
                    <div class="text-center text-muted py-5" id="emptyCartState">
                        <i class="bi bi-cart-x fs-1 opacity-25 d-block mb-2"></i>
                        Scan barcode or click a product card to add items to cart.
                    </div>`;
                subtotalVal.textContent = '₹0.00';
                grandTotalVal.textContent = '₹0.00';
                checkoutBtn.disabled = true;
                cartJsonInput.value = '';
                return;
            }

            let html = '';
            let total = 0;

            cart.forEach((item, index) => {
                const lineTotal = item.price * item.qty;
                total += lineTotal;

                let flag = '';
                if (item.dupatta === 'with') {
                    flag = '<span class="ci-flag with">With Dupatta</span>';
                } else if (item.dupatta === 'without') {
                    flag = '<span class="ci-flag without">Without Dupatta &middot; &minus;&#8377;' + (item.discount ?? DUPATTA_DISCOUNT) + '</span>';
                }

                html += `
                    <div class="cart-item-row">
                        <div class="flex-grow-1 min-w-0">
                            <div class="ci-name">${item.name}</div>
                            <div class="text-muted" style="font-size:.75rem;">₹${item.price.toFixed(2)} / unit</div>
                            ${flag}
                        </div>
                        <div class="ci-qty-ctrl">
                            <button type="button" class="ci-qty-btn" onclick="updateQty(${index}, -1)">-</button>
                            <input type="text" class="ci-qty-val" value="${item.qty}" readonly>
                            <button type="button" class="ci-qty-btn" onclick="updateQty(${index}, 1)">+</button>
                        </div>
                        <div class="fw-bold ms-2" style="min-width:60px; text-align:right; font-size:.88rem;">
                            ₹${lineTotal.toFixed(2)}
                        </div>
                        <button type="button" class="btn text-danger p-1 ms-1" onclick="removeItem(${index})"><i class="bi bi-x-circle"></i></button>
                    </div>`;
            });

            cartItemsWrap.innerHTML = html;
            subtotalVal.textContent = '₹' + total.toFixed(2);
            grandTotalVal.textContent = '₹' + total.toFixed(2);
            checkoutBtn.disabled = false;
            cartJsonInput.value = JSON.stringify(cart);
        }

        function addToCart(product, qty, dupatta) {
            qty = qty || 1;
            dupatta = dupatta || null;
            const key = product.id + '|' + (dupatta || '');
            const price = dupattaPrice(product.price, dupatta, product.discount);

            const existing = cart.find(i => i.key === key);
            if (existing) {
                if (existing.qty + qty > product.stock) {
                    alert('Cannot add more units than available stock (' + product.stock + ').');
                    return;
                }
                existing.qty += qty;
            } else {
                if (qty > product.stock) {
                    alert('Cannot add more units than available stock (' + product.stock + ').');
                    return;
                }
                cart.push({
                    key: key,
                    id: product.id,
                    name: product.name,
                    base_price: product.price,
                    price: price,
                    discount: product.discount === undefined ? DUPATTA_DISCOUNT : product.discount,
                    stock: product.stock,
                    qty: qty,
                    dupatta: dupatta
                });
            }
            renderCart();
        }

        // --- Dupatta choice popup -------------------------------------------------
        let pendingSelection = null;
        let dupattaModalInstance = null;
        const dupattaModalEl = document.getElementById('dupattaModal');

        function getDupattaModal() {
            if (!dupattaModalInstance) {
                dupattaModalInstance = new bootstrap.Modal(dupattaModalEl);
            }
            return dupattaModalInstance;
        }

        function requestAddToCart(product, qty) {
            qty = qty || 1;
            if (product.stock <= 0) {
                alert('Item is out of stock.');
                return;
            }
            if (!product.hasDupatta) {
                addToCart(product, qty, null);
                return;
            }

            pendingSelection = { product: product, qty: qty };
            document.getElementById('dupProductName').textContent = product.name;
            document.getElementById('dupProductMeta').textContent =
                (product.meta ? product.meta + ' · ' : '') + 'Qty ' + qty + ' · ' + product.stock + ' in stock';
            document.getElementById('dupWithPrice').textContent = '₹' + product.price.toFixed(2);
            const cut = product.discount === undefined ? DUPATTA_DISCOUNT : product.discount;
            document.getElementById('dupWithoutPrice').textContent =
                '₹' + dupattaPrice(product.price, 'without', cut).toFixed(2);
            document.getElementById('dupSaveNote').innerHTML = 'Save &#8377;' + cut;
            getDupattaModal().show();
        }

        function chooseDupatta(choice) {
            if (!pendingSelection) return;
            const { product, qty } = pendingSelection;
            pendingSelection = null;
            getDupattaModal().hide();
            addToCart(product, qty, choice);
        }

        document.getElementById('dupWithBtn').addEventListener('click', () => chooseDupatta('with'));
        document.getElementById('dupWithoutBtn').addEventListener('click', () => chooseDupatta('without'));
        dupattaModalEl.addEventListener('hidden.bs.modal', () => { pendingSelection = null; });

        // --- Order confirmation popup --------------------------------------------
        const checkoutForm = document.getElementById('checkoutForm');
        const confirmOrderModalEl = document.getElementById('confirmOrderModal');
        let confirmModalInstance = null;
        let orderConfirmed = false;

        function getConfirmModal() {
            if (!confirmModalInstance) {
                confirmModalInstance = new bootstrap.Modal(confirmOrderModalEl);
            }
            return confirmModalInstance;
        }

        function buildConfirmModal() {
            const body = document.getElementById('confirmItemsBody');
            let html = '';
            let total = 0;

            cart.forEach((item, i) => {
                const lineTotal = item.price * item.qty;
                total += lineTotal;

                let dup = '';
                if (item.dupatta === 'with') {
                    dup = '<div class="ci-flag with">With Dupatta</div>';
                } else if (item.dupatta === 'without') {
                    dup = '<div class="ci-flag without">Without Dupatta</div>';
                }

                html += `
                    <tr>
                        <td class="text-muted">${i + 1}</td>
                        <td><div class="cf-name">${item.name}</div>${dup}</td>
                        <td class="text-center">${item.qty}</td>
                        <td class="text-end">₹${item.price.toFixed(2)}</td>
                        <td class="text-end fw-bold">₹${lineTotal.toFixed(2)}</td>
                    </tr>`;
            });

            body.innerHTML = html;
            document.getElementById('confirmSubtotal').textContent = '₹' + total.toFixed(2);
            document.getElementById('confirmTotal').textContent = '₹' + total.toFixed(2);

            const custName = (checkoutForm.customer_name.value || '').trim();
            const custMobile = (checkoutForm.customer_mobile.value || '').trim();
            const payment = checkoutForm.payment_mode.value;
            const units = cart.reduce((s, i) => s + i.qty, 0);

            document.getElementById('confirmMeta').innerHTML = `
                <span><b>${units}</b> unit(s)</span>
                <span>Payment: <b>${payment}</b></span>
                ${custName ? `<span>Customer: <b>${custName}</b></span>` : ''}
                ${custMobile ? `<span>Mobile: <b>${custMobile}</b></span>` : ''}`;
        }

        checkoutForm.addEventListener('submit', (e) => {
            if (orderConfirmed) return;
            e.preventDefault();
            if (cart.length === 0) return;
            buildConfirmModal();
            getConfirmModal().show();
        });

        document.getElementById('confirmOrderBtn').addEventListener('click', () => {
            getConfirmModal().hide();
            orderConfirmed = true;
            checkoutForm.submit();
        });

        window.updateQty = function(index, delta) {
            if (!cart[index]) return;
            const item = cart[index];
            const newQty = item.qty + delta;
            if (newQty <= 0) {
                cart.splice(index, 1);
            } else if (newQty > item.stock) {
                alert('Stock limit reached (' + item.stock + ').');
            } else {
                item.qty = newQty;
            }
            renderCart();
        };

        window.removeItem = function(index) {
            cart.splice(index, 1);
            renderCart();
        };

        document.getElementById('clearCartBtn').addEventListener('click', () => {
            cart = [];
            renderCart();
        });

        // Click Product Card to Add to Cart
        function readCard(card) {
            return {
                id: parseInt(card.dataset.id),
                name: card.dataset.name,
                price: parseFloat(card.dataset.price),
                stock: parseInt(card.dataset.stock),
                hasDupatta: card.dataset.dupatta === '1',
                discount: parseFloat(card.dataset.dupattaDiscount || '300'),
                meta: [card.dataset.brand, card.dataset.type].filter(Boolean).join(' · ')
            };
        }

        document.querySelectorAll('.pos-card').forEach(card => {
            card.addEventListener('click', () => {
                if (parseInt(card.dataset.stock) <= 0) return;
                requestAddToCart(readCard(card), 1);
            });
        });

        // Barcode Scanner & Search Filters
        const barcodeInput = document.getElementById('barcodeInput');
        const scanQtyInput = document.getElementById('scanQty');

        function processBarcodeScan() {
            const code = barcodeInput.value.trim().toLowerCase();
            if (!code) return;
            const qty = parseInt(scanQtyInput.value) || 1;

            const cards = Array.from(document.querySelectorAll('.pos-card'));
            let match = cards.find(c => c.dataset.barcode.toLowerCase() === code);

            if (!match && code.length >= 4) {
                const last4 = code.slice(-4);
                match = cards.find(c => c.dataset.last4 === last4);
            }

            if (match) {
                const stock = parseInt(match.dataset.stock);
                if (stock <= 0) {
                    alert('Item is out of stock.');
                } else {
                    requestAddToCart(readCard(match), qty);
                    barcodeInput.value = '';
                    filterProducts();
                }
            } else {
                alert('Product not found for scanned barcode.');
            }
        }

        document.getElementById('scanAddBtn').addEventListener('click', processBarcodeScan);
        barcodeInput.addEventListener('keydown', e => {
            if (e.key === 'Enter') {
                e.preventDefault();
                processBarcodeScan();
            }
        });

        // Catalog Search & Category Filter
        const catalogSearch = document.getElementById('catalogSearch');
        const categoryPills = document.querySelectorAll('.category-pill');
        let selectedCategory = 'ALL';

        function filterProducts() {
            const q = catalogSearch.value.trim().toLowerCase();
            const bq = barcodeInput.value.trim().toLowerCase();
            const searchKeyword = q || bq;

            let visibleCount = 0;
            document.querySelectorAll('.pos-card').forEach(card => {
                const matchCat = selectedCategory === 'ALL' || card.dataset.type === selectedCategory;
                const matchSearch = !searchKeyword || card.dataset.search.includes(searchKeyword);
                const show = matchCat && matchSearch;
                card.style.display = show ? '' : 'none';
                if (show) visibleCount++;
            });

            const noMatchMsg = document.getElementById('noMatchMsg');
            if (noMatchMsg) {
                noMatchMsg.classList.toggle('d-none', visibleCount > 0);
            }
        }

        catalogSearch.addEventListener('input', filterProducts);
        barcodeInput.addEventListener('input', filterProducts);

        categoryPills.forEach(pill => {
            pill.addEventListener('click', () => {
                categoryPills.forEach(p => p.classList.remove('active'));
                pill.classList.add('active');
                selectedCategory = pill.dataset.cat;
                filterProducts();
            });
        });

        // Auto show receipt modal if order completed
    </script>
        @if ($lastOrder)
    <script>
            const receiptModalEl = document.getElementById('receiptModal');
            if (receiptModalEl) {
                const receiptModal = new bootstrap.Modal(receiptModalEl);
                receiptModal.show();
            }
    </script>
        @endif
@endpush
