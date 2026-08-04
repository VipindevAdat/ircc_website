<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Plank\Mediable\Mediable;
class AbstractBook extends Model
{
    use HasFactory;
    use Mediable;
    protected $fillable = ['title', 'date_from', 'date_to', 'location', 'description', 'sort_order', 'status'];
}
