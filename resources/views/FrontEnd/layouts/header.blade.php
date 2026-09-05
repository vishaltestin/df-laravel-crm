<header class="main-header">
    <!-- Logo -->
    <a href="{{ env('APP_URL') }}/opa-dashboard/welcome" class="logo">
        <!-- mini logo for sidebar mini 50x50 pixels -->
        <span class="logo-mini"><img src="{{ env('APP_URL') }}/dist/img/logo.jpeg" width="80px" height="50px"></span>
        <!-- logo for regular state and mobile devices -->
        <span class="logo-lg"><img src="{{ env('APP_URL') }}/dist/img/logo.jpeg" width="80px" height="50px">>
    </a>
    <!-- Header Navbar: style can be found in header.less -->
    <nav class="navbar navbar-static-top">
        <!-- Sidebar toggle button-->
        <a href="#" class="sidebar-toggle" data-toggle="push-menu" role="button">
            <span class="sr-only">Toggle navigation</span>
        </a>

        <div class="navbar-custom-menu">
            <ul class="nav navbar-nav">

 




                <li class="dropdown user user-menu">
                    <a href="#" class="dropdown-toggle" data-toggle="dropdown">
                        <img src="{{ env('APP_URL') }}/dist/img/user2-160x160.jpg" class="user-image" alt="User Image">
                        <span class="hidden-xs"> @if (Sentinel::check())

                                {{Sentinel::getUser()->first_name}}  {{Sentinel::getUser()->last_name}}
                            @else
                                Guest

                            @endif</span>
                    </a>
                    <ul class="dropdown-menu">
                        <!-- User image -->
                        <li class="user-header">
                            <img src="{{ env('APP_URL') }}/dist/img/user2-160x160.jpg" class="img-circle" alt="User Image">

                            <p>
                                @if (Sentinel::check())

                                     {{Sentinel::getUser()->first_name}}  {{Sentinel::getUser()->last_name}}
                                @else
                                     Guest

                                @endif
                                <small>Member since  {{date_convert(Sentinel::getUser()->created_at)}} </small>
                            </p>
                        </li>


                        <!-- Menu Footer-->
                        <li class="user-footer">



                            <div class="pull-left">
                                <a href="#" class="btn btn-default btn-flat">Profile</a>
                            </div>
                            <div class="pull-right">

                                <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display:none;">
    @csrf
</form>

<a href="#"
   onclick="event.preventDefault(); document.getElementById('logout-form').submit();"
   class="dropdown-item">
   <i class="bx bx-power-off font-size-16 align-middle me-1"></i>
   Log out
</a>

                            </div>
                        </li>
                    </ul>
                </li>
                <!-- Control Sidebar Toggle Button -->
                <li>
                    <a href="#" data-toggle="control-sidebar"><i class="fa fa-gears"></i></a>
                </li>
            </ul>
        </div>
    </nav>
</header>
<style>.skin-blue .main-header .logo {
	background-color: #fff !important;
	color: #fff;
	border-bottom: 0 solid transparent;
	padding-bottom: 18px;
	height: 55px;

}
.dropdown.bootstrap-select.show-tick.form-control {
    position: relative;
    z-index: 999;
}
</style>