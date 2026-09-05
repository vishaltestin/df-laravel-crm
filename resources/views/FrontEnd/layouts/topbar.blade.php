<header id="page-topbar">
    <div class="layout-width">
        <div class="navbar-header">
            <div class="d-flex">
                <!-- LOGO -->
                <div class="navbar-brand-box horizontal-logo">
                    <a href="index" class="logo logo-dark">
                        <span class="logo-sm">
                            <img src="{{ URL::asset('build/images/logo-sm.png') }}" alt="LMS" height="22">
                        </span>
                        <span class="logo-lg">
                            <img src="{{ URL::asset('build/images/logo-dark.png') }}" alt="" height="17">
                        </span>
                    </a>
                    <a href="index" class="logo logo-light">
                        <span class="logo-sm">
                            <img src="{{ URL::asset('build/images/logo-sm.png') }}" alt="LMS" height="22">
                        </span>
                        <span class="logo-lg">
                            <img src="{{ URL::asset('build/images/logo-light.png') }}" alt="" height="17">
                        </span>
                    </a>
                </div>

                <button type="button" class="btn btn-sm px-3 fs-16 header-item vertical-menu-btn topnav-hamburger" id="topnav-hamburger-icon">
                    <span class="hamburger-icon">
                        <span></span>
                        <span></span>
                        <span></span>
                    </span>
					
				  </button>

              </div>

            <div class="d-flex align-items-center">
 
	<span style="float:right">
    <u>			
@php
    $currentInstituteId = Session::get('current_institute');
    $institutes = Session::get('institutes', []);

    $currentInstituteName = '';

    foreach ($institutes as $inst) {
        if ($inst['id'] == $currentInstituteId) {
            $currentInstituteName = $inst['name'];
            break;
        }
    }
@endphp

   {{ $currentInstituteName }} 
    </u>
</span>       
@php
    $institutes = Session::get('institutes', []);
@endphp

@if(count($institutes) > 1)
<div class="dropdown topbar-head-dropdown ms-1 header-item">

    <button type="button" class="btn btn-icon btn-topbar btn-ghost-secondary rounded-circle" data-bs-toggle="dropdown">
 <i class="ri-school-line  fs-22"></i>     </button>

    <div class="dropdown-menu dropdown-menu-lg p-0 dropdown-menu-end">

        <div class="p-3 border-bottom">
            <div class="row align-items-center">
                <div class="col">
                    <h6 class="m-0 fw-semibold fs-15"> Change Institute </h6>
                </div> <div class="col">
@if(!Session::get('current_institute'))<a href="{{ route('clear.institute') }}" class="m-0 fw-semibold fs-15">
    View All
</a>     @endif           </div>
            </div>
        </div>

        <div class="p-2">
        @php
            $institutes = collect($institutes);
        @endphp

        @foreach($institutes->chunk(3) as $chunk)
            <div class="row g-0">

                @foreach($chunk as $inst)
                    <div class="col">
                        <a class="dropdown-icon-item"
                           href="{{ url('switch-institute/'.$inst['enc_id']) }}">

                            {{-- 🔥 Image optional --}}
                            {{-- 
                            <img src="{{ asset($inst['logo'] ?? 'uploads/default.png') }}"
                                 style="height:40px;width:40px;object-fit:cover;">
                            --}}

                            <span>{{ strtoupper($inst['name']) }}</span>
                        </a>
                    </div>
                @endforeach

            </div>
        @endforeach

        </div>

    </div>
</div>
@endif

             
             
                <div class="ms-1 header-item d-none d-sm-flex">
                    <button type="button" class="btn btn-icon btn-topbar btn-ghost-secondary rounded-circle" data-toggle="fullscreen">
                        <i class='bx bx-fullscreen fs-22'></i>
                    </button>
                </div>

                <div class="ms-1 header-item d-none d-sm-flex">
                    <button type="button" class="btn btn-icon btn-topbar btn-ghost-secondary rounded-circle light-dark-mode">
                        <i class='bx bx-moon fs-22'></i>
                    </button>
                </div>
				
				
					  


            <div class="dropdown topbar-head-dropdown ms-1 header-item" id="notificationDropdown">

    <button type="button"
        class="btn btn-icon btn-topbar btn-ghost-secondary rounded-circle"
        id="page-header-notifications-dropdown"
        data-bs-toggle="dropdown"
        data-bs-auto-close="outside"
        aria-haspopup="true"
        aria-expanded="false">

        <i class='bx bx-bell fs-22'></i>

        {{-- Unread Count --}}
  <span id="notiBellBadge"
    class="position-absolute topbar-badge fs-10 translate-middle badge rounded-pill bg-danger"
    style="display:none;">
    <span id="notiBellCount">0</span>
    <span class="visually-hidden">unread messages</span>
