@extends('layouts.app')

@section('page-title', 'Barcode Lookup')
@section('page-subtitle', 'Enter a barcode to view product details.')

@section('content')
    <div class="card-panel">
        <div class="section-title">Search by barcode</div>
        <form action="{{ route('barcode_lookup') }}" method="POST">
            @csrf
            <div class="row g-3 align-items-end">
                <div class="col-md-8">
                    <label class="form-label">Barcode</label>
                    <input type="text" class="form-control" placeholder="Scan or type barcode e.g. ly00001" name="barcode" value="{{ $barcode ?? old('barcode') }}" autofocus>
                </div>
                <div class="col-md-4">
                    <button class="btn btn-primary">Search</button>
                </div>
            </div>
        </form>

        @if (!empty($notFound))
            <div class="alert alert-danger mt-3 mb-0">No product found for barcode <strong>{{ $barcode }}</strong>.</div>
        @endif

        @if (!empty($product))
            <div class="section-title mt-4">Product details</div>
            <table class="table mb-0">
                <tbody>
                    <tr>
                        <th style="width: 220px;">Product ID</th>
                        <td>{{ $product->product_id }}</td>
                    </tr>
                    <tr>
                        <th>Product name</th>
                        <td>{{ $product->product_name }}</td>
                    </tr>
                    <tr>
                        <th>Brand</th>
                        <td>{{ $product->brand }}</td>
                    </tr>
                    <tr>
                        <th>Product type</th>
                        <td>{{ $product->product_type }}</td>
                    </tr>
                    <tr>
                        <th>Color</th>
                        <td>{{ $product->color }}</td>
                    </tr>
                    <tr>
                        <th>Size</th>
                        <td>{{ $product->size }}</td>
                    </tr>
                    <tr>
                        <th>SKU</th>
                        <td>{{ $product->sku }}</td>
                    </tr>
                    <tr>
                        <th>Barcode</th>
                        <td><strong>{{ $product->barcode }}</strong></td>
                    </tr>
                    <tr>
                        <th>Stock (quantity)</th>
                        <td>
                            @if ($product->stock > 0)
                                {{ $product->stock }}
                            @else
                                <span class="text-danger">Out of stock</span>
                            @endif
                        </td>
                    </tr>
                    <tr>
                        <th>Original price</th>
                        <td>₹{{ number_format($product->original_price ?? 0, 2) }}</td>
                    </tr>
                    <tr>
                        <th>MRP</th>
                        <td>₹{{ number_format($product->mrp ?? 0, 2) }}</td>
                    </tr>
                    <tr>
                        <th>Selling price</th>
                        <td>₹{{ number_format($product->selling_price ?? 0, 2) }}</td>
                    </tr>
                    <tr>
                        <th>Discount amount</th>
                        <td>₹{{ number_format($product->discount_amount ?? 0, 2) }}</td>
                    </tr>
                    @if ($product->description)
                        <tr>
                            <th>Description</th>
                            <td>{{ $product->description }}</td>
                        </tr>
                    @endif
                </tbody>
            </table>
        @endif
    </div>
@endsection
