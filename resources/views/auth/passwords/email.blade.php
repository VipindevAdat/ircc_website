@extends('layouts.app')

@section('content')
<main id="content" class="site-main bg-light-grey">
        <section class="inner-banner-wrap">
            <div class="inner-baner-container" style="background-image: url('../front/images/inner-banner-img-1.jpg');">
                <div class="container">
                    <div class="inner-banner-content">
                        <h1 class="inner-title">Login</h1>
                    </div>
                </div>
            </div>
        </section>
        <div class="donate-section">
            <div class="container">
                <form method="POST" action="{{ route('password.email') }}" class="donate-form">
                        @csrf
                        <div class="donate-detais form-inner">
                        <h3>Forgot Password</h3>
                        <div class="row mb-3">
                            <label for="email" class="col-md-4 col-form-label text-md-end">{{ __('Email Address') }}</label>

                            <div class="col-md-6">
                                <input id="email" type="email" class="form-control @error('email') is-invalid @enderror" name="email" value="{{ old('email') }}" required autocomplete="email" autofocus>

                                @error('email')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                        </div>

                        <div class="row mb-0">
                            <div class="col-md-6 offset-md-4">
                                <button type="submit" class="btn  button-round">
                                    {{ __('Send Password Reset Link') }}
                                </button>
                            </div>
                        </div>
                        </div>
                </form>
            
        </div>
    </div>
</div>
</main>
@endsection
