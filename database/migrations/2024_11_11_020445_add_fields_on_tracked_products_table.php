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
        Schema::table('tracked_products', function (Blueprint $table) {
            $table->boolean('deal')->after('folder_id')->default(false);
            $table->float('saved')->after('deal')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('tracked_products', function (Blueprint $table) {
            $table->dropColumn('deal');
            $table->dropColumn('saved');
        });
    }
};
