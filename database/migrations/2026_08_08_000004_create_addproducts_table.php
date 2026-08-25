<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAddproductsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('addproducts', function (Blueprint $table) {
            $table->id();
            $table->string('product_id')->unique();
            $table->string('product_name');
            $table->unsignedBigInteger('brand_id')->nullable();
            $table->string('brand')->nullable();
            $table->string('product_type')->nullable();
            $table->string('color')->nullable();
            $table->string('size')->nullable();
            $table->string('sku')->unique();
            $table->string('barcode')->nullable();
            $table->decimal('original_price', 10, 2)->nullable();
            $table->decimal('mrp', 10, 2)->nullable();
            $table->decimal('selling_price', 10, 2)->nullable();
            $table->decimal('discount_amount', 10, 2)->nullable();
            $table->text('description')->nullable();
            $table->timestamps();

            $table->foreign('brand_id')->references('id')->on('brands')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('addproducts');
    }
}
