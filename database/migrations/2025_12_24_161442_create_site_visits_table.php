<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('site_visits', function (Blueprint $table) {
            $table->id();
            $table->string('session_id')->unique();
            $table->string('ip_address', 45)->nullable();
            $table->timestamp('last_activity')->useCurrent();
            $table->timestamps();
        });

        // Bảng lưu thống kê tổng hợp
        Schema::create('site_statistics', function (Blueprint $table) {
            $table->id();
            $table->string('key')->unique();
            $table->bigInteger('value')->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('site_visits');
        Schema::dropIfExists('site_statistics');
    }
};
