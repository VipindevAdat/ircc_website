@extends('layouts.app')

@section('content')
<style>
    .verify-email-section {
        padding: 40px 0 60px;
    }
    .verify-email-box {
        max-width: 720px;
        margin: 0 auto;
        background: #ffffff;
        border-radius: 16px;
        box-shadow: 0 10px 30px rgba(0,0,0,0.08);
        padding: 32px 36px 28px;
        text-align: left;
    }
    .verify-email-box h3 {
        font-size: 26px;
        font-weight: 600;
        margin-bottom: 18px;
        text-align: center;
        color: #222;
    }
    .verify-email-box .alert {
        border-radius: 10px;
        font-size: 14px;
        padding: 10px 14px;
        margin-bottom: 18px;
    }
    .verify-email-box p {
        font-size: 15px;
        color: #444;
        margin-bottom: 8px;
    }
    .verify-email-box .verify-intro {
        margin-bottom: 16px;
        text-align: center;
    }
    .verify-email-box .verify-cta-row {
        margin-top: 22px;
        text-align: center;
    }
    .verify-email-box .verify-steps {
        margin-top: 22px;
        border-radius: 12px;
        background: #F8FAFF;
        padding: 18px 20px 14px;
        border-left: 4px solid #F15B43;
    }
    .verify-email-box .verify-steps-title {
        font-weight: 600;
        margin-bottom: 10px;
        color: #222;
    }
    .verify-email-box .verify-steps p {
        margin-bottom: 6px;
        font-size: 14px;
    }
    .verify-email-box .verify-steps a {
        color: #F15B43;
        word-break: break-all;
    }
    .verify-resend-btn {
        background-color: #F15B43;
        border-color: #F15B43;
        color: #fff !important;
        padding: 10px 26px;
        border-radius: 999px;
        font-size: 14px;
        font-weight: 500;
        text-decoration: none !important;
    }
    .verify-resend-btn:hover {
        background-color: #d94a35;
        border-color: #d94a35;
        color: #fff !important;
    }
    @media (max-width: 767px) {
        .verify-email-box {
            padding: 22px 18px 20px;
        }
        .verify-email-box h3 {
            font-size: 22px;
        }
        .verify-email-box p {
            font-size: 14px;
        }
    }
</style>
<div id="loading" style="display: none;"></div>
<main id="content" class="site-main bg-light-grey">
<section class="inner-banner-wrap">
    <div class="inner-baner-container" style="background-image: url('../front/images/inner-banner-img-1.jpg');">
        <div class="container">
            <div class="inner-banner-content">
                <h1 class="inner-title">Verify Your Email Address</h1>
            </div>
        </div>
    </div>
</section>

<!-- <div class="donate-section">
<div class="container">

    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">{{ __('Verify Your Email Address') }}</div>

                <div class="card-body">
                    @if (session('resent'))
                        <div class="alert alert-success" role="alert">
                            {{ __('A fresh verification link has been sent to your email address.') }}
                        </div>
                    @endif

                    {{ __('Before proceeding, please check your email for a verification link.') }}
                    {{ __('If you did not receive the email') }},
                    <form class="d-inline" method="POST" action="{{ route('verification.resend') }}">
                        @csrf
                        <button type="submit" class="btn btn-link p-0 m-0 align-baseline">{{ __('click here to request another') }}</button>.
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
</div> -->

<div class="campaign-page-section verify-email-section">
    <div class="campaign-page-inner">
        <div class="container">
            <div class="row justify-content-center">
            <div class="col-lg-10 secondary">
                <div class="sidebar d-flex justify-content-center">
                    <div class="verify-email-box">
                        <h3>{{ __('Verify Your Email Address') }}</h3>
                        @if (session('resent'))
                        <div class="alert alert-success" role="alert">
                            {{ __('A fresh verification link has been sent to your email address.') }}
                        </div>
                        @endif
                    <div class="verify-intro">
                        <p>{{ __('Before proceeding, please check your email for a verification link.') }}</p>
                        <p>{{ __('If you did not receive the email') }},</p>
                    </div>
                    <div class="verify-cta-row">
                        <form class="d-inline" method="POST" action="{{ route('verification.resend') }}">
                            @csrf
                            <button type="submit" class="verify-resend-btn">{{ __('click here to request another') }}</button>
                        </form>
                    </div>
                    <div class="verify-steps">
                        <p class="verify-steps-title">Your registration is not complete yet.</p>
                        <p>Once you verify your email, please log in to the IRCC Dashboard to complete the remaining steps.</p>
                        <p class="verify-steps-title">✅ Don’t Miss These Steps:</p>
                        <p>🔗 <a href="https://ircc.world/login" target="_blank">https://ircc.world/login</a></p>
                        <p>➡️ Go to Dashboard</p>
                        <p>➡️ Add participant details</p>
                        <p style="margin-bottom: 0;">➡️ Complete payment to confirm your spot</p>
                    </div>
                        
                    </div>
                    
                </div>
            </div>
            </div>
        </div>
    </div>
</div>
@endsection
