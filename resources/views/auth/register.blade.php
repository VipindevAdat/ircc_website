@extends('layouts.app')

@section('head')
    <style>
        .login-container {
            display: flex;
            justify-content: center;
            gap: 30px;
            padding: 40px 0;
            flex-wrap: wrap;
        }
        .login-form-section {
            flex: 1;
            max-width: 50%;
            background: #fff;
            border-radius: 12px;
            padding: 30px 25px;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
            border-top: 4px solid #F15B43;
        }
        .form-login {
            margin-bottom: 20px;
        }
        /* .form-control-registration {
            width: 100%;
            padding: 10px 15px;
            border-radius: 25px;
            border: 1px solid #ddd;
            font-size: 16px;
        } */
        .button-login {
            width: 100%;
            background-color: #F15B43;
            color: #fff;
            border: none;
            padding: 12px 20px;
            border-radius: 25px;
            font-size: 16px;
            cursor: pointer;
            text-align: center;
        }
        .text-end {
            text-align: end;
        }
        .text-center {
            text-align: center;
        }
        .form-validation{
            color:red;
        }
        /* Password toggle styling */
        .password-toggle {
            z-index: 10;
        }
        .password-toggle:hover {
            color: #F15B43 !important;
        }
        input[type="password"],
        input[type="text"][id*="password"] {
            padding-right: 40px !important;
        }
        /* Responsive Design */
        @media (max-width: 768px) {
            .login-container {
                flex-direction: column;
            }
            .login-tutorial,
            .login-form-section {
                max-width: 100%;
            }
        }
        #loading {
    background: url('./front/images/loading.gif') no-repeat center center;
    position: fixed;
    width: 100%;
    height: 100vh;
    z-index: 999;
}
    </style>
@endsection

