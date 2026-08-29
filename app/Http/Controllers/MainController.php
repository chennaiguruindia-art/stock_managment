<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Addproduct;
use App\Models\Brand;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\StockReturn;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Artisan;


class MainController extends Controller
{
    public function add_product(Request $request)
    {
        // Brand is selected from a dropdown; "__new" means a fresh brand was typed in.
        $brandName = $request->input('brand');
        if ($brandName === '__new') {
            $brandName = $request->input('new_brand_name');
        }
        $brandName = trim($brandName ?? '');

        if ($brandName === '') {
            return back()->withInput()->with('error', 'Please select or enter a brand.');
        }

        $brandAbbr = Str::lower(preg_replace('/\s+/', '', $brandName));
        $brandAbbr = substr($brandAbbr, 0, 2);

        // Find or create the brand.
        $brand = Brand::firstOrCreate(
            ['name' => $brandName],
            ['abbreviation' => $brandAbbr]
        );

        $productName = trim($request->input('product_name'));
        if ($productName === '') {
            return back()->withInput()->with('error', 'Product name is required.');
        }

        // product_id is unique per product name (e.g. Chudi = 001), not per brand.
        $productId = $this->productIdFor($productName);

        $size = trim((string) $request->input('size'));
        $color = trim((string) $request->input('color'));
        $productType = trim((string) $request->input('product_type'));

        // Duplicate rule: reject only when brand + product name + product type + color + size
        // all match an existing product. Same brand/name/type with a different color or size
        // is a valid variant and allowed.
        $duplicate = Addproduct::where('brand_id', $brand->id)
            ->whereRaw('LOWER(product_name) = ?', [Str::lower($productName)])
            ->whereRaw('LOWER(product_type) = ?', [Str::lower($productType)])
            ->whereRaw('LOWER(color) = ?', [Str::lower($color)])
            ->whereRaw('LOWER(size) = ?', [Str::lower($size)])
            ->exists();

        if ($duplicate) {
            return back()->withInput()->with('error', 'This product already exists with the same brand, name, type, color and size. Choose a different color or size to add a new variant.');
        }

        $sku = strtoupper(sprintf('%s-%s-%s-%s', $brand->abbreviation, $productId, $this->colorCode($color), $size));

        // Next auto barcode = ZY + running 8-digit number, e.g. ZY19821001 -> ZY19821002.
        $autoBarcode = $this->nextAutoBarcode();

        // Allow a manually set barcode; otherwise fall back to the auto one.
        $barcode = trim($request->input('barcode') ?? '');
        if ($barcode === '') {
            $barcode = $autoBarcode;
        }

        // Last 4 digits of the barcode must be unique across all products.
        $last4 = strtoupper(substr($barcode, -4));
        if (Addproduct::whereRaw('UPPER(RIGHT(barcode, 4)) = ?', [$last4])->exists()) {
            return back()->withInput()->with('error', "Barcode ending in {$last4} is already used. Please choose a unique one.");
        }

        // sanitize numeric fields (strip currency symbols and commas)
        $sanitizeDecimal = function ($val) {
            if ($val === null) return null;
            $val = trim((string) $val);
            if ($val === '') return null;
            $clean = preg_replace('/[^0-9.\-]/', '', $val);
            return $clean === '' ? null : $clean;
        };

        $originalPrice = $sanitizeDecimal($request->input('original_price'));
        $mrp = $sanitizeDecimal($request->input('mrp'));
        $sellingPrice = $sanitizeDecimal($request->input('selling_price'));
        $discountAmount = $sanitizeDecimal($request->input('discount_amount'));

        Addproduct::create([
            'product_name' => $request->input('product_name'),
            'brand_id' => $brand->id,
            'brand' => $brand->name,
            'product_type' => $request->input('product_type'),
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
            'description' => $request->input('description')
        ]);

        // Keep the brand row in sync with the latest product's identifiers,
        // store total stock (sum across the brand's products) in product_count,
        // and advance the per-brand barcode counter.
        $brand->update([
            'barcode' => $barcode,
            'sku' => $sku,
            'product_id' => $productId,
            'product_count' => (int) Addproduct::where('brand_id', $brand->id)->sum('stock'),
        ]);
        $brand->increment('barcode_count');

        return redirect()->route('dashboard')->with('success', 'Product added successfully');
    }

    

    /**
     * Update the stock of a product and refresh the brand's total stock.
     */
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

