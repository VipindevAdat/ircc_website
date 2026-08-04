@extends('admin.layouts.app')

@section('page-designation', 'Team')
@section('head')
<link rel="stylesheet" href="{{ url("libs/datepicker/daterangepicker.css")}}" type="text/css">
<style>
    .form-validation{
        color:red;
    }
    </style>
@endsection
@section('content')
<div class="row flex-column-reverse flex-md-row">
   <div class="col-md-12">
      <div class="tab-content" id="myTabContent">
         <div class="tab-pane fade show active" id="profile" role="tabpanel" aria-labelledby="profile-tab">
            <div class="mb-4">
               <div class="card mb-4">
                  <form  method="POST" action="{{ $team->id ? route('admin.team.update',$team->id) : route('admin.team.store') }}" enctype='multipart/form-data' >
                     @csrf
                     {{ $team->id ? method_field('PUT'):'' }}
                     <div class="card-body">
                        <h6 class="card-designation mb-4">{{ $team->id ? 'Edit' : 'Add' }} Team</h6>
                        
                        <div class="row">
                           <div class="col-md-6">
                              <div class="mb-3">
                                 <label class="form-label">Name</label>
                                 <input type="text" class="form-control" name="name" value="{{old('name', ($team->name ? $team->name : '' ))}}" required>
                                 @if ($errors->has('name'))
                                 <div  class="invalid-feedback d-block">
                                    {{ $errors->first('name') }}
                                 </div>
                                 @endif
                              </div>
                           </div>
                           <div class="col-md-6">
                           <div class="mb-3">
                                <label for="inputEmail4" class="form-label">Team Category</label>
                                <select class="form-select" aria-label="Default select example" name="team_category_id"
                                    id="team_category_id" required>
                                    <option value="">Select Category</option>
                                    @if(!empty($categories))
                                    @foreach($categories as $category)
                                    @if(count($category['sub_category']))
                                    <optgroup label="{{ $category['category']->name }}">
                                    @foreach($category['sub_category'] as $team_category)
                                    <option value="{{$team_category->id}}"  {{ old('team_category_id', $team->team_category_id ==$team_category->id? 'selected' : '')}}>{{$team_category->name}}</option>
                                    @endforeach
                                    </optgroup>
                                    @else
                                    {{-- If no sub-categories, allow selecting parent category directly --}}
                                    <option value="{{$category['category']->id}}" {{ old('team_category_id', $team->team_category_id ==$category['category']->id? 'selected' : '')}}>{{$category['category']->name}}</option>
                                    @endif
                                    @endforeach
                                    @endif
                                </select>
                                @if ($errors->has('team_category_id'))
                                    <div class="invalid-feedback d-block">
                                        {{ $errors->first('team_category_id') }}
                                    </div>
                                @endif
                            </div>
                           </div>
                           <div class="col-md-6">
                              <div class="mb-3">
                                 <label class="form-label">Designation</label>
                                 <input type="text" class="form-control" name="designation" value="{{old('designation', ($team->designation ? $team->designation : '' ))}}" required>
                                 @if ($errors->has('designation'))
                                 <div  class="invalid-feedback d-block">
                                    {{ $errors->first('designation') }}
                                 </div>
                                 @endif
                              </div>
                           </div>
                        

                           <div class="col-md-6">
                              <div class="mb-3">
                                 <label class="form-label">Organization</label>
                                 <input type="text" class="form-control" name="organization" value="{{old('organization', ($team->organization ? $team->organization : '' ))}}">
                                 @if ($errors->has('organization'))
                                 <div  class="invalid-feedback d-block">
                                    {{ $errors->first('organization') }}
                                 </div>
                                 @endif
                              </div>
                           </div>
                           
                           <div class="col-md-6">
                              <div class="mb-3">
                                 <label class="form-label">Qualification</label>
                                 <input type="text" class="form-control" name="qualification" value="{{old('qualification', ($team->qualification ? $team->qualification : '' ))}}" >
                                 @if ($errors->has('qualification'))
                                 <div  class="invalid-feedback d-block">
                                    {{ $errors->first('qualification') }}
                                 </div>
                                 @endif
                              </div>
                           </div>
                           <div class="col-md-6">
                              <div class="mb-3">
                                 <label class="form-label">Location</label>
                                 <input type="text" class="form-control" name="location" value="{{old('location', ($team->location ? $team->location : '' ))}}" >
                                 @if ($errors->has('location'))
                                 <div  class="invalid-feedback d-block">
                                    {{ $errors->first('location') }}
                                 </div>
                                 @endif
                              </div>
                           </div>
                           <div class="col-md-6">
                           <label for="inputAddress" class="form-label">Set team Image* </label>
                            <div class="col-md-6 mb-3">
                           
                            <label>Image (570 x 700 px) <span class="text-danger">*</span> :</label>
                                <div  id="image_div" class="mb-2">
                                    <img class="images image-input-wrapper img-fluid" id="image" src="{{!empty($featured)? $featured->getUrl():url('assets/images/no-image.png')}}" alt="Your Logo" style=" object-fit:contain"/>    
                                </div>
                                <input type="hidden" name="profile_avatar_remove" />
                                <input class="form-control file-upload" type="file" id="formFile" name="featured" accept=".png, .jpg, .jpeg" >
                            </div>
                           </div>
                           <div class="col-md-6">
                           <div class="row">
                           <div class="col-md-12">
                           <div class="mb-3">
                           <label class="form-label">Sort Order</label>
                           <input type="text" class="form-control" name="sort_order" value="{{old('sort_order', ($team->sort_order ? $team->sort_order : 1 ))}}" required>
                           @if ($errors->has('sort_order'))
                           <div  class="invalid-feedback d-block">
                              {{ $errors->first('sort_order') }}
                           </div>
                           @endif
                           </div>
                        </div>
                     
                     
                           <div class="col-md-12">
                              <div class="mb-3">
                                 <label class="form-label">Status</label>
                           <select class="form-select " aria-label="Default" name="status" id="status" required>
                           
                           <option value="1"  {{ old('status', $team->status ==1? 'selected' : '')}}>Enabled</option>
                           <option value="0" {{ old('status', $team->status ==0 ? 'selected' : '')}}>Disabled</option>
                           </select>
                           @if ($errors->has('status'))
                               <div  class="invalid-feedback d-block">
                               {{ $errors->first('status') }}
                           </div>
                           @endif
                              </div>
                           </div>   
                           <div class="col-md-12">
                           <label class="form-label">View on Homepage</label>
                              <div class="kt-checkbox-inline">
                                 <label class="kt-checkbox">
                                    <input type="checkbox" {{ $team->is_home ==1 ? 'checked' : '' }} name="checkbox" id="check" value="0" >
                                    <input type="hidden" name="is_home" id="val" value="@if($team->is_home){{ old('is_home', $team->is_home) }} @else 0 @endif">
                                 </label>

                              </div>
                              <span class="form-text text-muted">View on Homepage</span>
                          </div> 
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
        <h5 class="modal-designation" id="staticBackdropLabel">Delete Attachment</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      
      <div class="modal-body">
              <!-- <input type="hidden" name="_method" value="DELETE"> -->
                <input type="hidden" name="_token" value="{{ csrf_token() }}">
                <input type="hidden" id="deleteId" name="deleteId">
                <input type="hidden" id="teamId" value="{{ $team->id}}">
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
<script src="//cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>
<script src="{{ url("libs/form-repeater/repeater.min.js")}}"></script>
<script src="{{ url("libs/dataTable/datatables.min.js") }}"></script>
<script src="{{ url("libs/datepicker/daterangepicker.js")}}"></script>
<script>
   $('#check').click(function(){
      if ($('#check').is(":checked"))
         $("#val").attr( "value", "1" );
      else
         $("#val").attr( "value", "0" );
    });
   $('.file-upload').on('change',function(e){
    let reader = new FileReader();
 
 reader.onload = (e) => { 

   $('#image').attr('src', e.target.result); 
   $('#image_div').show();
   $('#featured_div').hide();
 }

 reader.readAsDataURL(this.files[0]); 
    
});
$('input[name="date"]').daterangepicker({
singleDatePicker: true,
showDropdowns: true,
locale: {
      format: 'DD-MM-Y'
   }

});
</script>

@endsection