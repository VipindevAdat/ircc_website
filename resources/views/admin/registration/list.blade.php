@extends('admin.layouts.app')

@section('page-title', 'Registration')

@section('head')
    <link rel="stylesheet" href="{{ url('libs/dataTable/datatables.min.css') }}" type="text/css">
    <link rel="stylesheet" href="{{ url('libs/range-slider/css/ion.rangeSlider.min.css') }}" type="text/css">
    <style>
        .table-responsive {
            overflow-x: scroll !important;
        }

        .table-responsive::-webkit-scrollbar {
            margin-top: 20px;
            height: 6px;
            /* width of the entire scrollbar */
        }

        .table-responsive::-webkit-scrollbar-track {
            background: #e3e3e3;
            /* color of the tracking area */
            border-radius: 20px;
        }

        .table-responsive::-webkit-scrollbar-thumb {
            background-color: #d02027;
            /* color of the scroll thumb */
            border-radius: 20px;
            /* roundness of the scroll thumb */
            /* border: 0.5px solid #117dc0;  creates padding around scroll thumb */
        }

    </style>
@endsection


@section('content')

    <div class="mb-4">
        <nav style="--bs-breadcrumb-divider: '>';" aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item">
                    <a href="{{ route('admin.dashboard') }}">
                        <i class="bi bi-globe2 small me-2"></i> Dashboard
                    </a>
                </li>
                <li class="breadcrumb-item active" aria-current="page">Registration</li>
            </ol>
        </nav>
    </div>

    <div class="content">
        <div class="">
            <div class="card">
            <form method="POST" action="{{route('admin.exportRegistration')}}" >
                @csrf
                <input type="hidden" name="type" value="registration">
                <div class="card-body">
                    <div class="d-md-flex gap-4 align-items-center">
                        <div class="d-none d-md-flex">All Registrations</div>
                        <div class="d-md-flex gap-4 align-items-center">

                                <div class="row g-3">

                                    <div class="col-md-12">
                                    <select class="form-select" id="pageLength">
                                        <option value="10">10</option>
                                        <option value="20">20</option>
                                        <option value="30">30</option>
                                        <option value="40">40</option>
                                        <option value="50">50</option>
                                        <option value="100">100</option>
                                    </select>
                                </div>


                                </div>

                        </div>
                        <div class="ms-auto">
                            <button type="submit"  class="btn btn-info mt-4"><i class="fa fa-upload"></i> Export XLS</button>
                        </div>

                    </div>
                    </div>
                    </form>
                </div>
                <div class="table-responsive">
                    <table class="table table-custom table-lg mb-0" id="registrations">
                    <thead>
                      <tr>
                         <th>Register Id</th>
                         <th>Created Date</th>
                         <th>Institute Name</th>
                        <th>Name</th>
                        <th>Participants</th>
                        <th>Email ID</th>
                        <th>Whatsapp Number</th>
                        <th>How did you hear about us?</th>
                        <th>Abstract Status</th>
                        <th>Payment Status</th>
                        <th>Review Status</th>
                        <th></th>
                      </tr>
                    </thead>
                    <tbody>


                    </tbody>
                    </table>
                </div>
            </div>
        </div>
    <div class="modal fade" id="detailModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">Registration Details</h5>
                <button type="button" class="close" data-bs-dismiss="modal" aria-label="Close">
                <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body" id="detailBody">
                <div class="row">
                    <div class="col-4">
                    Name :
                    </div>
                    <div class="col-8">
                    Rajesh MM
                    </div>

                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
    </div>
    <div class="modal fade" id="delete-modal" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="staticBackdropLabel">Delete Registration</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="delete-log-form"  method="POST">
            <div class="modal-body">
                    <!-- <input type="hidden" name="_method" value="DELETE"> -->
                        <input type="hidden" name="_token" value="{{ csrf_token() }}">
                        <input type="hidden" id="deleteId" name="deleteId">
                        <div id="msg_div"></div>
                        <p>Are you sure you want to delete this registration</p>
                        <div class="modal-footer">

                            <button type="button" class="btn btn-sm btn-danger" data-bs-dismiss="modal">Close</button>
                            <button type="button" class="btn btn-sm btn-danger btn_delete_registration" data-loading-text="">Confirm Delete</button>
                        </div>
                    </div>
                </form>
            </div>
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
        <form method="POST" action="" id="confirm-form">
                @csrf
        <div class="modal-body"  id="detailPayment">



        </div>
        <div class="modal-footer">
        <button type="submit" class="btn btn-primary" id="confirmVerify" style="display: none;">Confirm</button>
        <button type="button" class="btn btn-secondary" id="verifyAccompany">Verify Accompany</button>
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>

        </div>
        </form>
        </div>
    </div>
    </div>
    <div class="modal fade" id="editModal" tabindex="-1" role="dialog" aria-labelledby="editModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
        <div class="modal-header">
            <h5 class="modal-title" id="exampleModalLabel">Registration Details</h5>
            <button type="button" class="close" data-bs-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
            </button>
        </div>
        <div class="modal-body" id="registerDiv">
        <div class="card mb-4">
            <form class="donate-form"  method="post" action="{{route('projectSubmit')}}" id="registration-form">
                @csrf
                <input type="hidden" id="category_sel" name="category_sel" value="">
                <input type="hidden" name="reg_submit" id="reg_submit" value="1">

                <div class="donate-detais form-inner">

                    <div class="row mb-3">
                        <div class="col-lg-6 col-md-6 col-sm-6 col-12 form-group">
                            <div class="mb-3">
                                <input type="text" name="institute_name" id="institute_name" placeholder="Institute name" class="form-control-registration form-control" required>
                                <span class="form-validation" id="institute_name"></span>
                            </div>
                        </div>
                        <div class="col-lg-6 col-md-6 col-sm-6 col-12 form-group" >
                            <div class="mb-3">
                                <input class="form-control-registration form-control" type="text" name="first_name" placeholder="First name" required>
                                <span class="form-validation" id="first_name"></span>
                            </div>
                        </div>
                        <div class="col-lg-6 col-md-6 col-sm-6 col-12 form-group" >
                            <div class="mb-3">
                                <input class="form-control-registration form-control" type="text" name="last_name" placeholder="Last name" required>
                                <span class="form-validation" id="last_name"></span>
                            </div>
                        </div>
                        <div class="col-lg-6 col-md-6 col-sm-6 col-12 form-group" >
                        <div class="mb-3">
                            <input class="form-control-registration form-control" type="email" id="email" name="email" placeholder="E-mail*" required>
                            <span class="form-validation" id="email"></span>
                        </div>
                        </div>
                        <div class="col-lg-6 col-md-6 col-sm-6 col-12 form-group">
                        <div class="mb-3">
                            <input class="form-control-registration form-control" type="number" name="whatsapp_number" placeholder="Whatsapp Number*" required>
                            <span class="form-validation" id="whatsapp_number"></span>
                        </div>
                        </div>
{{--                        <div class="col-lg-6 col-md-6 col-sm-6 col-12 form-group">--}}
{{--                        <div class="mb-3">--}}
{{--                            <select id="participation" name="participation" class="form-control-registration form-control" required>--}}
{{--                                <option value="" selected>Select Participation</option>--}}
{{--                                <option value="1">Participants Only</option>--}}
{{--                                <option value="2">Participation and presentation</option>--}}
{{--                            </select>--}}
{{--                            <span class="form-validation" id="participation1"></span>--}}
{{--                        </div>--}}
{{--                        </div>--}}
{{--                        <div class="col-lg-6 col-md-6 col-sm-6 col-12 form-group">--}}
{{--                        <div class="mb-3">--}}
{{--                            <select class="form-control-registration form-control" type="text" id="members" name="members" data-id="name2" placeholder="Select number of students *" required>--}}
{{--                                <option value="" selected>Select number of students</option>--}}
{{--                        @for($index = 1; $index <= 10; $index++)--}}
{{--                                <option value="{{ $index }}" >{{ $index }}</option>--}}
{{--                        @endfor--}}
{{--                                --}}{{-- <option value="2">2</option>--}}
{{--                                <option value="3">3</option>--}}
{{--                                <option value="4">4</option>--}}
{{--                                <option value="5">5</option>--}}
{{--                                <option value="6">6</option>--}}
{{--                                <option value="7">7</option>--}}
{{--                                <option value="8">8</option>--}}
{{--                                <option value="9">9</option>--}}
{{--                                <option value="10">10</option> --}}
{{--                            </select>--}}
{{--                            <span class="form-validation" id="members1"></span>--}}
{{--                        </div>--}}
{{--                        </div>--}}
{{--                    </div>--}}
{{--                </div>--}}
                <div class="donate-detais  form-inner" id="students_div" >
                    <h5>Student Details</h5>
                    <div class="row form-wrap" id="students_data_div">
                    @if(2>0)
                    @for($i=0;$i<2;++$i)
                    @php $no=$i+1;$s=1;$n=10;@endphp
                    <label>Student {{$no}}</label>
                    @if($s!=0 && $n!=0)
                    <div class="col-lg-4 col-md-6 col-sm-6 col-12 form-group">
                    <div class="mb-3">
                    <input class="form-control-student form-control" type="text" name="stud_first_name[]"  title="Please enter alphabets only"  placeholder="Student firstname*"  data-id="first_name{{$no}}">
                    <span class="form-validation" id="first_name{{$no}}" data></span>
                    </div>
                    </div>
                    <div class="col-lg-4 col-md-6 col-sm-6 col-12 form-group" >
                    <div class="mb-3">
                    <input class="form-control-student form-control" type="text" name="stud_last_name[]"  title="Please enter alphabets only"  placeholder="Student lastname*" data-id="last_name{{$no}}">
                    <span class="form-validation" id="last_name{{$no}}"></span>
                    </div>
                    </div>

                    <div class="col-lg-4 col-md-6 col-sm-6 col-12 form-group" >
                    <div class="mb-3">
                    <select class="form-control-student form-control" type="text" name="stud_class[]" data-id="stud_class{{$no}}" placeholder="" >
                    <option value="">Select Class</option>
                        @for($k=$s;$k<=$n;++$k)
                        <option value="{{$k}}">{{$k}}</option>
                        @endfor
                    </select>
                    </div>
                    <span class="form-validation" id="stud_class{{$n}}"></span>
                    </div>
                    @else
                    <div class="col-lg-6 col-md-6 col-sm-6 col-12 form-group">
                    <div class="mb-3">
                    <input class="form-control-student form-control" type="text" name="stud_first_name[]"  title="Please enter alphabets only"  placeholder="Student firstname*"  data-id="first_name{{$no}}">
                    <span class="form-validation" id="first_name{{$no}}" data></span>
                    </div>
                    </div>
                    <div class="col-lg-6 col-md-6 col-sm-6 col-12 form-group" >
                    <div class="mb-3">
                    <input class="form-control-student form-control" type="text" name="stud_last_name[]"  title="Please enter alphabets only"  placeholder="Student lastname*" data-id="last_name{{$no}}">
                    <span class="form-validation" id="last_name{{$no}}"></span>
                    </div>
                    </div>

                    @endif
                    @endfor
                    @endif
                    </div>
                </div>
                <div class="donate-detais  form-inner" id="accompanyDetailsDiv">
                    <h5>Accompanying person </h5>
                    <div class="row form-wrap">

                        <div class="mt-4" id="accom_main_div" >
                            <div class="row">
                                <div class="col-lg-6 col-md-6 col-sm-6 col-12 form-group">
                                    <label>No of Person</label>
                                    <select class="form-control-registration form-control" type="text" id="no_accompany" name="no_accompany" data-id="no_accompany" placeholder="Select number of accompany *" required>
                                        <option value="0">Select number of person</option>
                                @for($index = 1; $index <= 10; $index++)
                                <option value="{{ $index }}" {{ $index==1 ? 'selected' : '' }}>{{ $index }}</option>
                                @endfor
                                        {{-- <option value="1"  selected>1</option>
                                        <option value="2">2</option>
                                        <option value="3">3</option>
                                        <option value="4">4</option>
                                        <option value="5">5</option>
                                        <option value="6">6</option>
                                        <option value="7">7</option>
                                        <option value="8">8</option>
                                        <option value="9">9</option>
                                        <option value="10">10</option> --}}
                                    </select>
                                    <span class="form-validation" id="no_accompany1"></span>
                                </div>

                            </div>
                            <div class="row mt-1" id="accompanyDiv">
                                <div class="col-lg-6 col-md-6 col-sm-6 col-12 form-group">
                                    <label>Name</label>
                                    <input type="text" class="form-control-accompany form-control" name="accompany_name[]" id="accompany_name"  data-id="accompany_name1" >
                                    <span class="form-validation" id="accompany_name1" data></span>
                                </div>
                                <div class="col-lg-6 col-md-6 col-sm-6 col-12 form-group">
                                    <label>Email</label>
                                    <input type="email" class="form-control-accompany form-control" name="accompany_email[]" id="accompany_email"  data-id="accompany_email1">
                                    <span class="form-validation " id="accompany_email1" data></span>
                                </div>
                            </div>
                            <div class="row pt-3">
                                <h5>Choose your payment method</h5>
                                <div class="col-lg-12 col-md-6 col-sm-6 col-12">
                                <div class="payment-detail" id="bankDiv" style="display:none;">
                                <h5>Payment Details</h5>
                                <div class="d-flex p-4 container">
                                <div class="col-md-7 pe-2">
                                    <p>After payment through bank account share payment details here</p>
                                    <div class="col-lg-12 col-md-6 col-sm-6 col-12 form-group"  >
                                        <label>Payment Reference</label>
                                        <input type="text" name="payment_reference_bank" class="form-control-accompany form-control_bank" id="payment_reference_bank"  data-id="payment_reference_bank_span" >
                                        <span class="form-validation" id="payment_reference_bank_span" data></span>
                                    </div>
                                    <div class="col-lg-12 col-md-6 col-sm-6 col-12 form-group" >
                                        <label>Payment Date</label>
                                        <input type="text" name="payment_date" id="payment_date" class="form-control-accompany form-control_bank payment_date" placeholder="Payment Date">
                                        <span class="form-validation" id="payment_date1" data></span>
                                    </div>
                                </div>
                                <div class="col-md-5 bg-light p-3 ms-2 payment-account">
                                    <h4>Bank Account Details</h4>
                                    <div class="row pb-1">
                                        <div class="col-sm-4 text-sm-right">Account #</div>
                                        <div class="col-sm-1 text-center d-none d-sm-inline">:</div>
                                        <div class="col-sm-7 fw-bold" >17310100084924</div>
                                    </div>
                                    <div class="row pb-1">
                                        <div class="col-sm-4 text-sm-right ">Account Name</div>
                                        <div class="col-sm-1 text-center d-none d-sm-inline">:</div>
                                        <div class="col-sm-7 fw-bold">Renju joseph</div>
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
                                    </div>
                            <div class="payment-detail" id="upiDiv">
                                <h5>Payment Details</h5>
                                <div class="d-flex p-4 container">
                                <div class="row">
                                    <p>After making payment online share payment details here.</p>
                                    <div class="col-lg-6 col-md-6 col-sm-6 col-12 form-group"  >
                                        <label>Payment Reference</label>
                                        <input type="text" name="payment_reference_upi" class="form-control-accompany form-control_upi form-control" id="payment_reference_upi"  data-id="payment_reference_upi_span" >
                                        <span class="form-validation" id="payment_reference_upi_span" data></span>
                                    </div>
                                    <div class="col-lg-6 col-md-6 col-sm-6 col-12 form-group" >
                                        <label>Payment Date</label>
                                        <input type="text" name="payment_date_upi" id="payment_date_upi" class="form-control-accompany form-control_upi form-control payment_date" placeholder="Payment Date">
                                        <span class="form-validation" id="payment_date_upi1" data></span>
                                    </div>
                                </div>

                                </div>
                                    </div>
                                    {{-- <div class="row border-box " id="upiDiv">
                                        <div class="col-md-7">
                                            <div class="col-lg-12 col-md-6 col-sm-6 col-12 form-group" >
                                                <p>Lorem Ipsum is simply dummy text of the printing simply dummy text of the printing</p>
                                                <label>Payment Reference</label>
                                                <input type="text" name="payment_reference_upi" class="form-control-accompany form-control_upi form-control" id="payment_reference"  data-id="payment_reference_upi_span" >
                                                <span class="form-validation" id="payment_reference_upi_span" data></span>
                                            </div>
                                            <div class="col-lg-12 col-md-6 col-sm-6 col-12 form-group" >
                                                <label>Payment Date</label>
                                                <input type="text" name="payment_date_upi" id="payment_date_upi" class="form-control-accompany form-control_upi form-control payment_date" placeholder="Payment Date">
                                                <span class="form-validation" id="payment_date_upi1" data></span>
                                            </div>
                                        </div>
                                        <div class="col-md-5 bg-light  p-3">
                                            <h4>UPI ID</h4>
                                            <p><span class="h5-title">GPay 8086200020</span></p>
                                        </div>
                                    </div> --}}
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-12">
                        <button type="submit" class="button-round reg_register">Register Now</button>
                    </div>
                    <!-- <div class="col-md-3">
                        <button type="submit"  class="button-round reg_submit">Register & Submit</button>
                        </div> -->
                </div>
            </form>
        </div>
        </div>
        </div>
    </div>
    </div>
    @endsection


    @section('script')
        <!-- Range slider -->
