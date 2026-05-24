<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('sinav_questions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('topic_id')->constrained('sinav_topics')->cascadeOnDelete();
            $table->foreignId('test_id')->constrained('sinav_tests')->cascadeOnDelete();

            $table->text('question_text');
            $table->text('option_a');
            $table->text('option_b');
            $table->text('option_c');
            $table->text('option_d');
            $table->text('option_e');
            $table->char('correct_option', 1);
            $table->text('explanation')->nullable();
            $table->unsignedInteger('sort_order')->default(0);
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->index(['test_id', 'sort_order']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('sinav_questions');
    }
};

