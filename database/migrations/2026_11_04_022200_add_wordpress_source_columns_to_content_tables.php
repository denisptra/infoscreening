<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('articles', function (Blueprint $table) {
            $table->unsignedBigInteger('wordpress_source_id')->nullable()->unique();
            $table->string('legacy_author_name')->nullable();
        });

        Schema::table('events', function (Blueprint $table) {
            $table->unsignedBigInteger('wordpress_source_id')->nullable()->unique();
        });
    }

    public function down(): void
    {
        Schema::table('events', function (Blueprint $table) {
            $table->dropUnique(['wordpress_source_id']);
            $table->dropColumn('wordpress_source_id');
        });

        Schema::table('articles', function (Blueprint $table) {
            $table->dropUnique(['wordpress_source_id']);
            $table->dropColumn(['wordpress_source_id', 'legacy_author_name']);
        });
    }
};
