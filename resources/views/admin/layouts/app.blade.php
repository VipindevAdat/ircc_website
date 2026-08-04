<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>International Research Conference for Children</title>

    <!-- Favicon -->
    <link rel="shortcut icon" href="{{ url("assets/images/favicon.png") }}"/>

    <!-- Google fonts -->
    <link rel="preconnect" href="https://fonts.gstatic.com">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600&display=swap" rel="stylesheet">

    <!-- Bootstrap icons -->
    <link rel="stylesheet" href="{{ url("dist/icons/bootstrap-icons-1.4.0/bootstrap-icons.min.css") }}" type="text/css">
    <!-- Bootstrap Docs -->
    <link rel="stylesheet" href="{{ url("dist/css/bootstrap-docs.css") }}" type="text/css">
    {{-- Font Awesome --}}
    <link rel="stylesheet" href="{{ url("dist/icons/font-awesome/css/font-awesome.min.css") }}" type="text/css">
    <link rel="stylesheet" href="//cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css" type="text/css">

    @yield('head')  
    @stack('styles')
    <!-- Main style file -->
    <link rel="stylesheet" href="{{ url("dist/css/app.min.css") }}" type="text/css">

    <style>
        .transparent-preloader {
            position: fixed;
            right: 0;
            left: 0;
            top: 0;
            bottom: 0;
            z-index: 9999;
            background-color: rgba(0,0,0,.4);
            display: -webkit-box;
            display: -ms-flexbox;
            display: flex;
            -webkit-box-align: center;
            -ms-flex-align: center;
            align-items: center;
            -webkit-box-pack: center;
            -ms-flex-pack: center;
            justify-content: center;
            gap: 15px;
        }
        .transparent-preloader .preloader-icon {
            border-radius: 50%;
            border: 5px solid #fff;
            border-top-color: #ff6e40;
            width: 50px;
            height: 50px;
            margin-left: 10px;
            -webkit-animation: spin .3s linear infinite;
            animation: spin .3s linear infinite;
        }
        .transparent-preloader h3 {
            color: #fff;
        }
    </style>
    <!--[if lt IE 9]>
    <script src="https://oss.maxcdn.com/html5shiv/3.7.3/html5shiv.min.js"></script>
    <script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
    <![endif]-->
</head>
<body class="">

<!-- preloader -->
<div class="preloader">
    <img src="{{ url('assets/images/ircc.png') }}" alt="logo">
    <div class="preloader-icon"></div>
</div>
<!-- ./ preloader -->

<div class="transparent-preloader" id="preloader" style="display: none">
    <div class="preloader-icon"></div>
    <h3>Loading....</h3>
</div>

<!-- sidebars -->

<!-- notifications sidebar -->
<div class="sidebar" id="notifications">
    <div class="sidebar-header d-block align-items-end">
        <div class="align-items-center d-flex justify-content-between py-4">
            Notifications
            <button data-sidebar-close>
                <i class="bi bi-arrow-right"></i>
            </button>
        </div>
        <ul class="nav nav-pills">
            <li class="nav-item">
                <a class="nav-link active" data-bs-toggle="tab" href="#activities">Activities</a>
            </li>
        </ul>
    </div>
</div>
<!-- ./ notifications sidebar -->


<!-- ./ sidebars -->

<!-- menu -->
@include('admin.components.menu')
<!-- ./  menu -->

<!-- layout-wrapper -->
<div class="layout-wrapper">

    <!-- header -->
    @include('admin.components.header-basic')
    <!-- ./ header -->

    <!-- content -->
    <div class="content @yield('contentClassName')">
        @yield('content')
    </div>
    <!-- ./ content -->

    <!-- content-footer -->
    <footer class="content-footer">
        <div>© {{ date('Y') }} International Research Conference for Children - GYRA Global-All Rights Reserved.</div>
        <div>
            <nav class="nav gap-4">
                {{-- <a href="https://themeforest.net/licenses/standard" class="nav-link">Licenses</a> --}}
               
                <a href="https://123.guide" target="_blank" class="nav-link">Get Help</a>
            </nav>
        </div>
    </footer>
    <!-- ./ content-footer -->

</div>
<!-- ./ layout-wrapper -->

<!-- Bundle scripts -->
<script src="{{ url("libs/bundle.js") }}"></script>

<!-- Notifications -->
<script>
    

</script>
<!-- Notifications -->
@yield('script')
@stack('scripts')

<!-- Main Javascript file -->
<script src="{{ url("dist/js/app.min.js") }}"></script>
<script src="{{ url("front/js/sweet-alert.js")}}"></script>
<script src="//cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>
<script type="text/javascript">
    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });
</script>
</body>
</html>
