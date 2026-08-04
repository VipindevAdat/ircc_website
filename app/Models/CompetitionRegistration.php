<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class CompetitionRegistration extends Model
{
    use HasFactory;
    use SoftDeletes;
    protected $fillable = ['school_name', 'first_name', 'last_name', 'teacher_name', 'designation', 'country', 'phone', 'email', 'category','category_id','participation', 'registration_number', 'members','no_accompany', 'comment', 'reason', 'other', 'status','payment_status','register_tag','mark',];

    public function abstract(){
        return $this->belongsTo(CompetitionAbstract::class,'id','registration_id');
    }
    public function teams(){
        return $this->hasMany(CompetitionRegistrationTeam::class,'registration_id','id');
    }
    public function accompany(){
        return $this->hasMany(AccompanyPerson::class,'registration_id','id');
    }
    public function accompany_payment(){
        return $this->belongsTo(AccompanyPaymentDetail::class,'id','registration_id');
    }
    public function competition_category(){
        return $this->belongsTo(Category::class,'category_id','id');
    }
}

