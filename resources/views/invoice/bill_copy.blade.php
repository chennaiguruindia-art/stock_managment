@extends('layouts.app')

@section('page-title', 'Invoice - ' . ($order->order_id ?? ''))
@section('page-subtitle', 'Official Invoice with customer details, item breakdown, and price summary.')

@section('content')
    <style>
        .bill-paper {
            background: #ffffff;
            border: 1px solid #f0d5da;
            border-radius: 12px;
            box-shadow: 0 10px 30px rgba(184, 82, 104, 0.08);
            padding: 2.2rem 2.5rem;
            max-width: 900px;
            margin: 0 auto;
            color: #2c2224;
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
            color: #c25b6c;
        }

        .zyra-title {
            font-size: 2.3rem;
            font-weight: 800;
            letter-spacing: 0.15em;
            color: #c25b6c;
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
            color: #4a3b3d;
            font-weight: 600;
            margin-bottom: 0.3rem;
        }

        .zyra-script {
            font-family: 'Brush Script MT', 'Pacifico', cursive, sans-serif;
            color: #d97787;
            font-size: 1.25rem;
            margin: 0.2rem 0;
        }

        .zyra-divider {
            color: #c25b6c;
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
            background: #c25b6c;
        }

        .bill-badge-wrapper {
            text-align: right;
        }

        .bill-badge-title {
            background: #c25b6c;
            color: #ffffff;
            font-size: 1.35rem;
            font-weight: 800;
            letter-spacing: 0.12em;
            padding: 0.4rem 2rem;
            border-radius: 6px;
            display: inline-block;
            text-transform: uppercase;
            box-shadow: 0 4px 10px rgba(194, 91, 108, 0.2);
            margin-bottom: 0.8rem;
        }

        .bill-meta-table {
            font-size: 0.88rem;
            margin-left: auto;
            color: #332628;
        }

        .bill-meta-table td {
            padding: 0.15rem 0.4rem;
        }

        .bill-meta-table td.lbl {
            font-weight: 700;
            color: #2b1f21;
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
            border: 1px solid #f5cbd2;
            border-radius: 10px;
            padding: 1rem 1.2rem;
            background: #fffafa;
            position: relative;
        }

        .cust-card-title {
            color: #c25b6c;
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
            background: #f5cbd2;
        }

        .cust-name {
            font-weight: 700;
            font-size: 1rem;
            color: #2b1f21;
            margin-bottom: 0.2rem;
        }

        .cust-address {
            font-size: 0.85rem;
            color: #554447;
            line-height: 1.45;
        }

        .cust-phone {
            margin-top: 0.4rem;
            font-size: 0.88rem;
            font-weight: 700;
            color: #2b1f21;
        }

        /* Items Table */
        .bill-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 1.5rem;
            font-size: 0.88rem;
        }

        .bill-table th {
            background: #c25b6c;
            color: #ffffff;
            font-weight: 700;
            font-size: 0.82rem;
            letter-spacing: 0.06em;
            padding: 0.65rem 0.75rem;
            text-transform: uppercase;
            border: 1px solid #b34f60;
        }

        .bill-table td {
            padding: 0.75rem 0.75rem;
            border: 1px solid #f5d3d9;
            vertical-align: top;
        }

        .p-name-title {
            font-weight: 700;
            color: #221517;
            font-size: 0.92rem;
        }

        .p-sub-detail {
            font-size: 0.78rem;
            color: #c25b6c;
            font-weight: 600;
            margin-top: 0.15rem;
        }

        .p-fabric-detail {
            font-size: 0.78rem;
            color: #786568;
        }

        /* Summary Grid */
        .bill-summary-grid {
            display: grid;
            grid-template-columns: 1.1fr 0.9fr;
            gap: 1.2rem;
            margin-bottom: 1.5rem;
        }

        .summary-card {
            border: 1px solid #f5cbd2;
            border-radius: 10px;
            padding: 1rem 1.2rem;
            background: #ffffff;
        }

        .summary-card-title {
            color: #c25b6c;
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
            color: #443336;
        }

        .grand-total-row {
            background: #fce8eb;
            color: #c25b6c;
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
            color: #443336;
        }
        .words-box b {
            font-weight: 700;
        }

        .thankyou-box {
            text-align: center;
        }

        .thankyou-title {
            color: #c25b6c;
            font-weight: 800;
            font-size: 0.88rem;
            letter-spacing: 0.05em;
            margin-bottom: 0.8rem;
        }

        .social-link {
            font-size: 0.83rem;
            color: #443336;
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
            border: 1px solid #e0d0d3;
            padding: 4px;
            border-radius: 6px;
        }
        .qr-caption {
            font-size: 0.75rem;
            font-weight: 800;
            color: #c25b6c;
            letter-spacing: 0.08em;
            margin-top: 0.2rem;
        }

        /* Terms & Footer */
        .terms-card {
            border: 1px solid #f5cbd2;
            border-radius: 10px;
            padding: 0.9rem 1.2rem;
            background: #fffafa;
            margin-bottom: 1.2rem;
        }

        .terms-header {
            text-align: center;
            color: #c25b6c;
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
            content: "◆";
            font-size: 0.6rem;
            color: #c25b6c;
        }

        .terms-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 0.4rem 1.5rem;
            font-size: 0.78rem;
            color: #554447;
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
            color: #c25b6c;
        }
        .footer-script .t-sub {
            font-size: 0.75rem;
            font-weight: 800;
            letter-spacing: 0.18em;
            color: #443336;
            text-transform: uppercase;
        }

        @media print {
            body * { visibility: hidden; }
            .bill-paper, .bill-paper * { visibility: visible; }
            .bill-paper {
                position: absolute;
                left: 0;
                top: 0;
                width: 100%;
                max-width: 100%;
                border: 0;
                box-shadow: none;
                padding: 1rem;
            }
            .no-print { display: none !important; }
        }
    </style>

    <div class="d-flex justify-content-between align-items-center mb-3 no-print" style="max-width:900px;margin:0 auto 1rem;">
        <a href="{{ route('invoice') }}" class="btn btn-outline-secondary btn-sm fw-semibold">
            <i class="bi bi-arrow-left me-1"></i> Back to Invoices
        </a>
        <button type="button" class="btn btn-danger btn-sm fw-bold px-3" onclick="window.print()" style="background:#c25b6c;border-color:#c25b6c;">
            <i class="bi bi-printer me-1"></i> Print Invoice
        </button>
    </div>

    @php
        $company = config('invoice.company');
        $invoicePrefix = config('invoice.invoice.prefix', 'ZYRA/24-25/');
        $formattedInvoiceNo = $invoicePrefix . str_pad($order->id, 6, '0', STR_PAD_LEFT);
        $orderDate = $order->created_at ? $order->created_at->format('d/m/Y') : date('d/m/Y');

        // Customer details fetched from DB!
        $custName = $order->customer_name ?: 'Ms. Kavitha R';
        $custPhone = $order->customer_mobile ?: '98765 43210';
        $custAddress = '1st Floor, F 200, 1st St, Block F, Annanagar East, Chennai, Greater Chennai, Tamil Nadu 600102 Tamil Nadu, India';

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
                        <td>: {{ $company['store_location'] ?? 'ZYRA Lifestyle – Chennai' }}</td>
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
                <div class="cust-address">Bangalore,Karnataka</div>
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
                    <div class="social-link"><i class="bi bi-whatsapp me-1 text-success"></i> {{ $company['phone'] ?? '98841 25555' }}</div>
                    <div class="social-link"><i class="bi bi-instagram me-1 text-danger"></i> {{ $company['instagram'] ?? '@zyraofficial46' }}</div>

                    <div class="qr-wrapper">
                        <!-- SVG / QR placeholder -->
                        <div style="width:90px;height:90px;border:1px solid #e0d0d3;border-radius:6px;padding:4px;display:inline-block;background:#fff;">
                            <svg viewBox="0 0 100 100" fill="#2c2224">
                                <rect x="0" y="0" width="30" height="30"/>
                                <rect x="5" y="5" width="20" height="20" fill="#fff"/>
                                <rect x="10" y="10" width="10" height="10"/>
                                <rect x="70" y="0" width="30" height="30"/>
                                <rect x="75" y="5" width="20" height="20" fill="#fff"/>
                                <rect x="80" y="10" width="10" height="10"/>
                                <rect x="0" y="70" width="30" height="30"/>
                                <rect x="5" y="75" width="20" height="20" fill="#fff"/>
                                <rect x="10" y="80" width="10" height="10"/>
                                <rect x="40" y="10" width="10" height="20"/>
                                <rect x="40" y="40" width="20" height="10"/>
                                <rect x="70" y="40" width="20" height="20"/>
                                <rect x="40" y="70" width="20" height="20"/>
                                <rect x="70" y="70" width="15" height="15"/>
                            </svg>
                        </div>
                        <div class="qr-caption">SHOP WITH ZYRA</div>
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
