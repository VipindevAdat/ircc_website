<?php

namespace App\Jobs;

use App\Mail\UserAbstract;
use App\Models\AbstractSubmission;
use Illuminate\Bus\Queueable;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Throwable;

class SendAbstractEmail
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $abstract;

    /**
     * Create a new job instance.
     */
    public function __construct(AbstractSubmission $abstract)
    {
        $this->abstract = $abstract;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $abstractId = $this->abstract->id;

        Log::info('abstract_mail: SendAbstractEmail started', [
            'abstract_id' => $abstractId,
            'pid' => $this->abstract->pid,
            'status' => (int) $this->abstract->status,
            'registration_id' => $this->abstract->registration_id,
        ]);

        $registration = $this->abstract->registration;

        if (! $registration) {
            Log::warning('abstract_mail: skipped — no registration relation', [
                'abstract_id' => $abstractId,
                'registration_id' => $this->abstract->registration_id,
            ]);

            return;
        }

        if (empty($registration->email)) {
            Log::warning('abstract_mail: skipped — registration has empty email', [
                'abstract_id' => $abstractId,
                'registration_id' => $registration->id,
            ]);

            return;
        }

        Log::info('abstract_mail: sending UserAbstract mailable', [
            'abstract_id' => $abstractId,
            'to' => $registration->email,
            'mail_mailer' => config('mail.default'),
        ]);

        try {
            Mail::to($registration->email)->send(new UserAbstract($this->abstract, $registration));
            Log::info('abstract_mail: Mail::send completed without exception', [
                'abstract_id' => $abstractId,
                'to' => $registration->email,
            ]);
        } catch (Throwable $e) {
            Log::error('abstract_mail: Mail::send threw', [
                'abstract_id' => $abstractId,
                'to' => $registration->email,
                'message' => $e->getMessage(),
                'exception' => $e::class,
            ]);
            Log::error($e->getMessage(), ['trace' => $e->getTraceAsString()]);
            throw $e;
        }
    }
}
