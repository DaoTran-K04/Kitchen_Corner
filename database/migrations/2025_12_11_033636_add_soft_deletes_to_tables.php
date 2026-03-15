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
        // Thêm soft deletes cho bảng books
        Schema::table('books', function (Blueprint $table) {
            $table->softDeletes();
        });

        // Thêm soft deletes cho bảng categories
        Schema::table('categories', function (Blueprint $table) {
            $table->softDeletes();
        });

        // Thêm soft deletes cho bảng users
        Schema::table('users', function (Blueprint $table) {
            $table->softDeletes();
        });

        // Thêm soft deletes cho bảng posts (nếu chưa có)
        if (!Schema::hasColumn('posts', 'deleted_at')) {
            Schema::table('posts', function (Blueprint $table) {
                $table->softDeletes();
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('books', function (Blueprint $table) {
            $table->dropSoftDeletes();
        });

        Schema::table('categories', function (Blueprint $table) {
            $table->dropSoftDeletes();
        });

        Schema::table('users', function (Blueprint $table) {
            $table->dropSoftDeletes();
        });

        if (Schema::hasColumn('posts', 'deleted_at')) {
            Schema::table('posts', function (Blueprint $table) {
                $table->dropSoftDeletes();
            });
        }
    }
};
