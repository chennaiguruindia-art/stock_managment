<table border="1" cellspacing="0" cellpadding="5">
    {{-- Main header row: product + one column per size + TOTAL, then a spacer, then the two price columns --}}
    <tr style="background:#f4cccc; color:#000000; font-weight:bold; text-align:center;">
        <td style="text-align:left;">Product_Name</td>
        @foreach ($sizeColumns as $size)
            <td>{{ $size }}</td>
        @endforeach
        <td>TOTAL</td>
        <td style="background:#ffffff; border:none;"></td>
        <td style="background:#ffd966; color:#000000;">Original Price</td>
        <td style="background:#ffd966; color:#000000;">Offer Price</td>
    </tr>

    @forelse ($groups as $g)
        <tr>
            <td style="font-weight:bold;">{{ $g['name'] }}</td>
            @foreach ($sizeColumns as $size)
                <td style="text-align:center;">{{ (int) ($g['bySize'][$size] ?? 0) }}</td>
            @endforeach
            <td style="text-align:center; font-weight:bold;">{{ $g['total'] }}</td>
            <td style="background:#ffffff; border:none;"></td>
            <td style="text-align:right;">{{ $g['mrp'] !== null ? number_format((float) $g['mrp'], 0) : '' }}</td>
            <td style="text-align:right;">{{ $g['selling_price'] !== null ? number_format((float) $g['selling_price'], 0) : '' }}</td>
        </tr>
    @empty
        <tr>
            <td colspan="{{ count($sizeColumns) + 5 }}">No products found.</td>
        </tr>
    @endforelse

    {{-- Footer: total stock per size column + grand total --}}
    <tr style="background:#e2efda; font-weight:bold;">
        <td>TOTAL STOCK</td>
        @foreach ($sizeColumns as $size)
            <td style="text-align:center;">{{ (int) ($sizeTotals[$size] ?? 0) }}</td>
        @endforeach
        <td style="text-align:center;">{{ $grandTotal }}</td>
        <td style="background:#ffffff; border:none;"></td>
        <td colspan="2"></td>
    </tr>

    <tr>
        <td colspan="{{ count($sizeColumns) + 5 }}" style="color:#666666; font-size:10px;">
            Generated: {{ date('d M Y, h:i A') }} &mdash; {{ config('invoice.company.name') }}
        </td>
    </tr>
</table>
