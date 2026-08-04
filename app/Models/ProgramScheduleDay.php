<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ProgramScheduleDay extends Model
{
    protected $fillable = [
        'title',
        'sort_order',
        'is_visible',
        'is_virtual',
        'status',
    ];

    protected $casts = [
        'is_visible' => 'boolean',
        'is_virtual' => 'boolean',
        'status' => 'boolean',
    ];

    public function items(): HasMany
    {
        return $this->hasMany(ProgramScheduleItem::class)->orderBy('sort_order');
    }

    public function activeItems(): HasMany
    {
        return $this->items()->where('status', true);
    }
}
