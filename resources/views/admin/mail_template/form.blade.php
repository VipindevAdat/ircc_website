@extends('admin.layouts.app')

@section('page-title', 'Mail Template')

@section('content')
<div class="row flex-column-reverse flex-md-row">
   <div class="col-md-12">
      <div class="tab-content" id="myTabContent">
         <div class="tab-pane fade show active" id="profile" role="tabpanel" aria-labelledby="profile-tab">
            <div class="mb-4">
               <div class="card mb-4">
                  <form  method="POST" action="{{ $mailTemplate->id ? route('admin.mailTemplate.update',$mailTemplate->id) : route('admin.mailTemplate.store') }}" enctype='multipart/form-data' >
                     @csrf
                     {{ $mailTemplate->id ? method_field('PUT'):'' }}
                     <div class="card-body">
                        <h6 class="card-title mb-4">{{ $mailTemplate->id ? 'Edit' : 'Add' }} Mail Template</h6>
                        
                        <div class="row">
                           <div class="col-md-12">
                              <div class="mb-3">
                                 <label class="form-label">Name</label>
                                 <input type="text" class="form-control" name="name" value="{{old('name', ($mailTemplate->name ? $mailTemplate->name : '' ))}}" required>
                                 @if ($errors->has('name'))
                                 <div  class="invalid-feedback d-block">
                                    {{ $errors->first('name') }}
                                 </div>
                                 @endif
                              </div>
                           </div>
                           <div class="col-md-12">
                           <div class="mb-3">
                                 <label class="form-label">Subject</label>
                                 <input type="text" class="form-control" name="subject" value="{{old('subject', ($mailTemplate->subject ? $mailTemplate->subject : '' ))}}" required>
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
                           <textarea id="editor" name="mail_body" required>
                                {!! $mailTemplate->mail_body !!}
                                </textarea>
                                @if ($errors->has('mail_body'))
                                    <div class="invalid-feedback d-block">
                                        {{ $errors->first('mail_body') }}
                                    </div>
                                @endif
                           
                           </div>
                            </div>
                            <div class="col-md-12">
                           <div class="mb-3">
                           <label class="form-label">Attachments</label>
                            <input class="form-control file-upload" type="file" id="file-upload" name="file-upload[]" accept=".png, .jpg, .jpeg,.pdf" multiple>
                           </div>
                            </div>
                            @if($mailTemplate->getmedia('attachment'))
                         <div class="row g-3" id="imgGallery" class="imgGallery">
                            
                          @foreach($mailTemplate->getmedia('attachment') as $image)
                            <div class="image-div col-md-3"  id="img{{$image->id}}" >
                              @if($image->mime_type=='application/pdf')
                              <embed src="{{$image->getUrl()}}#toolbar=0&navpanes=0&scrollbar=0" type="application/pdf" width="100%" height="150px" />
                             @else
                              <img class="images image-input-wrapper" style="width: 150px;height:150px; object-fit:contain" src="{{$image->getUrl()}}" title="{{$image->filename}}"/>
                            @endif
                              <span  class="delete-attachment" data-id="{{$image->id}}" data-bs-toggle="modal" data-bs-target="#delete-modal"><i class="fa fa-trash red" aria-hidden="true"></i></span>
                          </div>
                          @endforeach
                        
                          </div>
                          @endif
                           <div class="col-md-12">
                              <div class="mb-3">
                                 <button type="submit" class="btn btn-primary mb-3 pull-right">Save</button>
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
<div class="modal fade" id="delete-modal" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="staticBackdropLabel">Delete Attachment</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      
      <div class="modal-body">
              <!-- <input type="hidden" name="_method" value="DELETE"> -->
                <input type="hidden" name="_token" value="{{ csrf_token() }}">
                <input type="hidden" id="deleteId" name="deleteId">
                <input type="hidden" id="mailTemplateId" value="{{ $mailTemplate->id}}">
                <p>('Are you sure you want to delete this attachment)</p>
                <div class="modal-footer">
                   
                    <button type="button" class="btn btn-sm btn-danger" data-bs-dismiss="modal">Close</button>
                    <button type="button" class="btn btn-sm btn-danger delete-confirm" data-loading-text="">Delete</button>
                </div>
            </div>
        
    </div>
</div>   
</div>
@endsection
@section('script')
<script src="{{ url('libs/ckeditor5/ckeditor.js') }}"></script>
<script src="//cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>
<script>
    ClassicEditor.create(document.querySelector('#editor'))
    $(document).on('click','.delete-attachment',function(e){
    
    e.preventDefault();
   
    $('#deleteId').val($(this).data('id'));
    var val=$(this).data('id');   
    $(document).on('click','.delete-confirm',function(){
    
        $.ajax({
               headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')}, 
                type: "POST",
                url: "{{ route('admin.deleteAttachmentById') }}",
                dataType : 'JSON', 
                data:  {
                    id:val,
                    mailTemplateId:$('#mailTemplateId').val()
                },
                success: function (response) {
                    $('#delete-modal').modal('hide');
                   
                    $('#img'+val).remove();
                    toastr.success("Deleted successfully", "Success");            
                },
                error: function(error){
                    $('#delete-modal').modal('hide');
                    $.toastr.error("Something went wrong'", {
                        time: 3000,
                        position: 'top-right', 
                    });
              
                }

        }); 
       
        });
       

});
   
</script>
@endsection