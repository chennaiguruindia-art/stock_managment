<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Track which order items have been returned so they can't be returned twice.
     */
    public function up(): void
    {
        Schema::table('order_items', function (Blueprint $table) {
            $table->boolean('returned')->default(false)->after('total');
        });
    }

    public function down(): void
    {
        Schema::table('order_items', function (Blueprint $table) {
            $table->dropColumn('returned');
        });
    }
};
