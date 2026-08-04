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
                            <div class="sub-title">Registration Failed</div>
                            <img src="{{ asset('front/images/error.png') }}" alt="error" style="height: 75px;" onerror="this.style.display='none';">
                            <h2 class="section-title" style="color: #F15B43;">Registration Failed !!</h2>
                            <h5 class="mt-4" style="color:#F15B43;">
                                @if(session('error'))
                                    {{ session('error') }}
                                @else
                                    We encountered an error while processing your registration. Please try again or contact support if the problem persists.
                                @endif
                            </h5>
                            <div class="mt-4">
                                <a href="{{ route('register') }}" class="button-round" style="margin-right: 10px;">Try Again</a>
                                <a href="{{ route('contact-us') }}" class="button-round">Contact Support</a>
                            </div>
                        </div>
                    </div>
                    </div>
                
                </div>
            </div>
        </div>
    </main>
@endsection
