<table border="1" cellspacing="0" cellpadding="4">
    <tr style="background:#6b1f2a;color:#ffffff;">
        <th>ID</th>
        <th>Product</th>
        <th>Brand</th>
        <th>Type</th>
        <th>Color</th>
        <th>Size</th>
        <th>SKU</th>
        <th>Barcode</th>
        <th>Stock</th>
        <th>Selling Price</th>
        <th>MRP</th>
        <th>Status</th>
    </tr>
    @forelse ($products as $p)
        @php
            $status = $p->stock <= 0 ? 'Out of stock' : ($p->stock < 5 ? 'Low' : 'Healthy');
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
            <td>{{ $p->selling_price }}</td>
            <td>{{ $p->mrp }}</td>
            <td>{{ $status }}</td>
        </tr>
    @empty
        <tr><td colspan="12">No products found.</td></tr>
    @endforelse
    <tr>
        <td colspan="8" style="text-align:right;font-weight:bold;">TOTAL STOCK</td>
        <td style="font-weight:bold;">{{ $products->sum('stock') }}</td>
        <td colspan="3"></td>
    </tr>
    <tr>
        <td colspan="12">Generated: {{ date('d M Y, h:i A') }} &mdash; {{ config('invoice.company.name') }}</td>
    </tr>
</table>
