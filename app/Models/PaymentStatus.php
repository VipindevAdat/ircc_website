<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PaymentStatus extends Model
{
    use HasFactory;
    protected $fillable = ['status'];

    public function participantPayments(): HasMany
    {
        return $this->hasMany(ParticipantPayment::class, 'payment_status_id');
    }

}
