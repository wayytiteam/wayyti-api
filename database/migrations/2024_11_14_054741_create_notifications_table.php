<?php

use App\Models\BadgeUser;
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
        Schema::create('notifications', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('user_id')->constrained()->onDelete('cascade');
            $table->longText('message');
            $table->foreignUuid('monthly_draw_winner_id')->nullable()->constrained()->onDelete('cascade');
            $table->foreignUuid('google_product_id')->nullable()->constrained()->onDelete('cascade');
            $table->foreignIdFor(BadgeUser::class)->nullable()->constrained()->onDelete('cascade');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('notifications');
    }
};
