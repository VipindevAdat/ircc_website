@extends('admin.layouts.app')

@section('page-title', $graphic->id ? 'Edit award image' : 'Add award image')

@section('content')
<div class="mb-4">
    <nav style="--bs-breadcrumb-divider: '>';" aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
            <li class="breadcrumb-item"><a href="{{ route('admin.award-graphics.index') }}">Awards page</a></li>
            <li class="breadcrumb-item active">{{ $graphic->id ? 'Edit' : 'Add' }}</li>
        </ol>
    </nav>
</div>

<div class="card">
    <div class="card-body">
        <form method="POST" action="{{ $graphic->id ? route('admin.award-graphics.update', $graphic->id) : route('admin.award-graphics.store') }}" enctype="multipart/form-data">
            @csrf
            @if($graphic->id)
                @method('PUT')
            @endif

            <div class="mb-3">
                <label class="form-label">Image @if(!$graphic->id)<span class="text-danger">*</span>@endif</label>
                <div class="mb-2">
                    @php
                        $previewUrl = $graphic->id ? $graphic->imageUrl() : null;
                    @endphp
                    <img class="img-fluid" style="max-height:200px;object-fit:contain;" src="{{ $previewUrl ?: url('assets/images/no-image.png') }}" alt="">
                </div>
                @if($graphic->id && $graphic->usesPublicAssetPath())
                    <p class="text-muted small mb-2">This row uses the same bundled images as the public site used before CMS uploads. Upload a file below to replace it with your own image.</p>
                @endif
                <input type="file" name="featured" class="form-control" accept=".png,.jpg,.jpeg,.webp" {{ $graphic->id ? '' : 'required' }}>
                @if($graphic->id)
                    <small class="text-muted">Leave empty to keep the current image.</small>
                @endif
                @error('featured')
                    <div class="text-danger small">{{ $message }}</div>
                @enderror
            </div>

            <div class="mb-3">
                <label class="form-label">Alt text (accessibility)</label>
                <input type="text" name="alt_text" class="form-control" value="{{ old('alt_text', $graphic->alt_text) }}" maxlength="500">
                @error('alt_text')<div class="text-danger small">{{ $message }}</div>@enderror
            </div>

            <div class="mb-3">
                <label class="form-label">Sort order</label>
                <input type="number" name="sort_order" class="form-control" value="{{ old('sort_order', $graphic->sort_order) }}" min="0" required>
                @error('sort_order')<div class="text-danger small">{{ $message }}</div>@enderror
            </div>

            <div class="mb-3">
                <label class="form-label">Status</label>
                <select name="status" class="form-select" required>
                    <option value="1" {{ old('status', $graphic->status) == 1 ? 'selected' : '' }}>Enabled</option>
                    <option value="0" {{ old('status', $graphic->status) === 0 ? 'selected' : '' }}>Disabled</option>
                </select>
            </div>

            <button type="submit" class="btn btn-primary">{{ $graphic->id ? 'Update' : 'Create' }}</button>
            <a href="{{ route('admin.award-graphics.index') }}" class="btn btn-secondary">Cancel</a>
        </form>
    </div>
</div>
@endsection
