@extends('layouts.app')

@section('head')

<link rel="stylesheet" href="{{ url('libs/dataTable/datatables.min.css') }}" type="text/css">
<style>
    #loading {
    background: url('../front/images/loading.gif') no-repeat center center;
    position: fixed;
    width: 100%;
    height: 100vh;
    z-index: 999;
}
.menu-item{
    display: flex;
    justify-content: center;
}
.nav-item .active {
    background-color: #f15b43;
    /* background-color: #da988d; */
    color: white; 
    border-radius: 25px;
    width: 100%;
    padding: 13px 0; 
    display: inline-block;
    text-align: center; 
    font-weight: 500;
}
</style>
@endsection

@section('content')
<div id="loading" style="display: none;"></div>
    <main id="content" class="site-main bg-light-grey">
        <section class="inner-banner-wrap">
            <div class="inner-baner-container" style="background-image: url('../front/images/inner-banner-img-1.jpg');">
                <div class="container">
                    <div class="inner-banner-content">
                        <h1 class="inner-title">Dashboard</h1>
                    </div>
                </div>
            </div>
        </section>

        @if ($emailVerificationMessage)
            <div class="alert alert-success">
                {{ $emailVerificationMessage }}
            </div>
        @endif

        @if ($errors->any())
            <div class="alert alert-dismissible fade show" role="alert" style="background-color: #fef0ed; border-left: 4px solid #F15B43; border-radius: 4px; color: #333;">
                <ul style="margin-bottom: 0;">
                    @foreach ($errors->all() as $error)
                        <li style="list-style-type: none; color: #d94a35;">{{ $error }}</li>
                    @endforeach
                </ul>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        <div class="checkout-section">
            <div class="container-fluid">
                <div class="row">
                    <div class="col-md-3">
                        <aside class="sidebar">
                            <div class="widget-bg widget-table-summary">
                                <h4 class="bg-title">Dashboard</h4>
                                <!-- <div class="card">
                                    <div class="card-body">
                                        <ul class="nav nav-pills flex-column gap-2">
                                            <li class="nav-item">
                                                <a class="nav-link " href="./buyer-dashboard.html">Dashboard</a>
                                            </li>
                                            <li class="nav-item">
                                                <a class="nav-link  active " href="./buyer-orders.html">My Orders</a>
                                            </li>
                                            <li class="nav-item">
                                                <a class="nav-link " href="./buyer-addresses.html">My Addresses</a>
                                            </li>
                                            <li class="nav-item">
                                                <a class="nav-link " href="./buyer-wishlist.html">Wishlist</a>
                                            </li>
                                        </ul>
                                    </div>
                                </div> -->
                                <ul class="nav nav-pills flex-column gap-2">
                                    <li class="nav-item ">
                                        <a href="javascript:void(0);"
                                           class="dynamic-link menu-item link-dashboard active" data-class="link-dashboard"
                                           data-url="{{ route('viewDashboard') }}">
                                            Home
                                        </a>
                                        <hr />
                                    </li>
                                    <li  class="nav-item">
                                        <a href="javascript:void(0);"
                                           class="dynamic-link menu-item link-participant" data-class="link-participant"
                                           data-url="{{route('participant.index')}}">
                                            Add Participants
                                        </a>
                                        <hr />
                                    </li>
                                    @php
                                    $paymentStatus=$settings->where('code','payment_status')->first()?$settings->where('code','payment_status')->first()->value:'';
                                    $abstractStatus=$settings->where('code','abstract_status')->first()?$settings->where('code','abstract_status')->first()->value:'';
                                    $participant_delete_setting = optional($settings->where('code','participant_delete_status')->first())->value;
                                    $participant_delete = $participant_delete ?? ($participant_delete_setting !== null ? (int)$participant_delete_setting : 1);
                                    // Check if user has at least one completed payment (payment_status = 3)
                                    $hasCompletedPayment = \App\Models\Payment::where('registration_id', $user->id)
                                        ->where('payment_status', 3)
                                        ->exists();
                                    $isFullyPaidForAbstractNav = isset($payment_summary) && (float) str_replace(',', '', $payment_summary['amountPayable']) == 0;
                                    $abstractNavVisible = \App\Models\AbstractSubmission::dashboardAbstractNavVisible(
                                        $user->id,
                                        (bool) $abstractStatus,
                                        $hasCompletedPayment && $isFullyPaidForAbstractNav
                                    );
                                    $abstractNavLabel = \App\Models\AbstractSubmission::dashboardAbstractNavLabel($user->id);
                                    @endphp
                                    @if($paymentStatus )
                                    <li class="nav-item payment-menu-class">
                                        <a href="javascript:void(0);"
                                           class="dynamic-link menu-item link-payment " data-class="link-payment"
                                           data-url="{{ route('payment.index') }}">
                                            Make Payment
                                        </a>
                                        <hr />
                                    </li>
                                    @endif
                                    @php
                                        // Abstract upload only active when fully paid (amountPayable == 0)
                                        $isFullyPaid = $isFullyPaidForAbstractNav;
                                        // Students may have type 0, type null (legacy), or role 'student'; contains('type',0) misses null.
                                        $nonDeletedParticipants = $participants->filter(fn ($p) => (int) ($p->type ?? 0) !== 2);
                                        $hasStudentParticipant = $nonDeletedParticipants->contains(function ($p) {
                                            if (($p->role ?? '') === 'student') {
                                                return true;
                                            }
                                            $t = $p->type;

                                            return $t === null || (int) $t === 0;
                                        });
                                    @endphp
                                    @if($abstractNavVisible && $hasCompletedPayment && $isFullyPaid)
                                        <li class="nav-item abstract-menu-class" 
                                            style="{{ $nonDeletedParticipants->isEmpty() || ! $hasStudentParticipant ? 'pointer-events:none' : '' }}">
                                            <a href="javascript:void(0);"
                                            class="dynamic-link menu-item link-abstract" data-class="link-abstract"
                                            data-url="{{route('abstract.create')}}">
                                                {{ $abstractNavLabel }}
                                            </a>
                                            <hr />
                                        </li>
                                    @endif                                
                                    <li  class="nav-item ">
                                        <a href="javascript:;" onclick="event.preventDefault();document.getElementById('logout-form').submit();" class="menu-item">
                                        <i class="bi bi-box-arrow-right dropdown-item-icon"></i> Logout
                                        </a>
                                        <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
                                            @csrf
                                        </form>

                                    </li>
                                </ul>
                            </div>
                        </aside>
                    </div>
                    <div class="col-md-9 right-sidebar mb-5">
                        <div  id="dynamic-content">
                        <div class="alert alert-success alert-dismissible fade show success-message" role="alert" id="successDiv" style="display:none">
                                  <span id="success-message" class="success-message"></span>
                                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                            </div>
                            @if (session('error'))
                                <div class="alert alert-dismissible fade show success-message" role="alert" style="background-color: #fef0ed; border-left: 4px solid #F15B43; border-radius: 4px; color: #d94a35;">
                                    {{ session('error') }}
                                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                                </div>
                            @endif
                            @if (session('success'))
                                <div class="alert alert-success alert-dismissible fade show success-message" role="alert">
                                    {{ session('success') }}
                                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                                </div>
                            @endif
                            <div class="donate-section">
                                <div class="container">
                                <div class="donate-detais form-inner">
                                @if(!empty($paymentApprovePending) && !session('payment_success'))
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
                                <div class="donate-form" id="dynamic-content">
                                
                                <div class="form-inner">
                                    @if(session('givebutter_payment_initiated'))
                                    <div class="row g-3 mt-2">
                                        <div class="col-md-12">
                                            <div class="alert alert-info" style="text-align: center;">
                                                <h4><i class="fa fa-info-circle"></i> Payment in Progress</h4>
                                                <p><strong>Important:</strong> After completing your payment on Givebutter, please return to this dashboard.</p>
                                                <p>Your payment status will be automatically updated. You can also click the button below to check your payment status.</p>
                                                <div class="mt-3">
                                                    <a href="{{ route('accounts.dashboard') }}" class="btn btn-primary">
                                                        <i class="fa fa-refresh"></i> Refresh Payment Status
                                                    </a>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    @endif
                                    @if(session('payment_success'))
                                    <div class="row g-3 mt-2">
                                        <div class="col-md-12">
                                            <div class="contact-detail-container item-center" style="align-items: center;text-align: center;">
                                                <div class="section-heading">
                                                    <div class="sub-title">Success</div>
                                                    <img src="{{ asset('front/images/success.png') }}" class="mb-2" alt="success" style="height: 75px;">
                                                    <h2>{!!session('success_title')!!}</h2>
                                                    <div style="color:#364F75;font-weight: 500;padding-bottom: 20px;">
                                                    {!!session('payment_success')!!}
                                                    </div>
                                                    @if(session('show_dashboard_button'))
                                                    <div class="d-flex justify-content-center mt-3 mb-3">
                                                        <a href="{{ route('accounts.dashboard') }}" class="btn btn-primary" style="padding: 10px 30px; font-size: 16px;">
                                                            <i class="fa fa-arrow-left"></i> Back to Dashboard
                                                        </a>
                                                    </div>
                                                    <script>
                                                        // Auto-redirect to dashboard after 5 seconds
                                                        setTimeout(function() {
                                                            window.location.href = "{{ route('accounts.dashboard') }}";
                                                        }, 5000);
                                                    </script>
                                                    @endif
                                                </div>
                                                
                                            </div>
                                        </div>
                                    </div> 
                                    @else
                            
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
                                    <div class="row g-3 mt-2">
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
                                    <!-- <div class="col-md-12" style="font-size: 16px; color: #333;">
                                    <p>
                                        Once you add all participants, proceed with payment.<br>
                                        <strong>Pricing:</strong> Student Participants: ${{ $payment_summary['studentPrice'] ?? '150.00' }} | Adult Attendees: ${{ $payment_summary['adultPrice'] ?? '50.00' }}
                                   </p>
                                    </div> -->
                                    <div class="col-md-12 text-center">
                                    <p> 
                                        Once you add all participants, proceed with payment.<br>
                                        <strong>Pricing:</strong> Student Participants: ${{ $payment_summary['studentPrice'] ?? '150.00' }} | Adult Attendees: ${{ $payment_summary['adultPrice'] ?? '50.00' }}
                                        <br>
                                       
                                    </p>
                                               
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
                                                    @if($participant->type != 2)
                                                    <tr>
                                                        <td>{{$key+1}}</td>
                                                        <td>{{$participant->first_name}}</td>
                                                        <td>{{$participant->last_name}}</td>
                                                        <td>{{$participant->type==0?'No':'Yes'}}</td>
                                                        <td>{{$participant->class}}</td>
                                                    </tr>
                                                    @endif
                                                    @endforeach
                                                    </tbody>
                                                </table>
                                            @endif

                                    </div>
                                    </div>
                                    </div>
                                    <div class="form-inner" id="homePaymentSection">
                                  
                                    <div class="row g-3">
                                            <div class="col-md-6">
                                                <h4>Payment Details</h4>
                                            </div>
                                            <div class="col-md-6 text-right">
                                                <button class="btn" type="button" id="collapseButton" style="color:#F15B43;" data-bs-toggle="collapse" data-bs-target="#collapsePayment" aria-expanded="false" aria-controls="collapseExample">
                                                <i class="fas fa-chevron-down"></i>
                                                </button>

                                            </div>

                                            @if($payment_summary['amountPayable'] == 0)
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
                                            <div class="row d-flex justify-content-center mt-3">
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
                                            @endphp
                                            @if($hasCompletedPayment && $isFullyPaid && $abstractNavVisible)
                                            <div class="col-md-12 d-flex justify-content-center align-items-center mt-3 mb-3">
                                                <a href="javascript:void(0);" class="dynamic-link button-round reg_register"
                                                    data-url="{{ route('abstract.create') }}"  data-class="link-abstract"
                                                    >{{ $abstractNavLabel }}</a>
                                            </div>
                                            @endif

                                        @if($payment_summary['amountPayable']>0)
                                            <div class="col-md-12 d-flex justify-content-center">
                                                <h5 style="font-weight: bold;">
                                                    Payment Pending : ${{$payment_summary['amountPayable'] }}
                                                </h5>
                                            </div>
                                            <div class="d-flex justify-content-center align-items-center mt-3">
                                                <a href="javascript:void(0);" class="dynamic-link button-round reg_register make_payment"
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
                                                            <td>${{ $payment->payment_mode == 'Direct Bank Transfer' ?number_format($payment->amount,2): number_format($payment->paid_amount,2)}}</td>
                                                            <td style="font-weight:500;{{$status=='Completed'?'color:green':'color:red'}}">{{$payment->status?$payment->status->status:'Pending' }}</td>
                                                            <td>{{ $payment->created_at }}</td>
                                                        </tr>
                                                    @endforeach
                                                    </tbody>
                                                </table>
                                            </div>

                                        @endif
                                    </div>
                                    @endif
                                  
                                </div>
                                </div>
                                </div>
                                </div>
                             </div>
                    </div>
                </div>
            </div>
        </div>
        <div id="loading" style="display: none;"></div>
    </main>


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

                        <button type="button" class="btn btn-sm btn-danger" data-bs-dismiss="modal">Close</button>
                        <button type="button" class="btn btn-sm btn-danger btn_delete_participant" data-loading-text="">Delete</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@section('script')
    <script src="{{ url("front/js/sweet-alert.js")}}"></script>
    <script src="{{ url('libs/dataTable/datatables.min.js') }}"></script>
    <script src="{{ url('libs/form-repeater/repeater.min.js') }}"></script>
    <script>
    /**
     * Run script tags from AJAX-loaded HTML in order so external libs (e.g. form repeater)
     * load before inline scripts that call into them.
     */
    function runDynamicContentScripts(scriptsArray) {
        return scriptsArray.reduce(function (chain, script) {
            return chain.then(function () {
                if (script.src) {
                    var src = script.getAttribute('src') || '';
                    if (src.indexOf('repeater') !== -1 && typeof jQuery !== 'undefined' && jQuery.fn && typeof jQuery.fn.repeater === 'function') {
                        return Promise.resolve();
                    }
                    return new Promise(function (resolve) {
                        var el = document.createElement('script');
                        el.src = src;
                        el.onload = resolve;
                        el.onerror = function () {
                            console.warn('Script failed to load:', src);
                            resolve();
                        };
                        document.head.appendChild(el);
                    });
                }
                var scriptContent = (script.textContent || script.innerHTML || '').trim();
                if (!scriptContent) {
                    return Promise.resolve();
                }
                return new Promise(function (resolve) {
                    try {
                        var newScript = document.createElement('script');
                        newScript.textContent = scriptContent;
                        document.body.appendChild(newScript);
                        setTimeout(function () {
                            if (newScript.parentNode) {
                                newScript.parentNode.removeChild(newScript);
                            }
                            resolve();
                        }, 0);
                    } catch (e) {
                        console.warn('Error executing inline script:', e);
                        resolve();
                    }
                });
            });
        }, Promise.resolve());
    }

    $(document).ready(function () {
        $("html, body").animate({ scrollTop: 400 }, 1000);
  
        $('.make_payment').on('click', function (e) {
            $("html, body").animate({ scrollTop: $(document).height()-1200 }, 1000);
        });
        
        // Delegated submit handler for abstract form (loaded via AJAX)
        $(document).off('submit', '#abstract-form').on('submit', '#abstract-form', function (event) {
            event.preventDefault();
            event.stopPropagation();

            var $form = $(this);
            var isValid = true;

            function normalizedAbstractText(text) {
                return $.trim(String(text || '').replace(/\s+/g, ' '));
            }

            function abstractWordCount(text) {
                var n = normalizedAbstractText(text);
                if (!n) return 0;
                return n.split(/\s+/).filter(Boolean).length;
            }

            var abstractWordLimitMsg = 'The abstract may contain at most 250 words (not characters). Line breaks do not add words—please shorten your text.';

            // Client-side validation
            if ($("#abstract_title").val() && $("#abstract_title").val().trim() === "") {
                $("#title_error").show();
                isValid = false;
            } else {
                $("#title_error").hide();
            }
            if ($("#category_id").val() === null) {
                $("#category_error").show();
                isValid = false;
            } else {
                $("#category_error").hide();
            }
            if ($("#abstract_domain").val() === null) {
                $("#domain_error").show();
                isValid = false;
            } else {
                $("#domain_error").hide();
            }
            if ($("input[name='student_authors[]']:checked").length === 0) {
                $("#authors_error").show();
                isValid = false;
            } else {
                $("#authors_error").hide();
            }
            var emailPattern = /^[a-zA-Z0-9._-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,4}$/;
            if ($("#correspondent_author").val() && !emailPattern.test($("#correspondent_author").val().trim())) {
                $("#email_error").show();
                isValid = false;
            } else {
                $("#email_error").hide();
            }
            if ($("#correspondent_author_name").val() && $("#correspondent_author_name").val().trim() === "") {
                $("#correspondent_author_name_error").show();
                isValid = false;
            } else {
                $("#correspondent_author_name_error").hide();
            }
            var abstractRaw = $("#abstractDescription").val();
            var abstractWords = abstractWordCount(abstractRaw);
            if (abstractRaw && abstractWords > 250) {
                $("#word_error").text(abstractWordLimitMsg).show();
                swal('Abstract too long', abstractWordLimitMsg, 'error');
                return false;
            }
            if ($("#word_error").length) {
                $("#word_error").hide();
            }
            if ($("#abstractDescription").val() === '' || normalizedAbstractText($("#abstractDescription").val()) === '') {
                $("#content_error").show();
                isValid = false;
            } else {
                $("#content_error").hide();
            }

            // Validate references - check all repeater textareas
            var hasReference = false;
            $('#abstractReferenceDiv [data-repeater-item] textarea').each(function() {
                if ($(this).val().trim() !== '') {
                    hasReference = true;
                    return false; // break
                }
            });
            if (!hasReference) {
                $("#reference_error").show();
                isValid = false;
            } else {
                $("#reference_error").hide();
            }

            if (isValid) {
                var url = $form.attr('action');
                $('#loading').show();
                $.ajax({
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content'),
                        'X-Requested-With': 'XMLHttpRequest',
                        'Accept': 'application/json'
                    },
                    type: 'POST',
                    url: url,
                    data: new FormData($form[0]),
                    dataType: 'json',
                    contentType: false,
                    cache: false,
                    processData: false,
                    success: function (response) {
                        $('#loading').hide();
                        if (response.error == 0) {
                            swal({
                                title: "Success",
                                text: response.success,
                                icon: "success",
                            }).then(() => {
                                if (response.reload || !response.html) {
                                    location.reload();
                                    return;
                                }
                                if (response.html) {
                                    const dynamicContent = $('#dynamic-content');
                                    if (dynamicContent.length) {
                                        const tempDiv = document.createElement('div');
                                        tempDiv.innerHTML = response.html;
                                        const scripts = tempDiv.querySelectorAll('script');
                                        const scriptsArray = Array.from(scripts);
                                        scriptsArray.forEach(script => script.remove());
                                        dynamicContent.html(tempDiv.innerHTML);
                                        runDynamicContentScripts(scriptsArray).then(function () {
                                            $('.dynamic-link').removeClass('active');
                                            $('.link-abstract').addClass('active');
                                        });
                                    } else {
                                        location.reload();
                                    }
                                } else {
                                    location.reload();
                                }
                            });
                        } else {
                            swal("Error", response.message || "An error occurred", "error");
                        }
                    },
                    error: function (xhr, textStatus, err) {
                        $('#loading').hide();
                        console.error('abstract-form ajax error', textStatus, err, xhr.status, xhr.responseText && xhr.responseText.slice(0, 500));
                        var errorMsg = 'An error occurred. Please try again.';
                        if (xhr.responseJSON) {
                            var errs = xhr.responseJSON.errors;
                            if (errs && errs.abstract_content && errs.abstract_content[0]) {
                                errorMsg = errs.abstract_content[0];
                            } else if (xhr.responseJSON.message) {
                                errorMsg = xhr.responseJSON.message;
                            }
                            if (errs) {
                                jQuery.each(errs, function(index, item) {
                                    if(index != 'student_authors[]')
                                    $('.' + index + '1').text(item).show();
                                });
                            }
                        }
                        swal("Error", errorMsg, "error");
                    }
                });
            } else {
                swal("Error", "Please fill in all required fields, or fix the errors shown next to the form.", "error");
            }
            return false;
        });
        
        $(document).off('click', '.dynamic-link').on('click', '.dynamic-link', function (e) {
            e.preventDefault();

            const dynamicContent = $('#dynamic-content');

            $('#loading').show();

            $('.dynamic-link').removeClass('active');
            $('.'+$(this).data('class')).addClass('active');
            const url = $(this).data('url');
            $.ajax({
                url: url,
                headers: { 'X-Requested-With': 'XMLHttpRequest' },
                success: function (html) {
                    $('#loading').hide();
                    
                    // Extract and execute scripts separately to ensure they run
                    try {
                        // Use a temporary container to parse the HTML
                        const tempDiv = document.createElement('div');
                        tempDiv.innerHTML = html;
                        
                        // Get all script tags
                        const scripts = tempDiv.querySelectorAll('script');
                        const scriptsArray = Array.from(scripts);
                        
                        // Remove scripts from the HTML before inserting
                        scriptsArray.forEach(script => script.remove());
                        
                        // Insert the HTML without scripts
                        dynamicContent.html(tempDiv.innerHTML);

                        runDynamicContentScripts(scriptsArray).then(function () {
                        console.log('=== Dashboard content loaded and scripts executed ===');
                        console.log('Scripts found and executed:', scriptsArray.length);

                        // Keep RISE150 client discount in sync with server (same math as amount payable)
                        $.ajax({
                            url: '{{ route('payment.summary') }}',
                            method: 'GET',
                            headers: { 'X-Requested-With': 'XMLHttpRequest' },
                            success: function (data) {
                                if (data.success && typeof data.rise150_student_discount !== 'undefined') {
                                    window.IRCC_RISE150_STUDENT_DISCOUNT = parseFloat(data.rise150_student_discount) || 0;
                                }
                                if (data.success && data.payment_summary) {
                                    var ps = data.payment_summary;
                                    window.IRCC_STUDENT_AMOUNT_PAYABLE = parseFloat(String(ps.studentAmountPayable || '0').replace(/,/g, '')) || 0;
                                    window.IRCC_ADULT_AMOUNT_PAYABLE = parseFloat(String(ps.adultAmountPayable || '0').replace(/,/g, '')) || 0;
                                }
                            }
                        });
                        
                        // Test if scripts executed - check for payment function
                        setTimeout(function() {
                            console.log('Testing payment function availability:', typeof window.openPaymentPopupInWindow);
                            if (typeof window.openPaymentPopupInWindow === 'function') {
                                console.log('✓ Payment function is available');
                            } else {
                                console.warn('✗ Payment function NOT available - scripts may not have executed');
                            }
                            
                            // Attach event handlers directly if they're not already attached
                            // This ensures the modal opens even if scripts in the loaded content fail
                            $(document).off('click', '#payment-form button[type="submit"]').on('click', '#payment-form button[type="submit"]', function(e) {
                                const selectedMode = $('input[name="payment_mode"]:checked').val();
                                console.log('=== Submit button clicked (from main dashboard handler) ===');
                                console.log('Selected mode:', selectedMode);
                                
                                if (selectedMode === 'Online Transaction') {
                                    e.preventDefault();
                                    e.stopPropagation();
                                    e.stopImmediatePropagation();
                                    
                                    console.log('=== Opening payment iframe modal ===');
                                    
                                    if (typeof window.openPaymentPopupInWindow === 'function') {
                                        window.openPaymentPopupInWindow();
                                    } else {
                                        console.error('Payment function not available');
                                        alert('Payment function not available. Please refresh the page.');
                                    }
                                    
                                    return false;
                                }
                            });
                            
                            console.log('✓ Event handlers attached from main dashboard');
                            
                            // Also attach waiver modal handler after AJAX load
                            $(document).off('click', '#waiverModalLink').on('click', '#waiverModalLink', function(e) {
                                e.preventDefault();
                                e.stopPropagation();
                                console.log('Waiver modal link clicked (from main dashboard)');
                                const modal = $('#waiverModal');
                                if (modal.length) {
                                    console.log('Modal found, opening...');
                                    // Try Bootstrap 5 first, then fallback to jQuery
                                    if (typeof bootstrap !== 'undefined' && bootstrap.Modal) {
                                        const bsModal = new bootstrap.Modal(modal[0]);
                                        bsModal.show();
                                    } else {
                                        modal.modal('show');
                                    }
                                } else {
                                    console.error('Waiver modal not found in DOM');
                                    alert('Waiver form is loading. Please try again in a moment.');
                                }
                            });
                            
                            // Check if modal exists
                            if ($('#waiverModal').length) {
                                console.log('✓ Waiver modal found in DOM');
                            } else {
                                console.warn('⚠ Waiver modal not found in loaded content');
                            }
                            
                            // Also attach waiver submit button handler after AJAX load
                            $(document).off('click', '#waiver-submit-btn').on('click', '#waiver-submit-btn', function(e) {
                                e.preventDefault();
                                e.stopPropagation();
                                console.log('Submit waiver button clicked (from main dashboard)');
                                
                                const waiverAcknowledgment = $('#waiver_acknowledgment');
                                const waiverSignature = $('#waiver_signature');
                                const waiverDate = $('#waiver_date');
                                const waiverUnder12 = $('#waiver_under_12');
                                const waiver12OrOlder = $('#waiver_12_or_older');
                                
                                // Validate age checkbox - MUST check the one with data-required="true"
                                const waiverUnder12Required = waiverUnder12.attr('data-required') === 'true';
                                const waiver12OrOlderRequired = waiver12OrOlder.attr('data-required') === 'true';
                                
                                console.log('Age validation (from main dashboard):', {
                                    waiverUnder12Required: waiverUnder12Required,
                                    waiverUnder12Checked: waiverUnder12.is(':checked'),
                                    waiver12OrOlderRequired: waiver12OrOlderRequired,
                                    waiver12OrOlderChecked: waiver12OrOlder.is(':checked')
                                });
                                
                                // If "UNDER 12" checkbox has data-required="true", it MUST be checked
                                if (waiverUnder12Required) {
                                    if (!waiverUnder12.is(':checked')) {
                                        alert('You have participants under 12 years of age. Please check "At least one participant is UNDER 12 years of age – Adult registration is MANDATORY."');
                                        waiverUnder12.focus();
                                        return false;
                                    }
                                }
                                
                                // If "12 or older" checkbox has data-required="true", it MUST be checked
                                if (waiver12OrOlderRequired) {
                                    if (!waiver12OrOlder.is(':checked')) {
                                        alert('Please check "All participants are 12 YEARS OR OLDER – Adult registration is optional."');
                                        waiver12OrOlder.focus();
                                        return false;
                                    }
                                }
                                
                                // Fallback: at least one age checkbox must be checked
                                if (!waiverUnder12.is(':checked') && !waiver12OrOlder.is(':checked')) {
                                    alert('Please check at least one age requirement box (UNDER 12 or 12 YEARS OR OLDER).');
                                    waiverUnder12.focus();
                                    return false;
                                }
                                
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
                                
                                // Mark waiver as completed
                                $('#waiver-status').show();
                                $('#waiverModalLink').hide();
                                
                                // Sync waiver data to hidden fields (if function exists)
                                if (typeof syncWaiverDataToForm === 'function') {
                                    syncWaiverDataToForm();
                                } else {
                                    // Manual sync
                                    $('#waiver_acknowledgment_hidden').val(waiverAcknowledgment.is(':checked') ? '1' : '0');
                                    $('#waiver_signature_hidden').val(waiverSignature.val() || '');
                                    $('#waiver_date_hidden').val(waiverDate.val() || '');
                                    $('#waiver_under_12_hidden').val($('#waiver_under_12').is(':checked') ? '1' : '0');
                                    $('#waiver_12_or_older_hidden').val($('#waiver_12_or_older').is(':checked') ? '1' : '0');
                                }
                                
                                // Close modal
                                const modal = $('#waiverModal');
                                if (typeof bootstrap !== 'undefined' && bootstrap.Modal) {
                                    const bsModal = bootstrap.Modal.getInstance(modal[0]);
                                    if (bsModal) {
                                        bsModal.hide();
                                    } else {
                                        modal.modal('hide');
                                    }
                                } else {
                                    modal.modal('hide');
                                }
                                
                                alert('Waiver form submitted successfully. You can now proceed with payment.');
                                console.log('Waiver submitted successfully');
                            });
                            
                            console.log('✓ Waiver modal handler attached from main dashboard');
                            console.log('✓ Waiver submit button handler attached from main dashboard');
                        }, 500);
                        
                        // Widget is already in the loaded HTML, Givebutter should auto-detect it
                        // Just trigger payment mode change if Online Transaction is selected
                        setTimeout(function() {
                            const onlineRadio = document.querySelector('input[name="payment_mode"][value="Online Transaction"]');
                            if (onlineRadio && onlineRadio.checked) {
                                $(onlineRadio).trigger('change');
                            }
                        }, 300);
                        });
                    } catch (error) {
                        console.error('Error loading dynamic content:', error);
                        dynamicContent.html('<p>Error loading content. Please refresh the page.</p>');
                    }
                },
                error: function () {
                    $('#loading').hide();
                    dynamicContent.html('<p>Error loading content. Please try again later.</p>');
                }
            });
        });

        // Handle participant deletion
        var deleteHref = null;
        $('table').on('click', '.delete-participant', function(e) {
            e.preventDefault();
            deleteHref = $(this).data('href');
            $('#deleteId').val($(this).data('id'));
        });

        $('.btn_delete_participant').off().on('click', function() {
            if (!deleteHref) return;
            
            $.ajax({
                headers: { 
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content'),
                    'X-Requested-With': 'XMLHttpRequest'
                },
                type: 'DELETE',
                dataType: 'JSON',
                url: deleteHref,
                success: function(response) {
                    $('#delete-modal').modal('hide');
                    // Show success message
                    if (typeof swal !== 'undefined') {
                        swal("Success", response.success, "success");
                    } else {
                        alert(response.success);
                    }
                    // Reload the page to refresh participant list
                    location.reload();
                },
                error: function(xhr) {
                    $('#delete-modal').modal('hide');
                    deleteHref = null; // Clear to prevent re-firing
                    let errorMsg = 'Failed to delete participant';
                    if (xhr.responseJSON && xhr.responseJSON.success) {
                        errorMsg = xhr.responseJSON.success;
                    } else if (xhr.responseJSON && xhr.responseJSON.message) {
                        errorMsg = xhr.responseJSON.message;
                    }
                    if (typeof swal !== 'undefined') {
                        swal("Error", errorMsg, "error");
                    } else {
                        alert(errorMsg);
                    }
                }
            });
        });
    });

    // ========== COUPON CODE HANDLER (defined in parent page so it always works for AJAX-loaded content) ==========
    window.IRCC_RISE150_STUDENT_DISCOUNT = {{ number_format((float) ($rise150_student_discount ?? 0), 2, '.', '') }};
    window.IRCC_RISE150_LABEL = @json(\App\Support\PaymentCoupon::RISE150_LABEL);
    @php
        $__sIdx = isset($payment_summary) ? (float) str_replace(',', '', $payment_summary['studentAmountPayable'] ?? '0') : 0.0;
        $__aIdx = isset($payment_summary) ? (float) str_replace(',', '', $payment_summary['adultAmountPayable'] ?? '0') : 0.0;
    @endphp
    window.IRCC_STUDENT_AMOUNT_PAYABLE = {{ json_encode(round($__sIdx, 2)) }};
    window.IRCC_ADULT_AMOUNT_PAYABLE = {{ json_encode(round($__aIdx, 2)) }};
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
    // Define coupon codes globally
    window.couponCodes = {
        'Save10': { discount: 10, type: 'percent' },
        'Save5': { discount: 5, type: 'percent' },
        'Save25': { discount: 25, type: 'percent' },
        'RISE150': { type: 'rise_student' },
        'LIFO10': { discount: 10, type: 'percent' },
        'IRCC1': { type: 'fixed_total', finalAmount: 1 }
    };

    // Universal coupon apply function
    window.applyCouponCode = function(inputId, messageId, discountId, amountId, payableId) {
        var input = document.getElementById(inputId);
        if (!input) {
            console.error('Coupon input not found:', inputId);
            return false;
        }

        var code = (input.value || '').trim().toUpperCase();
        if (!code) {
            $('#' + messageId).html('<span style="color:#dc3545;">Please enter a coupon code.</span>');
            return false;
        }

        // Normalize coupon code
        var normalized = code;
        if (code === 'SAVE10') normalized = 'Save10';
        else if (code === 'SAVE5') normalized = 'Save5';
        else if (code === 'SAVE25') normalized = 'Save25';
        else if (code === 'RISE150') normalized = 'RISE150';
        else if (code === 'LIFO10') normalized = 'LIFO10';
        else if (code === 'IRCC1') normalized = 'IRCC1';

        var coupon = window.couponCodes[normalized];
        if (!coupon) {
            $('#' + messageId).html('<span style="color:#dc3545;">Invalid coupon code. Please check and try again.</span>');
            return false;
        }

        var payableEl = document.getElementById(payableId);
        if (!payableEl) {
            console.error('Amount payable element not found:', payableId);
            return false;
        }

        var prIdx = window.irccApplyCouponPricing(coupon);
        var currentAmount = prIdx.originalTotal;
        window.originalPaymentAmount = currentAmount;

        var discountValue = prIdx.discountAmount;
        var finalAmount = prIdx.finalAmount;
        var discountLabel = prIdx.discountLabel;

        // Store globally
        window.appliedCoupon = normalized;
        window.discountAmount = discountValue;

        // Update UI
        $('#' + messageId).html('<span style="color:#198754;font-weight:600;"><i class="fas fa-check-circle"></i> Coupon applied! ' + discountLabel + '</span>');
        $('#' + discountId).show();
        $('#' + amountId).text('-$' + discountValue.toFixed(2) + ' (' + discountLabel + ')');
        $('#' + payableId).text('$' + finalAmount.toFixed(2));

        // Also update modal amounts if they exist
        if ($('#modal-amount-payable').length) $('#modal-amount-payable').text('$' + finalAmount.toFixed(2));
        if ($('#summary-amount-payable-2').length) $('#summary-amount-payable-2').text('$' + finalAmount.toFixed(2));
        if ($('#summary-amount-payable').length) $('#summary-amount-payable').text('$' + finalAmount.toFixed(2));

        // Copy coupon code to modal field if it exists
        if ($('#coupon_code').length) $('#coupon_code').val(normalized);

        console.log('Coupon applied:', { code: normalized, discount: coupon.type === 'rise_student' ? 'RISE150 student' : (coupon.type === 'fixed_total' ? 'IRCC1 flat $1' : (coupon.discount + '% student')), original: currentAmount, discountValue: discountValue, final: finalAmount });
        return true;
    };

    // Universal coupon remove function
    window.removeCouponCode = function(inputId, messageId, discountId, payableId) {
        var originalAmount = (parseFloat(window.IRCC_STUDENT_AMOUNT_PAYABLE) || 0) + (parseFloat(window.IRCC_ADULT_AMOUNT_PAYABLE) || 0);
        window.originalPaymentAmount = originalAmount;

        window.appliedCoupon = null;
        window.discountAmount = 0;

        $('#' + inputId).val('');
        $('#' + messageId).html('');
        $('#' + discountId).hide();
        $('#' + payableId).text('$' + originalAmount.toFixed(2));

        // Clear modal coupon if exists
        if ($('#coupon_code').length) {
            $('#coupon_code').val('');
            $('#coupon-message').html('');
            $('#coupon-discount').hide();
        }
        if ($('#modal-amount-payable').length) $('#modal-amount-payable').text('$' + originalAmount.toFixed(2));
        if ($('#summary-amount-payable').length) $('#summary-amount-payable').text('$' + originalAmount.toFixed(2));
        if ($('#summary-amount-payable-2').length) $('#summary-amount-payable-2').text('$' + originalAmount.toFixed(2));
    };

    // Delegated click handler for ALL coupon apply buttons (works for AJAX-loaded content)
    $(document).on('click', '.apply-coupon-btn', function(e) {
        e.preventDefault();
        e.stopPropagation();

        var btn = $(this);
        window.applyCouponCode(
            btn.attr('data-input-id'),
            btn.attr('data-message-id'),
            btn.attr('data-discount-id'),
            btn.attr('data-amount-id'),
            btn.attr('data-payable-id')
        );
        return false;
    });

    // Delegated click handler for ALL coupon remove buttons
    $(document).on('click', '.remove-coupon-btn', function(e) {
        e.preventDefault();
        var btn = $(this);
        window.removeCouponCode(
            btn.attr('data-input-id'),
            btn.attr('data-message-id'),
            btn.attr('data-discount-id'),
            btn.attr('data-payable-id')
        );
        return false;
    });

    // Also handle Enter key in coupon input fields
    $(document).on('keypress', '.coupon-code-input', function(e) {
        if (e.which === 13) {
            e.preventDefault();
            $(this).closest('div').find('.apply-coupon-btn').trigger('click');
        }
    });

    console.log('✓ Coupon code handlers registered in parent dashboard page');
    // ========== END COUPON CODE HANDLER ==========

    </script>
    
    <!-- Payment Popup Function - Always available -->
    <script>
        // Payment modal function - opens modal with iframe
        window.openPaymentPopupInWindow = function() {
            console.log('=== openPaymentPopupInWindow called (from main dashboard) ===');
            
            // Validate and collect waiver data
            const waiverAcknowledgment = $('#waiver_acknowledgment');
            const waiverSignature = $('#waiver_signature');
            const waiverDate = $('#waiver_date');
            const waiverStatus = $('#waiver-status');
            
            // Get age-related checkboxes
            const waiverUnder12 = $('#waiver_under_12');
            const waiver12OrOlder = $('#waiver_12_or_older');
            const waiverUnder12Required = waiverUnder12.attr('data-required') === 'true';
            const waiver12OrOlderRequired = waiver12OrOlder.attr('data-required') === 'true';
            
            // Check if waiver is completed - validate ALL required fields
            if (!waiverStatus.is(':visible')) {
                alert('Please complete the Conference Waiver and Release Form before proceeding with payment.');
                $('#waiverModalLink').trigger('click');
                return false;
            }
            
            // Validate acknowledgment checkbox
            if (!waiverAcknowledgment.is(':checked')) {
                alert('Please check "I have read, understood, and agree to all terms and conditions" in the waiver form.');
                $('#waiverModalLink').trigger('click');
                return false;
            }
            
            // Validate age requirement checkboxes
            if (waiverUnder12Required && !waiverUnder12.is(':checked')) {
                alert('You have participants under 12 years of age. Please check "At least one participant is UNDER 12 years of age – Adult registration is MANDATORY."');
                $('#waiverModalLink').trigger('click');
                return false;
            }
            
            if (waiver12OrOlderRequired && !waiver12OrOlder.is(':checked')) {
                alert('All participants are 12 years or older. Please check "All participants are 12 YEARS OR OLDER – Adult registration is optional."');
                $('#waiverModalLink').trigger('click');
                return false;
            }
            
            // At least one age checkbox must be checked
            if (!waiverUnder12.is(':checked') && !waiver12OrOlder.is(':checked')) {
                alert('Please check at least one age requirement box (UNDER 12 or 12 YEARS OR OLDER).');
                $('#waiverModalLink').trigger('click');
                return false;
            }
            
            // Validate signature
            if (!waiverSignature.val() || waiverSignature.val().trim() === '') {
                alert('Please enter your signature (full name) in the waiver form.');
                $('#waiverModalLink').trigger('click');
                return false;
            }
            
            // Validate date
            if (!waiverDate.val()) {
                alert('Please enter the date in the waiver form.');
                $('#waiverModalLink').trigger('click');
                return false;
            }
            
            // Sync waiver data to hidden fields (if function exists)
            let waiverData = {};
            if (typeof syncWaiverDataToForm === 'function') {
                waiverData = syncWaiverDataToForm();
            } else {
                // Manual sync
                $('#waiver_acknowledgment_hidden').val(waiverAcknowledgment.is(':checked') ? '1' : '0');
                $('#waiver_signature_hidden').val(waiverSignature.val() || '');
                $('#waiver_date_hidden').val(waiverDate.val() || '');
                $('#waiver_under_12_hidden').val($('#waiver_under_12').is(':checked') ? '1' : '0');
                $('#waiver_12_or_older_hidden').val($('#waiver_12_or_older').is(':checked') ? '1' : '0');
                
                waiverData = {
                    acknowledgment: $('#waiver_acknowledgment_hidden').val(),
                    signature: $('#waiver_signature_hidden').val(),
                    date: $('#waiver_date_hidden').val(),
                    under12: $('#waiver_under_12_hidden').val(),
                    older12: $('#waiver_12_or_older_hidden').val()
                };
            }
            
            // Get payment amount from modal (with coupon discount if applied)
            // Note: coupon code handling is in make-payment.blade.php, but we need to get the final amount
            let amountPayable = $('#modal-amount-payable').text().replace('$', '').replace(/,/g, '') ||
                                  $('.amount-payable-box .float-end').last().text().replace('$', '').replace(/,/g, '') ||
                                  '0';
            
            // Check if coupon is applied (from make-payment page context)
            const couponCode = $('#coupon_code').val() || '';
            const appliedCoupon = window.appliedCoupon || null;
            const discountAmount = window.discountAmount || 0;
            const originalAmount = window.originalAmount || parseFloat(amountPayable);
            
            // If coupon is applied, use discounted amount
            if (appliedCoupon && discountAmount > 0) {
                amountPayable = (originalAmount - discountAmount).toFixed(2);
            }
            
            const participantsCount = $('#modal-participants-count').text() || '0';

            console.log('Opening payment modal with amount:', amountPayable, 'participants:', participantsCount, 'coupon:', appliedCoupon || 'none');
            console.log('Waiver data:', waiverData);
            
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
                    coupon_code: (window.appliedCoupon || ''),
                    discount_amount: (window.discountAmount || 0).toFixed(2),
                    original_amount: (window.originalAmount || parseFloat(amountPayable)).toFixed(2),
                    waiver_acknowledgment: waiverData.acknowledgment || (waiverAcknowledgment.is(':checked') ? '1' : '0'),
                    waiver_signature: waiverData.signature || waiverSignature.val() || '',
                    waiver_date: waiverData.date || waiverDate.val() || '',
                    waiver_under_12: waiverData.under12 || ($('#waiver_under_12').is(':checked') ? '1' : '0'),
                    waiver_12_or_older: waiverData.older12 || ($('#waiver_12_or_older').is(':checked') ? '1' : '0'),
                    _token: '{{ csrf_token() }}'
                },
                success: function(response) {
                    console.log('Payment URL response received:', response);
                    
                    if (response && (response.payment_url || response.url || response.checkout_url)) {
                        const paymentUrl = response.payment_url || response.url || response.checkout_url;
                        
                        console.log('Payment URL extracted, opening popup window:', paymentUrl);
                        
                        // Givebutter blocks iframe embedding, so we need to use a popup window
                        // Create popup window
                        const popupWindow = window.open(
                            paymentUrl,
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
                        
                        // Show a modal dialog to inform user that payment window is open
                        let infoModal = $('#payment-popup-info-modal');
                        if (infoModal.length === 0) {
                            $('body').append(`
                                <div class="modal fade" id="payment-popup-info-modal" tabindex="-1" aria-labelledby="paymentPopupInfoModalLabel" aria-hidden="true" data-bs-backdrop="static" data-bs-keyboard="false">
                                    <div class="modal-dialog modal-dialog-centered">
                                        <div class="modal-content">
                                            <div class="modal-header" style="background-color: #f15b43; color: white;">
                                                <h5 class="modal-title" id="paymentPopupInfoModalLabel">Payment Window Opened</h5>
                                            </div>
                                            <div class="modal-body text-center">
                                                <i class="fas fa-credit-card" style="font-size: 48px; color: #f15b43; margin-bottom: 15px;"></i>
                                                <p style="font-size: 16px; margin-bottom: 10px;">
                                                    <strong>A secure payment window has been opened.</strong>
                                                </p>
                                                <p style="font-size: 14px; color: #666;">
                                                    Please complete your payment in the popup window.<br>
                                                    Once payment is complete, you can close this dialog and the page will refresh automatically.
                                                </p>
                                            </div>
                                            <div class="modal-footer">
                                                <button type="button" class="btn btn-secondary" id="close-payment-info-modal">Close Dialog</button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            `);
                            infoModal = $('#payment-popup-info-modal');
                        }
                        
                        // Open info modal
                        try {
                            if (typeof bootstrap !== 'undefined' && bootstrap.Modal) {
                                const bsModal = new bootstrap.Modal(infoModal[0], {
                                    backdrop: 'static',
                                    keyboard: false
                                });
                                bsModal.show();
                                console.log('Payment info modal opened');
                            } else {
                                infoModal.modal({
                                    backdrop: 'static',
                                    keyboard: false
                                });
                                infoModal.modal('show');
                            }
                        } catch (error) {
                            console.error('Error opening info modal:', error);
                        }
                        
                        // Close info modal when button is clicked
                        $('#close-payment-info-modal').off('click').on('click', function() {
                            if (typeof bootstrap !== 'undefined' && bootstrap.Modal) {
                                const bsModal = bootstrap.Modal.getInstance(infoModal[0]);
                                if (bsModal) bsModal.hide();
                            } else {
                                infoModal.modal('hide');
                            }
                        });
                        
                        // Monitor popup window for closure
                        const checkPopup = setInterval(function() {
                            try {
                                if (popupWindow.closed) {
                                    clearInterval(checkPopup);
                                    console.log('Payment popup window closed, refreshing page');
                                    
                                    // Close info modal
                                    if (typeof bootstrap !== 'undefined' && bootstrap.Modal) {
                                        const bsModal = bootstrap.Modal.getInstance(infoModal[0]);
                                        if (bsModal) bsModal.hide();
                                    } else {
                                        infoModal.modal('hide');
                                    }
                                    
                                    // Refresh page after a short delay
                                    setTimeout(function() {
                                        window.location.reload();
                                    }, 1000);
                                }
                            } catch (e) {
                                // Cross-origin error - expected
                            }
                        }, 1000);
                        
                        // Listen for postMessage from popup (if Givebutter supports it)
                        const messageHandler = function(event) {
                            if (event.origin === 'https://givebutter.com' || event.origin === 'https://www.givebutter.com') {
                                if (event.data && (event.data.type === 'payment.completed' || event.data.status === 'completed')) {
                                    console.log('Payment completed via postMessage');
                                    clearInterval(checkPopup);
                                    window.removeEventListener('message', messageHandler);
                                    
                                    // Close info modal
                                    if (typeof bootstrap !== 'undefined' && bootstrap.Modal) {
                                        const bsModal = bootstrap.Modal.getInstance(infoModal[0]);
                                        if (bsModal) bsModal.hide();
                                    } else {
                                        infoModal.modal('hide');
                                    }
                                    
                                    // Refresh page
                                    setTimeout(function() {
                                        window.location.reload();
                                    }, 1000);
                                }
                            }
                        };
                        window.addEventListener('message', messageHandler);
                        
                        // Also check payment status periodically
                        const statusCheckInterval = setInterval(function() {
                            if (popupWindow.closed) {
                                clearInterval(statusCheckInterval);
                                return;
                            }
                            // Check payment status via AJAX
                            $.ajax({
                                url: '{{ route("payment.summary") }}',
                                method: 'GET',
                                success: function(response) {
                                    if (response && response.payment_summary) {
                                        const amountPayable = parseFloat(response.payment_summary.amountPayable) || 0;
                                        if (amountPayable === 0) {
                                            console.log('Payment completed - amount payable is 0');
                                            clearInterval(statusCheckInterval);
                                            clearInterval(checkPopup);
                                            window.removeEventListener('message', messageHandler);
                                            
                                            // Close popup and info modal
                                            if (!popupWindow.closed) {
                                                popupWindow.close();
                                            }
                                            if (typeof bootstrap !== 'undefined' && bootstrap.Modal) {
                                                const bsModal = bootstrap.Modal.getInstance(infoModal[0]);
                                                if (bsModal) bsModal.hide();
                                            } else {
                                                infoModal.modal('hide');
                                            }
                                            
                                            // Refresh page
                                            setTimeout(function() {
                                                window.location.reload();
                                            }, 1000);
                                        }
                                    }
                                }
                            });
                        }, 5000);
                        
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
        
        console.log('=== Payment popup function defined in main dashboard ===');
        
        // Always attach submit button handler - this ensures it works even if scripts in loaded content fail
        $(document).on('click', '#payment-form button[type="submit"], form#payment-form button[type="submit"], button[type="submit"][form="payment-form"]', function(e) {
            const selectedMode = $('input[name="payment_mode"]:checked').val();
            console.log('=== Submit button clicked (main dashboard handler) ===');
            console.log('Selected mode:', selectedMode);
            
            if (selectedMode === 'Online Transaction') {
                e.preventDefault();
                e.stopPropagation();
                e.stopImmediatePropagation();
                
                console.log('=== Online Transaction - opening payment iframe modal ===');
                
                if (typeof window.openPaymentPopupInWindow === 'function') {
                    window.openPaymentPopupInWindow();
                } else {
                    console.error('Payment function not available');
                    alert('Payment function not available. Please refresh the page.');
                }
                
                return false;
            }
        });
        
        // Also handle form submit event
        $(document).on('submit', '#payment-form', function(e) {
            const selectedMode = $('input[name="payment_mode"]:checked').val();
            console.log('=== Payment form submitted (main dashboard handler) ===');
            console.log('Selected mode:', selectedMode);
            
            if (selectedMode === 'Online Transaction') {
                e.preventDefault();
                e.stopPropagation();
                e.stopImmediatePropagation();
                
                console.log('=== Online Transaction - opening payment iframe modal ===');
                
                if (typeof window.openPaymentPopupInWindow === 'function') {
                    window.openPaymentPopupInWindow();
                } else {
                    console.error('Payment function not available');
                    alert('Payment function not available. Please refresh the page.');
                }
                
                return false;
            }
        });
    </script>

@endsection