        return redirect()->back()->with('success', 'Stock updated for ' . $product->product_name);
    }

    /**
     * Next auto barcode = ZY followed by a running 8-digit number.
     * First product gets ZY19821001, next ZY19821002, and so on.
     */
    private function nextAutoBarcode(): string
    {
        $max = Addproduct::where('barcode', 'like', 'ZY%')
            ->pluck('barcode')
            ->map(fn($b) => preg_match('/^ZY(\d{8})$/i', (string) $b) ? (int) substr($b, 2) : 0)
            ->max();

        return 'ZY' . str_pad((string) ($max > 0 ? $max + 1 : 19821001), 8, '0', STR_PAD_LEFT);
    }

    /**
     * Return next SKU / product_id and brand barcode for frontend (AJAX).
     */
    public function brandInfo(Request $request)
    {
        $brandName = $request->input('brand');
        if ($brandName === '__new') {
            $brandName = $request->input('new_brand_name');
        }
        $brandName = trim($brandName ?? '');

        if (!$brandName) {
            return response()->json(['error' => 'brand required'], 400);
        }

        $brandAbbr = Str::lower(preg_replace('/\s+/', '', $brandName));
        $brandAbbr = substr($brandAbbr, 0, 2);

        // Only look up the brand here (do NOT create it). A new brand is stored in the
        // brands table only when the product form is actually saved.
        $brand = Brand::where('name', $brandName)->first();

        $size = $request->input('size') ?? '';
        $color = $request->input('color') ?? '';
        $productName = trim($request->input('product_name') ?? '');

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

        // Preview the next auto barcode: ZY + running 8-digit number, e.g. ZY19821001.
        $barcode = $this->nextAutoBarcode();

        return response()->json([
            'sku' => $sku,
            'product_id' => $productId,
            'barcode' => $barcode,
            'seq' => $productId
        ]);
    }

    /**
     * Product id for a product name. Same name always reuses the same id; a new name
     * gets the next sequential id (never reused, tracked in counters).
     */
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

    /**
     * Short color code: single word -> first 3 letters, multi word -> first letters
     * of each word plus trailing consonants (Red -> RED, Blue -> BLU, Rani Pink -> RPNK).
     */
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

    /**
     * Read and increment a global counter value (never reused numbers).
     */
    private function nextCounter(string $name): int
    {
        DB::table('counters')->updateOrInsert(
            ['name' => $name],
            ['value' => DB::raw('value + 1')]
        );

        return (int) DB::table('counters')->where('name', $name)->value('value');
    }

    /**
     * Look up a product by its barcode and render its details.
     */
    /**
     * Check whether the last 4 digits of a barcode are unique across products.
     */
    public function barcodeCheck(Request $request)
    {
        $barcode = trim($request->input('barcode') ?? '');
        if ($barcode === '') {
            return response()->json(['last4' => '', 'unique' => false]);
        }

        $last4 = strtoupper(substr($barcode, -4));
        $unique = !Addproduct::whereRaw('UPPER(RIGHT(barcode, 4)) = ?', [$last4])->exists();

        return response()->json(['last4' => $last4, 'unique' => $unique]);
    }

    public function barcode_lookup(Request $request)
    {
        $barcode = trim($request->input('barcode') ?? '');

        $product = null;
        $notFound = false;

        if ($barcode !== '') {
            $product = Addproduct::where('barcode', $barcode)->first();
            $notFound = $product === null;
        }

        return view('Product.barcode_lookup', compact('product', 'notFound', 'barcode'));
    }

    /**
     * POS: look up a product by barcode (full or last 4 digits) and return the cart payload.
     */
    public function posAddItem(Request $request)
    {
        $barcode = trim($request->input('barcode') ?? '');

        // Match exact barcode first, then fall back to a unique last-4-digit match.
        $product = Addproduct::where('barcode', $barcode)->first();
        if (!$product && strlen($barcode) > 0) {
            $last4 = strtoupper(substr($barcode, -4));
            $product = Addproduct::whereRaw('UPPER(RIGHT(barcode, 4)) = ?', [$last4])->first();
        }

        if (!$product) {
            return response()->json(['error' => 'Product not found for barcode ' . $barcode], 404);
        }

        $price = (float) ($product->selling_price ?? 0);

        return response()->json([
            'id' => $product->id,
            'product_name' => $product->product_name,
            'brand' => $product->brand,
            'sku' => $product->sku,
            'barcode' => $product->barcode,
            'stock' => (int) $product->stock,
            'price' => $price,
        ]);
    }

    /**
     * POS: place the order. Creates the order, deducts stock, refreshes brand totals.
     */
    public function posCheckout(Request $request)
    {
        $customerName = trim($request->input('customer_name') ?? '');
        $customerMobile = trim($request->input('customer_mobile') ?? '');
        $cart = $request->input('cart');

        // The cart is posted as a JSON string via the hidden input.
        if (is_string($cart)) {
            $cart = json_decode($cart, true);
        }

        if (empty($cart) || !is_array($cart)) {
            return redirect()->back()->with('error', 'Cart is empty.');
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
                return redirect()->back()->with('error', "Insufficient stock for {$product->product_name}.");
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
            return redirect()->back()->with('error', 'No valid products in cart.');
        }

        // Order id based on the last order placed so far (no reuse after deletion).
        $lastOrderId = Order::orderByDesc('id')->value('order_id');
        $seq = $lastOrderId ? (int) substr($lastOrderId, 4) + 1 : 1;
        $orderId = 'ORD-' . str_pad($seq, 4, '0', STR_PAD_LEFT);

        $paymentMode = trim($request->input('payment_mode') ?? '') ?: 'Online UPI';

        $order = Order::create([
            'order_id' => $orderId,
            'customer_name' => $customerName ?: null,
            'customer_mobile' => $customerMobile ?: null,
            'payment_mode' => $paymentMode,
            'total' => round($total, 2),
        ]);

        foreach ($items as $item) {
            OrderItem::create($item + ['order_id' => $order->id]);

            // Deduct stock and refresh the brand's total stock.
            $product = Addproduct::find($item['product_id']);
            $product->decrement('stock', $item['qty']);
            $brand = Brand::find($product->brand_id);
            if ($brand) {
                $brand->update([
                    'product_count' => (int) Addproduct::where('brand_id', $brand->id)->sum('stock'),
                ]);
            }
        }

        return redirect()->back()
            ->with('success', "Order {$orderId} placed successfully!")
            ->with('last_order_id', $order->id);
    }

    /**
     * Invoice: generate an invoice for a single product from the invoice section.
     * Creates the order, deducts stock and shows the saved invoice on the detail page.
     */
    public function invoice_store(Request $request)
    {
        $request->validate([
            'product_id'      => 'required|integer|exists:addproducts,id',
            'quantity'        => 'required|integer|min:1',
            'customer_name'   => 'nullable|string|max:100',
            'customer_mobile' => 'nullable|string|max:20',
        ]);

        $product = Addproduct::find((int) $request->input('product_id'));
        $qty = max(1, (int) $request->input('quantity'));

        if ($qty > (int) $product->stock) {
            return back()
                ->with('error', "Insufficient stock for {$product->product_name}. Only {$product->stock} unit(s) available.");
        }

        $price = (float) $product->selling_price;
        $lineTotal = round($price * $qty, 2);

        $order = DB::transaction(function () use ($request, $product, $qty, $price, $lineTotal) {
            $order = Order::create([
                'order_id'        => Order::nextOrderId(),
                'customer_name'   => trim($request->input('customer_name') ?? '') ?: null,
                'customer_mobile' => trim($request->input('customer_mobile') ?? '') ?: null,
                'total'           => $lineTotal,
            ]);

            OrderItem::create([
                'order_id'     => $order->id,
                'product_id'   => $product->id,
                'product_name' => $product->product_name,
                'sku'          => $product->sku,
                'barcode'      => $product->barcode,
                'price'        => $price,
                'qty'          => $qty,
                'total'        => $lineTotal,
            ]);

            // Deduct stock and refresh the brand's total stock.
            $product->decrement('stock', $qty);
            $brand = Brand::find($product->brand_id);
            if ($brand) {
                $brand->update([
                    'product_count' => (int) Addproduct::where('brand_id', $brand->id)->sum('stock'),
                ]);
            }

            return $order;
        });

        return redirect()->route('invoice_detail', ['product' => $product->id])
            ->with('success', "Invoice {$order->order_id} generated successfully for {$product->product_name}!")
            ->with('last_order_id', $order->id);
    }

    /**
     * Return: mark an order item as returned and add its quantity back to stock.
     */
    public function processReturn(Request $request)
    {
        $itemId = (int) $request->input('item_id');
        $quantity = (int) $request->input('quantity');
        $reason = trim($request->input('reason') ?? '');

        $item = OrderItem::with('order')->find($itemId);

        if (!$item) {
            return redirect()->route('return_product')->with('error', 'Return item not found.');
        }

        $remaining = $item->qty - (int) $item->returned_qty;

        if ($quantity < 1 || $quantity > $remaining) {
            return redirect()->route('return_product')
                ->with('error', "Return quantity must be between 1 and {$remaining}.");
        }

        if ($reason === '') {
            return redirect()->route('return_product')
                ->with('error', 'Please enter a reason for the return.');
        }

        DB::transaction(function () use ($item, $quantity, $reason) {
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
                'reason' => $reason,
                'quantity' => $quantity,
                'returned_at' => now(),
            ]);
        });

        return redirect()->route('return_product')
            ->with('success', "Returned {$quantity} unit(s) of {$item->product_name} and stock updated.");
    }
}
