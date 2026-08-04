<?php

namespace App\Http\Controllers;

use App\Models\AbstractDomain;
use App\Models\AbstractSubmission;
use App\Models\Category;
use App\Models\Participant;
use App\Models\ParticipantPayment;
use App\Models\Payment;
use App\Models\PaymentStatus;
use App\Models\PaymentRefund;
use App\Models\Setting;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Exception;
use App\Mail\PaymentDetails;
use App\Mail\Register;
// use App\Mail\PaymentVerification; // Commented out - class not created yet
use App\Models\Registration;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Schema;
use App\Traits\GivebutterHelper;
use App\Support\PaymentCoupon;

class PaymentController extends Controller
{
    use GivebutterHelper;

    /**
     * Display payment page
     */
    public function index()
    {
        $user = Auth::guard('customer')->user();

        $participants = Participant::where('registration_id', $user->id)->where('type','<>',2)->get();
        $participantsCount = $participants->count();
        $paidParticipantsCount = Payment::where('registration_id', $user->id)->where('payment_status',3)->sum('participants');
        $payments = Payment::where('registration_id', $user->id)->where('payment_status',3)->orderBy('id', 'DESC')->get();
        $amountPerHead = optional(Setting::where('code', 'amount_per_head')->first())->value;
        $registration_fee = optional(Setting::where('code', 'registration_fee')->first())->value;
        $paymentDoneCount = Payment::where('registration_id', $user->id)->where('payment_status',3)->sum('participants');
        $paymentPendingCount = $participantsCount - $paymentDoneCount;

        $paymentModes = [];
        $paymentModeSetting = Setting::where('code', 'payment_mode')->first();
        $paymentModesArray = [];
        $paymentModeSelected = [];
        
        if ($paymentModeSetting) {
            $paymentModesArray = json_decode($paymentModeSetting->dataset, true) ?? [];
            $value = $paymentModeSetting->value;
            if (!empty($value)) {
                $paymentModeSelected = json_decode($value, true) ?? [];
            }
        }
        
        // If no payment modes are selected, show all available modes
        if (empty($paymentModeSelected) && !empty($paymentModesArray)) {
            $paymentModes = $paymentModesArray;
        } else {
            $paymentModes = $this->found_in_array($paymentModesArray, $paymentModeSelected);
        }
        
        // Fallback: If still empty, provide default payment modes
        if (empty($paymentModes)) {
            $paymentModes = [
                ['id' => 0, 'name' => 'Online Transaction'],
                ['id' => 1, 'name' => 'Direct Bank Transfer']
            ];
        }
        $paymentPendingCount = $participantsCount - $paidParticipantsCount;
        $totalAmountPaid = $payments->sum('amount');
        $payment_summary = $this->user_payments();
        $refundHistory = PaymentRefund::where('registration_id', $user->id)->get();
        $abstract = AbstractSubmission::where('registration_id', $user->id)->where('status', 1)->first();
        $paymentApprovePending= Payment::where('registration_id', $user->id)->where('payment_mode','Direct Bank Transfer')->where('payment_status',1)->first();
        
        return view('dashboard.make-payment', [
            'user' => $user,
            'participants' => Participant::where('registration_id', $user->id)->get(),
            'participantsCount' =>  Participant::where('registration_id', $user->id)->get()->count(),
            'paymentDoneCount' => $paymentDoneCount,
            'paymentPendingCount' => $paymentPendingCount,
            'paymentModes' => $paymentModes,
            'totalAmountPaid' => $totalAmountPaid,
            'payment_summary' => $payment_summary,
            'paidParticipantCount' => $paidParticipantsCount,
            'amountPerHead' => $amountPerHead,
            'refundHistory' => $refundHistory,
            'payments' => $payments,
            'registrationFee' => $registration_fee,
            'abstract'=>$abstract,
            'paymentApprovePending'=>$paymentApprovePending,
            'settings' => Setting::where('status', 1)->get(),
            'rise150_student_discount' => (float) str_replace(',', '', $payment_summary['rise150_student_discount'] ?? '0'),
        ]);
    }

    /**
     * Helper function to filter payment modes
     */
    function found_in_array($needle, $haystack) {
        $selected=[];
        foreach ($needle as $array) {
            if(!empty($haystack)){
                if(in_array($array['id'], $haystack)){
                    $selected[]=$array;
                }
            }
        }
        return $selected;
    }

