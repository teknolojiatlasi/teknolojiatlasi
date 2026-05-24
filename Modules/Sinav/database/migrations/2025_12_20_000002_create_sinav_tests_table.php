<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('sinav_tests', function (Blueprint $table) {
            $table->id();
            $table->foreignId('topic_id')->constrained('sinav_topics')->cascadeOnDelete();
            $table->string('title');
            $table->unsignedInteger('duration_minutes')->default(20);
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->index('topic_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('sinav_tests');
    }
};

