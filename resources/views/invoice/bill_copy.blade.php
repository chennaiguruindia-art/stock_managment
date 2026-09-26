@extends('layouts.app')

@section('page-title', 'Invoice - ' . ($order->order_id ?? ''))
@section('page-subtitle', 'Official Invoice with customer details, item breakdown, and price summary.')

@section('content')
    <style>
        .bill-paper {
            background: #ffffff;
            border: 1px solid #dfe4ee;
            border-radius: 10px;
            box-shadow: 0 1px 3px rgba(16, 24, 40, .07);
            padding: 2rem 2.25rem;
            max-width: 900px;
            margin: 0 auto;
            color: #0e1726;
            font-family: 'Segoe UI', system-ui, -apple-system, sans-serif;
            position: relative;
        }

        .bill-top-header {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            gap: 1.5rem;
            margin-bottom: 1.5rem;
        }

        .zyra-logo-box {
            text-align: center;
        }

        .zyra-lotus {
            width: 52px;
            height: 38px;
            display: inline-block;
            color: #1554d1;
        }

        .zyra-title {
            font-size: 2.3rem;
            font-weight: 800;
            letter-spacing: 0.15em;
            color: #1554d1;
            line-height: 1;
            margin: 0.2rem 0 0.1rem;
            font-family: 'Georgia', serif;
        }

        .zyra-title sup {
            font-size: 0.9rem;
            font-weight: normal;
        }

        .zyra-tagline-sub {
            font-size: 0.78rem;
            letter-spacing: 0.42em;
            text-transform: uppercase;
            color: #475569;
            font-weight: 600;
            margin-bottom: 0.3rem;
        }

        .zyra-script {
            font-family: 'Brush Script MT', 'Pacifico', cursive, sans-serif;
            color: #4d86e8;
            font-size: 1.25rem;
            margin: 0.2rem 0;
        }

        .zyra-divider {
            color: #1554d1;
            font-size: 0.75rem;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 0.5rem;
        }
        .zyra-divider::before, .zyra-divider::after {
            content: "";
            display: inline-block;
            width: 40px;
            height: 1px;
            background: #1554d1;
        }

        .bill-badge-wrapper {
            text-align: right;
        }

        .bill-badge-title {
            background: #1554d1;
            color: #ffffff;
            font-size: 1.35rem;
            font-weight: 800;
            letter-spacing: 0.12em;
            padding: 0.4rem 2rem;
            border-radius: 6px;
            display: inline-block;
            text-transform: uppercase;
            box-shadow: 0 2px 8px rgba(21, 84, 209, .28);
            margin-bottom: 0.8rem;
        }

        .bill-meta-table {
            font-size: 0.88rem;
            margin-left: auto;
            color: #334155;
        }

        .bill-meta-table td {
            padding: 0.15rem 0.4rem;
        }

        .bill-meta-table td.lbl {
            font-weight: 700;
            color: #0e1726;
            white-space: nowrap;
        }

        /* Customer Boxes */
        .bill-cust-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 1.2rem;
            margin-bottom: 1.5rem;
        }

        .cust-card {
            border: 1px solid #dfe4ee;
            border-radius: 10px;
            padding: 1rem 1.2rem;
            background: #f7f9fc;
            position: relative;
        }

        .cust-card-title {
            color: #1554d1;
            font-weight: 800;
            font-size: 0.82rem;
            letter-spacing: 0.12em;
            text-transform: uppercase;
            margin-bottom: 0.6rem;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }
        .cust-card-title::after {
            content: "";
            flex: 1;
            height: 1px;
            background: #dfe4ee;
        }

        .cust-name {
            font-weight: 700;
            font-size: 1rem;
            color: #0e1726;
            margin-bottom: 0.2rem;
        }

        .cust-address {
            font-size: 0.85rem;
            color: #475569;
            line-height: 1.45;
        }

        .cust-phone {
            margin-top: 0.4rem;
            font-size: 0.88rem;
            font-weight: 700;
            color: #0e1726;
        }

        /* Items Table */
        .bill-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 1.5rem;
            font-size: 0.88rem;
        }

        .bill-table th {
            background: #1554d1;
            color: #ffffff;
            font-weight: 700;
            font-size: 0.82rem;
            letter-spacing: 0.06em;
            padding: 0.65rem 0.75rem;
            text-transform: uppercase;
            border: 1px solid #1043a8;
        }

        .bill-table td {
            padding: 0.75rem 0.75rem;
            border: 1px solid #dfe4ee;
            vertical-align: top;
        }

        .p-name-title {
            font-weight: 700;
            color: #0e1726;
            font-size: 0.92rem;
        }

        .p-sub-detail {
            font-size: 0.78rem;
            color: #1554d1;
            font-weight: 600;
            margin-top: 0.15rem;
        }

        .p-fabric-detail {
            font-size: 0.78rem;
            color: #64748b;
        }

        /* Summary Grid */
        .bill-summary-grid {
            display: grid;
            grid-template-columns: 1.1fr 0.9fr;
            gap: 1.2rem;
            margin-bottom: 1.5rem;
        }

        .summary-card {
            border: 1px solid #dfe4ee;
            border-radius: 10px;
            padding: 1rem 1.2rem;
            background: #ffffff;
        }

        .summary-card-title {
            color: #1554d1;
            font-weight: 800;
            font-size: 0.85rem;
            letter-spacing: 0.08em;
            text-transform: uppercase;
            margin-bottom: 0.8rem;
        }

        .price-row {
            display: flex;
            justify-content: space-between;
            font-size: 0.88rem;
            padding: 0.25rem 0;
            color: #475569;
        }

        .grand-total-row {
            background: #e7eefe;
            color: #1554d1;
            font-weight: 800;
            font-size: 1.05rem;
            padding: 0.5rem 0.8rem;
            border-radius: 6px;
            margin: 0.5rem 0;
            display: flex;
            justify-content: space-between;
        }

        .words-box {
            font-size: 0.82rem;
            margin-top: 0.6rem;
            color: #475569;
        }
        .words-box b {
            font-weight: 700;
        }

        .thankyou-box {
            text-align: center;
        }

        .thankyou-title {
            color: #1554d1;
            font-weight: 800;
            font-size: 0.88rem;
            letter-spacing: 0.05em;
            margin-bottom: 0.8rem;
        }

        .social-link {
            font-size: 0.83rem;
            color: #475569;
            margin-bottom: 0.35rem;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        .qr-wrapper {
            margin-top: 0.8rem;
            text-align: center;
        }
        .qr-img {
            width: 90px;
            height: 90px;
            border: 1px solid #dfe4ee;
            padding: 4px;
            border-radius: 6px;
        }
        .qr-caption {
            font-size: 0.75rem;
            font-weight: 800;
            color: #1554d1;
            letter-spacing: 0.08em;
            margin-top: 0.2rem;
        }

        /* Terms & Footer */
        .terms-card {
            border: 1px solid #dfe4ee;
            border-radius: 10px;
            padding: 0.9rem 1.2rem;
            background: #f7f9fc;
            margin-bottom: 1.2rem;
        }

        .terms-header {
            text-align: center;
            color: #1554d1;
            font-weight: 800;
            font-size: 0.8rem;
            letter-spacing: 0.12em;
            margin-bottom: 0.6rem;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 0.5rem;
        }
        .terms-header::before, .terms-header::after {
            content: "â—†";
            font-size: 0.6rem;
            color: #1554d1;
        }

        .terms-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 0.4rem 1.5rem;
            font-size: 0.78rem;
            color: #475569;
        }
        .terms-grid ul {
            margin: 0;
            padding-left: 1.2rem;
        }
        .terms-grid li {
            margin-bottom: 0.2rem;
        }

        .footer-script {
            text-align: center;
            margin-top: 0.8rem;
        }
        .footer-script .t-script {
            font-family: 'Brush Script MT', 'Pacifico', cursive, sans-serif;
            font-size: 1.5rem;
            color: #1554d1;
        }
        .footer-script .t-sub {
            font-size: 0.75rem;
            font-weight: 800;
            letter-spacing: 0.18em;
            color: #475569;
            text-transform: uppercase;
        }

        /* Declaring an explicit @page rule is what stops Chrome printing its own
           date / URL / "1 of 2" headers and footers - with no @page at all it
           always adds them (verified in headless Chrome: 41500 bytes / 5 streams
           without @page vs 17072 / 3 with). Zeroing the margin additionally gives
           the bill the whole sheet instead of the default ~1cm borders. */
        @page {
            size: A4;
            margin: 0;
        }

        @media print {
            html, body {
                background: #fff !important;
                width: auto !important;
                height: auto !important;
            }

            /* display:none, not visibility:hidden: invisible elements still
               reserve layout height and were what pushed this onto page 2. */
            .sidebar, .topbar, .mobile-menu-btn, .no-print { display: none !important; }
            .app { display: block !important; min-height: 0 !important; }
            .main {
                margin-left: 0 !important;
                min-height: 0 !important;
                display: block !important;
            }
            .content {
                padding: 0 !important;
                width: 100% !important;
                min-width: 0 !important;
            }

            .bill-paper {
                position: static !important;
                box-sizing: border-box;
                margin: 0 !important;
                width: 210mm !important;
                max-width: 210mm !important;
                padding: 7mm 10mm !important;
                border: 0 !important;
                border-radius: 0 !important;
                box-shadow: none !important;
            }

            /* --- compact for paper ---------------------------------
               On screen the bill is 1327px tall; an A4 sheet only has
               1123px once @page margins are zeroed. These rules claw
               back ~250px so the whole invoice lands on one page.
               Screen styles are untouched. */
            .bill-top-header { margin-bottom: 8px !important; gap: 0.8rem !important; }
            .zyra-lotus { width: 38px !important; height: 28px !important; }
            .zyra-title { font-size: 1.95rem !important; }
            .zyra-tagline-sub { font-size: 0.66rem !important; margin-bottom: 0.1rem !important; }
            .zyra-script { font-size: 1rem !important; }
            .bill-badge-title {
                font-size: 1.05rem !important;
                padding: 0.28rem 1.3rem !important;
                margin-bottom: 0.4rem !important;
            }
            .bill-meta-table { font-size: 0.76rem !important; }
            .bill-meta-table td { padding: 0.06rem 0.35rem !important; }

            .bill-cust-grid { gap: 0.6rem !important; margin-bottom: 8px !important; }
            .cust-card { padding: 0.7rem 0.9rem !important; border-radius: 6px !important; }
            .cust-card-title { font-size: 0.7rem !important; margin-bottom: 0.25rem !important; }
            .cust-name { font-size: 0.88rem !important; }
            .cust-address { font-size: 0.75rem !important; line-height: 1.3 !important; }
            .cust-phone { font-size: 0.78rem !important; margin-top: 0.15rem !important; }

            .bill-table { margin-bottom: 8px !important; font-size: 0.78rem !important; }
            .bill-table th { font-size: 0.72rem !important; padding: 0.42rem 0.55rem !important; }
            .bill-table td { padding: 0.55rem 0.6rem !important; }
            .p-name-title { font-size: 0.82rem !important; }
            .p-sub-detail, .p-fabric-detail { font-size: 0.7rem !important; }

            .bill-summary-grid { gap: 0.6rem !important; margin-bottom: 8px !important; }
            .summary-card { padding: 0.7rem 0.9rem !important; border-radius: 6px !important; }
            .summary-card-title { font-size: 0.72rem !important; margin-bottom: 0.4rem !important; }
            .price-row { font-size: 0.8rem !important; padding: 0.16rem 0 !important; }
            .grand-total-row {
                font-size: 0.92rem !important;
                padding: 0.3rem 0.6rem !important;
                margin: 0.3rem 0 !important;
            }
            .words-box { font-size: 0.74rem !important; margin-top: 0.3rem !important; }
            .thankyou-title { font-size: 0.78rem !important; margin-bottom: 0.4rem !important; }
            .social-link { font-size: 0.74rem !important; margin-bottom: 0.2rem !important; }
            .qr-wrapper { margin-top: 0.35rem !important; }
            .qr-img { width: 74px !important; height: 74px !important; }
            .qr-caption { font-size: 0.66rem !important; }

            .terms-card { padding: 0.7rem 1rem !important; margin-bottom: 6px !important; }
            .terms-header { font-size: 0.7rem !important; margin-bottom: 0.3rem !important; }
            .terms-grid { font-size: 0.7rem !important; }

            .footer-script { margin-top: 4px !important; }
            .footer-script .t-script { font-size: 1.1rem !important; }
            .footer-script .t-sub { font-size: 0.62rem !important; }
        }
    </style>

    <div class="d-flex justify-content-between align-items-center mb-3 no-print" style="max-width:900px;margin:0 auto 1rem;">
        <a href="{{ route('invoice') }}" class="btn btn-outline-secondary btn-sm fw-semibold">
            <i class="bi bi-arrow-left me-1"></i> Back to Invoices
        </a>
        <button type="button" class="btn btn-danger btn-sm fw-bold px-3" onclick="window.print()" style="background:#1554d1;border-color:#1554d1;">
            <i class="bi bi-printer me-1"></i> Print Invoice
        </button>
    </div>

    @php
        $company = config('invoice.company');
        $invoicePrefix = config('invoice.invoice.prefix', 'ZYRA/24-25/');
        $formattedInvoiceNo = $invoicePrefix . str_pad($order->id, 6, '0', STR_PAD_LEFT);
        $orderDate = $order->created_at ? $order->created_at->format('d/m/Y') : date('d/m/Y');

        // Customer details fetched from DB!
        $custName = $order->customer_name ?: 'â€”';
        $custPhone = $order->customer_mobile ?: 'â€”';
        $instagramUrl = 'https://www.instagram.com/' . ltrim($company['instagram'] ?? '@zyraofficial46', '@') . '/';

        // Calculate MRP and discount totals
        $totalMrp = 0;
        $totalDiscount = 0;
        foreach ($order->items as $item) {
            $mrp = $item->product?->mrp ?? ($item->price + 200);
            $lineMrp = $mrp * $item->qty;
            $totalMrp += $lineMrp;
            $totalDiscount += ($lineMrp - $item->total);
        }
        if ($totalMrp < $order->total) {
            $totalMrp = $order->total;
            $totalDiscount = 0;
        }

        // Amount in words function
        if (!function_exists('numberToWordsIndian')) {
            function numberToWordsIndian($num) {
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

        $amountWords = numberToWordsIndian($order->total);
    @endphp

    <div class="bill-paper">
        <!-- Top Header -->
        <div class="bill-top-header">
            <!-- Brand Logo -->
            <div class="zyra-logo-box">
                <img src="{{ asset('logo/zyralogo.png') }}" alt="ZYRA Lifestyle Logo" style="max-width:240px;max-height:110px;object-fit:contain;display:block;margin:0 auto 0.4rem;">
            </div>

            <!-- Invoice Badge & Meta -->
            <div class="bill-badge-wrapper">
                <div class="bill-badge-title">Invoice</div>
                <table class="bill-meta-table">
                    <tr>
                        <td class="lbl">Bill No.</td>
                        <td>: <b>{{ $formattedInvoiceNo }}</b></td>
                    </tr>
                    <tr>
                        <td class="lbl">Bill Date</td>
                        <td>: {{ $orderDate }}</td>
                    </tr>
                    <tr>
                        <td class="lbl">Order No.</td>
                        <td>: <b>{{ $order->order_id }}</b></td>
                    </tr>
                    <tr>
                        <td class="lbl">Payment Mode</td>
                        <td>: Credit Card / Counter Cash</td>
                    </tr>
                    <tr>
                        <td class="lbl">Sales Executive</td>
                        <td>: {{ $company['sales_exec'] ?? 'Anitha Rajesh' }}</td>
                    </tr>
                    <tr>
                        <td class="lbl">Store</td>
                        <td>: {{ $company['store_location'] ?? 'ZYRA Lifestyle â€“ Chennai' }}</td>
                    </tr>
                </table>
            </div>
        </div>

        <!-- Customer Bill To / Ship To -->
        <div class="bill-cust-grid">
            <div class="cust-card">
                <div class="cust-card-title">BILL TO</div>
                <div class="cust-name">Zyra Lifestyle</div>
                <div class="cust-address">1st Floor, F 200, 1st St, Block F, Annanagar East, Chennai, Greater Chennai, Tamil Nadu 600102</div>
                <div class="cust-phone">Phone : 9884125555</div>
            </div>
            <div class="cust-card">
                <div class="cust-card-title">SHIP TO</div>
                <div class="cust-name">{{ $custName }}</div>
                <div class="cust-phone">Phone : {{ $custPhone }}</div>
            </div>
        </div>

        <!-- Products Table -->
        <table class="bill-table">
            <thead>
                <tr>
                    <th style="width:7%;text-align:center;">S.NO.</th>
                    <th style="width:40%;">PRODUCT NAME</th>
                    <th style="width:10%;text-align:center;">SIZE</th>
                    <th style="width:8%;text-align:center;">QTY</th>
                    <th style="width:12%;text-align:right;">RATE (&#8377;)</th>
                    <th style="width:11%;text-align:right;">DISCOUNT (&#8377;)</th>
                    <th style="width:12%;text-align:right;">AMOUNT (&#8377;)</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($order->items as $item)
                    @php
                        $mrp = $item->product?->mrp ?? ($item->price + 200);
                        $discount = max(0, $mrp - $item->price);
                        $size = $item->product?->size ?: 'M';
                        $productType = $item->product?->product_type ?: '3 Piece Set (Kurti, Pant & Dupatta)';
                        $fabric = $item->product?->color ? "Color: {$item->product->color}" : 'Fabric: Premium Cotton / Silk';
                    @endphp
                    <tr>
                        <td style="text-align:center;font-weight:700;">{{ $loop->iteration }}</td>
                        <td>
                            <div class="p-name-title">{{ $item->product_name }}</div>
                            <div class="p-sub-detail">{{ $productType }}</div>
                            @if ($item->dupatta)
                                <div class="p-sub-detail" style="color:{{ $item->dupatta === 'with' ? '#1554d1' : '#b45309' }};">
                                    {{ $item->dupatta === 'with' ? 'With Dupatta' : 'Without Dupatta' }}
                                </div>
                            @endif
                            <div class="p-fabric-detail">{{ $fabric }}</div>
                        </td>
                        <td style="text-align:center;font-weight:700;">{{ $size }}</td>
                        <td style="text-align:center;font-weight:700;">{{ $item->qty }}</td>
                        <td style="text-align:right;">{{ number_format($mrp, 2) }}</td>
                        <td style="text-align:right;">{{ number_format($discount * $item->qty, 2) }}</td>
                        <td style="text-align:right;font-weight:700;">{{ number_format($item->total, 2) }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" class="text-center text-muted py-3">No product items found in this order.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>

        <!-- Summary & Thank You Grid -->
        <div class="bill-summary-grid">
            <!-- Price details -->
            <div class="summary-card">
                <div class="summary-card-title">PRICE DETAILS</div>
                <div class="price-row">
                    <span>Total MRP</span>
                    <span>: &#8377; {{ number_format($totalMrp, 2) }}</span>
                </div>
                <div class="price-row">
                    <span>Total Discount</span>
                    <span>: &#8377; {{ number_format($totalDiscount, 2) }}</span>
                </div>
                <div class="price-row">
                    <span>Shipping Charges</span>
                    <span>: &#8377; 0.00</span>
                </div>
                <div class="grand-total-row">
                    <span>GRAND TOTAL</span>
                    <span>: &#8377; {{ number_format($order->total, 2) }}</span>
                </div>
                <div class="words-box">
                    <b>Amount in Words:</b><br>
                    Rupees {{ $amountWords }} Only
                </div>
            </div>

            <!-- Socials & QR code -->
            <div class="summary-card thankyou-box">
                <div class="thankyou-title">THANK YOU FOR SHOPPING WITH ZYRA!</div>
                <div class="d-flex flex-column align-items-center">
                    <div class="social-link"><i class="bi bi-globe me-1 text-danger"></i> {{ $company['website'] ?? 'www.shopwithzyra.in' }}</div>
                    <div class="social-link"><i class="bi bi-envelope me-1 text-danger"></i> {{ $company['email'] ?? 'order@shopwithzyra.in' }}</div>
                    <div class="social-link"><i class="bi bi-whatsapp me-1 text-success"></i> {{ $company['phone'] ?? '988487 5555' }}</div>
                    <div class="social-link"><i class="bi bi-instagram me-1 text-danger"></i> {{ $company['instagram'] ?? '@zyraofficial46' }}</div>

                    <div class="qr-wrapper">
                        <a href="{{ $instagramUrl }}" target="_blank" rel="noopener noreferrer" aria-label="Open Instagram">
                            <img src="{{ asset('qr/insta.png') }}" alt="Scan to visit Instagram" class="qr-img">
                        </a>
                        <div class="qr-caption">SCAN TO VISIT INSTAGRAM</div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Terms & Conditions -->
        <div class="terms-card">
            <div class="terms-header">TERMS &amp; CONDITIONS</div>
            <div class="terms-grid">
                <ul>
                    <li>Goods once sold will not be taken back or exchanged.</li>
                    <li>Exchange is allowed only for size issues within 7 days.</li>
                    <li>Product should be unused with original tags.</li>
                </ul>
                <ul>
                    <li>Colours may slightly vary due to photography.</li>
                    <li>For any queries, contact our customer care.</li>
                </ul>
            </div>
        </div>

        <!-- Footer Cursive Script -->
        <div class="footer-script">
            <div class="t-script">Thank You!</div>
            <div class="t-sub">FOR CHOOSING ZYRA</div>
        </div>
    </div>
@endsection
