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
        Schema::create('ingredients', function (Blueprint $table) {
            $table->id();
            $table->string('name', 50)->unique();
            $table->string('slug', 60)->unique();
            $table->string('unit', 20)->comment('e.g., gram, ml, cup, piece');
            $table->unsignedSmallInteger('calories_per_unit')->default(0);
            $table->unsignedSmallInteger('protein_per_unit')->default(0);
            $table->unsignedSmallInteger('carbs_per_unit')->default(0);
            $table->unsignedSmallInteger('fat_per_unit')->default(0);
            $table->string('icon', 100)->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ingredients');
    }
};
