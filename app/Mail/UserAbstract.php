<?php

namespace App\Mail;

use Illuminate\Mail\Mailables\Address;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;
use App\Models\AbstractSubmission;
use App\Models\Registration;
use Illuminate\Mail\Attachment;

class UserAbstract extends Mailable
{
    use Queueable, SerializesModels;

    protected AbstractSubmission $abstractSubmission;
    protected Registration $registration;

    /**
     * Create a new message instance.
     */
    public function __construct(
        AbstractSubmission $abstractSubmission,
        Registration $registration
    ) {
        $this->abstractSubmission = $abstractSubmission;
        $this->registration = $registration;
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        $isDraft = ((int) $this->abstractSubmission->status) === 0;
        $subject = $isDraft
            ? 'IRCC\'26 — Abstract saved as draft'
            : 'International Research Conference for Children 2026 (IRCC\'26) — Abstract submitted';

        return new Envelope(
            from: new Address('noreply@ircc.world', 'noreply@ircc.world'),
            replyTo: new Address('ignite@ircc.world', 'ignite@ircc.world'),
            subject: $subject,
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: 'mail.abstract-submit',
            with: [
                'data' => $this->abstractSubmission,
                'registration' => $this->registration,
            ],
        );
    }

    /**
     * Get the attachments for the message.
     *
     * @return array<int, \Illuminate\Mail\Mailables\Attachment>
     */
    public function attachments(): array
    {
        if (empty($this->abstractSubmission->abstract_file)) {
            return [];
        }

        $filePath = public_path('uploads/abstract/'.$this->abstractSubmission->abstract_file);

        if (!is_file($filePath)) {
            return [];
        }

        return [
            Attachment::fromPath($filePath)
                ->as($this->abstractSubmission->pid.'.pdf')
                ->withMime('application/pdf'),
        ];
    }

}