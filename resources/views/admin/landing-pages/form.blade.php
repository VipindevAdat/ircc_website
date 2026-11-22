@extends('admin.layouts.app')

@section('page-title', $landingPage->id ? 'Edit Landing Page' : 'Add Landing Page')

@section('content')
<div class="mb-4">
    <nav style="--bs-breadcrumb-divider: '>';" aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
            <li class="breadcrumb-item"><a href="{{ route('admin.landing-pages.index') }}">Landing Pages</a></li>
            <li class="breadcrumb-item active">{{ $landingPage->id ? 'Edit' : 'Add' }}</li>
        </ol>
    </nav>
</div>

<div class="card">
    <div class="card-body">
        <form method="POST" action="{{ $landingPage->id ? route('admin.landing-pages.update', $landingPage->id) : route('admin.landing-pages.store') }}" enctype="multipart/form-data">
            @csrf
            @if($landingPage->id)
                @method('PUT')
            @endif

            <div class="mb-3">
                <label class="form-label">Year</label>
                <input type="number" name="year" class="form-control" value="{{ old('year', $landingPage->year) }}" required>
            </div>

            <div class="mb-3">
                <label class="form-label">Title</label>
                <input type="text" name="title" class="form-control" value="{{ old('title', $landingPage->title) }}">
            </div>

            <div class="mb-3">
                <label class="form-label">Header Logo <small class="text-muted">(recommended 420×110 px, max 5MB)</small></label>
                @if($landingPage->header_logo)
                    <div class="mb-2"><img src="{{ url('uploads/' . $landingPage->header_logo) }}" style="max-height:60px;" alt=""></div>
                @endif
                <input type="file" name="header_logo" class="form-control" accept="image/*">
            </div>

            <div class="mb-3">
                <label class="form-label">Footer Logo <small class="text-muted">(recommended 241×86 px, max 5MB)</small></label>
                @if($landingPage->footer_logo)
                    <div class="mb-2"><img src="{{ url('uploads/' . $landingPage->footer_logo) }}" style="max-height:60px;" alt=""></div>
                @endif
                <input type="file" name="footer_logo" class="form-control" accept="image/*">
            </div>

            <div class="mb-3">
                <label class="form-label">Banner Image <small class="text-muted">(recommended 1024×576 px, max 10MB)</small></label>
                @if($landingPage->banner_image)
                    <div class="mb-2"><img src="{{ url('uploads/' . $landingPage->banner_image) }}" style="max-height:120px;" alt=""></div>
                @endif
                <input type="file" name="banner_image" class="form-control" accept="image/*">
            </div>

            <div class="mb-3">
                <label class="form-label">Video <small class="text-muted">(mp4/webm/ogg, max 100MB)</small></label>
                @if($landingPage->video)
                    <div class="mb-2"><video src="{{ url('uploads/' . $landingPage->video) }}" style="max-height:120px;" controls></video></div>
                @endif
                <input type="file" name="video" class="form-control" accept="video/mp4,video/webm,video/ogg">
            </div>

            <div class="mb-3">
                <label class="form-label">Video Thumbnail <small class="text-muted">(recommended 1280×720 px, max 5MB)</small></label>
                @if($landingPage->video_thumbnail)
                    <div class="mb-2"><img src="{{ url('uploads/' . $landingPage->video_thumbnail) }}" style="max-height:120px;" alt=""></div>
                @endif
                <input type="file" name="video_thumbnail" class="form-control" accept="image/*">
            </div>

            <div class="mb-3">
                <label class="form-label">Active</label>
                <select name="is_active" class="form-select">
                    <option value="1" {{ old('is_active', $landingPage->is_active) == 1 ? 'selected' : '' }}>Active</option>
                    <option value="0" {{ old('is_active', $landingPage->is_active) == 0 ? 'selected' : '' }}>Inactive</option>
                </select>
            </div>

            <button type="submit" class="btn btn-primary">{{ $landingPage->id ? 'Update' : 'Create' }}</button>
            <a href="{{ route('admin.landing-pages.index') }}" class="btn btn-secondary">Cancel</a>
        </form>
    </div>
</div>
@endsection
PHP
