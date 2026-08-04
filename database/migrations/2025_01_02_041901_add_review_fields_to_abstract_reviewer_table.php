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
        Schema::table('abstract_reviewer', function (Blueprint $table) {
            $table->integer('clarity')->nullable()->after('review_status');
            $table->integer('relevance')->nullable()->after('clarity');
            $table->integer('originality')->nullable()->after('relevance');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('abstract_reviewer', function (Blueprint $table) {
            $table->dropColumn(['clarity', 'relevance', 'originality']);
        });
    }
};
