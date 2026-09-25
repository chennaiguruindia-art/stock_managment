@extends('layouts.app')

@section('page-title', 'Product Barcode')
@section('page-subtitle', 'Barcode for ' . $product->product_name)

@section('content')
    <style>
        .bc-page { max-width: 760px; margin: 0 auto; }
        .bc-card {
            background: #fff;
            border: 1px solid var(--line, #ece6e0);
            border-radius: 22px;
            box-shadow: 0 18px 50px rgba(64, 0, 0, 0.10);
            padding: 2rem;
        }
        .bc-box {
            background: #fff;
            border: 1px solid #ece6e0;
            border-radius: 14px;
            padding: 1.4rem 1.2rem;
            box-shadow: 0 8px 24px rgba(64, 0, 0, 0.06);
            text-align: center;
        }
        .bc-box img {
            max-width: 100%;
            height: auto;
        }
        .bc-cap {
            margin-top: .7rem;
            font-family: ui-monospace, Consolas, monospace;
            font-size: 1.05rem;
            font-weight: 700;
            letter-spacing: .08em;
            color: var(--accent);
        }
        .bc-url {
            margin-top: 1rem;
            padding: .6rem .8rem;
            background: #f8f5f2;
            border: 1px dashed #d8cdc4;
            border-radius: 10px;
            font-size: .78rem;
            color: var(--muted);
            word-break: break-all;
            text-align: center;
        }
        .bc-meta { font-size: .9rem; }
        .bc-meta th { width: 140px; font-weight: 600; color: var(--muted); }
        .bc-meta td { font-weight: 600; }
        .bc-barcode {
            font-family: ui-monospace, Consolas, monospace;
            background: #f2f0ec;
            border-radius: 6px;
            padding: .2rem .5rem;
        }
        .btn-bc {
            border: 1px solid var(--line, #ece6e0);
            background: #fff;
            color: var(--text);
            font-weight: 600;
            border-radius: 12px;
            padding: .6rem 1.4rem;
        }
        .btn-bc:hover { background: var(--soft, #f7f4f1); }
        .btn-bc i { color: var(--accent); }
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

            <hr class="my-4" style="border-color: var(--line, #ece6e0);">

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

            <div class="d-flex flex-wrap gap-2 mt-4 pt-3 border-top bc-actions" style="border-color: var(--line, #ece6e0)!important;">
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