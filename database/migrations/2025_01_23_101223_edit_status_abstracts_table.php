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
            $table->tinyInteger('status')->default(1)->after('comments');
            $table->tinyInteger('edit_status')->default(0)->after('status');
            $table->dateTime('edited_at')->nullable()->after('edit_status');
            $table->string('pid')->unique(false)->nullable()->change();
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
