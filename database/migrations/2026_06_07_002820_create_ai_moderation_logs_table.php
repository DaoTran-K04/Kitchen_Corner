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
        Schema::create('ai_moderation_logs', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id')->nullable();
            $table->string('ip_address')->nullable();
            $table->string('source')->default('rule_based'); // e.g. rule_based, gemini_safety
            $table->string('severity')->nullable(); // MEDIUM, HIGH
            $table->string('intent')->nullable(); // adult.explicit_violation, etc.
            $table->text('blocked_content')->nullable();
            $table->text('excerpt')->nullable(); // The specific matched word or short snippet
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ai_moderation_logs');
    }
};
