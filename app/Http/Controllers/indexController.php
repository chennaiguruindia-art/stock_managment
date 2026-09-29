<?php

namespace App\Http\Controllers;

use App\Models\Addproduct;
use App\Models\Brand;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\StockReturn;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Picqer\Barcode\BarcodeGeneratorPNG;

class indexController extends Controller
{
    public function dashboard()
    {
        $products = Addproduct::orderBy('brand')->orderBy('product_name')->get();

        $totalProducts = $products->count();
        $totalStock = (int) $products->sum('stock');
        $lowStock = $products->filter(fn ($p) => $p->stock > 0 && $p->stock < 5)->count();
        $outOfStock = $products->filter(fn ($p) => $p->stock <= 0)->count();
        $healthyStock = $products->filter(fn ($p) => $p->stock >= 5)->count();

        return view('dashboard.index', compact(
            'products', 'totalProducts', 'totalStock', 'lowStock', 'outOfStock', 'healthyStock'
        ));
    }

    public function add_product()
    {
        $brands = Brand::orderBy('name')->get();

        return view('Product.add_product', compact('brands'));
    }

    /**
     * List all products (name + product id) with a link to open each product's barcode.
     */
    public function barcodes()
    {
        $products = Addproduct::orderBy('brand')->orderBy('product_name')->get();

        return view('Product.barcodes', compact('products'));
    }

    /**
     * Show the printable barcode page for a single product. The 1D barcode encodes
     * the product's barcode number (e.g. ZY19821005).
     */
    public function barcode_detail(Addproduct $product)
    {
        $barcodeDataUri = $this->barcodeDataUri($product->barcode);

        return view('Product.barcode_detail', compact('product', 'barcodeDataUri'));
    }

    /**
     * Build a base64 PNG data-URI 1D barcode (Code128) for the given value.
     */
    private function barcodeDataUri(string $value): string
    {
        try {
            $generator = new BarcodeGeneratorPNG;
            $image = $generator->getBarcode($value, $generator::TYPE_CODE_128, 2, 60);

            return 'data:image/png;base64,'.base64_encode($image);
        } catch (\Throwable $e) {
            return '';
        }
    }

    public function barcode_lookup()
    {
        return view('Product.barcode_lookup');
    }

    public function stock_management()
    {
        $products = Addproduct::orderBy('brand')->orderBy('product_name')->get();

        $totalProducts = $products->count();
        $totalStock = (int) $products->sum('stock');
        $lowStock = $products->filter(fn ($p) => $p->stock > 0 && $p->stock < 5)->count();
        $outOfStock = $products->filter(fn ($p) => $p->stock <= 0)->count();
        $healthyStock = $products->filter(fn ($p) => $p->stock >= 5)->count();

        return view('stock.stock_maintanace', compact('products', 'totalProducts', 'totalStock', 'lowStock', 'outOfStock', 'healthyStock'));
    }

    public function export_stock_excel()
    {
        $products = Addproduct::orderBy('brand')->orderBy('product_name')->get();

        return response()
            ->view('exports.stock_excel', compact('products'))
            ->header('Content-Type', 'application/vnd.ms-excel')
            ->header('Content-Disposition', 'attachment; filename="stock-report-'.date('Ymd-His').'.xls"');
    }

    public function export_stock_pdf()
    {
        $products = Addproduct::orderBy('brand')->orderBy('product_name')->get();

        $totalStock = (int) $products->sum('stock');
        $lowStock = $products->filter(fn ($p) => $p->stock > 0 && $p->stock < 5)->count();
        $outOfStock = $products->filter(fn ($p) => $p->stock <= 0)->count();
        $company = config('invoice.company');

        $pdf = Pdf::loadView(
            'exports.stock_pdf',
            compact('products', 'totalStock', 'lowStock', 'outOfStock', 'company')
        )->setPaper('a4', 'landscape');

        return $pdf->download('stock-report-'.date('Ymd-His').'.pdf');
    }

    public function return_product(Request $request)
    {
        $invoice = trim($request->input('invoice') ?? '');
        $notFound = false;
        $order = null;
        $items = collect();

        // Bill-first: only ever show the items of the bill that was asked for.
        if ($invoice !== '') {
            $order = $this->findOrderByInvoice($invoice);

            if ($order) {
                $order->load('items');
                $items = $order->items;
            } else {
                $notFound = true;
            }
        }

        $recentReturns = StockReturn::orderByDesc('id')->take(20)->get();
        $recentOrders = Order::withCount('items')->orderByDesc('id')->take(8)->get();

        return view('Product.return_product', compact(
            'order', 'items', 'recentOrders', 'recentReturns', 'invoice', 'notFound'
        ));
    }

    /**
     * Resolve a typed bill number (ORD-0001, 0001, 1, or a partial string) to one order.
     */
    private function findOrderByInvoice(string $invoice): ?Order
    {
        $needle = trim($invoice);

        if ($needle === '') {
            return null;
        }

        $exact = Order::where('order_id', $needle)->first();
        if ($exact) {
            return $exact;
        }

        $lower = Str::lower($needle);
        $digits = preg_replace('/\D/', '', $needle);

        $candidates = Order::query()
            ->where('order_id', 'like', '%'.$this->likeEscape($needle).'%')
            ->when($digits !== '' && (int) $digits > 0, fn ($q) => $q->orWhere('order_id', 'like', '%'.$digits.'%'))
            ->orderByDesc('id')
            ->limit(50)
            ->get();

        return $candidates->first(function (Order $o) use ($lower, $digits) {
            $orderLower = Str::lower($o->order_id);
            $orderDigits = preg_replace('/\D/', '', $o->order_id);

            if ($orderLower === $lower) {
                return true;
            }

            if (str_contains($orderLower, $lower) || str_contains($lower, $orderLower)) {
                return true;
            }

            return $digits !== '' && (int) $digits > 0 && (int) $orderDigits === (int) $digits;
        });
    }

