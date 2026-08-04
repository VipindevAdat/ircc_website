@extends('admin.layouts.app')

@section('page-title', 'Abstract Book')
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
                  <form  method="POST" action="{{ $abstractBook->id ? route('admin.abstract-book.update',$abstractBook->id) : route('admin.abstract-book.store') }}" enctype='multipart/form-data' >
                     @csrf
                     {{ $abstractBook->id ? method_field('PUT'):'' }}
                     <div class="card-body">
                        <h6 class="card-title mb-4">{{ $abstractBook->id ? 'Edit' : 'Add' }} Abstract Book</h6>
                        
                        <div class="row">
                           <div class="col-md-12">
                              <div class="mb-3">
                                 <label class="form-label">Title</label>
                                 <input type="text" class="form-control" name="title" value="{{old('title', ($abstractBook->title ? $abstractBook->title : '' ))}}" required>
                                 @if ($errors->has('title'))
                                 <div  class="invalid-feedback d-block">
                                    {{ $errors->first('title') }}
                                 </div>
                                 @endif
                              </div>
                           </div>
                     
                           <div class="col-md-12">
                           <div class="mb-3">
                                 <label class="form-label">Description</label>
                                 <textarea class="form-control" name="description"  rows="2" required>{{old('description', ($abstractBook->description ? $abstractBook->description : '' ))}}</textarea>
                                 @if ($errors->has('description'))
                                 <div  class="invalid-feedback d-block">
                                    {{ $errors->first('description') }}
                                 </div>
                                 @endif
                              </div>
                           </div>
                   
                              <div class="col-md-6">
                              <div class="mb-3">
                                 <label class="form-label">Location</label>
                                 <input type="text" class="form-control" name="location" value="{{old('location', ($abstractBook->location ? $abstractBook->location : '' ))}}" required>
                                 @if ($errors->has('location'))
                                 <div  class="invalid-feedback d-block">
                                    {{ $errors->first('location') }}
                                 </div>
                                 @endif
                              </div>
                           </div>
                           <div class="col-md-6">
                              <div class="mb-3">
                                 <label class="form-label">Date & Time</label>
                                 <input type="text" class="form-control" name="date_time" value="{{old('date_time', ($abstractBook->date_time ? $abstractBook->date_time : '' ))}}" id="date_time" required>
                                 @if ($errors->has('date_time'))
                                 <div  class="invalid-feedback d-block">
                                    {{ $errors->first('date') }}
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
                            @if($abstractBook->getmedia('abstractBook'))
                            <div class="col-md-12" id="imgGallery" class="imgGallery">
                            
                          @foreach($abstractBook->getmedia('abstractBook') as $image)
                            <div class="image-div col-md-3"  id="img{{$image->id}}" >
                              @if($image->mime_type=='application/pdf')
                              <embed src="{{$image->getUrl()}}#toolbar=0&navpanes=0&scrollbar=0" type="application/pdf" width="100%" height="150px" />
                             @else
                              <img class="images image-input-wrapper" style="width: 150px;height:150px; object-fit:contain" src="{{$image->getUrl()}}" title="{{$image->filename}}"/>
                            @endif
                              <span  class="delete-attachment" data-id="{{$image->id}}" data-bs-toggle="modal" data-bs-target="#delete-modal"><i class="fa fa-trash red" aria-hidden="true"></i></span>
                          </div>
                          @endforeach
                          @endif
                         </div>

                          
                           <div class="col-md-6">
                              <div class="mb-3">
                              <label class="form-label">Sort Order</label>
                              <input type="number" class="form-control" name="sort_order" value="{{old('sort_order', ($abstractBook->sort_order ? $abstractBook->sort_order : 1 ))}}" required>
                              @if ($errors->has('sort_order'))
                              <div  class="invalid-feedback d-block">
                                 {{ $errors->first('sort_order') }}
                              </div>
                              @endif
                              </div>
                           </div>
                     
                           <div class="col-md-6">
                              <div class="mb-3">
                                 <label class="form-label">Status</label>
                              <select class="form-select " aria-label="Default" name="status" id="status" required>
                           
                           <option value="1"  {{ old('status', $abstractBook->status ==1? 'selected' : '')}}>Enabled</option>
                           <option value="0" {{ old('status', $abstractBook->status ==0 ? 'selected' : '')}}>Disabled</option>
                           </select>
                           @if ($errors->has('status'))
                               <div  class="invalid-feedback d-block">
                               {{ $errors->first('status') }}
                           </div>
                           @endif
                              </div>
                           </div>  
                              
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
                <input type="hidden" id="abstractBookId" value="{{ $abstractBook->id}}">
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
<script src="{{ url("libs/datepicker/daterangepicker.js")}}"></script>
<script>
    ClassicEditor.create(document.querySelector('#editor'))
    $('input[name="date_time"]').daterangepicker({
      showDropdowns: true,
      locale: {
            format: 'DD/MM/YYYY'
         }

      });
    $(document).on('click','.delete-attachment',function(e){
    
    e.preventDefault();
   
    $('#deleteId').val($(this).data('id'));
    var val=$(this).data('id');   
    $(document).on('click','.delete-confirm',function(){
    
        $.ajax({
               headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')}, 
                type: "POST",
                url: "{{ route('admin.deleteAbstractById') }}",
                dataType : 'JSON', 
                data:  {
                    id:val,
                    abstractBookId:$('#abstractBookId').val()
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