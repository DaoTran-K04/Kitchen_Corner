<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
{
    if (!Schema::hasColumn('categories', 'deleted_at')) {
        Schema::table('categories', function (Blueprint $table) {
            $table->softDeletes();
        });
    }
}

public function down()
{
    Schema::table('categories', function (Blueprint $table) {
        $table->dropSoftDeletes(); // Xóa cột nếu rollback
    });
}
};
