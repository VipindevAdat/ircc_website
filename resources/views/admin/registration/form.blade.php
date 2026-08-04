@extends('admin.layouts.app')

@section('page-title', 'Registration')
@section('head')
<link rel="stylesheet" href="{{ url("libs/datepicker/daterangepicker.css")}}" type="text/css">
@endsection
@section('content')
<div class="row flex-column-reverse flex-md-row">
   <div class="col-md-12">
      <div class="tab-content" id="myTabContent">
         <div class="tab-pane fade show active" id="profile" role="tabpanel" aria-labelledby="profile-tab">
            <div class="mb-4">
            <div class="card mb-4">
    <form class="donate-form"  method="post" action="{{route('admin.updateRegistration')}}" id="registration-form">
        @csrf
        <input type="hidden" id="category_sel" name="category_sel" value="">
        <input type="hidden" name="reg_submit" id="reg_submit" value="1">

        <input type="hidden" name="registrationId" id="registrationId" value="{{$registration->id}}">
        <div class="donate-detais form-inner">
        <div class="card-body">
        <h6 class="card-title mb-4">{{ $registration->id ? 'Edit' : 'Add' }} Registration</h6>
            <div class="row">
                <div class="col-md-6">
                    <div class="mb-3">
                    <label class="form-label">Category</label>
                    <select id="category" name="category" class="form-control-registration form-control" required >
                        <option value="" selected>Select Category</option>
                @foreach($categories as $category)
                        <option data-value="{{$category->id}}" {{$category->id==$registration->category_id?'selected':''}} value="{{$category->id}}">{{$category->name}}</option>
                @endforeach

                    </select>
                <input type="hidden" id="category_id" name="category_id">
                    <span class="form-validation" id="category1"></span>
                </div>
                </div>
                <div class="col-lg-6 col-md-6 col-sm-6 col-12 form-group">
                <div class="mb-3">
                <label class="form-label">School name</label>
                    <input type="text" name="school_name" id="school_name" placeholder="Institute name" class="form-control-registration form-control" value="{{$registration->school_name}}" required>
                    <span class="form-validation" id="school_name1"></span>
                </div>
                </div>
                <div class="col-lg-6 col-md-6 col-sm-6 col-12 form-group" >
                <div class="mb-3">
                <label class="form-label">Teacher/Guardian</label>
                    <select class="form-control-registration form-control" name="teacher"   id="teacher" required>
                        <option value="teacher" {{$registration->teacher=='teacher'?'selected':''}}>Teacher</option>
                        <option value="guardian" {{$registration->teacher=='guardian'?'selected':''}}>Guardian</option>
                    </select>
                    <span class="form-validation" id="teacher1"></span>
                </div>
                </div>
                <div class="col-lg-6 col-md-6 col-sm-6 col-12 form-group" >
                <label class="form-label">Teacher name</label>
                <div class="mb-3">
                    <input class="form-control-registration form-control" type="text" name="teacher_name" placeholder="Teacher/guardian name" value="{{$registration->teacher_name}}">
                    <span class="form-validation" id="teacher_name1"></span>
                </div>
                </div>
                <div class="col-lg-6 col-md-6 col-sm-6 col-12 form-group" >
                <label class="form-label">Email</label>
                <div class="mb-3">
                    <input class="form-control-registration form-control" type="email" id="email" name="email" placeholder="E-mail*" value="{{$registration->email}}" required>
                    <span class="form-validation" id="email1"></span>
                </div>
                </div>
                <div class="col-lg-6 col-md-6 col-sm-6 col-12 form-group">
                <div class="mb-3">
                <label class="form-label">Phone</label>
                    <input class="form-control-registration form-control" type="number" name="phone" placeholder="Phone*" value="{{$registration->phone}}" required>
                    <span class="form-validation" id="phone1"></span>
                </div>
                </div>
                <div class="col-lg-6 col-md-6 col-sm-6 col-12 form-group">
                <div class="mb-3">
                <label class="form-label">Participation</label>
                    <select id="participation" name="participation" class="form-control-registration form-control" required>
                        <option value="" selected>Select Participation</option>
                        <option value="1" {{$registration->participation==1?'selected':''}}>Participants Only</option>
                        <option value="2" {{$registration->participation==2?'selected':''}}>Participation and presentation</option>
                    </select>
                    <span class="form-validation" id="participation1"></span>
                </div>
                </div>
                <div class="col-lg-6 col-md-6 col-sm-6 col-12 form-group">
                <div class="mb-3">
                <label class="form-label">No of Students</label>
                    <select class="form-control-registration form-control" type="text" id="members" name="members" data-id="name2" placeholder="Select number of students *" required>
                        <option value="" selected>Select number of students</option>
                @for($index = 1; $index <= 10; $index++)
                        <option value="{{ $index }}" {{$registration->members==$index?'selected':''}}>{{ $index }}</option>
                @endfor
                        {{-- <option value="2">2</option>
                        <option value="3">3</option>
                        <option value="4">4</option>
                        <option value="5">5</option>
                        <option value="6">6</option>
                        <option value="7">7</option>
                        <option value="8">8</option>
                        <option value="9">9</option>
                        <option value="10">10</option> --}}
                    </select>
                    <span class="form-validation" id="members1"></span>
                </div>
                </div>
            </div>

        <div class="row p-3 pt-4" id="students_div" >

        <section class="card card-body border mb-0">
            <div class="row form-wrap" id="students_data_div">
            <div class="col-md-12 repeater-1">
            <div class="row">
            <input type="hidden" name="deleteTeam" id="deleteTeam">

            <div class="col-md-9">
            <h5>Student Details</h5>
            </div>
            <div class="col-md-3">
            <button type="button" class="btn btn-primary pull-right" data-repeater-create><i class="fa fa-plus" aria-hidden="true"></i></button>
            </div>
            </div>
                <div data-repeater-list="team">

                @if(count($registration->teams))
                    @foreach($registration->teams as $key=>$team)
                    @php $no=$key+1;$s=1;$n=10;@endphp

                     <div data-repeater-item class="feature-box" data-id="{{$team->id}}">

                        <div class="row team-row">
                           <div class="col-md-4 col-sm-12 mb-3">
                              <label for="name" class="form-label">First Name</label>
                              <input type="text" class="form-control" type="text" name="stud_first_name"  title="Please enter alphabets only"  placeholder="Student firstname*"  data-id="first_name{{$no}}" value="{{$team->first_name}}" required>
                              <input type="hidden" name="team_id" class="team" value="{{$team->id}}">
                            </div>
                           <div class="col-md-3 col-sm-12 mb-3">
                              <label for="email" class="form-label">Last Name</label>
                              <input type="text" class="form-control" type="text" name="stud_last_name"  title="Please enter alphabets only"  placeholder="Student lastname*"  data-id="last_name{{$no}}" value="{{$team->last_name}}" required>
                           </div>

                           <div class="col-md-3">
                              <label class="form-label">Class</label>
                              <select class="form-control-student form-control form-select" type="text" name="stud_class" data-id="stud_class{{$no}}" placeholder="" >
                                <option value="">Select Class</option>
                                    @for($k=$s;$k<=$n;++$k)
                                    <option value="{{$k}}" {{$team->class==$k?'selected':''}}>{{$k}}</option>
                                    @endfor
                                </select>

                           </div>

                           <div class="col-md-1">
                              <div><label class="form-label"> </label></div>
                              <a class="btn btn-danger delete-team" data-id="{{$team->id}}"  data-repeater-delete ><i class="bi bi-trash"></i></a>
                           </div>
                        </div>
                     </div>
                     @endforeach
                     @else
                     @php $s=1;$n=10;@endphp
                     <div data-repeater-item class="feature-box" data-id="">

                        <div class="row team-row">
                           <div class="col-md-4 col-sm-12 mb-3">
                              <label for="name" class="form-label">First Name</label>
                              <input type="text" class="form-control" type="text" name="stud_first_name"  title="Please enter alphabets only"  placeholder="Student firstname*"  value="" required>
                              <input type="hidden" name="team_id" class="team" value="">
                            </div>
                           <div class="col-md-3 col-sm-12 mb-3">
                              <label for="email" class="form-label">Last Name</label>
                              <input type="text" class="form-control" type="text" name="stud_last_name"  title="Please enter alphabets only"  placeholder="Student lastname*"   value="" required>
                           </div>

                           <div class="col-md-3">
                              <label class="form-label">Class</label>
                              <select class="form-control-student form-control form-select" type="text" name="stud_class"  placeholder="" >
                                <option value="">Select Class</option>
                                    @for($k=$s;$k<=$n;++$k)
                                    <option value="{{$k}}">{{$k}}</option>
                                    @endfor
                                </select>

                           </div>

                           <div class="col-md-1">
                              <div><label class="form-label"> </label></div>
                              <a class="btn btn-danger delete-team"   data-repeater-delete ><i class="bi bi-trash"></i></a>
                           </div>
                        </div>
                     </div>
                @endif
                  </div>

            </div>
            </div>
        </section>
        </div>
        <div class="row p-3 pt-4" id="accompanyDetailsDiv">
        <section class="card card-body border mb-0">

            <div class="row form-wrap">

            <div class="col-md-12 repeater-2">
            <input type="hidden" name="deleteAccompany" id="deleteAccompany">
            <div class="row">
            <div class="col-md-5">
            <h5>Accompanying person </h5>
            </div>

            <div class="col-md-4">
            <div class="form-check form-switch">
                <input class="form-check-input" type="checkbox" id="accompanyYes" {{count($registration->accompany)?'checked':''}} style="width: 3.5em;height: 2em;">
                <input type="hidden" name="accompanyAdd" id="accompanyAdd" value="{{count($registration->accompany)?1:0}}">
                </div>
            </div>

            <div class="col-md-3" id="accompanyPlus" style="{{count($registration->accompany)?'display:block':'display:none'}}">
            <button type="button" class="btn btn-primary pull-right" data-repeater-create><i class="fa fa-plus" aria-hidden="true"></i></button>
            </div>
            </div>
            <div id="accompanyDiv" style="{{count($registration->accompany)?'display:bock':'display:none'}}">
            <div class="row">
                <div data-repeater-list="accompany"  >
                @if(count($registration->accompany))
                    @foreach($registration->accompany as $key=>$accompany)
                    @php $no=$key+1;$s=1;$n=10;@endphp

                     <div data-repeater-item class="accompany-box"  data-id="{{$accompany->id}}">
                        <div class="row">
                           <div class="col-md-5 col-sm-12 mb-3">
                           <label>Name</label>
                           <input type="hidden" name="accompany_id" class="accompany_id" value="{{$accompany->id}}">
                            <input type="text" class="form-control-accompany form-control" name="accompany_name" id="accompany_name"  data-id="accompany_name1" value="{{$accompany->name}}" required>
                            <span class="form-validation" id="accompany_name1" data></span>
                         </div>
                           <div class="col-md-5 col-sm-12 mb-3">
                           <label>Email</label>
                            <input type="email" class="form-control-accompany form-control" name="accompany_email" id="accompany_email"  data-id="accompany_email1" value="{{$accompany->email}}" required>
                            <span class="form-validation " id="accompany_email1" data></span>
                           </div>


                           <div class="col-md-2">
                              <div><label class="form-label"> </label></div>
                              <a class="btn btn-danger"  data-repeater-delete><i class="bi bi-trash"></i></a>
                           </div>
                        </div>
                     </div>
                     @endforeach
                @else
                <div data-repeater-item class="accompany-box" data-id="">
                        <div class="row">
                           <div class="col-md-5 col-sm-12 mb-3">
                           <input type="hidden" name="accompany_id" class="accompany_id" value="">
                           <label>Name</label>
                            <input type="text" class="form-control-accompany form-control" name="accompany_name" id="accompany_name"  data-id="accompany_name1" >
                            <span class="form-validation" id="accompany_name1" data></span>
                         </div>
                           <div class="col-md-5 col-sm-12 mb-3">
                           <label>Email</label>
                            <input type="email" class="form-control-accompany form-control" name="accompany_email" id="accompany_email"  data-id="accompany_email1">
                            <span class="form-validation " id="accompany_email1" data></span>
                           </div>


                           <div class="col-md-2">
                              <div><label class="form-label"> </label></div>
                              <a class="btn btn-danger"  data-repeater-delete><i class="bi bi-trash"></i></a>
                           </div>
                        </div>
                    </div>
               @endif
                  </div>
               </div>
               <div class="row form-wrap mt-5" >
               <h5>Payment details</h5>
               </div>
               <div class="row form-wrap mt-5" >

               <div class="col-md-3">
               <label class="form-label">Payment Type </label>
               </div>
                <div class="col-md-3">

                <div class="form-check">
                <input class="form-check-input" type="radio" name="payment_method" id="flexRadioDefault1" value="upi" {{$registration->accompany_payment?($registration->accompany_payment->payment_method=='upi'?'checked':''):'checked'}}>
                <label class="form-check-label" for="flexRadioDefault2">
                    UPI
                </label>
                </div>
                </div>
                <div class="col-md-3">
                <div class="form-check">
                <input class="form-check-input" type="radio" name="payment_method" id="flexRadioDefault2" value="bank" {{$registration->accompany_payment?($registration->accompany_payment->payment_method=='bank'?'checked':''):''}}>
                <label class="form-check-label" for="flexRadioDefault2">
                    Bank Transfer
                </label>
                </div>
                </div>
               </div>
                  <div class="row form-wrap mt-5" >



                    <div class="col-md-4">
                        <label>Transaction Number</label>
                    <input type="text" name="payment_reference" class="form-control" value="{{$registration->accompany_payment?$registration->accompany_payment->payment_reference:''}}" required/>
                    </div>
                    <div class="col-md-4">
                        <label>Payment Date</label>
                        <input type="text" name="payment_date" id="payment_date" class="form-control form-control-accompany payment_date" placeholder="Payment Date" value="{{$registration->accompany_payment?Carbon\Carbon::parse($registration->accompany_payment->payment_date)->format('dd-mm-Y'):Carbon\Carbon::parse(Carbon\Carbon::now())->format('dd-mm-Y')}}">

                    </div>
                    <div class="col-md-4">
                        <label>Payment Amount</label>
                        <input type="text" name="payment_amount" id="payment_amount" class="form-control form-control-accompany payment_amount" placeholder="Payment Amount" value="{{$registration->accompany_payment?$registration->accompany_payment->payment_amount:''}}">

                    </div>
                </div>
            </div>
            </div>


            </div>
        </section>
        </div>

        <div class="row">
            <div class="col-md-12">
            <button type="submit" class="btn btn-info mt-4 pull-right">Save</button>
            </div>
            <!-- <div class="col-md-3">
                <button type="submit"  class="button-round reg_submit">Register & Submit</button>
                </div> -->
        </div>
        </div>
    </form>
