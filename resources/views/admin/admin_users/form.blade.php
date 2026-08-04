@extends('admin.layouts.app')

@section('page-title', ($user->id ? 'Edit' : 'Add') . ' Admin User')

@section('content')

    <div class="mb-4">
        <nav style="--bs-breadcrumb-divider: '>';" aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item">
                    <a href="{{ route('admin.dashboard') }}">
                        <i class="bi bi-globe2 small me-2"></i> Dashboard
                    </a>
                </li>
                <li class="breadcrumb-item">
                    <a href="{{ route('admin.admin-users.index') }}">Admin Users</a>
                </li>
                <li class="breadcrumb-item active" aria-current="page">{{ $user->id ? 'Edit' : 'Add' }}</li>
            </ol>
        </nav>
    </div>

    <div class="row flex-column-reverse flex-md-row">
        <div class="col-md-8">
            <div class="card mb-4">
                <form method="POST" action="{{ $user->id ? route('admin.admin-users.update', $user->id) : route('admin.admin-users.store') }}">
                    @csrf
                    @if($user->id)
                        {{ method_field('PUT') }}
                    @endif
                    <div class="card-body">
                        <h6 class="card-title mb-4">{{ $user->id ? 'Edit' : 'Add' }} Admin User</h6>

                        @if ($errors->any())
                            <div class="alert alert-danger" style="background-color: #fff3f1; border-color: #F15B43; color: #F15B43;">
                                <ul class="mb-0">
                                    @foreach ($errors->all() as $error)
                                        <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        @endif

                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label">Name <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control @error('name') is-invalid @enderror" name="name" value="{{ old('name', $user->name ?? '') }}" required>
                                    @error('name')
                                        <div class="invalid-feedback d-block">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label">Email <span class="text-danger">*</span></label>
                                    <input type="email" class="form-control @error('email') is-invalid @enderror" name="email" value="{{ old('email', $user->email ?? '') }}" required>
                                    @error('email')
                                        <div class="invalid-feedback d-block">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label">Password @if(!$user->id)<span class="text-danger">*</span>@else <small class="text-muted">(leave blank to keep current)</small>@endif</label>
                                    <input type="password" class="form-control @error('password') is-invalid @enderror" name="password" {{ !$user->id ? 'required' : '' }}>
                                    @error('password')
                                        <div class="invalid-feedback d-block">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label">Confirm Password @if(!$user->id)<span class="text-danger">*</span>@endif</label>
                                    <input type="password" class="form-control" name="password_confirmation" {{ !$user->id ? 'required' : '' }}>
                                </div>
                            </div>
                            <div class="col-md-12">
                                <div class="mb-3">
                                    <a href="{{ route('admin.admin-users.index') }}" class="btn btn-secondary">Cancel</a>
                                    <button type="submit" class="btn btn-primary" style="background-color: #F15B43; border-color: #F15B43;">
                                        {{ $user->id ? 'Update' : 'Create' }} Admin User
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>

        @if($user->id)
        <div class="col-md-4">
            <div class="card mb-4">
                <div class="card-body">
                    <h6 class="card-title mb-3">User Info</h6>
                    <p><strong>ID:</strong> {{ $user->id }}</p>
                    <p><strong>Created:</strong> {{ $user->created_at ? $user->created_at->format('d-M-Y h:i A') : 'N/A' }}</p>
                    <p><strong>Updated:</strong> {{ $user->updated_at ? $user->updated_at->format('d-M-Y h:i A') : 'N/A' }}</p>
                    <p><strong>Status:</strong>
                        @if($user->email_verified_at)
                            <span class="badge bg-success">Active</span>
                        @else
                            <span class="badge bg-warning text-dark">Unverified</span>
                        @endif
                    </p>
                </div>
            </div>
        </div>
        @endif
    </div>

@endsection
