<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Track how much of an order item has been returned so partial
     * returns are supported. Replaces the old boolean flag.
     */
    public function up(): void
    {
        Schema::table('order_items', function (Blueprint $table) {
            $table->integer('returned_qty')->default(0)->after('qty');
            $table->dropColumn('returned');
        });
    }

    public function down(): void
    {
        Schema::table('order_items', function (Blueprint $table) {
            $table->dropColumn('returned_qty');
            $table->boolean('returned')->default(false)->after('qty');
        });
    }
};
