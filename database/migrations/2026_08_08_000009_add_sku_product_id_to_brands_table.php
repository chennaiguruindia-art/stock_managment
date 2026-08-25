<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Store the latest product's sku / product_id / barcode on the brand row,
     * set while saving a product.
     */
    public function up(): void
    {
        Schema::table('brands', function (Blueprint $table) {
            $table->string('sku')->nullable()->after('barcode');
            $table->string('product_id')->nullable()->after('sku');
        });
    }

    public function down(): void
    {
        Schema::table('brands', function (Blueprint $table) {
            $table->dropColumn(['sku', 'product_id']);
        });
    }
};
