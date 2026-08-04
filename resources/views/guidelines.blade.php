@extends('layouts.app')
@section('head')
<style>

</style>
@endsection
@section('content')
<main class="site-main">
            <!-- Inner Banner html start-->
   <section class="inner-banner-wrap">
      <div class="inner-baner-container" style="background-image: url(front/images/inner-banner-img-1.jpg);">
         <div class="container">
            <div class="inner-banner-content">
               <h1 class="inner-title">Guidelines</h1>
            </div>
         </div>
      </div>
   </section>

   <!-- Inner Banner html end-->
   <div class="career-detail-section">
      <div class="career-detail-inner">
         <div class="container">
            <div class="section-head  mt-4">
               {{-- <h2 class="section-title">Will announce soon..</h2> --}}
               <div class="row">
                  <div class="col-lg-8 primary right-sidebar">
                     <div class="event-detail-container">
                        <div class="row">  
                           <div class="col-lg-6" > 
                              <h3>Registration Guidelines</h3>
                           </div>
                        </div>
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
                                       <li>The early bird registration deadline for the conference will be March 31st, 2026. Participants are encouraged to register early to ensure their place at the conference.</li>
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
                       </div>
                       <h3>Abstract Guidelines</h3>
                       <div class="card-body">
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
                  <div class="col-lg-4 secondary">
                           <div class="sidebar">
                                          
                              <div class="widget widget-bg information-content widget-bg-image text-center" style="background-image: url(front/images/unbound-img23.jpg);">
                                 <h3>View Abstract template</h3>
                                 <p>An abstract is an outline/brief summary of your paper and your whole project.</p>
                                 <a id="abstractTemplate" data-bs-toggle="modal" href="#abstractTemplateModal"  data-backdrop="static" data-keyboard="false" class="button-round">VIEW</a>
                              </div>
                             
                        @if ($user && $abstract)
                           
                        @elseif ($user)
                           <div class="widget-bg upload-widget text-center">
                              <div class="widget-icon">
                                 <i class="fas fa-file-invoice"></i>
                              </div>
                              <h3>Send us your Abstract</h3>
                              <a href="{{route('accounts.dashboard')}}" class="button-round">Add Abstract</a>
                           </div>
                        @else
                           <div class="widget-bg upload-widget text-center">
                              <div class="widget-icon">
                                 <i class="fas fa-file-invoice"></i>
                              </div>
                              <h3>Send us your Abstract</h3>
                              <span class="or-style">OR</span>
                              @if(optional($settings->where('code','registration_status')->first())->value==1)
                                 <a href="{{route('login')}}" class="button-round">Register</a>
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
</main>
<div class="modal fade" id="abstractTemplateModal" tabindex="-1" role="dialog" aria-labelledby="abstractTemplateModalLabel" aria-hidden="true">
  
  <div class="modal-dialog modal-xl modal-dialog-centered" role="document">
 <div class="modal-content register-content">
 <div class="modal-header">
 <h3 class="modal-title" id="exampleModalLabel2">Abstract Template</h3>
 <button type="button" class="close modal-close" data-dismiss="modal" aria-label="Close"> <span aria-hidden="true">&times;</span> </button> 
 </div>
 <div class="modal-body">

 
 <iframe src="{{url('front/pdf/abstract-template1205.pdf')}}" style="width:100%; height:600px;" frameborder="0"></iframe>
          
 </div>
 
 
 </div>
 </div>
</div>
@endsection
@section('script')
<script>
$(document).ready(function() {
   
   $(document).on('click', '#abstractTemplate', function() {
          $('#abstractTemplateModal').modal('show');
               
      });
      $(document).on('click', '.modal-close', function() {
          $('#abstractTemplateModal').modal('hide');
                 
      });

    });
</script>
@endsection
