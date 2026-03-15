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
        Schema::create('comment_reports', function (Blueprint $table) {
            $table->id();
            $table->foreignId('comment_id')->constrained()->cascadeOnDelete();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete(); // Người báo cáo
            $table->enum('reason', ['spam', 'offensive', 'harassment', 'inappropriate', 'other'])->default('other');
            $table->text('description')->nullable(); // Mô tả chi tiết
            $table->enum('status', ['pending', 'approved', 'rejected'])->default('pending');
            $table->text('admin_note')->nullable(); // Ghi chú của admin
            $table->foreignId('resolved_by')->nullable()->constrained('users')->nullOnDelete(); // Admin xử lý
            $table->timestamp('resolved_at')->nullable();
            $table->timestamps();

            // Một user chỉ có thể report một comment một lần
            $table->unique(['comment_id', 'user_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('comment_reports');
    }
};
