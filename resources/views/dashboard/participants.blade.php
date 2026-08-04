<link rel="stylesheet" href="{{ url('libs/dataTable/datatables.min.css') }}" type="text/css">
<style>
    /* Auto-added adult alert styling */
    .swal-auto-adult .swal-title { font-size: 18px; color: #F15B43; }
    .swal-auto-adult .swal-text { font-size: 14px; color: #555; }
    .swal-auto-adult .swal-icon--warning { border-color: #F15B43; }
    .swal-auto-adult .swal-icon--warning__body, 
    .swal-auto-adult .swal-icon--warning__dot { background-color: #F15B43; }
    .swal-auto-adult .swal-button--confirm { background-color: #F15B43; font-size: 14px; padding: 6px 24px; }
    .swal-auto-adult .swal-button--confirm:hover { background-color: #d94a35; }
    .swal-auto-adult .swal-modal { max-width: 380px; padding: 20px; }

    /* Global swal button styling to match theme */
    .swal-button { background-color: #F15B43 !important; }
    .swal-button:hover { background-color: #d94a35 !important; }

    .form-validation {
        color: red;
        font-size: 0.9em;
        margin-top: 5px;
    }
    
    /* Participant form styling improvements */
    .participant-form-fields .form-group {
        margin-bottom: 1rem;
    }
    
    .participant-form-fields label {
        font-weight: 600;
        font-size: 14px;
        color: #333;
        margin-bottom: 6px;
        display: block;
    }
    
    .participant-form-fields .form-control,
    .participant-form-fields .form-control-select {
        padding: 10px 15px;
        border-radius: 8px;
        border: 1px solid #ddd;
        font-size: 14px;
        transition: all 0.3s ease;
        width: 100%;
        height: 42px;
    }
    
    .participant-form-fields .form-control:focus,
    .participant-form-fields .form-control-select:focus {
        border-color: #F15B43;
        outline: none;
        box-shadow: 0 0 0 3px rgba(241, 91, 67, 0.1);
    }
    
    .participant-form-fields .form-control-select {
        background-color: #f8f8f8;
        cursor: pointer;
    }
    
    .add-more-participant {
        width: 42px;
        height: 42px;
        border-radius: 8px;
        border: 2px solid #F15B43;
        background: white;
        color: #F15B43;
        font-size: 18px;
        display: flex;
        align-items: center;
        justify-content: center;
        transition: all 0.3s ease;
        margin-top: 28px;
    }
    
    .add-more-participant:hover {
        background: #F15B43;
        color: white;
        transform: scale(1.05);
    }
    
    .remove-btn {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        width: 42px;
        height: 42px;
        border-radius: 8px;
        border: 2px solid #dc3545;
        background: white;
        transition: all 0.3s ease;
    }
    
    .remove-btn:hover {
        background: #dc3545;
        transform: scale(1.05);
    }
    
    .remove-btn i {
        transition: color 0.3s ease;
    }
    
    .remove-btn:hover i {
        color: white !important;
    }
    
    /* Custom column widths for better layout */
    @media (min-width: 992px) {
        .col-lg-2-5 {
            flex: 0 0 20.833333%;
            max-width: 20.833333%;
        }
    }
    
    @media (max-width: 991px) {
        .participant-form-fields .col-md-2,
        .participant-form-fields .col-md-3 {
            margin-bottom: 1rem;
        }
        
        .add-more-participant {
            margin-top: 0;
            width: 100%;
            margin-bottom: 1rem;
        }
    }
    
    @media (max-width: 768px) {
        .participant-form-fields .form-group {
            margin-bottom: 1.25rem;
        }
    }
</style>
<div class="donate-section">
    <div class="container">
        <div class="donate-detais">
            @if (session('success'))
                <div class="alert alert-success">{{ session('success') }}</div>
            @endif
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
                    <div class="donate-form edit-section" id="editParticipantSection" style="display: none;">
                        <div class="row g-3">
                                <h4>Edit Participant</h4>
                                <form id="editParticipantForm" method="POST" >
                                    @csrf
                                    @method('PUT')
                                    <input type="hidden" name="participant_id" id="editParticipantId">
                                    <div class="row form-wrap">

                                        <div class="col-md-2 col-sm-12 mb-3 form-group">
                                            <label for="editRole">Role <span class="text-danger">*</span></label>
                                            <select name="role" class="form-control-select w-100 paricipant-edit-input participant-role-edit" id="editRole" required>
                                                <option value="">Select Role</option>
                                                <option value="student">Student Participant</option>
                                                <option value="adult">Adult Attendee</option>
                                            </select>
                                            <span class="form-validation editRole"></span>
                                        </div>
                                        <div class="col-md-2 col-sm-12 mb-3 form-group">
                                            <label for="editFirstName">First Name <span class="text-danger">*</span></label>
                                            <input type="text" class="form-control paricipant-edit-input" id="editFirstName" name="first_name" required>
                                            <span class="form-validation editFirstName"></span>
                                        </div>
                                        <div class="col-md-2 col-sm-12 mb-3 form-group">
                                            <label for="editLastName">Last Name <span class="text-danger">*</span></label>
                                            <input type="text" class="form-control paricipant-edit-input" id="editLastName" name="last_name" required>
                                            <span class="form-validation editLastName"></span>
                                        </div>
                                        <div class="col-md-2 col-sm-12 mb-3 form-group" id="editAgeField">
                                            <label for="editAge" id="editAgeLabel">Age <span class="text-danger">*</span> <span style="font-weight: normal; color: #666; font-size: 0.9em;">(as on June 6th, 2026)</span></label>
                                            <input type="number" class="form-control paricipant-edit-input participant-age-edit" id="editAge" name="age" min="1" max="120" placeholder="Age">
                                            <span class="form-validation editAge"></span>
                                        </div>
                                        <div class="col-md-2 col-sm-12 mb-3 form-group" id="editEmailField">
                                            <label for="editEmail" id="editEmailLabel">Email</label>
                                            <input type="email" class="form-control paricipant-edit-input participant-email-edit" id="editEmail" name="email" placeholder="Email">
                                            <span class="form-validation editEmail"></span>
                                        </div>
                                        <div class="col-md-2 col-sm-12 mb-3 form-group" id="class_fieldEdit">
                                            <label for="editClass" id="editClass_label">Class</label>
                                            <select class="form-control-select paricipant-edit-input" id="editClass" name="class">
                                                <option value="">Select Class</option>
                                                @for ($i = 1; $i <= 12; $i++)
                                                    <option value="{{ $i }}">{{ $i }}</option>
                                                @endfor
                                                <option value="Others/ Special Schools">Others/ Special Schools</option>
                                            </select>
                                            <span class="form-validation editClass"></span>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-12 pt-2 d-flex justify-content-end ">
                                                <a class="outline-round" id="cancelEdit" style="cursor: pointer;">Cancel</a>
                                                <button type="submit" style="margin-left: 10px;" class="button-round reg_register">Save Changes</button>
                                        </div>
                                    </div>
                                </form>
                        </div>
                    </div>
                    <div  id="participant-add-section" >
                    <div class="row g-3">
                        <div class="col-md-12">
                            <h4>Participant Details</h4>
                        </div>
                        <div class="col-md-12 d-flex justify-content-end">
                            <a href="javascript:void(0);" id="openParticipantFormBtn" class="button-round">
                                Add Participant
                            </a>
                        </div>
                    {{-- When coming from the left menu "Add Participant", show the form open by default --}}
                    @php
                        $openParticipantForm = request()->query('open') === '1';
                    @endphp

                    @if($participants->where('type','<>',2)->isEmpty() && !$openParticipantForm)
                    <div class="col-md-12 text-center" id="noParticipants">
                        <p>No participants added yet click + button  to add participants</p>

                    </div>
                    @endif
                    </div>
                    <div class="collapse mt-3 {{ $openParticipantForm || $participants->where('type','<>',2)->isEmpty() ? 'show' : '' }}" id="collapseParticipant" style="{{ $openParticipantForm || $participants->where('type','<>',2)->isEmpty() ? 'display:block' : 'display:none' }}">
                        <div class="row participant-row">
                            <form action="{{ route('participant.store') }}" method="POST" class="participant_form">
                                @csrf
                                <div class="col-md-12 mb-3">
                                    <div class="alert" style="margin-bottom: 15px; padding: 12px; background-color: #fef0ed; border-left: 4px solid #F15B43; border-radius: 4px; color: #333;">
                                        <i class="fas fa-info-circle" style="color: #F15B43; margin-right: 8px;"></i>
                                        <strong>Note:</strong> If at least one participant is UNDER 12 years of age – Adult registration is MANDATORY.
                                    </div>
                                </div>
                                <div class="col-md-12 mb-3">
                                    <div class="alert" style="margin-bottom: 15px; padding: 12px; background-color: #fef0ed; border-left: 4px solid #F15B43; border-radius: 4px; color: #333;">
                                        <i class="fas fa-exclamation-triangle" style="color: #F15B43; margin-right: 8px;"></i>
                                        <strong style="color: #d94a35;">When registering, please select your role carefully:</strong>
                                        <ul style="margin-top: 8px; margin-bottom: 0; padding-left: 20px;">
                                            <li>If you are presenting or actively participating in the conference, select <strong>"Student Participant."</strong></li>
                                            <li>If you are attending only as an accompanying adult, please select <strong>"Adult Attendee."</strong></li>
                                        </ul>
                                    </div>
                                </div>
                                <div class="col-md-12 participant-form-fields" id="participant-fields">
                                    <div class="row g-3">

                                        <div class="col-lg-2 col-md-3 col-sm-6 col-12 form-group">
                                            <label for="role0">Role <span class="text-danger">*</span></label>
                                            <select name="participants[0][role]" class="form-control-select w-100 paricipant-input participant-role" id="role0" data-index="0" required>
                                                <option value="">Select Role</option>
                                                <option value="student">Student Participant</option>
                                                <option value="adult">Adult Attendee</option>
                                            </select>
                                            <span class="form-validation role0"></span>
                                        </div>
                                        <div class="col-lg-2-5 col-md-3 col-sm-6 col-12 form-group">
                                            <label for="first_name0">First Name <span class="text-danger">*</span></label>
                                            <input type="text" name="participants[0][first_name]" class="form-control paricipant-input" id="first_name0" required>
                                            <span class="form-validation first_name0"></span>
                                        </div>
                                        <div class="col-lg-2-5 col-md-3 col-sm-6 col-12 form-group">
                                            <label for="last_name0">Last Name <span class="text-danger">*</span></label>
                                            <input type="text" name="participants[0][last_name]" class="form-control paricipant-input" id="last_name0" required>
                                            <span class="form-validation last_name0"></span>
                                        </div>
                                        <div class="col-lg-2 col-md-3 col-sm-6 col-12 form-group" id="age_field0" style="display: none;">
                                            <label for="age0" id="age_label0">Age <span class="text-danger">*</span> <span style="font-weight: normal; color: #666; font-size: 0.85em;">(as on June 6th, 2026)</span></label>
                                            <input type="number" name="participants[0][age]" class="form-control paricipant-input participant-age" id="age0" data-index="0" min="1" max="120" placeholder="Age">
                                            <span class="form-validation age0"></span>
                                        </div>
                                        <div class="col-lg-2-5 col-md-3 col-sm-6 col-12 form-group" id="email_field0" style="display: none;">
                                            <label for="email0" id="email_label0">Email</label>
                                            <input type="email" name="participants[0][email]" class="form-control paricipant-input participant-email" id="email0" data-index="0" placeholder="Email (optional)">
                                            <span class="form-validation email0"></span>
                                        </div>
                                        <div class="col-lg-2 col-md-3 col-sm-6 col-12 form-group" id="class_field0">
                                            <label for="class0" id="class_label0">Class</label>
                                            <select name="participants[0][class]" class="form-control-select w-100 paricipant-input class_field0" id="class0">
                                                <option value="" disabled selected>Select Class</option>
                                                @for ($i = 1; $i <= 12; ++$i)
                                                    <option value="{{ $i }}">{{ $i }}</option>
                                                @endfor
                                                <option value="Others/ Special Schools">Others/ Special Schools</option>
                                            </select>
                                            <span class="form-validation class0"></span>
                                        </div>
                                       
                                        <div class="col-lg-1 col-md-1 col-sm-12 col-12 form-group d-flex align-items-end">
                                            <button type="button" id="add-more-participant" class="btn btn-add add-more-participant">
                                                <i class="fas fa-plus"></i>
                                            </button>
                                        </div>
                                       
                                        <div class="col-md-12 mb-3" id="age_warning0" style="display: none;">
                                            <div class="alert" style="margin-bottom: 0; padding: 10px; background-color: #fef0ed; border-left: 4px solid #F15B43; border-radius: 4px; color: #333;">
                                                <i class="fas fa-exclamation-triangle" style="color: #F15B43;"></i> 
                                                <strong style="color: #d94a35;">Important:</strong> Since you are adding a student under 12, you will be automatically added as an adult participant. Adult participant fee is $50. You will not be able to remove yourself from the participants list while there is a student under 12.
                                            </div>
                                        </div>
                                    </div>

                                </div>
                                <div class="col-md-12 pt-2 d-flex justify-content-end ">
                                <a class="outline-round collapseButton" style="cursor: pointer;">Cancel</a>
                                <button type="submit" class="button-round reg_register" style="margin-left: 10px;" id="addParticipant">Save</button>
                                </div>

                            </form>
                        </div>
                    </div>
                    </div>
                    <div class="row g-3 mt-3" id="participantTable" style="{{$participants->where('type','<>',2)->isEmpty()?'display:none':''}}">
                        <table class="mb-5" id="participants-table" style="width:100%">
                            <thead>
                            <tr>
                                <th style="background-color: #f15b43; color: #FFFFFF;">#</th>
                                <th style="background-color: #f15b43; color: #FFFFFF;">Role</th>
                                <th style="background-color: #f15b43; color: #FFFFFF;">First Name</th>
                                <th style="background-color: #f15b43; color: #FFFFFF;">Last Name</th>
                                <th style="background-color: #f15b43; color: #FFFFFF;">Age <span style="font-weight: normal; font-size: 0.85em;">(as on June 6th, 2026)</span></th>
                                <th style="background-color: #f15b43; color: #FFFFFF;">Class</th>
                                <th style="background-color: #f15b43; color: #FFFFFF;">Email</th>
                                <th style="background-color: #f15b43; color: #FFFFFF;" class="action-column-header" >Action</th>
                            </tr>
                            </thead>
                            <tbody>


                            </tbody>
                        </table>


                    </div>
                </div>
                <div class="form-inner" id="paymentSection">
                    <h4>Payment Details</h4>
                    <div>
                        <div id="payment-info" class="mt-3" style="display: {{ $user->participants->where('type','<>',2)->isEmpty() ? 'block' : 'none' }}">
                            <div style="font-size: 16px; color: #333;">
                                <p>
                                    Once you add all participants, proceed with payment.<br>
                                    <strong>Pricing:</strong> Student Participants: ${{ $payment_summary['studentPrice'] ?? '150.00' }} | Adult Attendees: ${{ $payment_summary['adultPrice'] ?? '50.00' }}
                                </p>
                            </div>
                        </div>
                        <div id="amount-payable-box" class="amount-payable-box row d-flex justify-content-around mt-3">
                                @if($payment_summary['amountPayable']> 0)
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

                                    <div class="d-flex justify-content-end align-items-center mt-3">
                                        <a href="javascript:void(0);" class="dynamic-link button-round reg_register make_payment"
                                           data-url="{{ route('payment.index') }}" data-class="link-payment" id="make_payment">Make Payment</a>
                                    </div>
                                </div>
                                 @else
                                 @if ( $payment_summary['paidAmount'] && !$payment_summary['paidAmount'] == 0)
                                     <div class="col-md-12" id="payment-done">
                                         @if($participantCount==0)
                                         <p style="font-size: 16px; color: #666; text-align: center; width: 100%;">
                                            No participants added. Payment is not required at this time.
                                         </p>
                                         @else
                                         <p style="font-size: 18px; color: green; font-weight: bold; text-align: center; width: 100%;">
                                             No payment due. Thank you!
                                         </p>
                                         @endif
                                         <div class="row d-flex justify-content-center mt-3">
                                            @if($payment_summary['paidAmount']>0)
                                            <div class="col-md-4" style="display: flex; justify-content: center;">
                                                <p style="font-weight: bold;"> Total Amount Paid: ${{ $payment_summary['paidAmount'] }}</p>
                                            </div>
                                            @endif
                                          
                                         </div>
                                         @php
                                             $hasCompletedPayment = \App\Models\Payment::where('registration_id', $user->id)
                                                 ->where('payment_status', 3)
                                                 ->exists();
                                             // Abstract upload only active when fully paid (amountPayable == 0)
                                             $isFullyPaid = isset($payment_summary) && (float)str_replace(',', '', $payment_summary['amountPayable']) == 0;
                                             $abstractStatusPt = optional($settings->where('code','abstract_status')->first())->value;
                                             $abstractNavVisiblePt = \App\Models\AbstractSubmission::dashboardAbstractNavVisible(
                                                 $user->id,
                                                 (bool) $abstractStatusPt,
                                                 $hasCompletedPayment && $isFullyPaid
                                             );
                                             $abstractNavLabelPt = \App\Models\AbstractSubmission::dashboardAbstractNavLabel($user->id);
                                         @endphp
                                         @if($hasCompletedPayment && $isFullyPaid && $abstractNavVisiblePt)
                                         <div class="col-md-12 d-flex justify-content-center align-items-center mt-3 mb-3">
                                             <a href="javascript:void(0);" class="dynamic-link button-round reg_register"
                                                 data-url="{{ route('abstract.create') }}"  data-class="link-abstract"
                                                 >{{ $abstractNavLabelPt }}</a>
                                         </div>
                                         @endif

                                     </div>
                                 @endif                
                             @endif
                        </div>
                    </div>
                </div>


                <!--
            <div class="donate-form">
                <div class="form-inner">
                    <h4 class="mt-4">Participant Details</h4>
                    @if($participants->isEmpty())
                        <p>No participants added yet.</p>
                    @else
                        <table class="mb-5" id="participants-table">
                            <thead>
                            <tr>
                                <th style="background-color: #f15b43; color: #FFFFFF;">#</th>
                                <th style="background-color: #f15b43; color: #FFFFFF;">First Name</th>
                                <th style="background-color: #f15b43; color: #FFFFFF;">Last Name</th>
                                <th style="background-color: #f15b43; color: #FFFFFF;">Class</th>
                                <th style="background-color: #f15b43; color: #FFFFFF;">Action</th>
                            </tr>
                            </thead>
                            <tbody>


                            </tbody>
                        </table>
                    @endif
                </div>
            </div>

            <div class="mt-3" style=" display: flex; justify-content: space-between; align-items: center; padding: 15px; border: 1px solid #f15b43; border-radius: 8px; box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);">
                <div style="font-size: 16px; color: #333;">
                    <p>
                        Once you add all participants <a href="#">click here</a> to proceed with payment.<br>
                        <strong>Pricing:</strong> Student Participants: ${{ $payment_summary['studentPrice'] ?? '150.00' }} | Adult Attendees: ${{ $payment_summary['adultPrice'] ?? '50.00' }}
                    </p>
                </div>
                @if(isset($user->participants))
                <div id="amount-payable-box" class="amount-payable-box">
                    <p style="margin: 0px">
                        Amount Payable:
                    <div class="d-flex justify-content-center">
                        $<span id="amount_payable" >{{ $user->participants->count() + 1 * 500 }}</span>
                    </div>
                    </p>
                </div>
                @endif
            </div> -->
        </div>
    </div>
</div>


<div class="modal fade" id="delete-modal" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="staticBackdropLabel">Delete Participant</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="delete-log-form"  method="POST">
                <div class="modal-body">
                    <input type="hidden" name="_token" value="{{ csrf_token() }}">
                    <input type="hidden" id="deleteId" name="deleteId">
                    <p>Are you sure you want to delete this participant</p>
                    <div class="modal-footer">

                        <button type="button" class="btn btn-sm" style="background-color: #F15B43; color: white; border: none;" data-bs-dismiss="modal">Close</button>
                        <button type="button" class="btn btn-sm btn_delete_participant" style="background-color: #F15B43; color: white; border: none;" data-loading-text="">Delete</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>
<!-- <script src="{{ url('libs/dataTable/datatables.min.js') }}"></script> -->

<script>

    $(document).ready(function (){
        console.log('=== Participants page script loaded and executing ===');
        var registrationLastDate = "{{ \Carbon\Carbon::parse($registrationLastDate)->toDateString() }}";
        $('.collapseButton').on('click', function (e) {
            $('#collapseParticipant').toggle()
        });
        $(document).on('click', '#openParticipantFormBtn', function (e) {
            e.preventDefault();
            $('#collapseParticipant').show();
            $("html, body").animate({ scrollTop: $('#collapseParticipant').offset().top - 120 }, 400);
        });
        $('.make_payment').on('click', function (e) {
            $("html, body").animate({ scrollTop: $(document).height()-1200 }, 1000);
        });
        // dynamic-link handler is managed by the parent page (index.blade.php) via event delegation
            $(document).on('input', '.paricipant-input', function (obj) {
                   name = $(this).attr('id');
                $('.' + name).text('');
                });
       // Handle role changes - show/hide fields based on role
       $(document).on('change', '.participant-role', function(e) {
            const index = $(this).data('index');
            const role = $(this).val();
            
            if (role === 'student') {
                // Student: show age (required), class, show email (optional)
                $('#age_field' + index).show();
                $('#age' + index).prop('required', true);
                // Ensure the required asterisk is visible
                const ageLabel = $('#age_label' + index);
                if (ageLabel.find('.text-danger').length === 0) {
                    ageLabel.append(' <span class="text-danger">*</span>');
                }
                $('#class_field' + index).show();
                $('#email_field' + index).show();
                $('#email' + index).prop('required', false);
                // Update label to remove required asterisk and update placeholder
                const emailLabel = $('#email_label' + index);
                emailLabel.find('.text-danger').remove();
                $('#email' + index).attr('placeholder', 'Email (optional)');
                $('#age_warning' + index).hide();
            } else if (role === 'adult') {
                // Adult: hide age, class, show email (required)
                $('#age_field' + index).hide();
                $('#age' + index).prop('required', false).val('');
                $('#age_label' + index + ' .text-danger').hide();
                $('#class_field' + index).hide();
                $('#class' + index).val('');
                $('#email_field' + index).show();
                $('#email' + index).prop('required', true);
                // Update label to show required asterisk
                const emailLabel = $('#email_label' + index);
                if (emailLabel.find('.text-danger').length === 0) {
                    emailLabel.append(' <span class="text-danger">*</span>');
                }
                $('#email' + index).attr('placeholder', 'Email (required)');
                $('#age_warning' + index).hide();
            } else {
                // No role selected: hide all optional fields
                $('#age_field' + index).hide();
                $('#class_field' + index).hide();
                $('#email_field' + index).hide();
                $('#age_warning' + index).hide();
                // Reset email label and placeholder
                const emailLabel = $('#email_label' + index);
                emailLabel.find('.text-danger').remove();
                $('#email' + index).prop('required', false).attr('placeholder', 'Email');
            }
            
            // Recalculate pricing and check for adult requirement
            // This will also update age warnings based on total adult count
            checkAdultRequirement();
            updatePricingSummary();
        });
        
        // Helper function to count total adults (existing + in form)
        function getTotalAdultCount() {
            let adultCount = 0;
            
            // Count adults in the form (new participants being added)
            $('.participant-role').each(function() {
                const role = $(this).val();
                if (role === 'adult') {
                    adultCount++;
                }
            });
            
            // Count existing adults in the DataTable (already saved participants)
            if ($.fn.DataTable && $('#participants-table').length > 0) {
                const table = $('#participants-table').DataTable();
                if (table) {
                    table.rows().every(function() {
                        const data = this.data();
                        const role = data.role || '';
                        if (role.toLowerCase() === 'adult') {
                            adultCount++;
                        }
                    });
                }
            }
            
            return adultCount;
        }
        
        // Handle age changes - check if < 12 and show alert about mandatory adult
        $(document).on('input change', '.participant-age', function(e) {
            const index = $(this).data('index');
            const age = parseInt($(this).val());
            const role = $('#role' + index).val();
            
            if (role === 'student' && age > 0) {
                if (age < 12) {
                    // Show alert about mandatory adult participant and $50 fee
                    $('#age_warning' + index).html(`
                        <div class="alert" style="margin-bottom: 0; padding: 10px; background-color: #fef0ed; border-left: 4px solid #F15B43; border-radius: 4px; color: #333;">
                            <i class="fas fa-exclamation-triangle" style="color: #F15B43;"></i> 
                            <strong style="color: #d94a35;">Important:</strong> Since you are adding a student under 12, you will be automatically added as an adult participant. Adult participant fee is $50. You will not be able to remove yourself from the participants list while there is a student under 12.
                        </div>
                    `).show();
                    checkAdultRequirement();
                } else {
                    $('#age_warning' + index).hide();
                    checkAdultRequirement();
                }
            } else {
                $('#age_warning' + index).hide();
            }
            
            updatePricingSummary();
        });
        
        // Check if adult attendee is required (any student < 12)
        function checkAdultRequirement() {
            let requiresAdult = false;
            let studentCount = 0;
            let adultCount = 0;
            
            // Count adults and students in the form (new participants being added)
            $('.participant-role').each(function() {
                const index = $(this).data('index');
                const role = $(this).val();
                
                if (role === 'student') {
                    studentCount++;
                    const age = parseInt($('#age' + index).val() || 0);
                    if (age > 0 && age < 12) {
                        requiresAdult = true;
                    }
                } else if (role === 'adult') {
                    adultCount++;
                }
            });
            
            // Count existing adults in the DataTable (already saved participants)
            let existingAdultCount = 0;
            if ($.fn.DataTable && $('#participants-table').length > 0) {
                const table = $('#participants-table').DataTable();
                if (table) {
                    table.rows().every(function() {
                        const data = this.data();
                        const role = data.role || '';
                        if (role.toLowerCase() === 'adult') {
                            existingAdultCount++;
                        }
                    });
                }
            }
            
            // Total adults = existing adults + adults in form
            const totalAdultCount = existingAdultCount + adultCount;
            
            // Update age warnings for all students under 12 - hide if adults exist
            $('.participant-role').each(function() {
                const index = $(this).data('index');
                const role = $(this).val();
                if (role === 'student') {
                    const age = parseInt($('#age' + index).val() || 0);
                    if (age > 0 && age < 12) {
                        // Only show warning if there are no adults
                        if (totalAdultCount === 0) {
                            $('#age_warning' + index).show();
                        } else {
                            $('#age_warning' + index).hide();
                        }
                    }
                }
            });
            
            // Show/hide adult requirement warning
            if (requiresAdult && totalAdultCount === 0) {
                if ($('#adult-requirement-warning').length === 0) {
                    $('#participant-fields').before(`
                        <div class="col-md-12 mb-3" id="adult-requirement-warning">
                            <div class="alert" style="margin-bottom: 15px; padding: 12px; background-color: #fef0ed; border-left: 4px solid #F15B43; border-radius: 4px; color: #333;">
                                <i class="fas fa-exclamation-circle" style="color: #F15B43;"></i> 
                                <strong style="color: #d94a35;">Adult Attendee Required:</strong> Students under 12 must be accompanied by at least one adult attendee. Please add an Adult Attendee participant.
                            </div>
                        </div>
                    `);
                }
            } else {
                $('#adult-requirement-warning').remove();
            }
            
            return { requiresAdult, studentCount, adultCount: totalAdultCount };
        }
        
        // Update pricing summary dynamically
        function updatePricingSummary() {
            let studentCount = 0;
            let adultCount = 0;
            
            $('.participant-role').each(function() {
                const role = $(this).val();
                if (role === 'student') {
                    studentCount++;
                } else if (role === 'adult') {
                    adultCount++;
                }
            });
            
            const studentPrice = {{ (float) str_replace(',', '', $payment_summary['studentPrice'] ?? '150.00') }};
            const adultPrice = {{ $adultFee ?? 50 }};
            const studentTotal = studentCount * studentPrice;
            const adultTotal = adultCount * adultPrice;
            const grandTotal = studentTotal + adultTotal;
            
            // Update pricing display if element exists
            if ($('#pricing-summary').length === 0) {
                $('#participant-fields').after(`
                    <div class="col-md-12 mt-3 mb-3" id="pricing-summary">
                        <div class="card" style="background-color: #f8f9fa; padding: 15px;">
                            <h5 style="margin-bottom: 15px;">Pricing Summary</h5>
                            <div class="row">
                                <div class="col-md-6">
                                    <p style="margin-bottom: 5px;">Student Participants: <span id="student-count">0</span> × $<span id="student-price">${studentPrice}</span></p>
                                    <p style="margin-bottom: 5px;">Adult Attendees: <span id="adult-count">0</span> × $<span id="adult-price">{{ $adultFee ?? 50 }}</span></p>
                                </div>
                                <div class="col-md-6 text-end">
                                    <p style="margin-bottom: 5px;"><strong>Subtotal (Students):</strong> $<span id="student-total">0.00</span></p>
                                    <p style="margin-bottom: 5px;"><strong>Subtotal (Adults):</strong> $<span id="adult-total">0.00</span></p>
                                    <hr>
                                    <p style="margin-bottom: 0; font-size: 18px;"><strong>Total:</strong> $<span id="grand-total">0.00</span></p>
                                </div>
                            </div>
                        </div>
                    </div>
                `);
            }
            
            $('#student-count').text(studentCount);
            $('#adult-count').text(adultCount);
            $('#student-total').text(studentTotal.toFixed(2));
            $('#adult-total').text(adultTotal.toFixed(2));
            $('#grand-total').text(grandTotal.toFixed(2));
        }
        
        // Initialize pricing summary on page load
        updatePricingSummary();
        
        // Initialize field visibility for the first participant row on page load
        $(document).ready(function() {
            // Check if role0 has a value and show/hide fields accordingly
            const initialRole = $('#role0').val();
            if (initialRole === 'student') {
                $('#age_field0').show();
                $('#age0').prop('required', true);
                $('#class_field0').show();
                $('#email_field0').show();
            } else if (initialRole === 'adult') {
                $('#age_field0').hide();
                $('#class_field0').hide();
                $('#email_field0').show();
                $('#email0').prop('required', true);
            }
        });
        
        // Legacy handler for old "is_guest" field (for backward compatibility)
       $(document).on('change', '.is_guest', function(e) {
        var participantIndex = $(this).attr('name').match(/\d+/)[0];
                if ($(this).val() == 1) {
               $('#class'+participantIndex).prop('disabled','disabled');
               $('#class'+participantIndex).removeClass('paricipant-input');
               $('#class' + participantIndex).css('background-color', '#cdcdcd');
               $('#class_label').css('color', '#727272');
               $('#class'+participantIndex).val('');
            } else {
                $('#class'+participantIndex).removeAttr('disabled');
                $('#class'+participantIndex).addClass('paricipant-input');
                $('#class' + participantIndex).css('background-color', '#f8f8f8');
                $('#class_label').css('color', '#383838');
            }
        });

        $('#selected_typeEdit').on('change', function(e) {
            var participantType = $('#selected_typeEdit').val();
            console.log(participantType);

            if (participantType == 1) {
                $('#editClass').attr('disabled', 'disabled');
                $('#editClass').removeClass('paricipant-edit-input');
                $('#editClass').css('background-color', '#cdcdcd');
                $('#editClass_label').css('color', '#727272');
                $('#editClass').val('');
            } else {
                $('#editClass').removeAttr('disabled');
                $('#editClass').addClass('paricipant-edit-input');
                $('#editClass').css('background-color', '#f8f8f8');
                $('#editClass_label').css('color', '#383838');
            }
        });

        const amountPerParticipant = 500;
        let participantCount = {{ $participantCount }};
        let paidParticipantCount = {{ $paidParticipantCount }};
        let totalAmount = participantCount * amountPerParticipant;


        // if (participantCount === paidParticipantCount) {
        //     $('#payment-done').show();
        //     $('#payment').hide();
        // } else {
        //     $('#payment').show();
        //     $('#payment-done').hide();
        // }


        // Destroy any existing DataTable instance before re-initializing
        if ($.fn.DataTable.isDataTable('#participants-table')) {
            $('#participants-table').DataTable().destroy();
        }
        var $participantsTable =table= $('#participants-table').DataTable({
        processing: true,
        serverSide: true,
        ajax: {
            url: '{{ route("participants") }}',
            data: { partial: 'add_participant' },
            error: function(xhr, error, thrown) {
                console.error('DataTable AJAX error:', error, thrown, xhr.responseText);
            },
            dataSrc: function (json) {
                participantCount = json.recordsTotal;
                totalAmount = participantCount * amountPerParticipant;
                window.hasActionColumn = json.has_action_column;
                // Always show the Action column header - the registered user can always delete other participants
                // The action column is the last column (index 7: #, Role, First Name, Last Name, Age, Class, Email, Action)
                const actionColumnIndex = 7; // Action is the 8th column (0-indexed: 7)
                
                // Always show the Action column header
                $('#participants-table thead th.action-column-header').show();
                table.column(actionColumnIndex).visible(true);
                
                // If no action column needed, hide it (but this shouldn't happen for registered users)
                if (!window.hasActionColumn) {
                    $('#participants-table thead th.action-column-header').hide();
                    table.column(actionColumnIndex).visible(false);
                    $("#participants-table").css({
                                    "width": "100%"
                                    });
                }
               
                return json.data;
            }
        },
        columns: [
            {
                data: 'DT_RowIndex',
                name: 'DT_RowIndex',
                orderable: false,
                searchable: false
            },
            {
                data: 'role',
                name: 'role'
            },
            {
                data: 'first_name',
                name: 'first_name'
            },
            {
                data: 'last_name',
                name: 'last_name'
            },
            {
                data: 'age',
                name: 'age'
            },
            {
                data: 'class',
                name: 'class'
            },
            {
                data: 'email',
                name: 'email',
                defaultContent: 'N/A'
            },
            {
                data: 'action',
                name: 'action',
                orderable: false, searchable: false
            }
           
        ],
        drawCallback: function(settings) {
            // Always show delete buttons - deletion is controlled by backend based on payment status
            // The backend will only allow deletion if payment hasn't been completed or if there's still amount payable
            $('#participants-table tbody tr').each(function() {
                // Show delete button if it exists (backend controls whether it's rendered)
                $(this).find('.delete-participant').show();
            });
        }
    });
    
        $("#participants-table_filter, #participants-table_paginate, #participants-table_info, #participants-table_length").css({
            "display": "none"
        });
       
        // Initialize adult requirement check on page load
        // This will hide age warnings if adults already exist
        setTimeout(function() {
            checkAdultRequirement();
        }, 500); // Wait a bit for DataTable to fully initialize
       
        const participantFields = $('#participant-fields');
        const amountPayable = $('#amount_payable');
        const maxParticipants = 10;
        const $minClass=1;
        const $maxClass=12;
        var selectClass='';

        for(i=$minClass;i<=$maxClass;++i){
        selectClass+='<option value='+i+'>'+i+'</option>';
        }
        selectClass+='<option value="Others/ Special Schools">Others/ Special Schools</option>';


        var addMore=1;
            $(document).on('click', '.add-more-participant', function(e) {

                e.stopImmediatePropagation();
                e.preventDefault();
                // if(addMore==1){
                    // addMore=0;
                        console.log(participantCount);
                    if (participantCount+1 < maxParticipants) {
                        const newIndex = participantCount + 1;
                        const newParticipantDiv = $(`
                                <div class="row g-3 ajax-div">
                                    <div class="col-lg-2 col-md-3 col-sm-6 col-12 form-group">
                                        <label for="role${newIndex}">Role <span class="text-danger">*</span></label>
                                        <select name="participants[${newIndex}][role]" class="form-control-select w-100 paricipant-input participant-role" id="role${newIndex}" data-index="${newIndex}" required>
                                            <option value="">Select Role</option>
                                            <option value="student">Student Participant</option>
                                            <option value="adult">Adult Attendee</option>
                                        </select>
                                        <span class="form-validation role${newIndex}"></span>
                                    </div>
                                    <div class="col-lg-2-5 col-md-3 col-sm-6 col-12 form-group">
                                        <label for="first_name${newIndex}">First Name <span class="text-danger">*</span></label>
                                        <input type="text" name="participants[${newIndex}][first_name]" class="form-control paricipant-input" id="first_name${newIndex}" required>
                                        <span class="form-validation first_name${newIndex}"></span>
                                    </div>
                                    <div class="col-lg-2-5 col-md-3 col-sm-6 col-12 form-group">
                                        <label for="last_name${newIndex}">Last Name <span class="text-danger">*</span></label>
                                        <input type="text" name="participants[${newIndex}][last_name]" class="form-control paricipant-input" id="last_name${newIndex}" required>
                                        <span class="form-validation last_name${newIndex}"></span>
                                    </div>
                                    <div class="col-lg-2 col-md-3 col-sm-6 col-12 form-group" id="age_field${newIndex}" style="display: none;">
                                        <label for="age${newIndex}" id="age_label${newIndex}">Age <span class="text-danger">*</span> <span style="font-weight: normal; color: #666; font-size: 0.85em;">(as on June 6th, 2026)</span></label>
                                        <input type="number" name="participants[${newIndex}][age]" class="form-control paricipant-input participant-age" id="age${newIndex}" data-index="${newIndex}" min="1" max="120" placeholder="Age">
                                        <span class="form-validation age${newIndex}"></span>
                                    </div>
                                    <div class="col-lg-2-5 col-md-3 col-sm-6 col-12 form-group" id="email_field${newIndex}" style="display: none;">
                                        <label for="email${newIndex}" id="email_label${newIndex}">Email</label>
                                        <input type="email" name="participants[${newIndex}][email]" class="form-control paricipant-input participant-email" id="email${newIndex}" data-index="${newIndex}" placeholder="Email (optional)">
                                        <span class="form-validation email${newIndex}"></span>
                                    </div>
                                    <div class="col-lg-2 col-md-3 col-sm-6 col-12 form-group" id="class_field${newIndex}">
                                        <label for="class${newIndex}" id="class_label${newIndex}">Class</label>
                                        <select name="participants[${newIndex}][class]" class="form-control-select w-100 paricipant-input class_field${newIndex}" id="class${newIndex}">
                                            <option value="" disabled selected>Select Class</option>
                                            @for ($i = 1; $i <= 12; ++$i)
                                            <option value="{{ $i }}">{{ $i }}</option>
                                            @endfor
                                            <option value="Others/ Special Schools">Others/ Special Schools</option>
                                        </select>
                                        <span class="form-validation class${newIndex}"></span>
                                    </div>
                                    <div class="col-lg-1 col-md-1 col-sm-12 col-12 form-group d-flex align-items-end">
                                        <a class="remove-btn" style="margin-right: 10px;"><i class="fas fa-trash-alt" style="color:red; cursor: pointer; font-size: 18px;"></i></a>
                                        <button type="button" class="btn btn-add add-more-participant">
                                            <i class="fas fa-plus"></i>
                                        </button>
                                    </div>
                                    <div class="col-md-12 mb-3" id="age_warning${newIndex}" style="display: none;">
                                        <div class="alert" style="margin-bottom: 0; padding: 10px; background-color: #fef0ed; border-left: 4px solid #F15B43; border-radius: 4px; color: #333;">
                                            <i class="fas fa-exclamation-triangle" style="color: #F15B43;"></i> 
                                            <strong style="color: #d94a35;">Important:</strong> Since you are adding a student under 12, you will be automatically added as an adult participant. Adult participant fee is $50. You will not be able to remove yourself from the participants list while there is a student under 12.
                                        </div>
                                    </div>
                                </div>
                            `);
                            $('.btn-add').hide();
                            $('#participant-fields').append(newParticipantDiv);

                            // addMore=1;

                            participantCount++;
                                // amountPayable.text((participantCount * participantCost).toString());
                                    /* newParticipantDiv.find('.delete').on('click', function () {
                                    newParticipantDiv.remove();
                                    participantCount--;
                                    //  amountPayable.text((participantCount * participantCost).toString());
                                    }); */
                } else {
                    alert('You can only add up to 10 participants.');
                }

                // }


            });
            
            // Handle Save button for adding participants
            $(document).on('click', '#addParticipant', function (event) {
                event.preventDefault();
                event.stopImmediatePropagation();
                    console.log('addParticipant');
                    
                    // Note: We no longer block submission if there's a student under 12 without adults
                    // The backend will automatically add the registered user as an adult participant
                    // This check is just for informational purposes
                    const requirementCheck = checkAdultRequirement();
                    
                    // Validate required fields
                    var valid=1;
                    $('.participant-role').each(function() {
                        const index = $(this).data('index');
                        const role = $(this).val();
                        
                        if (!role) {
                            $('.role' + index).text('Role is required');
                            valid = 0;
                        } else {
                            $('.role' + index).text('');
                            
                            // Validate student fields
                            if (role === 'student') {
                                const age = $('#age' + index).val();
                                if (!age || age.trim() === '') {
                                    $('.age' + index).text('Age is required for students');
                                    valid = 0;
                                } else if (parseInt(age) < 1 || parseInt(age) > 120) {
                                    $('.age' + index).text('Please enter a valid age (1-120)');
                                    valid = 0;
                                } else {
                                    $('.age' + index).text('');
                                }
                            }
                            
                            // Validate adult fields
                            if (role === 'adult') {
                                const email = $('#email' + index).val();
                                if (!email || email.trim() === '') {
                                    $('.email' + index).text('Email is required for adult attendees');
                                    valid = 0;
                                } else if (!/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email)) {
                                    $('.email' + index).text('Please enter a valid email address');
                                    valid = 0;
                                } else {
                                    $('.email' + index).text('');
                                }
                            }
                        }
                    });
                    
                    // Validate other required fields
                    $('.paricipant-input[required]').each(function (i, obj) {
                        if ($(obj).is(':visible') && obj.value.trim() === '') {
                            let name = obj.id; 
                            $('.' + name).text('Field is required');
                            valid = 0;
                        } else {
                            let name = obj.id;
                            $('.' + name).text('');
                        }
                    });

                    var url =$('.participant_form').attr('action');
                    console.log('Validation result:', valid);
                    if (valid == 1) {
                        $('#loading').show();
                        $("html, body").animate({ scrollTop: $(document).height()-1500 }, 1000);
                             $.ajax({
                                headers: {
                                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                                },
                                type : 'POST',
                                url: url,
                                data : $('.participant_form').serialize(),
                                dataType: 'json',

                                success: function (result) {
                                $('#loading').hide();
                                $("#participants-table").css({
                                    "width": "100%"
                                    });
                                $('#participantTable').show();
                                $('#noParticipants').hide();

                                if (result.error == 0) {
                                $('.success-message').html(result.success);
                                // Show special alert if registered user was auto-added
                                if (result.auto_added_registered_user) {
                                    swal({
                                        title: "Participant Added",
                                        text: "An adult participant (you) has been auto-added. Fee: $50.",
                                        icon: "warning",
                                        button: {
                                            text: "OK",
                                            className: "swal-button--confirm"
                                        },
                                        className: "swal-auto-adult"
                                    });
                                } else {
                                    swal("Success", result.success, "success");
                                }
                                $('.success-message').css("color", "#1ed104");
                                $('.ajax-div').remove();
                                $('#collapseParticipant').hide();
                                $('.participant_form')[0].reset();
                                if(result.participantCount == 10){
                                    $('#collapseDiv').hide();
                                }
                                if(result.participantCount == 0 || !result.hasTypeZero)
                                 {
                                     $('.abstract-menu-class').css("pointer-events", "none");
                                }
                                 else
                                 {
                                     $('.abstract-menu-class').removeAttr("style");
                                }

                                // Reload the DataTable to show the newly added participant
                                setTimeout(function() {
                                    try {
                                        // Try multiple methods to ensure the table reloads
                                        if ($('#participants-table').length > 0 && $.fn.DataTable.isDataTable('#participants-table')) {
                                            $('#participants-table').DataTable().ajax.reload(null, false);
                                        } else if ($participantsTable && typeof $participantsTable.ajax !== 'undefined') {
                                            $participantsTable.ajax.reload(null, false);
                                        } else if (typeof table !== 'undefined' && table) {
                                            table.ajax.reload(null, false);
                                        }
                                        console.log('DataTable reloaded after participant addition');
                                    } catch (e) {
                                        console.error('Error reloading DataTable:', e);
                                        // Last resort: reload the page
                                        location.reload();
                                    }
                                }, 500); // Small delay to ensure the server has processed the request

                                $.ajax({
                                url: "{{ route('payment-details') }}",
                                headers: { 'X-Requested-With': 'XMLHttpRequest' },
                                success: function (html) {
                                    $('#paymentSection').html(html);
                                },
                                error: function (xhr, status, error) {
                                    console.error('Payment details error:', error);
                                    console.error('Response:', xhr.responseText);
                                    var errorMessage = 'Error loading payment details. Please try again later.';
                                    if (xhr.responseJSON && xhr.responseJSON.message) {
                                        errorMessage = xhr.responseJSON.message;
                                    }
                                    $('#paymentSection').html('<p style="color: red;">' + errorMessage + '</p>');
                                }
                            });

                                } else if (result.error == 1) {
                                // Business/validation error returned from backend (error == 1)
                                $('.success-message').html(result.success);
                                $('.success-message').delay(50).fadeOut('slow');
                                swal("Error", result.success, "error");
                                }
                                // status = 0;
                            },
                            error: function (xhr, status, error) {
                                $('#loading').hide();
                                console.error('Error:', error);
                                console.error('Status:', status);
                                console.error('Response:', xhr.responseText);
                                console.error('Response JSON:', xhr.responseJSON);
                                
                                var errorMessage = 'Error adding participant. Please try again.';
                                
                                // Check for success field (Laravel validation returns error in success field sometimes)
                                if (xhr.responseJSON && xhr.responseJSON.success) {
                                    errorMessage = xhr.responseJSON.success;
                                } else if (xhr.responseJSON && xhr.responseJSON.message) {
                                    errorMessage = xhr.responseJSON.message;
                                } else if (xhr.responseJSON && xhr.responseJSON.errors) {
                                    var errors = xhr.responseJSON.errors;
                                    errorMessage = 'Validation errors: ';
                                    $.each(errors, function(key, value) {
                                        if (Array.isArray(value)) {
                                            errorMessage += value.join(', ') + ' ';
                                        } else {
                                            errorMessage += value + ' ';
                                        }
                                    });
                                } else if (xhr.responseText) {
                                    // Try to parse HTML response for error messages
                                    try {
                                        var $response = $('<div>').html(xhr.responseText);
                                        var errorText = $response.find('.error, .alert-danger, .validation-error').text();
                                        if (errorText) {
                                            errorMessage = errorText;
                                        }
                                    } catch (e) {
                                        // If parsing fails, use default message
                                    }
                                }
                                
                                swal("Error", errorMessage, "error");
                                $("#addParticipant").removeAttr('disabled');
                            }

                        });

                    }

            });

            $(document).on('click', '.edit-participant', function (event) {
                // Check if the edit button is disabled (payment completed)
                // The disabled button has cursor: not-allowed, gray color, and title "Edit disabled - Payment completed"
                var title = $(this).attr('title') || '';
                var cursor = $(this).css('cursor');
                var color = $(this).css('color');
                
                if (title.includes('disabled') || cursor === 'not-allowed' || color === 'rgb(204, 204, 204)' || color === '#ccc') {
                    alert('Edit is disabled because payment for this registration has been completed.');
                    $('#loading').hide();
                    return false; // Prevent opening the edit form
                }
                
                $('#loading').show();
                var participantId = $(this).data('id');
                var firstName = $(this).data('first-name');
                var lastName = $(this).data('last-name');
                var classValue = $(this).data('class');
                var type = $(this).data('type');
                var role = $(this).data('role') || '';
                var age = $(this).data('age') || '';
                var email = $(this).data('email') || '';
                var isRegisteredUser = $(this).data('is-registered-user') == 1;
                var url=$(this).data('href');
                $('#editParticipantForm').attr('action', url);
                $('#editParticipantId').val(participantId);
                $('#editFirstName').val(firstName);
                $('#editLastName').val(lastName);
                $('#selected_typeEdit').val(type);
                $('#editRole').val(role);
                $('#editAge').val(age);
                
                // Auto-populate email if this is the registered user and email is empty
                if (isRegisteredUser && (!email || email === 'N/A' || email.trim() === '')) {
                    // Get email from user's registration - use the user object passed to the view
                    var userEmail = '{{ $user->email ?? "" }}';
                    if (userEmail) {
                        email = userEmail;
                    }
                }
                $('#editEmail').val(email || '');
                $('#editClass').val(classValue);
                $('#participant-add-section').hide();
                $('#editParticipantSection').show();
                $('#loading').hide();

                // Handle role-based field visibility
                if (role === 'student') {
                    $('#editAgeField').show();
                    $('#editAge').prop('required', true);
                    $('#editAgeLabel .text-danger').show();
                    $('#class_fieldEdit').show();
                    $('#editEmailField').show();
                    $('#editEmail').prop('required', false);
                    // Remove required asterisk from email label for students
                    $('#editEmailLabel .text-danger').remove();
                    $('#editEmail').attr('placeholder', 'Email (optional)');
                } else if (role === 'adult') {
                    $('#editAgeField').hide();
                    $('#editAge').prop('required', false).val('');
                    $('#editAgeLabel .text-danger').hide();
                    $('#class_fieldEdit').hide();
                    $('#editClass').val('');
                    $('#editEmailField').show();
                    $('#editEmail').prop('required', true);
                    // Add required asterisk to email label for adults
                    if ($('#editEmailLabel .text-danger').length === 0) {
                        $('#editEmailLabel').append(' <span class="text-danger">*</span>');
                    }
                    $('#editEmail').attr('placeholder', 'Email (required)');
                } else {
                    $('#editAgeField').hide();
                    $('#class_fieldEdit').hide();
                    $('#editEmailField').hide();
                }

                // Legacy type handling
                var participantType = $('#selected_typeEdit').val();
                if (participantType == 1) {
                    $('#editClass').attr('disabled','disabled');
                    $('#editClass').removeClass('paricipant-edit-input');
                    $('#editClass').css('background-color', '#cdcdcd');
                    $('#editClass_label').css('color', '#727272');
                } else {
                    $('#editClass').removeAttr('disabled');
                    $('#editClass').addClass('paricipant-edit-input');
                    $('#editClass').css('background-color', '#f8f8f8');
                    $('#editClass_label').css('color', '#383838');
                }
            });
            
            // Handle role changes in edit form
            $(document).on('change', '.participant-role-edit', function() {
                const role = $(this).val();
                if (role === 'student') {
                    $('#editAgeField').show();
                    $('#editAge').prop('required', true);
                    $('#editAgeLabel .text-danger').show();
                    $('#class_fieldEdit').show();
                    $('#editEmailField').show();
                    $('#editEmail').prop('required', false);
                } else if (role === 'adult') {
                    $('#editAgeField').hide();
                    $('#editAge').prop('required', false).val('');
                    $('#editAgeLabel .text-danger').hide();
                    $('#class_fieldEdit').hide();
                    $('#editClass').val('');
                    $('#editEmailField').show();
                    $('#editEmail').prop('required', true);
                }
            });

        $('#cancelEdit').on('click', function () {
            $('#editParticipantSection').hide();
            $('#participant-add-section').show();
        });

        $('#editParticipantForm').on('submit', function (e) {
            e.preventDefault();

            var formData = $(this).serializeArray();
            var route = $('#editParticipantForm').attr('action');

            var typeValue = $('#selected_typeEdit').val();
            if (typeValue == '1') {
                formData = formData.filter(function (field) {
                    return field.name !== 'class';
                });
                formData.push({ name: 'class', value: null });
            }
            var valid=1;
            var role = $('#editRole').val();
            $('.paricipant-edit-input').each(function (i, obj) {
                // Skip email validation for students (email is optional for students)
                if (obj.id === 'editEmail' && role === 'student') {
                    $('.editEmail').text(''); // Clear any previous error
                    return; // Skip validation for email when role is student
                }
                
                // Skip age validation for adults (age is optional for adults)
                if (obj.id === 'editAge' && role === 'adult') {
                    $('.editAge').text(''); // Clear any previous error
                    return; // Skip validation for age when role is adult
                }
                
                if (obj.value.trim() === '') {
                    let name = obj.id; 
                      
                    $('.' + name).text(obj.name+' is required');
                    
                    valid = 0;
                } else {
                    let name = obj.id;
                    $('.' + name).text('');
                }
                });
        if(valid==1){  
            // Add _method field for PUT request
            formData.push({ name: '_method', value: 'PUT' });
            
            $.ajax({
                url: route,
                type: 'POST', // Use POST with _method field for Laravel
                dataType: 'JSON',
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content'),
                    'X-Requested-With': 'XMLHttpRequest'
                },
                data: $.param(formData),
                success: function (response) {
                    $('#editParticipantSection').hide();
                    $('#participant-add-section').show();
                    $('#participants-table').DataTable().ajax.reload();
                    swal("Success", response.success, "success");
                    console.log(response.participantCount);
                    if(!response.hasTypeZero)
                        {
                            $('.abstract-menu-class').css("pointer-events", "none");
                        }
                        else
                        {
                            $('.abstract-menu-class').removeAttr("style");
                        }
                    // Reload payment section to reflect any changes
                    $.ajax({
                        url: "{{ route('payment-details') }}",
                        headers: { 'X-Requested-With': 'XMLHttpRequest' },
                        success: function (html) {
                            $('#paymentSection').html(html);
                        }
                    });
                    },
                error: function (xhr, status, error) {
                    console.error('Error updating participant:', error, xhr);
                    let errorMsg = 'Error updating participant.';
                    if (xhr.responseJSON) {
                        if (xhr.responseJSON.error) {
                            errorMsg = xhr.responseJSON.error;
                        } else if (xhr.responseJSON.message) {
                            errorMsg = xhr.responseJSON.message;
                        } else if (xhr.responseJSON.errors) {
                            errorMsg = Object.values(xhr.responseJSON.errors).flat().join(', ');
                        }
                    }
                    swal("Error", errorMsg, "error");
                }
            });
        }
        });

        $(document).on('click', '.remove-btn', function () {

           $(this).closest('.ajax-div').remove();
           participantCount--;
           if($(".remove-btn").length==0 || $('.btn-add').length==1)
            $('.btn-add').first().show();

        });


        // Store the delete URL when a delete icon is clicked
        var deleteParticipantHref = null;

        $('table').on('click', '.delete-participant', function(e) {
            deleteParticipantHref = $(this).data('href');
        });
        
        // Single handler for the confirm delete button (uses .off() to prevent stacking)
        $(document).off('click', '.btn_delete_participant').on('click', '.btn_delete_participant', function() {
            if (!deleteParticipantHref) return;
            
            var href = deleteParticipantHref;
            deleteParticipantHref = null; // Clear after use to prevent double-delete
            
                $.ajax({
                    headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') },
                    type: 'DELETE',
                    dataType: 'JSON',
                    url: href,
                    success: function(response) {
                        $('#delete-modal').modal('hide');
                        console.log(response.participantCount);
                        if(response.participantCount <10){
                            $('#collapseDiv').show();
                                if(response.participantCount <9){
                                    $('.btn-add').show();
                                }
                            }
                        if (response.participantCount == 0) {
                            $('.abstract-menu-class').css({
                                'pointer-events': 'none',
                            });
                        } else {
                            $('.abstract-menu-class').css({
                                'pointer-events': '',
                            });
                        }
                        swal("Success", response.success, "success");
                        $participantsTable.draw();

                        $.ajax({
                            url: "{{ route('payment-details') }}",
                            headers: { 'X-Requested-With': 'XMLHttpRequest' },
                            success: function(html) {
                                $('#paymentSection').html(html);
                            },
                            error: function() {
                                $('#paymentSection').html('<p>Error loading content. Please try again later.</p>');
                            }
                        });
                },
                error: function(xhr) {
                    $('#delete-modal').modal('hide');
                    var errorMsg = 'Failed to delete participant.';
                    if (xhr.responseJSON && xhr.responseJSON.success) {
                        errorMsg = xhr.responseJSON.success;
                    }
                    swal("Error", errorMsg, "error");
                }
            });
        });

    });

</script>


