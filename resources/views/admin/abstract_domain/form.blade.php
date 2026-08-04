@extends('admin.layouts.app')

@section('page-title', 'Abstract Domain')

@section('content')
    <div class="row flex-column-reverse flex-md-row">
        <div class="col-md-12">
            <div class="tab-content" id="myTabContent">
                <div class="tab-pane fade show active" id="profile" role="tabpanel" aria-labelledby="profile-tab">
                    <div class="mb-4">
                        <div class="card mb-4">
                            <form  method="POST" action="{{ $domain->id ? route('admin.domain.update',$domain->id) : route('admin.domain.store') }}">
                                @csrf
                                {{ $domain->id ? method_field('PUT'):'' }}
                                <div class="card-body">
                                    <h6 class="card-title mb-4">{{ $domain->id ? 'Edit' : 'Add' }} Abstract Domain</h6>
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="mb-3">
                                                <label class="form-label">Abstract Domain Topic</label>
                                                <input type="text" class="form-control" name="domain" value="{{old('domain', ($domain->domain ? $domain->domain : '' ))}}" required>
                                                @if ($errors->has('domain'))
                                                    <div  class="invalid-feedback d-block">
                                                        {{ $errors->first('domain') }}
                                                    </div>
                                                @endif
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="mb-3">
                                                <label class="form-label">Sort Order</label>
                                                <input type="text" class="form-control" name="sort_order" value="{{old('sort_order', ($domain->sort_order ? $domain->sort_order :1 ))}}" required>
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

                                                    <option value="1"  {{ old('status', $domain->status ==1? 'selected' : '')}}>Enabled</option>
                                                    <option value="0" {{ old('status', $domain->status ==0 ? 'selected' : '')}}>Disabled</option>
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
