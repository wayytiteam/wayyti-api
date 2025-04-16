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
        Schema::table('monthly_draw_winners', function (Blueprint $table) {
            $table->string('email')->nullable();
            $table->string('country')->nullable();
            $table->string('gift_card')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('monthly_draw_winners', function (Blueprint $table) {
            $table->dropColumn('email');
            $table->dropColumn('country');
            $table->dropColumn('gift_card');
        });
    }
};
