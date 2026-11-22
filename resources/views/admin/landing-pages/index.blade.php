@extends('admin.layouts.app')

@section('page-title', 'Landing Pages')

@section('content')
<div class="mb-4">
    <nav style="--bs-breadcrumb-divider: '>';" aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
            <li class="breadcrumb-item active">Landing Pages</li>
        </ol>
    </nav>
</div>

<div class="card">
    <div class="card-body">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <h5 class="mb-0">Year Landing Pages</h5>
            <a href="{{ route('admin.landing-pages.create') }}" class="btn btn-primary">Add Year</a>
        </div>
        <div class="table-responsive">
            <table class="table table-bordered">
                <thead>
                    <tr>
                        <th>Year</th>
                        <th>Title</th>
                        <th>Status</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($landingPages as $landingPage)
                    <tr>
                        <td>{{ $landingPage->year }}</td>
                        <td>{{ $landingPage->title }}</td>
                        <td>{{ $landingPage->is_active ? 'Active' : 'Inactive' }}</td>
                        <td>
                            <a href="{{ route('admin.landing-pages.edit', $landingPage->id) }}" class="btn btn-sm btn-primary">Edit</a>
                            <form action="{{ route('admin.landing-pages.destroy', $landingPage->id) }}" method="POST" style="display:inline-block;">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('Delete this landing page?')">Delete</button>
                            </form>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
PHP