<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('sosial_comments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('post_id')->constrained('sosial_posts')->cascadeOnDelete();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('parent_id')->nullable()->constrained('sosial_comments')->cascadeOnDelete();
            $table->text('body');
            $table->timestamps();

            $table->index(['post_id', 'created_at']);
            $table->index(['parent_id', 'created_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('sosial_comments');
    }
};

