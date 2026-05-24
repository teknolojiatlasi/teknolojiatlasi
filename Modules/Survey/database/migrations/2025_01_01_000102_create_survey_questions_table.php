<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('survey_questions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('survey_id')->constrained('surveys')->cascadeOnDelete();
            $table->string('question');
            $table->string('type', 50);
            $table->boolean('is_required')->default(false);
            $table->unsignedInteger('position')->default(0);
            $table->text('help_text')->nullable();
            $table->text('explanation')->nullable();
            $table->json('config')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->index(['survey_id', 'type']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('survey_questions');
    }
};
