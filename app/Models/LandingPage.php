<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LandingPage extends Model
{
    use HasFactory;

    protected $fillable = ['year', 'title', 'is_active', 'header_logo', 'footer_logo', 'banner_image', 'video', 'video_thumbnail'];

    protected $casts = [
        'year' => 'integer',
        'is_active' => 'boolean',
    ];
}