<?php

namespace App\Mail;
use Illuminate\Mail\Mailables\Address;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;
use App\Models\MailHistory;
use Illuminate\Mail\Attachment;

class Invitation extends Mailable
{
    use Queueable, SerializesModels;
    public $attachmentsFile;

    /**
     * Create a new message instance.
     */
    public function __construct(
        protected MailHistory $mailHistory,$attachmentsFile
      
       
    ) 
    {
        $this->attachmentsFile = $attachmentsFile;
    }
 
    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            from: new Address('noreply@ircc.world', 'noreply@ircc.world'),
            replyTo: new Address('ignite@ircc.world', 'ignite@ircc.world'),
            subject: $this->mailHistory->subject,
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: 'mail.invitation-mail',
            with: [
                'data' => $this->mailHistory,
                
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
        $mailAttach=[];
        foreach ($this->attachmentsFile as $file)
           {
            if(!empty($file)){
                $mailAttach[]=Attachment::fromPath($file['path'])
                ->as($file['as'].'.'.$file['extension'])
                ->withMime($file['mime']);
            }
            
            }
            return $mailAttach;
        
    }
}
