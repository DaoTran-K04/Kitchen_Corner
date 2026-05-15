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
        Schema::table('user_badges', function (Blueprint $table) {
            $table->unsignedInteger('display_order')->default(0)->after('expires_at')
                ->comment('Thứ tự hiển thị - số nhỏ hơn hiển thị trước');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('user_badges', function (Blueprint $table) {
            $table->dropColumn('display_order');
        });
    }
};
