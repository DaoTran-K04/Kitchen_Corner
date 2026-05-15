<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
{
    Schema::create('articles', function (Blueprint $table) {
        $table->id();
        $table->string('title');            // Tiêu đề bài viết
        $table->string('slug')->unique();   // Đường dẫn thân thiện (SEO)
        $table->string('thumbnail')->nullable(); // Ảnh bìa
        $table->string('tag')->nullable();  // Nhãn (Ví dụ: Mẹo Đọc, Cảm Hứng)
        $table->text('excerpt')->nullable();// Mô tả ngắn
        $table->longText('content');        // Nội dung chi tiết
        $table->boolean('is_featured')->default(false); // Đánh dấu bài Tiêu Điểm (To nhất)
        $table->unsignedBigInteger('user_id'); // Người viết
        $table->timestamps(); // created_at, updated_at

        // Khóa ngoại liên kết với bảng users
        $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
    });
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('articles');
    }
};
