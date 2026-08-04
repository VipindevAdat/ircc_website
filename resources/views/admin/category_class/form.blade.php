@extends('admin.layouts.app')

@section('page-title', 'Category Class')

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
                            <form  method="POST" action="{{ $categoryClass->id ? route('admin.category-class.update',$categoryClass->id) : route('admin.category-class.store') }}">
                                @csrf
                                {{ $categoryClass->id ? method_field('PUT'):'' }}
                                <div class="card-body">
                                    <h6 class="card-title mb-4">{{ $categoryClass->id ? 'Edit' : 'Add' }} Category</h6>
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="mb-3">
                                                <label class="form-label">Category</label>
                                                <select class="form-select" name="category_id">
                                                    <option value="" disabled {{ old('category_id', $categoryClass->category_id) ? '' : 'selected' }}>Select a category</option>
                                                    @foreach ($categories as $category)
                                                        <option value="{{ $category->id }}" {{ old('category_id', $categoryClass->category_id) == $category->id ? 'selected' : '' }}>
                                                            {{ $category->name }}
                                                        </option>
                                                    @endforeach
                                                </select>
                                                @if ($errors->has('category_id'))
                                                    <div class="invalid-feedback d-block">
                                                        {{ $errors->first('category_id') }}
                                                    </div>
                                                @endif
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="mb-3">
                                                <label class="form-label">Class</label>
                                                <select class="form-select select2-example" aria-label="Default select example" name="class[]" id="select_type" multiple>
                                                    <option value="" disabled {{ old('class', $categoryClass->class ?? []) ? '' : 'selected' }}>Select a class</option>
                                                    @for ($i = 1; $i <= 12; $i++)
                                                        <option value="{{ $i }}" {{ in_array($i, (array) old('class', $categoryClass->class ?? [])) ? 'selected' : '' }}>
                                                            {{ $i }}
                                                        </option>
                                                    @endfor
                                                    <option value="other" {{ in_array('other', (array) old('class', $categoryClass->class ?? [])) ? 'selected' : '' }}>
                                                        Others
                                                    </option>
                                                </select>
                                                @if ($errors->has('class'))
                                                    <div class="invalid-feedback d-block">
                                                        {{ $errors->first('class') }}
                                                    </div>
                                                @endif
                                          </div>
                                        </div>
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="mb-3">
                                                <label class="form-label">Status</label>
                                                <select class="form-select" aria-label="Default" name="status" id="status" required>

                                                    <option value="1"  {{ old('status', $categoryClass->status ==1? 'selected' : '')}}>Enabled</option>
                                                    <option value="0" {{ old('status', $categoryClass->status ==0 ? 'selected' : '')}}>Disabled</option>
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
    <script src="{{ url("libs/select2/js/select2.min.js")}}"></script>
    <script>

        $('.select2-example').select2({
            placeholder: 'Select'
        });

    </script>
@endsection
