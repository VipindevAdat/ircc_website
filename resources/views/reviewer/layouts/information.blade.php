<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>@if (!request()->segment(1)) Rotary Club of Northwest Austin  @else @yield('page-title') - Rotary Club of Northwest Austin @endif</title>

    <!-- Favicon -->
    <link rel="shortcut icon" href="{{ url("assets/images/favicon.ico") }}"/>

    <!-- Google fonts -->
    <link rel="preconnect" href="https://fonts.gstatic.com">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600&display=swap" rel="stylesheet">

    <!-- Bootstrap icons -->
    <link rel="stylesheet" href="{{ url("dist/icons/bootstrap-icons-1.4.0/bootstrap-icons.min.css") }}" type="text/css">
    <!-- Bootstrap Docs -->
    <link rel="stylesheet" href="{{ url("dist/css/bootstrap-docs.css") }}" type="text/css">

    @yield('head')

    <!-- Main style file -->
    <link rel="stylesheet" href="{{ url("dist/css/app.min.css") }}" type="text/css">

    <!--[if lt IE 9]>
    <script src="https://oss.maxcdn.com/html5shiv/3.7.3/html5shiv.min.js"></script>
    <script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
    <![endif]-->
</head>
<body class="top-menu dark2">

<!-- preloader -->
<div class="preloader">
    <img src="{{ url('assets/images/logo.png') }}" alt="logo">
    <div class="preloader-icon"></div>
</div>
<!-- ./ preloader -->

<!-- layout-wrapper -->
<div class="layout-wrapper">

    <!-- header -->
    @include('admin.components.information')
    <!-- ./ header -->

    <!-- content -->
    <div class="content @yield('contentClassName')">
        @yield('content')
    </div>
    <!-- ./ content -->

    <!-- content-footer -->
    <footer class="content-footer">
        <div>© {{ date('Y') }} iStock - <a href="http://webaune.com" target="_blank">Webaune</a></div>
        <div>
            <nav class="nav gap-4">
                {{-- <a href="https://themeforest.net/licenses/standard" class="nav-link">Licenses</a> --}}
                @foreach($informations as $info)
                <a href="{{ route('admin.information.show', $info->slug) }}" class="nav-link">{{ $info->title }}</a>
                @endforeach
                <a href="https://123.guide" target="_blank" class="nav-link">Get Help</a>
            </nav>
        </div>
    </footer>
    <!-- ./ content-footer -->

</div>
<!-- ./ layout-wrapper -->

<!-- Bundle scripts -->
<script src="{{ url("libs/bundle.js") }}"></script>

@yield('script')

<!-- Main Javascript file -->
<script src="{{ url("dist/js/app.min.js") }}"></script>
</body>
</html>
