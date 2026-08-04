@extends('admin.layouts.app')

@section('page-title', 'Reviewers')

@section('content')
    <div class="row flex-column-reverse flex-md-row">
        <div class="col-md-12">
            <div class="tab-content" id="myTabContent">
                <div class="tab-pane fade show active" id="profile" role="tabpanel" aria-labelledby="profile-tab">
                    <div class="mb-4">
                        <div class="card mb-4">
                            <form  method="POST" action="{{ $reviewer->id ? route('admin.reviewer.update',$reviewer->id) : route('admin.reviewer.store') }}">
                                @csrf
                                {{ $reviewer->id ? method_field('PUT'):'' }}
                                <div class="card-body">
                                    <h6 class="card-title mb-4">{{ $reviewer->id ? 'Edit' : 'Add' }} Reviewer</h6>
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="mb-3">
                                                <label class="form-label">First Name</label>
                                                <input type="text" class="form-control" name="first_name" value="{{ old('first_name', ($reviewer->first_name ? $reviewer->first_name : '' ))}}" required>
                                                @if ($errors->has('first_name'))
                                                    <div class="invalid-feedback d-block">
                                                        {{ $errors->first('first_name') }}
                                                    </div>
                                                @endif
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="mb-3">
                                                <label class="form-label">Last Name</label>
                                                <input type="text" class="form-control" name="last_name" value="{{old('last_name', ($reviewer->last_name ? $reviewer->last_name : '' ))}}" required>
                                                @if ($errors->has('last_name'))
                                                    <div  class="invalid-feedback d-block">
                                                        {{ $errors->first('last_name') }}
                                                    </div>
                                                @endif
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="mb-3">
                                                <label for="email" class="form-label">Email</label>
                                                <input type="email" name="email" class="form-control" value="{{ old('email', $reviewer->email ?? '') }}" required>
                                                @if ($errors->has('email'))
                                                  <div class="invalid-feedback d-block">{{ $errors->first('email') }}</div>
                                                @endif
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="mb-3">
                                                    <label for="category" class="form-label">Category</label>
                                                    <select id="category" name="category" class="form-control" required>
                                                        <option value="" disabled selected>Select Category</option>
                                                        @foreach($categories as $category)
                                                            <option value="{{ $category->id }}" {{ old('category', $reviewer->category ?? '') == $category->domain ? 'selected' : '' }}>
                                                                {{ $category->domain }}
                                                            </option>
                                                        @endforeach
                                                    </select>
                                                    @if ($errors->has('category'))
                                                        <div class="invalid-feedback d-block">{{ $errors->first('category') }}</div>
                                                    @endif
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="mb-3">
                                                    <label class="form-label">Sort Order</label>
                                                    <input type="text" class="form-control" name="sort_order" value="{{old('sort_order', ($reviewer->sort_order ? $reviewer->sort_order :1 ))}}" required>
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

                                                        <option value="1"  {{ old('status', $reviewer->status ==1? 'selected' : '')}}>Enabled</option>
                                                        <option value="0" {{ old('status', $reviewer->status ==0 ? 'selected' : '')}}>Disabled</option>
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
