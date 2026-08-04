<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ProgramScheduleItem extends Model
{
    protected $fillable = [
        'program_schedule_day_id',
        'time_slot',
        'title',
        'details',
        'venue',
        'sort_order',
        'status',
    ];

    protected $casts = [
        'status' => 'boolean',
    ];

    public function day(): BelongsTo
    {
        return $this->belongsTo(ProgramScheduleDay::class, 'program_schedule_day_id');
    }
}
