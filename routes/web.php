<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\indexController;
use App\Http\Controllers\MainController;




Route::get('/', [indexController::class, 'dashboard'])->name('dashboard');
Route::get('/dashboard', [indexController::class, 'dashboard']);
Route::get('/add-product', [indexController::class, 'add_product'])->name('add_product');
Route::post('/add-product', [MainController::class, 'add_product'])->name('add_product_post');  
Route::get('/add-product/import-sample', [MainController::class, 'download_sample'])->name('add_product_sample');
Route::get('/add-product/import-sample/csv', [MainController::class, 'download_sample_csv'])->name('add_product_sample_csv');
Route::post('/add-product/import', [MainController::class, 'import_products'])->name('add_product_import');
Route::post('/brand-info', [MainController::class, 'brandInfo'])->name('brand_info');
Route::post('/barcode-check', [MainController::class, 'barcodeCheck'])->name('barcode_check');
Route::get('/barcode-lookup', [indexController::class, 'barcode_lookup'])->name('barcode_lookup');
Route::post('/barcode-lookup', [MainController::class, 'barcode_lookup'])->name('barcode_lookup_post');
Route::get('/stock-management', [indexController::class, 'stock_management'])->name('stock_management');
Route::get('/stock-management/export/excel', [indexController::class, 'export_stock_excel'])->name('stock_export_excel');
Route::get('/stock-management/export/pdf', [indexController::class, 'export_stock_pdf'])->name('stock_export_pdf');
Route::post('/stock-management/update', [MainController::class, 'updateStock'])->name('stock_update');
Route::get('/return-product', [indexController::class, 'return_product'])->name('return_product');
Route::post('/return-product/process', [MainController::class, 'processReturn'])->name('return_process');
Route::get('/sell-pos', [indexController::class, 'sell_pos'])->name('sell_pos');
Route::get('/invoice', [indexController::class, 'invoices'])->name('invoice');
Route::get('/invoice/{product}', [indexController::class, 'invoice_detail'])->name('invoice_detail');
Route::get('/invoice/order/{orderId}', [indexController::class, 'view_order_invoice'])->name('view_order_invoice');
Route::post('/invoice/store', [MainController::class, 'invoice_store'])->name('invoice_store');
Route::post('/pos/add-item', [MainController::class, 'posAddItem'])->name('pos_add_item');
Route::post('/pos/checkout', [MainController::class, 'posCheckout'])->name('pos_checkout');
Route::get('/sales-history', [indexController::class, 'sales_history'])->name('sales_history');
Route::get('/report', [indexController::class, 'report'])->name('report');
Route::get('/logout', [indexController::class, 'logout'])->name('logout');

