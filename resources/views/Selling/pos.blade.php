@extends('layouts.app')

@section('page-title', 'Sell / POS Terminal')
@section('page-subtitle', 'Scan barcodes or tap products to bill customers in seconds.')

@section('content')
    <style>
        /* POS Styles */
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

        @media print {
            body * { visibility: hidden; }
            #receiptModal, #receiptModal * { visibility: visible; }
            #receiptModal { position: absolute; left: 0; top: 0; width: 100%; }
            .modal-header, .modal-footer, .btn-close { display: none !important; }
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
                         data-last4="{{ strtolower(substr($product->barcode, -4)) }}"
                         data-search="{{ strtolower($product->product_name . ' ' . $product->brand . ' ' . $product->sku . ' ' . $product->barcode) }}">

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
                            <span class="p-price">₹{{ number_format($product->selling_price ?? 0, 2) }}</span>
                            <span style="font-size:.78rem;" class="{{ $stockClass }}">{{ $stockText }}</span>
                        </div>
                    </div>
                @empty
                    <div class="text-center text-muted py-5">
                        <i class="bi bi-box-seam fs-1 opacity-50 mb-2 d-block"></i>
                        No products available in catalog.
                    </div>
                @endforelse
            </div>
        </div>

        <!-- Right Billing Cart -->
        <div class="col-lg-5 col-xl-4">
            <div class="cart-panel-card">
                <div class="cart-header">
                    <div class="cart-title"><i class="bi bi-cart3"></i> Billing Cart</div>
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

                        <button type="submit" class="btn-checkout-pay" id="checkoutBtn" disabled>
                            <i class="bi bi-check-circle-fill me-1"></i> Complete Order
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- POS Thermal Receipt Print Modal -->
    @if ($lastOrder)
        <div class="modal fade" id="receiptModal" tabindex="-1">
            <div class="modal-dialog modal-dialog-centered modal-sm">
                <div class="modal-content" style="border-radius:18px;">
                    <div class="modal-header border-0 pb-0">
                        <h6 class="modal-title fw-bold"><i class="bi bi-receipt me-1"></i> Sale Complete</h6>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body text-center" id="receiptContent">
                        <div class="fw-bold fs-5 mb-1">Zyra Store</div>
                        <div class="text-muted" style="font-size:.75rem;">Receipt #{{ $lastOrder->order_id }}</div>
                        <div class="text-muted mb-3" style="font-size:.72rem;">{{ $lastOrder->created_at->format('d M Y, h:i A') }}</div>
                        <hr class="my-2" style="border-style:dashed;">
                        @foreach ($lastOrder->items as $i)
                            <div class="d-flex justify-content-between text-start" style="font-size:.82rem;">
                                <div>{{ $i->product_name }} (x{{ $i->qty }})</div>
                                <div class="fw-bold">₹{{ number_format($i->total, 2) }}</div>
                            </div>
                        @endforeach
                        <hr class="my-2" style="border-style:dashed;">
                        <div class="d-flex justify-content-between fw-bold fs-6">
                            <span>TOTAL:</span>
                            <span class="text-danger">₹{{ number_format($lastOrder->total, 2) }}</span>
                        </div>
                    </div>
                    <div class="modal-footer border-0">
                        <button type="button" class="btn btn-outline-secondary btn-sm w-100" onclick="window.print()"><i class="bi bi-printer me-1"></i> Print Receipt</button>
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
            document.querySelectorAll('.pos-card').forEach(card => {
                const matchCat = selectedCategory === 'ALL' || card.dataset.type === selectedCategory;
                const matchSearch = !q || card.dataset.search.includes(q);
                card.style.display = (matchCat && matchSearch) ? '' : 'none';
            });
        }

        catalogSearch.addEventListener('input', filterProducts);
        categoryPills.forEach(pill => {
            pill.addEventListener('click', () => {
                categoryPills.forEach(p => p.classList.remove('active'));
                pill.classList.add('active');
                selectedCategory = pill.dataset.cat;
                filterProducts();
            });
        });

        // Auto show receipt modal if order completed
        @if ($lastOrder)
            const receiptModalEl = document.getElementById('receiptModal');
            if (receiptModalEl) {
                const receiptModal = new bootstrap.Modal(receiptModalEl);
                receiptModal.show();
            }
        @endif
    </script>
@endpush
