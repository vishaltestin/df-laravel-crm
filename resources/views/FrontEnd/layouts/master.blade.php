<!doctype html >
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" data-layout="vertical" data-sidebar="light" data-sidebar-size="lg" data-sidebar-image="none" data-preloader="disable">

<head>
    <meta charset="utf-8" />
    <title>@yield('title') | LMS - Admin & Dashboard </title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta content="Premium Multipurpose Admin & Dashboard " name="description" />
    <meta content="Digital Fueled IT Pvt. Ltd. " name="author" />
  <meta name="csrf-token" content="{{ csrf_token() }}">  <!-- App favicon -->
    <link rel="shortcut icon" href="{{ URL::asset('build/images/favicon.ico')}}">
    @include('FrontEnd/layouts.head-css')
</head>

@section('body')
    @include('FrontEnd/layouts.body')
@show
    <!-- Begin page -->
    <div id="layout-wrapper">
        @include('FrontEnd/layouts.topbar')
        @include('FrontEnd/layouts.sidebar')
        <!-- ============================================================== -->
        <!-- Start right Content here -->
        <!-- ============================================================== -->
        <div class="main-content">
            <div class="page-content">
                <div class="container-fluid">
                    @yield('content')
                </div>
                <!-- container-fluid -->
            </div>
            <!-- End Page-content -->
            @include('FrontEnd/layouts.footer')
        </div>
        <!-- end main content-->
    </div>
    <!-- END layout-wrapper -->

    @include('FrontEnd/layouts.customizer')

    <!-- JAVASCRIPT -->
    @include('FrontEnd/layouts.vendor-scripts')
</body>

</html>
