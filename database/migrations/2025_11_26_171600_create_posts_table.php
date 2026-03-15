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
        Schema::create('posts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('book_id')->nullable()->constrained()->onDelete('set null');

            $table->string('title');
            $table->string('slug')->unique();
            $table->string('thumbnail')->nullable();
            // Tóm tắt nội dung bài viết
            $table->text('excerpt')->nullable();
            $table->longText('content');
            $table->decimal('rating', 3, 1)->nullable();

            // Trạng thái bài viết (bản nháp, đang chờ, đã xuất bản, bị từ chối, ẩn)
            $table->enum('status', ['draft', 'pending', 'published', 'rejected', 'hidden'])->default('draft');
            $table->timestamp('published_at')->nullable();

            $table->integer('view_count')->default(0);
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('posts');
    }
};
