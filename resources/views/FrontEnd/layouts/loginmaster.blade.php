<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" data-topbar="light">

    <head>
    <meta charset="utf-8" />
    <title>@yield('title') | LMS - Admin & Dashboard </title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta content="Premium Multipurpose Admin & Dashboard " name="description" />
    <meta content="Digital Fueled IT Pvt. Ltd. " name="author" />
    <!-- App favicon -->
    <link rel="shortcut icon" href="{{ URL::asset('build/images/favicon.ico')}}">
        @include('FrontEnd/layouts.head-css')
  </head>

    @yield('body')

    @yield('content')

    @include('FrontEnd/layouts.vendor-scripts')
    </body>
</html>
