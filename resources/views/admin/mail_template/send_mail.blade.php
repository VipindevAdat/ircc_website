@extends('admin.layouts.app')

@section('page-title', 'Send Mail')
@section('head')
<link rel="stylesheet" href="{{ url("libs/select2/css/select2.min.css") }}" type="text/css">
@endsection
@section('content')
<div class="row flex-column-reverse flex-md-row">
   <div class="col-md-12">
      <div class="tab-content" id="myTabContent">
         <div class="tab-pane fade show active" id="profile" role="tabpanel" aria-labelledby="profile-tab">
            <div class="mb-4">
               <div class="card mb-4">
                  <form  method="POST" action="{{route('admin.sendMailSubmit') }}" enctype='multipart/form-data'  >
                     @csrf
                     
                     <div class="card-body">
                        <h6 class="card-title mb-4"> Send Mail</h6>
                        <div class="row">
                          
                           <div class="col-md-6">
                           <div class="mb-3">
                                <label for="inputEmail4" class="form-label">Tags</label>
                                <select class="form-select" aria-label="Default select example" name="select_tag"
                                    id="select_tag">
                                    <option value="">Select Tag</option>
                                    <option value="registration" {{ old('select_tag')=='registration"' ? 'selected' : ''}}>Register-IRCC-23-24</option>
                                    <option value="leads" {{ old('select_tag')=='leads"' ? 'selected' : ''}}>Leads-IRCC-23-24</option>
                                    <option value="abstract" {{ old('select_tag')=='abstract"' ? 'selected' : ''}}>Abstract-IRCC-23-24</option>
                                    <option value="participate" {{ old('select_tag')=='participate"' ? 'selected' : ''}}>participate-IRCC-23-24</option>
                                     
                                </select>
                                @if ($errors->has('select_tag'))
                                    <div class="invalid-feedback d-block">
                                        {{ $errors->first('select_tag') }}
                                    </div>
                                @endif
                            </div>
                           </div>
                           <div class="col-md-6">
                           <div class="mb-3">
                                <label for="inputEmail4" class="form-label">Mail Template</label>
                                <select class="form-select" aria-label="Default select example" name="select_template"
                                    id="select_template" >
                                    <option value="">Select Mail Template</option>
                                    @foreach($mailTemplates as $template)
                                    <option value="{{$template->id}}" {{ old('select_template')==$template->id ? 'selected' : ''}}>{{$template->name}}</option>
                                   @endforeach
                                     
                                </select>
                                @if ($errors->has('select_tag'))
                                    <div class="invalid-feedback d-block">
                                        {{ $errors->first('select_tag') }}
                                    </div>
                                @endif
                            </div>
                           </div>
                            <div class="col-md-12">
                              <div class="mb-3" id="tagDiv" style="display: none;" >
                                 <label class="form-label">To</label>
                                 <select class="select2-example" name="email_to_tag[]" id="email_to_tag" multiple>
                                </select>
                              </div>
                              <div class="mb-3" id="emailDiv">
                              <label class="form-label">To</label>
                              <textarea id="email_to" class="form-control" name="email_to" rows="1" required>{{old('email_to')}}</textarea>
                                 @if ($errors->has('email_to'))
                                 <div  class="invalid-feedback d-block">
                                    {{ $errors->first('email_to') }}
                                 </div>
                                 @endif
                              </div>
                           </div>
                           <div class="col-md-12">
                           <div class="mb-3">
                                 <label class="form-label">Subject</label>
                                 <input type="text" class="form-control" name="subject" id="subject" value="{{old('subject')}}">
                                 @if ($errors->has('subject'))
                                 <div  class="invalid-feedback d-block">
                                    {{ $errors->first('subject') }}
                                 </div>
                                 @endif
                              </div>
                           </div>
                           <div class="col-md-12">
                           <div class="mb-3">
                           <label class="form-label">Mail Body</label>
                           <textarea id="editor" name="mail_body"  required>
                               
                                </textarea>
                                @if ($errors->has('mail_body'))
                                    <div class="invalid-feedback d-block">
                                        {{ $errors->first('mail_body') }}
                                    </div>
                                @endif
                           
                           </div>
                            </div>
                            <div class="row g-3" id="imgAttachment" class="imgAttachment">
                        
                            </div>
                           <div class="col-md-12">
                              <div class="mb-3">
                                 <button type="submit" class="btn btn-primary mb-3 pull-right">Send</button>
                              </div>
                           </div>
                          
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
<script src="{{ url('libs/ckeditor5/ckeditor.js') }}"></script>
<script src="{{ url("libs/select2/js/select2.min.js")}}"></script>
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
   ClassicEditor.create(document.querySelector('#editor'));
   $('.select2-example').select2({
           placeholder: 'Select'
       });
       select_tag
    $(document).on('change', '#select_tag', function() {
            var id=$('#select_tag').val();
            if($('#select_tag').val()){
                $.ajax({
                    type: 'POST',
                    data:{tag:id},
                    dataType : 'JSON', 
                    url : '{{ route('admin.getTagEmails') }}',
                    success: function(response){
                    $('#email_to_tag').html(response.option);
                    $('#email_to_tag').select2();

                    } 
                });
                $('#tagDiv').show();
                $('#emailDiv').hide();

            }
            else{
                $('#tagDiv').hide();
                $('#emailDiv').show(); 
            }
    });
   
    $(document).on('change', '#select_template', function() {
      var id=$('#select_template').val();
      $.ajax({
         type: 'POST',
         data:{templateId:id},
         dataType : 'JSON', 
         url : '{{ route('admin.getTemplateById') }}',
         success: function(response){
           $('#subject').val(response.subject);
           $('#imgAttachment').html(response.html)
            
         $('.ck-editor').remove();
           ClassicEditor.create(document.querySelector('#editor')).then( editor => {
            editor.setData(response.mail_body)
            } );

         } 
       });
   
   });
  
</script>
@endsection