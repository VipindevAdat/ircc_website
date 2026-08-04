@extends('admin.layouts.app')

@section('page-title', 'Registration')
@section('head')
<link rel="stylesheet" href="{{ url("libs/datepicker/daterangepicker.css")}}" type="text/css">
@endsection
@section('content')
<div class="mb-4">
    <nav style="--bs-breadcrumb-divider: '>';" aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item">
                <a href="#">
                    <i class="bi bi-globe2 small me-2"></i> Dashboard
                </a>
            </li>
            <li class="breadcrumb-item active" aria-current="page">Registration Detail</li>
        </ol>
    </nav>
</div>

<div class="row">
       
    <div class="col-lg-8 col-md-12">
        <div class="card mb-4">
            <div class="card-body">
                <div class="mb-5 d-flex align-items-center justify-content-between">
                    <span>Registration No : <a href="#">#{{$registration->registration_number}}</a></span>
                    <div>
                        @if($registration->participants->count())
                        <span class="badge {{ !empty($abstractBadgeSuccess) ? 'bg-success' : 'bg-warning' }}">{{ $abstractStatus }}</span>
                        @else
                        <span class="badge bg-danger">Profile not completed</span>
                        @endif
                        <button class="btn btn-sm btn-outline-primary ms-2" data-bs-toggle="modal" data-bs-target="#editRegistrationModal" title="Edit Registration">
                            <i class="bi bi-pencil-square"></i> Edit
                        </button>
                    </div>
                </div>
                <div class="row mb-5 g-4">
                    <div class="col-md-3 col-sm-6">
                        <p class="fw-bold">Created at</p>
                        {{ Carbon\Carbon::parse($registration->created_at)->format('d-m-Y H:i:s A')}}
                    </div>
                    
                    <div class="col-md-3 col-sm-6">
                        <p class="fw-bold">Contact Name</p>
                        {{$registration->first_name.' '. $registration->last_name}}
                    </div>
                    <div class="col-md-3 col-sm-6">
                        <p class="fw-bold">Email</p>
                        {{$registration->email}}
                    </div>
                    <div class="col-md-3 col-sm-6">
                        <p class="fw-bold">Contact No</p>
                        {{$registration->phone}}
                    </div>
                    <div class="col-md-3 col-sm-6">
                        <p class="fw-bold">Participants Count</p>
                        {{$registration->participants->where('type', '<>', 2)->count()}}
                    </div>
                    <div class="col-md-6 col-sm-6">
                        <p class="fw-bold">Institution Name</p>
                        {{$registration->institution_name}}
                    </div>
                    @if($registration->hear_about_us)
                    <div class="col-md-6 col-sm-6">
                        <p class="fw-bold">How did you hear about us?</p>
                        {{$registration->hear_about_us}}
                    </div>
                    @endif
                </div>
                @if($registration->participants->isNotEmpty())
                    <div class="row g-3 mt-3">
                        <div class="col-md-6">
                            <h5>Participants</h5>
                        </div>
                        <div class="col-md-6 text-right d-flex justify-content-end">
                            <button class="btn" type="button" id="collapseButtonParticipants" 
                                    data-bs-toggle="collapse" data-bs-target="#collapseParticipants" 
                                    aria-expanded="false" aria-controls="collapseParticipants">
                                <i class="bi bi-chevron-down"></i>
                            </button>
                        </div>
                    </div>

                    <div class="row collapse show" id="collapseParticipants">
                        <div class="col-md-12 col-sm-12">
                            <div class="table-responsive" tabindex="1" style="overflow: hidden; outline: none;">
                                <table class="table table-custom mb-0">
                                    <thead>
                                        <tr>
                                            <th>#</th>
                                            <th>First Name</th>
                                            <th>Last Name</th>
                                            <th>Role</th>
                                            <th>Age</th>
                                            <th>Class</th>
                                            <th>Email</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @php $pCount = 0; @endphp
                                        @foreach($registration->participants as $participant)
                                            @if($participant->type != 2)
                                            @php $pCount++; @endphp
                                            <tr>
                                                <td>{{ $pCount }}</td>
                                                <td>{{ $participant->first_name }}</td>
                                                <td>{{ $participant->last_name }}</td>
                                                <td>
                                                    <span class="badge {{ $participant->role == 'adult' || $participant->type == 1 ? 'bg-info' : 'bg-primary' }}">
                                                        {{ $participant->role == 'adult' || $participant->type == 1 ? 'Adult' : 'Student' }}
                                                    </span>
                                                </td>
                                                <td>{{ $participant->age ?? '-' }}</td>
                                                <td>{{ $participant->class ?? '-' }}</td>
                                                <td>{{ $participant->email ?? '-' }}</td>
                                            </tr>
                                            @endif
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                @endif
                @if($registration->participants->where('type',1)->isNotEmpty())
                    <!-- <div class="row g-3 mt-1">
                        <div class="col-md-6">
                            <h5>Guest</h5>
                        </div>
                        <div class="col-md-6 text-end d-flex justify-content-end">
                            <button class="btn" type="button" id="collapseButtonGuest" 
                                    data-bs-toggle="collapse" data-bs-target="#collapseGuest" 
                                    aria-expanded="false" aria-controls="collapseGuest">
                                <i class="bi bi-chevron-down"></i>
                            </button>
                        </div>
                    </div>

                    <div class="row participant-row collapse show" id="collapseGuest">
                        <div class="col-md-12 col-sm-12">
                            <div class="table-responsive" tabindex="1" style="overflow: hidden; outline: none;">
                                <table class="table table-custom mb-0">
                                    <thead>
                                        <tr>
                                            <th>#</th>
                                            <th>First Name</th>
                                            <th>Last Name</th>
                                            <th>Type</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($registration->participants->where('type',1) as $participant)
                                            <tr>
                                                <td>{{ $loop->iteration }}</td>
                                                <td>{{ $participant->first_name }}</td>
                                                <td>{{ $participant->last_name }}</td>
                                                <td>{{ $participant->type==1?'Participant':( $participant->type==2?'Guest':'Register User') }}</td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div> -->
                @endif
            </div>
        </div>
        @if($payments->count())
        <div class="card widget">
    <h5 class="card-header">Payment History</h5>
    <div class="card-body">
        <div class="table-responsive" tabindex="1" style="outline: none;">
            <table class="table table-custom mb-0">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Payment Mode</th>
                        <th>Payment Date</th>
                        <th>Amount Paid</th>
                        <th>Payment Status</th>
                        <th>View</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($payments as $index)
                        @php
                            $status = is_object($index->status) ? $index->status->status : ($index->status ?? 'Failed');
                        @endphp
                        <tr>
                            <td>{{ $loop->iteration }}</td>
                            <td>{{ $index->payment_mode }}</td>
                            <td>{{ $index->payment_mode == 'Direct Bank Transfer' ? Carbon\Carbon::parse($index->payment_date)->format('d-m-y H:m A') : Carbon\Carbon::parse($index->transaction_date)->format('d-m-y H:m A') }}</td>
                            <td>${{ $index->payment_mode == 'Direct Bank Transfer' ? number_format($index->amount) : number_format($index->paid_amount) }}</td>
                            <td style="font-weight:500; {{ $status == 'Completed' ? 'color:green' : 'color:red' }}">
                                {{ $status }}
                            </td>
                            <td>
                                <button class="btn btn-sm view-details" 
                                        data-bs-toggle="modal" 
                                        data-bs-target="#paymentDetailsModal"
                                        data-id="{{ $index->id }}">
                                    <i class="fa fa-eye"></i>
                                </button>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
                
            </table>
        </div>
    </div>
