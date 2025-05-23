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
        Schema::table('points', function (Blueprint $table) {
            $table->dropColumn('type');
            $table->foreignUuid('attendance_id')->nullable()->constrained()->onDelete('cascade');
            $table->foreignUuid('tracked_product_id')->nullable()->constrained()->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('points', function (Blueprint $table) {
            $table->dropColumn('attendance_id');
            $table->dropColumn('tracked_product_id');
        });
    }
};
