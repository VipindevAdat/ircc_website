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
        <form method="POST" action="{{ $landingPage->id ? route('admin.landing-pages.update', $landingPage->id) : route('admin.landing-pages.store') }}">
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