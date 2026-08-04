<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Plank\Mediable\Mediable;
class Gallery extends Model
{
    use HasFactory;
    use Mediable;
    protected $fillable = ['name','gallery_type','is_home','video_link','sort_order','status'];
}
