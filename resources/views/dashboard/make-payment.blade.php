<div class="donate-section">
    <div class="container">
        <div class="donate-detais">
            @if (session('success'))
                <div class="alert alert-success">{{ session('success') }}</div>
            @endif
            @if($payment_summary['amountPayable']<=0)
            <div class="donate-form mt-3">
                <div class="form-inner">
                        <div class="row g-3 mt-2">
                            <div class="col-md-12">
                                <div class="contact-detail-container item-center" style="align-items: center;text-align: center;">
                                <div class="pb-4">
                                <span style="height: 75px;color:#198754;"><i class="fa fa-info-circle" style="font-size: 45px;"></i></span>
                                </div>
                                <div style="font-size: 12px; color: #333; text-align: center;">
                                    <h2 style="font-size: 40px;">Payment is completed</h2>
                                    
                                </div>
                                </div>
                                </div>
                            <div class="col-md-12 d-flex justify-content-center">
                                <p style="font-size: 18px; color: green; font-weight: bold; text-align: center; width: 100%;">
                                    No payment due. Thank you!
                                </p>
                            </div>
                            @if($payment_summary['paidAmount']>0)
                                <div class="col-md-12 d-flex justify-content-center">
                                    <h5 style="font-weight: bold;">
                                        Total Amount Paid : ${{$payment_summary['paidAmount'] }}
                                    </h5>
                                </div>
                            @endif
                            @php
                                $hasCompletedPayment = \App\Models\Payment::where('registration_id', $user->id)
                                    ->where('payment_status', 3)
                                    ->exists();
                                // Abstract upload only active when fully paid (amountPayable == 0)
                                $isFullyPaid = isset($payment_summary) && (float)str_replace(',', '', $payment_summary['amountPayable']) == 0;
                                $abstractStatusMp = optional($settings->where('code','abstract_status')->first())->value;
                                $abstractNavVisibleMp = \App\Models\AbstractSubmission::dashboardAbstractNavVisible(
                                    $user->id,
                                    (bool) $abstractStatusMp,
                                    $hasCompletedPayment && $isFullyPaid
                                );
                                $abstractNavLabelMp = \App\Models\AbstractSubmission::dashboardAbstractNavLabel($user->id);
                            @endphp
                            @if($hasCompletedPayment && $isFullyPaid && $abstractNavVisibleMp)
                            <div style="text-align: center; width: 100%; margin-top: 20px; margin-bottom: 20px;">
                                    <a href="javascript:void(0);" class="dynamic-link button-round reg_register"
                                        data-url="{{ route('abstract.create') }}"  data-class="link-abstract"
                                    >{{ $abstractNavLabelMp }}</a>
                                </div>
                            @endif
                        </div> 
                </div>
            </div>
            @else
            @if(!empty($paymentApprovePending))
            <div class="donate-form mt-3 mb-3">
                <div class="form-inner  p-3">
                    <div class="row">
                        <div class="col-md-12">
                            <div class="contact-detail-container item-center" style="align-items: center;text-align: center;">
                            <div class="pb-1">
                            <span style="height: 75px;color:#F15B43;"><i class="fa fa-info-circle" style="font-size: 24px;"></i></span>
                            </div>
                            <div style="font-size: 12px; color: #333; text-align: center;">
                                <h2 style="font-size: 24px;">Payment Verification Pending</h2>
                                
                            </div>
                                                    
                            <div style="font-size: 16px; color: #333; text-align: center;">
                            <!-- <p>Payment with  Reference{{$paymentApprovePending->payment_reference}} and amount of ${{$paymentApprovePending->amount}} recorded;</p> -->
                            <p>Your registration is subject to verification and will be approved within 24 hrs</p>
                            </div>
                        
                            </div>
                        </div>
                    
                    </div> 
                </div>
            </div>
            @endif
            <div class="donate-form mt-3">
                <div class="form-inner">
                    <!-- <h4>Make Payment</h4> -->
                    <h4>Registration Details</h4>
                    <div class="row">
                        <div class="col-sm-6">
                            <div>
                                <p><strong>Registration Number: </strong>&nbsp;&nbsp;{{ $user->registration_number }} <p>
                            </div>
                        </div>
                        @if(optional($abstract)->id)
                            <div class="col-sm-6">
                                <div>
                                    <p style="color: green;"><strong> Abstract Submitted</strong> <i class="fa fa-check text-success" aria-hidden="true"></i><p>
                                </div>
                            </div>
                        @endif
                        <div class="col-sm-6">
                            <div >
                                <p><strong>Name: </strong>&nbsp;&nbsp;{{ $user->first_name . ' ' . $user->last_name }}</p>
                            </div>
                        </div>
                        <div class="col-sm-6">
                            <div>
                                <p><strong>Mobile: </strong>&nbsp;&nbsp;{{ $user->phone }} <p>
                            </div>
                        </div>
                        <div class="col-sm-6">
                            <div >
                                <p><strong>Email: </strong>&nbsp;&nbsp;{{ $user->email }}</p>
                            </div>
                        </div>
                        <div class="col-sm-6">
                            <div>
                                <p><strong>Institution Name: </strong>&nbsp;&nbsp;{{ $user->institution_name }} <p>
                            </div>
                        </div>

                    </div>
                </div>
                <div class="form-inner">
                    <div class="row">
                        <div class="col-md-6">
                            <h4>Participant Details</h4>
                       </div>
                    </div>
                    @if($participants->isEmpty())
                        <!-- <div class="row">
                            <div class="col-md-8">
                                <h5>No participants added yet click here to <a href="#" style="text-decoration: underline;" class="dynamic-link"
                                  data-url="{{route('viewParticipants')}}" data-class="link-participant"> add participants</a></h5>
                            </div>
                        </div> -->
                        <div class="row g-3">
                        <div class="col-md-12 text-center">
                                    <p>No participants added yet click button below to add participants</p>
                                    <a href="#" class="dynamic-link btn outline-round" style="padding: 10px 18px;"
                                    data-url="{{route('participant.index')}}" data-class="link-participant">Add Participants </a>
                            </div>
                            <!-- <div class="col-md-4 text-right">
                            <a href="javascript:void(0);"
                                    class="dynamic-link btn" style="color:#F15B43;"
                                    data-url="{{route('participant.index')}}"><i class="fas fa-plus"></i>
                                Participants
                                </a>
                            </div>   -->
                        </div>
                    @else
                        <div class="row participant-row">
                            <div class="col-md-12">
                                @if($participants->isEmpty())
                                    <p>No participants added yet.</p>
                                @else
                                    <table class="mb-5" id="participants-table" style="width: 100%;">
                                        <thead>
                                        <tr>
                                            <th style="background-color: #f15b43; color: #FFFFFF;">First Name</th>
                                            <th style="background-color: #f15b43; color: #FFFFFF;">Last Name</th>
                                            <th style="background-color: #f15b43; color: #FFFFFF;">Guest</th>
                                            <th style="background-color: #f15b43; color: #FFFFFF;">Class</th>
                                        </tr>
                                        </thead>
                                        <tbody>
                                        @foreach($participants as $participant)
                                            <tr>
                                                <td>{{ $participant->first_name }}</td>
                                                <td>{{ $participant->last_name }}</td>
                                                <td>{{$participant->type==0?'No':'Yes'}}</td>
                                                <td>{{ $participant->class }}</td>
                                            </tr>
                                        @endforeach
                                        </tbody>
                                    </table>
                                @endif
                            </div>
                        </div>
                    @endif

                </div>
            </div>
            <div class="donate-form mt-5">
                <div class="form-inner">
                    <h4>Payment Summary</h4>
                    @if($payment_summary['amountPayable'] > 0)
                        <div id="amount-payable-box" class="amount-payable-box row d-flex justify-content-around mt-3">
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
                                    
                                    @if($participantsCount>0)
                                        <p style="margin-top: 10px;">
                                            <span>Total Participants</span>
                                    <span id="participant-count" class="float-end">{{ $participantsCount }}</span>
                                    </p>
                                    @php $pendingCount=$participantsCount - $paidParticipantCount;@endphp
                                    @if($pendingCount>0)
                                    <p style="margin: 0;">
                                        <span>Payment Pending For</span>
                                        <span id="pending-participant-count" class="float-end">{{ $participantsCount - $paidParticipantCount }}</span>
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
                                        
                                        @if(isset($payment_summary['amountPayable']) && (float)str_replace(',', '', $payment_summary['amountPayable']) > 0)
                                        <!-- Ensure coupon function is available - inline script -->
                                        <script>
                                        if (typeof window.IRCC_RISE150_STUDENT_DISCOUNT === 'undefined') {
                                            window.IRCC_RISE150_STUDENT_DISCOUNT = {{ number_format((float) ($rise150_student_discount ?? 0), 2, '.', '') }};
                                            window.IRCC_RISE150_LABEL = @json(\App\Support\PaymentCoupon::RISE150_LABEL);
                                        }
                                        @php
                                            $__sPayInline = (float) str_replace(',', '', $payment_summary['studentAmountPayable'] ?? '0');
                                            $__aPayInline = (float) str_replace(',', '', $payment_summary['adultAmountPayable'] ?? '0');
                                        @endphp
                                        window.IRCC_STUDENT_AMOUNT_PAYABLE = {{ json_encode(round($__sPayInline, 2)) }};
                                        window.IRCC_ADULT_AMOUNT_PAYABLE = {{ json_encode(round($__aPayInline, 2)) }};
                                        if (typeof window.irccApplyCouponPricing === 'undefined') {
                                            window.irccApplyCouponPricing = function(coupon) {
                                                var student = parseFloat(window.IRCC_STUDENT_AMOUNT_PAYABLE);
                                                if (isNaN(student)) student = 0;
                                                var adult = parseFloat(window.IRCC_ADULT_AMOUNT_PAYABLE);
                                                if (isNaN(adult)) adult = 0;
                                                student = Math.round(student * 100) / 100;
                                                adult = Math.round(adult * 100) / 100;
                                                var originalTotal = Math.round((student + adult) * 100) / 100;
                                                var discountAmount = 0, finalAmount = originalTotal, discountLabel = '';
                                                if (!coupon || !coupon.type) {
                                                    return { discountAmount: discountAmount, finalAmount: finalAmount, discountLabel: discountLabel, originalTotal: originalTotal };
                                                }
                                                if (coupon.type === 'fixed_total') {
                                                    finalAmount = parseFloat(coupon.finalAmount);
                                                    if (isNaN(finalAmount)) finalAmount = 1;
                                                    discountAmount = Math.round((originalTotal - finalAmount) * 100) / 100;
                                                    if (discountAmount < 0) discountAmount = 0;
                                                    discountLabel = 'Fixed price: $' + finalAmount.toFixed(2);
                                                } else if (coupon.type === 'rise_student') {
                                                    var rise = Math.min(parseFloat(window.IRCC_RISE150_STUDENT_DISCOUNT) || 0, student);
                                                    rise = Math.round(rise * 100) / 100;
                                                    discountAmount = rise;
                                                    finalAmount = Math.round((adult + student - discountAmount) * 100) / 100;
                                                    discountLabel = window.IRCC_RISE150_LABEL || 'RISE early bird benefit activated';
                                                } else {
                                                    discountAmount = Math.round((student * coupon.discount / 100) * 100) / 100;
                                                    finalAmount = Math.round((adult + student - discountAmount) * 100) / 100;
                                                    discountLabel = coupon.discount + '% off student fees';
                                                }
                                                return { discountAmount: discountAmount, finalAmount: finalAmount, discountLabel: discountLabel, originalTotal: originalTotal };
                                            };
                                        }
                                        (function() {
                                            // Define coupon codes if not defined
                                            if (typeof window.couponCodes === 'undefined') {
                                                window.couponCodes = {
                                                    'Save10': { discount: 10, type: 'percent' },
                                                    'Save5': { discount: 5, type: 'percent' },
                                                    'Save25': { discount: 25, type: 'percent' },
                                                    'RISE150': { type: 'rise_student' },
                                                    'LIFO10': { discount: 10, type: 'percent' },
                                                    'IRCC1': { type: 'fixed_total', finalAmount: 1 }
                                                };
                                            }
                                            
                                            // Define function if not defined - SIMPLE VERSION
                                            if (typeof window.handleSummaryCouponApply === 'undefined') {
                                                window.handleSummaryCouponApply = function(buttonId, inputId, messageId, discountId, amountId, amountPayableId) {
                                                    console.log('=== handleSummaryCouponApply called ===');
                                                    
                                                    if (typeof jQuery === 'undefined' || typeof $ === 'undefined') {
                                                        setTimeout(function() { window.handleSummaryCouponApply(buttonId, inputId, messageId, discountId, amountId, amountPayableId); }, 100);
                                                        return false;
                                                    }
                                                    
                                                    const input = $('#' + inputId);
                                                    if (input.length === 0) {
                                                        console.error('Input not found:', inputId);
                                                        return false;
                                                    }
                                                    
                                                    const code = input.val().trim().toUpperCase();
                                                    if (!code) {
                                                        $('#' + messageId).html('<span style="color: #dc3545;">Please enter a coupon code.</span>');
                                                        return false;
                                                    }
                                                    
                                                    let couponCode = code;
                                                    if (code === 'SAVE10') couponCode = 'Save10';
                                                    else if (code === 'SAVE5') couponCode = 'Save5';
                                                    else if (code === 'SAVE25') couponCode = 'Save25';
                                                    else if (code === 'RISE150') couponCode = 'RISE150';
                                                    else if (code === 'LIFO10') couponCode = 'LIFO10';
                                                    else if (code === 'IRCC1') couponCode = 'IRCC1';
                                                    
                                                    if (typeof window.couponCodes === 'undefined') {
                                                        window.couponCodes = {
                                                            'Save10': { discount: 10, type: 'percent' },
                                                            'Save5': { discount: 5, type: 'percent' },
                                                            'Save25': { discount: 25, type: 'percent' },
                                                            'RISE150': { type: 'rise_student' },
                                                            'LIFO10': { discount: 10, type: 'percent' },
                                                            'IRCC1': { type: 'fixed_total', finalAmount: 1 }
                                                        };
                                                    }
                                                    
                                                    const coupon = window.couponCodes[couponCode];
                                                    if (!coupon) {
                                                        $('#' + messageId).html('<span style="color: #dc3545;">Invalid coupon code.</span>');
                                                        return false;
                                                    }
                                                    
                                                    const amountEl = $('#' + amountPayableId);
                                                    if (amountEl.length === 0) {
                                                        console.error('Amount element not found:', amountPayableId);
                                                        return false;
                                                    }
                                                    
                                                    const pr = window.irccApplyCouponPricing(coupon);
                                                    const originalAmount = pr.originalTotal;
                                                    window.originalAmount = originalAmount;
                                                    const discountAmount = pr.discountAmount;
                                                    const finalAmount = pr.finalAmount;
                                                    const discountLabel = pr.discountLabel;
                                                    
                                                    window.appliedCoupon = couponCode;
                                                    window.discountAmount = discountAmount;
                                                    
                                                    $('#' + messageId).html('<span style="color: #198754; font-weight: 600;"><i class="fas fa-check-circle"></i> Coupon applied! ' + discountLabel + '</span>');
                                                    $('#' + discountId).show();
                                                    $('#' + amountId).text('-$' + discountAmount.toFixed(2) + ' (' + discountLabel + ')');
                                                    $('#' + amountPayableId).text('$' + finalAmount.toFixed(2));
                                                    
                                                    if ($('#modal-amount-payable').length) $('#modal-amount-payable').text('$' + finalAmount.toFixed(2));
                                                    if ($('#summary-amount-payable-2').length) $('#summary-amount-payable-2').text('$' + finalAmount.toFixed(2));
                                                    
                                                    console.log('Coupon applied successfully:', couponCode, discountAmount, finalAmount);
                                                    return true;
                                                };
                                                console.log('✓ handleSummaryCouponApply defined inline');
                                            }
                                        })();
                                        </script>
                                        
                                        <!-- Coupon Code Section -->
                                        <div style="margin-bottom: 15px; padding: 12px; background: #f8f9fa; border: 1px solid #ddd; border-radius: 5px;">
                                            <label for="summary-coupon-code" style="display: block; margin-bottom: 8px; font-weight: 600; font-size: 14px;">Coupon Code (Optional)</label>
                                            <div style="display: flex; gap: 10px;">
                                                <input type="text" id="summary-coupon-code" name="summary-coupon-code" class="coupon-code-input" placeholder="Enter coupon code" autocomplete="off" style="flex: 1; padding: 8px 12px; border: 1px solid #ddd; border-radius: 4px; font-size: 14px;">
                                                <button type="button" id="apply-summary-coupon-btn" class="apply-coupon-btn" data-input-id="summary-coupon-code" data-message-id="summary-coupon-message" data-discount-id="summary-coupon-discount" data-amount-id="summary-discount-amount" data-payable-id="summary-amount-payable" style="padding: 8px 20px; background: #F15B43; color: white; border: none; border-radius: 4px; cursor: pointer; font-weight: 600; white-space: nowrap;">Apply</button>
                                            </div>
                                            <div id="summary-coupon-message" style="margin-top: 8px; font-size: 13px; min-height: 20px;"></div>
                                            <div id="summary-coupon-discount" style="display: none; margin-top: 8px; padding: 8px; background: #e7f3ff; border-left: 3px solid #2196F3; border-radius: 3px;">
                                                <p style="margin: 0; font-size: 14px;">
                                                    <strong>Discount Applied:</strong> <span id="summary-discount-amount" style="color: #198754; font-weight: bold;"></span>
                                                    <button type="button" id="remove-summary-coupon-btn" class="remove-coupon-btn" data-input-id="summary-coupon-code" data-message-id="summary-coupon-message" data-discount-id="summary-coupon-discount" data-payable-id="summary-amount-payable" style="margin-left: 10px; padding: 2px 8px; background: #dc3545; color: white; border: none; border-radius: 3px; cursor: pointer; font-size: 12px;">Remove</button>
                                                </p>
                                            </div>
                                        </div>
                                        @endif
                                        
                                        <p style="margin-bottom: 10px; font-size: 18px; font-weight: bold;">
                                            <span>Total Amount Payable</span>
                                            <span class="float-end" id="summary-amount-payable">${{ $payment_summary['amountPayable'] ?? '0.00' }}</span>
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
                                        
                                        <!-- Coupon Code Section -->
                                        <div style="margin-bottom: 15px; padding: 12px; background: #f8f9fa; border: 1px solid #ddd; border-radius: 5px;">
                                            <label for="summary-coupon-code-2" style="display: block; margin-bottom: 8px; font-weight: 600; font-size: 14px;">Coupon Code (Optional)</label>
                                            <div style="display: flex; gap: 10px;">
                                                <input type="text" id="summary-coupon-code-2" name="summary-coupon-code-2" class="coupon-code-input" placeholder="Enter coupon code" autocomplete="off" style="flex: 1; padding: 8px 12px; border: 1px solid #ddd; border-radius: 4px; font-size: 14px;">
                                                <button type="button" id="apply-summary-coupon-btn-2" class="apply-coupon-btn" data-input-id="summary-coupon-code-2" data-message-id="summary-coupon-message-2" data-discount-id="summary-coupon-discount-2" data-amount-id="summary-discount-amount-2" data-payable-id="summary-amount-payable-2" style="padding: 8px 20px; background: #F15B43; color: white; border: none; border-radius: 4px; cursor: pointer; font-weight: 600; white-space: nowrap;">Apply</button>
                                            </div>
                                            <div id="summary-coupon-message-2" style="margin-top: 8px; font-size: 13px; min-height: 20px;"></div>
                                            <div id="summary-coupon-discount-2" style="display: none; margin-top: 8px; padding: 8px; background: #e7f3ff; border-left: 3px solid #2196F3; border-radius: 3px;">
                                                <p style="margin: 0; font-size: 14px;">
                                                    <strong>Discount Applied:</strong> <span id="summary-discount-amount-2" style="color: #198754; font-weight: bold;"></span>
                                                    <button type="button" id="remove-summary-coupon-btn-2" class="remove-coupon-btn" data-input-id="summary-coupon-code-2" data-message-id="summary-coupon-message-2" data-discount-id="summary-coupon-discount-2" data-payable-id="summary-amount-payable-2" style="margin-left: 10px; padding: 2px 8px; background: #dc3545; color: white; border: none; border-radius: 3px; cursor: pointer; font-size: 12px;">Remove</button>
                                                </p>
                                            </div>
                                        </div>
                                        
                                        <p style="margin-bottom: 10px; font-size: 18px; font-weight: bold;">
                                            <span>Total Amount Payable</span>
                                                <span class="float-end" id="summary-amount-payable-2">${{ $payment_summary['amountPayable'] ?? '0.00' }}</span>
                                        </p>
                                        @endif
                                    @endif
                                </div>
                            <!-- <div class="mt-2">
                                @if (!$payments->isEmpty()) 
                                    <p style="margin-bottom: 15px">
                                        <span>Amount Paid</span>
                                        <span class="float-end">${{ $payment_summary['paidAmount'] }}</span>
                                    </p>
                                    <p style="margin-bottom: 15px">
                                        <span>Amount Per Participant</span>
                                        <span class="float-end">${{$payment_summary['amountPerHead'] }}</span>
                                    </p>
                                    @if($participantsCount>0)
                                    <p style="margin-bottom: 15px">
                                        <span>Number of Participants</span>
                                        <span id="participant-count" class="float-end">{{ $participantsCount }}</span>
                                    </p>
                                    <p style="margin-bottom: 15px">
                                        <span>Payment Pending For</span>
                                        <span class="float-end">{{ $participantsCount - $paidParticipantCount }}</span>
                                    </p>
                                    @endif
                                    <hr style="margin: 10px 0; border-top: 2px solid #f15b43;">
                                    <p style="margin-bottom: 10px; font-size: 18px; font-weight: bold;">
                                        <span>Total Amount Payable</span>
                                        <span class="float-end">${{ $payment_summary['amountPayable'] }}</span>
                                    </p>
                                @else
                                    <p style="margin-bottom: 15px">
                                        <span>Amount Per Participant</span>
                                        <span class="float-end">${{ $payment_summary['amountPerHead']}}</span>
                                    </p>
                                    @if($participantsCount>0)
                                    <p style="margin: 0;">
                                        <span>Number of Participants</span>
                                        <span id="participant-count" class="float-end">{{ $participantsCount }}</span>
                                    </p>
                                    @endif
                                    <hr style="margin: 10px 0; border-top: 2px solid #f15b43;">
                                    @if($payment_summary['subTotal']>0)
                                    <p style="margin: 0;">
                                        <span style="font-size: 16px; font-weight: bold;">Sub Total</span>
                                        <span id="dynamic-total-amount" class="float-end">${{  $payment_summary['subTotal'] }}</span>
                                    </p>
                                    @endif
                                    <p style="margin: 0; font-size: 16px;">
                                        <span>Registered user Fee <span style="font-size: 12px"> ( {{$user->email}} )</span></span>
                                        <span class="float-end">+ ${{ number_format($registrationFee,2) }}</span>
                                    </p>
                                    <hr style="margin: 10px 0; border-top: 2px solid #f15b43;">
                                    <p style="margin-bottom: 10px; font-size: 18px; font-weight: bold;">
                                        <span>Total Amount Payable</span>
                                        <span class="float-end">${{ $payment_summary['amountPayable'] }}</span>
                                    </p>
                                @endif
                            </div>                             -->
                        </div>
                        
                        <div class="mt-3">
                            <div style="margin-bottom: 20px; padding: 15px; background: #fff3cd; border-left: 4px solid #ffc107; border-radius: 4px;">
                                <p style="margin-bottom: 10px; font-weight: 600; color: #856404;">
                                    <i class="fa fa-exclamation-triangle" style="margin-right: 8px;"></i>
                                    Before proceeding with payment, you must complete the Conference Waiver and Release Form.
                                </p>
                                <a href="javascript:void(0);" id="waiverModalLink" class="button-round" style="display: inline-block; padding: 10px 20px; background: #F15B43; color: white; text-decoration: none; border-radius: 25px; font-size: 14px;">
                                    <i class="fa fa-file-signature" style="margin-right: 5px;"></i>Complete Waiver Form
                                </a>
                                <span id="waiver-status" style="margin-left: 15px; color: #28a745; font-weight: 600; display: none;">
                                    <i class="fa fa-check-circle"></i> Waiver Completed
                                </span>
                            </div>
                            <h4>Payment Mode</h4>
                            
                            <!-- Givebutter Fee Information Image + Important Notice -->
                            <div class="mb-4" style="text-align: center; margin: 20px 0;">
                                @php
                                    $imagePath = public_path('uploads/images/givebutter-fee-info.png');
                                    $imageUrl = file_exists($imagePath) ? url('uploads/images/givebutter-fee-info.png') : null;
                                @endphp
                                @if($imageUrl)
                                <img src="{{ $imageUrl }}" 
                                     alt="Givebutter Fee Information - Uncheck 'I want to cover fees' and set Additional Donation to $0" 
                                     style="max-width: 100%; width: 100%; max-width: 600px; height: auto; border-radius: 8px; box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1); display: block; margin: 0 auto;">
                                @endif
                                <div style="margin-top: 12px; padding: 12px 16px; border-radius: 8px; background: #F15B43; border: 1px solid #CB4B36; display: inline-block; text-align: left; max-width: 700px;">
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
                            
                            <form action="{{ route('account.registration.payment') }}" method="POST" id="payment-form">
                                @csrf
                                <!-- Hidden fields to include waiver data with payment submission -->
                                <input type="hidden" name="waiver_acknowledgment" id="waiver_acknowledgment_hidden" value="">
                                <input type="hidden" name="waiver_signature" id="waiver_signature_hidden" value="">
                                <input type="hidden" name="waiver_date" id="waiver_date_hidden" value="">
                                <input type="hidden" name="waiver_under_12" id="waiver_under_12_hidden" value="">
                                <input type="hidden" name="waiver_12_or_older" id="waiver_12_or_older_hidden" value="">
                                <div class="form-group mb-0">
                                    @foreach($paymentModes as $paymentMode)
                                        <div class="custom-radio-field">
                                            <label>
                                                <input type="radio" name="payment_mode"
                                                       value="{{ $paymentMode['name'] }}"
                                                       id="{{ Str::slug($paymentMode['name'], '-') }}"
                                                    {{ $loop->first ? 'checked' : '' }}>
                                                <span class="radio-field"></span>
                                                {{ $paymentMode['name'] }}
                                            </label>
                                        </div>
                                        @if($paymentMode['name'] === 'Online Transaction')
                                    <!-- Payment Confirmation Modal -->
                                    <div class="modal fade" id="payment-confirmation-modal" tabindex="-1" aria-labelledby="paymentConfirmationModalLabel" aria-hidden="true">
                                        <div class="modal-dialog modal-dialog-centered modal-lg">
                                            <div class="modal-content">
                                                <div class="modal-header" style="background-color: #f15b43; color: white;">
                                                    <h5 class="modal-title" id="paymentConfirmationModalLabel">Payment Confirmation</h5>
                                                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                                                </div>
                                                <div class="modal-body" style="padding: 20px;">
                                                    <div class="text-center mb-3">
                                                        <i class="fas fa-credit-card" style="font-size: 32px; color: #f15b43; margin-bottom: 8px;"></i>
                                                        <h5 style="margin-bottom: 0;">Confirm Payment Amount</h5>
                                                    </div>
                                                    <div class="payment-summary-box" id="payment-summary-box" style="background: #f8f9fa; padding: 15px; border-radius: 5px; margin-bottom: 15px;">
                                                        @if(!$payments->isEmpty())
                                                            <p style="margin-bottom: 8px;">
                                                                <span>Amount Paid</span>
                                                                <span class="float-end"><strong id="modal-paid-amount">${{ $payment_summary['paidAmount'] }}</strong></span>
                                                            </p>
                                                        @endif
                                                        <p style="margin-bottom: 8px;">
                                                            <span>Amount Per Participant</span>
                                                            <span class="float-end" id="modal-amount-per-head">${{ $payment_summary['amountPerHead'] }}</span>
                                                        </p>
                                                        @if($participantsCount>0)
                                                        <p style="margin-bottom: 8px;">
                                                            <span>Number of Participants</span>
                                                            <span class="float-end" id="modal-participants-count">{{ $participantsCount }}</span>
                                                        </p>
                                                        @endif
                                                        <hr style="margin: 10px 0; border-top: 2px solid #f15b43;">
                                                        <p style="font-size: 18px; font-weight: bold; margin-bottom: 8px;">
                                                            <span>Subtotal</span>
                                                            <span class="float-end" style="color: #333; font-size: 18px;" id="modal-subtotal">${{ $payment_summary['amountPayable'] }}</span>
                                                        </p>
                                                        
                                                        <!-- Coupon Code Section (no examples shown) -->
                                                        <div style="margin-bottom: 15px; padding: 15px; background: #fff; border: 1px solid #ddd; border-radius: 5px;">
                                                            <label for="coupon_code" style="display: block; margin-bottom: 8px; font-weight: 600; font-size: 14px;">Coupon Code (Optional)</label>
                                                            <div style="display: flex; gap: 10px;">
                                                                <input type="text" id="coupon_code" name="coupon_code" class="coupon-code-input" placeholder="Enter coupon code" autocomplete="off" style="flex: 1; padding: 8px 12px; border: 1px solid #ddd; border-radius: 4px; font-size: 14px;">
                                                                <button type="button" id="apply-coupon-btn" class="apply-coupon-btn" data-input-id="coupon_code" data-message-id="coupon-message" data-discount-id="coupon-discount" data-amount-id="discount-amount" data-payable-id="modal-amount-payable" style="padding: 8px 20px; background: #F15B43; color: white; border: none; border-radius: 4px; cursor: pointer; font-weight: 600; white-space: nowrap;">Apply</button>
                                                            </div>
                                                            <div id="coupon-message" style="margin-top: 8px; font-size: 13px; min-height: 20px;"></div>
                                                            <div id="coupon-discount" style="display: none; margin-top: 8px; padding: 8px; background: #e7f3ff; border-left: 3px solid #2196F3; border-radius: 3px;">
                                                                <p style="margin: 0; font-size: 14px;">
                                                                    <strong>Discount Applied:</strong> <span id="discount-amount" style="color: #198754; font-weight: bold;"></span>
                                                                    <button type="button" id="remove-coupon-btn" class="remove-coupon-btn" data-input-id="coupon_code" data-message-id="coupon-message" data-discount-id="coupon-discount" data-payable-id="modal-amount-payable" style="margin-left: 10px; padding: 2px 8px; background: #dc3545; color: white; border: none; border-radius: 3px; cursor: pointer; font-size: 12px;">Remove</button>
                                                                </p>
                                                            </div>
                                                        </div>
                                                        
                                                        <hr style="margin: 10px 0; border-top: 2px solid #f15b43;">
                                                        <p style="font-size: 18px; font-weight: bold; margin-bottom: 0;">
                                                            <span>Total Amount Payable</span>
                                                            <span class="float-end" style="color: #f15b43; font-size: 20px;" id="modal-amount-payable">${{ $payment_summary['amountPayable'] }}</span>
                                                        </p>
                                                    </div>
                                                    <div class="alert alert-info" style="margin-bottom: 20px;">
                                                        <i class="fas fa-info-circle"></i> Click the button below to open the secure payment window. After payment, your status will be automatically updated.
                                                    </div>
                                                    
                                                    <div class="text-center" style="padding: 20px;">
                                                        <button type="button" id="open-payment-popup-btn" class="btn btn-primary btn-lg" style="padding: 15px 40px; font-size: 18px;">
                                                            <i class="fas fa-credit-card"></i> Open Payment Window
                                                        </button>
                                                        <p style="margin-top: 15px; color: #666; font-size: 14px;">
                                                            A secure payment window will open where you can complete your payment.
                                                        </p>
                                                    </div>
                                                </div>
                                                <div class="modal-footer">
                                                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="desc d-none" id="givebutter-widget-container">
                                        <div class="row mt-4">
                                            <div class="col-md-12">
                                                <div class="alert alert-info">
                                                    <p><strong>Complete your payment using the widget below:</strong></p>
                                                    <p>After completing payment, your payment status will be automatically updated via webhook.</p>
                                                    <p style="font-size: 12px; margin-top: 10px;">
                                                        <strong>Note:</strong> If the widget doesn't appear, please verify the widget ID "Ll51Kg" is correct in your Givebutter dashboard.
                                                    </p>
                                                </div>
                                            </div>
                                            <div class="col-md-12" style="text-align: center; margin-top: 20px; margin-bottom: 30px; min-height: 400px; position: relative;">
                                                <!-- Givebutter Widget with pre-filled amount -->
                                                <givebutter-widget id="Ll51Kg" amount="{{ str_replace(',', '', $payment_summary['amountPayable']) }}"></givebutter-widget>
                                                <!-- Fallback message if widget doesn't load -->
                                                <div id="widget-fallback" style="display: none; padding: 20px; background: #f8f9fa; border-radius: 5px; margin-top: 20px;">
                                                    <p style="color: #666;">Widget is loading... If this message persists, the widget ID may be incorrect.</p>
                                                    <p style="font-size: 12px; color: #999; margin-top: 10px;">Widget ID: Ll51Kg | Account ID: 324164</p>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                        @elseif($paymentMode['name'] === 'Direct Bank Transfer')
                                    <div class="desc d-none" id="bank-transfer-details">
                                        <div class="row">
                                        <div class="col-md-12">
                                        <p> Make your payment directly into our bank account or Scanning QRCode. After payment through bank account share payment details. You can't submit an abstract until our support team approves the payment.</p>
                                        </div>
                                        <div class="col-md-6">
                                       <div class="vacancy-content">
                                            <div class="row">
                                                <h4>Payment Details</h4>
                                                <p>After payment through bank account share payment details.</p>
                                                <div class="form-group col-md-12 mt-5">
                                                        <label for="payment_reference">Payment Reference</label>
                                                        <input type="text" name="payment_reference" id="payment_reference" class="form-control" placeholder="Enter Payment Reference">
                                                    </div>
                                                    <div class="form-group col-md-12 mb-5">
                                                        <label for="payment_date">Payment Date</label>
                                                        <input type="date" name="payment_date" id="payment_date" class="form-control">
                                                    </div>
                                            </div>
                                            <!-- <div class="row">
                                                <p>After payment through bank account share payment details.</p>
                                            </div> -->
                                       </div>
                                    </div>
                                    <div class="col-md-6">
                                       <div class="vacancy-content">
                                       <div class="row pb-2">
                                       <div class="col-md-12" style="justify-content: center;">
                                                <h4>Bank Account Details</h4>
                                                <div class="row">
                                                    <div class="col-sm-4 text-sm-right">Account #</div>
                                                    <div class="col-sm-1 text-center d-none d-sm-inline">:</div>
                                                    <div class="col-sm-7 fw-bold" >17310200003634</div>
                                                </div>
                                      
                                                <div class="row">
                                                    <div class="col-sm-4 text-sm-right ">Account Name</div>
                                                    <div class="col-sm-1 text-center d-none d-sm-inline">:</div>
                                                    <div class="col-sm-7 fw-bold">Global young researchers academy</div>
                                                </div>
                                                <div class="row pb-1">
                                                    <div class="col-sm-4 text-sm-right">Bank Name</div>
                                                    <div class="col-sm-1 text-center d-none d-sm-inline">:</div>
                                                    <div class="col-sm-7 fw-bold">Federal Bank Ayoor Branch</div>
                                                </div>
                                                <div class="row pb-1">
                                                    <div class="col-sm-4 text-sm-right">IFSC Code</div>
                                                    <div class="col-sm-1 text-center d-none d-sm-inline">:</div>
                                                    <div class="col-sm-7 fw-bold">FDRL0001731</div>
                                                </div>
                                        </div>
                                       </div>
                                        <div class="row">
                                        <h5>Scan QRCode</h5>
                                        <div class="col-sm-12" style="text-align: center;">
                                        <img src="{{url('front/images/qr_code.png')}}" style="max-width:50%;" alt="">
                                        </div>
                                        </div>
                                       </div>
                                    </div>
                                                   
                                                </div>
                                            </div>
                                        @endif
                                    @endforeach
                                </div>
                                <div class="d-flex justify-content-end mt-3">
                                <button type="submit" class="button-round reg_register">Submit</button>
                                </div>

                            </form>
                        </div>
                   
                    @else
                        @if ($payment_summary['amountPayable']== 0)
                            <div class="col-md-12" id="payment-done">
                                @if($payments->isEmpty()) 
                                <p style="font-size: 18px; color: green; font-weight: bold; text-align: center; width: 100%;">
                                   Payment completed. Thank you!
                                </p>
                                @else
                                <p style="font-size: 18px; color: green; font-weight: bold; text-align: center; width: 100%;">
                                    No payment due. Thank you!
                                </p>
                                @endif
                                <div class="row d-flex justify-content-center mt-3">
                                    <div class="col-md-6" style="display: flex; justify-content: center;">
                                        <p style="font-weight: bold;">Amount Paid: ${{ $payment_summary['paidAmount']}}</p>
                                    </div>
                                    <!-- <div class="col-md-6">
                                        <p style="font-weight: bold;">Payment Mode: {{ $payments->first()->payment_mode }}</p>
                                    </div> -->
                                </div>
                            </div>
                        @endif
                    @endif
                </div>
            </div>
            @endif
        </div>
    </div>
