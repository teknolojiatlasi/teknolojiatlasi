<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('contact_settings', function (Blueprint $table) {
            $table->id();

            $table->string('contact_company_name')->nullable();
            $table->string('contact_address')->nullable();
            $table->string('contact_city')->nullable();
            $table->string('contact_district')->nullable();
            $table->string('contact_phone')->nullable();
            $table->string('contact_email')->nullable();

            $table->string('contact_map_provider')->default('osm');
            $table->decimal('contact_lat', 10, 7)->nullable();
            $table->decimal('contact_lng', 10, 7)->nullable();

            $table->unsignedBigInteger('contact_updated_by_id')->nullable()->index();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('contact_settings');
    }
};

