<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Global counters so product_id and barcode numbers are never reused.
     */
    public function up(): void
    {
        Schema::create('counters', function (Blueprint $table) {
            $table->string('name')->primary();
            $table->unsignedBigInteger('value')->default(0);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('counters');
    }
};