</div>
            </div>
         </div>
      </div>
   </div>
</div>
@endsection
@section('script')
<script src="{{ url("libs/datepicker/daterangepicker.js")}}"></script>
<script src="{{ url("libs/form-repeater/repeater.min.js")}}"></script>
<script>
        $(document).ready(function () {
            $('.repeater-1').repeater({
                show: function () {
                    $(this).slideDown();
                },

                hide: function (deleteElement) {

                    if (confirm('Are you sure you want to delete this element?')) {
                        var idS=$('#deleteTeam').val();
                        if (idS.length > 0) {
                        idS=idS+"," + $(this).data('id');
                        } else {
                        idS=idS+$(this).data('id');
                        }

                        $('#deleteTeam').val(idS)
                    $(this).slideUp(deleteElement);
                    }
                    else{
                        return false;
                    }
                }
            });
            $('.repeater-2').repeater({
                show: function () {
                    $(this).slideDown();
                },

                hide: function (deleteElement) {
                    if (confirm('Are you sure you want to delete this element?')) {

                        var idS=$('#deleteAccompany').val();
                        if (idS.length > 0) {
                        idS=idS+"," + $(this).data('id');
                        } else {
                        idS=idS+$(this).data('id');
                        }
                        console.log(idS)
                        $('#deleteAccompany').val(idS);
                        $(this).slideUp(deleteElement);
                    }
                    else{
                        return false;
                    }
                }
            });
            $(document).on('click', '#accompanyYes', function() {
                if($(this).is(':checked')){
                    $('#accompanyAdd').val(1);
                    $('#accompanyDiv').show();
                    $('#accompanyPlus').show();
                }
                else{
                    $('#accompanyAdd').val(0);
                    $('#accompanyDiv').hide();
                    $('#accompanyPlus').hide();
                }

            });
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


        });
</script>
@endsection
