<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('contact_messages', function (Blueprint $table) {
            $table->id();

            $table->string('contact_full_name');
            $table->string('contact_email')->index();
            $table->string('contact_subject');
            $table->text('contact_message');

            $table->boolean('contact_is_read')->default(false);
            $table->timestamp('contact_read_at')->nullable();

            $table->boolean('contact_is_replied')->default(false);
            $table->timestamp('contact_replied_at')->nullable();
            $table->string('contact_reply_subject')->nullable();
            $table->text('contact_reply_message')->nullable();
            $table->unsignedBigInteger('contact_replied_by_id')->nullable()->index();

            $table->json('contact_meta')->nullable();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('contact_messages');
    }
};

