<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PaymentRefund extends Model
{
    use HasFactory;

    protected $fillable = [
        'registration_id',
        'refund_amount',
        'participants',
        'refund_reference',
        'request_date',
        'refund_date',
        'status',
    ];

    /**
     * Get the registration associated with this payment.
     */
    public function registration()
    {
        return $this->belongsTo(Registration::class, 'id');
    }

}
