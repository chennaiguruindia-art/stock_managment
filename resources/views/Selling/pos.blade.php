@extends('layouts.app')

@section('page-title', 'Sell / POS Terminal')
@section('page-subtitle', 'Scan barcodes or tap products to bill customers in seconds.')

@section('content')
    <style>
        /* POS Terminal Styles */
        .pos-scan-box {
            background: linear-gradient(135deg, #2a000a 0%, #4f111d 100%);
            border-radius: 16px;
            padding: .9rem 1.1rem;
            margin-bottom: 1rem;
            box-shadow: 0 8px 24px rgba(42, 0, 10, 0.18);
            display: flex;
            align-items: center;
            gap: .75rem;
        }

        .pos-scan-box input[type="text"] {
            flex: 1;
            min-width: 0;
            border: 0;
            border-radius: 10px;
            padding: .65rem 1rem;
            font-family: ui-monospace, Consolas, monospace;
            font-size: 1rem;
            font-weight: 600;
        }

        .pos-scan-box .qty-input {
            width: 75px;
            border: 0;
            border-radius: 10px;
            padding: .65rem .5rem;
            text-align: center;
            font-weight: 700;
            font-size: 1rem;
        }

        .pos-scan-box button {
            border: 0;
            border-radius: 10px;
            background: #ffffff;
            color: #6b1f2a;
            font-weight: 700;
            padding: .65rem 1.2rem;
            display: inline-flex;
            align-items: center;
            gap: .4rem;
            transition: background .15s ease;
        }

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
            border-radius: 999px;
            border: 1px solid #e8dfd8;
            padding: .55rem 1rem .55rem 2.4rem;
            background: #ffffff url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='16' height='16' fill='%2372686c' viewBox='0 0 16 16'%3E%3Cpath d='M11.742 10.344a6.5 6.5 0 1 0-1.397 1.398h-.001c.03.04.062.078.098.115l3.85 3.85a1 1 0 0 0 1.415-1.414l-3.85-3.85a1.007 1.007 0 0 0-.115-.1zM12 6.5a5.5 5.5 0 1 1-11 0 5.5 5.5 0 0 1 11 0z'/%3E%3C/svg%3E") no-repeat .9rem center;
            font-size: .9rem;
        }

        .category-pills {
            display: flex;
            align-items: center;
            gap: .4rem;
            overflow-x: auto;
            padding-bottom: .25rem;
        }

        .category-pill {
            background: #ffffff;
            border: 1px solid #e8dfd8;
            color: #2b1f1f;
            border-radius: 999px;
            padding: .4rem .9rem;
            font-size: .82rem;
            font-weight: 600;
            cursor: pointer;
            white-space: nowrap;
            transition: all .15s ease;
        }

        .category-pill.active {
            background: #6b1f2a;
            color: #ffffff;
            border-color: #6b1f2a;
        }

        .pos-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(190px, 1fr));
            gap: .9rem;
            max-height: 520px;
            overflow-y: auto;
            padding-right: .3rem;
        }

        .pos-card {
            background: #ffffff;
            border: 1px solid #e8dfd8;
            border-radius: 16px;
            padding: .9rem;
            cursor: pointer;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            transition: all .18s ease;
            box-shadow: 0 4px 12px rgba(0,0,0,0.02);
            user-select: none;
            min-height: 140px;
        }

        .pos-card:hover {
            border-color: #6b1f2a;
            transform: translateY(-2px);
            box-shadow: 0 8px 20px rgba(107, 31, 42, 0.12);
        }

        .pos-card.out-of-stock {
            opacity: .55;
            cursor: not-allowed;
            background: #fcf8f8;
        }

        .pos-card .p-type {
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

        .pos-card .p-name {
            font-weight: 700;
            font-size: .9rem;
            margin-bottom: .2rem;
            line-height: 1.25;
        }

        .pos-card .p-price {
            font-weight: 800;
            font-size: 1.05rem;
            color: #6b1f2a;
        }

        /* Cart Panel */
        .cart-panel-card {
            border-radius: 20px;
            background: #ffffff;
            border: 1px solid #e8dfd8;
            box-shadow: 0 14px 34px rgba(36, 25, 35, .06);
            display: flex;
            flex-direction: column;
            overflow: hidden;
            height: 100%;
        }

        .cart-header {
            padding: 1rem 1.2rem;
            background: linear-gradient(135deg, #2a000a 0%, #4f111d 100%);
            color: #ffffff;
            display: flex;
            align-items: center;
            justify-content: space-between;
        }

        .cart-title {
            font-size: .98rem;
            font-weight: 700;
            display: flex;
            align-items: center;
            gap: .45rem;
        }

        .btn-clear-cart {
            background: none;
            border: 0;
            color: #f3c2c7;
            font-size: .8rem;
            font-weight: 600;
            cursor: pointer;
        }

        .cart-items-wrap {
            flex: 1;
            overflow-y: auto;
            padding: .9rem 1.1rem;
            min-height: 250px;
            max-height: 380px;
        }

        .cart-item-row {
            display: flex;
            align-items: center;
            gap: .65rem;
            padding: .6rem 0;
            border-bottom: 1px dashed #efe9ea;
        }

        .ci-name {
            font-weight: 700;
            font-size: .88rem;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }

        .ci-qty-ctrl {
            display: flex;
            align-items: center;
            gap: .2rem;
            background: #f6f3f4;
            border-radius: 8px;
            padding: .15rem;
        }

        .ci-qty-btn {
            width: 24px;
            height: 24px;
            border: 0;
            background: #ffffff;
            border-radius: 6px;
            font-weight: 800;
            color: #6b1f2a;
            cursor: pointer;
        }

        .ci-qty-val {
            width: 28px;
            text-align: center;
            border: 0;
            background: transparent;
            font-weight: 700;
            font-size: .85rem;
        }

        .cart-checkout-footer {
            border-top: 1px solid #e8dfd8;
            background: #faf8f9;
            padding: 1rem 1.2rem;
        }

        .summary-line {
            display: flex;
            justify-content: space-between;
            font-size: .88rem;
            margin-bottom: .3rem;
            color: #7a6f6b;
        }

        .summary-line.grand-total {
            font-size: 1.25rem;
            font-weight: 800;
            color: #6b1f2a;
            margin-top: .4rem;
            padding-top: .4rem;
            border-top: 1px dashed #e8dfd8;
        }

        .btn-checkout-pay {
            width: 100%;
            border: 0;
            border-radius: 12px;
            background: linear-gradient(135deg, #6b1f2a 0%, #8c2e3d 100%);
            color: #ffffff;
            font-weight: 800;
            font-size: 1rem;
            padding: .8rem;
            margin-top: .75rem;
            box-shadow: 0 6px 18px rgba(107, 31, 42, 0.25);
            transition: all .18s ease;
        }

        .btn-checkout-pay:hover:not(:disabled) {
            transform: translateY(-2px);
            background: linear-gradient(135deg, #541720 0%, #762432 100%);
        }

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
                         data-last4="{{ strtolower(substr($product->barcode, -4)) }}"
                         data-search="{{ strtolower($product->product_name . ' ' . $product->brand . ' ' . $product->sku . ' ' . $product->barcode . ' ' . $product->color . ' ' . $product->size . ' ' . $product->product_type) }}">

                        <div>
                            @if ($product->product_type)
                                <span class="p-type">{{ $product->product_type }}</span>
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
                        <div style="font-size:.72rem;font-weight:800;letter-spacing:.05em;color:#8a7f83;text-transform:uppercase;">
                            Order ID&nbsp;: <span style="color:#ffff;">{{ $nextOrderId }}</span>
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

    <!-- Official Offline Dress Purchase Bill Copy Modal for POS -->
    @if ($lastOrder)
        @php
            $company = config('invoice.company');
            $invoicePrefix = config('invoice.invoice.prefix', 'ZYRA/24-25/');
            $formattedInvoiceNo = $invoicePrefix . str_pad($lastOrder->id, 6, '0', STR_PAD_LEFT);
            $orderDate = $lastOrder->created_at ? $lastOrder->created_at->format('d/m/Y') : date('d/m/Y');

            $custName = $lastOrder->customer_name ?: 'Ms. Kavitha R';
            $custPhone = $lastOrder->customer_mobile ?: '98765 43210';
            $custAddress = 'No. 12, 3rd Cross Street, Anna Nagar, Chennai - 600040, Tamil Nadu, India';

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
                <div class="modal-content shadow-lg border-0" style="border-radius:14px;">
                    <div class="modal-header border-0 pb-1 no-print">
                        <h6 class="modal-title fw-bold text-danger"><i class="bi bi-check-circle-fill me-1"></i> Order Completed</h6>
                        <div class="d-flex align-items-center gap-2">
                            <button type="button" class="btn btn-sm fw-bold text-white px-3" onclick="window.print()" style="background:#6b1f2a;border-color:#6b1f2a;">
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

                html += `
                    <div class="cart-item-row">
                        <div class="flex-grow-1 min-w-0">
                            <div class="ci-name">${item.name}</div>
                            <div class="text-muted" style="font-size:.75rem;">₹${item.price.toFixed(2)} / unit</div>
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

        function addToCart(product) {
            const existing = cart.find(i => i.id === product.id);
            if (existing) {
                if (existing.qty + product.qty > product.stock) {
                    alert('Cannot add more units than available stock (' + product.stock + ').');
                    return;
                }
                existing.qty += product.qty;
            } else {
                if (product.qty > product.stock) {
                    alert('Cannot add more units than available stock (' + product.stock + ').');
                    return;
                }
                cart.push({
                    id: product.id,
                    name: product.name,
                    price: product.price,
                    stock: product.stock,
                    qty: product.qty
                });
            }
            renderCart();
        }

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
        document.querySelectorAll('.pos-card').forEach(card => {
            card.addEventListener('click', () => {
                const stock = parseInt(card.dataset.stock);
                if (stock <= 0) return;
                addToCart({
                    id: parseInt(card.dataset.id),
                    name: card.dataset.name,
                    price: parseFloat(card.dataset.price),
                    stock: stock,
                    qty: 1
                });
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
                    addToCart({
                        id: parseInt(match.dataset.id),
                        name: match.dataset.name,
                        price: parseFloat(match.dataset.price),
                        stock: stock,
                        qty: qty
                    });
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
