<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('articles', function (Blueprint $table) {
            $table->index(['status', 'published_at'], 'articles_status_published_at_index');
            $table->index(['is_featured', 'published_at'], 'articles_featured_published_at_index');
            $table->index('views', 'articles_views_index');
        });

        Schema::table('article_category', function (Blueprint $table) {
            $table->index('category_id', 'article_category_category_id_index');
        });

        Schema::table('article_tag', function (Blueprint $table) {
            $table->index('tag_id', 'article_tag_tag_id_index');
        });

        Schema::table('events', function (Blueprint $table) {
            $table->index(['is_published', 'starts_at'], 'events_published_starts_at_index');
            $table->index(['is_published', 'ends_at'], 'events_published_ends_at_index');
        });

        Schema::table('contributions', function (Blueprint $table) {
            $table->index(['status', 'created_at'], 'contributions_status_created_at_index');
        });

        Schema::table('media', function (Blueprint $table) {
            $table->index('created_at', 'media_created_at_index');
        });

        Schema::table('users', function (Blueprint $table) {
            $table->index('is_admin', 'users_is_admin_index');
        });
    }

    public function down(): void
    {
        Schema::table('articles', function (Blueprint $table) {
            $table->dropIndex('articles_status_published_at_index');
            $table->dropIndex('articles_featured_published_at_index');
            $table->dropIndex('articles_views_index');
        });

        Schema::table('article_category', function (Blueprint $table) {
            $table->dropIndex('article_category_category_id_index');
        });

        Schema::table('article_tag', function (Blueprint $table) {
            $table->dropIndex('article_tag_tag_id_index');
        });

        Schema::table('events', function (Blueprint $table) {
            $table->dropIndex('events_published_starts_at_index');
            $table->dropIndex('events_published_ends_at_index');
        });

        Schema::table('contributions', function (Blueprint $table) {
            $table->dropIndex('contributions_status_created_at_index');
        });

        Schema::table('media', function (Blueprint $table) {
            $table->dropIndex('media_created_at_index');
        });

        Schema::table('users', function (Blueprint $table) {
            $table->dropIndex('users_is_admin_index');
        });
    }
};
