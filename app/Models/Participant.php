<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Participant extends Model
{
    use HasFactory;

    protected $fillable = [
        'type',
        'role', // 'student' or 'adult'
        'age', // Required for students
        'email', // Optional for students, required for adults
        'first_name',
        'last_name',
        'class',
        'registration_id',
    ];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function registration()
    {
        return $this->belongsTo(Registration::class, 'registration_id', 'id');
    }
}
