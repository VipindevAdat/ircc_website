@extends('admin.layouts.app')

@section('page-title', 'Awards page')

@section('content')
<div class="mb-4">
    <nav style="--bs-breadcrumb-divider: '>';" aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}"><i class="bi bi-globe2 small me-2"></i> Dashboard</a></li>
            <li class="breadcrumb-item active">Awards page</li>
        </ol>
    </nav>
</div>

@if(session('success_message'))
    <div class="alert alert-success">{{ session('success_message') }}</div>
@endif

<div class="card mb-4">
    <div class="card-body">
        <h6 class="card-title mb-3">Page headings</h6>
        <p class="text-muted small">These appear on the public <a href="{{ route('awards') }}" target="_blank" rel="noopener">/awards</a> page.</p>
        <form method="POST" action="{{ route('admin.award-graphics.headings') }}" class="row g-3">
            @csrf
            <div class="col-md-6">
                <label class="form-label">Banner title (H1)</label>
                <input type="text" name="awards_banner_title" class="form-control" value="{{ old('awards_banner_title', $bannerTitle?->value) }}" maxlength="255">
            </div>
            <div class="col-md-6">
                <label class="form-label">Section heading (H2)</label>
                <input type="text" name="awards_section_heading" class="form-control" value="{{ old('awards_section_heading', $sectionHeading?->value) }}" maxlength="255">
            </div>
            <div class="col-12">
                <button type="submit" class="btn btn-primary">Save headings</button>
            </div>
        </form>
    </div>
</div>

<div class="card">
    <div class="card-body">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <h6 class="card-title mb-0">Award images</h6>
            <a href="{{ route('admin.award-graphics.create') }}" class="btn btn-info"><i class="bi bi-plus"></i> Add image</a>
        </div>
        <p class="text-muted small">Order: first image = large left column, second = right column, any further images = full width below (same as the previous static layout).</p>

        <div class="table-responsive">
            <table class="table table-bordered align-middle">
                <thead>
                    <tr>
                        <th>Preview</th>
                        <th>Source</th>
                        <th>Alt text</th>
                        <th>Sort</th>
                        <th>Status</th>
                        <th width="150">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($graphics as $g)
                        <tr>
                            <td>
                                @if($g->imageUrl())
                                    <img src="{{ $g->imageUrl() }}" alt="" style="max-height:80px;max-width:120px;object-fit:contain;">
                                @else
                                    <span class="text-muted">No file</span>
                                @endif
                            </td>
                            <td>
                                @if($g->usesPublicAssetPath())
                                    <span class="badge text-bg-secondary">Bundled</span>
                                @elseif($g->imageUrl())
                                    <span class="badge text-bg-primary">Upload</span>
                                @else
                                    <span class="text-muted">—</span>
                                @endif
                            </td>
                            <td>{{ $g->alt_text ?: '—' }}</td>
                            <td>{{ $g->sort_order }}</td>
                            <td>{{ $g->status ? 'On' : 'Off' }}</td>
                            <td>
                                <a href="{{ route('admin.award-graphics.edit', $g->id) }}" class="btn btn-sm btn-outline-primary">Edit</a>
                                <form action="{{ route('admin.award-graphics.destroy', $g->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Delete this graphic?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-outline-danger">Delete</button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="text-muted">No images yet. Run database migrations to load the default bundled award images here, or add your own.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
