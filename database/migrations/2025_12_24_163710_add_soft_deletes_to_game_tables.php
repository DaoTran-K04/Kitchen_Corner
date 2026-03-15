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
        // Add deleted_at to badges
        Schema::table('badges', function (Blueprint $table) {
            $table->softDeletes();
        });

        // Add deleted_at to challenges
        Schema::table('challenges', function (Blueprint $table) {
            $table->softDeletes();
        });

        // Add deleted_at to avatar_frames
        Schema::table('avatar_frames', function (Blueprint $table) {
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('badges', function (Blueprint $table) {
            $table->dropSoftDeletes();
        });

        Schema::table('challenges', function (Blueprint $table) {
            $table->dropSoftDeletes();
        });

        Schema::table('avatar_frames', function (Blueprint $table) {
            $table->dropSoftDeletes();
        });
    }
};
