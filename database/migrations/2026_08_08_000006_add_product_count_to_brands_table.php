<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Per-brand product counter used to build sequential barcodes
     * like "ly00001" (brand abbreviation + running number).
     */
    public function up(): void
    {
        Schema::table('brands', function (Blueprint $table) {
            $table->unsignedInteger('product_count')->default(0)->after('barcode');
        });
    }

    public function down(): void
    {
        Schema::table('brands', function (Blueprint $table) {
            $table->dropColumn('product_count');
        });
    }
};
