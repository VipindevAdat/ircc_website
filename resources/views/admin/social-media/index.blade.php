@extends('admin.layouts.app')

@section('page-title', 'Social media')
@section('content')

<div class="mb-4">
    <nav style="--bs-breadcrumb-divider: '>';" aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item">
                <a href="{{ route('admin.dashboard') }}">
                    <i class="bi bi-globe2 small me-2"></i> Dashboard
                </a>
            </li>
            <li class="breadcrumb-item active" aria-current="page">Social media</li>
        </ol>
    </nav>
</div>

@if(session('success_message'))
    <div class="alert alert-success">{{ session('success_message') }}</div>
@endif

<div class="card mb-4">
    <div class="card-body">
        <h6 class="card-title mb-3">Social media links</h6>
        <p class="text-muted small mb-4">
            These URLs power the header, footer, About page partner icons, and the WhatsApp link on Contact.
            Use full URLs (e.g. <code>https://www.facebook.com/STEMIRCC</code>). For WhatsApp you may paste
            <code>https://wa.me/9714008683</code> or digits only.
        </p>

        <form method="POST" action="{{ route('admin.social-media.store') }}">
            @csrf
            <div class="row">
                @foreach($codes as $code)
                    @php $row = $settings->get($code); @endphp
                    <div class="col-md-6 mb-3">
                        <label class="form-label" for="field_{{ $code }}">{{ $row?->title ?? $code }}</label>
                        <input
                            type="text"
                            id="field_{{ $code }}"
                            name="{{ $code }}"
                            class="form-control @error($code) is-invalid @enderror"
                            value="{{ old($code, $row?->value) }}"
                            placeholder="{{ $row?->placeholder }}"
                        >
                        @error($code)
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                @endforeach
            </div>
            <button type="submit" class="btn btn-primary">Save</button>
        </form>
    </div>
</div>

@endsection
