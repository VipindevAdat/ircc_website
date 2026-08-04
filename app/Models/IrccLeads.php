<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class IrccLeads extends Model
{
    use HasFactory;
    protected $fillable = ['name', 'email','added_date','phone','tag'];
}
