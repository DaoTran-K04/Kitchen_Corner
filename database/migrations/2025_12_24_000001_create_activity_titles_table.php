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
        Schema::create('activity_titles', function (Blueprint $table) {
            $table->id();
            $table->string('name', 50);                          // Tên danh hiệu: "Tác giả tập sự"
            $table->string('icon', 50)->nullable();              // Emoji hoặc icon class
            $table->string('color', 20)->default('#6B7280');     // Màu sắc (hex)
            $table->unsignedSmallInteger('min_posts')->default(0);        // Số bài viết tối thiểu
            $table->unsignedSmallInteger('min_recipes')->default(0);        // Số công thức tối thiểu
            $table->unsignedTinyInteger('priority')->default(1);         // Thứ tự ưu tiên (cao = ưu tiên hơn)
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('activity_titles');
    }
};
