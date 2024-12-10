<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('google_products', function (Blueprint $table) {
            $table->renameColumn('price', 'original_price');
            $table->float('latest_price')->nullable()->after('original_price');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('google_products', function (Blueprint $table) {
            $table->renameColumn('original_price', 'price');
            $table->dropColumn('latest_price');
        });
    }
};