<script src="{{ url('libs/dataTable/datatables.min.js') }}"></script>
<script src="{{ url('libs/range-slider/js/ion.rangeSlider.min.js') }}"></script>
<script>
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
    </script>
<script type="text/javascript">
    $(document).on('click', '#registerDetail', function() {
      var id=$(this).data('id');
      $.ajax({
         type: 'POST',
         data:{id:id},
         dataType : 'JSON',
         url : '{{ route('admin.getRegistrationById') }}',
         success: function(response){
               $('#detailModal').modal('show');
               $('#detailBody').html(response.html);

         }
       });

   });
   $(document).on('click', '.paymentDetail', function() {
      var id=$(this).data('id');
      $('#verifyAccompany').show();
      $('#confirmVerify').hide();
      $.ajax({
         type: 'POST',
         data:{id:id},
         dataType : 'JSON',
         url : '{{ route('admin.getRegistrationById') }}',
         success: function(response){
               $('#paymentModal').modal('show');
               $('#detailPayment').html(response.htmlPayment);

         }
       });

   });


$(document).ready(function() {
    var $column = $('#sort').find(':selected').data('column');
    var $sort = $('#sort').find(':selected').data('sort');
    $registrationTable = $('#registrations').DataTable({
        //order: [[$column==undefined?'4':$column, $sort==undefined?'desc':$sort]],
        processing: true,
        serverSide: true,

        ajax: {
            url: '{{ route('admin.registration.index') }}',
            data: function(d) {

            }
        },

        columns: [
            {
                data: 'registration_number',
                name: 'registration_number'
            },
            {
                data: 'created_at',
                name: 'created_at'
            },
            {
                data: 'institution_name',
                name: 'institution_name'
            },
            {
                data: 'name',
                name: 'name'
            },
            {
                data: 'participants_count',
                name: 'participants_count',
                searchable: false
            },
            {
                data: 'email',
                name: 'email'
            },
            {
                data: 'phone',
                name: 'phone'
            },
            {
                data: 'hear_about_us',
                name: 'hear_about_us'
            },
            {
                data: 'abstract_status',
                name: 'abstract_status',
                orderable: false
            },
            {
                data: 'payment_status',
                name: 'payment_status'
            },
            {
                data: 'review_status',
                name: 'review_status',
                orderable: false
            },
            {
                data: 'action',
                name: 'action',
                orderable: false,
                searchable: false
            }
        ],
        columnDefs: [{
            'defaultContent': '--',
            "targets": "_all"
        }],

        });

    $(document).on("keyup", ".searchInput", function(e) {
        $registrationTable.search($(this).val()).draw();
    });
    $('#pageLength').on('change', function() {
        $registrationTable.page.len($(this).val()).draw();
    });
   $('#pageLength').val($registrationTable.page.len());

    $("#registrations_filter").css({
        "display": "none"
    });
    $(".dataTables_filter").css({
        "display": "none"
    });

    $("#registrations_length").css({
        "display": "none"
    });


    $('.filter').change(function() {
        $registrationTable.draw();
    });

    $('#sort').on('change', function() {
        $column = $(this).find(':selected').data('column');
        $sort = $(this).find(':selected').data('sort');
        $registrationTable.order([$column, $sort]).draw();
    })
    $(document).on('click', '#verifyAccompany', function() {
    var id=$('#registration_id').val();
    // $('#paymentModal').modal('hide');
    $('#verifyAccompany').hide();
    $('#confirmVerify').show();
    $('#amountDiv').show();
    $('#confirm-form').on('submit',  function (e) {
        e.preventDefault();
      $.ajax({
         type: 'POST',
         data: $(e.target).serialize(),
         dataType : 'JSON',
         url : '{{ route('admin.verifyAccompany') }}',
         success: function(response){
            $('#paymentModal').modal('hide');
            $registrationTable.draw();
            Swal.fire({
                icon: 'success',
                title: 'Successfully Verified',
                text:'',
                footer: ''
            });

         }
       });
    });
   });


});
$('table').off('click').on('click', '.delete-registration', function(e) {
    e.preventDefault();

    var href = $(this).data('href');
    $('#deleteId').val($(this).data('id')); 
    $('.btn_delete_registration').off('click').on('click', function() {
        $.ajax({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            type: 'DELETE',
            url: href,
            dataType: 'JSON',
            success: function(response) {
                $('#delete-modal').modal('hide'); 
                $('#registrations').DataTable().ajax.reload();
                
                Swal.fire({
                    icon: 'success',
                    title: 'Registration deleted successfully',
                    footer: ''
                });
            },
            error: function(xhr) {
                $('#delete-modal').modal('hide'); 
                Swal.fire({
                    icon: 'error',
                    title: 'Failed to delete registration',
                    text: xhr.responseJSON?.message || 'Something went wrong.',
                    footer: ''
                });
            }
        });
    });
});

$(document).on("click","#delete-reg",function() {
            $('#deleteId').val($(this).data('id'));
            console.log('reg');
          $('#delete-modal').modal('show');

        });
        $(document).on("click","#edit-reg",function() {
            $('#deleteId').val($(this).data('id'));
            id=$(this).data('id');
            $.ajax({
                type: 'POST',
                data:{id:id},
                dataType : 'JSON',
                url : '{{ route('admin.editRegistration') }}',
                success: function(response){
                    $('#editModal').modal('show');
                    $('#registerDiv').html(response.html);

                }
            });


        });


</script>
    @endsection
