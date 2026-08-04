@extends('layouts.app')
@section('head')
<style>
   .form-validation{
      color:red;
   }
   .modal-body{
      max-height: calc(100vh - 100px);
      overflow-y: auto;
   }
   .donate-form .form-inner {
      margin-bottom: 20px !important;
   }
   #loading {
      background: url('../front/images/loading.gif') no-repeat center center;
      position: fixed;
      width: 100%;
      height: 100vh;
      z-index: 999;
   }
   .amount-label{
      margin: 0 auto;
      border: 1px solid #DEDEDE;
      -webkit-border-radius: 25px;
      -moz-border-radius: 25px;
      -ms-border-radius: 25px;
      border-radius: 25px;
      font-size: 23px !important;
      font-weight: 600 !important;
      width: 150px;
      height: 55px;
      text-align: center;
      line-height: 55px;
      background-color: #364F75 !important;
      color: #ffffff !important;
   }

   .donate-form .radio-img{
      line-height: 45px;
      height: 45px;
   }
   .donate-form .form-group.radio-img {
      margin-bottom: 0px !important;
   }
   .border-box{
      border: 1px solid rgba(241, 91, 67, 0.25);
      -webkit-border-radius: 25px;
      -moz-border-radius: 25px;
      -ms-border-radius: 25px;
      border-radius: 25px;
      padding: 25px;
      width: 100%;
   }

   .payment-detail .container {
      border: 1px solid rgba(241, 91, 67, 0.25);
      -webkit-border-radius: 25px;
      -moz-border-radius: 25px;
      -ms-border-radius: 25px;
      border-radius: 25px;
   }
   .card-input-element {
      display: none;
   }
   .card-input:hover {
      cursor: pointer;
   }
   .card-input-element:checked + .card-input {
      box-shadow: 0 0 1px 1px #364f75;
      background-color: #f8f9fa;
   }

   .iconbox-container .iconbox-border img {
      height: 90px;
   }

   .payment-detail .form-group{
      margin-bottom: 15px !important;
   }

   .payment-detail .payment-account {
      -webkit-border-radius: 25px;
      -moz-border-radius: 25px;
      -ms-border-radius: 25px;
      border-radius: 25px;
   }

