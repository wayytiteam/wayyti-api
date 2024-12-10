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
            $table->string('title')->nullable()->after('product_id');
            $table->string('image')->nullable()->after('name');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('google_products', function (Blueprint $table) {
            $table->dropColumn('title');
            $table->dropColumn('image');
        });
    }
};
