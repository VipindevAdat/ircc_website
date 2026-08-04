<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AccompanyPerson extends Model
{
    use HasFactory;
    protected $table = 'accompany_persons';
    protected $fillable = ['registration_id', 'name', 'email','payment_status','verify_status'];
}
