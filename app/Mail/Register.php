<?php

namespace App\Mail;
use Illuminate\Mail\Mailables\Address;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;
use App\Models\Registration;
use App\Models\Participant;
use App\Models\Payment;

class Register extends Mailable
{
    use Queueable, SerializesModels;

    protected $participants;
    protected $registration;

    /**
     * Create a new message instance.
     *
     * @param Registration $registration
     * @param string $recipientType  'user' or 'admin'
     * @param bool|null $isFirstPayment  Force first-payment flag (true = registration confirmed, false = participant added). If null, auto-detect from payments.
     */
    public function __construct(Registration $registration, $recipientType, $isFirstPayment = null) {
        $this->registration = $registration;
        $this->participants = Participant::where('registration_id', $registration->id)->where('type', '<>', 2)->get();
        $this->recipientType = $recipientType;

        if ($isFirstPayment !== null) {
            $this->isFirstPayment = $isFirstPayment;
        } else {
            $latestPayment = Payment::where('registration_id', $registration->id)
                                ->latest('created_at')
                                ->first();
            $this->isFirstPayment = $latestPayment && $latestPayment->registration_fee == 500;
        }
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            from: new Address('noreply@ircc.world', 'noreply@ircc.world'),
            replyTo: new Address('ignite@ircc.world', 'ignite@ircc.world'),
            subject: 'International Research Conference for Children 2026 (IRCC\'25) - Registration',
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: 'mail.register-mail',
            with: [
                'registration' => $this->registration,
                'participants' => $this->participants,
                'recipientType' => $this->recipientType,
                'isFirstPayment' => $this->isFirstPayment,
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
        return [];
    }
}
