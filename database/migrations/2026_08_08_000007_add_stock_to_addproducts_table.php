<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Stock quantity per product, set when adding the product.
     */
    public function up(): void
    {
        Schema::table('addproducts', function (Blueprint $table) {
            $table->unsignedInteger('stock')->default(0)->after('barcode');
        });
    }

    public function down(): void
    {
        Schema::table('addproducts', function (Blueprint $table) {
            $table->dropColumn('stock');
        });
    }
};
