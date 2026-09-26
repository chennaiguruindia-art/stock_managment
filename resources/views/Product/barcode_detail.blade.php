@extends('layouts.app')

@section('page-title', 'Product Barcode')
@section('page-subtitle', 'Barcode for ' . $product->product_name)

@section('content')
    <style>
        .bc-page { max-width: 760px; margin: 0 auto; }
        .bc-card {
            background: #fff;
            border: 1px solid var(--line, #dfe4ee);
            border-radius: var(--radius, 10px);
            box-shadow: 0 1px 2px rgba(16, 24, 40, .05);
            padding: 1.5rem;
        }
        .bc-box {
            background: #fff;
            border: 1px solid var(--line, #dfe4ee);
            border-radius: var(--radius, 10px);
            padding: 1.4rem 1.2rem;
            box-shadow: var(--shadow, 0 1px 2px rgba(16, 24, 40, .05));
            text-align: center;
        }
        .bc-box img {
            max-width: 100%;
            height: auto;
        }
        .bc-cap {
            margin-top: .7rem;
            font-family: ui-monospace, Consolas, monospace;
            font-size: 1.02rem;
            font-weight: 700;
            letter-spacing: .08em;
            color: var(--accent, #1554d1);
        }
        .bc-url {
            margin-top: 1rem;
            padding: .6rem .8rem;
            background: var(--surface-2, #f7f9fc);
            border: 1px dashed var(--border-strong, #c5cede);
            border-radius: 8px;
            font-size: .78rem;
            color: var(--muted, #66748b);
            word-break: break-all;
            text-align: center;
        }
        .bc-meta { font-size: .875rem; margin-bottom: 0; }
        .bc-meta th {
            width: 140px;
            font-weight: 600;
            color: var(--muted, #66748b);
            background: var(--surface-2, #f7f9fc);
            border-top: 1px solid var(--line, #dfe4ee);
            padding: .55rem .7rem;
        }
        .bc-meta td { font-weight: 600; border-top: 1px solid var(--line, #dfe4ee); padding: .55rem .7rem; }
        .bc-barcode {
            font-family: ui-monospace, Consolas, monospace;
            background: var(--surface-3, #eaeff7);
            border: 1px solid var(--line, #dfe4ee);
            border-radius: 6px;
            padding: .18rem .45rem;
        }
        .btn-bc {
            border: 1px solid var(--border-strong, #c5cede);
            background: #fff;
            color: var(--text, #0e1726);
            font-weight: 600;
            border-radius: 8px;
            padding: .55rem 1.3rem;
        }
        .btn-bc:hover { background: var(--surface-2, #f7f9fc); color: var(--text, #0e1726); }
        .btn-bc i { color: var(--accent, #1554d1); }
        @media print {
            .sidebar, .topbar, .bc-actions, .btn-bc { display: none !important; }
            .main { margin-left: 0 !important; }
            .content { padding: 0 !important; }
            .bc-card { border: 1px solid #ccc; box-shadow: none; }
        }
    </style>

    <div class="bc-page">
        <div class="bc-card">

            @if ($barcodeDataUri)
                <div class="bc-box">
                    <img src="{{ $barcodeDataUri }}" alt="Barcode for {{ $product->product_name }}">
                </div>
                <div class="bc-cap">{{ $product->barcode }}</div>
                <div class="bc-url">
                    <i class="bi bi-arrow-left-right me-1"></i>
                    Scan with a barcode scanner for product {{ $product->product_id }}.
                </div>
            @else
                <div class="alert alert-danger mb-0 text-center">
                    Could not generate a barcode for this product.
                </div>
            @endif

            <hr class="my-4" style="border-color: var(--line, #dfe4ee);">

            <table class="table bc-meta mb-0">
                <tbody>
                    <tr>
                        <th>Product name</th>
                        <td>{{ $product->product_name }}</td>
                    </tr>
                    <tr>
                        <th>Product ID</th>
                        <td>{{ $product->product_id }}</td>
                    </tr>
                    <tr>
                        <th>Brand</th>
                        <td>{{ $product->brand }}</td>
                    </tr>
                    <tr>
                        <th>Variant</th>
                        <td>
                            {{ collect([$product->color, $product->size])->filter()->implode(' / ') ?: '—' }}
                        </td>
                    </tr>
                    <tr>
                        <th>SKU</th>
                        <td><span class="bc-barcode">{{ $product->sku }}</span></td>
                    </tr>
                    <tr>
                        <th>Barcode</th>
                        <td><span class="bc-barcode">{{ $product->barcode }}</span></td>
                    </tr>
                    <tr>
                        <th>Selling price</th>
                        <td>₹{{ number_format($product->selling_price ?? 0, 2) }}</td>
                    </tr>
                </tbody>
            </table>

            <div class="d-flex flex-wrap gap-2 mt-4 pt-3 border-top bc-actions" style="border-color: var(--line, #dfe4ee)!important;">
                <button type="button" class="btn btn-bc" onclick="window.print()">
                    <i class="bi bi-printer me-1"></i> Print barcode
                </button>
                <a href="{{ route('barcodes') }}" class="btn btn-bc">
                    <i class="bi bi-arrow-left me-1"></i> Back to list
                </a>
            </div>

        </div>
    </div>
@endsection