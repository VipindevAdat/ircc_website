<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class ReviewStatus extends Model
{
    use HasFactory;

    protected $table = 'review_status';

    protected $fillable = ['status'];

    public function abstractReviewers(): HasMany
    {
        return $this->hasMany(AbstractReviewer::class, 'review_status');
    }
    

}
