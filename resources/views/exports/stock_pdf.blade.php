<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Stock Report</title>
    <style>
        body { font-family: DejaVu Sans, sans-serif; font-size: 10px; color: #0e1726; }
        .head { text-align: center; border-bottom: 2px solid #0f1f36; padding-bottom: 8px; margin-bottom: 12px; }
        .head h1 { margin: 0; font-size: 18px; letter-spacing: 1px; color: #0f1f36; }
        .head .sub { font-size: 9px; color: #555; margin-top: 2px; }
        h2 { font-size: 12px; margin: 0 0 8px; color: #444; }

        table { width: 100%; border-collapse: collapse; }
        th, td { border: 1px solid #ccc; padding: 4px 6px; }

        /* main header row */
        thead .main-head td {
            background: #f4cccc;
            font-weight: bold;
            text-align: center;
            color: #000;
        }
        thead .main-head td.name { text-align: left; }
        /* spacer column between matrix and prices */
        thead .main-head td.spacer,
        tbody td.spacer,
        tfoot td.spacer { background: #ffffff; border: none; width: 10px; }
        /* price columns */
        .price-head { background: #ffd966 !important; color: #000 !important; text-align: center; }
        td.price { text-align: right; }

        tbody td.name { font-weight: bold; }
        tbody td.num { text-align: center; }
        tbody td.total { text-align: center; font-weight: bold; }
        tr:nth-child(even) td { background: #f5f7fb; }
        /* keep the spacer + price cells clean on zebra rows */
        tr:nth-child(even) td.spacer { background: #ffffff; }

        /* footer totals */
        tfoot td { background: #e2efda; font-weight: bold; }
        tfoot td.num { text-align: center; }

        .summary { margin-top: 14px; width: 55%; }
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
            <tr class="main-head">
                <td class="name">Product_Name</td>
                @foreach ($sizeColumns as $size)
                    <td>{{ $size }}</td>
                @endforeach
                <td>TOTAL</td>
                <td class="spacer"></td>
                <td class="price-head">Original Price</td>
                <td class="price-head">Offer Price</td>
            </tr>
        </thead>
        <tbody>
            @forelse ($groups as $g)
                <tr>
                    <td class="name">{{ $g['name'] }}</td>
                    @foreach ($sizeColumns as $size)
                        <td class="num">{{ (int) ($g['bySize'][$size] ?? 0) }}</td>
                    @endforeach
                    <td class="total">{{ $g['total'] }}</td>
                    <td class="spacer"></td>
                    <td class="price">{{ $g['mrp'] !== null ? number_format((float) $g['mrp'], 0) : '' }}</td>
                    <td class="price">{{ $g['selling_price'] !== null ? number_format((float) $g['selling_price'], 0) : '' }}</td>
                </tr>
            @empty
                <tr><td colspan="{{ count($sizeColumns) + 5 }}" style="text-align:center;padding:14px;">No products found.</td></tr>
            @endforelse
        </tbody>
        <tfoot>
            <tr>
                <td>TOTAL STOCK</td>
                @foreach ($sizeColumns as $size)
                    <td class="num">{{ (int) ($sizeTotals[$size] ?? 0) }}</td>
                @endforeach
                <td class="num">{{ $grandTotal }}</td>
                <td class="spacer"></td>
                <td colspan="2"></td>
            </tr>
        </tfoot>
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
