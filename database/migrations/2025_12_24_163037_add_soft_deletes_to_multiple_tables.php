<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Add deleted_at to banners
        Schema::table('banners', function (Blueprint $table) {
            $table->softDeletes();
        });

        // Add deleted_at to quotes
        Schema::table('quotes', function (Blueprint $table) {
            $table->softDeletes();
        });

        // Add deleted_at and timestamps to authors
        Schema::table('authors', function (Blueprint $table) {
            if (!Schema::hasColumn('authors', 'created_at')) {
                $table->timestamps();
            }
            $table->softDeletes();
        });

        // Add deleted_at to articles
        Schema::table('articles', function (Blueprint $table) {
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('banners', function (Blueprint $table) {
            $table->dropSoftDeletes();
        });

        Schema::table('quotes', function (Blueprint $table) {
            $table->dropSoftDeletes();
        });

        Schema::table('authors', function (Blueprint $table) {
            $table->dropSoftDeletes();
            if (Schema::hasColumn('authors', 'created_at')) {
                $table->dropTimestamps();
            }
        });

        Schema::table('articles', function (Blueprint $table) {
            $table->dropSoftDeletes();
        });
    }
};
