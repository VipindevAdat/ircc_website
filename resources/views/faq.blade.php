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
            <h1 class="inner-title">Faq</h1>
            </div>
        </div>
    </div>
</section>
<!-- Inner Banner html end-->
<!-- faq html start -->
<div class="faq-page-section">
    <div class="container">
        <div class="faq-page-container">
            <div class="row">
            <div class="col-lg-6">
                <div class="section-head">
                    <div class="sub-title">ANY QUESTIONS</div>
                    <h2 class="section-title">FREQUENTLY ASKED QUESTIONS</h2>
                    <p>Aperiam sociosqu urna praesent, tristique, corrupti condimentum asperiores platea ipsum ad arcu. Nostrud. Esse? Aut nostrum, ornare quas provident laoreet nesciunt odio voluptates etiam, omnis.</p>
                </div>
                <div id="accordion-tab-one" class="accordion-content" role="tablist">
                    <div id="accordion-A" class="card tab-pane fade show active" role="tabpanel" aria-labelledby="accordion-A">
                        <div class="card-header" role="tab" id="qus-A">
                        <h5 class="mb-0">
                            <a data-bs-toggle="collapse" href="#collapse-one" aria-expanded="true" aria-controls="collapse-one">
                                How we became best among others ?
                            </a>
                        </h5>
                        </div>
                        <div id="collapse-one" class="collapse show" data-bs-parent="#accordion-tab-one" role="tabpanel" aria-labelledby="qus-A">
                        <div class="card-body">
                            Lorem ipsum dolor sit amet, consectetur adipiscing elit. Ut elit tellus, luctus nec ullamcorper mattis, pulvinar dapibus leo.
                        </div>
                        </div>
                    </div>
                    <div id="accordion-B" class="card tab-pane" role="tabpanel" aria-labelledby="accordion-B">
                        <div class="card-header" role="tab" id="qus-B">
                        <h5 class="mb-0">
                            <a class="collapsed" data-bs-toggle="collapse" href="#collapse-two" aria-expanded="false" aria-controls="collapse-two">
                                What we offer to you ?
                            </a>
                        </h5>
                        </div>
                        <div id="collapse-two" class="collapse" data-bs-parent="#accordion-tab-one" role="tabpanel" aria-labelledby="qus-B">
                        <div class="card-body">
                            Lorem ipsum dolor sit amet, consectetur adipiscing elit. Ut elit tellus, luctus nec ullamcorper mattis, pulvinar dapibus leo.
                        </div>
                        </div>
                    </div>
                    <div id="accordion-C" class="card tab-pane" role="tabpanel" aria-labelledby="accordion-C">
                        <div class="card-header" role="tab" id="qus-C">
                        <h5 class="mb-0">
                            <a class="collapsed" data-bs-toggle="collapse" href="#collapse-three" aria-expanded="true" aria-controls="collapse-three">
                                How we provide services for you ?
                            </a>
                        </h5>
                        </div>
                        <div id="collapse-three" class="collapse" data-bs-parent="#accordion-tab-one" role="tabpanel" aria-labelledby="qus-C">
                        <div class="card-body">
                            Lorem ipsum dolor sit amet, consectetur adipiscing elit. Ut elit tellus, luctus nec ullamcorper mattis, pulvinar dapibus leo.
                        </div>
                        </div>
                    </div>
                    <div id="accordion-D" class="card tab-pane" role="tabpanel" aria-labelledby="accordion-D">
                        <div class="card-header" role="tab" id="qus-D">
                        <h5 class="mb-0">
                            <a class="collapsed" data-bs-toggle="collapse" href="#collapse-four" aria-expanded="true" aria-controls="collapse-four">
                                How we raise fund for charity ?
                            </a>
                        </h5>
                        </div>
                        <div id="collapse-four" class="collapse" data-bs-parent="#accordion-tab-one" role="tabpanel" aria-labelledby="qus-D">
                        <div class="card-body">
                            Lorem ipsum dolor sit amet, consectetur adipiscing elit. Ut elit tellus, luctus nec ullamcorper mattis, pulvinar dapibus leo.
                        </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-lg-6">
                <div class="qsn-form-container">
                    <h3>Still have a questions ?</h3>
                    <p>Lorem ipsum dolor sit amet, consectetur adipiscing elit. Ut elit tellus, luctus nec ullam corper</p>
                    <form class="faq-from"  method="post" action="{{route('faq-submit')}}" id="faq-from">
                        <p>
                        <input type="text" name="name" placeholder="Your Name*" class="faq-form-control">
                        <span class="form-validation" id="name1"></span>
                        </p>
                        <p>
                        <input type="email" name="email" placeholder="Your Email*" id="email" class="faq-form-control">
                        <span class="form-validation" id="email1"></span>
                        </p>
                        <p>
                        <input type="number" name="phone" placeholder="Your Number*" class="faq-form-control">
                        <span class="form-validation" id="phone1"></span>
                        </p>
                        <p>
                        <textarea rows="8" name="message" placeholder="Enter your message" class="faq-form-control"></textarea>
                        <span class="form-validation" id="message1"></span>
                        </p>
                        <p>
                        <input type="submit" name="submit" value="SUBMIT QUESTIONS">
                        <span id="successFaq" style="display:none;color:#ffff;">Submitted Successfully</span>
                        </p>
                    </form>
                </div>
            </div>
            </div>
        </div>
    </div>
</div>
<!-- faq html end -->
</main>
@endsection
@section('script')
<script src="{{ url("front/js/sweet-alert.js")}}"></script>
<script>
    $(document).on('input', '.faq-form-control', function (obj) {
     
        name = obj.name + 1;
       $('#' + name).text('');
    });
    $('#faq-from').submit(function (event) {
      event.preventDefault();
      var valid = 1
      $('.faq-form-control').each(function (i, obj) {
         console.log($(this).val());
         //console.log(obj.val());
         if ($(this).val() == '') {
            name = obj.name + 1;
            $('#' + name).text('field is required');
            valid = 0
         }
   
      });
      console.log(valid);
      var email = $('#email').val();
      console.log(email);
      if (validateEmail(email)) {
   
      } else {
         valid = 0;
         $('#email').text('email not valid');
      }
      console.log(valid);
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
                  $('#faq-from')[0].reset();
                 
                  swal("Success", response.message, "success");
                  $('#successfaq').show();
               } else {
   
                  swal("Error", "Something went wrong", "error");
                  $('#successfaq').show();
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