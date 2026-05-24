<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('sosial_post_tag', function (Blueprint $table) {
            $table->id();
            $table->foreignId('post_id')->constrained('sosial_posts')->cascadeOnDelete();
            $table->foreignId('tag_id')->constrained('sosial_tags')->cascadeOnDelete();
            $table->timestamps();

            $table->unique(['post_id', 'tag_id']);
            $table->index(['tag_id', 'post_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('sosial_post_tag');
    }
};