</div>
@endif

<div class="modal fade" id="paymentDetailsModal" tabindex="-1" aria-labelledby="paymentDetailsLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-body">
                <pre id="paymentDetails"></pre>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

    </div>
    <div class="col-lg-4 col-md-12 mt-4 mt-lg-0">
        <div class="card mb-4">
            <div class="card-body">
            <!-- <h6 class="card-title mb-4">Payment</h6> -->
            <div class="row">
            <div class="col-6">
                <h6 class="card-title mb-4">Payment</h6>
            </div>
            <div class="col-6">
            @if($registration->participants->count())
                <span class="badge {{$paymentStatus=='Payment Pending'?'bg-warning':'bg-success'}} text-end">{{$paymentStatus}}</span>
            @endif
            </div>
            </div>
                <div class="row justify-content-center mb-3">
                    <div class="col-4 text-end">Sub Total :</div>
                    <div class="col-4">${{$payment['subTotal']}}</div>
                </div>
                <!-- Registration Fee removed - it's already included in participant fees (role-based pricing) -->
                <!-- <div class="row justify-content-center mb-3">
                    <div class="col-4 text-end">RegisterFee:</div>
                    <div class="col-4">${{$payment['registrationFee']}}</div>
                </div> -->
                <!-- <div class="row justify-content-center mb-3">
                    <div class="col-4 text-end">Shipping :</div>
                    <div class="col-4">Free</div>
                </div>
                <div class="row justify-content-center mb-3">
                    <div class="col-4 text-end">Tax(18%) :</div>
                    <div class="col-4">$273,77</div>
                </div> -->
                <div class="row justify-content-center">
                    <div class="col-6 text-end">
                        <strong>Total :</strong>
                    </div>
                    <div class="col-6">
                        <!-- grandTotal is now the same as subTotal (no separate registration fee) -->
                        <strong>${{$payment['subTotal']}}</strong>
                    </div>
                </div>
                <div class="row justify-content-center">
                    <div class="col-6 text-end">
                      Amount Paid :
                    </div>
                    <div class="col-6">
                        <strong>${{$payment['paidAmount']}}</strong>
                    </div>
                </div>
                <div class="row justify-content-center">
                    <div class="col-6 text-end">
                     Pending Amount :
                    </div>
                    <div class="col-6">
                        <strong>${{$payment['amountPayable']}}</strong>
                    </div>
                </div>
                @if($paymentStatus=='Payment Pending')
                <div class="text-center mt-4">
                        <button class="btn btn-primary" href="javascript:void(0)"  data-bs-toggle="modal" data-bs-target="#paymentModal" title="Payment">Add Payment</button>
                </div>
                @endif
                @if ($errors->any())
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <ul>
                        @foreach ($errors->all() as $error)
                            <li style="list-style-type: none;">{{ $error }}</li>
                        @endforeach
                    </ul>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
                @endif
            </div>
        </div>
        @if(isset($abstracts) && $abstracts->isNotEmpty())
            @foreach($abstracts as $abs)
        <div class="card mb-3">
            <div class="card-body">
            <div class="row">
            <div class="col-6">
            <h6 class="card-title mb-4">Abstract @if($abstracts->count() > 1)<span class="text-muted small">({{ $abs->pid }})</span>@endif</h6>
            </div>
            <div class="col-6 text-end">
            @if($abs->abstractReviewer && $abs->abstractReviewer->review_status)
          @php $status = optional($abs->abstractReviewer->reviewStatus)->status; @endphp
                <span class="badge {{ $status ? ($status=='Approved'?'bg-success':($status=='Rejected'?'bg-danger':'bg-warning')): 'bg-secondary'}}">{{ $status ?: 'Reviewer Not Assigned' }}</span>
            @endif
            <span class="badge bg-{{ (int) $abs->status === 1 ? 'success' : 'secondary' }} ms-1">{{ (int) $abs->status === 1 ? 'Submitted' : 'Draft' }}</span>
            </div>
            </div>
                <div class="row justify-content-center mb-3">
                    <div class="col-6 text-end">Abstract ID :</div>
                    <div class="col-6">
                        <a href="#">#{{ $abs->pid }}</a>
                    </div>
                </div>
                <div class="row justify-content-center mb-3">
                    <div class="col-6 text-end">Title :</div>
                    <div class="col-6">{{ $abs->abstract_title ?? '—' }}</div>
                </div>
                <div class="row justify-content-center mb-3">
                    <div class="col-6 text-end">Category :</div>
                    <div class="col-6">{{ $abs->category ? $abs->category->name : '' }}</div>
                </div>
                <div class="row justify-content-center mb-3">
                    <div class="col-6 text-end">Domain :</div>
                    <div class="col-6">{{ $abs->abstractDomain ? $abs->abstractDomain->domain : '' }}</div>
                </div>
                <div class="text-center mt-4 d-flex flex-wrap gap-2 justify-content-center align-items-center">
                    <a href="{{ route('admin.download.pdf', ['abstractId' => $abs->id]) }}" class="btn btn-outline-primary">Download PDF</a>
                    @if((int) $abs->status === 1)
                        <form method="POST" action="{{ route('admin.abstract.revert-to-draft', $abs) }}" class="d-inline"
                              onsubmit="return confirm('Revert this abstract to draft? The student will be able to edit again. It will disappear from the submitted abstracts list until they submit again. Review scores for this abstract will be cleared.');">
                            @csrf
                            <button type="submit" class="btn btn-outline-warning">Revert to draft</button>
                        </form>
                    @endif
                </div>
            </div>
        </div>
            @endforeach
        @endif
    </div>
