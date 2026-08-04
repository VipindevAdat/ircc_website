<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ParticipantPayment extends Model
{
    use HasFactory;

    protected $fillable = [
        'registration_id',
        'payment_id',
        'participant_id',
        'participant_name',
        'payment_status_id',
    ];

    public function registration()
    {
        return $this->belongsTo(Registration::class);
    }

    public function payment()
    {
        return $this->belongsTo(Payment::class);
    }

    public function participant()
    {
        return $this->belongsTo(Participant::class);
    }

    public function paymentStatus(): BelongsTo
    {
        return $this->belongsTo(PaymentStatus::class, 'payment_status_id');
    }

}
