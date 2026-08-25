<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Stock Report</title>
    <style>
        body { font-family: DejaVu Sans, sans-serif; font-size: 10px; color: #2c2224; }
        .head { text-align: center; border-bottom: 2px solid #6b1f2a; padding-bottom: 8px; margin-bottom: 12px; }
        .head h1 { margin: 0; font-size: 18px; letter-spacing: 1px; color: #6b1f2a; }
        .head .sub { font-size: 9px; color: #555; margin-top: 2px; }
        h2 { font-size: 12px; margin: 0 0 8px; color: #444; }
        table { width: 100%; border-collapse: collapse; }
        th { background: #6b1f2a; color: #fff; padding: 5px 4px; font-size: 9px; text-align: left; }
        td { border: 1px solid #ddd; padding: 4px; }
        tr:nth-child(even) td { background: #faf5f6; }
        .st-out { color: #b3373f; font-weight: bold; }
        .st-low { color: #a2701c; font-weight: bold; }
        .summary { margin-top: 14px; width: 60%; }
        .summary td { border: none; padding: 2px 0; font-size: 10px; }
        .footer { margin-top: 16px; text-align: center; font-size: 8px; color: #888; border-top: 1px solid #ddd; padding-top: 6px; }
    </style>
</head>
<body>
    <div class="head">
        <h1>{{ $company['name'] }}</h1>
        <div class="sub">{{ $company['address_line1'] }}, {{ $company['address_line2'] }} | Ph: {{ $company['phone'] }} | GSTIN: {{ $company['gstin'] }}</div>
    </div>

    <h2>Stock Report &mdash; generated {{ date('d M Y, h:i A') }}</h2>

    <table>
        <thead>
            <tr>
                <th style="width:4%;">ID</th>
                <th style="width:17%;">Product</th>
                <th style="width:9%;">Brand</th>
                <th style="width:11%;">Type</th>
                <th style="width:7%;">Color</th>
                <th style="width:5%;">Size</th>
                <th style="width:14%;">SKU</th>
                <th style="width:13%;">Barcode</th>
                <th style="width:5%;">Stock</th>
                <th style="width:8%;">Price</th>
                <th style="width:7%;">Status</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($products as $p)
                @php
                    $status = $p->stock <= 0 ? ['Out', 'st-out'] : ($p->stock < 5 ? ['Low', 'st-low'] : ['Healthy', '']);
                @endphp
                <tr>
                    <td>{{ $p->product_id }}</td>
                    <td>{{ $p->product_name }}</td>
                    <td>{{ $p->brand }}</td>
                    <td>{{ $p->product_type }}</td>
                    <td>{{ $p->color }}</td>
                    <td>{{ $p->size }}</td>
                    <td>{{ $p->sku }}</td>
                    <td>{{ $p->barcode }}</td>
                    <td>{{ $p->stock }}</td>
                    <td>{{ number_format((float) $p->selling_price, 2) }}</td>
                    <td class="{{ $status[1] }}">{{ $status[0] }}</td>
                </tr>
            @empty
                <tr><td colspan="11" style="text-align:center;padding:14px;">No products found.</td></tr>
            @endforelse
        </tbody>
    </table>

    <table class="summary">
        <tr><td><b>Total products:</b></td><td>{{ $products->count() }}</td></tr>
        <tr><td><b>Total stock units:</b></td><td>{{ $totalStock }}</td></tr>
        <tr><td><b>Low stock items:</b></td><td>{{ $lowStock }}</td></tr>
        <tr><td><b>Out of stock items:</b></td><td>{{ $outOfStock }}</td></tr>
    </table>

    <div class="footer">{{ config('invoice.invoice.footer_note') }} &mdash; {{ $company['website'] }}</div>
</body>
</html>
