<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Per-brand counter used for the barcode number part (e.g. ZY-ly-00001),
     * while product_count now holds the total stock for the brand.
     */
    public function up(): void
    {
        Schema::table('brands', function (Blueprint $table) {
            $table->unsignedInteger('barcode_count')->default(0)->after('product_count');
        });
    }

    public function down(): void
    {
        Schema::table('brands', function (Blueprint $table) {
            $table->dropColumn('barcode_count');
        });
    }
};