</div>

<!-- Conference Waiver and Release Form Modal -->
<div class="modal fade" id="waiverModal" tabindex="-1" role="dialog" aria-labelledby="waiverModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-centered" role="document">
        <div class="modal-content register-content">
            <div class="modal-header">
                <h3 class="modal-title" id="waiverModalLabel">CONFERENCE WAIVER AND RELEASE FORM</h3>
                <button type="button" id="waiver-modal-close" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body" style="max-height: 70vh; overflow-y: auto;">
                <div class="col-xs-12 col-sm-12">
                    <div class="card-body">
                        <!-- Adult Supervision Requirements -->
                        <div style="margin-bottom: 30px;">
                            <h5 style="font-weight: 600; margin-bottom: 15px; color: #333;">ADULT SUPERVISION REQUIREMENTS</h5>
                            <div style="margin-bottom: 15px;">
                                <p style="margin-bottom: 8px;"><strong>Adult Name:</strong> {{ $user->first_name . ' ' . $user->last_name }}</p>
                            </div>
                            <div style="margin-bottom: 15px;">
                                <p style="margin-bottom: 10px;">Please check the applicable box:</p>
                                @php
                                    $hasStudentUnder12 = false;
                                    $hasStudents = false;
                                    $allStudents12OrOlder = true;
                                    
                                    // Check all participants
                                    foreach($participants as $participant) {
                                        if($participant->role === 'student') {
                                            $hasStudents = true;
                                            if($participant->age && (int)$participant->age < 12) {
                                                $hasStudentUnder12 = true;
                                                $allStudents12OrOlder = false;
                                                break;
                                            }
                                        }
                                    }
                                    
                                    // If no students, default to 12 or older
                                    if (!$hasStudents) {
                                        $allStudents12OrOlder = true;
                                        $hasStudentUnder12 = false;
                                    }
                                @endphp
                                <div style="margin-bottom: 10px;">
                                    <label style="display: flex; align-items: flex-start; cursor: pointer;">
                                        <input type="checkbox" name="waiver_under_12" id="waiver_under_12" value="1" checked data-required="{{ $hasStudentUnder12 ? 'true' : 'false' }}" style="margin-right: 10px; margin-top: 3px; width: 18px; height: 18px; cursor: pointer;">
                                        <span>At least one participant is UNDER 12 years of age – Adult registration is MANDATORY.</span>
                                    </label>
                                </div>
                                <div style="margin-bottom: 10px;">
                                    <label style="display: flex; align-items: flex-start; cursor: pointer;">
                                        <input type="checkbox" name="waiver_12_or_older" id="waiver_12_or_older" value="1" {{ !$hasStudentUnder12 ? 'checked' : '' }} data-required="{{ !$hasStudentUnder12 ? 'true' : 'false' }}" style="margin-right: 10px; margin-top: 3px; width: 18px; height: 18px; cursor: pointer;">
                                        <span>All participants are 12 YEARS OR OLDER – Adult registration is optional.</span>
                                    </label>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Liability Waiver and Release -->
                        <div style="margin-bottom: 30px;">
                            <h5 style="font-weight: 600; margin-bottom: 15px; color: #333;">LIABILITY WAIVER AND RELEASE</h5>
                            <p style="margin-bottom: 15px;">By signing this waiver, I acknowledge and agree to the following terms and conditions for participation in the International Research Conference for Children (IRCC'26):</p>
                            
                            <div style="margin-bottom: 20px;">
                                <p style="margin-bottom: 8px;"><strong>1. Supervision Requirements:</strong> I understand that for children under 12 years of age, an accompanying adult must be registered and present at the conference. For children 12 years and older, adult accompaniment is optional but recommended.</p>
                            </div>
                            
                            <div style="margin-bottom: 20px;">
                                <p style="margin-bottom: 8px;"><strong>2. Age Representation and Verification</strong></p>
                                <p style="margin-bottom: 8px;">I certify that the participant's date of birth and age provided during registration are true, accurate, and correct. I understand that providing false or misleading age information may result in removal from the conference without refund.</p>
                            </div>
                            
                            <div style="margin-bottom: 20px;">
                                <p style="margin-bottom: 8px;"><strong>3. Assumption of Risk:</strong> I understand that participation in conference activities may involve certain risks, and I voluntarily assume all risks associated with participation in the conference.</p>
                            </div>
                            
                            
                            
                            <div style="margin-bottom: 20px;">
                                <p style="margin-bottom: 8px;"><strong>4. Medical Treatment:</strong> I authorize conference organizers to obtain emergency medical treatment for the participant if necessary. I understand that I will be responsible for any medical expenses incurred.</p>
                            </div>
                            
                            <div style="margin-bottom: 20px;">
                                <p style="margin-bottom: 8px;"><strong>5. Release of Liability:</strong> I hereby release, waive, discharge, and covenant not to sue the International Research Conference for Children, its organizers, volunteers, staff, sponsors, and affiliated institutions from any and all liability for any injury, loss, or damage to person or property that may occur during participation in the conference.</p>
                            </div>
                            
                            <div style="margin-bottom: 20px;">
                                <p style="margin-bottom: 8px;"><strong>6. Photo/Video Release:</strong> I grant permission for photographs and/or videos taken during the conference to be used for promotional and educational purposes related to IRCC activities.</p>
                            </div>
                            
                            <div style="margin-bottom: 20px;">
                                <p style="margin-bottom: 8px;"><strong>7. Code of Conduct:</strong> I understand that all participants are expected to follow the conference rules and code of conduct. The organizers reserve the right to dismiss any participant whose behavior is deemed disruptive or inappropriate.</p>
                            </div>
                            
                            <div style="margin-bottom: 20px;">
                                <p style="margin-bottom: 8px;"><strong>8. No Refund Policy / Final Payment Clause</strong></p>
                                <p style="margin-bottom: 8px;">I understand and agree that all conference registration fees and related payments are final. All transactions are non-refundable and non-transferable under any circumstances, including but not limited to cancellation, personal schedule conflicts, travel delays, illness, or failure to attend the event.</p>
                            </div>
                        </div>
                        
                        <!-- Acknowledgment -->
                        <div style="margin-bottom: 30px;">
                            <h5 style="font-weight: 600; margin-bottom: 15px; color: #333;">ACKNOWLEDGMENT</h5>
                            <div style="margin-bottom: 20px;">
                                <label style="display: flex; align-items: flex-start; cursor: pointer;">
                                    <input type="checkbox" name="waiver_acknowledgment" id="waiver_acknowledgment" value="1" required style="margin-right: 10px; margin-top: 3px; width: 18px; height: 18px; cursor: pointer;">
                                    <span>I have read, understood, and agree to all terms and conditions stated in this Waiver, Release, and No Refund Policy.</span>
                                </label>
                            </div>
                            
                            <div class="row" style="margin-top: 25px;">
                                <div class="col-md-6" style="margin-bottom: 15px;">
                                    <label for="waiver_signature" style="display: block; margin-bottom: 5px; font-weight: 600;">Signature:</label>
                                    <input type="text" name="waiver_signature" id="waiver_signature" class="form-control" placeholder="Enter your full name" required style="border: 1px solid #ddd; padding: 10px; border-radius: 4px;">
                                </div>
                                <div class="col-md-6" style="margin-bottom: 15px;">
                                    <label for="waiver_printed_name" style="display: block; margin-bottom: 5px; font-weight: 600;">Printed Name:</label>
                                    <input type="text" name="waiver_printed_name" id="waiver_printed_name" class="form-control" value="{{ $user->first_name . ' ' . $user->last_name }}" readonly style="background: #f5f5f5; border: 1px solid #ddd; padding: 10px; border-radius: 4px;">
                                </div>
                                <div class="col-md-6">
                                    <label for="waiver_date" style="display: block; margin-bottom: 5px; font-weight: 600;">Date:</label>
                                    <input type="date" name="waiver_date" id="waiver_date" class="form-control" value="{{ date('Y-m-d') }}" required style="border: 1px solid #ddd; padding: 10px; border-radius: 4px;">
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal" data-bs-dismiss="modal">Close</button>
                <button type="button" id="waiver-submit-btn" class="btn btn-primary" style="background: #F15B43; border-color: #F15B43;">Submit Waiver</button>
            </div>
        </div>
    </div>
</div>

<script>
    console.log('=== make-payment.blade.php script tag loaded ===');
    @php
        $__sPayMain = (float) str_replace(',', '', $payment_summary['studentAmountPayable'] ?? '0');
        $__aPayMain = (float) str_replace(',', '', $payment_summary['adultAmountPayable'] ?? '0');
    @endphp
    window.IRCC_STUDENT_AMOUNT_PAYABLE = {{ json_encode(round($__sPayMain, 2)) }};
    window.IRCC_ADULT_AMOUNT_PAYABLE = {{ json_encode(round($__aPayMain, 2)) }};
    if (typeof window.irccApplyCouponPricing === 'undefined') {
        window.irccApplyCouponPricing = function(coupon) {
            var student = parseFloat(window.IRCC_STUDENT_AMOUNT_PAYABLE);
            if (isNaN(student)) student = 0;
            var adult = parseFloat(window.IRCC_ADULT_AMOUNT_PAYABLE);
            if (isNaN(adult)) adult = 0;
            student = Math.round(student * 100) / 100;
            adult = Math.round(adult * 100) / 100;
            var originalTotal = Math.round((student + adult) * 100) / 100;
            var discountAmount = 0, finalAmount = originalTotal, discountLabel = '';
            if (!coupon || !coupon.type) {
                return { discountAmount: discountAmount, finalAmount: finalAmount, discountLabel: discountLabel, originalTotal: originalTotal };
            }
            if (coupon.type === 'fixed_total') {
                finalAmount = parseFloat(coupon.finalAmount);
                if (isNaN(finalAmount)) finalAmount = 1;
                discountAmount = Math.round((originalTotal - finalAmount) * 100) / 100;
                if (discountAmount < 0) discountAmount = 0;
                discountLabel = 'Fixed price: $' + finalAmount.toFixed(2);
            } else if (coupon.type === 'rise_student') {
                var rise = Math.min(parseFloat(window.IRCC_RISE150_STUDENT_DISCOUNT) || 0, student);
                rise = Math.round(rise * 100) / 100;
                discountAmount = rise;
                finalAmount = Math.round((adult + student - discountAmount) * 100) / 100;
                discountLabel = window.IRCC_RISE150_LABEL || 'RISE early bird benefit activated';
            } else {
                discountAmount = Math.round((student * coupon.discount / 100) * 100) / 100;
                finalAmount = Math.round((adult + student - discountAmount) * 100) / 100;
                discountLabel = coupon.discount + '% off student fees';
            }
            return { discountAmount: discountAmount, finalAmount: finalAmount, discountLabel: discountLabel, originalTotal: originalTotal };
        };
    }
    window.IRCC_RISE150_STUDENT_DISCOUNT = {{ number_format((float) ($rise150_student_discount ?? 0), 2, '.', '') }};
    window.IRCC_RISE150_LABEL = @json(\App\Support\PaymentCoupon::RISE150_LABEL);
    
    // DEFINE COUPON FUNCTION IMMEDIATELY - SIMPLE TOP-LEVEL DEFINITION
    // Coupon codes mapping - make it global IMMEDIATELY
    if (typeof window.couponCodes === 'undefined') {
        window.couponCodes = {
            'Save10': { discount: 10, type: 'percent' },
            'Save5': { discount: 5, type: 'percent' },
            'Save25': { discount: 25, type: 'percent' },
            'RISE150': { type: 'rise_student' },
            'LIFO10': { discount: 10, type: 'percent' },
            'IRCC1': { type: 'fixed_total', finalAmount: 1 }
        };
    }
    
    // Function to handle summary coupon apply - DEFINED IMMEDIATELY (no IIFE)
    if (typeof window.handleSummaryCouponApply === 'undefined') {
        window.handleSummaryCouponApply = function(buttonId, inputId, messageId, discountId, amountId, amountPayableId) {
            console.log('=== handleSummaryCouponApply START ===', { buttonId, inputId, messageId });
            
            // Wait for jQuery if not ready
            if (typeof jQuery === 'undefined' || typeof $ === 'undefined') {
                console.error('jQuery not available yet, waiting...');
                setTimeout(function() {
                    window.handleSummaryCouponApply(buttonId, inputId, messageId, discountId, amountId, amountPayableId);
                }, 100);
                return false;
            }
            
            const summaryInput = $('#' + inputId);
            if (summaryInput.length === 0) {
                console.error('Summary coupon input not found:', inputId);
                if ($('#' + messageId).length) {
                    $('#' + messageId).html('<span style="color: #dc3545;">Coupon input not found. Please refresh the page.</span>');
                }
                return false;
            }

            const code = summaryInput.val().trim().toUpperCase();
            if (!code) {
                $('#' + messageId).html('<span style="color: #dc3545;">Please enter a coupon code.</span>');
                return false;
            }

            console.log('Coupon code entered:', code);

            // Check if couponCodes is available
            if (typeof window.couponCodes === 'undefined') {
                console.error('window.couponCodes not defined');
                $('#' + messageId).html('<span style="color: #dc3545;">Coupon system not initialized. Please refresh the page.</span>');
                return false;
            }

            // Normalize coupon code
            let couponCode = code;
            if (code === 'SAVE10') couponCode = 'Save10';
            else if (code === 'SAVE5') couponCode = 'Save5';
            else if (code === 'SAVE25') couponCode = 'Save25';
            else if (code === 'RISE150') couponCode = 'RISE150';
            else if (code === 'LIFO10') couponCode = 'LIFO10';
            else if (code === 'IRCC1') couponCode = 'IRCC1';

            console.log('Normalized coupon code:', couponCode);
            console.log('Available codes:', Object.keys(window.couponCodes));

            const coupon = window.couponCodes[couponCode];
            if (!coupon) {
                console.log('Coupon not found:', couponCode);
                $('#' + messageId).html('<span style="color: #dc3545;">Invalid coupon code. Please check your code and try again.</span>');
                return false;
            }

            console.log('Coupon found:', coupon);
                            
            // Get current amount payable
            const amountPayableEl = $('#' + amountPayableId);
            if (amountPayableEl.length === 0) {
                console.error('Amount payable element not found:', amountPayableId);
                return false;
            }

            const pr = window.irccApplyCouponPricing(coupon);
            const originalAmount = pr.originalTotal;
            window.originalAmount = originalAmount;
            const discountAmount = pr.discountAmount;
            const finalAmount = pr.finalAmount;
            const discountLabel = pr.discountLabel;

            console.log('Discount calculation:', {
                originalAmount: originalAmount,
                discount: discountAmount,
                finalAmount: finalAmount,
                couponType: coupon.type
            });

            // Update global state
            window.appliedCoupon = couponCode;
            window.discountAmount = discountAmount;

            // Update UI
            $('#' + messageId).html('<span style="color: #198754; font-weight: 600;"><i class="fas fa-check-circle"></i> Coupon applied! ' + discountLabel + '</span>');
            $('#' + discountId).show();
            $('#' + amountId).text('-$' + discountAmount.toFixed(2) + ' (' + discountLabel + ')');

            // Update amount payable
            $('#' + amountPayableId).text('$' + finalAmount.toFixed(2));

            // Also update modal if it exists
            if ($('#modal-amount-payable').length) {
                $('#modal-amount-payable').text('$' + finalAmount.toFixed(2));
            }
            if ($('#summary-amount-payable-2').length) {
                $('#summary-amount-payable-2').text('$' + finalAmount.toFixed(2));
                                }

            // Copy to modal coupon field if it exists
            if ($('#coupon_code').length) {
                $('#coupon_code').val(couponCode);
            }

            console.log('=== handleSummaryCouponApply SUCCESS ===');
            return true;
        };
    }
    
    // Verify function is defined
    if (typeof window.handleSummaryCouponApply === 'function') {
        console.log('✓ handleSummaryCouponApply function defined and available');
        console.log('✓ window.couponCodes defined:', Object.keys(window.couponCodes));
    } else {
        console.error('✗ ERROR: window.handleSummaryCouponApply is NOT a function!');
    }
    
    $(document).ready(function () {
        console.log('=== make-payment.blade.php document ready fired ===');
        // $('.make_payment').on('click', function (e) {
        //     $("html, body").animate({ scrollTop: $(document).height()-1200 }, 1000);
        // });
        // dynamic-link handler is managed by the parent page (index.blade.php) via event delegation
        $('input[name="payment_mode"]').on('change', function () {
            const value = $(this).val();
            console.log('Payment mode changed to:', value); // Debug log
            if (value === 'Direct Bank Transfer') {
                $('#bank-transfer-details').removeClass('d-none');
                $('#givebutter-widget-container').addClass('d-none');
            } else if (value === 'Online Transaction' || value.toLowerCase().includes('online') || value.toLowerCase().includes('transaction')) {
                // Just hide/show payment options, don't open modal here
                // Modal will open when form is submitted
                $('#bank-transfer-details').addClass('d-none');
                $('#givebutter-widget-container').addClass('d-none'); // Hide the separate widget container
            }
        });
        
        // Function to open Givebutter payment in popup window with iframe
        // Make it globally accessible
        window.openPaymentPopupInWindow = function openPaymentPopupInWindow() {
            console.log('=== openPaymentPopupInWindow called ===');
            
            // Validate waiver form before proceeding
            const waiverAcknowledgment = $('#waiver_acknowledgment');
            const waiverSignature = $('#waiver_signature');
            const waiverDate = $('#waiver_date');
            const waiverStatus = $('#waiver-status');
            
            // Check if waiver is completed
            if (!waiverStatus.is(':visible') || !waiverAcknowledgment.is(':checked') || !waiverSignature.val() || !waiverDate.val()) {
                alert('Please complete the Conference Waiver and Release Form before proceeding with payment.');
                $('#waiverModalLink').trigger('click');
                return false;
            }
            
            // Sync waiver data to hidden fields before proceeding
            const syncedWaiverData = syncWaiverDataToForm();
            
            // Use synced data directly (use distinct variable names to avoid const re-declaration conflicts)
            const waiverAcknowledgmentValue = syncedWaiverData.acknowledgment;
            const waiverSignatureValue = syncedWaiverData.signature;
            const waiverDateValue = syncedWaiverData.date;
            const waiverUnder12Value = syncedWaiverData.under12;
            const waiver12OrOlderValue = syncedWaiverData.older12;
            
            // Get payment amount from modal (with coupon discount if applied)
            // Use window. prefix to read global coupon state set by coupon handlers
            var _appliedCoupon = window.appliedCoupon || null;
            var _discountAmount = parseFloat(window.discountAmount) || 0;
            var _originalAmount = parseFloat(window.originalAmount) || 0;
            
            // Get amount from DOM (this should already be updated by coupon handlers if coupon is applied)
            let amountPayable = $('#modal-amount-payable').text().replace('$', '').replace(/,/g, '') || 
                                  $('.amount-payable-box .float-end').last().text().replace('$', '').replace(/,/g, '') ||
                                  '{{ str_replace(",", "", $payment_summary["amountPayable"] ?? "0") }}';
            
            // Parse the amount
            amountPayable = parseFloat(amountPayable) || 0;
            
            // If coupon is applied, use same student/adult split as server (irccApplyCouponPricing)
            if (_appliedCoupon) {
                if (typeof window.couponCodes === 'undefined') {
                    window.couponCodes = {
                        'Save10': { discount: 10, type: 'percent' },
                        'Save5': { discount: 5, type: 'percent' },
                        'Save25': { discount: 25, type: 'percent' },
                        'RISE150': { type: 'rise_student' },
                        'LIFO10': { discount: 10, type: 'percent' },
                        'IRCC1': { type: 'fixed_total', finalAmount: 1 }
                    };
                }
                var coupon = window.couponCodes[_appliedCoupon] || null;
                if (coupon && typeof window.irccApplyCouponPricing === 'function') {
                    var prOpen = window.irccApplyCouponPricing(coupon);
                    amountPayable = prOpen.finalAmount;
                } else if (_discountAmount > 0 && _originalAmount > 0) {
                    amountPayable = _originalAmount - _discountAmount;
                }
            }
            
            // Ensure amount is never negative and format to 2 decimal places
            if (amountPayable < 0) amountPayable = 0;
            amountPayable = amountPayable.toFixed(2);
            
            const participantsCount = $('#modal-participants-count').text() || '{{ $participantsCount ?? 0 }}';
            
            console.log('Opening payment popup:');
            console.log('  - Final amountPayable:', amountPayable);
            console.log('  - Applied coupon:', _appliedCoupon || 'none');
            console.log('  - Original amount:', _originalAmount);
            console.log('  - Discount amount:', _discountAmount);
            console.log('  - Participants:', participantsCount);
            if (_appliedCoupon && window.couponCodes && window.couponCodes[_appliedCoupon]) {
                console.log('  - Coupon type:', window.couponCodes[_appliedCoupon].type);
                if (window.couponCodes[_appliedCoupon].type === 'fixed_total') {
                    console.log('  - Coupon finalAmount:', window.couponCodes[_appliedCoupon].finalAmount);
                }
            }
            console.log('Waiver data:', { 
                waiverAcknowledgment: waiverAcknowledgmentValue, 
                waiverSignature: waiverSignatureValue ? 'present (' + waiverSignatureValue.length + ' chars)' : 'missing',
                waiverDate: waiverDateValue,
                waiverUnder12: waiverUnder12Value,
                waiver12OrOlder: waiver12OrOlderValue
            });
            
            // Validate waiver data before sending
            if (!waiverAcknowledgmentValue || waiverAcknowledgmentValue !== '1' || !waiverSignatureValue || !waiverDateValue) {
                console.error('Waiver validation failed:', {
                    waiverAcknowledgment: waiverAcknowledgmentValue,
                    hasSignature: !!waiverSignatureValue,
                    hasDate: !!waiverDateValue,
                    hiddenFields: {
                        acknowledgment: $('#waiver_acknowledgment_hidden').val(),
                        signature: $('#waiver_signature_hidden').val(),
                        date: $('#waiver_date_hidden').val()
                    },
                    visibleFields: {
                        acknowledgment: $('#waiver_acknowledgment').is(':checked'),
                        signature: $('#waiver_signature').val(),
                        date: $('#waiver_date').val()
                    }
                });
                alert('Please complete the Conference Waiver and Release Form before proceeding with payment.');
                $('#waiverModalLink').trigger('click');
                return false;
            }
            
            // Get payment URL from backend
            $.ajax({
                url: '{{ route("account.registration.payment") }}',
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content'),
                    'X-Requested-With': 'XMLHttpRequest'
                },
                data: {
                    payment_mode: 'Online Transaction',
                    amount: amountPayable,
                    participants: participantsCount,
                    coupon_code: _appliedCoupon || '',
                    discount_amount: _discountAmount.toFixed(2),
                    original_amount: (_originalAmount || parseFloat(amountPayable) || 0).toFixed(2),
                    waiver_acknowledgment: waiverAcknowledgmentValue,
                    waiver_signature: waiverSignatureValue,
                    waiver_date: waiverDateValue,
                    waiver_under_12: waiverUnder12Value,
                    waiver_12_or_older: waiver12OrOlderValue,
                    _token: '{{ csrf_token() }}'
                },
                success: function(response) {
                    console.log('Payment URL response received:', response);
                    
                    if (response && (response.payment_url || response.url || response.checkout_url)) {
                        const paymentUrl = response.payment_url || response.url || response.checkout_url;
                        
                        console.log('Payment URL extracted, opening popup:', paymentUrl);
                        
                        // Create popup window with iframe
                        const popupWindow = window.open(
                            '',
                            'GivebutterPayment',
                            'width=900,height=800,scrollbars=yes,resizable=yes,menubar=no,toolbar=no,location=no,status=no'
                        );
                        
                        console.log('Popup window created:', popupWindow);
                        
                        if (!popupWindow || popupWindow.closed || typeof popupWindow.closed === 'undefined') {
                            // Popup blocked - show error and redirect
                            console.warn('Popup was blocked');
                            swal({
                                title: "Popup Blocked",
                                text: "Please allow popups for this site and try again, or click OK to open in a new tab.",
                                icon: "warning",
                                button: "OK",
                            }).then(function() {
                                window.open(paymentUrl, '_blank');
                            });
                            return;
                        }
                        
                        try {
                            // Hosted Givebutter page in an iframe inside the popup.
                            // This is the most reliable way to pre-fill amount using URL params.
                            
                            // Recalculate the final amount to ensure we have the correct coupon-adjusted value
                            // This is critical because the backend might have used a different amount
                            var _appliedCoupon_check = window.appliedCoupon || null;
                            var _discountAmount_check = parseFloat(window.discountAmount) || 0;
                            var _originalAmount_check = parseFloat(window.originalAmount) || 0;
                            
                            var finalAmount = parseFloat(amountPayable).toFixed(2);
                            
                            if (_appliedCoupon_check) {
                                var coupon_check = window.couponCodes && window.couponCodes[_appliedCoupon_check] ? window.couponCodes[_appliedCoupon_check] : null;
                                if (coupon_check && typeof window.irccApplyCouponPricing === 'function') {
                                    var prUrl = window.irccApplyCouponPricing(coupon_check);
                                    finalAmount = prUrl.finalAmount.toFixed(2);
                                    console.log('Using irccApplyCouponPricing for URL amount:', finalAmount);
                                } else if (_discountAmount_check > 0 && _originalAmount_check > 0) {
                                    finalAmount = (_originalAmount_check - _discountAmount_check).toFixed(2);
                                    console.log('Using fallback coupon calculation:', finalAmount);
                                }
                            }
                            
                            var finalUrl = paymentUrl;

                            // Always rewrite the hosted URL amount so it matches the UI (coupon-adjusted or not).
                            // This also protects against "existing pending payment" sessions returning an old amount.
                            // Parse URL to properly handle query parameters
                            var urlObj = new URL(finalUrl);
                            urlObj.searchParams.set('amount', finalAmount);
                            urlObj.searchParams.set('min', finalAmount);
                            urlObj.searchParams.set('max', finalAmount);
                            // Also try alternative parameter names that Givebutter might use
                            urlObj.searchParams.set('locked', 'true');
                            urlObj.searchParams.set('fixed', 'true');
                            finalUrl = urlObj.toString();
                            
                            // Log URL construction for debugging
                            console.log('Popup window URL construction:');
                            console.log('  - amountPayable from earlier:', amountPayable);
                            console.log('  - Applied coupon:', _appliedCoupon_check);
                            console.log('  - Final calculated amount:', finalAmount);
                            console.log('  - Original payment URL:', paymentUrl);
                            console.log('  - Updated payment URL:', finalUrl);
                            console.log('  - URL amount parameter:', urlObj.searchParams.get('amount'));
                            console.log('  - URL min parameter:', urlObj.searchParams.get('min'));
                            console.log('  - URL max parameter:', urlObj.searchParams.get('max'));

                            popupWindow.document.write('<!DOCTYPE html><html><head><title>Payment - IRCC</title>');
                            popupWindow.document.write('<style>');
                            popupWindow.document.write('body{margin:0;padding:0;font-family:Arial,sans-serif;background:#f5f5f5}');
                            popupWindow.document.write('.pay-hdr{background:#fff;text-align:center;padding:12px 20px;border-bottom:2px solid #F15B43}');
                            popupWindow.document.write('.pay-hdr h2{color:#333;font-size:18px;margin:0 0 4px}');
                            popupWindow.document.write('.pay-hdr .amt{color:#F15B43;font-size:24px;font-weight:bold}');
                            popupWindow.document.write('.pay-note{background:#FFF3CD;border-bottom:1px solid #FFECB5;padding:8px 16px;font-size:12px;color:#664D03;text-align:center}');
                            popupWindow.document.write('#gb-frame{width:100%;border:none;height:calc(100vh - 92px)}');
                            popupWindow.document.write('</style></head><body>');
                            popupWindow.document.write('<div class="pay-hdr"><h2>IRCC Conference Payment</h2><div class="amt">Amount: $' + finalAmount + '</div></div>');
                            popupWindow.document.write('<div class="pay-note"><strong>Important:</strong> Please <strong>uncheck</strong> &quot;I want to cover fees&quot; and set <strong>Additional Donation to $0</strong> to avoid extra charges.</div>');
                            popupWindow.document.write('<iframe id="gb-frame" src="' + finalUrl + '"></iframe>');
                            popupWindow.document.write('</body></html>');
                            popupWindow.document.close();
                            
                            // Monitor popup window for payment completion
                            const checkPopup = setInterval(function() {
                                try {
                                    if (popupWindow.closed) {
                                        clearInterval(checkPopup);
                                        console.log('Popup window closed, checking payment status');
                                        setTimeout(function() {
                                            checkPaymentStatus();
                                        }, 2000);
                                    }
                                } catch (e) {
                                    // Cross-origin error - expected
                                }
                            }, 1000);
                            
                            // Listen for postMessage from popup window
                            window.addEventListener('message', function(event) {
                                if (event.data && event.data.type === 'payment.completed' && event.data.source === 'givebutter-popup') {
                                    clearInterval(checkPopup);
                                    handlePaymentCompletion();
                                }
                            });
                            
                        } catch (error) {
                            console.error('Error writing to popup window:', error);
                            swal("Error", "Failed to open payment window. Please try again.", "error");
                        }
                        
                    } else {
                        console.error('No payment URL in response:', response);
                        swal("Error", "Failed to get payment URL. Please try again.", "error");
                    }
                },
                error: function(xhr, status, error) {
                    console.error('Failed to get payment URL:', status, error, xhr.responseText);
                    let errorMessage = "Failed to initialize payment. Please try again.";
                    
                    // Try to extract error message from response
                    if (xhr.responseJSON && xhr.responseJSON.message) {
                        errorMessage = xhr.responseJSON.message;
                    } else if (xhr.responseText) {
                        try {
                            const response = JSON.parse(xhr.responseText);
                            if (response.message) {
                                errorMessage = response.message;
                            }
                        } catch (e) {
                            // Not JSON, use default message
                        }
                    }
                    
                    swal("Error", errorMessage, "error");
                    
                    // If it's a waiver error, open the waiver modal
                    if (errorMessage.includes('waiver') || errorMessage.includes('Waiver')) {
                        setTimeout(function() {
                            $('#waiverModalLink').trigger('click');
                        }, 1000);
                    }
                }
            });
        };
        
        // Make function globally accessible
        window.openPaymentPopupInWindow = openPaymentPopupInWindow;
        
        // Legacy function name for backward compatibility
        window.openPaymentPopup = function openPaymentPopup() {
            if (typeof window.openPaymentPopupInWindow === 'function') {
                window.openPaymentPopupInWindow();
            } else {
                console.error('openPaymentPopupInWindow not available');
            }
        };
        
        // Test function availability on page load
        console.log('=== Payment functions initialized ===');
        console.log('openPaymentPopupInWindow available:', typeof window.openPaymentPopupInWindow === 'function');
        
        // Function to check payment status
        function checkPaymentStatus() {
            $.ajax({
                url: '{{ route("payment.summary") }}',
                method: 'GET',
                headers: { 'X-Requested-With': 'XMLHttpRequest' },
                success: function(response) {
                    if (response && response.success) {
                        const amountPayable = parseFloat(response.payment_summary?.amountPayable || 0);
                        const paidAmount = parseFloat(response.paidAmount || 0);
                        const previousAmountPayable = parseFloat($('#modal-amount-payable').text().replace('$', '').replace(/,/g, '') || 0);
                        
                        // If amount payable is 0 or significantly reduced, payment might be complete
                        if (amountPayable === 0 || (paidAmount > 0 && amountPayable < previousAmountPayable)) {
                            handlePaymentCompletion();
                        }
                    }
                }
            });
        }
        
        // Attach click handler to payment button in modal - use multiple selectors to ensure it works
        $(document).on('click', '#open-payment-popup-btn', function(e) {
            e.preventDefault();
            e.stopPropagation();
            e.stopImmediatePropagation();
            console.log('=== Open Payment Window button clicked ===');
            
            // Validate waiver form before proceeding
            const waiverAcknowledgment = $('#waiver_acknowledgment');
            const waiverSignature = $('#waiver_signature');
            const waiverDate = $('#waiver_date');
            const waiverStatus = $('#waiver-status');
            
            // Check if waiver is completed
            if (!waiverStatus.is(':visible') || !waiverAcknowledgment.is(':checked') || !waiverSignature.val() || !waiverDate.val()) {
                alert('Please complete the Conference Waiver and Release Form before proceeding with payment.');
                $('#payment-confirmation-modal').modal('hide');
                $('#waiverModalLink').trigger('click');
                return false;
            }
            
            // Sync waiver data to hidden fields before proceeding
            syncWaiverDataToForm();
            
            if (typeof window.openPaymentPopupInWindow === 'function') {
                console.log('Calling openPaymentPopupInWindow function');
                window.openPaymentPopupInWindow();
            } else {
                console.error('openPaymentPopupInWindow function not defined!');
                alert('Payment function not available. Please refresh the page.');
            }
            return false;
        });
        
        // Also attach directly to the button when modal is shown (as backup)
        $('#payment-confirmation-modal').on('shown.bs.modal', function() {
            console.log('=== Modal shown event fired ===');
            console.log('Button element found:', $('#open-payment-popup-btn').length);
            
            const fullModal = (parseFloat(window.IRCC_STUDENT_AMOUNT_PAYABLE) || 0) + (parseFloat(window.IRCC_ADULT_AMOUNT_PAYABLE) || 0);
            if (fullModal > 0) {
                window.originalAmount = fullModal;
                originalAmount = fullModal;
                $('#modal-subtotal').text('$' + fullModal.toFixed(2));
                console.log('Coupon system initialized with amount:', fullModal);
            }

            if (window.appliedCoupon) {
                $('#coupon_code').val(window.appliedCoupon);
                const coupon = couponCodes[window.appliedCoupon];
                if (coupon && typeof window.irccApplyCouponPricing === 'function') {
                    const prSync = window.irccApplyCouponPricing(coupon);
                    const syncLabel = prSync.discountLabel;
                    $('#coupon-message').html('<span style="color: #198754; font-weight: 600;"><i class="fas fa-check-circle"></i> Coupon applied! ' + syncLabel + '</span>');
                    $('#coupon-discount').show();
                    $('#discount-amount').text('-$' + prSync.discountAmount.toFixed(2) + ' (' + syncLabel + ')');
                    $('#modal-amount-payable').text('$' + prSync.finalAmount.toFixed(2));
                    $('#modal-subtotal').text('$' + prSync.originalTotal.toFixed(2));
                }
            }
            $('#open-payment-popup-btn').off('click').on('click', function(e) {
                e.preventDefault();
                e.stopPropagation();
                e.stopImmediatePropagation();
                console.log('=== Open Payment Window button clicked (from modal shown event) ===');
                
                // Validate waiver form before proceeding
                const waiverAcknowledgment = $('#waiver_acknowledgment');
                const waiverSignature = $('#waiver_signature');
                const waiverDate = $('#waiver_date');
                const waiverStatus = $('#waiver-status');
                
                // Check if waiver is completed
                if (!waiverStatus.is(':visible') || !waiverAcknowledgment.is(':checked') || !waiverSignature.val() || !waiverDate.val()) {
                    alert('Please complete the Conference Waiver and Release Form before proceeding with payment.');
                    $('#payment-confirmation-modal').modal('hide');
                    $('#waiverModalLink').trigger('click');
                    return false;
                }
                
                // Sync waiver data to hidden fields before proceeding
                syncWaiverDataToForm();
                
                if (typeof window.openPaymentPopupInWindow === 'function') {
                    window.openPaymentPopupInWindow();
                } else {
                    console.error('openPaymentPopupInWindow function not defined!');
                    alert('Payment function not available. Please refresh the page.');
                }
                return false;
            });
        });
        
        // Listen for postMessage from Givebutter iframe (if they support it)
        window.addEventListener('message', function(event) {
            // Verify origin is from Givebutter
            if (event.origin !== 'https://givebutter.com' && event.origin !== 'https://www.givebutter.com') {
                return;
            }
            
            // Check if payment was completed
            if (event.data && (event.data.type === 'payment.completed' || event.data.status === 'completed' || event.data.payment_status === 'completed')) {
                console.log('Payment completed via postMessage:', event.data);
                handlePaymentCompletion();
            }
        });
        
        // Function to handle payment completion
        function handlePaymentCompletion() {
            // Close modal
            $('#payment-confirmation-modal').modal('hide');
            
            // Show success message
            swal({
                title: "Payment Processing!",
                text: "Your payment is being processed. The page will refresh shortly to show your updated payment status.",
                icon: "success",
                button: "OK",
            });
            
            // Wait a moment for webhook to process, then refresh
            setTimeout(function() {
                window.location.reload();
            }, 3000);
        }
        
        // Monitor iframe URL changes to detect payment completion
        // Note: This may not work due to cross-origin restrictions, but we'll try
        let lastIframeUrl = '';
        setInterval(function() {
            try {
                const iframe = document.getElementById('givebutter-payment-iframe');
                if (iframe && iframe.contentWindow) {
                    const currentUrl = iframe.contentWindow.location.href;
                    if (currentUrl !== lastIframeUrl) {
                        lastIframeUrl = currentUrl;
                        // Check if URL contains success indicators
                        if (currentUrl.includes('success') || currentUrl.includes('completed') || currentUrl.includes('thank')) {
                            handlePaymentCompletion();
                        }
                    }
                }
            } catch (e) {
                // Cross-origin restriction - expected, we'll rely on webhook and postMessage
            }
        }, 2000);
        
        // Also check for payment completion by polling the payment status
        let paymentCheckInterval = null;
        $('#payment-confirmation-modal').on('shown.bs.modal', function() {
            // Start checking payment status every 5 seconds
            paymentCheckInterval = setInterval(function() {
                $.ajax({
                    url: '{{ route("payment.summary") }}',
                    method: 'GET',
                    headers: { 'X-Requested-With': 'XMLHttpRequest' },
                    success: function(response) {
                        if (response && response.success) {
                            // Check if payment was completed (amountPayable is 0 or paidAmount increased)
                            const amountPayable = parseFloat(response.payment_summary?.amountPayable || 0);
                            const paidAmount = parseFloat(response.paidAmount || 0);
                            
                            // If amount payable is 0 or significantly reduced, payment might be complete
                            if (amountPayable === 0 || (paidAmount > 0 && amountPayable < parseFloat($('#modal-amount-payable').text().replace('$', '').replace(/,/g, '')))) {
                                handlePaymentCompletion();
                                if (paymentCheckInterval) {
                                    clearInterval(paymentCheckInterval);
                                    paymentCheckInterval = null;
                                }
                            }
                        }
                    }
                });
            }, 5000);
        });
        
        $('#payment-confirmation-modal').on('hidden.bs.modal', function() {
            // Stop checking when modal is closed
            if (paymentCheckInterval) {
                clearInterval(paymentCheckInterval);
                paymentCheckInterval = null;
            }
        });
        
        // Legacy code for widget (keeping for backward compatibility but not used in iframe mode)
        if (false) { // Disabled for iframe mode
                // Function to make widget amount field read-only
                function makeWidgetAmountReadOnly(widget) {
                    if (!widget) return;
                    
                    try {
                        // Try to access the widget's shadow DOM
                        const shadowRoot = widget.shadowRoot;
                        if (shadowRoot) {
                            // Find amount input fields in shadow DOM
                            const amountInputs = shadowRoot.querySelectorAll('input[type="number"], input[name*="amount"], input[id*="amount"], input[class*="amount"]');
                            amountInputs.forEach(function(input) {
                                input.setAttribute('readonly', 'readonly');
                                input.setAttribute('disabled', 'disabled');
                                input.style.pointerEvents = 'none';
                                input.style.backgroundColor = '#f5f5f5';
                                input.style.cursor = 'not-allowed';
                            });
                        }
                        
                        // Also try to find iframe inside widget
                        const iframe = widget.querySelector('iframe');
                        if (iframe) {
                            try {
                                const iframeDoc = iframe.contentDocument || iframe.contentWindow.document;
                                const iframeAmountInputs = iframeDoc.querySelectorAll('input[type="number"], input[name*="amount"], input[id*="amount"], input[class*="amount"]');
                                iframeAmountInputs.forEach(function(input) {
                                    input.setAttribute('readonly', 'readonly');
                                    input.setAttribute('disabled', 'disabled');
                                    input.style.pointerEvents = 'none';
                                    input.style.backgroundColor = '#f5f5f5';
                                    input.style.cursor = 'not-allowed';
                                });
                            } catch (e) {
                                // Cross-origin iframe, can't access - this is expected
                                console.log('Cannot access iframe content (cross-origin), using alternative method');
                            }
                        }
                        
                        // Use MutationObserver to watch for dynamically added amount fields
                        const observer = new MutationObserver(function(mutations) {
                            mutations.forEach(function(mutation) {
                                mutation.addedNodes.forEach(function(node) {
                                    if (node.nodeType === 1) { // Element node
                                        const amountInputs = node.querySelectorAll ? node.querySelectorAll('input[type="number"], input[name*="amount"], input[id*="amount"], input[class*="amount"]') : [];
                                        amountInputs.forEach(function(input) {
                                            input.setAttribute('readonly', 'readonly');
                                            input.setAttribute('disabled', 'disabled');
                                            input.style.pointerEvents = 'none';
                                            input.style.backgroundColor = '#f5f5f5';
                                            input.style.cursor = 'not-allowed';
                                        });
                                        
                                        // Also check if the node itself is an amount input
                                        if (node.tagName === 'INPUT' && (node.type === 'number' || (node.name && node.name.includes('amount')) || (node.id && node.id.includes('amount')))) {
                                            node.setAttribute('readonly', 'readonly');
                                            node.setAttribute('disabled', 'disabled');
                                            node.style.pointerEvents = 'none';
                                            node.style.backgroundColor = '#f5f5f5';
                                            node.style.cursor = 'not-allowed';
                                        }
                                    }
                                });
                            });
                        });
                        
                        // Observe widget for changes
                        observer.observe(widget, {
                            childList: true,
                            subtree: true
                        });
                        
                        // Also observe shadow root if available
                        if (shadowRoot) {
                            observer.observe(shadowRoot, {
                                childList: true,
                                subtree: true
                            });
                        }
                    } catch (e) {
                        console.warn('Could not make widget amount read-only:', e);
                    }
                }
                
                // Function to refresh payment details in modal (defined globally for access by intervals)
                let refreshErrorCount = 0;
                const MAX_REFRESH_ERRORS = 3;
                let refreshDisabled = false;
                let modalRefreshInterval = null; // Define globally even if periodic refresh is disabled
                
                function refreshModalPaymentDetails() {
                    // Don't attempt refresh if it's been disabled due to errors
                    if (refreshDisabled) {
                        return;
                    }
                    
                    console.log('Refreshing modal payment details...');
                    $.ajax({
                        url: '{{ route("payment.summary") }}',
                        headers: { 'X-Requested-With': 'XMLHttpRequest' },
                        method: 'GET',
                        timeout: 5000, // 5 second timeout
                        success: function(response) {
                            refreshErrorCount = 0; // Reset error count on success
                            if (response && response.success) {
                                const summary = response.payment_summary;
                                
                                if (!summary) {
                                    console.warn('Payment summary data missing in response');
                                    return;
                                }

                                if (summary.studentAmountPayable !== undefined) {
                                    window.IRCC_STUDENT_AMOUNT_PAYABLE = parseFloat(String(summary.studentAmountPayable).replace(/,/g, '')) || 0;
                                }
                                if (summary.adultAmountPayable !== undefined) {
                                    window.IRCC_ADULT_AMOUNT_PAYABLE = parseFloat(String(summary.adultAmountPayable).replace(/,/g, '')) || 0;
                                }
                                if (typeof response.rise150_student_discount !== 'undefined') {
                                    window.IRCC_RISE150_STUDENT_DISCOUNT = parseFloat(response.rise150_student_discount) || 0;
                                }
                                
                                // Update payment summary in modal
                                if (response.hasPayments && response.paidAmount > 0) {
                                    $('#modal-paid-amount').text('$' + response.paidAmount);
                                    $('#payment-summary-box p:has(#modal-paid-amount)').show();
                                } else {
                                    $('#payment-summary-box p:has(#modal-paid-amount)').hide();
                                }
                                
                                if (summary.amountPerHead) {
                                    $('#modal-amount-per-head').text('$' + summary.amountPerHead);
                                }
                                if (response.participantsCount !== undefined) {
                                    $('#modal-participants-count').text(response.participantsCount);
                                }
                                if (summary.amountPayable) {
                                    $('#modal-amount-payable').text('$' + summary.amountPayable);
                                    
                                    // Update widget amount
                                    const widget = document.querySelector('#payment-confirmation-modal givebutter-widget');
                                    if (widget) {
                                        const newAmount = summary.amountPayable.replace(/,/g, '');
                                        widget.setAttribute('amount', newAmount);
                                        console.log('Widget amount updated to:', newAmount);
                                        // Make amount read-only after updating
                                        setTimeout(function() {
                                            makeWidgetAmountReadOnly(widget);
                                        }, 500);
                                    }
                                }
                                
                                console.log('Modal payment details refreshed successfully');
                            } else {
                                console.warn('Payment summary response indicates failure:', response?.error || 'Unknown error');
                            }
                        },
                        error: function(xhr, status, error) {
                            refreshErrorCount++;
                            console.error('Failed to refresh payment summary:', status, error, 'Error count:', refreshErrorCount);
                            
                            // If too many errors, stop the periodic refresh
                            if (refreshErrorCount >= MAX_REFRESH_ERRORS) {
                                console.warn('Too many refresh errors (' + refreshErrorCount + '), stopping periodic refresh. The modal will still work, but won\'t auto-update.');
                                if (modalRefreshInterval) {
                                    clearInterval(modalRefreshInterval);
                                    modalRefreshInterval = null;
                                }
                                refreshDisabled = true;
                            }
                        }
                    });
                }
                
                // Initialize widget in modal after it's shown (use 'on' instead of 'one' so it works every time)
                $('#payment-confirmation-modal').off('shown.bs.modal').on('shown.bs.modal', function () {
                    // Try to fetch updated payment summary, but don't block modal if it fails
                    $.ajax({
                        url: '{{ route("payment.summary") }}',
                        headers: { 'X-Requested-With': 'XMLHttpRequest' },
                        method: 'GET',
                        timeout: 5000,
                        success: function(response) {
                            if (response && response.success) {
                                // Update payment summary in modal
                                const summary = response.payment_summary;
                                
                                if (!summary) {
                                    console.warn('Payment summary data missing, using existing values');
                                    return;
                                }

                                if (summary.studentAmountPayable !== undefined) {
                                    window.IRCC_STUDENT_AMOUNT_PAYABLE = parseFloat(String(summary.studentAmountPayable).replace(/,/g, '')) || 0;
                                }
                                if (summary.adultAmountPayable !== undefined) {
                                    window.IRCC_ADULT_AMOUNT_PAYABLE = parseFloat(String(summary.adultAmountPayable).replace(/,/g, '')) || 0;
                                }
                                if (typeof response.rise150_student_discount !== 'undefined') {
                                    window.IRCC_RISE150_STUDENT_DISCOUNT = parseFloat(response.rise150_student_discount) || 0;
                                }
                                
                                // Update Amount Paid
                                if (response.hasPayments && response.paidAmount > 0) {
                                    $('#modal-paid-amount').text('$' + response.paidAmount);
                                    // Show the Amount Paid row if it was hidden
                                    $('#payment-summary-box p:has(#modal-paid-amount)').show();
                                } else {
                                    $('#payment-summary-box p:has(#modal-paid-amount)').hide();
                                }
                                
                                // Update Amount Per Participant
                                if (summary.amountPerHead) {
                                    $('#modal-amount-per-head').text('$' + summary.amountPerHead);
                                }
                                
                                // Update Number of Participants
                                if (response.participantsCount !== undefined) {
                                    $('#modal-participants-count').text(response.participantsCount);
                                }
                                
                                // Update Total Amount Payable
                                if (summary.amountPayable) {
                                    $('#modal-amount-payable').text('$' + summary.amountPayable);
                                    
                                    // Update widget amount
                                    const widget = document.querySelector('#payment-confirmation-modal givebutter-widget');
                                    if (widget) {
                                        const newAmount = summary.amountPayable.replace(/,/g, '');
                                        widget.setAttribute('amount', newAmount);
                                        console.log('Widget amount updated to:', newAmount);
                                        // Make amount read-only after updating
                                        setTimeout(function() {
                                            makeWidgetAmountReadOnly(widget);
                                        }, 500);
                                    }
                                }
                                
                                console.log('Payment summary updated in modal');
                            } else {
                                console.warn('Payment summary response indicates failure, using existing values');
                            }
                        },
                        error: function(xhr, status, error) {
                            // Silently fail - modal will use existing values from page load
                            console.warn('Failed to fetch updated payment summary, using existing values:', status);
                        }
                    });
                    
                    // Format card number input
                    $('#card_number').on('input', function() {
                        let value = $(this).val().replace(/\s/g, '');
                        let formattedValue = value.match(/.{1,4}/g)?.join(' ') || value;
                        $(this).val(formattedValue);
                    });
                    
                    // Format expiry date input
                    $('#expiry_date').on('input', function() {
                        let value = $(this).val().replace(/\D/g, '');
                        if (value.length >= 2) {
                            value = value.substring(0, 2) + '/' + value.substring(2, 4);
                        }
                        $(this).val(value);
                    });
                    
                    // Format CVV input (numbers only)
                    $('#cvv').on('input', function() {
                        $(this).val($(this).val().replace(/\D/g, ''));
                    });
                    
                    // Direct payment form handler - attach it here so it's available when modal opens
                    attachPaymentFormHandler();
                });
                
                // Function to attach payment form handler (can be called multiple times)
                // Note: Handler is already attached at document level using event delegation
                // This function is kept for backward compatibility but doesn't need to do anything
                function attachPaymentFormHandler() {
                    // Handler is already attached at document level, so just log
                    console.log('attachPaymentFormHandler called - handler already attached at document level');
                    return;
                }
                
                // Also attach handler when modal is shown (in case form is recreated)
                $('#payment-confirmation-modal').on('shown.bs.modal', function() {
                    attachPaymentFormHandler();
                    
                    // Function to check payment status and refresh dashboard
                    function checkPaymentStatus() {
                        console.log('Checking payment status...');
                        
                        // Close modal
                        $('#payment-confirmation-modal').modal('hide');
                        
                        // Wait a moment for webhook to process, then refresh dashboard
                        setTimeout(function() {
                            const dynamicContent = $('#dynamic-content');
                            if (dynamicContent.length) {
                                const dashboardUrl = $('.link-dashboard').data('url') || $('.link-payment').data('url');
                                if (dashboardUrl) {
                                    $('#loading').show();
                                    $.ajax({
                                        url: dashboardUrl,
                                        method: 'GET',
                                        success: function(data) {
                                            dynamicContent.html(data);
                                            $('#loading').hide();
                                            console.log('Dashboard refreshed after payment');
                                        },
                                        error: function(xhr, status, error) {
                                            $('#loading').hide();
                                            console.error('Failed to refresh dashboard:', status, error);
                                            window.location.reload();
                                        }
                                    });
                                } else {
                                    window.location.reload();
                                }
                            } else {
                                window.location.reload();
                            }
                        }, 2000); // Wait 2 seconds for webhook to process
                    }
                });
                
                // Refresh payment details periodically while modal is open
                // DISABLED: Only enable if /payment/summary endpoint is working
                // Uncomment the code below once the endpoint is fixed
                /*
                let modalRefreshInterval = null;
                $('#payment-confirmation-modal').off('shown.bs.modal').on('shown.bs.modal', function () {
                    // Start periodic refresh every 5 seconds while modal is open
                    // This ensures the modal updates when payment completes via widget
                    modalRefreshInterval = setInterval(function() {
                        if ($('#payment-confirmation-modal').hasClass('show')) {
                            console.log('Periodic refresh of modal payment details...');
                            refreshModalPaymentDetails();
                        } else {
                            if (modalRefreshInterval) {
                                clearInterval(modalRefreshInterval);
                                modalRefreshInterval = null;
                            }
                        }
                    }, 5000); // Refresh every 5 seconds
                });
                */
                
                // Reset payment UI when modal is closed
                $('#payment-confirmation-modal').off('hidden.bs.modal').on('hidden.bs.modal', function () {
                    // Re-enable payment button and reset UI
                    $('#proceed-to-payment-btn').prop('disabled', false).show();
                    $('#payment-processing').hide();
                    $('#payment-status-message').hide();
                    
                    // Close payment window if still open
                    if (paymentWindow && !paymentWindow.closed) {
                        paymentWindow.close();
                    }
                    paymentWindow = null;
                    
                    // Clear any polling intervals
                    if (paymentCheckInterval) {
                        clearInterval(paymentCheckInterval);
                        paymentCheckInterval = null;
                    }
                    
                    // Remove window focus listener
                    $(window).off('focus.payment');
                    
                    // Stop periodic refresh if it exists
                    if (typeof modalRefreshInterval !== 'undefined' && modalRefreshInterval) {
                        clearInterval(modalRefreshInterval);
                        modalRefreshInterval = null;
                    }
                    // Wait a moment for payment to process via webhook, then refresh
                    setTimeout(function() {
                        // First, fetch updated payment summary to refresh modal content for next time
                        $.ajax({
                            url: '{{ route("payment.summary") }}',
                            headers: { 'X-Requested-With': 'XMLHttpRequest' },
                            method: 'GET',
                            success: function(response) {
                                if (response.success) {
                                    const summary = response.payment_summary;

                                    if (summary && summary.studentAmountPayable !== undefined) {
                                        window.IRCC_STUDENT_AMOUNT_PAYABLE = parseFloat(String(summary.studentAmountPayable).replace(/,/g, '')) || 0;
                                    }
                                    if (summary && summary.adultAmountPayable !== undefined) {
                                        window.IRCC_ADULT_AMOUNT_PAYABLE = parseFloat(String(summary.adultAmountPayable).replace(/,/g, '')) || 0;
                                    }
                                    if (typeof response.rise150_student_discount !== 'undefined') {
                                        window.IRCC_RISE150_STUDENT_DISCOUNT = parseFloat(response.rise150_student_discount) || 0;
                                    }
                                    
                                    // Update payment summary in modal (for next time it opens)
                                    if (response.hasPayments && response.paidAmount > 0) {
                                        $('#modal-paid-amount').text('$' + response.paidAmount);
                                        $('#payment-summary-box p:has(#modal-paid-amount)').show();
                                    } else {
                                        $('#payment-summary-box p:has(#modal-paid-amount)').hide();
                                    }
                                    
                                    $('#modal-amount-per-head').text('$' + summary.amountPerHead);
                                    $('#modal-participants-count').text(response.participantsCount);
                                    $('#modal-amount-payable').text('$' + summary.amountPayable);
                                    
                                    // Update widget amount for next time
                                    const widget = document.querySelector('#payment-confirmation-modal givebutter-widget');
                                    if (widget) {
                                        widget.setAttribute('amount', summary.amountPayable.replace(/,/g, ''));
                                        // Make amount read-only after updating
                                        setTimeout(function() {
                                            makeWidgetAmountReadOnly(widget);
                                        }, 500);
                                    }
                                    
                                    console.log('Modal payment details refreshed');
                                }
                            },
                            error: function() {
                                console.error('Failed to refresh payment summary');
                            }
                        });
                        
                        // Also refresh the dashboard content
                        const dynamicContent = $('#dynamic-content');
                        if (dynamicContent.length) {
                            // Reload the dashboard view to show updated payment details
                            const dashboardUrl = $('.link-dashboard').data('url') || $('.link-payment').data('url');
                            if (dashboardUrl) {
                                $('#loading').show();
                                $.ajax({
                                    url: dashboardUrl,
                                    headers: { 'X-Requested-With': 'XMLHttpRequest' },
                                    success: function (html) {
                                        $('#loading').hide();
                                        dynamicContent.html(html);
                                        console.log('Dashboard refreshed with updated payment details');
                                    },
                                    error: function () {
                                        $('#loading').hide();
                                        console.error('Failed to refresh dashboard');
                                        // Fallback: reload the page
                                        window.location.reload();
                                    }
                                });
                            } else {
                                // Fallback: reload the page
                                window.location.reload();
                            }
                        } else {
                            // If not in AJAX context, reload the page
                            window.location.reload();
                        }
                    }, 2000); // Wait 2 seconds for payment to process via webhook
                });
            } else {
                $('#bank-transfer-details').addClass('d-none');
                $('#givebutter-widget-container').addClass('d-none');
            }
        });

        // Function to process payment (extracted for reuse) - Define FIRST so it's available
        function processPayment(form, btn, processingDiv) {
            console.log('processPayment function called', {form: form.length, btn: btn.length, processingDiv: processingDiv.length});
            
            // Validate form fields manually (form element might not exist due to HTML stripping)
            const cardholderName = $('#cardholder_name');
            const cardNumber = $('#card_number');
            const expiryDate = $('#expiry_date');
            const cvv = $('#cvv');
            const billingEmail = $('#billing_email');
            
            // Manual validation
            if (!cardholderName.val() || cardholderName.val().trim() === '') {
                alert('Please enter cardholder name.');
                cardholderName.focus();
                return false;
            }
            if (!cardNumber.val() || cardNumber.val().replace(/\s/g, '').length < 13) {
                alert('Please enter a valid card number.');
                cardNumber.focus();
                return false;
            }
            if (!expiryDate.val() || !/^\d{2}\/\d{2}$/.test(expiryDate.val())) {
                alert('Please enter a valid expiry date (MM/YY).');
                expiryDate.focus();
                return false;
            }
            if (!cvv.val() || cvv.val().length < 3) {
                alert('Please enter a valid CVV.');
                cvv.focus();
                return false;
            }
            if (!billingEmail.val() || !/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(billingEmail.val())) {
                alert('Please enter a valid billing email.');
                billingEmail.focus();
                return false;
            }
            
            // If form element exists, also use its validation
            if (form && form.length > 0 && form[0] && form[0].checkValidity) {
                if (!form[0].checkValidity()) {
                    console.log('Form validation failed in processPayment');
                    form[0].reportValidity();
                    return false;
                }
            }
            
            console.log('Form validation passed, processing payment...');
            
            // Disable button and show processing
            if (btn && btn.length) {
                btn.prop('disabled', true);
            }
            if (processingDiv && processingDiv.length) {
                processingDiv.show();
            }
            
            // Collect form data
            const formData = {
                payment_mode: 'Online Transaction',
                cardholder_name: $('#cardholder_name').val(),
                card_number: $('#card_number').val().replace(/\s/g, ''),
                expiry_date: $('#expiry_date').val(),
                cvv: $('#cvv').val(),
                billing_email: $('#billing_email').val(),
                _token: '{{ csrf_token() }}'
            };
            
            console.log('Submitting payment form with data:', {
                payment_mode: formData.payment_mode,
                cardholder_name: formData.cardholder_name,
                card_number: formData.card_number ? '****' + formData.card_number.slice(-4) : 'empty',
                expiry_date: formData.expiry_date,
                cvv: formData.cvv ? '***' : 'empty',
                billing_email: formData.billing_email
            });
            
            // Make AJAX call to process payment via API
            $.ajax({
                url: '{{ route("account.registration.payment") }}',
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content'),
                    'X-Requested-With': 'XMLHttpRequest'
                },
                data: formData,
                success: function(response) {
                    console.log('Payment processed successfully', response);
                    
                    if (response.success) {
                        // Payment successful - close modal and refresh dashboard
                        $('#payment-confirmation-modal').modal('hide');
                        
                        // Wait a moment then refresh dashboard
                        setTimeout(function() {
                            checkPaymentStatus();
                        }, 1000);
                    } else {
                        // Payment failed
                        btn.prop('disabled', false);
                        processingDiv.hide();
                        if (typeof swal !== 'undefined') {
                            swal("Error", 'Payment failed: ' + (response.message || 'Unknown error'), "error");
                        } else {
                            alert('Payment failed: ' + (response.message || 'Unknown error'));
                        }
                    }
                },
                error: function(xhr, status, error) {
                    console.error('Payment processing failed:', status, error);
                    console.error('Response status:', xhr.status);
                    console.error('Response text:', xhr.responseText);
                    
                    // Re-enable button
                    btn.prop('disabled', false);
                    processingDiv.hide();
                    
                    // Show error message
                    let errorMsg = 'Failed to process payment. Please try again.';
                    if (xhr.responseJSON && xhr.responseJSON.message) {
                        errorMsg = xhr.responseJSON.message;
                    } else if (xhr.responseJSON && xhr.responseJSON.error) {
                        errorMsg = xhr.responseJSON.error;
                    } else if (xhr.responseText) {
                        try {
                            const response = JSON.parse(xhr.responseText);
                            if (response.message) {
                                errorMsg = response.message;
                            } else if (response.error) {
                                errorMsg = response.error;
                            }
                        } catch (e) {
                            // If response is HTML, try to extract error
                            if (xhr.responseText.includes('error') || xhr.responseText.includes('Error')) {
                                errorMsg = 'Server error occurred. Please check the console for details.';
                            }
                        }
                    }
                    
                    // Show error in a better way
                    if (typeof swal !== 'undefined') {
                        swal("Error", errorMsg, "error");
                    } else {
                        alert('Error: ' + errorMsg);
                    }
                }
            });
            
            return false;
        }
        
        // Attach payment form handler on document ready (using event delegation)
        // These handlers are attached AFTER the function is defined
        $(document).on('submit', '#direct-payment-form', function(e) {
            e.preventDefault();
            e.stopPropagation();
            
            console.log('Payment form submitted (document ready handler)');
            
            const form = $(this);
            const btn = $('#process-payment-btn');
            const processingDiv = $('#payment-processing');
            
            processPayment(form, btn, processingDiv);
            
            return false;
        });
        
        // Also attach click handler directly on the button as backup
        $(document).on('click', '#process-payment-btn', function(e) {
            e.preventDefault();
            e.stopPropagation();
            
            console.log('Process Payment button clicked');
            
            // Find form within the modal context (more reliable)
            const modal = $('#payment-confirmation-modal');
            let form = modal.find('#direct-payment-form').length ? modal.find('#direct-payment-form') : $('#direct-payment-form');
            const btn = $(this);
            const processingDiv = modal.find('#payment-processing').length ? modal.find('#payment-processing') : $('#payment-processing');
            
            console.log('Form found:', form.length, 'Modal found:', modal.length);
            
            // If form doesn't exist, create it dynamically or use the button's parent container
            if (form.length === 0) {
                console.warn('Form tag not found, but form fields exist. Creating form wrapper or using parent container.');
                
                // Find the button's parent container that has all the form fields
                const buttonParent = btn.closest('.modal-body');
                
                // Check if form fields exist
                const cardholderName = $('#cardholder_name');
                const cardNumber = $('#card_number');
                
                if (cardholderName.length > 0 && cardNumber.length > 0) {
                    // Form fields exist, create a wrapper div to act as form
                    // Or just use the modal body as the form container
                    form = buttonParent;
                    console.log('Using modal body as form container');
                } else {
                    console.error('Form fields not found!');
                    alert('Payment form fields not found. Please refresh the page and try again.');
                    return false;
                }
            }
            
            // Validate form fields manually if form element doesn't exist
            const cardholderName = $('#cardholder_name');
            const cardNumber = $('#card_number');
            const expiryDate = $('#expiry_date');
            const cvv = $('#cvv');
            const billingEmail = $('#billing_email');
            
            // Manual validation
            let isValid = true;
            if (!cardholderName.val() || cardholderName.val().trim() === '') {
                alert('Please enter cardholder name.');
                cardholderName.focus();
                isValid = false;
            } else if (!cardNumber.val() || cardNumber.val().replace(/\s/g, '').length < 13) {
                alert('Please enter a valid card number.');
                cardNumber.focus();
                isValid = false;
            } else if (!expiryDate.val() || !/^\d{2}\/\d{2}$/.test(expiryDate.val())) {
                alert('Please enter a valid expiry date (MM/YY).');
                expiryDate.focus();
                isValid = false;
            } else if (!cvv.val() || cvv.val().length < 3) {
                alert('Please enter a valid CVV.');
                cvv.focus();
                isValid = false;
            } else if (!billingEmail.val() || !/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(billingEmail.val())) {
                alert('Please enter a valid billing email.');
                billingEmail.focus();
                isValid = false;
            }
            
            if (!isValid) {
                return false;
            }
            
            // If form element exists, use its validation
            if (form.length > 0 && form[0].checkValidity) {
                if (!form[0].checkValidity()) {
                    console.log('Form validation failed on button click');
                    form[0].reportValidity();
                    return false;
                }
            }
            
            // Call processPayment directly
            console.log('Calling processPayment from button click');
            processPayment(form, btn, processingDiv);
            
            return false;
        });
        
        // Waiver Modal Handler - Use event delegation for dynamically loaded content
        $(document).on('click', '#waiverModalLink', function(e) {
            e.preventDefault();
            e.stopPropagation();
            console.log('Waiver modal link clicked');
            const modal = $('#waiverModal');
            if (modal.length) {
                console.log('Modal found, opening...');
                
                // Initialize age checkboxes when modal opens
                setTimeout(function() {
                    const waiverUnder12 = $('#waiver_under_12');
                    const waiver12OrOlder = $('#waiver_12_or_older');
                    
                    // If neither is checked, check the appropriate one based on participants
                    if (!waiverUnder12.is(':checked') && !waiver12OrOlder.is(':checked')) {
                        // Default to "12 or older" if no student under 12
                        waiver12OrOlder.prop('checked', true);
                        console.log('Age checkbox initialized to "12 or older"');
                    }
                }, 100);
                
                // Try Bootstrap 5 first, then fallback to jQuery (Bootstrap 4)
                if (typeof bootstrap !== 'undefined' && bootstrap.Modal) {
                    try {
                        const bsModal = new bootstrap.Modal(modal[0]);
                        bsModal.show();
                    } catch (bsError) {
                        console.log('Bootstrap 5 failed, trying jQuery:', bsError);
                        modal.modal('show');
                    }
                } else {
                    modal.modal('show');
                }
            } else {
                console.error('Waiver modal not found in DOM');
                alert('Waiver form is loading. Please try again in a moment.');
            }
        });
        
        // Prevent unchecking required checkboxes
        $(document).on('change', '#waiver_under_12', function() {
            const checkbox = $(this);
            if (checkbox.attr('data-required') === 'true' && !checkbox.is(':checked')) {
                alert('You have participants under 12 years of age. This checkbox must remain checked.');
                checkbox.prop('checked', true);
                return false;
            }
        });
        
        $(document).on('change', '#waiver_12_or_older', function() {
            const checkbox = $(this);
            if (checkbox.attr('data-required') === 'true' && !checkbox.is(':checked')) {
                alert('This checkbox must remain checked based on your participants.');
                checkbox.prop('checked', true);
                return false;
            }
        });
        
        // Also initialize checkboxes when modal is shown
        $(document).on('shown.bs.modal', '#waiverModal', function() {
            const waiverUnder12 = $('#waiver_under_12');
            const waiver12OrOlder = $('#waiver_12_or_older');
            
            console.log('Modal shown - initializing checkboxes:', {
                waiverUnder12Checked: waiverUnder12.is(':checked'),
                waiverUnder12Required: waiverUnder12.attr('data-required'),
                waiver12OrOlderChecked: waiver12OrOlder.is(':checked'),
                waiver12OrOlderRequired: waiver12OrOlder.attr('data-required')
            });
            
            // Ensure the required checkbox is checked based on data-required attribute
            if (waiverUnder12.attr('data-required') === 'true' && !waiverUnder12.is(':checked')) {
                waiverUnder12.prop('checked', true);
                console.log('Auto-checked UNDER 12 checkbox (required)');
            }
            
            if (waiver12OrOlder.attr('data-required') === 'true' && !waiver12OrOlder.is(':checked')) {
                waiver12OrOlder.prop('checked', true);
                console.log('Auto-checked 12 or older checkbox (required)');
            }
        });
        
        $(document).on('click', '#waiver-modal-close', function(e) {
            e.preventDefault();
            $('#waiverModal').modal('hide');
        });
        
        // Also handle close button in modal footer
        $(document).on('click', '#waiverModal .btn-secondary[data-dismiss="modal"]', function(e) {
            e.preventDefault();
            $('#waiverModal').modal('hide');
        });
        
        // Initialize modal after content loads (for AJAX-loaded content)
        setTimeout(function() {
            if ($('#waiverModal').length) {
                console.log('Waiver modal initialized');
            }
        }, 100);
        
        // Submit Waiver Button Handler
        $(document).on('click', '#waiver-submit-btn', function(e) {
            e.preventDefault();
            e.stopPropagation();
            e.stopImmediatePropagation();
            console.log('=== Submit waiver button clicked ===');
            
            const waiverAcknowledgment = $('#waiver_acknowledgment');
            const waiverSignature = $('#waiver_signature');
            const waiverDate = $('#waiver_date');
            const waiverUnder12 = $('#waiver_under_12');
            const waiver12OrOlder = $('#waiver_12_or_older');
            
            // Check if elements exist
            if (waiverUnder12.length === 0 || waiver12OrOlder.length === 0) {
                console.error('Waiver checkboxes not found!');
                alert('Error: Waiver form elements not found. Please refresh the page.');
                return false;
            }
            
            console.log('Waiver fields:', {
                acknowledgment: waiverAcknowledgment.is(':checked'),
                signature: waiverSignature.val(),
                date: waiverDate.val(),
                under12: waiverUnder12.is(':checked'),
                twelveOrOlder: waiver12OrOlder.is(':checked')
            });
            
            // Validate age checkbox - MUST check the one with data-required="true"
            const waiverUnder12RequiredAttr = waiverUnder12.attr('data-required');
            const waiver12OrOlderRequiredAttr = waiver12OrOlder.attr('data-required');
            const waiverUnder12Required = waiverUnder12RequiredAttr === 'true';
            const waiver12OrOlderRequired = waiver12OrOlderRequiredAttr === 'true';
            
            console.log('Age validation:', {
                waiverUnder12RequiredAttr: waiverUnder12RequiredAttr,
                waiverUnder12Required: waiverUnder12Required,
                waiverUnder12Checked: waiverUnder12.is(':checked'),
                waiver12OrOlderRequiredAttr: waiver12OrOlderRequiredAttr,
                waiver12OrOlderRequired: waiver12OrOlderRequired,
                waiver12OrOlderChecked: waiver12OrOlder.is(':checked')
            });
            
            // VALIDATE AGE CHECKBOX FIRST - This is critical
            // If "UNDER 12" checkbox has data-required="true", it MUST be checked
            if (waiverUnder12Required && !waiverUnder12.is(':checked')) {
                console.error('VALIDATION FAILED: UNDER 12 checkbox required but not checked');
                alert('You have participants under 12 years of age. Please check "At least one participant is UNDER 12 years of age – Adult registration is MANDATORY."');
                waiverUnder12.focus();
                return false;
            }
            
            // If "12 or older" checkbox has data-required="true", it MUST be checked
            if (waiver12OrOlderRequired && !waiver12OrOlder.is(':checked')) {
                console.error('VALIDATION FAILED: 12 or older checkbox required but not checked');
                alert('Please check "All participants are 12 YEARS OR OLDER – Adult registration is optional."');
                waiver12OrOlder.focus();
                return false;
            }
            
            // Fallback: at least one must be checked
            if (!waiverUnder12.is(':checked') && !waiver12OrOlder.is(':checked')) {
                console.error('VALIDATION FAILED: No age checkbox checked');
                alert('Please check at least one age requirement box (UNDER 12 or 12 YEARS OR OLDER).');
                waiverUnder12.focus();
                return false;
            }
            
            console.log('Age validation passed');
            
            // Validate ALL required checkboxes in the waiver form
            if (!waiverAcknowledgment.is(':checked')) {
                alert('Please check "I have read, understood, and agree to all terms and conditions" in the waiver form.');
                waiverAcknowledgment.focus();
                return false;
            }
            
            if (!waiverSignature.val() || waiverSignature.val().trim() === '') {
                alert('Please enter your signature (full name) in the waiver form.');
                waiverSignature.focus();
                return false;
            }
            
            if (!waiverDate.val()) {
                alert('Please enter the date in the waiver form.');
                waiverDate.focus();
                return false;
            }
            
            console.log('Waiver validation passed, submitting...');
            
            // Mark waiver as completed
            $('#waiver-status').show();
            $('#waiverModalLink').hide();
            
            // Sync waiver data to hidden fields
            syncWaiverDataToForm();
            
            console.log('Waiver data synced to hidden fields');
            
            // Close modal
            if (typeof bootstrap !== 'undefined' && bootstrap.Modal) {
                const modalElement = document.getElementById('waiverModal');
                if (modalElement) {
                    const bsModal = bootstrap.Modal.getInstance(modalElement);
                    if (bsModal) {
                        bsModal.hide();
                    } else {
                        $('#waiverModal').modal('hide');
                    }
                }
            } else {
                $('#waiverModal').modal('hide');
            }
            
            alert('Waiver form submitted successfully. You can now proceed with payment.');
            console.log('Waiver submitted successfully');
        });
        
        // Coupon code handling - make variables global for access from other scripts
        window.appliedCoupon = null;
        window.originalAmount = (parseFloat(window.IRCC_STUDENT_AMOUNT_PAYABLE) || 0) + (parseFloat(window.IRCC_ADULT_AMOUNT_PAYABLE) || 0);
        window.discountAmount = 0;
        
        // Local references for easier access
        let appliedCoupon = window.appliedCoupon;
        let originalAmount = window.originalAmount;
        let discountAmount = window.discountAmount;
        
        // Debug: Log initial values
        console.log('Coupon code system initialized:', {
            originalAmount: originalAmount,
            appliedCoupon: appliedCoupon,
            discountAmount: discountAmount
        });
        
        // Keep pre-coupon total aligned with billing engine (student + adult payable)
        $(document).ready(function() {
            setTimeout(function() {
                const full = (parseFloat(window.IRCC_STUDENT_AMOUNT_PAYABLE) || 0) + (parseFloat(window.IRCC_ADULT_AMOUNT_PAYABLE) || 0);
                if (full > 0) {
                    window.originalAmount = full;
                    originalAmount = full;
                    if ($('#modal-subtotal').length) {
                        $('#modal-subtotal').text('$' + full.toFixed(2));
                    }
                    console.log('Updated originalAmount (student+adult payable):', full);
                }
            }, 500);
        });
        
        // Coupon codes mapping - make it global
        window.couponCodes = {
            'Save10': { discount: 10, type: 'percent' },
            'Save5': { discount: 5, type: 'percent' },
            'Save25': { discount: 25, type: 'percent' },
            'RISE150': { type: 'rise_student' },
            'LIFO10': { discount: 10, type: 'percent' },
            'IRCC1': { type: 'fixed_total', finalAmount: 1 }
        };
        const couponCodes = window.couponCodes; // Local reference
        
        // Note: handleSummaryCouponApply is already defined at the top of the script (outside document.ready)
        // so it's available immediately when the page loads
        
        // Apply coupon code on button click or Enter key - make it globally accessible
        window.applyCoupon = function() {
            const couponCodeInput = $('#coupon_code').val().trim();
            const couponMessage = $('#coupon-message');
            const couponDiscount = $('#coupon-discount');
            const discountAmountSpan = $('#discount-amount');
            
            if (!couponCodeInput) {
                couponMessage.html('<span style="color: #dc3545;">Please enter a coupon code.</span>');
                return;
            }
            
            // Make coupon code case-insensitive by converting to proper case
            let couponCode = couponCodeInput;
            // Normalize to match our coupon codes (Save10, Save5, Save25, RISE150, LIFO10, IRCC1)
            if (couponCodeInput.toLowerCase() === 'save10') {
                couponCode = 'Save10';
            } else if (couponCodeInput.toLowerCase() === 'save5') {
                couponCode = 'Save5';
            } else if (couponCodeInput.toLowerCase() === 'save25') {
                couponCode = 'Save25';
            } else if (couponCodeInput.toLowerCase() === 'rise150') {
                couponCode = 'RISE150';
            } else if (couponCodeInput.toLowerCase() === 'lifo10') {
                couponCode = 'LIFO10';
            } else if (couponCodeInput.toUpperCase() === 'IRCC1') {
                couponCode = 'IRCC1';
            }
            
            const coupon = couponCodes[couponCode];
            if (!coupon) {
                couponMessage.html('<span style="color: #dc3545;">Invalid coupon code. Please check your code and try again.</span>');
                return;
            }
            
            const prModal = window.irccApplyCouponPricing(coupon);
            originalAmount = prModal.originalTotal;
            window.originalAmount = originalAmount;
            const finalAmount = prModal.finalAmount;
            const discountLabel = prModal.discountLabel;
            discountAmount = prModal.discountAmount;
            
            // Update UI
            window.appliedCoupon = couponCode;
            appliedCoupon = couponCode;
            window.discountAmount = discountAmount;
            couponMessage.html('<span style="color: #198754; font-weight: 600;"><i class="fas fa-check-circle"></i> Coupon "' + couponCode + '" applied! ' + discountLabel + '</span>');
            couponDiscount.show();
            discountAmountSpan.text('-$' + discountAmount.toFixed(2) + ' (' + discountLabel + ')');
            
            // Update payment amounts
            updatePaymentAmounts(finalAmount);
            
            console.log('Coupon applied:', {
                code: couponCode,
                originalAmount: originalAmount,
                discount: discountAmount,
                finalAmount: finalAmount
            });
        };
        
        // Also create a local reference for jQuery handlers
        const applyCoupon = window.applyCoupon;
        
        $(document).on('click', '#apply-coupon-btn', function() {
            applyCoupon();
        });
        
        // Apply coupon on Enter key press
        $(document).on('keypress', '#coupon_code', function(e) {
            if (e.which === 13) { // Enter key
                e.preventDefault();
                applyCoupon();
            }
        });
        
        // Remove coupon
        $(document).on('click', '#remove-coupon-btn', function() {
            window.appliedCoupon = null;
            window.discountAmount = 0;
            appliedCoupon = null;
            discountAmount = 0;
            $('#coupon_code').val('');
            $('#coupon-message').html('');
            $('#coupon-discount').hide();
            const fullReset = (parseFloat(window.IRCC_STUDENT_AMOUNT_PAYABLE) || 0) + (parseFloat(window.IRCC_ADULT_AMOUNT_PAYABLE) || 0);
            window.originalAmount = fullReset;
            originalAmount = fullReset;
            updatePaymentAmounts(fullReset);
        });
        
        // Update payment amounts display
        function updatePaymentAmounts(finalAmount) {
            const sub = (typeof window.originalAmount === 'number' && window.originalAmount > 0)
                ? window.originalAmount
                : ((parseFloat(window.IRCC_STUDENT_AMOUNT_PAYABLE) || 0) + (parseFloat(window.IRCC_ADULT_AMOUNT_PAYABLE) || 0));
            $('#modal-amount-payable').text('$' + finalAmount.toFixed(2));
            $('#modal-subtotal').text('$' + sub.toFixed(2));
            
            // Update summary amount payable
            $('#summary-amount-payable').text('$' + finalAmount.toFixed(2));
            $('#summary-amount-payable-2').text('$' + finalAmount.toFixed(2));
            
            // Update Givebutter widget amount if it exists
            const widget = document.querySelector('#payment-confirmation-modal givebutter-widget');
            if (widget) {
                widget.setAttribute('amount', finalAmount.toFixed(2));
            }
        }
        
        // Summary coupon code handlers - make it globally accessible
        window.applySummaryCoupon = function(inputId, messageId, discountId, amountId, removeBtnId, amountPayableId) {
            console.log('applySummaryCoupon called with:', { inputId, messageId, discountId, amountId, amountPayableId });
            
            // Check if window.couponCodes is defined
            if (typeof window.couponCodes === 'undefined') {
                console.error('window.couponCodes is not defined!');
                $('#' + messageId).html('<span style="color: #dc3545;">Coupon system not initialized. Please refresh the page.</span>');
                return false;
            }
            
            // Verify all required elements exist
            const couponCodeInputEl = $('#' + inputId);
            const couponMessage = $('#' + messageId);
            const couponDiscount = $('#' + discountId);
            const discountAmountSpan = $('#' + amountId);
            const amountPayableEl = $('#' + amountPayableId);
            
            if (couponCodeInputEl.length === 0) {
                console.error('Coupon code input element not found:', inputId);
                return false;
            }
            if (couponMessage.length === 0) {
                console.error('Coupon message element not found:', messageId);
                return false;
            }
            if (amountPayableEl.length === 0) {
                console.error('Amount payable element not found:', amountPayableId);
                return false;
            }
            
            const couponCodeInput = couponCodeInputEl.val().trim();
            
            console.log('Coupon code input:', couponCodeInput);
            console.log('Elements found:', {
                input: couponCodeInputEl.length,
                message: couponMessage.length,
                discount: couponDiscount.length,
                amountSpan: discountAmountSpan.length,
                amountPayable: amountPayableEl.length
            });
            
            if (!couponCodeInput) {
                couponMessage.html('<span style="color: #dc3545;">Please enter a coupon code.</span>');
                return false;
            }
            
            // Make coupon code case-insensitive
            let couponCode = couponCodeInput;
            if (couponCodeInput.toLowerCase() === 'save10') {
                couponCode = 'Save10';
            } else if (couponCodeInput.toLowerCase() === 'save5') {
                couponCode = 'Save5';
            } else if (couponCodeInput.toLowerCase() === 'save25') {
                couponCode = 'Save25';
            } else if (couponCodeInput.toLowerCase() === 'rise150') {
                couponCode = 'RISE150';
            } else if (couponCodeInput.toLowerCase() === 'lifo10') {
                couponCode = 'LIFO10';
            } else if (couponCodeInput.toUpperCase() === 'IRCC1') {
                couponCode = 'IRCC1';
            }
            
            console.log('Normalized coupon code:', couponCode);
            console.log('Available coupon codes:', Object.keys(window.couponCodes));
            
            const coupon = window.couponCodes[couponCode];
            if (!coupon) {
                couponMessage.html('<span style="color: #dc3545;">Invalid coupon code. Please check your code and try again.</span>');
                console.log('Coupon not found:', couponCode);
                return false;
            }
            
            console.log('Coupon found:', coupon);
            
            const prSummary = window.irccApplyCouponPricing(coupon);
            const originalAmount = prSummary.originalTotal;
            window.originalAmount = originalAmount;
            const summaryDiscountAmount = prSummary.discountAmount;
            const finalAmount = prSummary.finalAmount;
            const summaryDiscountLabel = prSummary.discountLabel;
            
            // Update global coupon state
            window.appliedCoupon = couponCode;
            appliedCoupon = couponCode;
            window.discountAmount = summaryDiscountAmount;
            discountAmount = summaryDiscountAmount;
            
            // Update UI
            couponMessage.html('<span style="color: #198754; font-weight: 600;"><i class="fas fa-check-circle"></i> Coupon applied! ' + summaryDiscountLabel + '</span>');
            couponDiscount.show();
            discountAmountSpan.text('-$' + summaryDiscountAmount.toFixed(2) + ' (' + summaryDiscountLabel + ')');
            
            // Update summary amount payable
            $('#' + amountPayableId).text('$' + finalAmount.toFixed(2));
            
            // Also sync to modal if it exists
            $('#modal-amount-payable').text('$' + finalAmount.toFixed(2));
            $('#modal-subtotal').text('$' + originalAmount.toFixed(2));
            
            // Sync coupon to modal coupon input if modal is open
            if ($('#coupon_code').length) {
                $('#coupon_code').val(couponCode);
                $('#coupon-message').html('<span style="color: #198754; font-weight: 600;"><i class="fas fa-check-circle"></i> Coupon applied! ' + summaryDiscountLabel + '</span>');
                $('#coupon-discount').show();
                $('#discount-amount').text('-$' + summaryDiscountAmount.toFixed(2) + ' (' + summaryDiscountLabel + ')');
            }
            
            console.log('Summary coupon applied:', {
                code: couponCode,
                originalAmount: originalAmount,
                discount: summaryDiscountAmount,
                finalAmount: finalAmount
            });
            
            return true;
        };
        
        // Also create a local reference for jQuery handlers
        const applySummaryCoupon = window.applySummaryCoupon;
        
        function removeSummaryCoupon(inputId, messageId, discountId, amountPayableId) {
            const originalAmountValue = (parseFloat(window.IRCC_STUDENT_AMOUNT_PAYABLE) || 0) + (parseFloat(window.IRCC_ADULT_AMOUNT_PAYABLE) || 0);
            
            // Reset coupon state
            window.appliedCoupon = null;
            window.discountAmount = 0;
            appliedCoupon = null;
            discountAmount = 0;
            window.originalAmount = originalAmountValue;
            originalAmount = originalAmountValue;
            
            // Clear inputs
            $('#' + inputId).val('');
            $('#' + messageId).html('');
            $('#' + discountId).hide();
            
            // Also clear modal coupon if exists
            if ($('#coupon_code').length) {
                $('#coupon_code').val('');
                $('#coupon-message').html('');
                $('#coupon-discount').hide();
            }
            
            // Reset amounts
            $('#' + amountPayableId).text('$' + originalAmountValue.toFixed(2));
            $('#modal-amount-payable').text('$' + originalAmountValue.toFixed(2));
            $('#modal-subtotal').text('$' + originalAmountValue.toFixed(2));
        }
        
        // Note: handleSummaryCouponApply is now defined above (right after couponCodes) so it's available immediately
        // Create local reference for easier access
        const handleSummaryCouponApply = window.handleSummaryCouponApply;

        // Event handler for coupon apply buttons (using class selector for reliability)
        // Use capture phase and highest priority to ensure it fires
        $(document).off('click', '.apply-coupon-btn').on('click', '.apply-coupon-btn', function(e) {
            e.preventDefault();
            e.stopPropagation();
            e.stopImmediatePropagation();
            
            console.log('=== Apply coupon button clicked ===');
            console.log('Button ID:', $(this).attr('id'));
            console.log('Button class:', $(this).attr('class'));
            
            const button = $(this);
            const inputId = button.data('input-id');
            const messageId = button.data('message-id');
            const discountId = button.data('discount-id');
            const amountId = button.data('amount-id');
            const payableId = button.data('payable-id');
            
            console.log('Button data:', { inputId, messageId, discountId, amountId, payableId });
            
            // Ensure coupon codes are defined
            if (typeof window.couponCodes === 'undefined') {
                window.couponCodes = {
                    'Save10': { discount: 10, type: 'percent' },
                    'Save5': { discount: 5, type: 'percent' },
                    'Save25': { discount: 25, type: 'percent' },
                    'RISE150': { type: 'rise_student' },
                    'LIFO10': { discount: 10, type: 'percent' },
                    'IRCC1': { type: 'fixed_total', finalAmount: 1 }
                };
                console.log('Coupon codes defined:', window.couponCodes);
            }
            
            // Ensure function is defined
            if (typeof window.handleSummaryCouponApply !== 'function') {
                console.log('Defining handleSummaryCouponApply function...');
                window.handleSummaryCouponApply = function(b, i, m, d, a, ap) {
                    console.log('handleSummaryCouponApply called with:', { b, i, m, d, a, ap });
                    
                    if (typeof jQuery === 'undefined' || typeof $ === 'undefined') {
                        console.log('jQuery not ready, retrying...');
                        setTimeout(function() { window.handleSummaryCouponApply(b, i, m, d, a, ap); }, 100);
                        return false;
                    }
                    
                    const input = $('#' + i);
                    console.log('Input element found:', input.length);
                    if (!input.length) {
                        console.error('Input element not found:', i);
                        return false;
                    }
                    
                    const code = input.val().trim().toUpperCase();
                    console.log('Coupon code entered:', code);
                    
                    if (!code) {
                        $('#' + m).html('<span style="color:#dc3545;">Please enter a coupon code.</span>');
                        return false;
                    }
                    
                    const c = code === 'SAVE10' ? 'Save10' : code === 'SAVE5' ? 'Save5' : code === 'SAVE25' ? 'Save25' : code === 'RISE150' ? 'RISE150' : code === 'LIFO10' ? 'LIFO10' : code === 'IRCC1' ? 'IRCC1' : code;
                    console.log('Normalized coupon code:', c);
                    
                    const coupon = window.couponCodes[c];
                    console.log('Coupon found:', coupon);
                    
                    if (!coupon) {
                        $('#' + m).html('<span style="color:#dc3545;">Invalid coupon code.</span>');
                        return false;
                    }
                    
                    const el = $('#' + ap);
                    console.log('Amount payable element found:', el.length);
                    if (!el.length) {
                        console.error('Amount payable element not found:', ap);
                        return false;
                    }
                    
                    const prBk = window.irccApplyCouponPricing(coupon);
                    window.originalAmount = prBk.originalTotal;
                    const disc = prBk.discountAmount;
                    const fin = prBk.finalAmount;
                    const lbl2 = prBk.discountLabel;
                    console.log('Discount calculation:', { orig: prBk.originalTotal, disc, fin, couponType: coupon.type });
                    
                    window.appliedCoupon = c;
                    window.discountAmount = disc;
                    
                    $('#' + m).html('<span style="color:#198754;font-weight:600;"><i class="fas fa-check-circle"></i> Coupon applied! ' + lbl2 + '</span>');
                    $('#' + d).show();
                    $('#' + a).text('-$' + disc.toFixed(2));
                    $('#' + ap).text('$' + fin.toFixed(2));
                    if ($('#modal-amount-payable').length) $('#modal-amount-payable').text('$' + fin.toFixed(2));
                    if ($('#summary-amount-payable-2').length) $('#summary-amount-payable-2').text('$' + fin.toFixed(2));
                    
                    console.log('Coupon applied successfully!');
                    return true;
                };
                console.log('handleSummaryCouponApply function defined');
            }
            
            // Call the function
            if (typeof window.handleSummaryCouponApply === 'function') {
                console.log('Calling handleSummaryCouponApply...');
                window.handleSummaryCouponApply(
                    button.attr('id'),
                    inputId,
                    messageId,
                    discountId,
                    amountId,
                    payableId
                );
            } else {
                console.error('handleSummaryCouponApply is not a function!');
                alert('Coupon system not ready. Please refresh the page.');
            }
            
            return false;
        });
        
        console.log('✓ Coupon apply button event handler attached');

        // Direct handler for button 2 as backup (in case class selector doesn't work)
        $(document).on('click', '#apply-summary-coupon-btn-2', function(e) {
            e.preventDefault();
            e.stopPropagation();
            e.stopImmediatePropagation();
            
            console.log('=== Button 2 clicked (direct ID handler) ===');
            
            const button = $(this);
            const inputId = button.data('input-id') || 'summary-coupon-code-2';
            const messageId = button.data('message-id') || 'summary-coupon-message-2';
            const discountId = button.data('discount-id') || 'summary-coupon-discount-2';
            const amountId = button.data('amount-id') || 'summary-discount-amount-2';
            const payableId = button.data('payable-id') || 'summary-amount-payable-2';
            
            // Ensure coupon codes are defined
            if (typeof window.couponCodes === 'undefined') {
                window.couponCodes = {
                    'Save10': { discount: 10, type: 'percent' },
                    'Save5': { discount: 5, type: 'percent' },
                    'Save25': { discount: 25, type: 'percent' },
                    'RISE150': { type: 'rise_student' },
                    'LIFO10': { discount: 10, type: 'percent' },
                    'IRCC1': { type: 'fixed_total', finalAmount: 1 }
                };
            }
            
            // Ensure function is defined
            if (typeof window.handleSummaryCouponApply !== 'function') {
                window.handleSummaryCouponApply = function(b, i, m, d, a, ap) {
                    if (typeof jQuery === 'undefined' || typeof $ === 'undefined') {
                        setTimeout(function() { window.handleSummaryCouponApply(b, i, m, d, a, ap); }, 100);
                        return false;
                    }
                    const input = $('#' + i);
                    if (!input.length) return false;
                    const code = input.val().trim().toUpperCase();
                    if (!code) {
                        $('#' + m).html('<span style="color:#dc3545;">Please enter a coupon code.</span>');
                        return false;
                    }
                    const c = code === 'SAVE10' ? 'Save10' : code === 'SAVE5' ? 'Save5' : code === 'SAVE25' ? 'Save25' : code === 'RISE150' ? 'RISE150' : code === 'LIFO10' ? 'LIFO10' : code === 'IRCC1' ? 'IRCC1' : code;
                    const coupon = window.couponCodes[c];
                    if (!coupon) {
                        $('#' + m).html('<span style="color:#dc3545;">Invalid coupon code.</span>');
                        return false;
                    }
                    const el = $('#' + ap);
                    if (!el.length) return false;
                    const prB2 = window.irccApplyCouponPricing(coupon);
                    window.originalAmount = prB2.originalTotal;
                    const disc = prB2.discountAmount;
                    const fin = prB2.finalAmount;
                    const lbl = prB2.discountLabel;
                    window.appliedCoupon = c;
                    window.discountAmount = disc;
                    $('#' + m).html('<span style="color:#198754;font-weight:600;"><i class="fas fa-check-circle"></i> Coupon applied! ' + lbl + '</span>');
                    $('#' + d).show();
                    $('#' + a).text('-$' + disc.toFixed(2) + ' (' + lbl + ')');
                    $('#' + ap).text('$' + fin.toFixed(2));
                    if ($('#modal-amount-payable').length) $('#modal-amount-payable').text('$' + fin.toFixed(2));
                    if ($('#summary-amount-payable-2').length) $('#summary-amount-payable-2').text('$' + fin.toFixed(2));
                    return true;
                };
            }
            
            // Call the function
            if (typeof window.handleSummaryCouponApply === 'function') {
                window.handleSummaryCouponApply(
                    'apply-summary-coupon-btn-2',
                    inputId,
                    messageId,
                    discountId,
                    amountId,
                    payableId
                );
            } else {
                alert('Coupon system not ready. Please refresh the page.');
            }
            
            return false;
        });
        
        console.log('✓ Direct handler for button 2 attached');
        
        $(document).on('keypress', '#summary-coupon-code, #summary-coupon-code-2', function(e) {
            if (e.which === 13) { // Enter key
                e.preventDefault();
                const inputId = $(this).attr('id');
                if (inputId === 'summary-coupon-code') {
                    handleSummaryCouponApply('apply-summary-coupon-btn', 'summary-coupon-code', 'summary-coupon-message', 'summary-coupon-discount', 'summary-discount-amount', 'summary-amount-payable');
                } else {
                    handleSummaryCouponApply('apply-summary-coupon-btn-2', 'summary-coupon-code-2', 'summary-coupon-message-2', 'summary-coupon-discount-2', 'summary-discount-amount-2', 'summary-amount-payable-2');
                }
            }
        });
        
        $(document).on('click', '#remove-summary-coupon-btn', function() {
            removeSummaryCoupon('summary-coupon-code', 'summary-coupon-message', 'summary-coupon-discount', 'summary-amount-payable');
        });
        
        $(document).on('click', '#remove-summary-coupon-btn-2', function() {
            removeSummaryCoupon('summary-coupon-code-2', 'summary-coupon-message-2', 'summary-coupon-discount-2', 'summary-amount-payable-2');
        });
        
        // Comprehensive verification function
        function verifyCouponSystem() {
            console.log('=== COUPON SYSTEM VERIFICATION ===');
            const btn1 = $('#apply-summary-coupon-btn');
            const btn2 = $('#apply-summary-coupon-btn-2');
            const input1 = $('#summary-coupon-code');
            const input2 = $('#summary-coupon-code-2');
            
            console.log('Button 1 exists:', btn1.length > 0);
            console.log('Button 2 exists:', btn2.length > 0);
            console.log('Input 1 exists:', input1.length > 0);
            console.log('Input 2 exists:', input2.length > 0);
            console.log('window.couponCodes defined:', typeof window.couponCodes !== 'undefined');
            console.log('window.handleSummaryCouponApply defined:', typeof window.handleSummaryCouponApply !== 'undefined');
            console.log('window.applyCoupon defined:', typeof window.applyCoupon !== 'undefined');
            
            if (typeof window.couponCodes !== 'undefined') {
                console.log('Available coupon codes:', Object.keys(window.couponCodes));
            }
            
            // Test if handlers are attached
            if (btn1.length > 0) {
                try {
                    const events = $._data(btn1[0], 'events');
                    console.log('Button 1 click events:', events && events.click ? events.click.length : 0);
                } catch(e) {
                    console.log('Button 1 events check failed (may use delegated handlers)');
                }
            }
            
            if (btn2.length > 0) {
                try {
                    const events = $._data(btn2[0], 'events');
                    console.log('Button 2 click events:', events && events.click ? events.click.length : 0);
                } catch(e) {
                    console.log('Button 2 events check failed (may use delegated handlers)');
                }
            }
            
            // Test click simulation
            if (btn1.length > 0 && typeof window.handleSummaryCouponApply === 'function') {
                console.log('✓ Coupon system appears to be ready!');
                console.log('To test: Enter "LIFO10" and click Apply button');
            } else {
                console.warn('⚠ Coupon system may not be fully initialized');
            }
            console.log('=== END VERIFICATION ===');
        }
        
        // Run verification after page loads
        $(document).ready(function() {
            setTimeout(verifyCouponSystem, 2000);
            
            // Test if button click handler is working
            setTimeout(function() {
                const testBtn = $('.apply-coupon-btn').first();
                if (testBtn.length > 0) {
                    console.log('✓ Apply coupon button found:', testBtn.attr('id'));
                    console.log('✓ Button has class apply-coupon-btn:', testBtn.hasClass('apply-coupon-btn'));
                    const events = $._data(testBtn[0], 'events');
                    console.log('✓ Event handlers:', events ? Object.keys(events) : 'none');
                } else {
                    console.warn('⚠ Apply coupon button not found');
                }
            }, 3000);
        });
        
        // Sync waiver data to hidden fields before form submission
        function syncWaiverDataToForm() {
            const acknowledgment = $('#waiver_acknowledgment').is(':checked') ? '1' : '0';
            const signature = $('#waiver_signature').val() || '';
            const date = $('#waiver_date').val() || '';
            const under12 = $('#waiver_under_12').is(':checked') ? '1' : '0';
            const older12 = $('#waiver_12_or_older').is(':checked') ? '1' : '0';
            
            $('#waiver_acknowledgment_hidden').val(acknowledgment);
            $('#waiver_signature_hidden').val(signature);
            $('#waiver_date_hidden').val(date);
            $('#waiver_under_12_hidden').val(under12);
            $('#waiver_12_or_older_hidden').val(older12);
            
            console.log('Waiver data synced to hidden fields:', {
                acknowledgment: acknowledgment,
                signature: signature ? 'present (' + signature.length + ' chars)' : 'empty',
                date: date,
                under12: under12,
                older12: older12
            });
            
            return {
                acknowledgment: acknowledgment,
                signature: signature,
                date: date,
                under12: under12,
                older12: older12
            };
        }
        
        // Update hidden fields when waiver fields change
        $(document).on('change', '#waiver_acknowledgment, #waiver_signature, #waiver_date, #waiver_under_12, #waiver_12_or_older', function() {
            syncWaiverDataToForm();
        });
        
        // Trigger change on page load to show widget if Online Transaction is selected
        // Use setTimeout to ensure DOM is ready after AJAX load
        setTimeout(function() {
        $('input[name="payment_mode"]:checked').trigger('change');
            // Sync waiver data on page load
            syncWaiverDataToForm();
            // Also attach payment form handler on page load
            if (typeof attachPaymentFormHandler === 'function') {
                attachPaymentFormHandler();
            }
        }, 100);

        // Flag to prevent multiple submissions
        let isSubmitting = false;
        
        // Also prevent form submission on button click for Online Transaction
        $(document).on('click', '#payment-form button[type="submit"], form#payment-form button[type="submit"]', function(e) {
            // Validate waiver form before proceeding - STRICT VALIDATION
            const waiverAcknowledgment = $('#waiver_acknowledgment');
            const waiverSignature = $('#waiver_signature');
            const waiverDate = $('#waiver_date');
            const waiverStatus = $('#waiver-status');
            const waiverAcknowledgmentHidden = $('#waiver_acknowledgment_hidden');
            const waiverSignatureHidden = $('#waiver_signature_hidden');
            const waiverDateHidden = $('#waiver_date_hidden');
            
            // Check if waiver is completed - check both visible and hidden fields
            const isAcknowledged = waiverAcknowledgment.is(':checked') || waiverAcknowledgmentHidden.val() === '1';
            const hasSignature = (waiverSignature.val() && waiverSignature.val().trim() !== '') || 
                                 (waiverSignatureHidden.val() && waiverSignatureHidden.val().trim() !== '');
            const hasDate = (waiverDate.val() && waiverDate.val().trim() !== '') || 
                           (waiverDateHidden.val() && waiverDateHidden.val().trim() !== '');
            const statusVisible = waiverStatus.is(':visible');
            
            if (!statusVisible || !isAcknowledged || !hasSignature || !hasDate) {
                e.preventDefault();
                e.stopPropagation();
                e.stopImmediatePropagation();
                alert('Please complete the Conference Waiver and Release Form before proceeding with payment.');
                $('#waiverModalLink').trigger('click');
                return false;
            }
            
            // Sync waiver data to hidden fields before submission
            syncWaiverDataToForm();
            
            // Double-check after syncing
            if ($('#waiver_acknowledgment_hidden').val() !== '1' || 
                !$('#waiver_signature_hidden').val() || 
                !$('#waiver_date_hidden').val()) {
                e.preventDefault();
                e.stopPropagation();
                e.stopImmediatePropagation();
                alert('Please complete the Conference Waiver and Release Form before proceeding with payment.');
                $('#waiverModalLink').trigger('click');
                return false;
            }
            
            const selectedMode = $('input[name="payment_mode"]:checked').val();
            console.log('=== Submit button clicked ===');
            console.log('Selected mode:', selectedMode);
            
            if (selectedMode === 'Online Transaction') {
                e.preventDefault();
                e.stopPropagation();
                e.stopImmediatePropagation();
                
                try {
                // Validate waiver form before opening payment modal
                const waiverAcknowledgment = $('#waiver_acknowledgment');
                const waiverSignature = $('#waiver_signature');
                const waiverDate = $('#waiver_date');
                const waiverStatus = $('#waiver-status');
                
                // Check if waiver is completed
                if (!waiverStatus.is(':visible') || !waiverAcknowledgment.is(':checked') || !waiverSignature.val() || !waiverDate.val()) {
                    alert('Please complete the Conference Waiver and Release Form before proceeding with payment.');
                    $('#waiverModalLink').trigger('click');
                    return false;
                }
                
                // Sync waiver data to hidden fields before proceeding
                    if (typeof syncWaiverDataToForm === 'function') {
                syncWaiverDataToForm();
                    } else {
                        console.error('syncWaiverDataToForm function not found');
                    }
                
                console.log('=== Online Transaction selected - opening payment iframe modal directly ===');
                
                // Open payment iframe modal directly
                if (typeof window.openPaymentPopupInWindow === 'function') {
                    window.openPaymentPopupInWindow();
                } else {
                    console.error('Payment function not available');
                    alert('Payment function not available. Please refresh the page.');
                    }
                } catch (error) {
                    console.error('Error in payment submission:', error);
                    alert('An error occurred: ' + error.message + '. Please check the console for details.');
                }
                
                return false;
            }
        });
        
        // Use event delegation for payment form since it's loaded dynamically via AJAX
        $(document).on('submit', '#payment-form', function (e) {
            // Always prevent default first
            e.preventDefault();
            e.stopPropagation();
            e.stopImmediatePropagation();
            
            // Prevent multiple submissions
            if (isSubmitting) {
                console.log('Form submission already in progress, ignoring');
                return false;
            }
            
            console.log('Payment form submitted');
            
            // Validate waiver form before proceeding - STRICT VALIDATION
            const waiverAcknowledgment = $('#waiver_acknowledgment');
            const waiverSignature = $('#waiver_signature');
            const waiverDate = $('#waiver_date');
            const waiverStatus = $('#waiver-status');
            const waiverAcknowledgmentHidden = $('#waiver_acknowledgment_hidden');
            const waiverSignatureHidden = $('#waiver_signature_hidden');
            const waiverDateHidden = $('#waiver_date_hidden');
            
            // Check if waiver is completed - check both visible and hidden fields
            const isAcknowledged = waiverAcknowledgment.is(':checked') || waiverAcknowledgmentHidden.val() === '1';
            const hasSignature = (waiverSignature.val() && waiverSignature.val().trim() !== '') || 
                                 (waiverSignatureHidden.val() && waiverSignatureHidden.val().trim() !== '');
            const hasDate = (waiverDate.val() && waiverDate.val().trim() !== '') || 
                           (waiverDateHidden.val() && waiverDateHidden.val().trim() !== '');
            const statusVisible = waiverStatus.is(':visible');
            
            if (!statusVisible || !isAcknowledged || !hasSignature || !hasDate) {
                e.preventDefault();
                e.stopPropagation();
                alert('Please complete the Conference Waiver and Release Form before proceeding with payment.');
                $('#waiverModalLink').trigger('click');
                return false;
            }
            
            // Sync waiver data to hidden fields before submission
            syncWaiverDataToForm();
            
            // Double-check after syncing
            if ($('#waiver_acknowledgment_hidden').val() !== '1' || 
                !$('#waiver_signature_hidden').val() || 
                !$('#waiver_date_hidden').val()) {
                e.preventDefault();
                e.stopPropagation();
                alert('Please complete the Conference Waiver and Release Form before proceeding with payment.');
                $('#waiverModalLink').trigger('click');
                return false;
            }
            
            const selectedMode = $('input[name="payment_mode"]:checked').val();
            console.log('Selected payment mode:', selectedMode);
            
            // Check if a payment mode is selected
            if (!selectedMode) {
                alert('Please select a payment mode.');
                return false;
            }
            
            // Prevent form submission for Online Transaction - open iframe modal directly
            if (selectedMode === 'Online Transaction') {
                console.log('Online Transaction selected, opening payment iframe modal directly');
                
                try {
                // Validate waiver form before opening payment modal
                const waiverAcknowledgment = $('#waiver_acknowledgment');
                const waiverSignature = $('#waiver_signature');
                const waiverDate = $('#waiver_date');
                const waiverStatus = $('#waiver-status');
                
                // Check if waiver is completed
                if (!waiverStatus.is(':visible') || !waiverAcknowledgment.is(':checked') || !waiverSignature.val() || !waiverDate.val()) {
                    alert('Please complete the Conference Waiver and Release Form before proceeding with payment.');
                    $('#waiverModalLink').trigger('click');
                    return false;
                }
                
                // Sync waiver data to hidden fields before proceeding
                syncWaiverDataToForm();
                
                // Open payment iframe modal directly
                if (typeof window.openPaymentPopupInWindow === 'function') {
                    window.openPaymentPopupInWindow();
                } else {
                    console.error('Payment function not available');
                    alert('Payment function not available. Please refresh the page.');
                }
                
                return false;
            }
            
            // For Direct Bank Transfer, allow form submission
            if (selectedMode === 'Direct Bank Transfer') {
                const reference = $('#payment_reference').val();
                const date = $('#payment_date').val();
                if (!reference || !date) {
                    alert('Please fill in both Payment Reference and Payment Date for Bank Transfer.');
                    return false;
                }
                // Allow form submission for bank transfer
                isSubmitting = true;
                this.submit();
                return true;
            }
            
            // Default: prevent submission (should not reach here)
            console.warn('Unexpected payment mode or form submission prevented');
            return false;
        });
        
        // Reset submission flag when modal is closed
        $('#payment-confirmation-modal').on('hidden.bs.modal', function() {
            isSubmitting = false;
    });

        // Widget is now in the modal, no need for separate confirmation button handler
    });
</script>
<!-- Givebutter Widget Script is loaded in layout head -->
<!-- Givebutter Widget Script is loaded in layout head -->