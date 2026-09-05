<!doctype html>
<html lang="en">

<head>
    <meta charset="utf-8" />
    <title>{{ $page->title ?? 'Landing Page' }}</title>

    <link rel="shortcut icon" href="{{ asset('build/images/favicon.ico') }}">

    <link href="{{ asset('build/libs/swiper/swiper-bundle.min.css') }}" rel="stylesheet" />
    <script src="{{ asset('build/js/layout.js') }}"></script>

    <link href="{{ asset('build/css/bootstrap.min.css') }}" rel="stylesheet" />
    <link href="{{ asset('build/css/icons.min.css') }}" rel="stylesheet" />
    <link href="{{ asset('build/css/app.min.css') }}" rel="stylesheet" />
    <link href="{{ asset('build/css/custom.min.css') }}" rel="stylesheet" />
</head>

<body data-bs-spy="scroll" data-bs-target="#navbar-example">
    <div class="layout-wrapper landing">

    {{-- Your dynamic sections from render.blade.php --}}
    @yield('content')
</div>
    <script src="{{ asset('build/libs/bootstrap/js/bootstrap.bundle.min.js') }}"></script>
    <script src="{{ asset('build/libs/simplebar/simplebar.min.js') }}"></script>
    <script src="{{ asset('build/libs/node-waves/waves.min.js') }}"></script>
    <script src="{{ asset('build/libs/feather-icons/feather.min.js') }}"></script>
    <script src="{{ asset('build/js/plugins.js') }}"></script>

    <script src="{{ asset('build/libs/swiper/swiper-bundle.min.js') }}"></script>
    <script src="{{ asset('build/js/pages/landing.init.js') }}"></script>
</body>

</html>