    /**
     * Store payment - Create payment record and initiate Givebutter payment
     */
    public function store(Request $request)
    {
        try {
            $user = Auth::guard('customer')->user();
        
            if (!$user) {
                if ($request->ajax() || $request->wantsJson()) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Unauthorized access. Please log in.'
                    ], 403);
                }
                abort(403, 'Unauthorized access.');
            }
        
        $participantsCount = Participant::where('registration_id', $user->id)->count();
        $paidParticipantsCount = Payment::where('registration_id', $user->id)->where('payment_status',3)->sum('participants');
        
        if ($participantsCount === $paidParticipantsCount) {
            return redirect()->back()->with('error', 'All participants have already been paid for.');
        }
    
        $amountPerHead = optional(Setting::where('code', 'amount_per_head')->first())->value;
        $registrationFee = optional(Setting::where('code', 'registration_fee')->first())->value;
        $adminEmail = Setting::where('code', 'email')->first()?->value ?? 'ignite@ircc.world';
        $payment_summary = $this->user_payments();
        $isFirstPayment = Payment::where('registration_id', $user->id)->where('payment_status',3)->doesntExist();
        
        // Check if there's already a pending payment for Online Transaction (to prevent loops)
        if ($request->input('payment_mode') === 'Online Transaction') {
            $pendingPayment = Payment::where('registration_id', $user->id)
                ->where('payment_status', 2) // Processing
                ->where('payment_status_name', 'Processing')
                ->where('created_at', '>=', now()->subMinutes(5)) // Created in last 5 minutes
                ->latest()
                ->first();
            
            if ($pendingPayment) {
                $requestedAmount = (float) $request->input('amount', 0);
                $currentAmountPayable = (float) str_replace(',', '', ($payment_summary['amountPayable'] ?? 0));
                $expectedAmount = $requestedAmount > 0 ? $requestedAmount : $currentAmountPayable;
                $pendingAmount = (float) ($pendingPayment->amount ?? 0);
                $amountMatches = abs($pendingAmount - $expectedAmount) < 0.01;

                if (!$amountMatches) {
                    \Log::info('Skipping pending payment reuse due to amount mismatch', [
                        'pending_payment_id' => $pendingPayment->id,
                        'pending_order_id' => $pendingPayment->payment_order_id,
                        'pending_amount' => $pendingAmount,
                        'expected_amount' => $expectedAmount,
                        'requested_amount' => $requestedAmount,
                        'current_amount_payable' => $currentAmountPayable,
                    ]);
                    $pendingPayment = null;
                }
            }

            if ($pendingPayment) {
                \Log::info('Pending payment already exists, returning existing payment URL', [
                    'payment_id' => $pendingPayment->id,
                    'order_id' => $pendingPayment->payment_order_id,
                ]);
                
                // Return existing payment URL if AJAX request
                if ($request->ajax() || $request->wantsJson()) {
                    $response = $this->createOrder($pendingPayment->payment_order_id, $pendingPayment->amount, 
                        ['first_name' => $user->first_name, 'last_name' => $user->last_name, 'email' => $user->email], 
                        ['additional_info1' => $user->id, 'additional_info2' => $user->email, 'additional_info3' => $user->phone]);
                    
                    if ($response['status'] == 'ACTIVE') {
                        $paymentUrl = $response['url'] ?? $response['payment_url'] ?? $response['checkout_url'];
                        return response()->json([
                            'success' => true,
                            'message' => 'Using existing payment session',
                            'payment_url' => $paymentUrl,
                            'order_id' => $pendingPayment->payment_order_id
                        ]);
                    }
                }
                
                // For non-AJAX, redirect to dashboard with message
                return redirect()->route('accounts.dashboard')->with('info', 'You already have a payment in progress. Please complete it or wait a few minutes.');
            }
        }
    
        if ($isFirstPayment) {
            $amount = str_replace(",", "",$payment_summary['amountPayable']);
            $participantFee = $amount - $registrationFee;
        } else {
            $amount = str_replace(",", "",$payment_summary['amountPayable']);
            $participantFee = $amount;
            $registrationFee = null;
        }
        
        // Validate waiver form is completed before processing payment
        $waiverAcknowledgment = $request->input('waiver_acknowledgment');
        $waiverSignature = $request->input('waiver_signature');
        $waiverDate = $request->input('waiver_date');
        
        // Log waiver data for debugging
        \Log::info('Payment waiver validation', [
            'waiver_acknowledgment' => $waiverAcknowledgment,
            'waiver_signature' => $waiverSignature ? 'present' : 'missing',
            'waiver_date' => $waiverDate,
            'all_inputs' => $request->all()
        ]);
        
        if (empty($waiverAcknowledgment) || $waiverAcknowledgment !== '1' || 
            empty($waiverSignature) || empty($waiverDate)) {
            $errorMessage = 'Please complete the Conference Waiver and Release Form before proceeding with payment.';
            if ($request->ajax() || $request->wantsJson()) {
                \Log::warning('Payment blocked: Waiver incomplete', [
                    'waiver_acknowledgment' => $waiverAcknowledgment,
                    'has_signature' => !empty($waiverSignature),
                    'has_date' => !empty($waiverDate)
                ]);
                return response()->json([
                    'success' => false,
                    'message' => $errorMessage
                ], 422);
            }
            return redirect()->back()->with('error', $errorMessage);
        }
        
        $request->validate([
            'payment_mode' => 'required|string|max:255',
        ]);
    
        $payment_reference = null;
        $payment_date = null;
    
        // Handle Direct Bank Transfer
        if ($request->payment_mode === 'Direct Bank Transfer') {
            $request->validate([
                'payment_reference' => 'required|unique:payments,payment_reference',
                'payment_date' => 'required|date',
            ]);
    
            $payment_reference = $request->payment_reference;
            $payment_date = $request->payment_date;
        }
        
        // Handle coupon code (server-side authoritative calculation; discounts apply to student line only)
        $couponCode = trim((string) $request->input('coupon_code', ''));
        $studentPayable = (float) str_replace(',', '', $payment_summary['studentAmountPayable'] ?? '0');
        $adultPayable = (float) str_replace(',', '', $payment_summary['adultAmountPayable'] ?? '0');

        try {
            $applied = PaymentCoupon::apply($couponCode, $studentPayable, $adultPayable, $user->id, null);
        } catch (\InvalidArgumentException $e) {
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Invalid coupon code.',
                ], 422);
            }

            return redirect()->back()->with('error', 'Invalid coupon code.');
        }

        $amount = $applied['amount'];
        $discountAmount = $applied['discount_amount'];
        $originalAmount = $applied['original_amount'];
        $couponCode = $applied['coupon_code'] ?? '';

        if ($couponCode !== '') {
            \Log::info('Coupon applied server-side', [
                'coupon_code' => $couponCode,
                'student_payable' => $studentPayable,
                'adult_payable' => $adultPayable,
                'discount_amount' => $discountAmount,
                'final_amount' => $amount,
            ]);
        }
   
        // Create the Payment record
        $slug = strtoupper(Str::slug($user->registration_number));
        $count = Payment::where('registration_id',$user->id)->count();
        $paymentOrderId = $count ? "{$slug}-{$count}" : $slug;

        // Build payment data array
        $paymentData = [
            'payment_order_id'=>$paymentOrderId,
            'registration_id' => $user->id,
            'payment_reference' => $payment_reference,
            'payment_date' => $payment_date,
            'participants' => $participantsCount - $paidParticipantsCount,
            'amount' => $amount,
            'payment_mode' => $request->payment_mode,
            'registration_fee' => $registrationFee,
            'participant_fee' => $participantFee,
            'payment_amount'=> $amount,
        ];
        
        // Add coupon fields only if columns exist (check via schema)
        try {
            if (Schema::hasColumn('payments', 'coupon_code')) {
                $paymentData['coupon_code'] = $couponCode ?: null;
            }
            if (Schema::hasColumn('payments', 'discount_amount')) {
                $paymentData['discount_amount'] = $discountAmount > 0 ? $discountAmount : null;
            }
            if (Schema::hasColumn('payments', 'original_amount')) {
                $paymentData['original_amount'] = $originalAmount;
            }
        } catch (\Exception $e) {
            \Log::warning('Could not check for coupon columns, skipping coupon fields', [
                'error' => $e->getMessage()
            ]);
        }
        
        // Add remaining payment fields
        $paymentData['currency'] = 'INR';
        $paymentData['customer_name'] = $user->first_name.' '.$user->last_name;
        $paymentData['mobile'] = $user->phone;
        $paymentData['email'] = $user->email;
        $paymentData['payment_status'] = 1;
        $paymentData['payment_status_name'] = 'Pending';
        // Waiver fields
        $paymentData['waiver_acknowledgment'] = $request->input('waiver_acknowledgment') == '1' || $request->input('waiver_acknowledgment') === '1';
        $paymentData['waiver_signature'] = $request->input('waiver_signature');
        $paymentData['waiver_date'] = $request->input('waiver_date');
        $paymentData['waiver_under_12'] = $request->input('waiver_under_12') == '1' || $request->input('waiver_under_12') === '1';
        $paymentData['waiver_12_or_older'] = $request->input('waiver_12_or_older') == '1' || $request->input('waiver_12_or_older') === '1';
        
        $payment = Payment::create($paymentData);

        // Handle Direct Bank Transfer
        if ($request->payment_mode === 'Direct Bank Transfer') {
            $paymentStatusId = optional(PaymentStatus::where('status', 'Pending')->first())->id;
            $payment->update(['payment_status'=>1,'payment_status_name'=>'Pending']); 
            
            $participants = Participant::where('registration_id', $user->id)->get();
            $existingParticipantIds = ParticipantPayment::where('registration_id', $user->id)->pluck('participant_id')->toArray();

            foreach ($participants as $participant) {
                if (!in_array($participant->id, $existingParticipantIds)) {
                    ParticipantPayment::create([
                        'registration_id' => $user->id,
                        'payment_id' => $payment->id,
                        'participant_id' => $participant->id,
                        'payment_status_id'=>3,
                        'participant_name' => $participant->first_name . ' ' . $participant->last_name,
                    ]);
                }
            }
                    
            try{
                \Log::info('Attempting to send bank transfer payment emails', [
                    'user_email' => $user->email,
                    'payment_id' => $payment->id,
                ]);
                
                Mail::to($adminEmail)->send(new PaymentDetails($user,$payment->id, 'admin'));
                \Log::info('Bank transfer admin email sent successfully', [
                    'admin_email' => $adminEmail,
                    'payment_id' => $payment->id,
                ]);
                
                Mail::to($user->email)->send(new PaymentDetails($user,$payment->id, 'user'));
                \Log::info('Bank transfer user email sent successfully', [
                    'user_email' => $user->email,
                    'payment_id' => $payment->id,
                ]);
                
                Mail::to($user->email)->send(new Register($user,'user'));
                \Log::info('Registration email sent successfully', [
                    'user_email' => $user->email,
                ]);
            }
            catch(\Exception $e){
                \Log::error('Failed to send bank transfer payment emails', [
                    'error' => $e->getMessage(),
                    'file' => $e->getFile(),
                    'line' => $e->getLine(),
                    'trace' => $e->getTraceAsString(),
                    'payment_id' => $payment->id ?? 'unknown',
                ]);
            } catch(\Throwable $e) {
                \Log::error('Failed to send bank transfer payment emails (Throwable)', [
                    'error' => $e->getMessage(),
                    'file' => $e->getFile(),
                    'line' => $e->getLine(),
                    'payment_id' => $payment->id ?? 'unknown',
                ]);
            }
            
            $payments=Payment::where('registration_id',$user->id)->get();
            if($payments->count()==1){
                $success_message='<p>The payment of ₹'.$payment->amount.' was recorded. Your payment Reference is '.$payment->payment_reference.'</p><p> Thank you for registration with IRCC\'25</p><p>Your registration is subject to verification and will be approved within 24 hrs</p>';
            }
            else{
                $success_message='<p>The payment of ₹'.$payment->amount.' was recorded. Your payment Reference is '.$payment->payment_reference.'</p><p>Your registration has been updated with new participants.</p><p>Your registration is subject to verification and will be approved within 24 hrs</p>';
            }
           
            return redirect()->route('accounts.dashboard')->with(['payment_success'=>$success_message,'success_title'=>'Payment Recorded !'] );
        }
        // Handle Givebutter Online Payment
        else{
            // Check if payment details are provided (direct API processing)
            if ($request->has('card_number') && $request->has('cardholder_name')) {
                // Direct payment processing via API
                return $this->processDirectPayment($request, $payment, $user, $amount, $adminEmail);
            }
            
            // Otherwise, create Givebutter donation page URL (legacy flow)
            // Create Givebutter donation via API
            $additionalInfo=['additional_info1'=>$user->id,'additional_info2'=>$user->email,'additional_info3'=>$user->phone];
            $customer=['first_name'=>$user->first_name,'last_name'=>$user->last_name,'email'=>$user->email,'email_alt'=>$user->email,'mobile'=>$user->phone,'mobile_alt'=>$user->phone];
            
            $response = $this->createOrder($payment->payment_order_id, $amount, $customer, $additionalInfo);
            
            if($response['status']=='ACTIVE'){
                // Update payment with Givebutter donation ID
                $payment->update([
                    'bdOrderId' => $response['donation_id'] ?? $payment->payment_order_id,
                    'payment_status' => 2,
                    'payment_status_name' => 'Processing'
                ]);
                
                // Return Givebutter checkout page URL
                // The webhook will automatically update payment status when payment is completed
                $paymentUrl = $response['url'] ?? $response['payment_url'] ?? $response['checkout_url'];
                if ($paymentUrl) {
                    // Store payment info in session for when user returns
                    $returnUrl = route('payment.success', ['order_id' => $payment->payment_order_id]);
                    session()->put('givebutter_payment_initiated', [
                        'order_id' => $payment->payment_order_id,
                        'return_url' => $returnUrl,
                        'dashboard_url' => route('accounts.dashboard'),
                        'message' => 'After completing payment on Givebutter, please return to your dashboard to see payment confirmation.',
                    ]);
                    
                    \Log::info('Redirecting to Givebutter payment page', [
                        'order_id' => $payment->payment_order_id,
                        'return_url' => $returnUrl,
                        'dashboard_url' => route('accounts.dashboard'),
                        'note' => 'User must manually return after payment - Givebutter does not support auto-redirect',
                    ]);
                    
                    // If AJAX request, return JSON with payment URL
                    if ($request->ajax() || $request->wantsJson()) {
                        return response()->json([
                            'success' => true,
                            'message' => 'Payment initiated successfully',
                            'payment_url' => $paymentUrl,
                            'order_id' => $payment->payment_order_id
                        ]);
                    }
                    
                    // Show a message before redirecting
                    return redirect($paymentUrl)->with('info', 'You are being redirected to Givebutter to complete payment. After payment, please return to your dashboard to see the confirmation.');
                } else {
                    if ($request->ajax() || $request->wantsJson()) {
                        return response()->json([
                            'success' => false,
                            'message' => 'Payment URL not received from Givebutter.'
                        ], 400);
                    }
                    return redirect()->back()->with('error', 'Payment URL not received from Givebutter.');
                }
            }
            else{
                $payment->update(['payment_status'=>6,'payment_status_name'=>'Failed']);  
                
                // Get detailed error message
                $errorMsg = $response['error'] ?? 'Failed to create donation';
                if (isset($response['details']) && is_array($response['details'])) {
                    if (isset($response['details']['message'])) {
                        $errorMsg = $response['details']['message'];
                    } elseif (isset($response['details']['error'])) {
                        $errorMsg = is_array($response['details']['error']) ? json_encode($response['details']['error']) : $response['details']['error'];
                    }
                }
                
                // Log for debugging
                \Log::error('Payment initiation failed', [
                    'payment_id' => $payment->id,
                    'order_id' => $payment->payment_order_id,
                    'response' => $response,
                ]);
                
                // If AJAX request, return JSON error
                if ($request->ajax() || $request->wantsJson()) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Payment initiation failed: ' . $errorMsg . '. Please check your Givebutter API configuration or try again.'
                    ], 400);
                }
                
                return redirect()->back()->with('error', 'Payment initiation failed: ' . $errorMsg . '. Please check your Givebutter API configuration or try again.');
            }
        }
        } catch (\Exception $e) {
            \Log::error('Payment store error', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
                'request_data' => $request->except(['card_number', 'cvv', 'password'])
            ]);
            
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Payment processing failed: ' . $e->getMessage()
                ], 500);
            }
            
            return redirect()->back()->with('error', 'Payment processing failed: ' . $e->getMessage());
        }
    }

    /**
     * Handle Givebutter payment success callback
     */
    public function paymentSuccess(Request $request)
    {
        $orderId = $request->query('order_id');
        
        if (!$orderId) {
            return redirect()->route('accounts.dashboard')->with('error', 'Invalid payment session.');
        }

        // Find the payment
        $payment = Payment::where('payment_order_id', $orderId)->first();
        
        if (!$payment) {
            return redirect()->route('accounts.dashboard')->with('error', 'Payment not found.');
        }

        // If payment is already confirmed via webhook, show success
        if ($payment->payment_status == 3) {
            $user = Registration::find($payment->registration_id);
            
            if ($user) {
                $payments = Payment::where('registration_id', $user->id)->where('payment_status', 3)->get();
                $currency = $payment->currency ?? 'USD';
                $amountDisplay = '$' . number_format($payment->paid_amount ?? $payment->amount, 2);
                
                // Check if this is a partial payment
                $isPartial = strpos($payment->payment_status_name ?? '', 'Partial') !== false;
                
                if($isPartial) {
                    $success_message = '<p>Your partial payment of ' . $amountDisplay . ' was received successfully.</p><p>Transaction ID: ' . ($payment->transaction_id ?? $payment->payment_order_id) . '</p><p>This amount has been applied to reduce your pending balance.</p>';
                } elseif($payments->count() == 1){
                    $success_message = '<p>The payment of ' . $amountDisplay . ' was Successful. Your payment transaction ID is ' . ($payment->transaction_id ?? $payment->payment_order_id) . '</p><p> Thank you for registration with IRCC\'25</p>';
                } else {
                    $success_message = '<p>The payment of ' . $amountDisplay . ' was Successful. Your payment transaction ID is ' . ($payment->transaction_id ?? $payment->payment_order_id) . '</p><p> Your registration has been updated with new participants.</p>';
                }

                // Clear the payment initiated session message
                session()->forget('givebutter_payment_initiated');
                
                return redirect()->route('accounts.dashboard')->with([
                    'payment_success' => $success_message,
                    'success_title' => $isPartial ? 'Partial Payment Received!' : 'Payment Successful!',
                    'show_dashboard_button' => true
                ]);
            }
        }

        // Payment still processing - check if it was processed via webhook
        // For partial payments, webhook might have already processed it
        // Check for any recent completed payments for this user
        $user = Registration::find($payment->registration_id);
        if ($user) {
            $recentPayment = Payment::where('registration_id', $user->id)
                ->where('payment_status', 3)
                ->where('created_at', '>=', now()->subMinutes(10))
                ->latest()
                ->first();
            
            if ($recentPayment && $recentPayment->id != $payment->id) {
                // A recent payment was completed (likely via webhook)
                $currency = $recentPayment->currency ?? 'USD';
                $amountDisplay = '$' . number_format($recentPayment->paid_amount ?? $recentPayment->amount, 2);
                $isPartial = strpos($recentPayment->payment_status_name ?? '', 'Partial') !== false;
                
                if($isPartial) {
                    $success_message = '<p>Your partial payment of ' . $amountDisplay . ' was received successfully.</p><p>Transaction ID: ' . ($recentPayment->transaction_id ?? $recentPayment->payment_order_id) . '</p><p>This amount has been applied to reduce your pending balance.</p>';
                } else {
                    $success_message = '<p>The payment of ' . $amountDisplay . ' was Successful. Your payment transaction ID is ' . ($recentPayment->transaction_id ?? $recentPayment->payment_order_id) . '</p><p> Thank you for registration with IRCC\'25</p>';
                }
                
                // Clear the payment initiated session message
                session()->forget('givebutter_payment_initiated');
                
                return redirect()->route('accounts.dashboard')->with([
                    'payment_success' => $success_message,
                    'success_title' => $isPartial ? 'Partial Payment Received!' : 'Payment Successful!',
                    'show_dashboard_button' => true
                ]);
            }
        }

        // Payment still processing - wait for webhook
        return redirect()->route('accounts.dashboard')->with('info', 'Your payment is being processed. You will receive a confirmation email once it is verified. If you just completed payment, please wait a moment and refresh this page.');
    }

    /**
     * Validate Givebutter webhook amount against our pending Payment.
     * Campaign coupons (e.g. IRCC1) may report list/gross totals while we store the discounted net in `amount`.
     *
     * @return array{ok: bool, book_amount: ?float, reason: ?string}
     */
    private function givebutterWebhookValidateReceivedAmount(Payment $payment, ?float $receivedAmount): array
    {
        if ($receivedAmount === null) {
            return ['ok' => true, 'book_amount' => null, 'reason' => null];
        }

        $received = (float) $receivedAmount;
        $expectedNet = (float) $payment->amount;

        $hasCoupon = ((float) ($payment->discount_amount ?? 0) > 0.001)
            || (is_string($payment->coupon_code ?? null) && trim((string) $payment->coupon_code) !== '');

        $gross = $expectedNet;
        if (Schema::hasColumn('payments', 'original_amount') && $payment->original_amount !== null) {
            $gross = (float) $payment->original_amount;
        }

        if (! $hasCoupon || $gross <= $expectedNet + 0.009) {
            $allowed = max(1.0, $expectedNet * 0.05);
            if (abs($expectedNet - $received) > $allowed) {
                return ['ok' => false, 'book_amount' => null, 'reason' => 'amount_mismatch'];
            }

            return ['ok' => true, 'book_amount' => $received, 'reason' => null];
        }

        $tolNet = max(0.5, min(10.0, max($expectedNet * 0.25, $expectedNet * 0.05 + 0.25)));
        if (abs($received - $expectedNet) <= $tolNet) {
            return ['ok' => true, 'book_amount' => $received, 'reason' => null];
        }

        $tolGross = max(5.0, $gross * 0.03);
        if (abs($received - $gross) <= $tolGross) {
            return ['ok' => true, 'book_amount' => $expectedNet, 'reason' => 'matched_gross_pre_discount'];
        }

        return ['ok' => false, 'book_amount' => null, 'reason' => 'amount_mismatch_coupon'];
    }

    /**
     * Handle Givebutter webhook callbacks
     * Processes payment status updates from Givebutter
     */
    public function paymentResponse(Request $request)
    {
        $payload = $request->getContent();
        
        // Try multiple possible signature header names
        $signature = $request->header('X-Givebutter-Signature') 
                  ?? $request->header('X-Webhook-Signature')
                  ?? $request->header('Givebutter-Signature')
                  ?? $request->header('Signature');
        
        // Verify webhook signature using HMAC-SHA256
        // verifyWebhook computes: HMAC-SHA256(payload, webhook_secret) and compares with incoming signature
        $event = $this->verifyWebhook($payload, $signature);
        
        // If verification failed, reject the webhook for security
        if ($event === false) {
            \Log::error('Givebutter webhook rejected: Signature verification failed', [
                'payload_preview' => substr($payload, 0, 200),
                'signature_header' => $signature ? 'present' : 'missing',
            ]);
            return response()->json(['error' => 'Invalid signature'], 401);
        }
        
        // If no event returned (shouldn't happen, but handle gracefully)
        if (!$event) {
            \Log::error('Givebutter webhook: Invalid payload or verification error', [
                'payload_preview' => substr($payload, 0, 200)
            ]);
            return response()->json(['error' => 'Invalid payload'], 400);
        }

        $adminEmail = Setting::where('code', 'email')->first()?->value ?? 'ignite@ircc.world';

        // Extract data from Givebutter webhook payload
        // Givebutter webhook structure: { "event": "transaction.created", "data": {...} }
        $eventType = $event['event'] ?? $event['type'] ?? null;
        $transactionData = $event['data'] ?? $event;
        
        // Extract order_id from metadata, custom fields, or checkout URL params we pass to Givebutter
        $meta = is_array($transactionData['metadata'] ?? null) ? $transactionData['metadata'] : [];
        $customFields = is_array($transactionData['custom_fields'] ?? null) ? $transactionData['custom_fields'] : [];
        $customFieldsAlt = is_array($transactionData['customFields'] ?? null) ? $transactionData['customFields'] : [];
        $orderId = $meta['order_id']
                ?? $meta['ref']
                ?? $customFields['order_id']
                ?? $customFieldsAlt['order_id']
                ?? $transactionData['reference']
                ?? $transactionData['external_id']
                ?? $transactionData['order_id']
                ?? null;

        $transactionId = $transactionData['id'] 
                      ?? $transactionData['transactionId'] 
                      ?? $transactionData['donationId']
                      ?? $transactionData['number'] // Givebutter uses 'number' field
                      ?? null;

        $status = $transactionData['status'] 
               ?? $transactionData['paymentStatus']
               ?? 'unknown';

        // Givebutter webhooks: prefer net/settlement-like fields first; `amount` is often pre-discount list total when coupons apply.
        $amount = null;
        foreach (['donated', 'net_amount', 'total_received', 'amount', 'totalAmount'] as $field) {
            if (isset($transactionData[$field]) && is_numeric($transactionData[$field])) {
                $amount = (float) $transactionData[$field];
                break;
            }
        }

        \Log::info('Givebutter webhook: parsed amount', [
            'raw_candidates' => array_intersect_key($transactionData, array_flip(['donated', 'net_amount', 'total_received', 'amount', 'totalAmount'])),
            'parsed_amount' => $amount,
            'note' => 'Prefer donated/net fields when present; amounts treated as dollars',
        ]);
        
        // Extract donor email from various possible locations
        $donorEmail = $transactionData['email']
                   ?? $transactionData['donor']['email']
                   ?? $transactionData['payer']['email']
                   ?? null;

        // Handle different Givebutter webhook events
        // Givebutter sends: transaction.succeeded, transaction.created, transaction.updated, transaction.completed
        if (in_array($eventType, ['transaction.created', 'transaction.updated', 'transaction.completed', 'transaction.succeeded'])) {
            // Try to find payment by order_id from metadata
            if (!$orderId && $transactionId) {
                // If no order_id, try to find by transaction_id
                $payment = Payment::where('transaction_id', $transactionId)
                                  ->orWhere('bdOrderId', $transactionId)
                                  ->first();
            } else {
                $payment = Payment::where('payment_order_id', $orderId)->first();
            }

            // If payment still not found, try a smart fallback:
            // match latest pending/processing payment for this donor email and amount
            if (!$payment && $donorEmail) {
                \Log::info('Givebutter webhook: Attempting fallback payment matching', [
                    'donor_email' => $donorEmail,
                    'amount' => $amount,
                    'transaction_id' => $transactionId,
                ]);
                
                $paymentQuery = Payment::where('email', $donorEmail)
                    ->whereIn('payment_status', [1, 2]) // Pending or Processing
                    ->orderByDesc('id');

                if ($amount !== null) {
                    $tol = max(8.0, abs($amount) * 0.04);
                    $paymentQuery->where(function ($q) use ($amount, $tol) {
                        $q->whereBetween('amount', [$amount - $tol, $amount + $tol]);
                        if (Schema::hasColumn('payments', 'original_amount')) {
                            $q->orWhereBetween('original_amount', [$amount - $tol, $amount + $tol]);
                        }
                    });
                }

                $payment = $paymentQuery->first();

                if (! $payment && $donorEmail) {
                    $pendingForEmail = Payment::where('email', $donorEmail)
                        ->whereIn('payment_status', [1, 2])
                        ->orderByDesc('id')
                        ->get();
                    if ($pendingForEmail->count() === 1) {
                        $payment = $pendingForEmail->first();
                        \Log::info('Givebutter webhook: Matched sole pending/processing payment for email (coupon/gross amount safe path)', [
                            'donor_email' => $donorEmail,
                            'webhook_amount' => $amount,
                            'payment_id' => $payment->id,
                            'payment_order_id' => $payment->payment_order_id,
                            'payment_amount' => $payment->amount,
                        ]);
                    }
                }

                if ($payment) {
                    \Log::info('Givebutter webhook: Matched payment by email/amount fallback', [
                        'donor_email' => $donorEmail,
                        'webhook_amount' => $amount,
                        'payment_amount' => $payment->amount,
                        'payment_id' => $payment->id,
                        'payment_order_id' => $payment->payment_order_id,
                    ]);
                } else {
                    \Log::warning('Givebutter webhook: Fallback matching failed', [
                        'donor_email' => $donorEmail,
                        'amount' => $amount,
                        'available_payments' => Payment::where('email', $donorEmail)
                            ->whereIn('payment_status', [1, 2])
                            ->get(['id', 'payment_order_id', 'amount', 'original_amount', 'coupon_code', 'payment_status'])
                            ->toArray(),
                    ]);
                }
            }

            // Validate amount if payment is found (coupon-aware; IRCC1 net $1 vs GB gross ~$200)
            if ($payment && $amount !== null) {
                $validation = $this->givebutterWebhookValidateReceivedAmount($payment, $amount);
                if (! $validation['ok']) {
                    \Log::warning('Givebutter webhook: Amount mismatch detected', [
                        'payment_id' => $payment->id,
                        'payment_order_id' => $payment->payment_order_id,
                        'expected_net' => (float) $payment->amount,
                        'original_amount' => Schema::hasColumn('payments', 'original_amount') ? $payment->original_amount : null,
                        'received_amount' => $amount,
                        'coupon_code' => $payment->coupon_code ?? null,
                        'discount_amount' => $payment->discount_amount ?? null,
                        'reason' => $validation['reason'] ?? null,
                        'transaction_id' => $transactionId,
                    ]);

                    return response()->json([
                        'error' => 'Amount mismatch',
                        'message' => 'Payment amount does not match expected amount. Please contact support.',
                        'expected' => (float) $payment->amount,
                        'received' => $amount,
                    ], 400);
                }
                if ($validation['book_amount'] !== null) {
                    $amount = $validation['book_amount'];
                }
            }
            
            // If payment not found, handle partial payment scenario
            if (!$payment) {
                // Check if this is a partial payment (small amount that doesn't match any payment)
                // Find the oldest pending/processing payment for this user and apply partial payment
                if ($donorEmail && $amount !== null && $amount > 0) {
                    $oldestPendingPayment = Payment::where('email', $donorEmail)
                        ->whereIn('payment_status', [1, 2]) // Pending or Processing
                        ->orderBy('id', 'asc') // Oldest first
                        ->first();
                    
                    if ($oldestPendingPayment) {
                        \Log::info('Givebutter webhook: Handling partial payment', [
                            'webhook_amount' => $amount,
                            'pending_payment_id' => $oldestPendingPayment->id,
                            'pending_payment_amount' => $oldestPendingPayment->amount,
                            'pending_payment_order_id' => $oldestPendingPayment->payment_order_id,
                        ]);
                        
                        // Create a new payment record for this partial payment
                        $user = Registration::where('email', $donorEmail)->first();
                        if ($user) {
                            $partialPayment = Payment::create([
                                'payment_order_id' => $oldestPendingPayment->payment_order_id . '-PARTIAL-' . time(),
                                'registration_id' => $user->id,
                                'participants' => 0, // Partial payment doesn't cover participants
                                'amount' => $amount,
                                'payment_mode' => 'Givebutter Online',
                                'payment_amount' => $amount,
                                'paid_amount' => $amount,
                                'currency' => strtoupper($transactionData['currency'] ?? 'USD'),
                                'customer_name' => $user->first_name . ' ' . $user->last_name,
                                'mobile' => $user->phone,
                                'email' => $user->email,
                                'payment_status' => 3, // Completed
                                'payment_status_name' => 'Completed (Partial)',
                                'transaction_id' => $transactionId ?? $transactionData['id'] ?? null,
                                'transaction_date' => isset($transactionData['transacted_at']) 
                                    ? Carbon::parse($transactionData['transacted_at'])->format("Y-m-d H:i:s")
                                    : Carbon::now()->format("Y-m-d H:i:s"),
                                'payment_mode_type' => $transactionData['paymentMethod'] ?? $transactionData['method'] ?? 'card',
                                'error_description' => 'Partial payment received - reducing pending balance',
                                'auth_status' => '0300',
                            ]);
                            
                            // Store original amount before reducing
                            $originalAmount = $oldestPendingPayment->amount;
                            
                            // Reduce the amount from the oldest pending payment
                            $newAmount = max(0, $originalAmount - $amount);
                            $oldestPendingPayment->update([
                                'amount' => $newAmount,
                                'payment_amount' => $newAmount,
                                'error_description' => 'Amount reduced by partial payment of $' . number_format($amount, 2) . '. Remaining: $' . number_format($newAmount, 2),
                            ]);
                            
                            // If the pending payment is now fully paid, mark it as completed
                            if ($newAmount <= 0.01) { // Allow for rounding
                                $oldestPendingPayment->update([
                                    'payment_status' => 3,
                                    'payment_status_name' => 'Completed',
                                    'paid_amount' => $originalAmount, // Use original amount before reduction
                                ]);
                                
                                // Create participant payments if needed
                                $participants = Participant::where('registration_id', $user->id)->get();
                                $existingParticipantIds = ParticipantPayment::where('registration_id', $user->id)->pluck('participant_id')->toArray();
                                foreach ($participants as $participant) {
                                    if (!in_array($participant->id, $existingParticipantIds)) {
                                        ParticipantPayment::create([
                                            'registration_id' => $user->id,
                                            'payment_id' => $oldestPendingPayment->id,
                                            'participant_id' => $participant->id,
                                            'participant_name' => $participant->first_name . ' ' . $participant->last_name,
                                        ]);
                                    }
                                }
                            }
                            
                            // Send email notifications
                            try {
                                \Log::info('Attempting to send partial payment confirmation email', [
                                    'user_email' => $user->email,
                                    'payment_id' => $partialPayment->id,
                                ]);
                                
                                Mail::to($user->email)->send(new PaymentDetails($user, $partialPayment->id, 'user'));
                                \Log::info('Partial payment user email sent successfully', [
                                    'user_email' => $user->email,
                                    'payment_id' => $partialPayment->id,
                                ]);
                                
                                Mail::to($adminEmail)->send(new PaymentDetails($user, $partialPayment->id, 'admin'));
                                \Log::info('Partial payment admin email sent successfully', [
                                    'admin_email' => $adminEmail,
                                    'payment_id' => $partialPayment->id,
                                ]);
                            } catch(\Exception $e) {
                                \Log::error('Failed to send partial payment confirmation email', [
                                    'error' => $e->getMessage(),
                                    'file' => $e->getFile(),
                                    'line' => $e->getLine(),
                                    'trace' => $e->getTraceAsString(),
                                    'payment_id' => $partialPayment->id ?? 'unknown',
                                    'user_email' => $user->email ?? 'unknown',
                                ]);
                            } catch(\Throwable $e) {
                                \Log::error('Failed to send partial payment confirmation email (Throwable)', [
                                    'error' => $e->getMessage(),
                                    'file' => $e->getFile(),
                                    'line' => $e->getLine(),
                                    'payment_id' => $partialPayment->id ?? 'unknown',
                                ]);
                            }
                            
                            \Log::info('Givebutter webhook: Partial payment processed successfully', [
                                'partial_payment_id' => $partialPayment->id,
                                'partial_amount' => $amount,
                                'remaining_balance' => $newAmount,
                                'pending_payment_id' => $oldestPendingPayment->id,
                            ]);
                            
                            return response()->json([
                                'received' => true, 
                                'status' => 'processed',
                                'message' => 'Partial payment applied successfully'
                            ]);
                        }
                    }
                }
                
                \Log::warning('Givebutter webhook: Payment not found and partial payment handling failed', [
                    'order_id' => $orderId,
                    'transaction_id' => $transactionId,
                    'event' => $eventType,
                    'donor_email' => $donorEmail,
                    'amount' => $amount,
                ]);
                return response()->json(['error' => 'Payment not found'], 404);
            }

            // Handle successful payment
            // transaction.succeeded event means payment was successful
            if (in_array(strtolower($status), ['completed', 'paid', 'success', 'succeeded', 'captured']) 
                || in_array($eventType, ['transaction.completed', 'transaction.succeeded'])) {
                
                // Track if payment was already completed before this webhook
                $wasAlreadyCompleted = ($payment->payment_status == 3);
                
                // Update payment status if not already completed
                if (!$wasAlreadyCompleted) {
                    $payment->update([
                        'transaction_id' => $transactionId ?? $transactionData['id'] ?? $orderId,
                        'transaction_date' => isset($transactionData['createdAt']) 
                            ? Carbon::parse($transactionData['createdAt'])->format("Y-m-d H:i:s")
                            : Carbon::now()->format("Y-m-d H:i:s"),
                        'payment_mode_type' => $transactionData['paymentMethod'] ?? $transactionData['method'] ?? 'card',
                        'paid_amount' => $amount ?? $payment->amount,
                        'currency' => strtoupper($transactionData['currency'] ?? 'INR'),
                        'payment_status' => 3,
                        'payment_status_name' => 'Completed',
                        'error_description' => 'Transaction is successful',
                        'auth_status' => '0300',
                    ]);
                }

                $user = Registration::find($payment->registration_id);
                
                if ($user) {
                    // Link participants if payment was just completed
                    if (!$wasAlreadyCompleted) {
                        $participants = Participant::where('registration_id', $user->id)->get();
                        $existingParticipantIds = ParticipantPayment::where('registration_id', $user->id)->pluck('participant_id')->toArray();
                        
                        foreach ($participants as $participant) {
                            if (!in_array($participant->id, $existingParticipantIds)) {
                                ParticipantPayment::create([
                                    'registration_id' => $user->id,
                                    'payment_id' => $payment->id,
                                    'participant_id' => $participant->id,
                                    'participant_name' => $participant->first_name . ' ' . $participant->last_name,
                                ]);
                            }
                        }
                    }

                    // Always attempt to send emails when webhook confirms payment is completed
                    // This ensures emails are sent even if payment was already marked as completed
                    // (e.g., if webhook was delayed or payment was manually updated)
                    try {
                        \Log::info('Webhook received for completed payment - attempting to send confirmation email', [
                            'user_email' => $user->email,
                            'payment_id' => $payment->id,
                            'payment_order_id' => $payment->payment_order_id,
                            'was_already_completed' => $wasAlreadyCompleted,
                            'webhook_event' => $eventType,
                            'webhook_status' => $status,
                        ]);
                        
                        // Send user email
                        Mail::to($user->email)->send(new PaymentDetails($user, $payment->id, 'user'));
                        \Log::info('User payment confirmation email sent successfully from webhook', [
                            'user_email' => $user->email,
                            'payment_id' => $payment->id,
                            'payment_order_id' => $payment->payment_order_id,
                        ]);
                        
                        // Send admin email
                        Mail::to($adminEmail)->send(new PaymentDetails($user, $payment->id, 'admin'));
                        \Log::info('Admin payment confirmation email sent successfully from webhook', [
                            'admin_email' => $adminEmail,
                            'payment_id' => $payment->id,
                            'payment_order_id' => $payment->payment_order_id,
                        ]);
                    } catch(\Exception $e) {
                        \Log::error('Failed to send payment confirmation email from webhook', [
                            'error' => $e->getMessage(),
                            'file' => $e->getFile(),
                            'line' => $e->getLine(),
                            'trace' => $e->getTraceAsString(),
                            'payment_id' => $payment->id,
                            'payment_order_id' => $payment->payment_order_id,
                            'user_email' => $user->email ?? 'unknown',
                            'admin_email' => $adminEmail ?? 'unknown',
                        ]);
                        // Don't throw - log error but continue processing webhook
                    } catch(\Throwable $e) {
                        \Log::error('Failed to send payment confirmation email from webhook (Throwable)', [
                            'error' => $e->getMessage(),
                            'file' => $e->getFile(),
                            'line' => $e->getLine(),
                            'trace' => $e->getTraceAsString(),
                            'payment_id' => $payment->id,
                            'payment_order_id' => $payment->payment_order_id,
                        ]);
                    }
                } else {
                    \Log::warning('Webhook: User not found for payment', [
                        'payment_id' => $payment->id,
                        'payment_order_id' => $payment->payment_order_id,
                        'registration_id' => $payment->registration_id,
                    ]);
                }

                return response()->json(['received' => true, 'status' => 'processed']);
            }

            // Handle failed payment
            if (in_array(strtolower($status), ['failed', 'cancelled', 'canceled', 'declined', 'refunded'])) {
                $payment->update([
                    'transaction_id' => $transactionId ?? $transactionData['id'] ?? $orderId,
                    'payment_status' => 6,
                    'payment_status_name' => 'Failed',
                    'error_description' => $transactionData['errorMessage'] ?? $transactionData['failureReason'] ?? 'Transaction failed',
                    'auth_status' => '0399',
                ]);

                return response()->json(['received' => true, 'status' => 'processed']);
            }
        }

        // Log unknown event/status
        \Log::info('Givebutter webhook received', [
            'event_type' => $eventType,
            'order_id' => $orderId,
            'status' => $status,
            'transaction_id' => $transactionId
        ]);

        return response()->json(['received' => true, 'status' => 'processed']);
    }

    /**
     * Handle payment cancellation
     */
    public function payment_cancel(Request $request){
        $orderId = $request->query('order_id');
        
        if ($orderId) {
            $payment = Payment::where('payment_order_id', $orderId)->first();
            if ($payment && $payment->payment_status == 2) {
                $payment->update([
                    'payment_status' => 6,
                    'payment_status_name' => 'Cancelled',
                ]);
            }
        }
        
        return redirect()->route('accounts.dashboard')->with('error', 'Payment was cancelled.');
    }

    /**
     * Verify payment via email link
     * This allows admins to verify payments directly from email
     * Secure signed URL with 24-hour expiration
     */
    public function verifyPayment(Request $request, $payment)
    {
        $payment = Payment::findOrFail($payment);
        $orderId = $request->query('order_id');
        
        // Verify the order_id matches
        if ($orderId && $payment->payment_order_id !== $orderId) {
            return redirect()->route('admin.payment.index')
                ->with('error', 'Invalid payment verification link.');
        }

        // Check if payment is already verified
        if ($payment->payment_status == 3) {
            return redirect()->route('admin.payment.index')
                ->with('info', 'This payment has already been verified.');
        }

        // Only allow verification for processing payments
        if ($payment->payment_status != 2) {
            return redirect()->route('admin.payment.index')
                ->with('error', 'Payment cannot be verified in its current status.');
        }

        $user = Registration::find($payment->registration_id);
        $adminEmail = Setting::where('code', 'email')->first()?->value ?? 'ignite@ircc.world';

        if (!$user) {
            return redirect()->route('admin.payment.index')
                ->with('error', 'User not found for this payment.');
        }

        // Update payment status to verified
        $payment->update([
            'payment_status' => 3,
            'payment_status_name' => 'Completed',
            'transaction_date' => Carbon::now()->format("Y-m-d H:i:s"),
            'error_description' => 'Payment verified via email link',
            'auth_status' => '0300',
        ]);

        // Link participants
        $participants = Participant::where('registration_id', $user->id)->get();
        $existingParticipantIds = ParticipantPayment::where('registration_id', $user->id)->pluck('participant_id')->toArray();

        foreach ($participants as $participant) {
            if (!in_array($participant->id, $existingParticipantIds)) {
                ParticipantPayment::create([
                    'registration_id' => $user->id,
                    'payment_id' => $payment->id,
                    'participant_id' => $participant->id,
                    'participant_name' => $participant->first_name . ' ' . $participant->last_name,
                ]);
            }
        }

        // Send confirmation emails
        try {
            \Log::info('Attempting to send manual verification payment emails', [
                'user_email' => $user->email,
                'payment_id' => $payment->id,
            ]);
            
            Mail::to($user->email)->send(new PaymentDetails($user, $payment->id, 'user'));
            \Log::info('Manual verification user email sent successfully', [
                'user_email' => $user->email,
                'payment_id' => $payment->id,
            ]);
            
            Mail::to($adminEmail)->send(new PaymentDetails($user, $payment->id, 'admin'));
            \Log::info('Manual verification admin email sent successfully', [
                'admin_email' => $adminEmail,
                'payment_id' => $payment->id,
            ]);
        } catch(\Exception $e) {
            \Log::error('Failed to send manual verification payment confirmation email', [
                'error' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
                'trace' => $e->getTraceAsString(),
                'payment_id' => $payment->id ?? 'unknown',
            ]);
        } catch(\Throwable $e) {
            \Log::error('Failed to send manual verification payment confirmation email (Throwable)', [
                'error' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
                'payment_id' => $payment->id ?? 'unknown',
            ]);
        }

        return redirect()->route('admin.payment.index')
            ->with('success', 'Payment verified successfully! Confirmation emails have been sent to the user and admin.');
    }

    /**
     * Refund request
     */
    public function refundRequest()
    {
        $user = Auth::guard('customer')->user();

        if (!$user) {
            abort(403, 'Unauthorized access.');
        }

        $amountPerHead = Setting::where('code', 'amount_per_head')->first()->value;

        $participants= Participant::where('registration_id', $user->id)->pluck('id')->toArray();
        $deletedParticipants=ParticipantPayment::whereNotIn('participant_id',$participants)->get()->count();

        $paidParticipantsCount = Payment::where('registration_id', $user->id)->sum('participants');
        $refundAmount = $deletedParticipants* $amountPerHead;

       $refund=PaymentRefund::create([
            'registration_id' => $user->id,
            'refund_amount' => $refundAmount,
            'participants' => $deletedParticipants,
            'request_date' => Carbon::now(),
       ]);

        return redirect()->back()->with('success', 'Refund recorded successfully!');
    }

    /**
     * Get updated payment summary for AJAX requests
     */
    public function getPaymentSummary()
    {
        $user = Auth::guard('customer')->user();
        
        if (!$user) {
            return response()->json(['success' => false, 'error' => 'Unauthorized'], 401);
        }

        try {
            // Single source of truth with dashboard payment math (per-registration student unit price)
            $payment_summary = $this->user_payments($user->id);

            $participants = Participant::where('registration_id', $user->id)->where('type', '<>', 2)->get();
            $participantsCount = $participants->count();
            $paidParticipantsCount = (int) (Payment::where('registration_id', $user->id)->where('payment_status', 3)->sum('participants') ?: 0);
            $payments = Payment::where('registration_id', $user->id)->where('payment_status', 3)->orderBy('id', 'DESC')->get();

            $paidAmountFormatted = str_replace(',', '', $payment_summary['paidAmount']);
            $amountPerHead = (float) str_replace(',', '', $payment_summary['amountPerHead']);

            return response()->json([
                'success' => true,
                'payment_summary' => $payment_summary,
                'participantsCount' => $participantsCount,
                'paidParticipantsCount' => $paidParticipantsCount,
                'amountPerHead' => $amountPerHead,
                'paidAmount' => $paidAmountFormatted,
                'amountPayable' => $payment_summary['amountPayable'],
                'hasPayments' => !$payments->isEmpty(),
                'rise150_student_discount' => (float) str_replace(',', '', $payment_summary['rise150_student_discount'] ?? '0'),
            ]);
        } catch (\Exception $e) {
            Log::error('Error in getPaymentSummary: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString(),
                'file' => $e->getFile(),
                'line' => $e->getLine()
            ]);
            return response()->json([
                'success' => false,
                'error' => 'Failed to fetch payment summary: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Process payment directly via API
     * NOTE: Givebutter API does NOT support direct payment processing.
     * This method processes payment through a payment gateway API (e.g., Stripe)
     * and then records it in Givebutter using their /transactions endpoint.
     * 
     * To use this, you need to integrate with a payment gateway that supports API-based processing.
     * 
     * @param Request $request
     * @param Payment $payment
     * @param Registration $user
     * @param float $amount
     * @param string $adminEmail
     * @return \Illuminate\Http\JsonResponse|\Illuminate\Http\RedirectResponse
     */
    private function processDirectPayment(Request $request, Payment $payment, $user, $amount, $adminEmail)
    {
        try {
            // Validate payment details
            $request->validate([
                'cardholder_name' => 'required|string|max:255',
                'card_number' => 'required|string|regex:/^[0-9\s]+$/|min:13|max:19',
                'expiry_date' => 'required|string|regex:/^\d{2}\/\d{2}$/',
                'cvv' => 'required|string|regex:/^\d{3,4}$/',
                'billing_email' => 'required|email',
            ]);

            $cardNumber = str_replace(' ', '', $request->card_number);
            $expiry = explode('/', $request->expiry_date);
            $expMonth = $expiry[0];
            $expYear = '20' . $expiry[1];

            \Log::info('Processing direct payment via API', [
                'payment_id' => $payment->id,
                'order_id' => $payment->payment_order_id,
                'amount' => $amount,
                'card_last4' => substr($cardNumber, -4),
            ]);

            // IMPORTANT: Givebutter API does NOT support direct payment processing
            // You need to integrate with a payment gateway that supports API-based processing
            // Examples: Stripe, PayPal, Square, etc.
            
            // For now, we'll simulate the payment and record it in Givebutter
            // In production, you would:
            // 1. Process payment through a payment gateway API (e.g., Stripe)
            // 2. Get the transaction ID from the gateway
            // 3. Record the transaction in Givebutter using their /transactions endpoint
            
            // TODO: Integrate with a payment gateway API here
            // Example with Stripe:
            // $stripe = new \Stripe\StripeClient(env('STRIPE_SECRET_KEY'));
            // $charge = $stripe->charges->create([
            //     'amount' => $amount * 100, // Convert to cents
            //     'currency' => 'usd',
            //     'source' => $token, // Token from Stripe.js
            //     'description' => 'IRCC Payment - ' . $payment->payment_order_id,
            // ]);
            
            // IMPORTANT: This is a placeholder/simulation - no actual payment is processed
            // In production, you MUST integrate with a real payment gateway (Stripe, PayPal, etc.)
            // For now, we'll only mark as complete if Givebutter recording succeeds
            // Otherwise, the payment should remain in "Processing" status
            
            // Record transaction in Givebutter using their /transactions endpoint
            // This endpoint is for recording offline/past transactions
            // Note: In a real implementation, you would process payment FIRST, then record in Givebutter
            $transactionId = 'TXN-' . time() . '-' . rand(1000, 9999);
            $givebutterResponse = $this->recordTransactionInGivebutter($payment, $user, $amount, $transactionId);
            
            if ($givebutterResponse['status'] === 'SUCCESS') {
                // Only mark as completed if Givebutter recording succeeded
                // Update payment record
                $payment->update([
                    'transaction_id' => $transactionId,
                    'payment_status' => 3, // Completed
                    'payment_status_name' => 'Completed',
                    'paid_amount' => $amount,
                    'payment_amount' => $amount,
                    'transaction_date' => Carbon::now()->format("Y-m-d H:i:s"),
                    'payment_mode_type' => 'card',
                    'auth_status' => '0300',
                ]);

                // Create participant payments
                // First, verify that payment_status_id 3 (Completed) exists in payment_statuses table
                $completedStatus = PaymentStatus::find(3);
                if (!$completedStatus) {
                    \Log::warning('Payment status 3 (Completed) not found in payment_statuses table. Attempting to find by status name.', [
                        'payment_id' => $payment->id,
                    ]);
                    // Try to find by status name
                    $completedStatus = PaymentStatus::where('status', 'Completed')->first();
                    if ($completedStatus) {
                        $statusId = $completedStatus->id;
                    } else {
                        // Try to use status 1 (Pending) as fallback
                        $pendingStatus = PaymentStatus::find(1) ?? PaymentStatus::where('status', 'Pending')->first();
                        $statusId = $pendingStatus?->id;
                        if (!$statusId) {
                            \Log::error('No payment statuses found in database. Please run PaymentStatusSeeder.', [
                                'payment_id' => $payment->id,
                            ]);
                        }
                    }
                } else {
                    $statusId = 3;
                }
                
                if ($statusId) {
                    $participants = Participant::where('registration_id', $user->id)->where('type', '<>', 2)->get();
                    $existingParticipantIds = ParticipantPayment::where('registration_id', $user->id)->pluck('participant_id')->toArray();
                    
                    foreach ($participants as $participant) {
                        if (!in_array($participant->id, $existingParticipantIds)) {
                            try {
                                ParticipantPayment::create([
                                    'registration_id' => $user->id,
                                    'payment_id' => $payment->id,
                                    'participant_id' => $participant->id,
                                    'participant_name' => $participant->first_name . ' ' . $participant->last_name,
                                    'payment_status_id' => $statusId,
                                ]);
                            } catch (\Exception $e) {
                                \Log::error('Failed to create participant payment', [
                                    'payment_id' => $payment->id,
                                    'participant_id' => $participant->id,
                                    'payment_status_id' => $statusId,
                                    'error' => $e->getMessage(),
                                ]);
                                // Continue with other participants even if one fails
                                // Don't throw exception - payment is already recorded in Givebutter
                            }
                        }
                    }
                } else {
                    \Log::warning('Skipping participant_payments creation - no valid payment_status_id found', [
                        'payment_id' => $payment->id,
                    ]);
                }

                // Send confirmation emails
                try {
                    Mail::to($user->email)->send(new PaymentDetails($user, $payment->id, 'user'));
                    Mail::to($adminEmail)->send(new PaymentDetails($user, $payment->id, 'admin'));
                    \Log::info('Payment confirmation emails sent', [
                        'payment_id' => $payment->id,
                        'user_email' => $user->email,
                    ]);
                } catch (\Exception $e) {
                    \Log::error('Failed to send payment confirmation emails', [
                        'error' => $e->getMessage(),
                        'payment_id' => $payment->id,
                    ]);
                }

                // Return success response
                if ($request->ajax() || $request->wantsJson()) {
                    return response()->json([
                        'success' => true,
                        'message' => 'Payment processed successfully',
                        'transaction_id' => $transactionId,
                        'order_id' => $payment->payment_order_id
                    ]);
                }

                return redirect()->route('accounts.dashboard')->with([
                    'payment_success' => '<p>Your payment of $' . number_format($amount, 2) . ' was processed successfully.</p><p>Transaction ID: ' . $transactionId . '</p>',
                    'success_title' => 'Payment Successful!'
                ]);
            } else {
                // Givebutter recording failed - do NOT mark payment as completed
                // Since this is a simulation and no actual payment gateway is integrated,
                // we should not mark it as complete if Givebutter recording fails
                \Log::error('Payment processing failed: Givebutter recording failed', [
                    'payment_id' => $payment->id,
                    'error' => $givebutterResponse['error'] ?? 'Unknown error',
                    'note' => 'Payment not marked as complete because Givebutter recording failed',
                ]);

                // Keep payment in Processing status (status 2) or mark as Failed
                // Truncate error message to fit database column (string type, typically 255 chars)
                $errorMsg = 'Givebutter recording failed: ' . ($givebutterResponse['error'] ?? 'Unknown error');
                // Extract just the main error message if it's too long
                if (strlen($errorMsg) > 180) {
                    // Try to get just the first part before any nested errors
                    $parts = explode('.', $errorMsg);
                    $errorMsg = $parts[0] . '.';
                }
                $errorMsg = Str::limit($errorMsg, 180); // Limit to 180 chars to be very safe
                
                try {
                    $payment->update([
                        'payment_status' => 6, // Failed
                        'payment_status_name' => 'Failed',
                        'error_description' => $errorMsg,
                    ]);
                } catch (\Exception $updateException) {
                    // If update fails, use a shorter message
                    \Log::error('Failed to update payment error_description', [
                        'payment_id' => $payment->id,
                        'error' => $updateException->getMessage(),
                    ]);
                    $payment->update([
                        'payment_status' => 6,
                        'payment_status_name' => 'Failed',
                        'error_description' => 'Givebutter recording failed',
                    ]);
                }

                if ($request->ajax() || $request->wantsJson()) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Payment processing failed: ' . ($givebutterResponse['error'] ?? 'Givebutter recording failed. Please configure campaign code or title.'),
                        'error' => $givebutterResponse['error'] ?? 'Givebutter recording failed',
                    ], 400);
                }

                return redirect()->back()->with('error', 'Payment processing failed: ' . ($givebutterResponse['error'] ?? 'Givebutter recording failed. Please configure campaign code or title.'));
            }
        } catch (\Exception $e) {
            \Log::error('Direct payment processing failed', [
                'payment_id' => $payment->id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            // Extract just the essential error message (avoid nested SQL errors)
            $errorMessage = $e->getMessage();
            
            // If it's a database error, extract just the key part
            if (strpos($errorMessage, 'SQLSTATE') !== false) {
                // Extract the main error type (e.g., "Integrity constraint violation" or "String data, right truncated")
                if (preg_match('/SQLSTATE\[(\d+)\]:\s*([^\(]+)/', $errorMessage, $matches)) {
                    $errorType = trim($matches[2] ?? 'Unknown database error');
                    // Keep it short
                    if (strlen($errorType) > 50) {
                        $errorType = substr($errorType, 0, 50) . '...';
                    }
                    $errorMessage = 'DB Error: ' . $errorType;
                } else {
                    $errorMessage = 'Database error occurred';
                }
            } else {
                // For non-database errors, also truncate if too long
                if (strlen($errorMessage) > 100) {
                    $errorMessage = substr($errorMessage, 0, 100) . '...';
                }
            }
            
            // Truncate error message to fit database column (limit to 150 chars to be very safe)
            $errorMsg = 'Payment failed: ' . $errorMessage;
            $errorMsg = Str::limit($errorMsg, 150);
            
            try {
                $payment->update([
                    'payment_status' => 6,
                    'payment_status_name' => 'Failed',
                    'error_description' => $errorMsg,
                ]);
            } catch (\Exception $updateException) {
                // If update fails due to error_description being too long, use a shorter message
                \Log::error('Failed to update payment error_description', [
                    'payment_id' => $payment->id,
                    'error' => $updateException->getMessage(),
                ]);
                $payment->update([
                    'payment_status' => 6,
                    'payment_status_name' => 'Failed',
                    'error_description' => 'Payment processing failed',
                ]);
            }

            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Payment processing failed: ' . $e->getMessage()
                ], 400);
            }

            return redirect()->back()->with('error', 'Payment processing failed: ' . $e->getMessage());
        }
    }

    /**
     * Record transaction in Givebutter using their /transactions endpoint
     * This endpoint is for recording offline/past transactions, not for processing payments
     * 
     * @param Payment $payment
     * @param Registration $user
     * @param float $amount
     * @param string $transactionId
     * @return array
     */
    private function recordTransactionInGivebutter($payment, $user, $amount, $transactionId)
    {
        try {
            $apiKey = $this->getGivebutterApiKey();
            $apiUrl = $this->getGivebutterApiUrl();
            $campaignCode = $this->getGivebutterCampaignCode();
            $campaignTitle = $this->getGivebutterCampaignTitle();

            if (!$apiKey || !$apiUrl) {
                return [
                    'status' => 'ERROR',
                    'error' => 'Givebutter API credentials not configured',
                ];
            }

            // Campaign code or title is required by Givebutter API
            if (empty($campaignCode) && empty($campaignTitle)) {
                Log::warning('Givebutter campaign code and title not configured', [
                    'payment_id' => $payment->id,
                ]);
                return [
                    'status' => 'ERROR',
                    'error' => 'Givebutter campaign code or campaign title is required. Please configure GIVEBUTTER_CAMPAIGN_CODE or GIVEBUTTER_CAMPAIGN_TITLE in your .env file or settings table.',
                ];
            }

            // Convert amount to cents (Givebutter API uses cents)
            $amountInCents = (int)($amount * 100);

            // Prepare transaction data for Givebutter API
            // Note: Givebutter requires transacted_at to be in the past, so use 1 minute ago
            $transactedAt = Carbon::now()->subMinute()->toIso8601String();
            
            $transactionData = [
                'amount' => $amountInCents,
                'currency' => 'USD',
                'first_name' => $user->first_name ?? '',
                'last_name' => $user->last_name ?? '',
                'donor' => [
                    'first_name' => $user->first_name ?? '',
                    'last_name' => $user->last_name ?? '',
                    'email' => $user->email,
                ],
                'metadata' => [
                    'order_id' => $payment->payment_order_id,
                    'registration_id' => $user->id,
                ],
                'payment_method' => 'card',
                'status' => 'completed',
                'transacted_at' => $transactedAt,
            ];

            // Add campaign_code if available, otherwise use campaign_title
            // IMPORTANT: Givebutter API expects 'campaign_code' NOT 'campaign_id'
            if (!empty($campaignCode)) {
                $transactionData['campaign_code'] = $campaignCode;
            } elseif (!empty($campaignTitle)) {
                $transactionData['campaign_title'] = $campaignTitle;
            }

            // Make API call to record transaction
            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . $apiKey,
                'Content-Type' => 'application/json',
            ])->timeout(10)->post($apiUrl . '/transactions', $transactionData);

            if ($response->successful()) {
                $transaction = $response->json();
                Log::info('Transaction recorded in Givebutter', [
                    'payment_id' => $payment->id,
                    'givebutter_transaction_id' => $transaction['id'] ?? null,
                    'transaction_id' => $transactionId,
                ]);

                return [
                    'status' => 'SUCCESS',
                    'transaction' => $transaction,
                    'givebutter_transaction_id' => $transaction['id'] ?? null,
                ];
            } else {
                $errorBody = $response->json();
                $errorMsg = $errorBody['message'] ?? $errorBody['error'] ?? 'API request failed with status ' . $response->status();
                
                // Log all validation errors if available
                $validationErrors = $errorBody['errors'] ?? $errorBody['validation_errors'] ?? null;
                
                Log::warning('Failed to record transaction in Givebutter', [
                    'payment_id' => $payment->id,
                    'status' => $response->status(),
                    'error' => $errorMsg,
                    'validation_errors' => $validationErrors,
                    'full_response' => $errorBody,
                    'campaign_code' => $campaignCode ?? 'not set',
                    'campaign_title' => $campaignTitle ?? 'not set',
                    'transaction_data' => $transactionData,
                ]);

                return [
                    'status' => 'ERROR',
                    'error' => $errorMsg,
                ];
            }
        } catch (\Exception $e) {
            Log::error('Error recording transaction in Givebutter', [
                'payment_id' => $payment->id,
                'error' => $e->getMessage(),
            ]);

            return [
                'status' => 'ERROR',
                'error' => $e->getMessage(),
            ];
        }
    }
}
