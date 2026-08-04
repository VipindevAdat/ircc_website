@php
    // Ensure payment_summary has all required keys with defaults
    if (!isset($payment_summary)) {
        $payment_summary = [
            'amountPayable' => '0.00',
            'paidAmount' => '0.00',
            'amountPerHead' => '0.00',
            'subTotal' => '0.00',
            'amountPayable' => '0.00'
        ];
    }
    $payment_summary['amountPayable'] = $payment_summary['amountPayable'] ?? '0.00';
    $payment_summary['studentAmountPayable'] = $payment_summary['studentAmountPayable'] ?? '0.00';
    $payment_summary['adultAmountPayable'] = $payment_summary['adultAmountPayable'] ?? '0.00';
    $payment_summary['paidAmount'] = $payment_summary['paidAmount'] ?? '0.00';
    $payment_summary['amountPerHead'] = $payment_summary['amountPerHead'] ?? '0.00';
    $payment_summary['subTotal'] = $payment_summary['subTotal'] ?? '0.00';
@endphp
<h4>Payment Details</h4>
    <div>
        <div id="payment-info" class="mt-3" style="display: {{ $user->participants->isEmpty() ? 'block' : 'none' }}">
            <div style="font-size: 16px; color: #333; text-align: center;">
                <p>
                    Once you add all participants, proceed with payment.<br>
                    <strong>Pricing:</strong> Student Participants: ${{ $payment_summary['studentPrice'] ?? number_format((float) config('ircc.student_price_early', 150), 2) }} | Adult Attendees: ${{ $payment_summary['adultPrice'] ?? '50.00' }}
                </p>
            </div>
        </div>
        <div id="amount-payable-box" class="amount-payable-box row d-flex justify-content-around mt-3">
            @if(isset($payment_summary['amountPayable']) && $payment_summary['amountPayable'] > 0)
                <!-- <div id="payment">
                    @if(!$payments->isEmpty())
                        <p style="margin-bottom: 15px">
                            <span>Amount Paid</span>
                            <span class="float-end">${{ $payment_summary['paidAmount'] }}</span>
                        </p>
                    @endif
                    <p style="font-size: 16px; margin-bottom: 15px">
                        <span>Amount Per Participant</span>
                        <span class="float-end">${{ $payment_summary['amountPerHead'] }}</span>
                    </p>
                    @if($participantCount>0)
                    <p>
                    <span>Number of Participants</span>
                    <span id="participant-count" class="float-end">{{ $participantCount }}</span>
                    </p>
                    @php $pendingCount=$participantCount - $paidParticipantCount;@endphp
                    @if($pendingCount>0)
                    <p style="margin: 0;">
                        <span>Payment Pending For</span>
                        <span id="pending-participant-count" class="float-end">{{ $participantCount - $paidParticipantCount }}</span>
                    </p>
                    @endif
                    @endif
                    @if($payments->isEmpty())
                        <hr style="margin: 10px 0; border-top: 2px solid #f15b43;">
                        @if($payment_summary['subTotal']>0)
                        <p style="margin: 0;">
                            <span style="font-size: 16px; font-weight: bold;">Sub Total</span>
                            <span id="dynamic-total-amount" class="float-end">${{ $payment_summary['subTotal'] }}</span>
                        </p>
                        @endif
                        <p style="margin: 0; font-size: 16px;">
                            <span>Registered user Fee <span style="font-size: 12px"> ( {{$user->email}} )</span></span> 
                            <span class="float-end">+ ${{ number_format($registrationFee,2) }}</span>
                        </p>
                        @if($payment_summary['paidAmount']>0)
                        <p style="margin: 0; font-size: 16px;">
                            <span>Amount Paid <span style="font-size: 12px"></span></span> 
                            <span class="float-end">- ${{ $payment_summary['paidAmount'] }}</span>
                        </p>
                        @endif
                        <hr style="margin: 10px 0; border-top: 2px solid #f15b43;">
                        <p style="margin-bottom: 10px; font-size: 18px; font-weight: bold;">
                            <span>Total Amount Payable</span>
                            <span class="float-end">${{ $payment_summary['amountPayable'] }}</span>
                        </p>
                    @else
                        <hr style="margin: 10px 0; border-top: 2px solid #f15b43;">
                        @if($payment_summary['subTotal']>0)
                        <p style="margin: 0;">
                            <span style="font-size: 16px; font-weight: bold;">Sub Total</span>
                            <span id="dynamic-total-amount" class="float-end">${{ $payment_summary['subTotal'] }}</span>
                        </p>
                        @endif
                        <p style="margin: 0; font-size: 16px;">
                            <span>Registered user Fee <span style="font-size: 12px"> ( {{$user->email}} )</span></span> 
                            <span class="float-end">+ ${{ number_format($registrationFee,2) }}</span>
                        </p>
                        @if($payment_summary['paidAmount']>0)
                        <p style="margin: 0; font-size: 16px;">
                            <span>Amount Paid <span style="font-size: 12px"></span></span> 
                            <span class="float-end">- ${{ $payment_summary['paidAmount'] }}</span>
                        </p>
                        @endif
                        <hr style="margin: 10px 0; border-top: 2px solid #f15b43;">
                        <p style="margin-bottom: 10px; font-size: 18px; font-weight: bold;">
                            <span>Total Amount Payable</span>
                            <span class="float-end">${{ $payment_summary['amountPayable'] }}</span>
                        </p>
                    @endif
                </div> -->
                <div id="payment">
                    @if(!$payments->isEmpty())
                        <p style="margin-bottom: 15px">
                            <span>Amount Paid</span>
                            <span class="float-end">${{ $payment_summary['paidAmount'] ?? '0.00' }}</span>
                        </p>
                    @endif
                    @php
                        $studentCount = isset($payment_summary['studentCount']) ? (int)$payment_summary['studentCount'] : 0;
                        $adultCount = isset($payment_summary['adultCount']) ? (int)$payment_summary['adultCount'] : 0;
                        $studentPrice = $payment_summary['studentPrice'] ?? number_format((float) config('ircc.student_price_early', 150), 2);
                        $studentPriceEarly = $payment_summary['studentPriceEarly'] ?? number_format((float) config('ircc.student_price_early', 150), 2);
                        $studentPriceRegular = $payment_summary['studentPriceRegular'] ?? number_format((float) config('ircc.student_price_regular', 200), 2);
                        $studentCountEarly = isset($payment_summary['pendingStudentCountEarly']) ? (int)$payment_summary['pendingStudentCountEarly'] : (isset($payment_summary['studentCountEarly']) ? (int)$payment_summary['studentCountEarly'] : 0);
                        $studentCountRegular = isset($payment_summary['pendingStudentCountRegular']) ? (int)$payment_summary['pendingStudentCountRegular'] : (isset($payment_summary['studentCountRegular']) ? (int)$payment_summary['studentCountRegular'] : 0);
                        $studentCount = $studentCountEarly + $studentCountRegular;
                        $adultCount = isset($payment_summary['pendingAdultCount']) ? (int)$payment_summary['pendingAdultCount'] : $adultCount;
                        $adultPrice = $payment_summary['adultPrice'] ?? '50.00';
                        $studentSubtotal = isset($payment_summary['studentSubtotal']) ? $payment_summary['studentSubtotal'] : '0.00';
                        $studentSubtotalEarly = $payment_summary['pendingStudentSubtotalEarly'] ?? ($payment_summary['studentSubtotalEarly'] ?? '0.00');
                        $studentSubtotalRegular = $payment_summary['pendingStudentSubtotalRegular'] ?? ($payment_summary['studentSubtotalRegular'] ?? '0.00');
                        $adultSubtotal = isset($payment_summary['pendingAdultSubtotal']) ? $payment_summary['pendingAdultSubtotal'] : (isset($payment_summary['adultSubtotal']) ? $payment_summary['adultSubtotal'] : '0.00');
                    @endphp
                    
                    @if($studentCount > 0 || $adultCount > 0)
                        <p style="font-size: 16px; margin-bottom: 10px; font-weight: bold;">
                            <span>Pricing Breakdown</span>
                        </p>
                        @if($studentCountEarly > 0)
                            <p style="margin-bottom: 8px; font-size: 14px;">
                                <span>{{ $studentCountEarly }} Student{{ $studentCountEarly > 1 ? 's' : '' }} (Early) × ${{ $studentPriceEarly }}</span>
                                <span class="float-end">${{ $studentSubtotalEarly }}</span>
                            </p>
                        @endif
                        @if($studentCountRegular > 0)
                            <p style="margin-bottom: 8px; font-size: 14px;">
                                <span>{{ $studentCountRegular }} Student{{ $studentCountRegular > 1 ? 's' : '' }} (Regular) × ${{ $studentPriceRegular }}</span>
                                <span class="float-end">${{ $studentSubtotalRegular }}</span>
                            </p>
                        @endif
                        @if($adultCount > 0)
                            <p style="margin-bottom: 8px; font-size: 14px;">
                                <span>{{ $adultCount }} Adult{{ $adultCount > 1 ? 's' : '' }} × ${{ $adultPrice }}</span>
                                <span class="float-end">${{ $adultSubtotal }}</span>
                            </p>
                        @endif
                    @else
                        <p style="font-size: 16px; margin-bottom: 15px">
                            <span>Amount Per Participant</span>
                            <span class="float-end">${{ $payment_summary['amountPerHead'] ?? '0.00' }}</span>
                        </p>
                    @endif
                    
                    @if($participantCount>0)
                        <p style="margin-top: 10px;">
                            <span>Total Participants</span>
                            <span id="participant-count" class="float-end">{{ $participantCount }}</span>
                        </p>
                        @php $pendingCount=$participantCount - $paidParticipantCount;@endphp
                        @if($pendingCount>0)
                            <p style="margin: 0;">
                                <span>Payment Pending For</span>
                                <span id="pending-participant-count" class="float-end">{{ $participantCount - $paidParticipantCount }}</span>
                            </p>
                        @endif
                    @endif
                    @if($payments->isEmpty())
                        <hr style="margin: 10px 0; border-top: 2px solid #f15b43;">
                        @if(isset($payment_summary['paidAmount']) && (float)str_replace(',', '', $payment_summary['paidAmount']) > 0)
                            <p style="margin: 0; font-size: 16px;">
                                <span>Amount Paid <span style="font-size: 12px"></span></span> 
                                <span class="float-end"> ${{ $payment_summary['paidAmount'] }}</span>
                            </p>
                            <hr style="margin: 10px 0; border-top: 2px solid #f15b43;">
                        @endif
                        <p style="margin-bottom: 10px; font-size: 18px; font-weight: bold;">
                            <span>Total Amount Payable</span>
                            <span class="float-end">${{ $payment_summary['amountPayable'] ?? '0.00' }}</span>
                        </p>
                    @else
                        <hr style="margin: 10px 0; border-top: 2px solid #f15b43;">
                        @if(isset($payment_summary['paidAmount']) && (float)str_replace(',', '', $payment_summary['paidAmount']) > 0)
                            <p style="margin: 0; font-size: 16px;">
                                <span>Amount Paid <span style="font-size: 12px"></span></span> 
                                <span class="float-end"> ${{ $payment_summary['paidAmount'] }}</span>
                            </p>
                            <hr style="margin: 10px 0; border-top: 2px solid #f15b43;">
                        @endif
                        @if(isset($payment_summary['amountPayable']) && (float)str_replace(',', '', $payment_summary['amountPayable']) > 0)
                            <p style="margin-bottom: 10px; font-size: 18px; font-weight: bold;">
                                <span>Total Amount Payable</span>
                                <span class="float-end">${{ $payment_summary['amountPayable'] ?? '0.00' }}</span>
                            </p>
                        @endif
                    @endif

                    <!-- Givebutter Fee Information Image + Important Notice -->
                    @if(isset($payment_summary['amountPayable']) && (float)str_replace(',', '', $payment_summary['amountPayable']) > 0)
                    <div class="mb-3" style="text-align: center; margin: 20px 0;">
                        @php
                            $imagePath = public_path('uploads/images/givebutter-fee-info.png');
                            $imageUrl = file_exists($imagePath) ? url('uploads/images/givebutter-fee-info.png') : null;
                        @endphp
                        @if($imageUrl)
                        <img src="{{ $imageUrl }}" 
                             alt="Givebutter Fee Information - Uncheck 'I want to cover fees' and set Additional Donation to $0" 
                             style="max-width: 100%; width: 100%; max-width: 600px; height: auto; border-radius: 8px; box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1); display: block; margin: 0 auto;">
                        @endif
                    </div>
                    <div class="mb-3" style="text-align: center; margin: 10px 0 20px 0;">
                        <div style="margin-top: 4px; padding: 12px 16px; border-radius: 8px; background: #F15B43; border: 1px solid #CB4B36; display: inline-block; text-align: left; max-width: 700px;">
                            <p style="margin: 0; font-size: 14px; color: #ffffff; font-weight: 600;">
                                <i class="fas fa-exclamation-triangle" style="color: #ffffff; margin-right: 6px;"></i>
                                Important: To avoid extra charges on Givebutter, please:
                            </p>
                            <ul style="margin: 8px 0 0 22px; padding: 0; font-size: 14px; color: #ffffff;">
                                <li><strong>Uncheck</strong> the option <strong>"I want to cover fees"</strong></li>
                                <li>Set <strong>"Additional Donation"</strong> to <strong>$0</strong></li>
                            </ul>
                        </div>
                    </div>
                    @endif

                    <div class="d-flex justify-content-end align-items-center mt-3">
                        <a href="javascript:void(0);" class="dynamic-link button-round reg_register make_payment"
                            data-url="{{ route('payment.index') }}" data-class="link-payment" id="make_payment">Make Payment</a>
                    </div>
                </div>
            @else
                @php
                    $paidAmountValue = isset($payment_summary['paidAmount']) ? (float)str_replace(',', '', $payment_summary['paidAmount']) : 0;
                    $amountPayableValue = isset($payment_summary['amountPayable']) ? (float)str_replace(',', '', $payment_summary['amountPayable']) : 0;
                    $hasPayment = $paidAmountValue > 0;
                    $hasParticipants = $participantCount > 0;
                    $isFullyPaid = $amountPayableValue == 0;
                    // Only show payment completion message if there are participants AND payment was made AND amount payable is 0
                    $shouldShowPaymentMessage = $hasParticipants && $hasPayment && $isFullyPaid;
                @endphp
                @if($shouldShowPaymentMessage)
                    <div class="col-md-12" id="payment-done">
                        <p style="font-size: 18px; color: green; font-weight: bold; text-align: center; width: 100%;">
                            No payment due. Thank you!
                        </p>
                        <div class="row d-flex justify-content-center mt-3">
                            <div class="col-md-6" style="display: flex; justify-content: center;">
                                <p style="font-weight: bold;">Amount Paid: ${{ $payment_summary['paidAmount']}}</p>
                            </div>
                          
                        </div>
                        @php
                            $hasCompletedPayment = \App\Models\Payment::where('registration_id', $user->id)
                                ->where('payment_status', 3)
                                ->exists();
                            // Abstract upload only active when fully paid (amountPayable == 0)
                            $isFullyPaid = isset($payment_summary) && isset($payment_summary['amountPayable']) && (float)str_replace(',', '', $payment_summary['amountPayable']) == 0;
                            $abstractStatusPd = optional($settings->where('code','abstract_status')->first())->value;
                            $abstractNavVisiblePd = \App\Models\AbstractSubmission::dashboardAbstractNavVisible(
                                $user->id,
                                (bool) $abstractStatusPd,
                                $hasCompletedPayment && $isFullyPaid
                            );
                            $abstractNavLabelPd = \App\Models\AbstractSubmission::dashboardAbstractNavLabel($user->id);
                        @endphp
                        @if($hasCompletedPayment && $isFullyPaid && $abstractNavVisiblePd)
                            <div class="col-md-12 d-flex justify-content-center align-items-center mt-3 mb-3">
                                <a href="javascript:void(0);" class="dynamic-link button-round reg_register"
                                    data-url="{{ route('abstract.create') }}"  data-class="link-abstract"
                                    >{{ $abstractNavLabelPd }}</a>
                            </div>
                        @endif
                    </div>
                @endif
            @endif
        </div>
    </div>

<script>
    $(document).ready(function (){
        $('.make_payment').on('click', function (e) {
        $("html, body").animate({ scrollTop: $(document).height()-1200 }, 1000);
        });

        // dynamic-link handler is managed by the parent page (index.blade.php) via event delegation

        const amountPerParticipant = {{ ($amountPerHead ?? 0) }};
        let participantCount = {{ $participantCount ?? 0 }};
        let paidParticipantCount = {{ $paidParticipantCount ?? 0 }};
        let totalAmount = (participantCount - paidParticipantCount) * amountPerParticipant;

        // if (totalAmount == 0) {
        //     $('#payment-done').show();
        //     $('#payment').hide();
        // } else {
        //     if (participantCount == 0){
        //         $('#payment-info').show()
        //     }else {
        //         $('#payment').show();
        //         $('#payment-done').hide();
        //     }
        // }

        function updatePaymentSection() {
            $('#participant-count').text(participantCount-paidParticipantCount);
            $('#dynamic-total-amount').text('$' + totalAmount);
            $('#payment').toggle(totalAmount !== 0);
            $('#payment-done').toggle(totalAmount == 0);
            $('#payment-info').toggle(participantCount ==0)
        }

    });
</script>
