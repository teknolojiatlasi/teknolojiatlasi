<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Storage;

return new class extends Migration {
    public function up(): void
    {
        if (! Schema::hasTable('media')) {
            return;
        }

        $paths = collect();

        if (Schema::hasTable('blogs')) {
            DB::table('blogs')
                ->whereNotNull('cover_image')
                ->where('cover_image', '!=', '')
                ->pluck('cover_image')
                ->each(fn ($path) => $paths->push($path));
        }

        if (Schema::hasTable('blog_images')) {
            DB::table('blog_images')
                ->whereNotNull('image_path')
                ->where('image_path', '!=', '')
                ->pluck('image_path')
                ->each(fn ($path) => $paths->push($path));
        }

        $paths->unique()->values()->each(function (string $path): void {
            $path = ltrim($path, '/');

            if (DB::table('media')->where('file_path', $path)->exists()) {
                return;
            }

            DB::table('media')->insert([
                'file_name' => basename($path),
                'file_path' => $path,
                'mime_type' => Storage::disk('public')->exists($path) ? Storage::disk('public')->mimeType($path) : null,
                'size' => Storage::disk('public')->exists($path) ? Storage::disk('public')->size($path) : 0,
                'user_id' => null,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        });

        if (! Schema::hasTable('blogs') || ! Schema::hasColumn('blogs', 'cover_media_id')) {
            return;
        }

        DB::table('blogs')
            ->whereNotNull('cover_image')
            ->whereNull('cover_media_id')
            ->get(['id', 'cover_image'])
            ->each(function ($blog): void {
                $mediaId = DB::table('media')
                    ->where('file_path', ltrim((string) $blog->cover_image, '/'))
                    ->value('id');

                if ($mediaId) {
                    DB::table('blogs')
                        ->where('id', $blog->id)
                        ->update(['cover_media_id' => $mediaId]);
                }
            });
    }

    public function down(): void
    {
        if (Schema::hasTable('blogs') && Schema::hasColumn('blogs', 'cover_media_id')) {
            DB::table('blogs')->update(['cover_media_id' => null]);
        }
    }
};
