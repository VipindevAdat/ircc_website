<?php

namespace App\Models;

use App\Jobs\GenerateAbstractPdf;
use App\Jobs\SendAbstractEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Log;
use Throwable;

class AbstractSubmission extends Model
{
    use HasFactory;

    /**
     * After save: 0 = none, 1 = draft confirmation email only, 2 = generate PDF then confirmation email.
     */
    public int $abstractNotificationAfterSave = 0;

    protected $table = 'abstracts';

    protected $fillable = [
        'pid',
        'registration_id',
        'category_id',
        'abstract_domain',
        'abstract_title',
        'student_authors',
        'additional_authors',
        'correspondent_author',
        'abstract_content',
        'references',
        'abstract_file',
        'score',
        'reviewer_id',
        'comments',
        'status',
        'edit_status',
        'edited_at'
    ];

    protected static function booted(): void
    {
        static::creating(function (AbstractSubmission $abstract): void {
            $abstract->abstractNotificationAfterSave = ((int) $abstract->status) === 1 ? 2 : 1;
            Log::info('abstract_mail: creating abstract, notification queued after save', [
                'registration_id' => $abstract->registration_id,
                'status' => (int) $abstract->status,
                'notification_kind' => $abstract->abstractNotificationAfterSave,
                'note' => 'abstract_id is assigned in the following saved log line',
            ]);
        });

        static::updating(function (AbstractSubmission $abstract): void {
            if ($abstract->isDirty('status')) {
                $previous = (int) $abstract->getOriginal('status');
                $next = (int) $abstract->status;
                if ($next === 1 && $previous !== 1) {
                    $abstract->abstractNotificationAfterSave = 2;
                    Log::info('abstract_mail: status transition will queue confirmation email and PDF job after save', [
                        'abstract_id' => $abstract->id,
                        'registration_id' => $abstract->registration_id,
                        'from_status' => $previous,
                        'to_status' => $next,
                        'notification_kind' => 2,
                    ]);
                }

                return;
            }

            if ((int) $abstract->status === 0 && $abstract->isDirty()) {
                $abstract->abstractNotificationAfterSave = 1;
                Log::info('abstract_mail: draft save will queue email after save', [
                    'abstract_id' => $abstract->id,
                    'registration_id' => $abstract->registration_id,
                    'dirty' => array_keys($abstract->getDirty()),
                    'notification_kind' => 1,
                ]);
            }
        });

        static::saved(function (AbstractSubmission $abstract): void {
            $kind = $abstract->abstractNotificationAfterSave;
            if ($kind === 0) {
                return;
            }
            $abstract->abstractNotificationAfterSave = 0;

            Log::info('abstract_mail: saved hook dispatching job', [
                'abstract_id' => $abstract->id,
                'registration_id' => $abstract->registration_id,
                'pid' => $abstract->pid,
                'status' => (int) $abstract->status,
                'notification_kind' => $kind,
                'kind_label' => $kind === 2 ? 'email_plus_pdf_jobs' : 'email_only',
                'queue_connection' => config('queue.default'),
            ]);

            try {
                if ($kind === 2) {
                    // Send confirmation immediately; PDF generation can fail or be slow without blocking mail.
                    SendAbstractEmail::dispatch($abstract);
                    // Defer PDF until after the HTTP response (critical when QUEUE_CONNECTION=sync — DomPDF was blocking JSON).
                    GenerateAbstractPdf::dispatch($abstract)->afterResponse();
                } elseif ($kind === 1) {
                    SendAbstractEmail::dispatch($abstract);
                }
                Log::info('abstract_mail: dispatch() returned (job queued or run sync)', [
                    'abstract_id' => $abstract->id,
                    'notification_kind' => $kind,
                ]);
            } catch (Throwable $e) {
                Log::error('abstract_mail: dispatch failed', [
                    'abstract_id' => $abstract->id,
                    'kind' => $kind,
                    'message' => $e->getMessage(),
                    'exception' => $e::class,
                ]);
                Log::error($e->getMessage(), ['trace' => $e->getTraceAsString()]);
            }
        });
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function registration(){
        return $this->belongsTo(Registration::class,'registration_id','id');
    }

    public function reviewers()
    {
        return $this->belongsToMany(Reviewer::class, 'abstract_reviewer', 'abstract_id', 'reviewer_id')
            ->withPivot('review_status', 'score', 'comments')
            ->withTimestamps();
    }


    /**
     * Accessor to decode student_authors JSON.
     */
    public function getStudentAuthorsAttribute($value)
    {
        return json_decode($value, true);
    }

    /**
     * Mutator to encode student_authors as JSON.
     */
    public function setStudentAuthorsAttribute($value)
    {
        $this->attributes['student_authors'] = json_encode($value);
    }

    public function category()
    {
        return $this->belongsTo(Category::class, 'category_id', 'id');
    }

    public function abstractDomain()
    {
        return $this->belongsTo(AbstractDomain::class, 'abstract_domain', 'id');
    }

    public function abstractReviewer()
    {
        return $this->hasOne(AbstractReviewer::class, 'abstract_id','id');
    }

    /**
     * Admin/mail legacy field: stored value is `pid`.
     */
    public function getAbstractNumberAttribute(): ?string
    {
        return $this->attributes['pid'] ?? null;
    }

    /**
     * Dashboard sidebar / quick-link label for abstract section.
     */
    public static function dashboardAbstractNavLabel(int $registrationId): string
    {
        $count = static::where('registration_id', $registrationId)->count();
        $hasDraft = static::where('registration_id', $registrationId)->where('status', 0)->exists();

        if ($count === 0) {
            return 'Add Abstract';
        }
        if ($hasDraft) {
            return 'Manage Abstracts';
        }
        if ($count < 2) {
            return 'Add Abstract';
        }

        return 'Abstracts';
    }

    /**
     * Whether to show the abstract menu item on the customer dashboard.
     *
     * Registrations that already have abstract rows (draft or submitted) can always open the section
     * so drafts can be finished even if the public abstract portal setting is turned off.
     * New submissions (no rows yet) require the portal to be open, unless the registration is already
     * fully paid with a completed payment (so checkout → abstract flow works when abstract_status is off).
     */
    public static function dashboardAbstractNavVisible(
        int $registrationId,
        bool $abstractPortalOpen,
        bool $fullyPaidWithCompletedPayment = false
    ): bool {
        $count = static::where('registration_id', $registrationId)->count();
        $hasDraft = static::where('registration_id', $registrationId)->where('status', 0)->exists();

        if ($count >= 2 && ! $hasDraft) {
            return false;
        }

        if ($count > 0) {
            return true;
        }

        return (bool) $abstractPortalOpen || $fullyPaidWithCompletedPayment;
    }

}
