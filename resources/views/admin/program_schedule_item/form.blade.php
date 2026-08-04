@extends('admin.layouts.app')

@section('page-title', 'Program Schedule Item')

@section('content')
<div class="mb-4">
    <nav style="--bs-breadcrumb-divider: '>';" aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
            <li class="breadcrumb-item"><a href="{{ route('admin.program-schedule.index') }}">Program Schedule</a></li>
            <li class="breadcrumb-item"><a href="{{ route('admin.program-schedule-items.index', $day->id) }}">{{ $day->title }}</a></li>
            <li class="breadcrumb-item active">{{ $item->id ? 'Edit item' : 'Add item' }}</li>
        </ol>
    </nav>
</div>

<div class="card">
    <div class="card-body">
        <form method="POST" action="{{ $item->id ? route('admin.program-schedule-items.update', [$day->id, $item->id]) : route('admin.program-schedule-items.store', $day->id) }}">
            @csrf
            {{ $item->id ? method_field('PUT') : '' }}
            <h6 class="card-title mb-4">{{ $item->id ? 'Edit' : 'Add' }} schedule item</h6>
            <div class="row">
                <div class="col-md-4 mb-3">
                    <label class="form-label">Time slot</label>
                    <input type="text" name="time_slot" class="form-control" value="{{ old('time_slot', $item->time_slot) }}" placeholder="e.g. 8:00 am – 8:30 am" required>
                    @error('time_slot')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
                </div>
                <div class="col-md-4 mb-3">
                    <label class="form-label">Sort order</label>
                    <input type="number" name="sort_order" class="form-control" value="{{ old('sort_order', $item->sort_order ?? 1) }}" min="0" required>
                </div>
                <div class="col-md-4 mb-3">
                    <label class="form-label">Status</label>
                    <select name="status" class="form-select" required>
                        <option value="1" {{ old('status', $item->status ?? true) ? 'selected' : '' }}>Enabled</option>
                        <option value="0" {{ !old('status', $item->status ?? true) ? 'selected' : '' }}>Disabled</option>
                    </select>
                </div>
                <div class="col-md-12 mb-3">
                    <label class="form-label">Activity title</label>
                    <input type="text" name="title" class="form-control" value="{{ old('title', $item->title) }}" required>
                    @error('title')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
                </div>
                <div class="col-md-12 mb-3">
                    <label class="form-label">Venue</label>
                    <input type="text" name="venue" class="form-control" value="{{ old('venue', $item->venue) }}" placeholder="e.g. VENUE-I, Lobby, or VIRTUAL">
                    <small class="text-muted">For virtual days, use "VIRTUAL" to show the virtual badge.</small>
                </div>
                <div class="col-md-12 mb-3">
                    <label class="form-label">Details (speakers, chair, etc.)</label>
                    <textarea id="editor" name="details" class="form-control" rows="8">{{ old('details', $item->details) }}</textarea>
                    <small class="text-muted">Optional. Use for speaker names, titles, chair/co-chair, and panel details.</small>
                </div>
                <div class="col-md-12">
                    <button type="submit" class="btn btn-primary">Save</button>
                    <a href="{{ route('admin.program-schedule-items.index', $day->id) }}" class="btn btn-secondary">Cancel</a>
                </div>
            </div>
        </form>
    </div>
</div>
@endsection

@section('script')
<script src="{{ url('libs/ckeditor5/ckeditor.js') }}"></script>
<script>
    ClassicEditor.create(document.querySelector('#editor'));
</script>
@endsection