@section('content')
    <div id="loading" style="display: none;"></div>
    <main id="content" class="site-main bg-light-grey">
        <section class="inner-banner-wrap">
            <div class="inner-baner-container" style="background-image: url('./front/images/inner-banner-img-1.jpg');">
                <div class="container">
                    <div class="inner-banner-content">
                        <h1 class="inner-title">Login</h1>
                    </div>
                </div>
            </div>
        </section>

        <div class="container">
            <div class="login-container">
                <!-- Video Tutorial Section -->
                <div class="login-form-section">
                    <h3>How to Register</h3>
                    <div>
                        <div id="guideline">
                            <ul>
                                <li>The person who is registering <strong>must</strong> accompany the participants to the conference.</li>
                                <li>Maximum 10 participants under one registration including students and guests.</li>
                                <li>All communications will be through the registered email and phone number.</li>
                                <li>Only one abstract can be submitted from one registration.</li>
                                <li>Check the <a href="javascript:;" id="guidelineLink">guidelines</a> for more details</li>
                            </ul>
                        </div>
                        <!-- IRCC'26 Registration Payment Workflow Video -->
                        <!-- TODO: Replace iN6Ezglg5r8 with the new IRCC'26 registration payment workflow video ID -->
                        <!-- Current video (iN6Ezglg5r8) is for IRCC'25 - needs to be updated for IRCC'26 -->
                        <div style="position: relative; padding-bottom: 56.25%; height: 0; overflow: hidden; max-width: 100%; border-radius: 8px;">
                            <iframe style="position: absolute; top: 0; left: 0; width: 100%; height: 100%;" 
                                    src="https://www.youtube.com/embed/iN6Ezglg5r8" 
                                    frameborder="0" 
                                    allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture" 
                                    allowfullscreen>
                            </iframe>
                        </div>
                        <p style="font-size: 12px; color: #999; margin-top: 10px; text-align: center;">
                            <em>Note: This video shows the IRCC'25 registration flow. A new video for IRCC'26 will be available soon.</em>
                        </p>
                    </div>
                </div>

                <!-- Login Form Section -->
                <div class="login-form-section" id="login-layout" style="display: none;">
                    <h3>Login</h3>
                    <form method="POST" action="{{ route('login') }}">
                        @csrf
                        <!-- Email Field -->
                        <div class="row form-wrap">
                                             
                        <div class="col-lg-12 col-md-6 col-sm-6 col-12 form-group">
                            <label for="email">Email</label>
                            <input type="email" id="email" name="email"
                                   class="@error('email') is-invalid @enderror"
                                   placeholder="Enter your email"
                                   value="{{ old('email') }}"
                                   >
                            @error('email')
                            <span class="form-validation" style="color: red;">{{ $message }}</span>
                            @enderror
                        </div>
                        </div>

                        <!-- Password Field -->
                        <div class="row form-wrap">
                        <div class="col-lg-12 col-md-6 col-sm-6 col-12 form-group">
                            <label for="password">Password</label>
                            <input type="password" id="password" name="password"
                                   class="@error('password') is-invalid @enderror"
                                   placeholder="Enter your password"
                                   >
                            @error('password')
                            <span class="form-validation">{{ $message }}</span>
                            @enderror
                        </div>
                        </div>
                        <!-- Forgot Password -->
                        <div class="form-login text-end">
                            <a href="{{ route('password.request') }}" class="text-decoration-none">Forgot Password?</a>
                        </div>

                        <!-- Submit Button -->
                        <div class="form-group">
                            <button type="submit" class="button-login">Log In</button>
                        </div>
                    </form>

                    <div class="form-group text-center">
                        <p>New User? <a  class="text-decoration-none register-btn" style="cursor: pointer;">Register</a></p>
                    </div>
                </div>
                <!-- Register Form Section -->
             
                <div class="login-form-section" id="register-layout">
                
                    @if(optional($settings->where('code','registration_status')->first())->value==1)
                    <form class="donate-form"  method="post" action="{{route('register')}}" id="registration-form">
                        <input type="hidden" name="otp" id="verify_otp">
                            @csrf
                            @honeypot
                            
                            <h3>Register</h3>
                                <div class="row form-wrap">
                                    <div class="col-lg-6 col-md-6 col-sm-6 col-12 form-group">
                                        <input class="form-control-registration" type="text" name="first_name" placeholder="First name" value="{{ old('first_name') }}" required>
                                        @error('first_name')
                                        <span class="form-validation" id="first_name1">{{ $message }}</span>
                                        @enderror
                                        <span class="form-validation" id="first_name1"></span>
                                    </div>
                                    <div class="col-lg-6 col-md-6 col-sm-6 col-12 form-group">
                                        <input class="form-control-registration" type="text" name="last_name"  placeholder="Last name" value="{{ old('last_name') }}" required>
                                        @error('last_name')
                                        <span class="form-validation" id="last_name1">{{ $message }}</span>
                                        @enderror
                                        <span class="form-validation" id="last_name1"></span>
                                    </div>
                                    <div class="col-lg-12 col-md-6 col-sm-6 col-12 form-group">
                                        <input class="form-control-registration" type="email" name="email" placeholder="E-mail*" value="{{ old('email') }}"  id="register-email" required>
                                        <p style="font-size: 12px; color: #666; margin-top: 8px; margin-bottom: 0; line-height: 1.4;">Please use the same email address for registration and when making the payment.</p>
                                        <span class="form-validation" id="email1"></span>
                                    </div>
                                    <div class="col-lg-12 col-md-6 col-sm-6 col-12 form-group">
                                        <input type="text" name="institution_name" placeholder="Institution name (e.g., JF Kennedy High School, G. M. Walters Middle School)" class="form-control-registration" value="{{ old('institution_name') }}"  required>
                                        @error('institution_name')
                                        <span class="form-validation" id="institution_name1">{{ $message }}</span>
                                        @enderror
                                        <span class="form-validation" id="institution_name1"></span>
                                    </div>
                                    <input type="hidden" name="ircc_form" value="{{Crypt::encrypt('IRCC2026')}}">
                                    <div class="col-lg-12 col-md-6 col-sm-6 col-12 form-group">
                                        <input class="form-control-registration" type="number" name="phone" placeholder="Phone Number*" value="{{ old('phone') }}" required>
                                        @error('phone')
                                        <span class="form-validation" id="phone1">{{ $message }}</span>
                                        @enderror
                                        <span class="form-validation" id="phone1"></span>
                                    </div>
                                    <div class="col-lg-12 col-md-6 col-sm-6 col-12 form-group">
                                        @php
                                            $selectedHearAbout = old('hear_about_us', $hearAboutUs ?? '');
                                        @endphp
                                        <select class="form-control-registration" name="hear_about_us" id="hear_about_us" required style="width: 100%; padding: 10px 15px; border-radius: 25px; border: 1px solid #ddd; font-size: 16px; background-color: #fff;">
                                            <option value="">How did you hear about us? *</option>
                                            <option value="MANCA" {{ $selectedHearAbout == 'MANCA' ? 'selected' : '' }}>MANCA</option>
                                            <option value="GYRA" {{ $selectedHearAbout == 'GYRA' ? 'selected' : '' }}>GYRA</option>
                                            <option value="STEM4Girls" {{ $selectedHearAbout == 'STEM4Girls' ? 'selected' : '' }}>STEM4Girls</option>
                                            <option value="Bay Area Champions" {{ $selectedHearAbout == 'Bay Area Champions' ? 'selected' : '' }}>Bay Area Champions</option>
                                            <option value="PTA" {{ $selectedHearAbout == 'PTA' ? 'selected' : '' }}>PTA</option>
                                            <option value="Other" {{ $selectedHearAbout == 'Other' ? 'selected' : '' }}>Other</option>
                                        </select>
                                        @error('hear_about_us')
                                        <span class="form-validation" id="hear_about_us1">{{ $message }}</span>
                                        @enderror
                                        <span class="form-validation" id="hear_about_us1"></span>
                                    </div>
                                    <div class="col-lg-12 col-md-6 col-sm-6 col-12 form-group" id="hear_about_other_field" style="display: none;">
                                        <input class="form-control-registration" type="text" name="hear_about_other" id="hear_about_other" placeholder="Please specify" value="{{ old('hear_about_other') }}">
                                        <span class="form-validation" id="hear_about_other1"></span>
                                    </div>
                                    {{-- <div class="col-lg-4 col-md-6 col-sm-6 col-12 form-group text-end">
                                        <button type="button" href="#" class="button-round" id="sendOtp">Verify</button>
                                    </div> --}}
                                    <div class="col-lg-12 col-md-6 col-sm-6 col-12 form-group" id="otpDIv"  style="display: none;">
                                    <div class="row form-wrap">  
                                    <div class="col-lg-12 col-md-6 col-sm-6 col-12 form-group"> 
                                    <input  type="text" name="otp" placeholder="OTP*" id="enterOtp">
                                    <span class="form-validation" id="enterOtp1"></span>
                                     </div>
                                   
                                     <div class="col-lg-12" > 
                                        <div class="alert alert-info otpClass" role="alert" >
                                    
                                        </div>
                                     </div>
                                       </div>
                                    </div>
                                    <div class="col-lg-6 col-md-6 col-sm-6 col-12 form-group" style="position: relative;">
                                        <input class="form-control-registration" type="password" name="password" id="register-password" placeholder="Password" value="{{ old('password') }}" required>
                                        <button type="button" class="password-toggle" data-target="register-password" style="position: absolute; right: 10px; top: 12px; cursor: pointer; background: none; border: none; color: #666; font-size: 18px; padding: 5px; z-index: 100; outline: none;">
                                            <i class="fas fa-eye" id="register-password-eye"></i>
                                        </button>
                                        @error('password')
                                        <span class="form-validation" id="password1">{{ $message }}</span>
                                        @enderror
                                        <span class="form-validation" id="password1"></span>
                                    </div>
                                    <div class="col-lg-6 col-md-6 col-sm-6 col-12 form-group" style="position: relative;">
                                        <input class="form-control-registration" type="password" name="password_confirmation" id="register-password-confirmation" placeholder="Confirm Password" value="{{ old('password_confirmation') }}" required>
                                        <button type="button" class="password-toggle" data-target="register-password-confirmation" style="position: absolute; right: 10px; top: 12px; cursor: pointer; background: none; border: none; color: #666; font-size: 18px; padding: 5px; z-index: 100; outline: none;">
                                            <i class="fas fa-eye" id="register-password-confirmation-eye"></i>
                                        </button>
                                        @error('password_confirmation')
                                        <span class="form-validation" id="password_confirmation1">{{ $message }}</span>
                                        @enderror
                                        <span class="form-validation" id="password_confirmation1"></span>
                                    </div>
                                    <div class="mb-3">
                                        <div class="col-md-12 form-check">
                                            <input  class="form-check-input form-control-registration" type="checkbox" name="agree" data-id="agree1" id="read_agree" required />
                                            <label for="read_agree" class="form-check-label label-title"> I have read <a href="javascript:;" id="termsLink">Guidelines</a>.</label>
                                        </div>
                                        <span class="form-validation" id="agree1"></span>
                                    </div>
                                </div>
                                {{-- <div class="">
                                    <div class="">
                                        <span class="form-validation" id="agree1"></span>
    
                                        <div class="col-md-12 form-check">
                                            <input  class="form-check-input form-control-registration" type="checkbox" name="agree" data-id="agree1" id="read_agree" required />
                                            <label for="read_agree" class="form-check-label label-title"> I have read <a href="javascript:;" id="termsLink">Registration Guidelines</a>.</label>
                                        </div>
                                        <span class="form-validation" id="validform1"></span>
                                    </div>
                                </div> --}}
                                {{-- <div class="form-login text-end">
                            <a href="{{ route('password.request') }}" class="text-decoration-none">Forgot Password?</a>
                        </div> --}}

                                <div class="col-lg-12 col-md-6 col-sm-6 col-12 form-group">
                                    <label for="captcha_answer" style="display:block; margin-bottom:6px;">Anti-spam check: What is {{ $captchaA ?? 0 }} + {{ $captchaB ?? 0 }}? *</label>
                                    <input class="form-control-registration" type="number" name="captcha_answer" id="captcha_answer" placeholder="Your answer" value="{{ old('captcha_answer') }}" required>
                                    @error('captcha_answer')
                                    <span class="form-validation" id="captcha_answer1">{{ $message }}</span>
                                    @enderror
                                    <span class="form-validation" id="captcha_answer1"></span>
                                </div>
                                <div class="form-group">
                            <button type="submit" class="button-login reg_register">Register</button>
                        </div>
                        </form>
                    @else
                    <div class="donate-form form-inner" style="margin-bottom: 20px;!important;">
                        <div class="row align-items-center justify-content-between">

                            <div class="col-lg-12">
                                <div class="event-content text-center">
                                    <h3>Registration Closed !!!</h3>

                                    <div class="button-wrap mt-5">
                                        <a id="btn_not_now"  href="{{route('/')}}" class="outline-round ">Back</a>
                                    </div>
                                </div>
                            </div>

                        </div>
                    </div>
                    @endif

                    <div class="form-group text-center">
                        <p>Already Registered? <a class="text-decoration-none login-btn"  style="cursor: pointer;">Login</a></p>
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
                                    <li><strong style="color: #F15B43;">Presentations will be held under 4 categories.</strong></li>
                                        <ul>
                                            <li><strong style="color: #F15B43;">Elementary School -- 1st Grade to 5th Grade</strong></li>
                                            <li><strong style="color: #F15B43;">Middle School -- 6th Grade to 8th Grade</strong></li>
                                            <li><strong style="color: #F15B43;">High School -- 9th Grade to 12th Grade</strong></li>
                                            <li><strong style="color: #F15B43;">Special School -- No age restrictions</strong></li>
                                        </ul>
                                    <li>Be able to understand and follow the conference proceedings in English.</li>
                                    <li>Have the permission of their parents or guardians to attend the conference.</li>
                                </ul>
                                
                                <div class="sub-title" style="padding-top:20px;">Registration Deadline</div>
                                <ul>
                                    <li>The registration will be officially open from February 7th, 2026.</li>
                                    <li>
                                        The early bird registration deadline for the conference will be
                                        <del>March 31st, 2026</del>
                                        &nbsp;&rarr;&nbsp;
                                        <strong>April 15th, 2026</strong>.
                                        Participants are encouraged to register early to ensure their place at the conference.
                                    </li>
                                    <li>
                                        Abstract submissions will close on
                                        <del>April 15th, 2026</del>
                                        &nbsp;&rarr;&nbsp;
                                        <strong>April 30th, 2026</strong>.
                                    </li>
                                    <li>Limited seats are available for the conference and participants are allotted on a first come-first served basis.</li>
                                    <li><strong style="color: #F15B43;">The registration fee is defined as follows. Early bird registration fee: $150 (ends on March 31st). Regular registration fee: $200 (from April 1st)</strong></li>
                                    <li>Payment should be made only through the website.</li>
                                    <li>Please avoid duplicate registration. No refunds will be entertained.</li>
                                </ul>
                                
                                <div class="sub-title" style="padding-top:20px;">Registration Process</div>
                                <ul>
                                    <li>The registration process should be done by a teacher/guardian. The registered user is considered a participant of the conference.</li>
                                    <li>Each registration can have maximum 10 additional participants including students and guests. If you have more than 10 participants, make additional registrations.</li>
                                    <li>A team submitting an abstract can have 1-10 students. All student participants should register online under the teacher/guardian as a team. A teacher/guardian is mandatory for each team.</li>
                                    <li>We are not accepting any registrations by email, Phone call, or postal mail. Only the Online Registration method is accepted.</li>
                                    <li>Once the registration is submitted successfully an automated email will be generated and sent to the registered e-mail address of the teacher/guardian. Please check your spam folder if you do not find the email. Also, add ignite@ircc.world to your contacts to get all updates properly.</li>
                                    <li>It is mandatory to register for the conference to submit the abstract for a poster/oral presentation.</li>
                                    <li>The scientific committee will review the submitted abstract and determine whether it qualifies for a poster or oral presentation.</li>
                                    <li>The decision of the scientific committee regarding the presentation format will be final.</li>
                                    <li><strong style="color: #F15B43;">Note: Submitting a paper is not necessary to attend the conference.</strong></li>
                                </ul>

                                <div class="sub-title" style="padding-top:20px;">Inquiries</div>
                                <ul>
                                    <li>In case of any queries, write to ignite@ircc.world or call. Inquiries through email will be preferred.</li>
                                    <li>Phone calls regarding IRCC'26 are directed to +1 (617) 201-4330. Phone inquiries should be limited between 9 AM to 4 PM Pacific.</li>
                                </ul>

                                <div class="sub-title" style="padding-top:20px;">Disclaimer</div>
                                <p>By attending the conference, participants consent to be photographed or recorded for use on the organization's YouTube channel and social media. Please inform the organizers in advance if you have any concerns.</p>
                                
                                <h3 style="margin-top: 30px; margin-bottom: 15px;">Abstract Guidelines</h3>
                                <p>An abstract in a conference is a summary of a research study or presentation, highlighting its objectives, methods, and key findings, used to inform attendees and reviewers about the content of the work.</p>
   
                                <p>All students who want to present their research work at IRCC'26 are required to submit an abstract through the website.</p>
                                
                                <div class="sub-title" style="padding-top:20px;">Abstract preparation & submission</div>
                                <ul>
                                   <li>An abstract should be of maximum 250 words.</li>
                                   <li>The abstract should have a title and references.</li>
                                   <li>An appropriate topic domain should be selected for each abstract.</li>
                                   <li>Abstracts can only be submitted after registration through the website.</li>
                                   <li>At least one student who is an author of the abstract should be registered for the conference.</li>
                                   <li>The teacher/guardian who is registering the team will be the corresponding author for the abstract. All communications regarding the abstract will be sent to the registered email address.</li>
                                   <li>A registered user can only submit one abstract using one email ID.</li>
                                   <li>No option to upload a document will be provided. All abstracts should be submitted through the web portal.</li>
                                   <li>Abstracts submitted after the deadline will not be considered.</li>
                               </ul>
                               
                               <div class="sub-title" style="padding-top:20px;">Abstract judging criteria</div>
                               <ul>
                                   <li>Abstracts will be evaluated for their quality by the scientific review committee.</li>
                                   <li>A limited number of abstracts will be selected for oral/platform presentation at the conference in each category. Other eligible abstracts will be considered for poster presentation.</li>
                                   <li>The abstracts will be judged based on the research methodology rather than the research area.</li>
                                   <li>The category of the presentation will be determined by the category of the student authors registered for the conference. The category of the seniormost student author will define the presentation category.</li>
                                   <li>The abstracts that do not meet the guidelines will be rejected. Please follow the guidelines properly.</li>
                               </ul>
                                    
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </main>
@endsection

