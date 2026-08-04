<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class CompetitionAbstract extends Model
{
    use HasFactory;
    use SoftDeletes;
    protected $fillable = ['abstract_number', 'registration_id', 'registration_number', 'prefix', 'abstract_index', 'category', 'title', 'authors', 'correspondent_name', 'correspondent_email', 'affiliation', 'abstract', 'reference','tag', 'abstract_file', 'status'];
    
    public function registration(){
        return $this->belongsTo(CompetitionRegistration::class,'registration_id','id');
    }

}
