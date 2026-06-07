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
        Schema::create('supabase_ingredients', function (Blueprint $table) {
            $table->id();
            $table->string('vietnamese_name', 100)->unique();
            $table->string('english_name', 100)->nullable();
            $table->unsignedSmallInteger('calories')->default(0);
            $table->decimal('protein', 6, 2)->unsigned()->default(0);
            $table->decimal('carbs', 6, 2)->unsigned()->default(0);
            $table->decimal('fat', 6, 2)->unsigned()->default(0);
            $table->string('image_url', 200)->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('supabase_ingredients');
    }
};
