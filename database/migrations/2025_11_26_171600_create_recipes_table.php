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
        Schema::create('recipes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('category_id')->nullable()->constrained()->nullOnDelete();
            $table->string('title');
            $table->string('slug')->unique();
            $table->text('description')->nullable();
            $table->integer('cooking_time')->nullable()->comment('in minutes');
            $table->enum('difficulty', ['easy', 'medium', 'hard'])->default('medium');
            $table->integer('total_calories')->nullable();
            $table->integer('total_protein')->nullable()->comment('in grams');
            $table->integer('total_carbs')->nullable()->comment('in grams');
            $table->integer('total_fat')->nullable()->comment('in grams');
            $table->string('image')->nullable();
            $table->integer('view_count')->default(0);
            $table->boolean('is_featured')->default(false);
            $table->enum('status', ['draft', 'published', 'hidden', 'pending_delete'])->default('published');
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('recipes');
    }
};
