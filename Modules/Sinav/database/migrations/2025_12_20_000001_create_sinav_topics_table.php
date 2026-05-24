<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('sinav_topics', function (Blueprint $table) {
            $table->id();
            $table->foreignId('lesson_id')->constrained('sinav_lessons')->cascadeOnDelete();
            $table->foreignId('parent_id')->nullable()->constrained('sinav_topics')->cascadeOnDelete();
            $table->string('title');
            $table->text('description')->nullable();
            $table->unsignedInteger('sort_order')->default(0);
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->index(['lesson_id', 'parent_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('sinav_topics');
    }
};

