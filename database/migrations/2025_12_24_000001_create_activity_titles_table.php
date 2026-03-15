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
            $table->string('name');                          // Tên danh hiệu: "Tác giả tập sự"
            $table->string('icon')->nullable();              // Emoji hoặc icon class
            $table->string('color')->default('#6B7280');     // Màu sắc (hex)
            $table->integer('min_posts')->default(0);        // Số bài viết tối thiểu
            $table->integer('min_books')->default(0);        // Số sách đề xuất tối thiểu
            $table->integer('priority')->default(1);         // Thứ tự ưu tiên (cao = ưu tiên hơn)
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
