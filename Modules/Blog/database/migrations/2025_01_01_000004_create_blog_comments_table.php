<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('blog_comments', function (Blueprint $table) {
            $table->id();

            $table->foreignId('blog_id')
                ->constrained('blogs')
                ->cascadeOnDelete();

            $table->foreignId('parent_id')
                ->nullable()
                ->constrained('blog_comments')
                ->cascadeOnDelete();

            $table->string('author_name', 80);
            $table->text('body');

            $table->string('ip_address', 45)->nullable();
            $table->string('user_agent', 255)->nullable();

            $table->timestamps();

            $table->index(['blog_id', 'parent_id', 'created_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('blog_comments');
    }
};

