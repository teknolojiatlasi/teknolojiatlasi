<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('sinav_attempt_answers', function (Blueprint $table) {
            $table->id();
            $table->foreignId('attempt_id')->constrained('sinav_attempts')->cascadeOnDelete();
            $table->foreignId('question_id')->constrained('sinav_questions')->cascadeOnDelete();
            $table->char('selected_option', 1)->nullable();
            $table->boolean('is_correct')->default(false);
            $table->timestamp('created_at')->nullable();

            $table->unique(['attempt_id', 'question_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('sinav_attempt_answers');
    }
};

