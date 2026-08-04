<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProgramScheduleSetting extends Model
{
    protected $fillable = [
        'conference_title',
        'venue',
        'event_dates',
        'disclaimer',
    ];

    public static function current(): self
    {
        return static::first() ?? new static();
    }
}
