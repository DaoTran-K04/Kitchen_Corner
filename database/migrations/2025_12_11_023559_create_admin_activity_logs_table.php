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
        Schema::create('admin_activity_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('admin_id')->constrained('users')->onDelete('cascade');
            $table->string('action'); // create, update, delete, login, logout, export, etc.
            $table->string('description'); // Mô tả hành động
            $table->string('model_type')->nullable(); // App\Models\Book, App\Models\Post, etc.
            $table->unsignedBigInteger('model_id')->nullable(); // ID của đối tượng bị tác động
            $table->json('old_values')->nullable(); // Giá trị cũ (cho update/delete)
            $table->json('new_values')->nullable(); // Giá trị mới (cho create/update)
            $table->string('ip_address', 45)->nullable();
            $table->text('user_agent')->nullable();
            $table->timestamps();

            $table->index(['admin_id', 'created_at']);
            $table->index('action');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('admin_activity_logs');
    }
};
