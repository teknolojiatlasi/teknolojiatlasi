<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCvsTable extends Migration
{
    public function up(): void
    {
        Schema::create('cvs', function (Blueprint $table) {
            $table->id();

            // Kişisel bilgiler
            $table->string('full_name');
            $table->string('title')->nullable();
            $table->string('email');
            $table->string('phone')->nullable();
            $table->string('address')->nullable();

            // Hakkında
            $table->text('about')->nullable();

            // Medya & şablon
            $table->string('photo')->nullable();
            $table->string('template')->default('modern');

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('cvs');
    }
}
