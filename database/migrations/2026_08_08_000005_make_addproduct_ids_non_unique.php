<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * product_id is now shared by every product of the same brand, and sku repeats
     * for identical variants. The barcode is the unique identifier per product.
     */
    public function up(): void
    {
        Schema::table('addproducts', function (Blueprint $table) {
            $table->dropUnique('addproducts_product_id_unique');
            $table->dropUnique('addproducts_sku_unique');
        });
    }

    public function down(): void
    {
        Schema::table('addproducts', function (Blueprint $table) {
            $table->unique('product_id');
            $table->unique('sku');
        });
    }
};
