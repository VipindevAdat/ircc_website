@extends('layouts.app')
@section('head')
<style>
.form-validation{
   color:red;
}

/* Contact Details Spacing Fixes */
.contact-details-list {
   margin-top: 30px !important;
   padding-top: 0 !important;
}

.contact-details-list ul li {
   margin-bottom: 30px !important;
   padding: 25px 35px 28px 35px !important;
}

.contact-details-list ul li:last-child {
   margin-bottom: 0 !important;
}

.contact-details-list .details-content {
   padding-left: 25px !important;
   line-height: 1.8 !important;
   margin-bottom: 0 !important;
   padding-bottom: 0 !important;
}

.contact-details-list .details-content span {
   display: block !important;
   line-height: 1.8 !important;
   margin-bottom: 0 !important;
   padding-bottom: 0 !important;
}

.contact-details-list .details-content a {
   display: block !important;
   margin-bottom: 2px !important;
   line-height: 1.4 !important;
}

.contact-details-list .details-content > span[style*="color:red"] {
   margin-bottom: 2px !important;
   line-height: 1.4 !important;
}

.contact-details-list .details-content > div[style*="display:flex"] {
   margin-top: 0 !important;
}
</style>
@endsection
@section('content')
<main id="content" class="site-main">
            <!-- Inner Banner html start-->
            <section class="inner-banner-wrap">
               <div class="inner-baner-container" style="background-image: url({{ url("front/images/inner-banner-img-1.jpg")}}">
                  <div class="container">
                     <div class="inner-banner-content">
                        <h1 class="inner-title">Contact us</h1>
                     </div>
                  </div>
               </div>
            </section>
            <!-- Inner Banner html end-->
            <!-- contact form html start -->
            <div class="contact-page-section">
               <div class="contact-form-inner">
                  <div class="container">
                     <div class="row">
                        <div class="col-md-6">
                           <div class="contact-detail-container">
                              <div class="section-heading">
                                 <div class="sub-title">CONTACT US</div>
                                 <h2 style="font-size:44px;">Feel free to contact & reach us !!</h2>
                                 <p>Please submit your queries and our representative will contact you within 2 business days.</p>
                              </div>
                              <div class="contact-details-list" style="margin-top: 30px;">
                                 <ul>
                                    <li style="margin-bottom: 0;">
                                       <span class="icon">
                                          <i class="fas fa-map-marker-alt"></i>
                                       </span>
                                       <div class="details-content" style="line-height: 1.8; padding-left: 25px; margin-bottom: 0; padding-bottom: 0;">
                                          <!-- <h3>Head office address :</h3> -->
                                          <span style="display: block; line-height: 1.8; margin-bottom: 0; padding-bottom: 0;">
                                          {!!trim(optional($settings->where('code','address')->first())->value)!!}</span>
                                       </div>
                                    </li>
                                    
                                 </ul>
                              </div>
                              <div class="contact-details-list" style="margin-top: 15px;">
                                 <ul>
                                    <li style="margin-bottom: 0;">
                                       <span class="icon">
                                          <i class="fas fa-phone-volume"></i>
                                       </span>
                                       <div class="details-content" style="line-height: 1.8; padding-left: 25px;">
                                        
                                          <span style="color:red;font-size:12px;display:block;margin-bottom:2px;line-height:1.4;">Phone inquiries to be limited between 9:00AM to 5:00PM PST</span>
                                          <!-- <a href="tel:9846730308"><span >9846730308 </span></a> <span>:  Dr. Priyankar Bhushan </span><br> -->
                                          <a href="tel:+1 6172014330" style="display:block;margin-bottom:2px;line-height:1.4;"><span >+1 6172014330 </span></a>
                                          <a href="tel:+1 4085642087" style="display:block;margin-bottom:2px;line-height:1.4;"><span >+1 4085642087 </span></a>
                                          @if(!empty($socialLinks['whatsapp']))
                                          <div style="display:flex;align-items:center;gap:8px;">
                                             <a aria-label="Chat on WhatsApp" href="{{ $socialLinks['whatsapp'] }}" target="_blank" rel="noopener noreferrer" style="display:inline-block;line-height:1.6;"><span >+1(971) 4008683 </span></a>
                                             <a aria-label="Chat on WhatsApp" href="{{ $socialLinks['whatsapp'] }}" target="_blank" rel="noopener noreferrer"><i class="fab fa-whatsapp" style="color:#05b171;font-weight: 600;font-size:18px;"></i></a>
                                          </div>
                                          @endif
                                          <!-- <a aria-label="Chat on WhatsApp" href="https://wa.me/7034547485" target="_blank"><span>7034547485 </span></a> <span>:  Dr. Finosh Thankam </span><a aria-label="Chat on WhatsApp" href="https://wa.me/7034547485" target="_blank"><i class="fab fa-whatsapp" style="color:#05b171;font-weight: 600;"></i></a><br> -->
                                         
                                        
                                       </div>
                                    </li>
                                   
                                 </ul>
                              </div>
                           </div>
                        </div>
                        <div class="col-md-6">
                           <div class="contact-from-wrap" style="background-image: url(front/images/unbound-img5.jpg);">
                           <form class="contact-from"  method="post" action="{{route('contact-submit')}}" id="contact-from">
                                <p>
                                    <input class="contact-form-control" type="text" name="name" id="name" placeholder="Enter Name*">
                                    <span class="form-validation" id="name1"></span>
                                 </p>
                                 <p>
                                    <input class="contact-form-control" type="email" name="email" id="email" placeholder="Email*">
                                    <span class="form-validation" id="email1"></span>
                                 </p>
                                 <p>
                                    <input class="contact-form-control" type="number" name="phone" id="phone" placeholder="Phone*">
                                    <span class="form-validation" id="phone1"></span>
                                 </p>
                                 <p>
                                    <input class="contact-form-control" type="text" name="subject" id="subject" placeholder="Subject*">
                                    <span class="form-validation" id="subject1"></span>
                                 </p>
                                
                                 <p class="width-full">
                                    <textarea class="contact-form-control" rows="8" name="message" id="message" placeholder="Message*"></textarea>
                                    <span class="form-validation" id="message1"></span>
                                 </p>
                                 <p class="width-full">
                                    <input type="submit" name="submit" value="Submit Now">
                                    <span id="successContact" style="display: none;color:#ffff">Submitted Successfully</span>
                                 </p>
                              </form>
                              <div class="overlay"></div>
                           </div>
                          
                        </div>
                        <div class="col-md-6">
                           <div class="contact-detail-container" style="padding-right: 0px !important;">
                           <div class="contact-details-list">
                                 <ul>
                          
                                    <li>
                                       <span class="icon">
                                          <i class="fas fa-envelope-open-text"></i>
                                       </span>
                                       <div class="details-content">
                                          <a href="mailto:ignite@ircc.world">ignite@ircc.world</a><br>
                                          <a href="mailto:gyraglobal@gmail.com">gyraglobal@gmail.com</a><br>
                                          @php
                                          $support_email=optional($settings->where('code','support_email')->first())->value;
                                          if($support_email) {
                                              $emails=explode(',',$support_email);
                                              foreach($emails as $email) {
                                                  $email = trim($email);
                                                  if($email && $email !== 'ignite@ircc.world' && $email !== 'gyraglobal@gmail.com') {
                                                      echo '<a href="mailto:' . $email . '">' . $email . '</a><br>';
                                                  }
                                              }
                                          }
                                          @endphp
                                          <br>                             
                                       </div>
                                    </li>
                                 </ul>
                              </div>
                           </div>
                        </div>
                     </div>
                  </div>
               </div>
               
               <div class="map-section">
               <iframe src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3168.2189780981616!2d-121.89761112368143!3d37.4319314318344!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x808fced665280dab%3A0xde525bbd2f28ab2c!2sIndia%20Community%20Center%20(ICC)!5e0!3m2!1sen!2sus!4v1762801993067!5m2!1sen!2sus" width="400" height="300" style="border:0;" allowfullscreen="" loading="lazy" referrerpolicy="no-referrer-when-downgrade"></iframe>
               </div>
            </div>
            <!-- contact form html end -->
