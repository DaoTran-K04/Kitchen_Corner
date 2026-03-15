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
        Schema::create('books', function (Blueprint $table) {
            $table->id();
            $table->foreignId('created_by_user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('title');
            $table->string('slug')->unique();
            $table->string('author_name');
            $table->string('publisher')->nullable();
            $table->integer('published_year')->nullable();
            $table->text('description')->nullable();
            $table->string('cover_image')->nullable();
            $table->integer('view_count')->default(0);
            $table->decimal('avg_rating', 3, 1)->default(0.0);
            // Admin duyệt sách trước khi hiển thị
            $table->boolean('is_approved')->default(false);
            $table->timestamps();
        });

        // Bảng 
        Schema::create('book_category', function (Blueprint $table) {
            $table->foreignId('book_id')->constrained()->cascadeOnDelete();
            $table->foreignId('category_id')->constrained()->cascadeOnDelete();
            $table->primary(['book_id', 'category_id']); // Khóa chính kép để tránh trùng lặp
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('books');
    }
};
