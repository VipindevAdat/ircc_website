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
           $table->string('payment_order_id')->after('id');
            $table->string('payment_type')->after('payment_order_id')->nullable();
            $table->string('transaction_id')->after('registration_id')->nullable();
            $table->string('payment_mode_type')->after('transaction_id')->nullable();
            $table->decimal('payment_amount', 10, 2)->nullable()->after('payment_mode');
            $table->string('currency')->after('payment_amount');
            $table->string('customer_name')->after('currency')->nullable();
            $table->string('mobile')->after('customer_name')->nullable();
            $table->string('email')->after('mobile')->nullable();
            $table->string('payment_status')->after('email')->nullable();
            $table->string('payment_status_code')->after('payment_status')->nullable();
            $table->string('payment_status_name')->after('payment_status_code')->nullable();
            $table->string('bank_reference_number')->after('payment_status_name')->nullable();
            $table->string('transaction_date')->after('bank_reference_number')->nullable();
            $table->string('success_message')->after('transaction_date')->nullable();
            $table->string('error_code')->after('success_message')->nullable();
            $table->string('error_description')->after('error_code')->nullable();

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
