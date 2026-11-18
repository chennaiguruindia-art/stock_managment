<table border="1" cellspacing="0" cellpadding="5">
    {{-- Company & Report Header --}}
    <tr style="background:#1554d1; color:#ffffff; font-weight:bold; font-size:16px;">
        <td colspan="12" style="text-align:center;">
            {{ $company['name'] ?? 'ZYRA LIFESTYLE' }} — GST SALES & TURNOVER REPORT
        </td>
    </tr>
    <tr style="background:#f0f4ff; color:#333333; font-size:11px;">
        <td colspan="12" style="text-align:center;">
            GSTIN: <b>{{ trim($company['gstin'] ?? '33BMTPA7854C2Z0') }}</b> &nbsp;|&nbsp; 
            Phone: {{ $company['phone'] ?? '' }} &nbsp;|&nbsp; 
            Period: <b>{{ $monthLabel }}</b> &nbsp;|&nbsp; 
            GST Rate: <b>{{ $gstRate }}% (Price Inclusive)</b> &nbsp;|&nbsp;
            Generated on: {{ date('d M Y, h:i A') }}
        </td>
    </tr>
    <tr><td colspan="12" style="border:none;"></td></tr>

    {{-- SECTION 1: Monthly Consolidated Summary --}}
    <tr style="background:#0f9d58; color:#ffffff; font-weight:bold; font-size:13px;">
        <td colspan="12">1. MONTH-WISE CONSOLIDATED SUMMARY (GSTR-3B)</td>
    </tr>
    <tr style="background:#e8f5e9; font-weight:bold; text-align:center; font-size:11px;">
        <td style="text-align:left;">Month / Period</td>
        <td>Total Invoices</td>
        <td>Total Units Sold</td>
        <td>Gross Amount (₹)</td>
        <td>Returns (₹)</td>
        <td>Net Turnover (₹)</td>
        <td>Taxable Value (₹)</td>
        <td>CGST ({{ number_format($gstRate / 2, 2) }}%) (₹)</td>
        <td>SGST ({{ number_format($gstRate / 2, 2) }}%) (₹)</td>
        <td>Total GST ({{ $gstRate }}%) (₹)</td>
        <td colspan="2">Filing Remarks</td>
    </tr>

    @php
        $sumInvoices = 0;
        $sumUnits = 0;
        $sumGross = 0;
        $sumReturns = 0;
        $sumNet = 0;
        $sumTaxable = 0;
        $sumCgst = 0;
        $sumSgst = 0;
        $sumGst = 0;
    @endphp

    @forelse ($monthlySummaries as $ms)
        @php
            $sumInvoices += $ms['orders_count'];
            $sumUnits += $ms['items_sold'];
            $sumGross += $ms['gross_total'];
            $sumReturns += $ms['returned_total'];
            $sumNet += $ms['net_total'];
            $sumTaxable += $ms['taxable_value'];
            $sumCgst += $ms['cgst'];
            $sumSgst += $ms['sgst'];
            $sumGst += $ms['total_gst'];
        @endphp
        <tr style="font-size:11px;">
            <td style="font-weight:bold;">{{ $ms['month_label'] }}</td>
            <td style="text-align:center;">{{ $ms['orders_count'] }}</td>
            <td style="text-align:center;">{{ $ms['items_sold'] }}</td>
            <td style="text-align:right;">{{ number_format($ms['gross_total'], 2, '.', '') }}</td>
            <td style="text-align:right; color:#c62828;">{{ number_format($ms['returned_total'], 2, '.', '') }}</td>
            <td style="text-align:right; font-weight:bold;">{{ number_format($ms['net_total'], 2, '.', '') }}</td>
            <td style="text-align:right; font-weight:bold; color:#1554d1;">{{ number_format($ms['taxable_value'], 2, '.', '') }}</td>
            <td style="text-align:right;">{{ number_format($ms['cgst'], 2, '.', '') }}</td>
            <td style="text-align:right;">{{ number_format($ms['sgst'], 2, '.', '') }}</td>
            <td style="text-align:right; font-weight:bold; color:#0f9d58;">{{ number_format($ms['total_gst'], 2, '.', '') }}</td>
            <td colspan="2" style="text-align:center; color:#555555;">Ready for GSTR-3B Table 3.1</td>
        </tr>
    @empty
        <tr><td colspan="12" style="text-align:center;">No records available.</td></tr>
    @endforelse

    <tr style="background:#c8e6c9; font-weight:bold; font-size:11px;">
        <td style="text-align:left;">CONSOLIDATED TOTAL</td>
        <td style="text-align:center;">{{ $sumInvoices }}</td>
        <td style="text-align:center;">{{ $sumUnits }}</td>
        <td style="text-align:right;">{{ number_format($sumGross, 2, '.', '') }}</td>
        <td style="text-align:right; color:#c62828;">{{ number_format($sumReturns, 2, '.', '') }}</td>
        <td style="text-align:right;">{{ number_format($sumNet, 2, '.', '') }}</td>
        <td style="text-align:right; color:#1554d1;">{{ number_format($sumTaxable, 2, '.', '') }}</td>
        <td style="text-align:right;">{{ number_format($sumCgst, 2, '.', '') }}</td>
        <td style="text-align:right;">{{ number_format($sumSgst, 2, '.', '') }}</td>
        <td style="text-align:right; color:#0f9d58;">{{ number_format($sumGst, 2, '.', '') }}</td>
        <td colspan="2"></td>
    </tr>

    <tr><td colspan="12" style="border:none;"></td></tr>
    <tr><td colspan="12" style="border:none;"></td></tr>

    {{-- SECTION 2: Invoice-level sales register --}}
    <tr style="background:#1554d1; color:#ffffff; font-weight:bold; font-size:13px;">
        <td colspan="12">2. INVOICE-LEVEL SALES REGISTER (GSTR-1 B2C / B2B)</td>
    </tr>
    <tr style="background:#e3f2fd; font-weight:bold; text-align:center; font-size:11px;">
        <td>Invoice No</td>
        <td>Invoice Date</td>
        <td>Customer Name</td>
        <td>Customer Mobile</td>
        <td>Payment Mode</td>
        <td>Items Sold</td>
        <td>Gross Bill (₹)</td>
        <td>Returns (₹)</td>
        <td>Net Invoice (₹)</td>
        <td>Taxable Value (₹)</td>
        <td>CGST (₹)</td>
        <td>SGST (₹)</td>
    </tr>

    @php
        $regGross = 0;
        $regReturns = 0;
        $regNet = 0;
        $regTaxable = 0;
        $regCgst = 0;
        $regSgst = 0;
    @endphp

    @forelse ($orders as $order)
        @php
            $net = (float) $order->netTotal();
            $ret = (float) $order->returnedTotal();
            $gross = (float) $order->total;
            $taxable = $net > 0 ? round($net / (1 + ($gstRate / 100)), 2) : 0;
            $gst = round($net - $taxable, 2);
            $cgst = round($gst / 2, 2);
            $sgst = round($gst - $cgst, 2);

            $regGross += $gross;
            $regReturns += $ret;
            $regNet += $net;
            $regTaxable += $taxable;
            $regCgst += $cgst;
            $regSgst += $sgst;
        @endphp
        <tr style="font-size:11px;">
            <td style="font-weight:bold;">{{ $order->order_id }}</td>
            <td>{{ $order->created_at ? $order->created_at->format('d-m-Y H:i') : '—' }}</td>
            <td>{{ $order->customer_name ?: 'Walk-in' }}</td>
            <td style="text-align:center;">{{ $order->customer_mobile ?: '—' }}</td>
            <td style="text-align:center;">{{ strtoupper($order->payment_mode ?: 'CASH') }}</td>
            <td style="text-align:center;">{{ $order->netQty() }}</td>
            <td style="text-align:right;">{{ number_format($gross, 2, '.', '') }}</td>
            <td style="text-align:right; color:#c62828;">{{ number_format($ret, 2, '.', '') }}</td>
            <td style="text-align:right; font-weight:bold;">{{ number_format($net, 2, '.', '') }}</td>
            <td style="text-align:right; color:#1554d1;">{{ number_format($taxable, 2, '.', '') }}</td>
            <td style="text-align:right;">{{ number_format($cgst, 2, '.', '') }}</td>
            <td style="text-align:right;">{{ number_format($sgst, 2, '.', '') }}</td>
        </tr>
    @empty
        <tr><td colspan="12" style="text-align:center;">No invoices found.</td></tr>
    @endforelse

    <tr style="background:#bbdefb; font-weight:bold; font-size:11px;">
        <td colspan="6" style="text-align:right;">REGISTER TOTAL:</td>
        <td style="text-align:right;">{{ number_format($regGross, 2, '.', '') }}</td>
        <td style="text-align:right; color:#c62828;">{{ number_format($regReturns, 2, '.', '') }}</td>
        <td style="text-align:right;">{{ number_format($regNet, 2, '.', '') }}</td>
        <td style="text-align:right; color:#1554d1;">{{ number_format($regTaxable, 2, '.', '') }}</td>
        <td style="text-align:right;">{{ number_format($regCgst, 2, '.', '') }}</td>
        <td style="text-align:right;">{{ number_format($regSgst, 2, '.', '') }}</td>
    </tr>
</table>
