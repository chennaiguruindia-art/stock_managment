<?php

use App\Http\Controllers\ApiController;
use Illuminate\Support\Facades\Route;

Route::middleware('api')->group(function () {
    Route::get('/dashboard', [ApiController::class, 'dashboard']);
    Route::get('/stock-management', [ApiController::class, 'stock_management']);
    Route::get('/barcode-lookup', [ApiController::class, 'barcode_lookup']);
    Route::get('/sales-history', [ApiController::class, 'sales_history']);
    Route::get('/report', [ApiController::class, 'report']);

    Route::post('/add-product', [ApiController::class, 'add_product']);
    Route::post('/brand-info', [ApiController::class, 'brandInfo']);
    Route::post('/barcode-check', [ApiController::class, 'barcodeCheck']);
    Route::post('/stock-management/update', [ApiController::class, 'updateStock']);
    Route::post('/return-product/process', [ApiController::class, 'processReturn']);
    Route::post('/invoice/store', [ApiController::class, 'invoice_store']);
    Route::post('/pos/add-item', [ApiController::class, 'posAddItem']);
    Route::post('/pos/checkout', [ApiController::class, 'posCheckout']);
    Route::post('/logout', [ApiController::class, 'logout']);

    Route::get('/return-product', [ApiController::class, 'return_product']);
    Route::get('/sell-pos', [ApiController::class, 'sell_pos']);
    Route::get('/invoice', [ApiController::class, 'invoices']);
    Route::get('/invoice/{product}', [ApiController::class, 'invoice_detail']);
    Route::get('/invoice/order/{orderId}', [ApiController::class, 'view_order_invoice']);
});
