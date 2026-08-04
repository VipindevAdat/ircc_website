@extends('admin.layouts.app')

@section('page-title', 'Team Category')

@section('content')
<div class="row flex-column-reverse flex-md-row">
   <div class="col-md-12">
      <div class="tab-content" id="myTabContent">
         <div class="tab-pane fade show active" id="profile" role="tabpanel" aria-labelledby="profile-tab">
            <div class="mb-4">
               <div class="card mb-4">
                  <form  method="POST" action="{{ $teamCategory->id ? route('admin.team-category.update',$teamCategory->id) : route('admin.team-category.store') }}">
                     @csrf
                     {{ $teamCategory->id ? method_field('PUT'):'' }}
                     <div class="card-body">
                        <h6 class="card-title mb-4">{{ $teamCategory->id ? 'Edit' : 'Add' }} Category</h6>
                        <div class="row">
                           <div class="col-md-6">
                              <div class="mb-3">
                                 <label class="form-label">Name</label>
                                 <input type="text" class="form-control" name="name" value="{{old('name', ($teamCategory->name ? $teamCategory->name : '' ))}}">
                                 @if ($errors->has('name'))
                                 <div  class="invalid-feedback d-block">
                                    {{ $errors->first('name') }}
                                 </div>
                                 @endif
                              </div>
                           </div>
                           <div class="col-md-6">
                           <div class="mb-3">
                                <label for="inputEmail4" class="form-label">Parent</label>
                                <select class="form-select" aria-label="Default select example" name="parent_id"
                                    id="category">
                                    <option value="">Select parent</option>
                                    @if(!empty($categories))
                                    @foreach($categories as $category)
                                    <option value="{{$category->id}}"  {{ old('parent_id', $teamCategory->parent_id ==$category->id? 'selected' : '')}}>{{$category->name}}</option>
                                    @endforeach
                                    @endif
                                </select>
                                @if ($errors->has('parent_id'))
                                    <div class="invalid-feedback d-block">
                                        {{ $errors->first('parent_id') }}
                                    </div>
                                @endif
                            </div>
                           </div>
                        </div>
                      
                           <div class="row">
                           <div class="col-md-6">
                              <div class="mb-3">
                                 <label class="form-label">Sort Order</label>
                                 <input type="text" class="form-control" name="sort_order" value="{{old('sort_order', ($teamCategory->sort_order ? $teamCategory->sort_order : 1 ))}}">
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
                           <select class="form-select" aria-label="Default" name="status" id="status" required>
                           
                           <option value="1"  {{ old('status', $teamCategory->status ==1? 'selected' : '')}}>Enabled</option>
                           <option value="0" {{ old('status', $teamCategory->status ==0 ? 'selected' : '')}}>Disabled</option>
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
@endsection
@section('script')
<script>
     $(document).on('change', '#select_type', function() {
            var id=$('#select_type').val();
            if($('#select_type').val()==2){
               $('#grade_div').show(); 
            }
            else{
               $('#grade_div').hide(); 
            }
    });
   
</script>
@endsection