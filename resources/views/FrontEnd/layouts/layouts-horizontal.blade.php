<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" data-layout="horizontal" data-layout-style="" data-layout-position="fixed"  data-topbar="light">

<head>
    <meta charset="utf-8" />
    <title> @yield('title')| LMS - Admin & Dashboard </title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta content="Premium Multipurpose Admin & Dashboard " name="description" />
    <meta content="Digital Fueled IT Pvt. Ltd. " name="author" />
    <!-- App favicon -->
    <link rel="shortcut icon" href="{{ URL::asset('build/images/favicon.ico')}}">
    @include('FrontEnd/layouts.head-css')
</head>
<body>

    <!-- Begin page -->
    <div id="layout-wrapper">
        @include('FrontEnd/layouts.topbar')
        @include('FrontEnd/layouts.sidebar')
        <!-- ============================================================== -->
        <!-- Start right Content here -->
        <!-- ============================================================== -->
        <div class="main-content">
            <div class="page-content">
                <!-- Start content -->
                <div class="container-fluid">
                    @yield('content')
                </div> <!-- content -->
            </div>
            @include('FrontEnd/layouts.footer')
        </div>
        <!-- ============================================================== -->
        <!-- End Right content here -->
        <!-- ============================================================== -->
    </div>
    <!-- END wrapper -->

    <!-- Right Sidebar -->
    @include('FrontEnd/layouts.customizer')
    <!-- END Right Sidebar -->

    @include('FrontEnd/layouts.vendor-scripts')
</body>

</html>
