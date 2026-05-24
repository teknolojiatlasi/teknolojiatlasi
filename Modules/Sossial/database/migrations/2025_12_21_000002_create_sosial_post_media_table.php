<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('sosial_post_media', function (Blueprint $table) {
            $table->id();
            $table->foreignId('post_id')->constrained('sosial_posts')->cascadeOnDelete();
            $table->string('type', 16);
            $table->string('path')->nullable();
            $table->string('url', 2048)->nullable();
            $table->unsignedInteger('sort')->default(0);
            $table->timestamps();

            $table->index(['post_id', 'sort']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('sosial_post_media');
    }
};

