<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class AbstractReviewer extends Model
{
    use HasFactory;

    protected $table = 'abstract_reviewer';

    protected $fillable = [
        'abstract_id',
        'reviewer_id',
        'review_status',
        'score',
        'comments',
        'clarity',
        'relevance',
        'originality',
    ];

    public function abstract()
    {
        return $this->belongsTo(AbstractSubmission::class, 'abstract_id','id');
    }

    public function reviewer()
    {
        return $this->belongsTo(User::class, 'reviewer_id');
    }

    public function reviewStatus()
    {
        return $this->belongsTo(ReviewStatus::class, 'review_status');
    }
}
