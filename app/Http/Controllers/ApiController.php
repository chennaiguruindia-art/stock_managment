<?php

namespace App\Http\Controllers;

use App\Models\Addproduct;
use App\Models\Brand;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\StockReturn;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class ApiController extends Controller
{
    public function dashboard()
    {
        $products = Addproduct::orderBy('brand')->orderBy('product_name')->get();

        return response()->json([
            'success' => true,
            'data' => [
                'products' => $products,
                'totalProducts' => $products->count(),
                'totalStock' => (int) $products->sum('stock'),
                'lowStock' => $products->filter(fn ($p) => $p->stock > 0 && $p->stock < 5)->count(),
                'outOfStock' => $products->filter(fn ($p) => $p->stock <= 0)->count(),
                'healthyStock' => $products->filter(fn ($p) => $p->stock >= 5)->count(),
            ],
        ]);
    }

    public function add_product(Request $request)
    {
        $brandName = trim((string) ($request->input('brand') === '__new' ? $request->input('new_brand_name') : $request->input('brand')));

        if ($brandName === '') {
            return response()->json(['success' => false, 'message' => 'Please select or enter a brand.'], 422);
        }

        $brandAbbr = Str::lower(preg_replace('/\s+/', '', $brandName));
        $brandAbbr = substr($brandAbbr, 0, 2);

        $brand = Brand::firstOrCreate(['name' => $brandName], ['abbreviation' => $brandAbbr]);

        $productName = trim((string) $request->input('product_name'));
        if ($productName === '') {
            return response()->json(['success' => false, 'message' => 'Product name is required.'], 422);
        }

        $size = trim((string) $request->input('size'));
        $color = trim((string) $request->input('color'));
        $productType = trim((string) $request->input('product_type'));

        $duplicate = Addproduct::where('brand_id', $brand->id)
            ->whereRaw('LOWER(product_name) = ?', [Str::lower($productName)])
            ->whereRaw('LOWER(product_type) = ?', [Str::lower($productType)])
            ->whereRaw('LOWER(color) = ?', [Str::lower($color)])
            ->whereRaw('LOWER(size) = ?', [Str::lower($size)])
            ->exists();

        if ($duplicate) {
            return response()->json([
                'success' => false,
                'message' => 'This product already exists with the same brand, name, type, color and size.',
            ], 409);
        }

        $productId = $this->productIdFor($productName);
        $sku = strtoupper(sprintf('%s-%s-%s-%s', $brand->abbreviation, $productId, $this->colorCode($color), $size));
        $barcode = trim((string) $request->input('barcode')) !== '' ? trim((string) $request->input('barcode')) : $this->nextAutoBarcode();

        $last4 = strtoupper(substr($barcode, -4));
        if (Addproduct::whereRaw('UPPER(RIGHT(barcode, 4)) = ?', [$last4])->exists()) {
            return response()->json([
                'success' => false,
                'message' => "Barcode ending in {$last4} is already used. Please choose a unique one.",
            ], 409);
        }

        $sanitizeDecimal = function ($val) {
            if ($val === null) {
                return null;
            }

            $val = trim((string) $val);
            if ($val === '') {
                return null;
            }
            $clean = preg_replace('/[^0-9.\-]/', '', $val);

            return $clean === '' ? null : $clean;
        };

        $originalPrice = $sanitizeDecimal($request->input('original_price'));
        $mrp = $sanitizeDecimal($request->input('mrp'));
        $sellingPrice = $sanitizeDecimal($request->input('selling_price'));
        $discountAmount = $sanitizeDecimal($request->input('discount_amount'));

        $product = Addproduct::create([
            'product_name' => $productName,
            'brand_id' => $brand->id,
            'brand' => $brand->name,
            'product_type' => $productType,
            'color' => $color,
            'size' => $size,
            'sku' => $sku,
            'product_id' => $productId,
            'barcode' => $barcode,
            'stock' => (int) $request->input('stock', 0),
            'original_price' => $originalPrice === null ? null : (float) $originalPrice,
            'mrp' => $mrp === null ? null : (float) $mrp,
            'selling_price' => $sellingPrice === null ? null : (float) $sellingPrice,
            'discount_amount' => $discountAmount === null ? null : (float) $discountAmount,
            'description' => $request->input('description'),
        ]);

        $brand->update([
            'barcode' => $barcode,
            'sku' => $sku,
            'product_id' => $productId,
            'product_count' => (int) Addproduct::where('brand_id', $brand->id)->sum('stock'),
        ]);
        $brand->increment('barcode_count');

        return response()->json([
            'success' => true,
            'message' => 'Product added successfully',
            'data' => $product,
        ], 201);
    }

    public function brandInfo(Request $request)
    {
        $brandName = trim((string) ($request->input('brand') === '__new' ? $request->input('new_brand_name') : $request->input('brand')));

        if ($brandName === '') {
            return response()->json(['success' => false, 'message' => 'brand required'], 422);
        }

        $brandAbbr = Str::lower(preg_replace('/\s+/', '', $brandName));
        $brandAbbr = substr($brandAbbr, 0, 2);
        $brand = Brand::where('name', $brandName)->first();

        $size = trim((string) $request->input('size'));
        $color = trim((string) $request->input('color'));
        $productName = trim((string) $request->input('product_name'));

        $abbr = $brand->abbreviation ?? $brandAbbr;
        $existing = $productName !== ''
            ? Addproduct::whereRaw('LOWER(product_name) = ?', [Str::lower($productName)])
                ->whereNotNull('product_id')
                ->orderBy('id')
                ->first()
            : null;

        $productId = $existing
            ? $existing->product_id
            : str_pad((int) DB::table('counters')->where('name', 'product_id_seq')->value('value') + 1, 3, '0', STR_PAD_LEFT);

        $sku = strtoupper(sprintf('%s-%s-%s-%s', $abbr, $productId, $this->colorCode($color), $size));

        return response()->json([
            'success' => true,
            'data' => [
                'sku' => $sku,
                'product_id' => $productId,
                'barcode' => $this->nextAutoBarcode(),
                'seq' => $productId,
            ],
        ]);
    }

    public function barcodeCheck(Request $request)
    {
        $barcode = trim((string) $request->input('barcode'));
        if ($barcode === '') {
            return response()->json(['success' => false, 'message' => 'Barcode required'], 422);
        }

        $last4 = strtoupper(substr($barcode, -4));
        $unique = !Addproduct::whereRaw('UPPER(RIGHT(barcode, 4)) = ?', [$last4])->exists();

        return response()->json([
            'success' => true,
            'data' => ['last4' => $last4, 'unique' => $unique],
        ]);
    }

    public function barcode_lookup(Request $request)
    {
        $barcode = trim((string) $request->input('barcode'));

        $product = $barcode !== '' ? Addproduct::where('barcode', $barcode)->first() : null;

        return response()->json([
            'success' => true,
            'data' => [
                'barcode' => $barcode,
                'product' => $product,
                'notFound' => $product === null && $barcode !== '',
            ],
        ]);
    }

    public function stock_management()
    {
        $products = Addproduct::orderBy('brand')->orderBy('product_name')->get();

        return response()->json([
            'success' => true,
            'data' => [
                'products' => $products,
                'totalProducts' => $products->count(),
                'totalStock' => (int) $products->sum('stock'),
                'lowStock' => $products->filter(fn ($p) => $p->stock > 0 && $p->stock < 5)->count(),
                'outOfStock' => $products->filter(fn ($p) => $p->stock <= 0)->count(),
                'healthyStock' => $products->filter(fn ($p) => $p->stock >= 5)->count(),
            ],
        ]);
    }

    public function updateStock(Request $request)
    {
        $request->validate([
            'id' => 'required|integer|exists:addproducts,id',
            'stock' => 'required|integer|min:0',
        ]);

        $product = Addproduct::findOrFail($request->input('id'));
        $product->update(['stock' => (int) $request->input('stock')]);

        $brand = Brand::find($product->brand_id);
        if ($brand) {
            $brand->update([
                'product_count' => (int) Addproduct::where('brand_id', $brand->id)->sum('stock'),
            ]);
        }

        return response()->json([
            'success' => true,
            'message' => 'Stock updated for ' . $product->product_name,
            'data' => $product->fresh(),
        ]);
    }

    public function return_product(Request $request)
    {
        $invoice = trim((string) ($request->input('invoice') ?? ''));
        $orders = Order::with('items')->orderByDesc('id')->get();

        $items = $orders->flatMap(fn ($o) => $o->items->map(function ($i) use ($o) {
            return [
                'id' => $i->id,
                'order_id' => $o->order_id,
                'customer_name' => $o->customer_name,
                'product_name' => $i->product_name,
                'barcode' => $i->barcode,
                'qty' => $i->qty,
                'returned_qty' => $i->returned_qty,
            ];
        }));

        if ($invoice !== '') {
            $invClean = Str::lower($invoice);
            $invDigits = preg_replace('/\D/', '', $invoice);
            $matched = $orders->filter(function ($o) use ($invClean, $invDigits) {
                $orderClean = Str::lower($o->order_id);
                $orderDigits = preg_replace('/\D/', '', $o->order_id);

                return $orderClean === $invClean
                    || str_contains($orderClean, $invClean)
                    || str_contains($invClean, $orderClean)
                    || ($invDigits !== '' && (int) $invDigits > 0 && (int) $orderDigits === (int) $invDigits);
            });

            if ($matched->isNotEmpty()) {
                $items = $matched->flatMap(fn ($o) => $o->items->map(function ($i) use ($o) {
                    return [
                        'id' => $i->id,
                        'order_id' => $o->order_id,
                        'customer_name' => $o->customer_name,
                        'product_name' => $i->product_name,
                        'barcode' => $i->barcode,
                        'qty' => $i->qty,
                        'returned_qty' => $i->returned_qty,
                    ];
                }));
            }
        }

        return response()->json([
            'success' => true,
            'data' => [
                'orders' => $orders,
                'items' => $items,
                'recentReturns' => StockReturn::orderByDesc('id')->take(20)->get(),
                'invoice' => $invoice,
            ],
        ]);
    }

    public function processReturn(Request $request)
    {
        $request->validate([
            'item_id' => 'required|integer|exists:order_items,id',
            'quantity' => 'required|integer|min:1',
            'reason' => 'required|string|min:3',
        ]);

        $item = OrderItem::with('order')->findOrFail($request->input('item_id'));
        $remaining = $item->qty - (int) $item->returned_qty;
        $quantity = (int) $request->input('quantity');

        if ($quantity > $remaining) {
            return response()->json([
                'success' => false,
                'message' => "Return quantity must be between 1 and {$remaining}.",
            ], 422);
        }

        DB::transaction(function () use ($item, $quantity, $request) {
            $item->increment('returned_qty', $quantity);

            $product = Addproduct::find($item->product_id);
            if ($product) {
                $product->increment('stock', $quantity);
                $brand = Brand::find($product->brand_id);
                if ($brand) {
                    $brand->update([
                        'product_count' => (int) Addproduct::where('brand_id', $brand->id)->sum('stock'),
                    ]);
                }
            }

            StockReturn::create([
                'order_id' => $item->order->order_id,
                'customer_name' => $item->order->customer_name,
                'product_id' => $item->product_id,
                'product_name' => $item->product_name,
                'barcode' => $item->barcode,
                'reason' => trim((string) $request->input('reason')),
                'quantity' => $quantity,
                'returned_at' => now(),
            ]);
        });

        return response()->json([
            'success' => true,
            'message' => "Returned {$quantity} unit(s) of {$item->product_name} and stock updated.",
        ]);
    }

    public function sell_pos()
    {
        $products = Addproduct::orderBy('brand')->orderBy('product_name')->get();
        $lastOrder = session('last_order_id') ? Order::with('items')->find(session('last_order_id')) : null;

        return response()->json([
            'success' => true,
            'data' => [
                'products' => $products,
                'lastOrder' => $lastOrder,
                'nextOrderId' => Order::nextOrderId(),
            ],
        ]);
    }

    public function invoices()
    {
        return response()->json([
            'success' => true,
            'data' => [
                'products' => Addproduct::orderBy('brand')->orderBy('product_name')->get(),
                'recentInvoices' => Order::with('items.product')->orderByDesc('id')->take(15)->get(),
            ],
        ]);
    }

    public function invoice_detail(Request $request, Addproduct $product)
    {
        $savedOrder = null;
        if ($request->has('order_id')) {
            $savedOrder = Order::with('items.product')->where('order_id', $request->order_id)->orWhere('id', $request->order_id)->first();
        } elseif (session('last_order_id')) {
            $savedOrder = Order::with('items.product')->find(session('last_order_id'));
        }

        return response()->json([
            'success' => true,
            'data' => [
                'product' => $product,
                'nextInvoiceNo' => Order::nextOrderId(),
                'savedOrder' => $savedOrder,
            ],
        ]);
    }

    public function view_order_invoice($orderId)
    {
        $order = Order::with('items.product')->where('order_id', $orderId)->orWhere('id', $orderId)->firstOrFail();

        return response()->json([
            'success' => true,
            'data' => [
                'order' => $order,
            ],
        ]);
    }

    public function invoice_store(Request $request)
    {
        $request->validate([
            'product_id' => 'required|integer|exists:addproducts,id',
            'quantity' => 'required|integer|min:1',
            'customer_name' => 'nullable|string|max:100',
            'customer_mobile' => 'nullable|string|max:20',
        ]);

        $product = Addproduct::findOrFail((int) $request->input('product_id'));
        $qty = max(1, (int) $request->input('quantity'));

        if ($qty > (int) $product->stock) {
            return response()->json([
                'success' => false,
                'message' => "Insufficient stock for {$product->product_name}. Only {$product->stock} unit(s) available.",
            ], 422);
        }

        $price = (float) $product->selling_price;
        $lineTotal = round($price * $qty, 2);

        $order = DB::transaction(function () use ($request, $product, $qty, $price, $lineTotal) {
            $order = Order::create([
                'order_id' => Order::nextOrderId(),
                'customer_name' => trim((string) $request->input('customer_name')) ?: null,
                'customer_mobile' => trim((string) $request->input('customer_mobile')) ?: null,
                'total' => $lineTotal,
            ]);

            OrderItem::create([
                'order_id' => $order->id,
                'product_id' => $product->id,
                'product_name' => $product->product_name,
                'sku' => $product->sku,
                'barcode' => $product->barcode,
                'price' => $price,
                'qty' => $qty,
                'total' => $lineTotal,
            ]);

            $product->decrement('stock', $qty);
            $brand = Brand::find($product->brand_id);
            if ($brand) {
                $brand->update([
                    'product_count' => (int) Addproduct::where('brand_id', $brand->id)->sum('stock'),
                ]);
            }

            return $order;
        });

        return response()->json([
            'success' => true,
            'message' => "Invoice {$order->order_id} generated successfully for {$product->product_name}!",
            'data' => $order->load('items'),
        ], 201);
    }

    public function posAddItem(Request $request)
    {
        $barcode = trim((string) $request->input('barcode'));

        $product = Addproduct::where('barcode', $barcode)->first();
        if (!$product && $barcode !== '') {
            $last4 = strtoupper(substr($barcode, -4));
            $product = Addproduct::whereRaw('UPPER(RIGHT(barcode, 4)) = ?', [$last4])->first();
        }

        if (!$product) {
            return response()->json(['success' => false, 'message' => 'Product not found for barcode ' . $barcode], 404);
        }

        return response()->json([
            'success' => true,
            'data' => [
                'id' => $product->id,
                'product_name' => $product->product_name,
                'brand' => $product->brand,
                'sku' => $product->sku,
                'barcode' => $product->barcode,
                'stock' => (int) $product->stock,
                'price' => (float) ($product->selling_price ?? 0),
            ],
        ]);
    }

    public function posCheckout(Request $request)
    {
        $customerName = trim((string) $request->input('customer_name'));
        $customerMobile = trim((string) $request->input('customer_mobile'));
        $cart = $request->input('cart');

        if (is_string($cart)) {
            $cart = json_decode($cart, true);
        }

        if (empty($cart) || !is_array($cart)) {
            return response()->json(['success' => false, 'message' => 'Cart is empty.'], 422);
        }

        $items = [];
        $total = 0;

        foreach ($cart as $row) {
            $product = Addproduct::find($row['id'] ?? null);
            if (!$product) {
                continue;
            }

            $qty = max(1, (int) ($row['qty'] ?? 1));
            if ($qty > (int) $product->stock) {
                return response()->json([
                    'success' => false,
                    'message' => "Insufficient stock for {$product->product_name}.",
                ], 422);
            }

            $price = (float) $product->selling_price;
            $lineTotal = round($price * $qty, 2);
            $total += $lineTotal;

            $items[] = [
                'product_id' => $product->id,
                'product_name' => $product->product_name,
                'sku' => $product->sku,
                'barcode' => $product->barcode,
                'price' => $price,
                'qty' => $qty,
                'total' => $lineTotal,
            ];
        }

        if (empty($items)) {
            return response()->json(['success' => false, 'message' => 'No valid products in cart.'], 422);
        }

        $lastOrderId = Order::orderByDesc('id')->value('order_id');
        $seq = $lastOrderId ? (int) substr($lastOrderId, 4) + 1 : 1;
        $orderId = 'ORD-' . str_pad($seq, 4, '0', STR_PAD_LEFT);

        $paymentMode = trim((string) $request->input('payment_mode')) ?: 'Online UPI';

        $order = DB::transaction(function () use ($customerName, $customerMobile, $paymentMode, $total, $items, $orderId) {
            $order = Order::create([
                'order_id' => $orderId,
                'customer_name' => $customerName ?: null,
                'customer_mobile' => $customerMobile ?: null,
                'payment_mode' => $paymentMode,
                'total' => round($total, 2),
            ]);

            foreach ($items as $item) {
                OrderItem::create($item + ['order_id' => $order->id]);
                $product = Addproduct::find($item['product_id']);
                if ($product) {
                    $product->decrement('stock', $item['qty']);
                    $brand = Brand::find($product->brand_id);
                    if ($brand) {
                        $brand->update([
                            'product_count' => (int) Addproduct::where('brand_id', $brand->id)->sum('stock'),
                        ]);
                    }
                }
            }

            return $order;
        });

        return response()->json([
            'success' => true,
            'message' => "Order {$orderId} placed successfully!",
            'data' => $order->load('items'),
        ], 201);
    }

    public function sales_history()
    {
        $orders = Order::with('items')->orderByDesc('id')->get();

        return response()->json([
            'success' => true,
            'data' => [
                'orders' => $orders,
                'totalOrders' => $orders->count(),
                'totalRevenue' => (float) $orders->sum('total'),
                'totalItems' => (int) $orders->sum(fn ($o) => $o->items->sum('qty')),
                'avgOrder' => $orders->count() > 0 ? round((float) $orders->sum('total') / $orders->count(), 2) : 0,
            ],
        ]);
    }

    public function report()
    {
        $products = Addproduct::all();
        $orders = Order::with('items')->get();

        $revenue = (float) $orders->sum('total');
        $ordersCount = $orders->count();
        $itemsSold = (int) $orders->sum(fn ($o) => $o->items->sum('qty'));
        $stockValue = (float) $products->sum(fn ($p) => ($p->stock ?? 0) * ($p->selling_price ?? 0));

        $healthyStock = $products->filter(fn ($p) => $p->stock >= 5)->count();
        $lowStock = $products->filter(fn ($p) => $p->stock > 0 && $p->stock < 5)->count();
        $outOfStock = $products->filter(fn ($p) => $p->stock <= 0)->count();
        $totalProducts = $products->count();
        $healthPct = $totalProducts ? round($healthyStock / $totalProducts * 100) : 0;

        $topProducts = \App\Models\OrderItem::selectRaw('product_name, SUM(qty) as total_qty, SUM(total) as total_rev')
            ->groupBy('product_name')
            ->orderByDesc('total_qty')
            ->limit(5)
            ->get();

        $salesByDay = collect();
        for ($i = 6; $i >= 0; $i--) {
            $day = now()->subDays($i)->toDateString();
            $total = (float) $orders->filter(fn ($o) => $o->created_at->toDateString() === $day)->sum('total');
            $salesByDay->push([
                'day' => now()->subDays($i)->format('D'),
                'total' => $total,
            ]);
        }

        return response()->json([
            'success' => true,
            'data' => [
                'revenue' => $revenue,
                'ordersCount' => $ordersCount,
                'itemsSold' => $itemsSold,
                'stockValue' => $stockValue,
                'healthyStock' => $healthyStock,
                'lowStock' => $lowStock,
                'outOfStock' => $outOfStock,
                'totalProducts' => $totalProducts,
                'healthPct' => $healthPct,
                'topProducts' => $topProducts,
                'salesByDay' => $salesByDay,
            ],
        ]);
    }

    public function logout()
    {
        return response()->json([
            'success' => true,
            'message' => 'Logged out successfully.',
        ]);
    }

    private function productIdFor(string $productName): string
    {
        $existing = Addproduct::whereRaw('LOWER(product_name) = ?', [Str::lower($productName)])
            ->whereNotNull('product_id')
            ->orderBy('id')
            ->first();

        if ($existing) {
            return $existing->product_id;
        }

        return str_pad($this->nextCounter('product_id_seq'), 3, '0', STR_PAD_LEFT);
    }

    private function nextCounter(string $name): int
    {
        DB::table('counters')->updateOrInsert(
            ['name' => $name],
            ['value' => DB::raw('value + 1')]
        );

        return (int) DB::table('counters')->where('name', $name)->value('value');
    }

    private function nextAutoBarcode(): string
    {
        $max = Addproduct::where('barcode', 'like', 'ZY%')
            ->pluck('barcode')
            ->map(fn ($b) => preg_match('/^ZY(\d{8})$/i', (string) $b) ? (int) substr($b, 2) : 0)
            ->max();

        return 'ZY' . str_pad((string) ($max > 0 ? $max + 1 : 19821001), 8, '0', STR_PAD_LEFT);
    }

    private function colorCode(?string $color): string
    {
        $color = trim((string) $color);
        if ($color === '') {
            return '';
        }

        $words = preg_split('/\s+/', $color);
        if (count($words) === 1) {
            return strtoupper(substr($color, 0, 3));
        }

        $code = '';
        foreach ($words as $word) {
            $code .= strtoupper(mb_substr($word, 0, 1));
        }

        $last = strtoupper(end($words));
        $consonants = preg_replace('/[AEIOU]/', '', $last);
        $code .= substr($consonants, 1);

        return $code;
    }
}
