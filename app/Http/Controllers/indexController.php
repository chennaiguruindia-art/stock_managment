<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Addproduct;
use App\Models\Brand;
use App\Models\Order;
use App\Models\StockReturn;
use Illuminate\Support\Str;

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
            ->header('Content-Disposition', 'attachment; filename="stock-report-' . date('Ymd-His') . '.xls"');
    }

    public function export_stock_pdf()
    {
        $products = Addproduct::orderBy('brand')->orderBy('product_name')->get();

        $totalStock = (int) $products->sum('stock');
        $lowStock = $products->filter(fn ($p) => $p->stock > 0 && $p->stock < 5)->count();
        $outOfStock = $products->filter(fn ($p) => $p->stock <= 0)->count();
        $company = config('invoice.company');

        $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView(
            'exports.stock_pdf',
            compact('products', 'totalStock', 'lowStock', 'outOfStock', 'company')
        )->setPaper('a4', 'landscape');

        return $pdf->download('stock-report-' . date('Ymd-His') . '.pdf');
    }

    public function return_product(Request $request)
    {
        $invoice = trim($request->input('invoice') ?? '');
        $notFound = false;

        $orders = Order::with('items')->orderByDesc('id')->get();

        $items = $orders->flatMap(fn ($o) => $o->items->map(function ($i) use ($o) {
            return (object) [
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

                if ($orderClean === $invClean) {
                    return true;
                }
                if (str_contains($orderClean, $invClean) || str_contains($invClean, $orderClean)) {
                    return true;
                }
                if ($invDigits !== '' && (int) $invDigits > 0) {
                    if ((int) $orderDigits === (int) $invDigits) {
                        return true;
                    }
                }
                return false;
            });

            if ($matched->isNotEmpty()) {
                $items = $matched->flatMap(fn ($o) => $o->items->map(function ($i) use ($o) {
                    return (object) [
                        'id' => $i->id,
                        'order_id' => $o->order_id,
                        'customer_name' => $o->customer_name,
                        'product_name' => $i->product_name,
                        'barcode' => $i->barcode,
                        'qty' => $i->qty,
                        'returned_qty' => $i->returned_qty,
                    ];
                }));
            } else {
                $notFound = true;
            }
        }

        $recentReturns = StockReturn::orderByDesc('id')->take(20)->get();

        return view('Product.return_product', compact(
            'orders', 'items', 'recentReturns', 'invoice', 'notFound'
        ));
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

        $totalOrders = $orders->count();
        $totalRevenue = (float) $orders->sum('total');
        $totalItems = (int) $orders->sum(fn ($o) => $o->items->sum('qty'));
        $avgOrder = $totalOrders > 0 ? round($totalRevenue / $totalOrders, 2) : 0;

        return view('stock.sales_history', compact(
            'orders', 'totalOrders', 'totalRevenue', 'totalItems', 'avgOrder'
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
        $topProducts = \App\Models\OrderItem::selectRaw('product_name, SUM(qty) as total_qty, SUM(total) as total_rev')
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