<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AccompanyPaymentDetail extends Model
{
    use HasFactory;
    protected $fillable = ['registration_id','payment_method','payment_reference', 'payment_date','payment_amount'];
}
