@extends('admin.layouts.app')

@section('page-title', 'Abstract')

@section('content')
<div class="row flex-column-reverse flex-md-row">
   <div class="col-md-12">
      <div class="tab-content" id="myTabContent">
         <div class="tab-pane fade show active" id="profile" role="tabpanel" aria-labelledby="profile-tab">
            <div class="mb-4">
               <div class="card mb-4">
                  <form  method="POST" action="{{ $abstract->id ? route('admin.abstract.update',$abstract->id) : route('admin.abstract.store') }}">
                     @csrf
                     {{ $abstract->id ? method_field('PUT'):'' }}
                     <div class="card-body">
                        <h6 class="card-title mb-4">{{ $abstract->id ? 'Edit' : 'Add' }} Abstract</h6>
                        <div class="row">
                           <div class="col-md-12">
                              <div class="mb-3">
                                 <label class="form-label">Title</label>
                                 <input type="text" class="form-control" name="title" value="{{old('title', ($abstract->title ? $abstract->title : '' ))}}" required>
                                 @if ($errors->has('title'))
                                 <div  class="invalid-feedback d-block">
                                    {{ $errors->first('title') }}
                                 </div>
                                 @endif
                              </div>
                           </div>
                           <div class="col-md-6">
                              <div class="mb-3">
                                 <label class="form-label">Correspondent name</label>
                                 <input type="text" class="form-control" name="correspondent_name" value="{{old('correspondent_name', ($abstract->correspondent_name ? $abstract->correspondent_name : '' ))}}" required>
                                 @if ($errors->has('title'))
                                 <div  class="invalid-feedback d-block">
                                    {{ $errors->first('title') }}
                                 </div>
                                 @endif
                              </div>
                           </div>
                           <div class="col-md-6">
                              <div class="mb-3">
                                 <label class="form-label">Correspondent email</label>
                                 <input type="text" class="form-control" name="correspondent_email" value="{{old('correspondent_email', ($abstract->correspondent_email ? $abstract->correspondent_email : '' ))}}" required>
                                 @if ($errors->has('title'))
                                 <div  class="invalid-feedback d-block">
                                    {{ $errors->first('title') }}
                                 </div>
                                 @endif
                              </div>
                           </div>
                           <div class="col-md-12">
                           <div class="mb-3">
                                 <label class="form-label">Authors</label>
                                 <input type="text" class="form-control" name="authors" value="{{old('authors', ($abstract->authors ? $abstract->authors : '' ))}}" required>
                                 @if ($errors->has('authors'))
                                 <div  class="invalid-feedback d-block">
                                    {{ $errors->first('authors') }}
                                 </div>
                                 @endif
                              </div>
                           </div>
                           <div class="col-md-12">
                           <div class="mb-3">
                                 <label class="form-label">Affiliation</label>
                                 <input type="text" class="form-control" name="affiliation" value="{{old('affiliation', ($abstract->affiliation ? $abstract->affiliation : '' ))}}" required>
                                 @if ($errors->has('affiliation'))
                                 <div  class="invalid-feedback d-block">
                                    {{ $errors->first('affiliation') }}
                                 </div>
                                 @endif
                              </div>
                           </div>
                           <div class="col-md-12">
                           <div class="mb-3">
                                 <label class="form-label">Reference</label>
                                 <input type="text" class="form-control" name="reference" value="{{old('reference', ($abstract->reference ? $abstract->reference : '' ))}}" required>
                                 @if ($errors->has('reference'))
                                 <div  class="invalid-feedback d-block">
                                    {{ $errors->first('reference') }}
                                 </div>
                                 @endif
                              </div>
                           </div>
                           <div class="col-md-12">
                           <div class="mb-3">
                           <label class="form-label">Abstract</label>
                           <textarea id="editor" name="abstract" required>
                                {!! $abstract->abstract !!}
                                </textarea>
                                @if ($errors->has('abstract'))
                                    <div class="invalid-feedback d-block">
                                        {{ $errors->first('abstract') }}
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
<script src="{{ url('libs/ckeditor5/ckeditor.js') }}"></script>
<script>
    ClassicEditor.create(document.querySelector('#editor'))
 
</script>
@endsection