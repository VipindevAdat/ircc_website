@extends('layouts.app')
@section('head')
    <style>
        .donate-form .form-inner {
            margin-bottom: 20px !important;
        }
        #loading {
            background: url('../front/images/loading.gif') no-repeat center center;
            position: fixed;
            width: 100%;
            height: 100vh;
            z-index: 999;
        }
        .donate-form .radio-img{
            line-height: 45px;
            height: 45px;
        }
        .donate-form .form-group.radio-img {
            margin-bottom: 0px !important;
        }
        .donate-detais{
            height: 300px;
            display: flex;
            align-items: center;
        }
    </style>
@endsection
@section('content')
    <div id="loading" style="display: none;"></div>
    <main id="content" class="site-main bg-light-grey">
        <!-- Inner Banner html start-->
        <section class="inner-banner-wrap">
            <div class="inner-baner-container" style="background-image: url('./front/images/inner-banner-img-1.jpg');">
                <div class="container">
                    <div class="inner-banner-content">
                        <h1 class="inner-title">Registration</h1>
                    </div>
                </div>
            </div>
        </section>

        <div class="carrer-page-section">
            <div class="container">
                <div class="vacancy-section">
                    <div class="section-head text-center">
                    <div class="row">
                        <div class="col-lg-8 offset-lg-2">
                            <div class="sub-title">Success</div>
                            <img src="{{ asset('front/images/success.png') }}" alt="success" style="height: 75px;">
                            <h2 class="section-title">Successfully submitted !!</h2>
                            <h5 class="mt-4" style="color:#F15B43">
                                To complete the registration process please check your mail for the verification link.
                                If you dont see the mail please check your spam folder.
                            </h5>
                            <div style="margin-top: 18px; text-align: left; display: inline-block;">
                                <p style="margin-bottom: 8px;"><strong>Your registration is not complete yet.</strong></p>
                                <p style="margin-bottom: 12px;">Once you verify your email, please log in to the IRCC Dashboard to complete the remaining steps.</p>
                                <p style="margin-bottom: 8px;"><strong>✅ Don’t Miss These Steps:</strong></p>
                                <p style="margin-bottom: 8px;">🔗 <a href="{{ url('/login') }}" target="_blank">{{ url('/login') }}</a></p>
                                <p style="margin-bottom: 4px;">➡️ Go to Dashboard</p>
                                <p style="margin-bottom: 4px;">➡️ Add participant details</p>
                                <p style="margin-bottom: 0;">➡️ Complete payment to confirm your spot</p>
                            </div>
                        </div>
                    </div>
                    </div>
                
                </div>
            </div>
        </div>
    </main>
@endsection
