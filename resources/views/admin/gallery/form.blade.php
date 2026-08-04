@extends('admin.layouts.app')

@section('page-title', 'Gallery')

@section('content')
<div class="row flex-column-reverse flex-md-row">
    <div class="col-md-12">
        <div class="tab-content" id="myTabContent">
            <div class="tab-pane fade show active" id="profile" role="tabpanel" aria-labelledby="profile-tab">
                <div class="mb-4">
                    <div class="card mb-4">
                        <form method="POST" action="{{ $gallery->id ? route('admin.gallery.update',$gallery->id) : route('admin.gallery.store') }}" enctype='multipart/form-data'>
                            @csrf
                            {{ $gallery->id ? method_field('PUT'):'' }}
                            <div class="card-body">
                                <h6 class="card-title mb-4">{{ $gallery->id ? 'Edit' : 'Add' }} Gallery</h6>
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label class="form-label">Name</label>
                                            <input type="text" class="form-control" name="name" value="{{old('name', ($gallery->name ? $gallery->name : '' ))}}" required>
                                            @if ($errors->has('name'))
                                            <div class="invalid-feedback d-block">
                                                {{ $errors->first('name') }}
                                            </div>
                                            @endif
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <label for="inputAddress" class="form-label">Gallery Type *</label>
                                        <select class="form-select" aria-label="Default" name="gallery_type" id="gallery_type" required>
                                            <option value="1" {{ old('gallery_type', $gallery->gallery_type ==1? 'selected' : '')}}>Photos</option>

                                            <option value="2" {{ old('gallery_type', $gallery->gallery_type ==2? 'selected' : '')}}>Video</option>

                                        </select>
                                    </div>
                                </div>
                                <div class="row g-3" id="video-div" class="video-div" style="{{$gallery->gallery_type!=2?'display:none':''}}">
                                    <div class="col-12 media-form">
                                        <label for="inputAddress" class="form-label">Video Link *( https://youtu.be/tf0cxJyClyg)</label>
                                        <input type="text" class="form-control" id="video_link" placeholder="Video Link" class="form-control @error('video_link') is-invalid @enderror" name="video_link" value="{{ old('video_link',$gallery->video_link) }}" {{$gallery->type==2?'required':''}}>
                                    </div>

                                </div>
                                <div class="row  g-3" id="image-div" style="{{$gallery->gallery_type==2?'display:none':''}}">
                                    <div class="col-md-6">
                                    <label for="inputAddress" class="form-label">Select Gallery Images </label>
                                       <div class="mb-3">
                                             <input class="form-control file-upload" type="file" id="file-upload" name="file-upload[]" accept=".png, .jpg, .jpeg" multiple>
                                           
                                       </div>
                                    </div>
                                 </div>
                                <div class="row  g-3">
                                    <div class="col-md-12">
                                        @if($gallery->getmedia('gallery'))
                                        <div class="row g-3" id="imgGallery" class="imgGallery">
                                            @foreach($gallery->getmedia('gallery') as $image)
                                            <div class="image-div col-md-3" id="img{{$image->id}}">
                                                <img class="images image-input-wrapper" style="width: 200px;height:200px; object-fit:contain" src="{{$image->getUrl()}}" title="{{$image->filename}}" />
                                                <span class="delete-gallery" data-id="{{$image->id}}" data-bs-toggle="modal" data-bs-target="#delete-modal"><i class="fa fa-trash red" aria-hidden="true"></i></span>
                                            </div>
                                            @endforeach

                                        </div>
                                        @endif
                                   
                                    </div>
                           
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label class="form-label">Sort Order</label>
                                            <input type="text" class="form-control" name="sort_order" value="{{old('sort_order', ($gallery->sort_order ? $gallery->sort_order : '' ))}}" required>
                                            @if ($errors->has('sort_order'))
                                            <div class="invalid-feedback d-block">
                                                {{ $errors->first('sort_order') }}
                                            </div>
                                            @endif
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label class="form-label">Status</label>
                                            <select class="form-select" aria-label="Default" name="status" id="status" required>

                                                <option value="1" {{ old('status', $gallery->status ==1? 'selected' : '')}}>Enabled</option>
                                                <option value="0" {{ old('status', $gallery->status ==0 ? 'selected' : '')}}>Disabled</option>
                                            </select>
                                            @if ($errors->has('status'))
                                            <div class="invalid-feedback d-block">
                                                {{ $errors->first('status') }}
                                            </div>
                                            @endif
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label">View on Homepage</label>
                                        <div class="kt-checkbox-inline">
                                            <label class="kt-checkbox">
                                            <input type="checkbox" {{ $gallery->is_home ==1 ? 'checked' : '' }} name="checkbox" id="check" value="0" >
                                                <input type="hidden" name="is_home" id="val" value="@if($gallery->is_home){{ old('is_home', $gallery->is_home) }} @else 0 @endif">
                                            
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
<div class="modal fade" id="delete-modal" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="staticBackdropLabel">Delete Image</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>

            <div class="modal-body">
                <!-- <input type="hidden" name="_method" value="DELETE"> -->
                <input type="hidden" name="_token" value="{{ csrf_token() }}">
                <input type="hidden" id="deleteId" name="deleteId">
                <input type="hidden" id="galleryId" name="galleryId" value="{{$gallery->id}}">
                <p>('Are you sure you want to delete this image)</p>
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
<script>
@if(Session::has('success_message'))
$(document).ready(function() {
    Swal.fire({
        icon: 'success',
        title: '{{ Session::get('
        success_message ') }}',
        // text: 'we will notify through email once the adjustment is complete',
        footer: ''
    })
})
@endif
@if(Session::has('failure_message'))
$(document).ready(function() {
    Swal.fire({
        icon: 'error',
        title: '{{ Session::get('
        failure_message ') }}',
        // text: 'we will notify through email once the adjustment is complete',
        footer: ''
    })
})
@endif
const dt = new DataTransfer();
$(function() {
    var previewImages = function(input, index) {
        if (input.files) {
            var filesAmount = input.files.length;
            for (i = 0; i < filesAmount; i++) {
                var name = input.files[i]['name']
                var reader = new FileReader();
                reader.fileName = name;
                reader.onload = function(e) {
                    var html = $($.parseHTML('<div class="image-div col-md-3" ><img class="images image-input-wrapper" style="width: 200px;height:200px; object-fit:contain" src="' + e.target.result + '" title="' + event.target.fileName + '"/> <span data-file="' + event.target.fileName + '" class="btn-delete"><i class="fa fa-trash red" aria-hidden="true"></i></span></div>'));
                    $('#imgGallery').append(html);
                }
                reader.readAsDataURL(input.files[i]);
            }
        }
    };
    var i = 0;
    $('.file-upload').on('change', function() {
        previewImages(this, ++i);
        for (let file of this.files) {
            dt.items.add(file);
        }
    });
});
$('#check').click(function(){
      if ($('#check').is(":checked"))
         $("#val").attr( "value", "1" );
      else
         $("#val").attr( "value", "0" );
    });
$('#gallery_type').on('change', function() {
    if ($(this).val() == '1') {
        $('#video-div').hide();
        $('#image-div').show();
        $('#file-upload').prop('required', true);
        $('#video_link').prop('required', false);
    } else {
        $('#video-div').show();
        $('#image-div').hide();
        $('#file-upload').prop('required', false);
        $('#video_link').prop('required', true);
    }

});

$(document).on('click', '.delete-gallery', function(e) {

    e.preventDefault();

    $('#deleteId').val($(this).data('id'));
    var val = $(this).data('id');
    $(document).on('click', '.delete-confirm', function() {

        $.ajax({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            type: "POST",
            url: "{{ route('admin.deleteGalleryById') }}",
            dataType: 'JSON',
            data: {
                id: val,
                galleryId: $('#galleryId').val()
            },
            success: function(response) {
                $('#delete-modal').modal('hide');
                toastr.success("Deleted successfully", "Success");  
                $('#img' + val).remove();
                // toastr.success("Deleted successfully", "Success");            
            },
            error: function(error) {
                $('#delete-modal').modal('hide');
                toastr.error("Something went wrong'", {
                    time: 3000,
                    position: 'top-right',
                });

            }

        });

    });


});
</script>
@endsection