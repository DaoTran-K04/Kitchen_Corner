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
    Schema::create('banners', function (Blueprint $table) {
        $table->id();
        $table->string('title');        // Tiêu đề sách
        $table->string('tag')->nullable(); // Tag (vd: Sách của tháng)
        $table->text('description')->nullable(); // Mô tả ngắn/Trích dẫn
        $table->string('image');        // Link ảnh bìa
        $table->string('rating')->nullable(); // Đánh giá (vd: 4.9/5.0)
        $table->string('link')->nullable();   // Link khi bấm vào (vd: link tới sách)
        $table->boolean('is_active')->default(true); // Ẩn/Hiện
        $table->integer('order')->default(0); // Thứ tự hiển thị
        $table->timestamps();
    });
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('banners');
    }
};
