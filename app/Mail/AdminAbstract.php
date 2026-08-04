<?php

namespace App\Mail;
use Illuminate\Mail\Mailables\Address;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;
use App\Models\CompetitionAbstract;
use Illuminate\Contracts\Mail\Attachable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Mail\Attachment;

class AdminAbstract extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * Create a new message instance.
     */
    public function __construct(
        protected CompetitionAbstract $competitionAbstract,
    ) {}

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            from: new Address('noreply@ircc.world', 'noreply@ircc.world'),
            replyTo: new Address('ignite@ircc.world', 'ignite@ircc.world'),
            subject: 'Abstract Submission (IRCC-24)',
        );
    }
    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: 'mail.abstract-submit-admin',
            with: [
                'data' => $this->competitionAbstract,
                
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
        return [ Attachment::fromPath(public_path('uploads/abstract/'.$this->competitionAbstract->abstract_file))
        ->as($this->competitionAbstract->abstract_number.'.pdf')
        ->withMime('application/pdf'),];
    }
}
