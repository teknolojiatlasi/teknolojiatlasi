<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasColumn('blog_comments', 'social_comment_id')) {
            Schema::table('blog_comments', function (Blueprint $table) {
                $table->foreignId('social_comment_id')
                    ->nullable()
                    ->after('parent_id');
            });
        }

        if (! Schema::hasColumn('sosial_posts', 'blog_id')) {
            Schema::table('sosial_posts', function (Blueprint $table) {
                $table->foreignId('blog_id')
                    ->nullable()
                    ->after('user_id');
            });
        }

        if (! $this->hasIndex('blog_comments', 'blog_comments_social_comment_id_unique')) {
            Schema::table('blog_comments', function (Blueprint $table) {
                $table->unique('social_comment_id', 'blog_comments_social_comment_id_unique');
            });
        }

        if (! $this->hasForeignKey('blog_comments', 'blog_comments_social_comment_id_foreign')) {
            Schema::table('blog_comments', function (Blueprint $table) {
                $table->foreign('social_comment_id', 'blog_comments_social_comment_id_foreign')
                    ->references('id')
                    ->on('sosial_comments')
                    ->nullOnDelete();
            });
        }

        if (! $this->hasIndex('sosial_posts', 'sosial_posts_blog_id_unique')) {
            Schema::table('sosial_posts', function (Blueprint $table) {
                $table->unique('blog_id', 'sosial_posts_blog_id_unique');
            });
        }

        if (! $this->hasForeignKey('sosial_posts', 'sosial_posts_blog_id_foreign')) {
            Schema::table('sosial_posts', function (Blueprint $table) {
                $table->foreign('blog_id', 'sosial_posts_blog_id_foreign')
                    ->references('id')
                    ->on('blogs')
                    ->cascadeOnDelete();
            });
        }

        Schema::table('sosial_comments', function (Blueprint $table) {
            $table->foreignId('user_id')
                ->nullable()
                ->change();
        });

        if (! Schema::hasColumn('sosial_comments', 'author_name')) {
            Schema::table('sosial_comments', function (Blueprint $table) {
                $table->string('author_name', 80)
                    ->nullable()
                    ->after('parent_id');
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasColumn('sosial_comments', 'author_name')) {
            Schema::table('sosial_comments', function (Blueprint $table) {
                $table->dropColumn('author_name');
            });
        }

        if ($this->hasForeignKey('sosial_posts', 'sosial_posts_blog_id_foreign')) {
            Schema::table('sosial_posts', function (Blueprint $table) {
                $table->dropForeign('sosial_posts_blog_id_foreign');
            });
        }

        if ($this->hasIndex('sosial_posts', 'sosial_posts_blog_id_unique')) {
            Schema::table('sosial_posts', function (Blueprint $table) {
                $table->dropUnique('sosial_posts_blog_id_unique');
            });
        }

        if (Schema::hasColumn('sosial_posts', 'blog_id')) {
            Schema::table('sosial_posts', function (Blueprint $table) {
                $table->dropColumn('blog_id');
            });
        }

        if ($this->hasForeignKey('blog_comments', 'blog_comments_social_comment_id_foreign')) {
            Schema::table('blog_comments', function (Blueprint $table) {
                $table->dropForeign('blog_comments_social_comment_id_foreign');
            });
        }

        if ($this->hasIndex('blog_comments', 'blog_comments_social_comment_id_unique')) {
            Schema::table('blog_comments', function (Blueprint $table) {
                $table->dropUnique('blog_comments_social_comment_id_unique');
            });
        }

        if (Schema::hasColumn('blog_comments', 'social_comment_id')) {
            Schema::table('blog_comments', function (Blueprint $table) {
                $table->dropColumn('social_comment_id');
            });
        }

        Schema::table('sosial_comments', function (Blueprint $table) {
            $table->foreignId('user_id')
                ->nullable(false)
                ->change();
        });
    }

    private function hasIndex(string $table, string $indexName): bool
    {
        return DB::table('information_schema.statistics')
            ->where('table_schema', DB::getDatabaseName())
            ->where('table_name', $table)
            ->where('index_name', $indexName)
            ->exists();
    }

    private function hasForeignKey(string $table, string $constraintName): bool
    {
        return DB::table('information_schema.table_constraints')
            ->where('table_schema', DB::getDatabaseName())
            ->where('table_name', $table)
            ->where('constraint_name', $constraintName)
            ->where('constraint_type', 'FOREIGN KEY')
            ->exists();
    }
};