@section('script')
    <script src="{{ url("front/js/sweet-alert.js")}}"></script>
    <script>
            

        $(document).ready(function () {
            // Show register form by default on register page
            $('#register-layout').show();
            $('#guideline').show();
            $('#login-layout').hide();
            
            // Handle "How did you hear about us" dropdown
            $('#hear_about_us').on('change', function() {
                if ($(this).val() === 'Other') {
                    $('#hear_about_other_field').show();
                    $('#hear_about_other').prop('required', true);
                } else {
                    $('#hear_about_other_field').hide();
                    $('#hear_about_other').prop('required', false);
                    $('#hear_about_other').val('');
                }
            });
            
            // Check on page load if "Other" is already selected or if a value is pre-selected
            if ($('#hear_about_us').val() === 'Other') {
                $('#hear_about_other_field').show();
                $('#hear_about_other').prop('required', true);
            } else if ($('#hear_about_us').val() !== '') {
                // If a value is pre-selected (from query parameter), ensure the field is visible
                $('#hear_about_other_field').hide();
                $('#hear_about_other').prop('required', false);
            }
          
            $('#register-email').on('blur', function(){ 
                var myData = $(this).val();
                if(myData.length>10){
                $.ajax({
                        headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                        },
                        type : 'POST',
                        url : '{{ route("check-email") }}',
                        dataType: 'json',
                        data:{email:myData},
                    success: function (result) {
                       console.log(result.errors);
                        if(result.exists==true){
                            console.log('true');
                            jQuery.each(result.errors, function(index, item) {
                            $('#' +index+1).text(item);
                        });   
                        }                                          
                    },
                    error:function(result){
                        jQuery.each(result.responseJSON.errors, function(index, item) {
                            $('#' +index+1).text(item);
                        });
                       
                    }
            });
        }
    });
            var otpVerified=false;
            $(document).on('click', '#sendOtp', function (event) {
                event.preventDefault();
                $('#otpDIv').hide();
                $('#verify_otp').val('');
                $('.otpClass').text('');
             if(validatePhone()){
                $('#loading').show();
                  $.ajax({
                        headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                        },
                        type : 'GET',
                        url : '{{ route("send-otp") }}',
                        dataType: 'json',
                    success: function (result) {
                        console.log(result);
                        $('#otpDIv').show();
                        $('#verify_otp').val(result.otp);
                        $('.otpClass').text(result.success);
                        $('#sendOtp').text('Resend')
                        $('#loading').hide();
                                                                    
                    },
                    error:function(result){
                        $('#loading').hide();
                       
                    }
            
                });
            }
              
            });
            $('#enterOtp').on('keyup', function(){ 

                var myData = $(this).val();
                if(myData.length % 4 == 0){
                    $('#loading').show();
                     if ($('#verify_otp').val()==$(this).val()){
                        $('#sendOtp').html('<i class="fa fa-check" aria-hidden="true"></i> Verified');
                        otpVerified=true;
                        $('#otpDIv').hide();
                      }  
                      else{
                         $('#enterOtp1').text("OTP doesn't match");
                      }
                      $('#loading').hide();
                }
            });

            $(document).on('click', '.register-btn', function () {
                console.log('click');
                    $('#register-layout').show();
                    $('#guideline').show();
                    $('#login-layout').hide();
            });
            $(document).on('click', '.login-btn', function () {
                    $('#register-layout').hide();
                    $('#guideline').hide();
                    $('#login-layout').show();
            });

            $(document).on('click', '.reg_register', function (event) {
                event.preventDefault();
                var isValid = true;
                $('#loading').show();
                $('.form-validation').text('');

                if($("#read_agree").prop('checked') == true){
                    isValid =true;
                }
                else{
                    $('#agree1').text('Read the guidelines and check I have read the guidelines.');
                    isValid = false ;
                }

                $('.form-control-registration').each(function (i, obj) {
                    // Skip validation for hear_about_other if it's hidden
                    if ($(this).attr('name') === 'hear_about_other' && $('#hear_about_other_field').is(':hidden')) {
                        return true; // Skip this field
                    }
                    
                    if ($(this).val() == '' ) {
                        name = obj.name + 1;
                        var fieldName = $(this).attr('name');

                        var readableName = fieldName
                            .toLowerCase()
                            .split('_')
                            .map(word => word.charAt(0).toUpperCase() + word.slice(1))
                            .join(' ');

                        $('#' + name).text(readableName + ' is required');
                        isValid = false

                        if($("#read_agree").prop('checked') == true){
                            isValid =true;
                        }
                        else{
                            $('#agree1').text('Read the guidelines and check I have read the guidelines.');
                            isValid = false ;
                        }
                    }
                });
                
                // Special validation for "hear_about_us" - if "Other" is selected, require the other field
                if ($('#hear_about_us').val() === 'Other' && $('#hear_about_other').val().trim() === '') {
                    $('#hear_about_other1').text('Please specify how you heard about us');
                    isValid = false;
                }

                var url = $('#registration-form').attr('action');
                if(!validatePhone()){
                    isValid=false;
                }
                // if(!otpVerified){
                //     $('#phone1').text('Phone number is not verified');
                // }
                if (isValid) {
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
                        console.log(result);
                        $('#loading').hide();
                        
                        // Check if registration was actually successful
                        // Only treat as failure if status is explicitly false
                        if (result.status === false) {
                            // Registration failed on server side
                            if (result.message) {
                                alert(result.message || 'Registration failed. Please try again.');
                            }
                            window.location = '{{ url('/register-failure')}}';
                        } else {
                            // Registration successful (status is true or undefined, both mean success)
                            window.location = '{{ url('/register-success')}}';
                        }
                                             
                    },
                    error:function(result){
                        $('#loading').hide();
                        
                        // Handle validation errors
                        if (result.responseJSON && result.responseJSON.errors) {
                            jQuery.each(result.responseJSON.errors, function(index, item) {
                                $('#' +index+1).text(item);
                            });
                        }
                        
                        // Handle general error messages
                        if (result.responseJSON && result.responseJSON.message) {
                            alert(result.responseJSON.message);
                            // If it's a registration failure (not validation), redirect to failure page
                            if (result.status === 422 && result.responseJSON.message.includes('Registration failed')) {
                                window.location = '{{ url('/register-failure')}}';
                            }
                        } else {
                            // Generic error
                            alert('Registration failed. Please try again or contact support.');
                            window.location = '{{ url('/register-failure')}}';
                        }
                    }
            
                });

                } else {
                    $('#loading').hide();
                    console.log('Form validation failed. Not submitting.'); 
                }
            });

            function validateEmail(email_register) {
                var pattern = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
                return pattern.test(email);
            }
        });

        $(document).on('input', '.form-control-registration', function () {
            var name = $(this).attr('name'); 
            var errorFieldId = name + '1'; 

            $('#' + errorFieldId).text('');

            $('#validform1').text('');
        });

        $(document).on('click', '#guidelineLink', function() {
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

        function validateEmail(email) {
            var pattern = /^([\w-\.]+)@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.)|(([\w-]+\.)+))([a-zA-Z]{2,4}|[0-9]{1,3})(\]?)$/;
            return !!$.trim(email).match(pattern);
        }
       function validatePhone(){
        var phoneValid=true;
        var phone = $('input[name="phone"]').val();
                if (phone.trim() === '') {
                    $('#phone1').text('Phone number is required.');
                    phoneValid = false;
                } else if (!/^\d{10}$/.test(phone)) {
                    if (!/^\d+$/.test(phone)) {
                        $('#phone1').text('Phone number should only contain numbers.');
                    } else if (phone.length !== 10) {
                        $('#phone1').text('Phone number must be exactly 10 digits.');
                    }
                    phoneValid = false;
                } else {
                    $('#phone1').text('');
                    phoneValid=true;
                }
                return phoneValid;
       }
       
       // Toggle password visibility - make it global
       window.togglePassword = function(inputId) {
           var input = document.getElementById(inputId);
           if (!input) {
               console.error('Input field not found: ' + inputId);
               return;
           }
           
           var eyeIcon = document.getElementById(inputId + '-eye');
           if (!eyeIcon) {
               console.error('Eye icon not found: ' + inputId + '-eye');
               return;
           }
           
           if (input.type === 'password') {
               input.type = 'text';
               eyeIcon.classList.remove('fa-eye');
               eyeIcon.classList.add('fa-eye-slash');
           } else {
               input.type = 'password';
               eyeIcon.classList.remove('fa-eye-slash');
               eyeIcon.classList.add('fa-eye');
           }
       };
       
       // Use event delegation for password toggles
       $(document).on('click', '.password-toggle', function(e) {
           e.preventDefault();
           var targetId = $(this).data('target');
           if (targetId) {
               window.togglePassword(targetId);
           }
       });
    </script>
@endsection

