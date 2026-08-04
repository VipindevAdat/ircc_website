@extends('admin.layouts.app')

@section('page-title', 'Leads')

@section('content')
<div class="row flex-column-reverse flex-md-row">
   <div class="col-md-12">
      <div class="tab-content" id="myTabContent">
         <div class="tab-pane fade show active" id="profile" role="tabpanel" aria-labelledby="profile-tab">
            <div class="mb-4">
               <div class="card mb-4">
                  <form  method="POST" action="{{ $leads->id ? route('admin.leads.update',$leads->id) : route('admin.leads.store') }}">
                     @csrf
                     {{ $leads->id ? method_field('PUT'):'' }}
                     <div class="card-body">
                        <h6 class="card-title mb-4">{{ $leads->id ? 'Edit' : 'Add' }} Leads</h6>
                        <div class="row">
                           <div class="col-md-6">
                              <div class="mb-3">
                                 <label class="form-label">Name</label>
                                 <input type="text" class="form-control" name="name" value="{{old('name', ($leads->name ? $leads->name : '' ))}}">
                                 @if ($errors->has('name'))
                                 <div  class="invalid-feedback d-block">
                                    {{ $errors->first('name') }}
                                 </div>
                                 @endif
                              </div>
                           </div>
                           <div class="col-md-6">
                              <div class="mb-3">
                                 <label class="form-label">Email</label>
                                 <input type="text" class="form-control" name="email" value="{{old('email', ($leads->email ? $leads->email : '' ))}}">
                                 @if ($errors->has('email'))
                                 <div  class="invalid-feedback d-block">
                                    {{ $errors->first('email') }}
                                 </div>
                                 @endif
                              </div>
                           </div>
                           <div class="col-md-6">
                              <div class="mb-3">
                                 <label class="form-label">Phone</label>
                                 <input type="text" class="form-control" name="phone" value="{{old('phone', ($leads->phone ? $leads->phone : '' ))}}">
                                 @if ($errors->has('phone'))
                                 <div  class="invalid-feedback d-block">
                                    {{ $errors->first('phone') }}
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

@endsection