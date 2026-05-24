<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCvExperiencesTable extends Migration
{
    public function up(): void
    {
        Schema::create('cv_experiences', function (Blueprint $table) {
            $table->id();

            $table->foreignId('cv_id')
                ->constrained('cvs')
                ->cascadeOnDelete();

            $table->string('company');
            $table->string('position');
            $table->string('start_date');
            $table->string('end_date')->nullable();
            $table->text('description')->nullable();

            $table->unsignedInteger('sort_order')->default(0);

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('cv_experiences');
    }
}