</span>

    </button>

    <div class="dropdown-menu dropdown-menu-lg dropdown-menu-end p-0"
        aria-labelledby="page-header-notifications-dropdown">

        <div class="dropdown-head bg-secondary bg-pattern rounded-top">
            <div class="p-3">
                <div class="row align-items-center">
                    <div class="col">
                        <h6 class="m-0 fs-16 fw-semibold text-white"> Notifications </h6>
                    </div>
                    <div class="col-auto dropdown-tabs">
                        <span class="badge bg-light-subtle text-body fs-13">
                            <span id="notiNewText">0 New</span>
                        </span>
                    </div>
                </div>
            </div>

            <div class="px-2 pt-2">
                <ul class="nav nav-tabs dropdown-tabs nav-tabs-custom" id="notificationItemsTab" role="tablist">

                    <li class="nav-item waves-effect waves-light">
                        <a class="nav-link active" data-bs-toggle="tab" href="#all-noti-tab" role="tab"
                           aria-selected="true" onclick="loadNotifications('all')">
                            All (<span id="notiAllCount">0</span>)
                        </a>
                    </li>

                    <li class="nav-item waves-effect waves-light">
                        <a class="nav-link" data-bs-toggle="tab" href="#messages-tab" role="tab"
                           aria-selected="false" onclick="loadNotifications('message')">
                            Messages
                        </a>
                    </li>

                    <li class="nav-item waves-effect waves-light">
                        <a class="nav-link" data-bs-toggle="tab" href="#alerts-tab" role="tab"
                           aria-selected="false" onclick="loadNotifications('alert')">
                            Alerts
                        </a>
                    </li>

                </ul>
            </div>
        </div>

        <div class="tab-content position-relative" id="notificationItemsTabContent">

            {{-- ALL --}}
            <div class="tab-pane fade show active py-2 ps-2" id="all-noti-tab" role="tabpanel">
                <div data-simplebar style="max-height: 300px;" class="pe-2">
                    <div id="all-noti-list"></div>

                    <div class="my-3 text-center view-all">
                        <button type="button"
                            class="btn btn-soft-success waves-effect waves-light"
                            onclick="markAllNotificationsRead()">
                            Mark All Read <i class="ri-check-double-line align-middle"></i>
                        </button>
                    </div>
                </div>
            </div>

            {{-- MESSAGES --}}
            <div class="tab-pane fade py-2 ps-2" id="messages-tab" role="tabpanel">
                <div data-simplebar style="max-height: 300px;" class="pe-2">
                    <div id="messages-noti-list"></div>
                </div>
            </div>

            {{-- ALERTS --}}
            <div class="tab-pane fade py-2 ps-2" id="alerts-tab" role="tabpanel">
                <div data-simplebar style="max-height: 300px;" class="pe-2">
                    <div id="alerts-noti-list"></div>
                </div>
            </div>

            <div class="notification-actions" id="notification-actions">
                <div class="d-flex text-muted justify-content-center">
                    Select
                    <div id="select-content" class="text-body fw-semibold px-1">0</div>
                    Result
                    <button type="button"
                        class="btn btn-link link-danger p-0 ms-3"
                        onclick="markSelectedNotificationsRead()">
                        Mark Read
                    </button>
                </div>
            </div>

        </div>
    </div>
</div>

                <div class="dropdown ms-sm-3 header-item topbar-user">
                    <button type="button" class="btn" id="page-header-user-dropdown" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                        <span class="d-flex align-items-center">
                            
						 @if(session('avatar') && file_exists(public_path('uploads/users/' . session('avatar'))))
							<img src="{{ asset('uploads/users/' . session('avatar')) }}" alt="   {{Sentinel::getUser()->first_name}}  {{Sentinel::getUser()->last_name}}   Avatar" class="rounded-circle header-profile-user">
						@else
							<img src="{{ URL::asset('images/user-img-2.png') }}" alt="Default Avatar" class="rounded-circle header-profile-user">
						@endif



                            
                        <span class="text-start ms-xl-2">
                                <span class="d-none d-xl-inline-block ms-1 fw-semibold user-name-text">@if (Sentinel::check())

                                {{Sentinel::getUser()->first_name}}  {{Sentinel::getUser()->last_name}} [{{ Sentinel::getUser()?->roles->first()?->name }}]
                            @else
                                Guest

                            @endif</span>
                                <span class="d-none d-xl-block ms-1 fs-12 user-name-sub-text">Member since  {{date_convert(Sentinel::getUser()->created_at)}}</span>
                            </span>
                        </span>

                    </button>
                    <div class="dropdown-menu dropdown-menu-end">
                        <!-- item-->
                        <h6 class="dropdown-header">Welcome   @if (Sentinel::check())

                                {{Sentinel::getUser()->first_name}}  {{Sentinel::getUser()->last_name}} [{{ Sentinel::getUser()?->roles->first()?->name }}]
                                @else
                                     Guest

                                @endif!</h6>
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
                </div>
            </div>
        </div>
    </div>
</header>


<!-- removeNotificationModal -->
<div id="removeNotificationModal" class="modal fade zoomIn" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close" id="NotificationModalbtn-close"></button>
            </div>
            <div class="modal-body">
                <div class="mt-2 text-center">
                    <lord-icon src="https://cdn.lordicon.com/gsqxdxog.json" trigger="loop" colors="primary:#495057,secondary:#f06548" style="width:100px;height:100px"></lord-icon>
                    <div class="mt-4 pt-2 fs-15 mx-4 mx-sm-5">
                        <h4 class="fw-bold">Are you sure ?</h4>
                        <p class="text-muted mx-4 mb-0">Are you sure you want to remove this Notification ?</p>
                    </div>
                </div>
                <div class="d-flex gap-2 justify-content-center mt-4 mb-2">
                    <button type="button" class="btn w-sm btn-light" data-bs-dismiss="modal">Close</button>
                    <button type="button" class="btn w-sm btn-danger" id="delete-notification">Yes, Delete It!</button>
                </div>
            </div>

        </div><!-- /.modal-content -->
    </div><!-- /.modal-dialog -->
</div><!-- /.modal -->
