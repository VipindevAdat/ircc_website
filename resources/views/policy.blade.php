@extends('layouts.app')
@section('head')
<style>

</style>
@endsection
@section('content')
<main id="content" class="site-main">
            <!-- Inner Banner html start-->
            <section class="inner-banner-wrap">
               <div class="inner-baner-container" style="background-image: url(front/images/inner-banner-img-1.jpg);">
                  <div class="container">
                     <div class="inner-banner-content">
                        <h1 class="inner-title">Privacy Policy</h1>
                     </div>
                  </div>
               </div>
            </section>
            <!-- Inner Banner html end-->
            <div class="policy-page-section">
               <div class="policy-inner">
                  <div class="container">
                     <h2>Privacy Policy </h2>
                     <p>Effective Date: January 28, 2025</p>
                     <h4>1. Introduction</h4>
                     <p>Welcome to the International Research Conference for Children (IRCC’26) website, accessible at <a href="https://www.ircc.world/" rel="external nofollow noopener" target="_blank">https://www.ircc.world/. </a>  We are committed to safeguarding your privacy and ensuring the protection of your personal information. This Privacy Policy outlines how we collect, use, disclose, and protect your data when you interact with our website and services.</p>
                     <h4>2. Information We Collect</h4>
                     <p>We may collect the following types of information:</p>
                     <ul>
                     <li>
                     <p><strong>Personal Information: </strong> When you register for the conference, submit inquiries, or participate in our events, we may collect personal details such as your name, email address, phone number, institutional affiliation, and other relevant information.</p>
                     </li>
                     <li>
                     <p><strong>Non-Personal Information: </strong> We automatically gather certain non-identifiable information when you visit our website, including your IP address, browser type, operating system, referring URLs, and pages viewed. This data helps us analyze website usage and improve user experience.</p>
                     </li>
                     </ul>
                     <h4>3. How We Use Your Information</h4>
                     <p>The information we collect is utilized for the following purposes:</p>
                     <ul>
                     <li>
                     <p><strong>Conference Participation:</strong> To process registrations, manage event logistics, and communicate essential updates related to the conference.</p>
                     </li>
                     <li>
                     <p><strong>Communication: </strong> To respond to your inquiries, provide information about our events and services, and send newsletters or promotional materials, if you have opted to receive them.</p>
                     </li>
                     <li>
                     <p><strong>Website Improvement: </strong> To analyze website traffic and usage patterns, enabling us to enhance our website's functionality and content.</p>
                     </li>
                     <li>
                     <p><strong>Legal Compliance</strong> To comply with applicable laws, regulations, and legal processes.</p>
                     </li>
                     </ul>

                     <!-- <h3>Collecting and Using Your Personal Data</h3> -->
                     <h4>4. Information Sharing and Disclosure</h4>
                     
                     <p>We respect your privacy and do not sell or rent your personal information to third parties. However, we may share your information in the following circumstances:</p>
                     <ul>
                     <li>
                     <p><strong>Service Providers: </strong> We may engage trusted third-party service providers to assist in operating our website, conducting our events, or providing services on our behalf. These providers have access to your information solely to perform specific tasks and are obligated to maintain its confidentiality.</p>
                     </li>
                     <li>
                     <p><strong>Legal Requirements:  </strong> We may disclose your information if required to do so by law or in response to valid requests by public authorities.   
                     
                     </p>
                     </li>
                     </ul>
                     <h4>3. How We Use Your Information</h4>
                     <p>The information we collect is utilized for the following purposes:</p>
                     <ul>
                     <li>
                     <p><strong>Conference Participation:</strong> To process registrations, manage event logistics, and communicate essential updates related to the conference.</p>
                     </li>
                     <li>
                     <p><strong>Communication: </strong> To respond to your inquiries, provide information about our events and services, and send newsletters or promotional materials, if you have opted to receive them.</p>
                     </li>
                     <li>
                     <p><strong>Website Improvement: </strong> To analyze website traffic and usage patterns, enabling us to enhance our website's functionality and content.</p>
                     </li>
                     <li>
                     <p><strong>Legal Compliance</strong> To comply with applicable laws, regulations, and legal processes.</p>
                     </li>
                     </ul></p>
                     </li>
                     </ul>
                     <h4>5. Data Security</h4>
                     <p>We implement appropriate technical and organizational measures to protect your personal information against unauthorized access, alteration, disclosure, or destruction. While we strive to use commercially acceptable means to protect your data, no method of transmission over the internet or electronic storage is 100% secure.</p>
                     <h4>6. Cookies and Tracking Technologies</h4>
                     <p>Our website uses cookies and similar technologies to enhance user experience and analyze website performance. You can control the use of cookies through your browser settings. For more information, please refer to our [Cookie Policy].</p>
                    
                    <h4>7. Third-Party Links</h4>
                     <p>Our website may contain links to external sites not operated by us. We are not responsible for the content or privacy practices of these third-party websites. We encourage you to review their privacy policies before providing any personal information.</p>
                     <h4>8. Children's Privacy</h4>
                     <p>Protecting the privacy of children is paramount. Our conference is designed for students from elementary to high school. We collect personal information from children only with parental consent and solely for conference-related purposes.</p>
                     <h4>9. Your Rights</h4>
                     <p>Depending on your jurisdiction, you may have the following rights regarding your personal information:</p>
                     <ul>
                     <li><strong>Access:</strong> Request a copy of the personal data we hold about you.</li>
                     <li><strong>Correction: </strong> Request correction of any inaccurate or incomplete data.</li>
                     <li><strong>Deletion: </strong> Request deletion of your personal data, subject to legal obligations.</li>
                     <li><strong>Correction: </strong> Object to the processing of your data under certain circumstances..</li>
                     </ul>
                     <p>To exercise these rights, please contact us using the information provided below.</p>
                     <h4>10. Changes to This Privacy Policy</h4>

                     <p>We may update this Privacy Policy periodically to reflect changes in our practices or legal requirements. We will notify you of any significant changes by posting the new policy on our website with the updated effective date.</p>
                    
                     <h4>11. Refund Policy</h4>
                     <p>The registration fee is non-refundable.</p>
                     <h4>12. Contact Us</h4>
                     <p>If you have any questions or concerns about this Privacy Policy or our data practices, please contact us at:</p>
                     
                     <ul>
                     <li><strong>Email: </strong>  ignite@ircc.world</li>
                     <li><strong>Phone:</strong>+1 6172014330</li>
                     </ul>
                   
                     <p>By using our website and participating in our events, you consent to the terms outlined in this Privacy Policy.</p>
                  </div>
               </div>
            </div>
         </main>
@endsection
@section('script')
<script>
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
                 // swal("Success", response.message, "success");
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