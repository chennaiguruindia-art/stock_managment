@extends('layouts.app')

@section('page-title', 'Invoice detail')
@section('page-subtitle', 'Company, customer and product details combined in one printable invoice.')

@section('content')
    <style>
        .invoice-sheet {
            background: #ffffff;
            border: 1px solid #e8dfd8;
            border-radius: 18px;
            box-shadow: 0 14px 34px rgba(36, 25, 35, .06);
            padding: 2rem 2.25rem;
            max-width: 860px;
            margin: 0 auto;
        }

        .inv-head {
            display: flex;
            justify-content: space-between;
            gap: 1.5rem;
            flex-wrap: wrap;
            border-bottom: 2px solid #6b1f2a;
            padding-bottom: 1.1rem;
        }

        .inv-company-name {
            font-size: 1.45rem;
            font-weight: 800;
            color: #400000;
            margin: 0;
            letter-spacing: -.01em;
        }

        .inv-company-meta {
            font-size: .82rem;
            color: #7a6f6b;
            line-height: 1.55;
            margin-top: .3rem;
        }

        .inv-doc-badge {
            text-align: right;
        }

        .inv-doc-title {
            font-size: 1.6rem;
            font-weight: 800;
            letter-spacing: .28em;
            color: #6b1f2a;
            text-transform: uppercase;
        }

        .inv-doc-chip {
            font-family: ui-monospace, "Cascadia Code", Consolas, monospace;
            font-weight: 700;
            color: #6b1f2a;
            background: #f3e2e3;
            border-radius: 8px;
            padding: .25rem .6rem;
            display: inline-block;
            font-size: .95rem;
        }

        .inv-section-label {
            font-size: .72rem;
            letter-spacing: .14em;
            text-transform: uppercase;
            color: #9c8f92;
            font-weight: 700;
            margin-bottom: .35rem;
        }

        .inv-billto {
            display: flex;
            justify-content: space-between;
            gap: 1.5rem;
            flex-wrap: wrap;
            padding: 1.15rem 0;
        }

        .inv-cust-input {
            border: 0;
            border-bottom: 1px dashed #cfc5c9;
            background: transparent;
            padding: .3rem .15rem;
            font-weight: 600;
            width: 100%;
            max-width: 300px;
            font-size: .95rem;
        }

        .inv-cust-input:focus { outline: none; border-bottom-color: #6b1f2a; }

        .inv-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: .4rem;
            font-size: .9rem;
        }

        .inv-table th {
            background: linear-gradient(120deg, #2a000a, #6b1f2a);
            color: #fff;
            padding: .65rem .75rem;
            text-align: left;
            font-size: .74rem;
            letter-spacing: .08em;
            text-transform: uppercase;
        }

        .inv-table td {
            padding: .8rem .75rem;
            border-bottom: 1px solid #efe9ea;
            vertical-align: middle;
        }

        .inv-totals {
            margin-left: auto;
            width: 320px;
            max-width: 100%;
            margin-top: 1.1rem;
            font-size: .92rem;
        }

        .inv-total-row {
            display: flex;
            justify-content: space-between;
            padding: .32rem .1rem;
            color: #7a6f6b;
        }

        .inv-grand {
            border-top: 2px solid #6b1f2a;
            margin-top: .4rem;
            padding-top: .55rem !important;
            font-size: 1.22rem;
            font-weight: 800;
            color: #400000;
        }

        .inv-words {
            font-size: .85rem;
            font-style: italic;
            color: #7a6f6b;
            margin-top: .8rem;
        }

        .inv-foot {
            display: flex;
            justify-content: space-between;
            align-items: flex-end;
            gap: 1.5rem;
            flex-wrap: wrap;
            border-top: 1px dashed #d8cfd3;
            margin-top: 1.6rem;
            padding-top: 1rem;
            font-size: .8rem;
            color: #7a6f6b;
        }

        .inv-sign {
            text-align: center;
            min-width: 200px;
        }

        .inv-sign .line {
            border-top: 1px solid #b9adb1;
            margin-top: 3.2rem;
            padding-top: .3rem;
            font-weight: 700;
            color: #400000;
        }

        .qty-stepper {
            display: inline-flex;
            align-items: center;
            border: 1px solid #e5e2dc;
            border-radius: 10px;
            overflow: hidden;
            background: #fbfaf8;
        }
        .qty-stepper button {
            border: 0;
            background: transparent;
            width: 30px;
            height: 32px;
            font-size: 1rem;
            font-weight: 700;
            color: #6b1f2a;
            cursor: pointer;
        }
        .qty-stepper button:hover { background: #f3e2e3; }
        .qty-stepper input {
            width: 56px;
            border: 0;
            text-align: center;
            font-weight: 700;
            font-size: .9rem;
            height: 32px;
            background: #fff;
        }
        .qty-stepper input:focus { outline: none; }

        .mrp-strike { text-decoration: line-through; color: #b3a7ab; font-size: .8rem; }

        .print-show { display: none; }

        @media print {
            body * { visibility: hidden; }
            .invoice-sheet, .invoice-sheet * { visibility: visible; }
            .invoice-sheet {
                position: absolute;
                left: 0;
                top: 0;
                width: 100%;
                border: 0;
                box-shadow: none;
                border-radius: 0;
                padding: .5rem;
            }
            .no-print { display: none !important; }
            .print-show { display: block !important; }
            .inv-cust-input { border-bottom-color: transparent; }
        }
    </style>

    @include('layouts.alerts')

    <div class="d-flex justify-content-between align-items-center mb-3 no-print">
        <a href="{{ route('invoice') }}" class="text-decoration-none fw-semibold" style="color:#6b1f2a;">
            <i class="bi bi-arrow-left me-1"></i> Back to products
        </a>
        <div class="d-flex gap-2">
            @if (isset($savedOrder))
                <a href="{{ route('view_order_invoice', $savedOrder->order_id) }}" class="btn btn-danger" style="background:#c25b6c;border-color:#c25b6c;font-weight:700;">
                    <i class="bi bi-receipt-cutoff me-1"></i> View Official Invoice
                </a>
            @endif
            <button type="button" class="btn btn-outline-accent" onclick="window.print()"><i class="bi bi-printer me-1"></i> Print Quick Invoice</button>
        </div>
    </div>

        <form method="POST" action="{{ route('invoice_store') }}" id="invoiceForm"
            data-unit-price="{{ (float) ($product->selling_price ?? 0) }}"
            data-max-stock="{{ max((int) $product->stock, 1) }}">
        @csrf
        <input type="hidden" name="product_id" value="{{ $product->id }}">

        <!-- ==================== INVOICE SHEET ==================== -->
        <div class="invoice-sheet">
            <!-- Company header -->
            <div class="inv-head">
                <div class="d-flex align-items-center gap-3">
                    <img src="{{ asset('logo/zyralogo.png') }}" alt="logo" style="max-width:180px;max-height:65px;object-fit:contain;">
                    <div>
                        <h2 class="inv-company-name">{{ config('invoice.company.name') }}</h2>
                        <div class="inv-company-meta">
                            {{ config('invoice.company.tagline') }}<br>
                            {{ config('invoice.company.address_line1') }}, {{ config('invoice.company.address_line2') }}, {{ config('invoice.company.address_line3') }}<br>
                            <i class="bi bi-telephone me-1"></i>{{ config('invoice.company.phone') }}
                            &nbsp; <i class="bi bi-envelope me-1"></i>{{ config('invoice.company.email') }}
                            @if (config('invoice.company.gstin'))
                                <br><span class="fw-semibold">GSTIN:</span> {{ config('invoice.company.gstin') }}
                            @endif
                        </div>
                    </div>
                </div>
                <div class="inv-doc-badge">
                    <div class="inv-doc-title">Invoice</div>
                    <div class="mt-2">
                        <span class="inv-section-label">Invoice No.</span><br>
                        <span class="inv-doc-chip">{{ $savedOrder->order_id ?? $nextInvoiceNo }}</span>
                    </div>
                    <div class="mt-2" style="font-size:.85rem;color:#7a6f6b;">
                        <span class="inv-section-label">Date</span><br>
                        {{ ($savedOrder?->created_at ?? now())->format('d M Y, h:i A') }}
                    </div>
                </div>
            </div>

            <!-- Customer details -->
            <div class="inv-billto">
                <div style="min-width:260px;">
                    <div class="inv-section-label">Bill To</div>
                    <input type="text" name="customer_name" id="customerName" class="inv-cust-input"
                           value="{{ old('customer_name', $savedOrder->customer_name ?? '') }}"
                           placeholder="Customer name" maxlength="100" autocomplete="off">
                    <div class="mt-2">
                        <input type="text" name="customer_mobile" id="customerMobile" class="inv-cust-input"
                               value="{{ old('customer_mobile', $savedOrder->customer_mobile ?? '') }}"
                               placeholder="Mobile number" maxlength="20" autocomplete="off">
                    </div>
                </div>
                <div style="font-size:.85rem;color:#7a6f6b;">
                    <div class="inv-section-label">Product status</div>
                    Available stock: <b class="{{ $product->stock > 0 ? 'text-success' : 'text-danger' }}">{{ $product->stock }} unit(s)</b><br>
                    Payment mode: <b>Cash / Card at counter</b><br>
                    Status: <b>{{ isset($savedOrder) ? 'Paid' : 'Pending' }}</b>
                </div>
            </div>

            <!-- Product details -->
            <table class="inv-table">
                <thead>
                    <tr>
                        <th style="width:42%;">Product</th>
                        <th style="width:16%;">Quantity</th>
                        <th style="width:16%;">Rate</th>
                        <th style="width:16%;text-align:right;">Amount</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td>
                            <div class="fw-bold">{{ $product->product_name }}</div>
                            <div class="text-muted" style="font-size:.78rem;">
                                {{ $product->brand }}
                                @if ($product->product_type) &middot; {{ $product->product_type }} @endif
                                @if ($product->color) &middot; Color: {{ $product->color }} @endif
                                @if ($product->size) &middot; Size: {{ $product->size }} @endif
                            </div>
                            <div class="text-muted" style="font-size:.76rem;font-family:ui-monospace,Consolas,monospace;">
                                SKU: {{ $product->sku }} &middot; Barcode: {{ $product->barcode }}
                            </div>
                            @if (($product->mrp ?? 0) > ($product->selling_price ?? 0))
                                <div class="mt-1" style="font-size:.78rem;">
                                    MRP: <span class="mrp-strike">&#8377;{{ number_format($product->mrp, 2) }}</span>
                                    <span class="badge bg-success rounded-pill ms-1">
                                        Save &#8377;{{ number_format(($product->mrp - $product->selling_price) + ($product->discount_amount ?? 0), 2) }}
                                    </span>
                                </div>
                            @endif
                        </td>
                        <td>
                            <div class="qty-stepper no-print">
                                <button type="button" data-step="-1">&minus;</button>
                                <input type="number" name="quantity" id="quantityInput" value="{{ $savedOrder?->items->sum('qty') ?? 1 }}" min="1" max="{{ max($product->stock, 1) }}">
                                <button type="button" data-step="1">+</button>
                            </div>
                            <span class="print-show fw-bold">{{ $savedOrder?->items->sum('qty') ?? 1 }}</span>
                        </td>
                        <td>
                            &#8377;{{ number_format($product->selling_price ?? 0, 2) }}
                            @if (($product->mrp ?? 0) > ($product->selling_price ?? 0))<br><span class="mrp-strike">&#8377;{{ number_format($product->mrp, 2) }}</span>@endif
                        </td>
                        <td class="text-end fw-bold" id="lineAmountCell">&#8377;{{ number_format(($savedOrder?->total ?? ($product->selling_price ?? 0)), 2) }}</td>
                    </tr>
                </tbody>
            </table>

            <!-- Totals -->
            <div class="inv-totals">
                <div class="inv-total-row"><span>Items</span><b id="qtySummary">{{ $savedOrder?->items->sum('qty') ?? 1 }}</b></div>
                <div class="inv-total-row"><span>Subtotal</span><b id="subtotalVal">&#8377;{{ number_format($savedOrder?->total ?? ($product->selling_price ?? 0), 2) }}</b></div>
                <div class="inv-total-row inv-grand"><span>Total Payable</span><span id="grandTotalVal">&#8377;{{ number_format($savedOrder?->total ?? ($product->selling_price ?? 0), 2) }}</span></div>
            </div>

            <div class="inv-words">
                <i class="bi bi-info-circle me-1"></i> Amount in words: <span id="amountWords">...</span>
            </div>

            <!-- Footer -->
            <div class="inv-foot">
                <div style="max-width:420px;">
                    <div class="inv-section-label">Terms &amp; conditions</div>
                    {{ config('invoice.invoice.footer_note') }}
                </div>
                <div class="inv-sign">
                    <div class="line">For {{ config('invoice.company.name') }}<br><span style="font-weight:500;font-size:.72rem;color:#9c8f92;">Authorised Signatory</span></div>
                </div>
            </div>
        </div>

        <!-- Actions -->
        <div class="text-center mt-4 no-print">
            @if ($product->stock <= 0)
                <div class="alert alert-warning d-inline-block px-4 py-2 mb-0" style="border-radius:12px;">
                    <i class="bi bi-exclamation-triangle me-1"></i> This product is out of stock — restock before invoicing.
                </div>
            @else
                <button type="submit" class="btn btn-lg px-5 text-white" style="background:linear-gradient(135deg,#6b1f2a,#8c2e3d);border:0;border-radius:12px;font-weight:800;box-shadow:0 6px 18px rgba(107,31,42,.25);">
                    <i class="bi bi-save me-2"></i>Save Invoice
                </button>
                <div class="text-muted mt-2" style="font-size:.82rem;">
                    Saving deducts stock and records the sale in Sales history &amp; Returns.
                </div>
            @endif
        </div>
    </form>
@endsection

@push('scripts')
    <script>
        const invoiceForm = document.getElementById('invoiceForm');
        const UNIT_PRICE = Number(invoiceForm.dataset.unitPrice);
        const MAX_STOCK = Number(invoiceForm.dataset.maxStock);
        const qtyInput = document.getElementById('quantityInput');

        // Quantity stepper
        document.querySelectorAll('.qty-stepper button[data-step]').forEach(btn => {
            btn.addEventListener('click', () => {
                let val = (parseInt(qtyInput.value) || 1) + parseInt(btn.dataset.step);
                val = Math.min(Math.max(val, 1), MAX_STOCK);
                qtyInput.value = val;
                updateTotals();
            });
        });

        qtyInput.addEventListener('input', () => {
            let val = parseInt(qtyInput.value);
            if (!isNaN(val)) {
                if (val > MAX_STOCK) { val = MAX_STOCK; qtyInput.value = val; }
                if (val < 1) { val = 1; qtyInput.value = val; }
            }
            updateTotals();
        });

        function moneyFmt(n) {
            return '\u20B9' + n.toLocaleString('en-IN', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
        }

        function updateTotals() {
            const qty = Math.max(parseInt(qtyInput.value) || 1, 1);
            const total = UNIT_PRICE * qty;
            document.getElementById('lineAmountCell').textContent = moneyFmt(total);
            document.getElementById('qtySummary').textContent = qty;
            document.getElementById('subtotalVal').textContent = moneyFmt(total);
            document.getElementById('grandTotalVal').textContent = moneyFmt(total);
            document.getElementById('amountWords').textContent = rupeesInWords(total) + ' only';
        }

        // Indian numbering system amount-in-words
        function rupeesInWords(num) {
            const ones = ['', 'One', 'Two', 'Three', 'Four', 'Five', 'Six', 'Seven', 'Eight', 'Nine', 'Ten',
                'Eleven', 'Twelve', 'Thirteen', 'Fourteen', 'Fifteen', 'Sixteen', 'Seventeen', 'Eighteen', 'Nineteen'];
            const tens = ['', '', 'Twenty', 'Thirty', 'Forty', 'Fifty', 'Sixty', 'Seventy', 'Eighty', 'Ninety'];

            function twoDigits(n) {
                return n < 20 ? ones[n] : tens[Math.floor(n / 10)] + (n % 10 ? ' ' + ones[n % 10] : '');
            }
            function threeDigits(n) {
                const h = Math.floor(n / 100), r = n % 100;
                return (h ? ones[h] + ' Hundred' + (r ? ' ' : '') : '') + (r ? twoDigits(r) : '');
            }

            num = Math.round(num * 100) / 100;
            const rupees = Math.floor(num);
            const paise = Math.round((num - rupees) * 100);
            let result = '';

            if (rupees === 0) result = 'Zero Rupees';
            else {
                const crore = Math.floor(rupees / 10000000);
                const lakh = Math.floor((rupees % 10000000) / 100000);
                const thousand = Math.floor((rupees % 100000) / 1000);
                const hundred = rupees % 1000;
                result = '';
                if (crore) result += threeDigits(crore) + ' Crore ';
                if (lakh) result += twoDigits(lakh) + ' Lakh ';
                if (thousand) result += twoDigits(thousand) + ' Thousand ';
                if (hundred) result += threeDigits(hundred);
                result += ' Rupees';
            }
            if (paise > 0) result += ' and ' + twoDigits(paise) + ' Paise';
            return result.trim();
        }

        updateTotals();
    </script>
@endpush