</div>
<div class="modal fade" id="paymentModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
        <div class="modal-header">
            <h5 class="modal-title" id="exampleModalLabel">Registration Details</h5>
            <button type="button" class="close" data-bs-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
            </button>
        </div>
        <form method="POST" action="{{route('admin.addPayment')}}" id="confirm-form">
        @csrf
        <div class="modal-body"  id="detailPayment">
        <div  id="amountDiv" class="pb-1">
                <h5>Enter payment details</h5> 
                
                <div class="row">
                <div class="col-md-6">
                    <div class="mb-3">
                        <input type="hidden" name="registration_id" value="{{$registration->id}}">
                        <label class="form-label">Payment Reference</label>
                    <input type="text" name="payment_reference" class="form-control" value="" required/>
                    </div>
                </div>
                    <div class="col-md-6">
                    <div class="mb-3">
                        <label class="form-label">Payment Date</label>
                    <input type="text" name="payment_date" class="form-control payment_date"  required/>
                    </div>
                    </div>
                    <div class="col-md-6">
                    <div class="mb-3">
                        <label class="form-label">Amount</label>
                    <input type="text" name="payment_amount" class="form-control @error('payment_amount') is-invalid @enderror" value="{{ old('payment_amount', $payment['amountPayable']) }}" required/>
                    @error('payment_amount')
                        <div class="invalid-feedback d-block">{{ $message }}</div>
                    @enderror
                    </div>
                    </div>
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label class="form-label">Coupon (optional)</label>
                            <select name="coupon_code" class="form-select @error('coupon_code') is-invalid @enderror">
                                <option value="" {{ old('coupon_code') === null || old('coupon_code') === '' ? 'selected' : '' }}>None</option>
                                @foreach (\App\Support\PaymentCoupon::VALID_CODES as $couponOption)
                                    <option value="{{ $couponOption }}" {{ old('coupon_code') === $couponOption ? 'selected' : '' }}>{{ $couponOption }}</option>
                                @endforeach
                            </select>
                            <small class="text-muted">Same discounts as customer checkout (applied to the amount above).</small>
                            @error('coupon_code')
                                <div class="invalid-feedback d-block">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div> 
               
                </div> 
        </div>
        <div class="modal-footer">
        <button type="submit" class="btn btn-primary" id="confirmVerify" >Confirm</button>
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>

        </div>
        </form>
        </div>
    </div>
    </div>
