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
            $table->string('bdOrderId')->nullable()->after('payment_order_id');
            $table->decimal('surcharge', 10, 2)->nullable()->after('payment_mode_type');
            $table->decimal('paid_amount', 10, 2)->nullable()->after('payment_amount');
            $table->decimal('charge_amount', 10, 2)->nullable()->after('paid_amount');
            $table->string('payment_category')->nullable()->after('charge_amount');
            $table->string('transaction_error_type')->nullable()->after('transaction_id');
            $table->string('txn_process_type')->nullable()->after('transaction_error_type');
            $table->string('transaction_error_code')->nullable()->after('txn_process_type');
            $table->string('transaction_error_desc')->nullable()->after('transaction_error_code');

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
