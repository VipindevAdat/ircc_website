<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Setting extends Model
{
    use HasFactory;
    protected $fillable = ['field_type', 'dataset', 'data_source', 'code', 'title', 'placeholder', 'value', 'status'];
}
