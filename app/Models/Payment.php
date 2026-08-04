<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Payment extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
      'payment_order_id', 'bdOrderId', 'payment_type', 'registration_id', 'transaction_id', 'transaction_error_type', 'txn_process_type', 'transaction_error_code', 'transaction_error_desc', 'payment_mode_type', 'surcharge', 'payment_reference', 'payment_date', 'participants', 'amount', 'registration_fee', 'participant_fee', 'payment_mode', 'payment_amount', 'paid_amount', 'charge_amount', 'discount', 'payment_category', 'currency', 'customer_name', 'mobile', 'email', 'payment_status', 'payment_status_code', 'payment_status_name', 'bank_reference_number', 'bank_id', 'payment_ru', 'payment_eci', 'payment_card_details', 'transaction_date','auth_status','error_description','comments', 'waiver_acknowledgment', 'waiver_signature', 'waiver_date', 'waiver_under_12', 'waiver_12_or_older', 'coupon_code', 'discount_amount', 'original_amount'
    ];

    /**
     * Get the registration associated with this payment.
     */
    public function registration()
    {
        return $this->belongsTo(Registration::class, 'registration_id');
    }
    public function status()
    {
        return $this->belongsTo(PaymentStatus::class, 'payment_status','id');
    }
}
