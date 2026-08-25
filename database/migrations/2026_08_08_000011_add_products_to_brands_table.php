<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * JSON list of every product saved under the brand, so the brand row keeps
     * all product names, barcodes, product ids, skus, colors, sizes and stock.
     */
    public function up(): void
    {
        Schema::table('brands', function (Blueprint $table) {
            $table->json('products')->nullable()->after('product_count');
        });
    }

    public function down(): void
    {
        Schema::table('brands', function (Blueprint $table) {
            $table->dropColumn('products');
        });
    }
};
