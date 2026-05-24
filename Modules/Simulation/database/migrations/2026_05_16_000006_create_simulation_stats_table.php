<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('simulation_stats', function (Blueprint $table) {
            $table->id();
            $table->foreignId('simulation_id')->unique()->constrained('simulations')->cascadeOnDelete();
            $table->unsignedBigInteger('views_count')->default(0);
            $table->unsignedBigInteger('runs_count')->default(0);
            $table->unsignedBigInteger('favorites_count')->default(0);
            $table->unsignedBigInteger('shares_count')->default(0);
            $table->timestamp('last_viewed_at')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('simulation_stats');
    }
};
