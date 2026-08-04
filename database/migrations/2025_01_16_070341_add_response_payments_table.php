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
            $table->string('discount')->nullable()->after('charge_amount');
            $table->string('bank_id')->nullable()->after('bank_reference_number');
            $table->string('payment_ru')->nullable()->after('bank_id');
            $table->string('payment_eci')->nullable()->after('payment_ru');
            $table->text('payment_card_details')->nullable()->after('payment_eci');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('payments', function (Blueprint $table) {
            //
        });
    }
};
