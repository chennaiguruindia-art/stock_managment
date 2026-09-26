<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasColumn('addproducts', 'dupatta_discount')) {
            Schema::table('addproducts', function (Blueprint $table) {
                // Rupees removed from selling_price when sold without the dupatta.
                $table->decimal('dupatta_discount', 10, 2)->default(300);
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasColumn('addproducts', 'dupatta_discount')) {
            Schema::table('addproducts', function (Blueprint $table) {
                $table->dropColumn('dupatta_discount');
            });
        }
    }
};