    private function likeEscape(string $value): string
    {
        return str_replace(['\\', '%', '_'], ['\\\\', '\%', '\_'], $value);
    }

    public function sell_pos()
    {
        $products = Addproduct::orderBy('brand')->orderBy('product_name')->get();

        $lastOrder = null;
        if (session('last_order_id')) {
            $lastOrder = Order::with('items')->find(session('last_order_id'));
        }

        $nextOrderId = Order::nextOrderId();

        return view('Selling.pos', compact('products', 'lastOrder', 'nextOrderId'));
    }

    public function invoices()
    {
        $products = Addproduct::orderBy('brand')->orderBy('product_name')->get();
        $recentInvoices = Order::with('items.product')->orderByDesc('id')->take(15)->get();

        return view('invoice.index', compact('products', 'recentInvoices'));
    }

    public function customer_invoices(Request $request)
    {
        $mobile = trim((string) $request->input('mobile', ''));
        $searched = $request->filled('mobile');
        $orders = collect();
        $customerName = null;
        $totalSpend = 0;
        $totalOrders = 0;

        if ($mobile !== '') {
            $digits = preg_replace('/\D/', '', $mobile);

            $orders = Order::with('items.product')
                ->where(function ($query) use ($mobile, $digits) {
                    $query->where('customer_mobile', 'like', "%{$mobile}%");
                    if ($digits !== '') {
                        $query->orWhere('customer_mobile', 'like', "%{$digits}%");
                    }
                })
                ->orderByDesc('id')
                ->get();

            $totalOrders = $orders->count();
            $totalSpend = (float) $orders->sum('total');
            $customerName = $orders->firstWhere('customer_name', '!=', null)?->customer_name;
        }

        return view('invoice.customer_invoices', compact(
            'mobile',
            'searched',
            'orders',
            'customerName',
            'totalSpend',
            'totalOrders'
        ));
    }

    public function invoice_detail(Request $request, Addproduct $product)
    {
        $nextInvoiceNo = Order::nextOrderId();

        $savedOrder = null;
        if ($request->has('order_id')) {
            $savedOrder = Order::with('items.product')->where('order_id', $request->order_id)->orWhere('id', $request->order_id)->first();
        } elseif (session('last_order_id')) {
            $savedOrder = Order::with('items.product')->find(session('last_order_id'));
        }

        return view('invoice.detail', compact('product', 'nextInvoiceNo', 'savedOrder'));
    }

    public function view_order_invoice($orderId)
    {
        $order = Order::with('items.product')->where('order_id', $orderId)->orWhere('id', $orderId)->firstOrFail();

        return view('invoice.bill_copy', compact('order'));
    }

    public function sales_history()
    {
        $orders = Order::with('items')->orderByDesc('id')->get();

        $totalOrders   = $orders->count();
        $totalReturned = (float) $orders->sum(fn (Order $o) => $o->returnedTotal());
        $totalRevenue  = (float) $orders->sum(fn (Order $o) => $o->netTotal());
        $totalItems    = (int) $orders->sum(fn (Order $o) => $o->netQty());
        $returnedItems = (int) $orders->sum(fn (Order $o) => $o->returnedQty());
        $ordersWithReturns = (int) $orders->filter(fn (Order $o) => $o->returnedQty() > 0)->count();
        $avgOrder = $totalOrders > 0 ? round($totalRevenue / $totalOrders, 2) : 0;

        return view('stock.sales_history', compact(
            'orders', 'totalOrders', 'totalRevenue', 'totalReturned',
            'totalItems', 'returnedItems', 'ordersWithReturns', 'avgOrder'
        ));
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

        // Top selling products (by quantity sold).
        $topProducts = OrderItem::selectRaw('product_name, SUM(qty) as total_qty, SUM(total) as total_rev')
            ->groupBy('product_name')
            ->orderByDesc('total_qty')
            ->limit(5)
            ->get();

        // Sales per day for the last 7 days.
        $salesByDay = collect();
        for ($i = 6; $i >= 0; $i--) {
            $day = now()->subDays($i)->toDateString();
            $total = (float) $orders->filter(fn ($o) => $o->created_at->toDateString() === $day)->sum('total');
            $salesByDay->push([
                'day' => now()->subDays($i)->format('D'),
                'total' => $total,
            ]);
        }
        $maxDay = max($salesByDay->max('total'), 1);

        return view('stock.report', compact(
            'revenue', 'ordersCount', 'itemsSold', 'stockValue',
            'healthyStock', 'lowStock', 'outOfStock', 'totalProducts', 'healthPct',
            'topProducts', 'salesByDay', 'maxDay'
        ));
    }

    public function logout()
    {
        return view('logout');
    }
}
