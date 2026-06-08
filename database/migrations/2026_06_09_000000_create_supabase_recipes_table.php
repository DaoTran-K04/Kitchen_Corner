<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    protected $connection = 'supabase';

    public function up(): void
    {
        if (!Schema::connection('supabase')->hasTable('supabase_recipes')) {
            Schema::connection('supabase')->create('supabase_recipes', function (Blueprint $table) {
                $table->id();
                $table->string('meal_id', 50)->unique();
                $table->string('name', 150);
                $table->string('category', 50)->nullable();
                $table->string('area', 50)->nullable();
                $table->text('instructions')->nullable();
                $table->string('image_url', 150)->nullable();
                $table->string('youtube_url', 150)->nullable();
                $table->json('ingredients_json')->nullable();
                $table->timestamps();
            });
        }
    }

    public function down(): void
    {
        Schema::connection('supabase')->dropIfExists('supabase_recipes');
    }
};
