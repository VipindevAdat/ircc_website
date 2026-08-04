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
        Schema::table('categories', function (Blueprint $table) {
            $table->tinyInteger('type')->nullable()->default(1)->after('last_number')->comment('1-no Agelimit  2- grade');
            $table->integer('grade_from')->nullable()->after('type');
            $table->integer('grade_to')->nullable()->after('grade_from');
            $table->tinyInteger('status')->nullable()->after('grade_to');
            $table->integer('sort_order')->nullable()->after('status');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('categories', function (Blueprint $table) {
            //
        });
    }
};
