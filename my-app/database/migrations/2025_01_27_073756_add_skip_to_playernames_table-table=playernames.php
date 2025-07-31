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
        Schema::table('playernames', function (Blueprint $table) {
            //カラム追加
            $table->integer('skip')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('playernames', function (Blueprint $table) {
            //
            $table->dropColumn('skip');
        });
    }
};
