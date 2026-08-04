<link rel="stylesheet" href="{{ url('libs/dataTable/datatables.min.css') }}" type="text/css">

<div class="donate-section">
    <div class="container">
        <div class="donate-detais">
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
                    <h4>Registration Details</h4>
                    <div class="row">
                        <div class="col-sm-6">
                            <div>
                                <p><strong>Registration Number: </strong>&nbsp;&nbsp;{{ $user->registration_number }} <p>
                            </div>
                        </div>
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
                    @include('dashboard.partials.registration-abstracts', ['abstracts' => $abstracts ?? collect()])
                </div>
                <div class="form-inner">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <h4>Participant Details</h4>
                        </div>

                        @if(!$participants->where('type','<>',2)->isEmpty())
                            <div class="col-md-6 text-right" id="collapseDiv">
                                <button class="btn" type="button" id="collapseButton" style="color:#F15B43;" data-bs-toggle="collapse" data-bs-target="#collapseParticipant" aria-expanded="false" aria-controls="collapseExample">
                                    <i class="fas fa-chevron-down"></i>
                                </button>

                            </div>
                        @endif
                    </div>

                    @if($participants->where('type','<>',2)->isEmpty())
                        <div class="row g-3">
                            <div class="col-md-12 text-center">
                            <p>
                                Once you add all participants, proceed with payment.<br>
                                <strong>Pricing:</strong> Student Participants: ${{ $payment_summary['studentPrice'] ?? '150.00' }} | Adult Attendees: ${{ $payment_summary['adultPrice'] ?? '50.00' }}
                                <br>
                            </p>
                                <a href="#" class="dynamic-link btn outline-round" style="padding: 10px 18px;"
                                   data-url="{{route('participant.index')}}" data-class="link-participant">Add Participants </a>
                            </div>
                        </div>
                    @endif
                    <div class="row participant-row" id="collapseParticipant">
                            <div class="col-md-12">
                                @if(!$participants->where('type','<>',2)->isEmpty())
                                        <table class="mb-5" id="participants-table" style="width: 100%;">
                                        <thead>
                                        <tr>
                                            <th style="background-color: #f15b43; color: #FFFFFF;">#</th>
                                            <th style="background-color: #f15b43; color: #FFFFFF;">First Name</th>
                                            <th style="background-color: #f15b43; color: #FFFFFF;">Last Name</th>
                                            <th style="background-color: #f15b43; color: #FFFFFF;">Guest</th>
                                            <th style="background-color: #f15b43; color: #FFFFFF;">Class</th>
                                        </tr>
                                        </thead>
                                        <tbody>
                                        @foreach($participants as $key=>$participant)
                                            <tr>
                                                <td>{{$key+1}}</td>
                                                <td>{{$participant->first_name}}</th>
                                                <td>{{$participant->last_name}}</th>
                                                <td>{{$participant->type==0?'No':'Yes'}}</td>
                                                <td>{{$participant->class}}</th>

                                            </tr>
                                        @endforeach

                                        </tbody>
                                    </table>
                                @endif

                            </div>
                        </div>
                </div>
                <div class="form-inner">
                      
                        <div class="row g-3">
                            <div class="col-md-6">
                                <h4>Payment Details</h4>
                            </div>
                            <div class="col-md-6 text-right">
                                <button class="btn" type="button" id="collapseButton" style="color:#F15B43;" data-bs-toggle="collapse" data-bs-target="#collapsePayment" aria-expanded="false" aria-controls="collapseExample">
                                <i class="fas fa-chevron-down"></i>
                                </button>

                            </div>

                            <div class="row d-flex justify-content-center mt-3">
                            @if($payment_summary['amountPayable']== 0)
                            <div class="col-md-12 d-flex justify-content-center">
                            @if($participants->isEmpty())
                            <p style="font-size: 16px; color: #666; text-align: center; width: 100%;">
                                No participants added. Payment is not required at this time.
                            </p>
                             @else
                             <p style="font-size: 18px; color: green; font-weight: bold; text-align: center; width: 100%;">
                                No payment due. Thank you!
                            </p>
                             @endif
                            </div>
                            @endif
                                @if($payment_summary['amountPayable']>0)
                                <div class="col-md-6" style="display: flex; justify-content: center;">
                                    <p style="font-weight: bold;">Amount Payable: ${{ $payment_summary['amountPayable'] }}</p>
                                </div>
                                @endif
                                @if($payment_summary['paidAmount'])
                                <div class="col-md-6" style="display: flex; justify-content: center;">
                                    <p style="font-weight: bold;"> Total Amount Paid: ${{ $payment_summary['paidAmount'] }}</p>
                                </div>
                                @endif
                                {{-- @if($payment_summary['refund'])
                                <div class="col-md-4" style="display: flex; justify-content: center;">
                                    <p style="font-weight: bold;"> Refund Amount: ${{ $payment_summary['refund'] }}</p>
                                </div>
                                @endif --}}
                        </div>
                             @php
                                 $hasCompletedPayment = \App\Models\Payment::where('registration_id', $user->id)
                                     ->where('payment_status', 3)
                                     ->exists();
                                 // Abstract upload only active when fully paid (amountPayable == 0)
                                 $isFullyPaid = isset($payment_summary) && (float)str_replace(',', '', $payment_summary['amountPayable']) == 0;
                                 $abstractStatusRd = optional($settings->where('code','abstract_status')->first())->value;
                                 $abstractNavVisibleRd = \App\Models\AbstractSubmission::dashboardAbstractNavVisible(
                                     $user->id,
                                     (bool) $abstractStatusRd,
                                     $hasCompletedPayment && $isFullyPaid
                                 );
                                 $abstractNavLabelRd = \App\Models\AbstractSubmission::dashboardAbstractNavLabel($user->id);
                             @endphp
                             @if($hasCompletedPayment && $isFullyPaid && $abstractNavVisibleRd)
                             <div class="col-md-12 d-flex justify-content-center align-items-center mt-3 mb-3">
                                 <a href="javascript:void(0);" class="dynamic-link button-round reg_register"
                                     data-url="{{ route('abstract.create') }}"  data-class="link-abstract"
                                     >{{ $abstractNavLabelRd }}</a>
                             </div>
                             @endif

                        @if($payment_summary['amountPayable']>0)
                            <div class="col-md-12 d-flex justify-content-center">
                                <h5 style="font-weight: bold;">
                                    Payment Pending : ${{$payment_summary['amountPayable'] }}
                                </h5>
                            </div>
                            <div class="d-flex justify-content-center align-items-center mt-3">
                                <!-- <a href="javascript:void(0);" class="dynamic-link button-round reg_register"
                                   data-url="{{ route('payment.index') }}">Make Payment</a> -->
                                   <a href="javascript:void(0);" class="dynamic-link button-round reg_payment make_payment"
                                   data-url="{{ route('payment.index') }}" data-class="link-payment">Make Payment</a>
                            </div>
                        @endif
                        {{-- @if($payment_summary['refund'])
                            <div class="col-md-12 d-flex justify-content-center">
                                <h5 style="font-weight: bold;">
                                    Refund Amount : ${{$payment_summary['refund'] }}
                                </h5>
                            </div>
                        @endif
                        @if($refundHistory->count())
                        <div class="col-md-12">
                        <p style="    display: flex; font-size: 18px; color: green; font-weight: bold; justify-content: center;">
                            A Request has been send for refund.
                        </p>
                        </div>
                        @endif --}}
                    </div>

                        <div class="row participant-row collapse mt-3" id="collapsePayment">
                            @if(!$payments->count())
                                <div class="col-md-12">
                                    <p>Payment Not Done</p>
                                </div>
                            @else
                                <div class="col-md-12">
                                    <table class="table">
                                        <thead>
                                        <tr>
                                            <th style="background-color: #f15b43; color: #FFFFFF;">#</th>
                                            <th style="background-color: #f15b43; color: #FFFFFF;">Payment Mode</th>
                                            <th style="background-color: #f15b43; color: #FFFFFF;">Payment Reference</th>
                                            <th style="background-color: #f15b43; color: #FFFFFF;">Payment Date</th>
                                            <th style="background-color: #f15b43; color: #FFFFFF;">No. of Participants</th>
                                            <th style="background-color: #f15b43; color: #FFFFFF;">Amount Paid</th>
                                            <th style="background-color: #f15b43; color: #FFFFFF;">Payment Status</th>
                                            <th style="background-color: #f15b43; color: #FFFFFF;">Created On</th>
                                        </tr>
                                        </thead>
                                        <tbody>
                                        @foreach($payments as $index => $payment)
                                        @php
                                        $status=$payment->status?$payment->status->status:'Failed';
                                        @endphp
                                                <tr >
                                                <td>{{ $loop->iteration }}</td>
                                                <td>{{ $payment->payment_mode }}</td>
                                                <td>{{ $payment->payment_mode == 'Direct Bank Transfer' ? $payment->payment_reference : $payment->transaction_id }}</td>
                                                <td>{{ $payment->payment_mode == 'Direct Bank Transfer' ? $payment->payment_date :  Carbon\Carbon::parse($payment->transaction_date)->format('d-M-Y h:h A') }}</td>
                                                <td>{{ $payment->participants }}</td>
                                                <td>${{ $payment->payment_mode == 'Direct Bank Transfer' ?number_format($payment->amount,2):number_format($payment->paid_amount,2)}}</td>
                                                <td style="font-weight:500;{{$status=='Completed'?'color:green':'color:red'}}">{{$payment->status?$payment->status->status:'Pending' }}</td>
                                                <td>{{ $payment->created_at }}</td>
                                            </tr>
                                        @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            @endif
                        </div>
                   
                </div>
            </div>
        </div>
    </div>
</div>


<script>
$(document).ready(function () {
    $('.make_payment').on('click', function (e) {
        $("html, body").animate({ scrollTop: $(document).height()-1200 }, 1000);
    });
    // dynamic-link handler is managed by the parent page (index.blade.php) via event delegation
});  
</script>

