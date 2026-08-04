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
        Schema::table('payments', function (Blueprint $table) {
            $table->boolean('waiver_acknowledgment')->default(false)->after('comments')->nullable();
            $table->string('waiver_signature')->nullable()->after('waiver_acknowledgment');
            $table->date('waiver_date')->nullable()->after('waiver_signature');
            $table->boolean('waiver_under_12')->default(false)->after('waiver_date')->nullable();
            $table->boolean('waiver_12_or_older')->default(false)->after('waiver_under_12')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('payments', function (Blueprint $table) {
            $table->dropColumn([
                'waiver_acknowledgment',
                'waiver_signature',
                'waiver_date',
                'waiver_under_12',
                'waiver_12_or_older'
            ]);
        });
    }
};
