<?php

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
        Schema::create('user_answers', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('exam_session_id');
            $table->unsignedBigInteger('question_id');
            $table->char('selected_answer', 1); // A, B, C, D, or E
            $table->boolean('is_correct')->nullable();
            $table->timestamps();
            
            $table->foreign('exam_session_id')->references('id')->on('exam_sessions')->onDelete('cascade');
            $table->foreign('question_id')->references('id')->on('questions')->onDelete('cascade');
            $table->unique(['exam_session_id', 'question_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('user_answers');
    }
};