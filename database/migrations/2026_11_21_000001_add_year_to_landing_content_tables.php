<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        foreach (['teams', 'reviews', 'galleries', 'award_graphics'] as $table) {
            if (Schema::hasTable($table) && !Schema::hasColumn($table, 'year')) {
                Schema::table($table, function (Blueprint $table) {
                    $table->unsignedSmallInteger('year')->nullable()->after('status');
                    $table->index('year');
                });
            }
        }
    }

    public function down(): void
    {
        foreach (['teams', 'reviews', 'galleries', 'award_graphics'] as $table) {
            if (Schema::hasTable($table) && Schema::hasColumn($table, 'year')) {
                Schema::table($table, function (Blueprint $table) {
                    $table->dropIndex(['year']);
                    $table->dropColumn('year');
                });
            }
        }
    }
};
