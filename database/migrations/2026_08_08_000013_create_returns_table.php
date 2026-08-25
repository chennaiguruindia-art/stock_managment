<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Store every returned item: invoice, customer, product, barcode, reason, quantity.
     */
    public function up(): void
    {
        Schema::create('returns', function (Blueprint $table) {
            $table->id();
            $table->string('order_id');
            $table->string('customer_name')->nullable();
            $table->foreignId('product_id')->constrained('addproducts');
            $table->string('product_name');
            $table->string('barcode')->nullable();
            $table->string('reason')->nullable();
            $table->integer('quantity');
            $table->timestamp('returned_at')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('returns');
    }
};
