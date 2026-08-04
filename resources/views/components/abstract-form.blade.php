<div class="alert alert-success alert-dismissible fade show success-message" role="alert" id="successDiv" style="display:none">
<span id="success-message" class="success-message"></span>
<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
</div>
<form  method="post" class="donate-form" action="{{route('abstract.store')}}" id="abstract-form"  enctype="multipart/form-data">
    @csrf  
    <div class="donate-form form-inner">
    <h3>Submission details</h3>
    <input type="hidden" name="register_number" id="register_number" value="{{isset($registration)?Crypt::encryptString($registration->id):''}}">
    <div class="row form-wrap  d-flex">
        <div class="col-lg-6 col-md-6 col-sm-6 col-12 form-group">
        <label>Abstract title *</label>
            <input class="abstract-form-control" type="text" name="title" data-id="title1" placeholder="Abstract title *">
            <span class="form-validation" id="title1"></span>
        </div>
        <div class="col-lg-6 col-md-6 col-sm-6 col-12 form-group">
        <label>Authors name *<span style="font-size:12px;color:red;">* ( Enter name with comma seperated)</span></label>
                <input type="text" class="abstract-form-control tagsinput-example" placeholder="authors" name="author_name" id="author_name" value="">
                <span class="form-validation" id="author_name1"></span>
        </div>
        <div class="col-lg-6 col-md-6 col-sm-6 col-12 form-group">
        <label>Correspondent name *</label>
                <input class="abstract-form-control" type="text" name="correspondent_name" placeholder="Corresponding author name*">
                <span class="form-validation" id="correspondent_name1"></span>
        </div>
        <div class="col-lg-6 col-md-6 col-sm-6 col-12 form-group">
        <label>Correspondent email *</label>
                <input class="abstract-form-control" type="text" name="correspondent_email" id="correspondent_email" placeholder="corresponding author email"> 
                <span class="form-validation" id="correspondent_email1"></span>
        </div>
        <div class="col-lg-6 col-md-6 col-sm-6 col-12 form-group">
        <label>Affiliation *</label>
                <input class="abstract-form-control" type="text" name="affiliation" placeholder="Affiliation"> 
                <span class="form-validation" id="affiliation1"></span>
        </div>
        <div class="col-lg-6 col-md-6 col-sm-6 col-12 form-group">
        <label>Reference *</label>
                <input class="abstract-form-control" type="text" name="reference" placeholder="Reference"> 
                <span class="form-validation" id="reference1"></span>
        </div>
    
        <div class="col-lg-12 col-12 form-group">
        <label>Abstract *</label>
        <textarea class="abstract-form-control" id="abstractDescription" name="abstract" placeholder="maximum 250 words"  rows="20"></textarea>
            <span class="form-validation" id="abstract1"></span>
          
            <span id="word_left" class="text-end"></span>

        </div>
        <span class="form-validation" id="validform1"></span>
    </div>
    </div>
    <button type="submit" class="button-round">Submit Now</button>

</form>
<script>
        var status = 0;

// $("#abstract-form :input").prop('readonly', true);
$(document).on('submit', '#abstract-form', function (event) {
// $('#abstract-form').submit(function (event) {
   event.preventDefault();
   var valid = 1
   $('.abstract-form-control').each(function (i, obj) {
      console.log($(this).val());
      //console.log(obj.val());
      if ($(this).val() == '') {
         name = obj.name + 1;
         $('#' + name).text('field is required');
         valid = 0
      }

   });
   var email = $('#correspondent_email').val();
   console.log(email);
   if (validateEmail(email)) {

   } else {
      valid = 0;
      $('#correspondent_email1').text('email not valid');
   }
   if (valid == 1) {
      var url = $(this).attr('action');
      $('#loading').show();
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
            $('#loading').hide();
            if (response.error == 0) {
               $('.form-validation').text('');
               $('#abstract_success').show();
               $('#abstract_submit_div').hide()
               $('#abstract_div').hide()
               $('#abstract_head').hide()
               $('#register_detail').hide()
               swal("Success", response.message, "success");
               $('.success-abstract').css("color", "#1ed104");
               $('.success-abstract').html(response.message);
               $('.success-abstract').delay(50).fadeOut('slow');
               $("#author_name").val('');
               $("#author_name").html('');

               $('#abstract-form')[0].reset();
               $("#abstract-form :input").prop('readonly', true);
               $('#registered_id').val('');
               $('#verify-div').hide();
               $('#verified-div').hide();
               $('#verify_div').hide();
            } else {

               swal("Error", response.message, "error");
            }
         },
         error: function(error){
            $('#loading').hide();
            swal("Error", 'Manadory field are empty', "error");
            }


      });
   }
   else{
      $('#validform1').text('Manadory field are empty');
   }
  
});
</script>