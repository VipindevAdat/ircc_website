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
use App\Models\ParticipantPayment;
use App\Models\Payment;
use App\Models\Setting;
use App\Traits\GivebutterHelper;

class PaymentDetails extends Mailable
{
    use Queueable, SerializesModels, GivebutterHelper;

    protected $registration;
    protected $payment;
    protected $amountPerHead;
    protected $registrationFee;
    protected $participantCount;
    protected $paymentSummary;
    protected $participants;

    /**
     * Create a new message instance.
     */
    public function __construct(Registration $registration,$paymentId, $recipientType)
    {
        $this->registration = $registration;
        $this->paymentId = $paymentId;
        $this->recipientType = $recipientType;
        
        // Get all participants for this payment (for this specific payment)
        $this->participants = ParticipantPayment::with('participant')->where('registration_id', $registration->id)->where('payment_id',$paymentId)->get();

        // Get the payment record
        $this->payment = Payment::where('registration_id', $registration->id)->where('id',$paymentId)
                                ->latest()
                                ->first();

        // If payment not found, log error but don't throw exception
        if (!$this->payment) {
            \Log::error('PaymentDetails: Payment not found', [
                'registration_id' => $registration->id,
                'payment_id' => $paymentId,
            ]);
            // Set default values to prevent errors
            $this->payment = new Payment();
            $this->payment->id = $paymentId;
            $this->payment->registration_id = $registration->id;
        }

        // Fetch settings
        $this->amountPerHead = optional(Setting::where('code', 'amount_per_head')->first())->value ?? 0;
        $this->registrationFee = optional(Setting::where('code', 'registration_fee')->first())->value ?? 0;

        // Get participants count for THIS payment
        // First try from ParticipantPayment records (most accurate - these are the actual participants linked to this payment)
        $thisPaymentParticipantCount = $this->participants->count();
        
        // If no ParticipantPayment records exist yet, use the payment's participants field
        // This field stores how many participants this payment covers
        if ($thisPaymentParticipantCount == 0 && $this->payment) {
            $thisPaymentParticipantCount = (int)($this->payment->participants ?? 0);
        }
        
        // Get TOTAL participants count for this registration (for context and calculations)
        $totalParticipantsInRegistration = Participant::where('registration_id', $registration->id)->where('type', '<>', 2)->count();
        
        // For email display: Show participants covered by THIS payment
        // For partial payments (participants = 0), we'll show 0 in the email with a note
        $this->participantCount = $thisPaymentParticipantCount;

        // Calculate Payment Summary with total amounts
        $this->paymentSummary = $this->calculatePaymentSummary();
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            from: new Address('noreply@ircc.world', 'noreply@ircc.world'),
            replyTo: new Address('ignite@ircc.world', 'ignite@ircc.world'),
            subject: 'International Research Conference for Children 2025 (IRCC\'25) - Payment',
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        // Determine if this is a partial payment
        $isPartialPayment = false;
        if ($this->payment) {
            $isPartialPayment = (
                ($this->payment->participants == 0) || 
                (strpos($this->payment->payment_status_name ?? '', 'Partial') !== false)
            );
        }
        
        // Get total participants in registration
        $totalParticipantsInRegistration = Participant::where('registration_id', $this->registration->id)
            ->where('type', '<>', 2)
            ->count();
        
        return new Content(
            view: 'mail.payment-details-mail',
            with: [
                'registration' => $this->registration,
                'recipientType' => $this->recipientType,
                'participants' => $this->participants,
                'payment' => $this->payment,
                'amountPerHead' => $this->amountPerHead,
                'registrationFee' => $this->registrationFee,
                'participantCount' => $this->participantCount,
                'totalParticipantsInRegistration' => $totalParticipantsInRegistration,
                'isPartialPayment' => $isPartialPayment,
                'paymentSummary' => $this->paymentSummary,
            ],
        );
    }

    /**
     * Get the attachments for the message.
     */
    public function attachments(): array
    {
        return [];
    }

    /**
     * Calculate the payment summary using role-based pricing.
     */
    protected function calculatePaymentSummary()
    {
        // Use the GivebutterHelper trait's user_payments method for correct role-based pricing
        $paymentSummary = $this->user_payments($this->registration->id);
        
        // Get TOTAL amount paid across ALL completed payments for this registration
        $completedPayments = Payment::where('registration_id', $this->registration->id)
            ->where('payment_status', 3) // Completed payments only
            ->get();
        
        $totalAmountPaid = $completedPayments->sum(function($payment) {
            return (float)($payment->paid_amount ?? $payment->amount ?? 0);
        });
        
        // Get subtotal and grand total from the payment summary (already calculated correctly)
        $subTotal = (float)str_replace(',', '', $paymentSummary['subTotal']);
        $grandTotal = (float)str_replace(',', '', $paymentSummary['grandTotal']); // This is already subTotal (no separate reg fee)

        // Remaining balance must match the dashboard billing engine (GivebutterHelper::user_payments),
        // which uses pending-slot logic and mixed early/regular student rates — not (grandTotal - paid).
        $amountPayable = (float) str_replace(',', '', $paymentSummary['amountPayable'] ?? '0');
        $amountRemaining = max(0, $amountPayable);

        return [
            'subTotal' => $subTotal,
            'grandTotal' => $grandTotal, // This is the correct total (no separate reg fee)
            'registrationFee' => null, // Don't show separate registration fee - it's included in participant fees
            'amountPaid' => $totalAmountPaid,
            'amountPayable' => $amountPayable,
            'amountRemaining' => $amountRemaining,
            'thisPaymentAmount' => ($this->payment && $this->payment->id) ? ($this->payment->paid_amount ?? $this->payment->amount ?? 0) : 0,
            // Include role-based pricing details for the email template
            'studentCount' => $paymentSummary['studentCount'] ?? 0,
            'adultCount' => $paymentSummary['adultCount'] ?? 0,
            'studentPrice' => $paymentSummary['studentPrice'] ?? '150.00',
            'adultPrice' => $paymentSummary['adultPrice'] ?? '50.00',
            'studentSubtotal' => $paymentSummary['studentSubtotal'] ?? '0.00',
            'adultSubtotal' => $paymentSummary['adultSubtotal'] ?? '0.00',
            'studentPriceEarly' => $paymentSummary['studentPriceEarly'] ?? null,
            'studentPriceRegular' => $paymentSummary['studentPriceRegular'] ?? null,
            'studentCountEarly' => $paymentSummary['studentCountEarly'] ?? 0,
            'studentCountRegular' => $paymentSummary['studentCountRegular'] ?? 0,
            'studentSubtotalEarly' => $paymentSummary['studentSubtotalEarly'] ?? '0.00',
            'studentSubtotalRegular' => $paymentSummary['studentSubtotalRegular'] ?? '0.00',
            'pendingStudentCountEarly' => $paymentSummary['pendingStudentCountEarly'] ?? 0,
            'pendingStudentCountRegular' => $paymentSummary['pendingStudentCountRegular'] ?? 0,
            'pendingAdultCount' => $paymentSummary['pendingAdultCount'] ?? 0,
            'pendingStudentSubtotalEarly' => $paymentSummary['pendingStudentSubtotalEarly'] ?? '0.00',
            'pendingStudentSubtotalRegular' => $paymentSummary['pendingStudentSubtotalRegular'] ?? '0.00',
            'pendingAdultSubtotal' => $paymentSummary['pendingAdultSubtotal'] ?? '0.00',
        ];
    }
}
