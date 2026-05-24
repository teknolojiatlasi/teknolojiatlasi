<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('cv_educations', function (Blueprint $table) {
            $table->id();

            $table->foreignId('cv_id')
                ->constrained('cvs')
                ->cascadeOnDelete();

            $table->string('school'); // Üniversite / Enstitü
            $table->string('degree'); // Lisans / Yüksek Lisans
            $table->string('year');   // 2019
            $table->text('description')->nullable();

            $table->unsignedInteger('order')->default(0);

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('cv_educations');
    }
};
