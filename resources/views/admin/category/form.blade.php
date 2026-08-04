@extends('admin.layouts.app')

@section('page-title', 'Category')

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
                            <form  method="POST" action="{{ $category->id ? route('admin.category.update',$category->id) : route('admin.category.store') }}">
                                @csrf
                                {{ $category->id ? method_field('PUT'):'' }}
                                <div class="card-body">
                                    <h6 class="card-title mb-4">{{ $category->id ? 'Edit' : 'Add' }} Category</h6>
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="mb-3">
                                                <label class="form-label">Name</label>
                                                <input type="text" class="form-control" name="name" value="{{old('name', ($category->name ? $category->name : '' ))}}">
                                                @if ($errors->has('name'))
                                                    <div  class="invalid-feedback d-block">
                                                        {{ $errors->first('name') }}
                                                    </div>
                                                @endif
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="mb-3">
                                                <label for="inputEmail4" class="form-label">Type</label>
                                                <select class="form-select" aria-label="Default select example" name="type"
                                                        id="select_type">
                                                    <option value="1" {{ old('type',$category->type)==1 ? 'selected' : ''}}>No Grade Limit</option>
                                                    <option value="2" {{ old('type',$category->type)==2 ? 'selected' : ''}}>Grade</option>

                                                </select>
                                                @if ($errors->has('type'))
                                                    <div class="invalid-feedback d-block">
                                                        {{ $errors->first('type') }}
                                                    </div>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="mb-3">
                                                <label class="form-label">Class</label>
                                                <select class="form-select select2-example" aria-label="Default select example" name="class[]" id="select_type1" multiple>
                                                    @foreach($classes as $class)
                                                        <option value="{{ $class }}"
                                                            {{ in_array($class, old('class', $selectedClasses ?? [])) ? 'selected' : '' }}>
                                                            {{ $class }}
                                                        </option>
                                                    @endforeach
                                                </select>
                                                @if ($errors->has('class'))
                                                    <div class="invalid-feedback d-block">
                                                        {{ $errors->first('class') }}
                                                    </div>
                                                @endif
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="mb-3">
                                                <label class="form-label">Status</label>
                                                <select class="form-select" aria-label="Default" name="status" id="status" required>
                                                    <option value="1"  {{ old('status', $category->status ==1? 'selected' : '')}}>Enabled</option>
                                                    <option value="0" {{ old('status', $category->status ==0 ? 'selected' : '')}}>Disabled</option>
                                                </select>
                                                @if ($errors->has('status'))
                                                    <div  class="invalid-feedback d-block">
                                                        {{ $errors->first('status') }}
                                                    </div>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="mb-3">
                                                <label class="form-label">Sort Order</label>
                                                <input type="text" class="form-control" name="sort_order" value="{{old('sort_order', ($category->sort_order ? $category->sort_order : '' ))}}">
                                                @if ($errors->has('sort_order'))
                                                    <div  class="invalid-feedback d-block">
                                                        {{ $errors->first('sort_order') }}
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
    <script src="{{ url("libs/select2/js/select2.min.js")}}"></script>

    <script>

        $('.select2-example').select2({
            placeholder: ' Select Class'
        });

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
