<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasColumn('order_items', 'dupatta')) {
            Schema::table('order_items', function (Blueprint $table) {
                // 'with' | 'without' | null (null = product has no dupatta option)
                $table->string('dupatta')->nullable();
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasColumn('order_items', 'dupatta')) {
            Schema::table('order_items', function (Blueprint $table) {
                $table->dropColumn('dupatta');
            });
        }
    }
};
