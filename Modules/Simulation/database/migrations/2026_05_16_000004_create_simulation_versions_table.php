<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('simulation_versions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('simulation_id')->constrained('simulations')->cascadeOnDelete();
            $table->unsignedInteger('version');
            $table->longText('html_code')->nullable();
            $table->longText('css_code')->nullable();
            $table->longText('js_code')->nullable();
            $table->string('change_note')->nullable();
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();

            $table->unique(['simulation_id', 'version']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('simulation_versions');
    }
};