<!-- Edit Registration Modal -->
<div class="modal fade" id="editRegistrationModal" tabindex="-1" aria-labelledby="editRegistrationLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="editRegistrationLabel">Edit Registration - #{{$registration->registration_number}}</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="editRegistrationForm">
                @csrf
                <input type="hidden" name="registrationId" value="{{$registration->id}}">
                <div class="modal-body">
                    <!-- Registration Details Section -->
                    <h6 class="fw-bold mb-3"><i class="bi bi-person-circle"></i> Registration Details</h6>
                    <div class="row g-3 mb-4">
                        <div class="col-md-6">
                            <label class="form-label">First Name <span class="text-danger">*</span></label>
                            <input type="text" name="first_name" class="form-control" value="{{$registration->first_name}}" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Last Name <span class="text-danger">*</span></label>
                            <input type="text" name="last_name" class="form-control" value="{{$registration->last_name}}" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Email <span class="text-danger">*</span></label>
                            <input type="email" name="email" class="form-control" value="{{$registration->email}}" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Phone <span class="text-danger">*</span></label>
                            <input type="text" name="phone" class="form-control" value="{{$registration->phone}}" required>
                        </div>
                        <div class="col-md-12">
                            <label class="form-label">Institution Name</label>
                            <input type="text" name="institution_name" class="form-control" value="{{$registration->institution_name}}">
                        </div>
                    </div>

                    <hr>

                    <!-- Participants Section -->
                    <div class="d-flex align-items-center justify-content-between mb-3">
                        <h6 class="fw-bold mb-0"><i class="bi bi-people"></i> Participants</h6>
                        <button type="button" class="btn btn-sm btn-success" id="addParticipantRow">
                            <i class="bi bi-plus-circle"></i> Add Participant
                        </button>
                    </div>

                    <div id="participantsContainer">
                        @php $pIdx = 0; @endphp
                        @foreach($registration->participants->where('type', '<>', 2)->values() as $participant)
                        <div class="participant-row card mb-3 p-3 bg-light" data-participant-id="{{$participant->id}}">
                            <div class="d-flex justify-content-between align-items-center mb-2">
                                <span class="fw-bold participant-label">Participant #{{$pIdx + 1}}</span>
                                <button type="button" class="btn btn-sm btn-outline-danger remove-participant" data-id="{{$participant->id}}" title="Remove Participant">
                                    <i class="bi bi-trash"></i>
                                </button>
                            </div>
                            <input type="hidden" name="participants[{{$pIdx}}][id]" value="{{$participant->id}}">
                            <div class="row g-2">
                                <div class="col-md-3">
                                    <label class="form-label">First Name <span class="text-danger">*</span></label>
                                    <input type="text" name="participants[{{$pIdx}}][first_name]" class="form-control form-control-sm" value="{{$participant->first_name}}" required>
                                </div>
                                <div class="col-md-3">
                                    <label class="form-label">Last Name <span class="text-danger">*</span></label>
                                    <input type="text" name="participants[{{$pIdx}}][last_name]" class="form-control form-control-sm" value="{{$participant->last_name}}" required>
                                </div>
                                <div class="col-md-2">
                                    <label class="form-label">Role <span class="text-danger">*</span></label>
                                    <select name="participants[{{$pIdx}}][role]" class="form-select form-select-sm participant-role-select" required>
                                        <option value="student" {{$participant->role == 'student' || $participant->type == 0 ? 'selected' : ''}}>Student</option>
                                        <option value="adult" {{$participant->role == 'adult' || $participant->type == 1 ? 'selected' : ''}}>Adult</option>
                                    </select>
                                </div>
                                <div class="col-md-1">
                                    <label class="form-label">Age</label>
                                    <input type="number" name="participants[{{$pIdx}}][age]" class="form-control form-control-sm" value="{{$participant->age}}" min="1" max="100">
                                </div>
                                <div class="col-md-1">
                                    <label class="form-label">Class</label>
                                    <input type="text" name="participants[{{$pIdx}}][class]" class="form-control form-control-sm" value="{{$participant->class}}">
                                </div>
                                <div class="col-md-2">
                                    <label class="form-label">Email</label>
                                    <input type="email" name="participants[{{$pIdx}}][email]" class="form-control form-control-sm" value="{{$participant->email}}">
                                </div>
                            </div>
                        </div>
                        @php $pIdx++; @endphp
                        @endforeach
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary" id="saveRegistrationBtn">
                        <i class="bi bi-check-circle"></i> Save Changes
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
@section('script')
<script src="{{ url("libs/datepicker/daterangepicker.js")}}"></script>
    <script>
     var date = new Date();
            var today = new Date(date.getFullYear(), date.getMonth(), date.getDate());
            $('.payment_date').daterangepicker({
                    singleDatePicker: true,
                    showDropdowns: true,
                    defaultDate: new Date(),
                    locale: {
                        format: "DD-MM-Y"
                    },

                    });
        @if(Session::has('success_message'))
        $(document).ready( function () {
        Swal.fire({
        icon: 'success',
        title: '{{ Session::get('success_message') }}',
        // text: 'we will notify through email once the adjustment is complete',
        footer: ''
        })
        })
        @endif
        @if(Session::has('error'))
        $(document).ready( function () {
        Swal.fire({
        icon: 'error',
        title: '{{ Session::get('error') }}',
        footer: ''
        })
        })
        @endif
        $(document).ready(function () {
            // Payment detail view
            $('.view-details').on('click', function () {
                var id = $(this).data('id');
                $.ajax({
                    type: 'GET',
                    data: { id: id },
                    dataType: 'html',
                    url: '{{ route('admin.paymentView') }}',
                    success: function (response) {
                        $('#paymentDetailsModal .modal-body').html(response);
                        $('#paymentDetailsModal').modal('show');
                    },
                    error: function (xhr, status, error) {
                        console.log(error);
                    }
                });
            });

            // --- Edit Registration Modal Logic ---
            var participantIndex = {{ $registration->participants->where('type', '<>', 2)->count() }};

            // Add new participant row
            $('#addParticipantRow').on('click', function () {
                var html = `
                <div class="participant-row card mb-3 p-3 bg-light" data-participant-id="new_${participantIndex}">
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <span class="fw-bold participant-label">New Participant</span>
                        <button type="button" class="btn btn-sm btn-outline-danger remove-new-participant" title="Remove">
                            <i class="bi bi-trash"></i>
                        </button>
                    </div>
                    <input type="hidden" name="participants[${participantIndex}][id]" value="">
                    <div class="row g-2">
                        <div class="col-md-3">
                            <label class="form-label">First Name <span class="text-danger">*</span></label>
                            <input type="text" name="participants[${participantIndex}][first_name]" class="form-control form-control-sm" required>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">Last Name <span class="text-danger">*</span></label>
                            <input type="text" name="participants[${participantIndex}][last_name]" class="form-control form-control-sm" required>
                        </div>
                        <div class="col-md-2">
                            <label class="form-label">Role <span class="text-danger">*</span></label>
                            <select name="participants[${participantIndex}][role]" class="form-select form-select-sm participant-role-select" required>
                                <option value="student">Student</option>
                                <option value="adult">Adult</option>
                            </select>
                        </div>
                        <div class="col-md-1">
                            <label class="form-label">Age</label>
                            <input type="number" name="participants[${participantIndex}][age]" class="form-control form-control-sm" min="1" max="100">
                        </div>
                        <div class="col-md-1">
                            <label class="form-label">Class</label>
                            <input type="text" name="participants[${participantIndex}][class]" class="form-control form-control-sm">
                        </div>
                        <div class="col-md-2">
                            <label class="form-label">Email</label>
                            <input type="email" name="participants[${participantIndex}][email]" class="form-control form-control-sm">
                        </div>
                    </div>
                </div>`;
                $('#participantsContainer').append(html);
                participantIndex++;
                renumberParticipants();
            });

            // Remove new (unsaved) participant row
            $(document).on('click', '.remove-new-participant', function () {
                $(this).closest('.participant-row').remove();
                renumberParticipants();
            });

            // Remove existing participant (from DB)
            $(document).on('click', '.remove-participant', function () {
                var btn = $(this);
                var participantId = btn.data('id');
                var row = btn.closest('.participant-row');
                var name = row.find('input[name$="[first_name]"]').val() + ' ' + row.find('input[name$="[last_name]"]').val();

                Swal.fire({
                    title: 'Delete Participant?',
                    text: 'Are you sure you want to delete "' + name.trim() + '"? This action cannot be undone.',
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#d33',
                    cancelButtonColor: '#6c757d',
                    confirmButtonText: 'Yes, delete'
                }).then(function (result) {
                    if (result.isConfirmed) {
                        $.ajax({
                            type: 'POST',
                            url: '{{ route("admin.deleteParticipant") }}',
                            data: { id: participantId },
                            success: function (response) {
                                if (response.success) {
                                    row.slideUp(300, function () {
                                        $(this).remove();
                                        renumberParticipants();
                                    });
                                    Swal.fire({ icon: 'success', title: response.message, timer: 1500, showConfirmButton: false });
                                } else {
                                    Swal.fire({ icon: 'error', title: 'Error', text: response.message });
                                }
                            },
                            error: function (xhr) {
                                Swal.fire({ icon: 'error', title: 'Error', text: 'Failed to delete participant.' });
                            }
                        });
                    }
                });
            });

            // Submit edit form via AJAX
            $('#editRegistrationForm').on('submit', function (e) {
                e.preventDefault();
                var btn = $('#saveRegistrationBtn');
                btn.prop('disabled', true).html('<span class="spinner-border spinner-border-sm"></span> Saving...');

                $.ajax({
                    type: 'POST',
                    url: '{{ route("admin.updateRegistration") }}',
                    data: $(this).serialize(),
                    success: function (response) {
                        if (response.success) {
                            Swal.fire({
                                icon: 'success',
                                title: response.message,
                                timer: 1500,
                                showConfirmButton: false
                            }).then(function () {
                                location.reload();
                            });
                        } else {
                            btn.prop('disabled', false).html('<i class="bi bi-check-circle"></i> Save Changes');
                            Swal.fire({ icon: 'error', title: 'Error', text: response.message });
                        }
                    },
                    error: function (xhr) {
                        btn.prop('disabled', false).html('<i class="bi bi-check-circle"></i> Save Changes');
                        var msg = 'Failed to update registration.';
                        if (xhr.responseJSON && xhr.responseJSON.message) {
                            msg = xhr.responseJSON.message;
                        }
                        Swal.fire({ icon: 'error', title: 'Error', text: msg });
                    }
                });
            });

            // Re-number participant labels
            function renumberParticipants() {
                $('#participantsContainer .participant-row').each(function (i) {
                    $(this).find('.participant-label').text('Participant #' + (i + 1));
                });
            }
        });

    </script>
@endsection