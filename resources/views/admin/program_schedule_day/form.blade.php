@extends('admin.layouts.app')

@section('page-title', 'Program Schedule Day')

@section('content')
<div class="mb-4">
    <nav style="--bs-breadcrumb-divider: '>';" aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
            <li class="breadcrumb-item"><a href="{{ route('admin.program-schedule.index') }}">Program Schedule</a></li>
            <li class="breadcrumb-item active">{{ $day->id ? 'Edit day' : 'Add day' }}</li>
        </ol>
    </nav>
</div>

<div class="card">
    <div class="card-body">
        <form method="POST" action="{{ $day->id ? route('admin.program-schedule-days.update', $day->id) : route('admin.program-schedule-days.store') }}">
            @csrf
            {{ $day->id ? method_field('PUT') : '' }}
            <h6 class="card-title mb-4">{{ $day->id ? 'Edit' : 'Add' }} schedule day</h6>
            <div class="row">
                <div class="col-md-8 mb-3">
                    <label class="form-label">Day title</label>
                    <input type="text" name="title" class="form-control" value="{{ old('title', $day->title) }}" required>
                    @error('title')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
                </div>
                <div class="col-md-4 mb-3">
                    <label class="form-label">Sort order</label>
                    <input type="number" name="sort_order" class="form-control" value="{{ old('sort_order', $day->sort_order ?? 1) }}" min="0" required>
                </div>
                <div class="col-md-4 mb-3">
                    <label class="form-label">Show on public page</label>
                    <select name="is_visible" class="form-select">
                        <option value="1" {{ old('is_visible', $day->is_visible ?? true) ? 'selected' : '' }}>Visible</option>
                        <option value="0" {{ !old('is_visible', $day->is_visible ?? true) ? 'selected' : '' }}>Hidden</option>
                    </select>
                </div>
                <div class="col-md-4 mb-3">
                    <label class="form-label">Virtual day</label>
                    <select name="is_virtual" class="form-select">
                        <option value="1" {{ old('is_virtual', $day->is_virtual) ? 'selected' : '' }}>Yes (VIRTUAL badge)</option>
                        <option value="0" {{ !old('is_virtual', $day->is_virtual) ? 'selected' : '' }}>No</option>
                    </select>
                </div>
                <div class="col-md-4 mb-3">
                    <label class="form-label">Status</label>
                    <select name="status" class="form-select" required>
                        <option value="1" {{ old('status', $day->status ?? true) ? 'selected' : '' }}>Enabled</option>
                        <option value="0" {{ !old('status', $day->status ?? true) ? 'selected' : '' }}>Disabled</option>
                    </select>
                </div>
                <div class="col-md-12">
                    <button type="submit" class="btn btn-primary">Save</button>
                    <a href="{{ route('admin.program-schedule.index') }}" class="btn btn-secondary">Cancel</a>
                </div>
            </div>
        </form>
    </div>
</div>
@endsection
