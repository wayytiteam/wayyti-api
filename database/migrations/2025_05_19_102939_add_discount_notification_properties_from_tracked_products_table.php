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
            $table->float('discount_notification_value')->default(0);
            $table->string('discount_notification_type')->default('percentage');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('tracked_products', function (Blueprint $table) {
            $table->dropColumn('discount_notification_value');
            $table->dropColumn('discount_notification_type');
        });
    }
};
