<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasColumn('addproducts', 'has_dupatta')) {
            Schema::table('addproducts', function (Blueprint $table) {
                $table->boolean('has_dupatta')->default(false);
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasColumn('addproducts', 'has_dupatta')) {
            Schema::table('addproducts', function (Blueprint $table) {
                $table->dropColumn('has_dupatta');
            });
        }
    }
};