</main>
@endsection
@section('script')
<script src="{{ url("front/js/sweet-alert.js")}}"></script>
<script>
    $(document).on('input', '.contact-form-control', function (obj) {
   
      name = $(this).attr('name') + 1;
      $('#' + name).text('');

   });
    $('#contact-from').submit(function (event) {
      event.preventDefault();
      var valid = 1
      $('.contact-form-control').each(function (i, obj) {
         console.log($(this).val());
         //console.log(obj.val());
         if ($(this).val() == '') {
            name = obj.name + 1;
            $('#' + name).text('field is required');
            valid = 0
         }
   
      });
      var email = $('#email').val();
      console.log(email);
      if (validateEmail(email)) {
   
      } else {
         valid = 0;
         $('#email').text('email not valid');
      }
      if (valid == 1) {
         var url = $(this).attr('action');
         $.ajax({
            headers: {
               'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            type: 'POST',
            url: url,
            data: new FormData(this),
            dataType: 'json',
            contentType: false,
            cache: false,
            processData: false,
            success: function (response) {
   
               if (response.error == 0) {
                  $('.form-validation').text('');
                  $('#contact-from')[0].reset();
                  $('#successContact').show();
                 swal("Success", response.message, "success");
               } else {
   
                  swal("Error", "Something went wrong", "error");
               }
            }
   
         });
      }
   });
   function validateEmail(email) {
   var pattern = /^([\w-\.]+)@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.)|(([\w-]+\.)+))([a-zA-Z]{2,4}|[0-9]{1,3})(\]?)$/;
   
   return $.trim(email).match(pattern) ? true : false;
   }  
</script>
@endsection