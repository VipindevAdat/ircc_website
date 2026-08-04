<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TeamCategory extends Model
{
    use HasFactory;
    protected $fillable = [
        'name', 'parent_id','sort_order', 'status'
    ];
    public function parent(){
        return $this->belongsTo(TeamCategory::class,'parent_id','id');
    }
    public function teams(){
        return $this->hasMany(Team::class,'team_category_id','id');
    }

}
