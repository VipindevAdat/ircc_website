@extends('reviewer.layouts.auth')

@section('page-title', 'Reset Password')

@section('content')

    <div class="form-wrapper">
        <div class="container">
            <div class="card">
                <div class="row g-0">
                    <div class="col">
                        <div class="row align-items-center">
                            <div class="col-md-10 offset-md-1">
                                <div class="d-block d-lg-none text-center text-lg-start">
                                    <img width="120" src="{{ url('assets/images/logo.png') }}" alt="logo">
                                </div>
                                <div class="my-5 text-center text-lg-start">
                                    <h1 class="display-8 mb-3">{{ __('Confirm Password') }}</h1>
                                    <p class="text-muted">{{ __('Please confirm your password before continuing.') }}</p>
                                </div>
                                <form method="POST" action="{{ route('password.confirm') }}">
                                    @csrf
                                    <div class="mb-3">
                                        <input type="password" class="form-control @error('password') is-invalid @enderror" placeholder="Enter password" required autocomplete="current-password">
                                        @error('password')
                                            <div class="invalid-feedback" role="alert">
                                                {{ $message }}
                                            </div>
                                        @enderror
                                    </div>
                                    <div class="text-center text-lg-start">
                                        <button type="submit" class="btn btn-primary"> {{ __('Confirm Password') }}</button>

                                        @if (Route::has('password.request'))
                                            <p class="small">Can't access your account? <a href="{{ route('password.request') }}">{{ __('Forgot Your Password?') }}</a>.</p>
                                        @endif
                                    </div>
                                </form>
                                
                                <p class="text-center d-block d-lg-none mt-4 mt-lg-0">
                                    You can now <a href="{{ route('login') }}">sign in</a>{{--  or <a href="#">create an
                                        account</a> --}}.
                                </p>
                            </div>
                        </div>
                    </div>
                    <div class="col d-none d-lg-flex border-start align-items-center justify-content-between flex-column text-center">
                        <div class="logo">
                            <img width="225" src="{{ url('assets/images/logo.png') }}" alt="logo">
                        </div>
                        <div>
                            <h3 class="fw-bold mb-5">Do a different action</h3>
                            <div class="text-center">
                                You can now <a class="btn btn-primary btn-sm" href="{{ route('login') }}">sign in</a>{{--  or
                                <a class="btn btn-primary btn-sm" href="#">create an account</a> --}}.
                            </div>
                        </div>
                        <ul class="list-inline">
                            @foreach($informations->whereIn('type',[1,2]) as $info)
                            <li class="list-inline-item">
                                <a target="_blank" href="{{ route('information.show',$info->slug) }}">{{ $info->title }}</a>
                            </li>
                            @endforeach
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>

@endsection


{{-- @extends('layouts.auth')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">{{ __('Confirm Password') }}</div>

                <div class="card-body">
                    {{ __('Please confirm your password before continuing.') }}

                    <form method="POST" action="{{ route('password.confirm') }}">
                        @csrf

                        <div class="row mb-3">
                            <label for="password" class="col-md-4 col-form-label text-md-end">{{ __('Password') }}</label>

                            <div class="col-md-6">
                                <input id="password" type="password" class="form-control @error('password') is-invalid @enderror" name="password" required autocomplete="current-password">

                                @error('password')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                        </div>

                        <div class="row mb-0">
                            <div class="col-md-8 offset-md-4">
                                <button type="submit" class="btn btn-primary">
                                    {{ __('Confirm Password') }}
                                </button>

                                @if (Route::has('password.request'))
                                    <a class="btn btn-link" href="{{ route('password.request') }}">
                                        {{ __('Forgot Your Password?') }}
                                    </a>
                                @endif
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
 --}}