</style>
@endsection
@section('content')
<div id="loading" style="display: none;"></div>
<main id="content" class="site-main bg-light-grey">
	<!-- Inner Banner html start-->
	<section class="inner-banner-wrap">
		<div class="inner-baner-container" style="background-image: url('../front/images/inner-banner-img-1.jpg');">
			<div class="container">
				<div class="inner-banner-content">
					<h1 class="inner-title">Registration</h1>
				</div>
			</div>
		</div>
	</section>
	
	<div class="donate-section">
		<div class="container">
      @if($type=='Verify')
      <div class="donate-detais form-inner"  id="verify_div">
         
                  <form method="post" class="donate-form newsletter"  method="post" action="{{route('spotVerify')}}">
                  @csrf  
                  <div class="donate-form form-inner">
                  <h3>Verification</h3>
                     <div class="row align-items-center justify-content-between">
                   
                        <div class="col-lg-9  form-group">
                        <label>Verification Code *</label>
                              <input type="text" class="form-control" id="verification_code" placeholder="Enter Verification code*" name="verification_code" value="">
                             @if(isset($error_message))
                              <span class="form-validation">{{$error_message}}</span> 
                              @endif
                        </div>
                  
                        <div class="col-lg-3  form-group text-center" id="verify-div">
                           
                           <button type="submit" id="verifyButton" class="button-round">Verify</button>
                           
                           
                        </div>
                       
                                                         
                     </div>
         </div>
         </form>
         </div>
         @elseif($type=='Register')
			<div class="donate-detais form-inner" id="registration_div" >
        
				<form class="donate-form"  method="post" action="{{route('projectSubmit')}}" id="registration-form">
					@csrf  
					<input type="hidden" id="category_sel" name="category_sel" value="">
					<input type="hidden" name="reg_submit" id="reg_submit" value="1">
					<div class="donate-detais form-inner">
						<div class="row align-items-center justify-content-between">
							<div class="col-lg-9">
								<div class="callback-content">
									<p>To complete the registration process for every group of ten students, it is mandatory to have at least one teacher or guardian present.<br>
                              The students in the team can be from mixed category; however, the presentation will be included in the category of student in the highest grade.
                               For example, if a team has participants from elementary and high school, the team will be included in the high school category for presentation/competition.
                              </br>For Other categories, the institute name is not mandatory
                              </p>
								</div>
							</div>
							<div class="col-lg-3">
								<div class="button-wrap" style="text-align:center;">
									<a class="button-round" id="guidelinesButton" data-bs-toggle="modal" href="#guidelinesModalModal"  data-backdrop="static" data-keyboard="false">Registration Guidelines</a> 
								</div>
							</div>
						</div>
					</div>
					<!-- <div class="row">
						<div class="col-lg-12 text-end mb-2">
							<a href="{{route('register.abstract')}}" style="text-decoration: underline" id="alreadyRegister">Already Registered ?</a>
						</div>
					</div> -->
					<div class="donate-detais form-inner">
						<h3>Your Details</h3>
						<div class="row form-wrap">
                  <div class="col-lg-6 col-md-6 col-sm-6 col-12 form-group">
								<select id="category" name="category" class="form-control-registration" required >
									<option value="" selected>Select Category</option>
                           @foreach($categories as $category)
									<option data-value="{{$category->id}}" value="{{$category->name}}">{{$category->name}}</option>
                           @endforeach
								
								</select>
                        <input type="hidden" id="category_id" name="category_id">
								<span class="form-validation" id="category1"></span>
							</div>
							<div class="col-lg-6 col-md-6 col-sm-6 col-12 form-group">
								<input type="text" name="school_name" id="school_name" placeholder="Institute name" class="form-control-registration" required>
								<span class="form-validation" id="school_name1"></span>
							</div>
							<div class="col-lg-6 col-md-6 col-sm-6 col-12 form-group" >
								<select class="form-control-registration" name="teacher"   id="teacher" required>
									<option value="teacher">Teacher</option>
									<option value="guardian">Guardian</option>
								</select>
								<span class="form-validation" id="teacher1"></span>
							</div>
							<div class="col-lg-6 col-md-6 col-sm-6 col-12 form-group" >
								<input class="form-control-registration" type="text" name="teacher_name" placeholder="Teacher/guardian name" required> 
								<span class="form-validation" id="teacher_name1"></span>
							</div>
							<div class="col-lg-6 col-md-6 col-sm-6 col-12 form-group" >
								<input class="form-control-registration" type="email" id="email" name="email" placeholder="E-mail*" required>
								<p style="font-size: 12px; color: #666; margin-top: 8px; margin-bottom: 0; line-height: 1.4;">Please use the same email address for registration and when making the payment.</p>
								<span class="form-validation" id="email1"></span>
							</div>
							<div class="col-lg-6 col-md-6 col-sm-6 col-12 form-group">
								<input class="form-control-registration" type="number" name="phone" placeholder="Phone*" required>
								<span class="form-validation" id="phone1"></span>
							</div>
							<div class="col-lg-6 col-md-6 col-sm-6 col-12 form-group">
								<select id="participation" name="participation" class="form-control-registration" required>
									<option value="" selected>Select Participation</option>
									<option value="1">Participants Only</option>
									<option value="2">Participation and presentation</option>
								</select>
								<span class="form-validation" id="participation1"></span>
							</div>
			
							<div class="col-lg-6 col-md-6 col-sm-6 col-12 form-group">
								<select class="form-control-registration" type="text" id="members" name="members" data-id="name2" placeholder="Select number of students *" required>
									<option value="" selected>Select number of students/Participants</option>
                           @for($index = 1; $index <= 10; $index++)
									<option value="{{ $index }}" >{{ $index }}</option>
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
					<div class="donate-detais  form-inner" id="students_div" style="display: none;">
						<h3>Student Details</h3>
						<div class="row form-wrap" id="students_data_div">
						</div>
					</div>
					<div class="donate-detais  form-inner" id="accompanyDetailsDiv" style="display: none;">
						<h3>Accompanying person <a data-bs-toggle="modal" href="#accompanyModal"  data-backdrop="static" data-keyboard="false" class="form-check-label label-title" >
                 
                  <label style="font-size: 14px">Guidelines</label>
                           </a></h3>
						<div class="row form-wrap">
							<div class="col-lg-12 primary">
                        <p>The accompanying person is anyone who wants to attend the conference apart from the team members and teacher supervisor
                        <br>For one person, the amount is<b> Rs 2500</b>, so the amount to be paid will be the number of persons<b> x Rs 2500</b>.</p>
                        <div class="row align-items-center">
                           <div class="col-lg-6 col-md-6 col-sm-6 col-12">
                              <h5 class="mb-2 mt-2">Do you like to add an accompany person</h5>
                           </div>
                           <div class="col-lg-3 col-md-6 col-sm-6 col-12">
                              <div class="form-wrap d-flex gy-5">
                                 <div class="form-group radio-img">
                                    <input type="radio"  name="accompanyYes" class="accompanyYes" id="accompanyYes" value="1">
                                    <label for="accompanyYes">
                                    Yes
                                    </label>
                                 </div>
                                 <div class="form-group radio-img">
                                    <input type="radio" checked name="accompanyYes" id="accompanyNo" class="accompanyYes" value="0">
                                    <label for="accompanyNo">
                                    No
                                    </label>
                                 </div>
                                 <input type="hidden" name="accompanyAdd" id="accompanyAdd" value="0">
                              </div>
                           </div>
                        </div>
							</div>
							<div class="mt-4" id="accom_main_div" style="display: none;">
								<div class="row">
									<div class="col-lg-6 col-md-6 col-sm-6 col-12 form-group">
										<label>No of Person</label>
										<select class="form-control-registration" type="text" id="no_accompany" name="no_accompany" data-id="no_accompany" placeholder="Select number of accompany *" required>
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
									<div class="col-lg-6 col-md-6 col-sm-6 col-12 form-group text-center">
										<label style="">Amount to be paid</label>
										<label class="amount-label">INR 2500</label>
									</div>
								</div>
								<div class="row mt-1" id="accompanyDiv">
									<div class="col-lg-6 col-md-6 col-sm-6 col-12 form-group">
										<label>Name</label>
										<input type="text" class="form-control-accompany" name="accompany_name[]" id="accompany_name"  data-id="accompany_name1" >
										<span class="form-validation" id="accompany_name1" data></span>
									</div>
									<div class="col-lg-6 col-md-6 col-sm-6 col-12 form-group">
										<label>Email</label>
										<input type="email" class="form-control-accompany" name="accompany_email[]" id="accompany_email"  data-id="accompany_email1">
										<span class="form-validation " id="accompany_email1" data></span>
									</div>
								</div>
								<div class="row pt-3">
									<h5>Choose your payment method</h5>
									<div class="col-lg-12 col-md-6 col-sm-6 col-12">
										<div class="row">
											<div class="event-page-section pb-0" >
												<div class="iconbox-container d-flex flex-wrap mt-1">
													<input type="hidden"  name="payment_method" id="payment_method" value="upi">
													<div class="iconbox-item-2 d-flex flex-wrap ">
														<label for="upiOpt">
															<input type="radio" name="a" id="upiOpt" class="card-input-element" checked>
															<div class="iconbox-border d-flex align-items-center card-input p-3">
                                                <img src="{{url('front/images/VPA.png')}}">
																<div class="iconbox-content">
																	<p>Accepting UPI Payments with  Phonepe, Gpay and Paytm at UPI ID renjuj29-2@okicici
																	</p>
																</div>
															</div>
														</label>
													</div>
													<div class="iconbox-item-2 d-flex flex-wrap">
														<label for="bankOpt">
															<input type="radio" name="a" id="bankOpt" class="card-input-element">
															<div class="iconbox-border d-flex align-items-center card-input p-3">
                                                <img src="{{url('front/images/bank-transfer.png')}}">
																<div class="iconbox-content">
																	<p>Money can be sent through bank transfer, for details click here.</p>
																</div>
															</div>
														</label>
													</div>
												</div>
											</div>
										</div>
										<div class="payment-detail" id="bankDiv" style="display:none;">
                                 <h5>Payment Details</h5>
                                 <div class="d-flex p-4 container">
                                    <div class="col-md-7 pe-2">
                                       <p>After payment through bank account share payment details here</p>
                                       <div class="col-lg-12 col-md-6 col-sm-6 col-12 form-group"  >
                                          <label>Payment Reference</label>
                                          <input type="text" name="payment_reference_bank" class="form-control-accompany_bank" id="payment_reference_bank"  data-id="payment_reference_bank_span" >
                                          <span class="form-validation" id="payment_reference_bank_span" data></span>
                                       </div>
                                       <div class="col-lg-12 col-md-6 col-sm-6 col-12 form-group" >
                                          <label>Payment Date</label>
                                          <input type="text" name="payment_date" id="payment_date" class="form-control-accompany_bank payment_date" placeholder="Payment Date">
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
                                    <div class="col-md-7 pe-2">
                                       <p>After making payment online share payment details here.</p>
                                       <div class="col-lg-12 col-md-6 col-sm-6 col-12 form-group"  >
                                          <label>Payment Reference</label>
                                          <input type="text" name="payment_reference_upi" class="form-control-accompany_upi" id="payment_reference_upi"  data-id="payment_reference_upi_span" >
                                          <span class="form-validation" id="payment_reference_upi_span" data></span>
                                       </div>
                                       <div class="col-lg-12 col-md-6 col-sm-6 col-12 form-group" >
                                          <label>Payment Date</label>
                                          <input type="text" name="payment_date_upi" id="payment_date_upi" class="form-control-accompany_upi payment_date" placeholder="Payment Date">
                                          <span class="form-validation" id="payment_date_upi1" data></span>
                                       </div>
                                    </div>
                                    <div class="col-md-5 bg-light p-3 ms-2 payment-account">
                                       <h4>UPI IDs</h4>
                                       <div class="row pb-1">
                                          <!-- <div class="col-sm-4 text-sm-right">GPay</div> -->
                                          <!-- <div class="col-sm-1 text-center d-none d-sm-inline">:</div> -->
                                          <div class="col-sm-7  fw-bold"> renjuj29-2@okicici</div>
                                       </div>
                                    </div>
                                 </div>
										</div>
										{{-- <div class="row border-box " id="upiDiv">
											<div class="col-md-7">
												<div class="col-lg-12 col-md-6 col-sm-6 col-12 form-group" >
													<p>Lorem Ipsum is simply dummy text of the printing simply dummy text of the printing</p>
													<label>Payment Reference</label>
													<input type="text" name="payment_reference_upi" class="form-control-accompany_upi" id="payment_reference"  data-id="payment_reference_upi_span" >
													<span class="form-validation" id="payment_reference_upi_span" data></span>
												</div>
												<div class="col-lg-12 col-md-6 col-sm-6 col-12 form-group" >
													<label>Payment Date</label>
													<input type="text" name="payment_date_upi" id="payment_date_upi" class="form-control-accompany_upi payment_date" placeholder="Payment Date">
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
					<div class="donate-detais form-inner">
						<div class="row form-wrap">
							<span class="form-validation" id="agree1"></span>
                     
							<div class="col-md-6 form-check">
								<input  class="form-check-input form-control-registration" type="checkbox" name="agree" data-id="agree1" id="read_agree" required />
								<label for="read_agree" class="form-check-label label-title"> I have read <a href="javascript:;" id="termsLink">Registration Guidelines</a>.</label>
							</div>
                     <span class="form-validation" id="validform1"></span>
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
		    <div>
				<div class="row" id="register_detail" style="display: none;">
				</div>
			</div>
         @endif
		</div>
	</div>
	<div id="loading"></div>
</main>
<div class="modal fade" id="accompanyModal" tabindex="-1" role="dialog" aria-labelledby="accompanyModalLabel" aria-hidden="true">
	<div class="modal-dialog modal-xl modal-dialog-centered" role="document">
		<div class="modal-content register-content">
			<div class="modal-header">
				<h3 class="modal-title" id="exampleModalLabel2">Guidelines</h3>
				<button type="button" id="accompany-modal-close" class="close" data-dismiss="modal" aria-label="Close"> <span aria-hidden="true">&times;</span> </button> 
			</div>
			<div class="modal-body">
				<div class="col-xs-12 col-sm-12">
					<!-- <h3>Registration Guidelines</h3> -->
					<div class="card-body">
						<!-- <div class="sub-title">Eligibility</div> -->
                  <ul>
                           <li> If a group is accompanied by more than one teacher or a mentor or a parent, they will be charged 
                            <ul>
                                <li>Rs 2,000 per head till February 29</li>
                                <li>Rs 2,500 per head till March 31,</li>
                                <li> Rs 3,000 per head till June 30 and</li>
                                <li>Spot registration Rs 4000 per head.</li>
                                <li>(After completion of registration process, participants are required to send the scan copy of the registration fee receipt or transaction proof to us through mail).</li>
                            </ul>
                           </li> 
                           <li>Payment should be in the form of either one of the following:
                            <ul>
                                <li> Bank transfer to following account<br>
                                  <p>- BANK NAME:- Federal Bank</p>
                                  <p>-  Branch:  Ayoor</p>
                                  <p>- Account Number: 17310100084924</p>
                                  <p>-  IFSC Code:- FDRL0001731</p>
                                </li>
                           <li>UPI Number – 8086200020<br>UPI id –renjuj29-2@okicici
                           </li>
                           <li>On-site: Only one of the following credit cards<br>
                              <p>1. VISA　2. MasterCard   </p></li>
                            </ul>
                           </li>
                           <li>Registered members are asked to intimate about the registration immediately through email.</li>

                        </ul>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>
<div class="modal fade" id="guidelinesModal" tabindex="-1" role="dialog" aria-labelledby="guidelinesModalLabel" aria-hidden="true">
	<div class="modal-dialog modal-xl modal-dialog-centered" role="document">
		<div class="modal-content register-content">
			<div class="modal-header">
				<h3 class="modal-title" id="exampleModalLabel2">Registration Guidelines</h3>
				<button type="button" id="modal-close" class="close" data-dismiss="modal" aria-label="Close"> <span aria-hidden="true">&times;</span> </button> 
			</div>
			<div class="modal-body">
				<div class="col-xs-12 col-sm-12">
					<!-- <h3>Registration Guidelines</h3> -->
					<div class="card-body">
						<div class="sub-title">Eligibility</div>
						<p>To be eligible to register for the conference, participants must meet the following criteria:</p>
						<ul>
							<li>Be between the ages of 5 and 18 years old on the date of the conference.</li>
							<ul>
								<li>Be between the ages of 5 and 18 years old on the date of the conference.</li>
								<li> Presentations will be held under 6 categories.</li>
								<li> GYRA School category        -- Grade 6 to 12</li>
								<li> Elementary category         -- Grade 1 to 4</li>
                        <li> Mid School category         -- Grade 5 to 7</li>
								<li> High School category        -- Grade 8 to 12  </li>
								<li> Others category             -- No age/grade limit</li>
								<li> Special School category     -- No age/grade limit</li>
							</ul>
							<li>Be able to understand and follow the conference proceedings in English.</li>
							<li>Have the permission of their parents or guardians to attend the conference.</li>
						</ul>
						<div class="sub-title" style="padding-top:20px;">Registration Process</div>
						<ul>
							<li> The registration process should be done by the teacher/guardian. All student participants should register online under the teacher/guardian as a team. A teacher/guardian is needed for each team. Each team can have 1-10 students.
								We are not accepting any registrations by email, Phone call or postal mail. Only Online Registration method is accepted.
							</li>
							<li>   Once the registration is submitted successfully an automated email will be generated and sent to the registered e-mail address of the teacher/guardian.</li>
							<li>   <strong>Any modifications will be not accepted after the registration.</strong></li>
							<li>   It is mandatory to register for the conference submits the abstract for poster/presentation.</li>
							<li>   The registration is free of cost for the team (1-10 students and 1 teacher/guardian). Additional accompanying persons are required to pay the registration fees.</li>
							<ul>
								<li> Rs 2,000/person till February 29,</li>
								<li> Rs 2,500/person till March 31, </li>
								<li> Rs 3,000/person till June 30 and  </li>
								<li> Spot registration Rs 4000/person </li>
							</ul>
							<li>Payment should be in the form of either one of the following:</li>
							<ul>
								<li>  Bank transfer to following account</li>
								<ul style="list-style: none;">
									<li>   - BANK NAME</li>
									<li>   - Address:</li>
									<li>    - Branch:</li>
									<li>   - Beneficiary:</li>
									<li>   - Account Number: </li>
								</ul>
							</ul>
							<li>On-site: Only one of the following credit cards</li>
							<ul style="list-style: none;">
								<li>  1. VISA　2. MasterCard </li>
							</ul>
						</ul>
						<div class="sub-title" style="padding-top:20px;"> Registration Deadline</div>
						<ul>
                     <li>The registration deadline for the conference will be March 31st, 2024. Participants are encouraged to register early to ensure their place at the conference.</li>
                     <li>Accompanying persons are asked to intimate about the registration and submit the screenshot of transaction via email. contactustoircc@gmail.com</li>
                     <li>Limited seats are available for the conference and participants are allotted on a first come-first served basis.</li>
                     <li>In case of any queries, write to contactustoircc@gmail.com or call 8086200020. Enquiries through email will be preferred.</li>
                  </ul>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>
@endsection
@section('script')
<!-- <script src="{{ url("image/tagsinput/bootstrap-tagsinput.js")}}"></script> -->
<script src="{{ url("front/js/sweet-alert.js")}}"></script>
<script>
   var date = new Date();
  var today = new Date(date.getFullYear(), date.getMonth(), date.getDate());
  $('.payment_date').datepicker({
      defaultDate: new Date(),
      dateFormat: "dd-mm-yy",
});
$( '.payment_date').datepicker( 'setDate', today );

  $(document).on('click', '.reg_submit', function() {
         $('#reg_submit').val(2);
            
   });
   $(document).on('click', '.reg_submit', function() {
         $('#reg_submit').val(1);
            
   });
   $(document).on('change', '#bankOpt', function() {
      if($('#bankOpt').is(':checked')){
            $('#payment_method').val('bank')
            $('#bankDiv').show();
            $('#upiDiv').hide();
         }
            
   });
   $(document).on('change', '#upiOpt', function() {
      if($('#upiOpt').is(':checked')){
            $('#payment_method').val('upi');
            $('#upiDiv').show();
            $('#bankDiv').hide()
         }
            
   });
   
   $(document).on('change', '.accompanyYes', function() {
      console.log($(this).val());
         if($(this).val()==1){
            $('#accom_main_div').show();
            $('#accompanyAdd').val(1);
         }
         else{
            $('#accom_main_div').hide();
            $('#accompanyAdd').val(0)
         }
            
   });
   
  $(document).on('change', '#category', function() {
    $('#members').val('');
    if($('#category').val()=='Others'){
      $('#school_name').prop('required',false);
      $('#school_name').removeClass('form-control-registration');
      $('#school_name1').text('');
    }
    else{
      $('#school_name').prop('required',true);
      $('#school_name').addClass('form-control-registration');
    }
    $('#category_id').val($('#category').find(':selected').attr('data-value'));
    $('#members').trigger('change');
  });
  $(document).on('click', '.btn_submit_now', function() {
         $('#abstract_submit_div').hide();
         $('#abstract_div').show();
         $('#register_success').hide();
         $('#register_detail').hide();
         $('#registration_div').hide();
         $('#selection_div').hide();
         $('#abstract_head').show();
         console.log('ffff');         
   });
 
   $(document).on('click', '#btn_not_now', function() {
         $('#selection_div').hide();
   });
   $(document).on('click', '.register_now', function() {
         $('#registration_div').show();
         $('#abstract_div').hide();
         $('#selection_div').hide();
         $('#abstract_head').hide();
               
   });
   $(document).on('click', '#abstract_submit', function() {
         $('#abstract_div').show();
         $('#registration_div').hide();
         $('#selection_div').hide();
         $('#abstract_head').show();
         console.log('ffff');         
   });
   
   $(document).on('click', '#guidelinesButton', function() {
         $('#guidelinesModal').modal('show');
         console.log('ffff');         
   });
   $(document).on('click', '#abstractGuidelinesButton', function() {
         $('#abstractGuidelinesModal').modal('show');
         console.log('ffff');         
   });
   $(document).on('click','#termsLink', function() {
         $('#guidelinesModal').modal('show');
         console.log('ffff');         
   });
      $(document).on('click', '#accompany-modal-close', function() {
          $('#accompanyModal').modal('hide');
                 
      });
   $(document).on('click', '#abstract-modal-close', function() {
          $('#abstractGuidelinesModal').modal('hide');
                 
      });
   $(document).on('click', '#modal-close', function() {
          $('#guidelinesModal').modal('hide');
                 
      });
      //////////////////// copy paste validation////////////////
   $(document).on("paste keydown", "#abstractDescription", function (e) {
      var $this = $(this);
      var max = 250;
   
      setTimeout(function () {
         var text = $this.val();
         len = $('#abstractDescription').val().split(/[\s]+/);
         var text = $this.val();
         if (len.length >= max) {
            e.preventDefault();
            var trimmed = $('#abstractDescription').val().split(/[\s]+/, 250).join(" ");
            $('#abstractDescription').val(trimmed + " ");
         }
      }, 100);
   
   });
   
   $(document).ready(function () {

  
   $(document).on('click', '.submit', function () {
      $('#submit_type').val($(this).data('value'));
   });
   $(document).on('input', '.form-control-registration', function (obj) {
   
      name = $(this).attr('name') + 1;
      $('#' + name).text('');
      $('#validform1').text('');
   
   });
   $(document).on('input', '.form-control-student', function (obj) {
   
      name = $(this).data('id');
   $('#' + name).text('');
   $('#validform1').text('');

 });
 $(document).on('input', '.form-control-accompany', function (obj) {
   name = $(this).data('id');
   $('#' + name).text('');
   $('#validform1').text('');
});
 $(document).on('input', '.form-control-accompany_upi', function (obj) {
   name = $(this).data('id');
   $('#' + name).text('');
   $('#validform1').text('');
});
$(document).on('input', '.form-control-accompany_bank', function (obj) {
   name = $(this).data('id');
   $('#' + name).text('');
   $('#validform1').text('');
});

   // $("input.tagsinput-example").tagsinput('items');
   $('#reason').click(function () {
      console.log($(this).val());
      if ($(this).val() == 6) {
         $('#other_reason').show();
      } else {
         $('#other_reason').hide();
      }
   });
   $('.nav-bar').click(function () {
      $('.nav-bar').removeClass('active');
      $(this).addClass('active');
   });
   $(document).on('click', '#submitFirst', function () {
      var valid = 1
   
      if (number > 0 && valid == 1) {
         $.ajax({
            headers: {
               'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            type: 'POST',
            data: {
               number: number,
               email: email
            },
            dataType: 'JSON',
            url: '{{ route('ajaxLoad') }}',
            success: function (response) {
               if (response.error == 0) {
                  //$('#submit_div').css({'display':'none'}); 
                  $('#students_div').css({
                     'display': 'block'
                  });
                  $('#students_data_div').html(response.html);
                  $('#accompanyDetailsDiv').show();
                  
                  $('.success-message').html('');
               } else {
                  $('.success-message').css("color", "red");
                  $('.success-message').html(response.message);
                  $('.success-message').delay(50).fadeOut('slow');
               }
   
            }
         });
      }
   
   });
   $(document).on('change', '#members', function () {
      console.log($(this).val());
      var valid = 1
      $('.form-control-registration').each(function (i, obj) {
         console.log($(this).val());
         //console.log(obj.val());
         if ($(this).val() == '' && obj.name != 'author_name[]') {
            name = obj.name + 1;
            $('#' + name).text('field is required');
            valid = 0
         }
   
      });
      var number = $('#members').val();
      var email = $('#email').val();
      if (validateEmail(email)) {
   
      } else {
         valid = 0;
         $('#email1').text('email not valid');
      }
      var number = $('#members').val();
      var category=$('#category').find(':selected').attr('data-value');
      if (number > 0) {
         $.ajax({
            headers: {
               'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            type: 'POST',
            data: {
               number: number,
               category:category
            },
            dataType: 'JSON',
            url: '{{ route('ajaxLoad') }}',
            success: function (response) {
               if (response.error == 0) {
                  $('#submit_div').css({
                     'display': 'none'
                  });
                  $('#students_div').css({
                     'display': 'block'
                  });
                  $('#students_data_div').html(response.html);
                  $('#accompanyDetailsDiv').show();
                  $('.success-message').html('');
               } else {
                  $('.success-message').css("color", "red");
                  $('.success-message').html(response.message);
               }
   
            }
         });
      }
      else{
            $('#students_div').css({
               'display': 'none'
            });
      }
   
   });
   $(document).on('change', '#no_accompany', function () {
     
      var number = $('#no_accompany').val();
      $('.amount-label').text('INR '+number*2500);
      if (number > 0) {
         $.ajax({
            headers: {
               'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            type: 'POST',
            data: {
               number: number
               },
            dataType: 'JSON',
            url: '{{ route('ajaxAccompany') }}',
            success: function (response) {
               if (response.error == 0) {
                  $('#accompanyDiv').css({
                     'display': 'block'
                  });
                  $('#accompanyDiv').html(response.html);
                 
               }
   
            }
         });
      }
      else{
            $('#students_div').css({
               'display': 'none'
            });
      }
   
   });
   
   var status = 0;
   $(document).on('click', '.reg_register', function () {
     
      event.preventDefault();
  
      status = 1;
      valid = 1
      var valid = 1
      var url = $('#registration-form').attr('action');
     
      if($("#read_agree").prop('checked') == true){
       valid =1;
      }
      else{
         $('#agree1').text('field is required');
            valid = 0 ;
      }
      
      $('.form-control-registration').each(function (i, obj) {
  
         if ($(this).val() == '' && obj.name != 'author_name[]') {
            name = obj.name + 1;
            $('#' + name).text('field is required');
            valid = 0
         }
   
      });

      $('.form-control-student').each(function (i, obj) {
    
         if ($(this).val() == '') {
   
            name = $(this).data('id');
            $('#' + name).text('field is required');
            valid = 0
         }
     
      });
      if($('#accompanyYes').is(':checked')){
         $('.form-control-accompany').each(function (i, obj) {
               if ($(this).val() == '') {

                  name = $(this).data('id');
                  $('#' + name).text('field is required');
                  valid = 0
               }
             });
         if($("#payment_method").val()=='bank'){
            $('.form-control-accompany_bank').each(function (i, obj) {
               if ($(this).val() == '') {
                  name = $(this).data('id');
                  $('#' + name).text('field is required');
                  valid = 0
               }
             });
         }
         if($("#payment_method").val()=='upi'){
            $('.form-control-accompany_upi').each(function (i, obj) {
               if ($(this).val() == '') {

                  name = $(this).data('id');
                  $('#' + name).text('field is required');
                  valid = 0
               }
             });
         }

         
   }
      var number = $('#members').val();
      var email = $('#email').val();
      if (validateEmail(email)) {
   
      } else {
         valid = 0
         $('#email1').text('email not valid');
      }
    
      if (valid == 1) {
         $('#loading').show();
         $("html, body").animate({ scrollTop: 0 }, "slow");
         var url = $('#registration-form').attr('action');
       
         var datos =  $('#registration-form').serialize();
         console.log(datos);
         $.ajax({
            headers: {
               'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            type : 'POST',
            url : 'url',
            data : $('#registration-form').serialize(), 
            url: url,
            dataType: 'json',
              
            success: function (result) {
               $('#loading').hide();
            if (result.error == 0) {
               $('.success-message').html(result.message);
               swal("Success", result.message, "success");
               $('.success-message').css("color", "#1ed104");
               $('#registration-form')[0].reset();

               if ($('#submit_type').val() == 'submit') {
                  $('.form-validation').text('');
                 
               } else {

                  $('#register_detail').html('');
                  $('#register_success').show();
                  $('#register_detail').show();
                  $('#registration_div').hide();
                  $('#abstract_div').hide();
                  $('#abstract_submit_div').hide();
                  $("#abstract-form :input").prop('readonly', false);
                  $('#verify-div').hide();
                  $('#register_number').val(result.data.id);
                  $('#registered_id').val(result.data.register_num);
                  $('#register_detail').html(result.html);
                 
                  // $('#register-header').hide();
                  //     $('#abstract-header').show();
               }
   
            } else if (result.error == 1) {
               $('.success-message').html(result.message);
               $('.success-message').delay(50).fadeOut('slow');
               swal("Error", result.message, "error");
               //$('.success-message').css("color", "#1ed104");
   
            }
            status = 0;
         }
   
   });
   }
   else{
      $('#validform1').text('Manadory field are empty');
      }
   });
   
   });
  
   function validateEmail(email) {
   var pattern = /^([\w-\.]+)@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.)|(([\w-]+\.)+))([a-zA-Z]{2,4}|[0-9]{1,3})(\]?)$/;
   
   return $.trim(email).match(pattern) ? true : false;
   }  
</script>
@endsection