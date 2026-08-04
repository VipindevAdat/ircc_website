<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CategoryClass extends Model
{
    use HasFactory;

    protected $fillable = [
        'category_id',
        'class',
        'status',
    ];

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function getClassAttribute($value)
    {
        return json_decode($value, true);
    }

}
