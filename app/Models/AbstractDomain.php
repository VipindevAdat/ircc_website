<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AbstractDomain extends Model
{
    use HasFactory;

    protected $fillable =
        [
            'domain',
            'sort_order',
            'status',
        ];
}
