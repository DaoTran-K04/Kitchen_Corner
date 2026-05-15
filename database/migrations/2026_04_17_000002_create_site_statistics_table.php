<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('site_statistics')) {
            Schema::create('site_statistics', function (Blueprint $table) {
                $table->id();
                $table->string('key')->unique();
                $table->bigInteger('value')->default(0);
                $table->timestamps();
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('site_statistics');
    }
};
