@extends('admin.layouts.app')

@section('page-title', 'Reviews')

@section('content')
<div class="row flex-column-reverse flex-md-row">
   <div class="col-md-12">
      <div class="tab-content" id="myTabContent">
         <div class="tab-pane fade show active" id="profile" role="tabpanel" aria-labelledby="profile-tab">
            <div class="mb-4">
               <div class="card mb-4">
                  <form  method="POST" action="{{ $review->id ? route('admin.review.update',$review->id) : route('admin.review.store') }}">
                     @csrf
                     {{ $review->id ? method_field('PUT'):'' }}
                     <div class="card-body">
                        <h6 class="card-title mb-4">{{ $review->id ? 'Edit' : 'Add' }} Review</h6>
                        <div class="row">
                           <div class="col-md-12">
                           <div class="mb-3">
                           <label class="form-label">Message</label>
                           <textarea id="editor" name="message" required>
                                {!! $review->message !!}
                                </textarea>
                                @if ($errors->has('message'))
                                    <div class="invalid-feedback d-block">
                                        {{ $errors->first('message') }}
                                    </div>
                                @endif
                           
                           </div>
                            </div>
                            <div class="col-md-12">
                              <div class="mb-3">
                                 <label class="form-label">Author</label>
                                 <input type="text" class="form-control" name="author" value="{{old('author', ($review->author ? $review->author : '' ))}}" required>
                                 @if ($errors->has('author'))
                                 <div  class="invalid-feedback d-block">
                                    {{ $errors->first('author') }}
                                 </div>
                                 @endif
                              </div>
                           </div>
                           <div class="col-md-4">
                              <div class="mb-3">
                              <label class="form-label">Sort Order</label>
                              <input type="text" class="form-control" name="sort_order" value="{{old('sort_order', ($review->sort_order ? $review->sort_order :1 ))}}" required>
                              @if ($errors->has('sort_order'))
                              <div  class="invalid-feedback d-block">
                                 {{ $errors->first('sort_order') }}
                              </div>
                              @endif
                              </div>
                           </div>
                         
                       
                           <div class="col-md-4">
                              <div class="mb-3">
                                 <label class="form-label">Status</label>
                              <select class="form-select " aria-label="Default" name="status" id="status" required>
                           
                           <option value="1"  {{ old('status', $review->status ==1? 'selected' : '')}}>Enabled</option>
                           <option value="0" {{ old('status', $review->status ==0 ? 'selected' : '')}}>Disabled</option>
                           </select>
                           @if ($errors->has('status'))
                               <div  class="invalid-feedback d-block">
                               {{ $errors->first('status') }}
                           </div>
                           @endif
                              </div>
                           </div>
                           <div class="col-md-4">
                              <div class="kt-checkbox-inline">
                                 <label class="kt-checkbox">
                                    <input type="checkbox"
                                          {{ $review->is_home == 1 ? 'checked' : 'unchecked' }}
                                          name="check" value="0" id="check"> View on Homepage
                                          <input type="hidden" name="is_home" id="val" value="@if($review->is_home){{ old('is_home', $review->is_home) }} @else 0 @endif">
                                    <span></span>
                                 </label>

                              </div>
                              <span class="form-text text-muted">View on Homepage</span>
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
@endsection
@section('script')
<script src="{{ url('libs/ckeditor5/ckeditor.js') }}"></script>
<script>
       $('#check').click(function(){
      if ($('#check').is(":checked"))
         $("#val").attr( "value", "1" );
      else
         $("#val").attr( "value", "0" );
    });
    ClassicEditor.create(document.querySelector('#editor'))
 
</script>
@endsection