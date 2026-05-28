<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('media', function (Blueprint $table) {
            $table->string('collection')->nullable()->after('file_path')->index();
        });

        DB::table('media')
            ->where('file_path', 'like', 'blogs/covers/%')
            ->update(['collection' => 'Blog Kapaklari']);

        DB::table('media')
            ->where('file_path', 'like', 'blogs/images/%')
            ->update(['collection' => 'Blog Galerisi']);

        DB::table('media')
            ->where('file_path', 'like', 'uploads/covers/%')
            ->update(['collection' => 'Kapak Resimleri']);
    }

    public function down(): void
    {
        Schema::table('media', function (Blueprint $table) {
            $table->dropColumn('collection');
        });
    }
};
