<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Carbon;

class Registration extends Authenticatable implements MustVerifyEmail
{
    use HasApiTokens, HasFactory, Notifiable,SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'registration_tag',
        'first_name','registration_number','last_name','institution_name','phone',
        'email',
        'password',
        'hear_about_us',
        'is_attending',
        'student_participant_unit_price',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
        'student_participant_unit_price' => 'decimal:2',
    ];

    /**
     * Student per-head price for a brand-new account at signup time (snapshotted on the registration row).
     */
    public static function studentUnitPriceForNewAccount(?Carbon $at = null): float
    {
        $at = $at ?? Carbon::now();
        $startsAt = Carbon::parse(
            config('ircc.student_regular_pricing_starts_at', '2026-04-01 00:00:00'),
            config('app.timezone')
        );

        if ($at->lt($startsAt)) {
            return (float) config('ircc.student_price_early', 150.00);
        }

        return (float) config('ircc.student_price_regular', 200.00);
    }

    public function hasVerifiedEmail()
    {
       
        return ! is_null($this->email_verified_at);
    }

    /**
     * Mark the given user's email as verified.
     *
     * @return bool
     */
    public function markEmailAsVerified()
    {
        return $this->forceFill([
            'email_verified_at' => $this->freshTimestamp(),
        ])->save();
    }
    public function participants()
    {
        return $this->hasMany(Participant::class, 'registration_id','id');
    }

    public function abstract()
    {
        return $this->hasOne(AbstractSubmission::class, 'registration_id', 'id');
    }

    public function abstracts()
    {
        return $this->hasMany(AbstractSubmission::class, 'registration_id', 'id');
    }

    public function getAbstractStatusAttribute()
    {
        return $this->abstract()->exists() ? 'Abstract Submitted' : 'Not Submitted';
    }
}
