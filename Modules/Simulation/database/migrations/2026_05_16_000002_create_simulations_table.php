<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('simulations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('category_id')->nullable()->constrained('simulation_categories')->nullOnDelete();
            $table->string('topic_path')->nullable();
            $table->string('title');
            $table->string('slug')->unique();
            $table->text('excerpt')->nullable();
            $table->longText('content')->nullable();
            $table->string('content_type', 32)->default('html');
            $table->longText('html_code')->nullable();
            $table->longText('css_code')->nullable();
            $table->longText('js_code')->nullable();
            $table->string('video_url')->nullable();
            $table->string('video_source', 32)->nullable();
            $table->string('cover_image')->nullable();
            $table->string('status', 24)->default('draft');
            $table->timestamp('published_at')->nullable();
            $table->string('seo_title')->nullable();
            $table->text('seo_description')->nullable();
            $table->string('seo_keywords')->nullable();
            $table->boolean('is_featured')->default(false);
            $table->timestamps();

            $table->index(['category_id', 'status']);
            $table->index(['content_type', 'published_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('simulations');
    }
};
