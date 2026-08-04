<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'prefix',
        'last_number',
        'type',
        'grade_from',
        'grade_to',
        'status',
        'sort_order'
    ];

    public function categoryClasses()
    {
        return $this->hasMany(CategoryClass::class);
    }

}
