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
        Schema::table('abstracts', function (Blueprint $table) {
            $table->integer('reviewer_id')->nullable()->after('abstract_file');
            $table->decimal('score', 5, 2)->default(0)->after('reviewer_id');
            $table->text('comments')->nullable()->after('score');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('abstracts', function (Blueprint $table) {
            //
        });
    }
};
