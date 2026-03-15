<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Thêm giá trị 'pending_delete' vào ENUM status của bảng posts
        DB::statement("ALTER TABLE `posts` MODIFY `status` ENUM('draft', 'pending', 'published', 'rejected', 'hidden', 'pending_delete') DEFAULT 'draft'");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Trước khi rollback, cần chuyển các bài viết có status 'pending_delete' sang status khác
        DB::table('posts')->where('status', 'pending_delete')->update(['status' => 'hidden']);
        
        // Xóa giá trị 'pending_delete' khỏi ENUM
        DB::statement("ALTER TABLE `posts` MODIFY `status` ENUM('draft', 'pending', 'published', 'rejected', 'hidden') DEFAULT 'draft'");
    }
};
