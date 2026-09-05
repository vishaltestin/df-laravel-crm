@extends('FrontEnd.layouts.master')
@section('title') Organization -> {{ $organization->name }} @endsection
@section('css')
<link href="{{ URL::asset('build/libs/sweetalert2/sweetalert2.min.css') }}" rel="stylesheet" type="text/css" />

@endsection
@section('css')
@endsection
@section('content')

@php
  $role = $role ?? null;
  $orgId = $orgId ?? null;
@endphp




@component('FrontEnd.components.breadcrumb') ✅

@slot('li_1') Organization @endslot
        @slot('title') {{ $organization->name }} Details  @endslot
    @endcomponent
  <div class="row">
        <div class="col-lg-12">
            <div class="card mt-n4 mx-n4">
                <div class="bg-warning-subtle">
                    <div class="card-body pb-0 px-4">
                        <div class="row mb-3">
                            <div class="col-md">
                                <div class="row align-items-center g-3">
                                    <div class="col-md-auto">
                                        <div class="avatar-md">
                                            <div class="avatar-title bg-white rounded-circle">
														<img src="{{ URL::asset('') }}{{ $organization->logo }}" alt="" class="avatar-xs">
											</div>
                                        </div>
                                    </div>
                                    <div class="col-md">
                                        <div>
                                            <h4 class="fw-bold">{{ $organization->name }}</h4>
                                            <div class="hstack gap-3 flex-wrap">
                                                <div><i class="ri-building-line align-bottom me-1"></i> {{ $organization->address }}  {{ $organization->city }} 
					{{ $organization->state }} 
					{{ $organization->country }} ,{{ $organization->zipcode }}  </div>
                                                <div class="vr"></div>
                                                <div>Create Date : <span class="fw-medium">{{ datec($organization->created_at) }}
									  </span></div>
                                                
                                                <div class="vr"></div>
                                                <div class="badge rounded-pill bg-info fs-12">  
												@php
													$startDate = Carbon\Carbon::parse($organization->created_at);  
												@endphp
												Since{{ $startDate->diffForHumans() }}
	
												</div>
              

												@if ($organization->status == 0)
												<div class="badge rounded-pill bg-danger fs-12">Inactive</div>
												@else 
												<div class="badge rounded-pill bg-success fs-12">Active</div>
												@endif


	
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-auto">
                                <div class="hstack gap-1 flex-wrap">
                                    <a href="tel:{{ $organization->mobile }} "
                                class="avatar-title bg-success-subtle text-success fs-15 rounded">
                                <i class="ri-phone-line"></i>
                            </a>
                                     <a href="email?to=/{{ $organization->email }}"
                                class="avatar-title bg-danger-subtle text-danger fs-15 rounded">
                                <i class="ri-mail-line"></i>
                            </a>
                                    <a href="javascript:void(0);"
                                class="avatar-title bg-info-subtle text-info fs-15 rounded">
                                <i class=" ri-whatsapp-line "></i>
                            </a> 
                                </div>
                            </div>
                        </div>
						<ul class="nav nav-tabs-custom border-bottom-0" role="tablist">
                            <li class="nav-item">
                                <a class="nav-link active fw-bold" data-bs-toggle="tab" href="#project-overview" role="tab">
                                    Overview
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link fw-bold" data-bs-toggle="tab" href="#project-documents" role="tab">
                                    Team
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link fw-bold" data-bs-toggle="tab" href="#project-activities" role="tab">
                                    Activities
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link fw-bold" data-bs-toggle="tab" href="#project-team" role="tab">
                                    Designations
                                </a>
                            </li>
                        </ul>
                    </div>
                    <!-- end card body -->
                </div>
            </div>
            <!-- end card -->
        </div>
        <!-- end col -->
    </div>
    <!-- end row -->







 <div class="row">
        <div class="col-lg-12">
            <div class="tab-content text-muted">
                <div class="tab-pane fade show active" id="project-overview" role="tabpanel">
                    <div class="row">
                        <div class="col-xl-9 col-lg-8">
                            <div class="card">
                                <div class="card-body">
                                    <div class="text-muted">
                                        <h6 class="mb-3 fw-bold text-uppercase">Summary</h6>
                                     <p class="text-muted">    👋 Welcome   <strong>{{ $organization->name }}</strong> to our Company LMS.<br><br>

    You have been registered with us since
    <strong>@php
													$startDate = Carbon\Carbon::parse($organization->created_at);  
												@endphp
												Since{{ $startDate->diffForHumans() }}</strong>
    <span class="text-muted">({{ datec($organization->created_at) }})</span>.
	<hr>Our CRM tools empower your organization by bringing teams, data, and workflows together in one place. 
From managing users and teams to tracking activities and performance, everything becomes more organized, 
transparent, and efficient—helping you focus on growth and customer satisfaction.
</hr>
</p>

<p class="text-muted mb-2">
    Currently, you are managing:
</p>

<ul class="ps-4 text-muted">
    <li>
        <strong>{{ count($teamData) }}</strong> Teams
    </li>
    <li>
        <strong>{{ count($designationData) }}</strong> Designations
    </li>
    <li>
        <strong>{{ count($users) }}</strong> Users
    </li>
</ul>

<p class="text-muted mt-3">
    Keep building your organization efficiently with our CRM tools 🚀
</p>

                                        <div class="pt-3 border-top border-top-dashed mt-4">
                                            <div class="row gy-3">

                                                <div class="col-lg-3 col-sm-6">
                                                    <div>
                                                        <p class="mb-2 text-uppercase fw-medium fs-13">Create Date :</p>
                                                        <h5 class="fs-15 mb-0">{{ datec($organization->created_at) }}</h5>
                                                    </div>
                                                </div>
                                                <div class="col-lg-3 col-sm-6">
                                                    <div>
                                                        <p class="mb-2 text-uppercase fw-medium fs-13">Last Login:</p>
                                                        <h5 class="fs-15 mb-0">Today</h5>
                                                    </div>
                                                </div>
                                                <div class="col-lg-3 col-sm-6">
                                                    <div>
                                                        <p class="mb-2 text-uppercase fw-medium fs-13">Priority :</p>
                                                        <div class="badge bg-danger fs-12">High</div>
                                                    </div>
                                                </div>
                                                <div class="col-lg-3 col-sm-6">
                                                    <div>
                                                        <p class="mb-2 text-uppercase fw-medium fs-13">Status :</p>
                                                        <div class="badge bg-warning fs-12">Active</div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                          </div>
                                </div>
                                <!-- end card body -->
                            </div>
                            <!-- end card -->

                               <!-- end card -->
                        </div>
                        <!-- ene col -->
                     <div class="col-xl-3 col-lg-4">

    {{-- ================= TEAMS ================= --}}
    <div class="card">
	<div class="card-header align-items-center d-flex"> <h4 class="card-title mb-0 flex-grow-1">Teams</h4></div>
								
        <div class="card-body">
            <h5 class="card-title mb-4"></h5>
            <div class="d-flex flex-wrap gap-2 fs-16">

     @foreach($teamData as $team)
  <h4 class="fs-18 lh-base fw-bold mb-0"><i class="ri-stop-fill align-middle fs-18 text-warning me-2"></i> {{ $team['name'] }} <span class="text-success"> <i class="ri-group-line"></i> {{ $team['total_users'] }}</span> </h4>  
  <div class="col-sm-auto">
    <div class="avatar-group">

        {{-- FIRST 3 USER AVATARS --}}
        @foreach($team['users'] as $u)
            <div class="avatar-group-item">
                <a href="javascript:void(0);"
                   class="d-inline-block"
                   data-bs-toggle="tooltip"
                   data-bs-placement="top"
                   title="{{ $u->first_name }} {{ $u->last_name }}">

                    <img
                        src="{{ ($u->avatar && file_exists(public_path('uploads/users/'.$u->avatar)))
                            ? asset('uploads/users/'.$u->avatar)
                            : asset('images/user-img-2.png') }}"
                        alt="{{ $u->first_name }}"
                        class="rounded-circle avatar-xxs">
                </a>
            </div>
        @endforeach

        {{-- REMAINING COUNT --}}
        @if($team['extra_count'] > 0)
            <div class="avatar-group-item">
                <a href="javascript:void(0);">
                    <div class="avatar-xxs">
                        <span class="avatar-title rounded-circle bg-success text-white">
                            {{ $team['extra_count'] }}
                        </span>
                    </div>
                </a>
            </div>
        @endif

    </div>
</div>

 
@endforeach


            </div>
        </div>
    </div>
    {{-- end card --}}

    {{-- ================= USERS ================= --}}
    <div class="card">
        <div class="card-header align-items-center d-flex border-bottom-dashed">
            <h4 class="card-title mb-0 flex-grow-1">Users</h4>
            <div class="flex-shrink-0">
                
            </div>
        </div>

        <div class="card-body">
            <div data-simplebar style="height: 235px;" class="mx-n3 px-3">
                <div class="vstack gap-3">
 
                    @forelse($users as $u)
                        <div class="d-flex align-items-center">
                          
  <div class="avatar-xs flex-shrink-0 me-3">
                               <a href="javascript:void(0);"
                   class="d-inline-block"
                   data-bs-toggle="tooltip"
                   data-bs-placement="top"
                   title="{{ $u->first_name }} {{ $u->last_name }}">    <img
                                    src="{{ ($u->avatar && file_exists(public_path('uploads/users/'.$u->avatar)))
                                        ? asset('uploads/users/'.$u->avatar)
                                        : URL::asset('images/user-img-2.png') }}"
                                    class="img-fluid rounded-circle"
                                    alt="User Avatar"></a>
                            </div>

                            <div class="flex-grow-1">
                                <h5 class="fs-16 mb-0">
                                     <a href="javascript:void(0);"
                   class="d-inline-block"
                   data-bs-toggle="tooltip"
                   data-bs-placement="top"
                   title="{{ $u->first_name }} {{ $u->last_name }}"> 
                                        {{ $u->first_name }} {{ $u->last_name }}
                                    </a>
                                </h5>
                                <h6 class="fs-6 mb-0">
                             {{ $u->designationRel->name ?? '-' }}
                                </h6>
                            </div>
							
                        </div>
                    @empty
                        <p class="text-center text-muted">
                            No users found for this organization.
                        </p>
                    @endforelse

                </div>
            </div>
        </div>
    </div>
    {{-- end card --}}

    {{-- ================= DESIGNATIONS ================= --}}
    <div class="card">
        <div class="card-header align-items-center d-flex border-bottom-dashed">
            <h4 class="card-title mb-0 flex-grow-1">Designations</h4>
        </div>

        <div class="card-body">
            <div data-simplebar style="height: 235px;" class="mx-n3 px-3">
                <div class="vstack gap-3">

                 @foreach($designationData as $d)

    <h6 class="fw-bold">
        {{ $d['name'] }}
        <span class="text-muted">({{ $d['total_users'] }})</span>
    </h6>

    <div class="avatar-group mb-2">

        @foreach($d['users'] as $u)
            <div class="avatar-group-item">
              <a href="javascript:void(0);"
                   class="d-inline-block"
                   data-bs-toggle="tooltip"
                   data-bs-placement="top"
                   title="{{ $u->first_name }} {{ $u->last_name }}">    <img
                    src="{{ ($u->avatar && file_exists(public_path('uploads/users/'.$u->avatar)))
                        ? asset('uploads/users/'.$u->avatar)
                        : asset('images/user-img-2.png') }}"
                    class="rounded-circle avatar-xxs"
                    title="{{ $u->first_name }} {{ $u->last_name }}"></a>
            </div>
        @endforeach

        @if($d['extra_count'] > 0)
            <div class="avatar-group-item">
                <div class="avatar-xxs">
                    <span class="avatar-title rounded-circle bg-success text-white">
                        +{{ $d['extra_count'] }}
                    </span>
                </div>
            </div>
        @endif

    </div>

@endforeach


                </div>
            </div>
        </div>
    </div>
    {{-- end card --}}

</div>
 
                    </div>
                    <!-- end row -->
                </div>
                <!-- end tab pane -->
                <div class="tab-pane fade" id="project-documents" role="tabpanel">
                    <div class="card">
                        <div class="card-body">
                            <div class="d-flex align-items-center mb-4">
                                <h5 class="card-title flex-grow-1">Users by Designation</h5>
                            </div>
                            <div class="row">
                                <div class="col-lg-12">
                                
                                
                                 <!-- 5️⃣ DESIGNATION WISE USER GROUP -->
  <div class="col-md-12 mt-4">
     <div class="row">
      @forelse($designationWiseUsers as $designationId => $groupUsers)
      <div class="col-md-4 mb-3">
        <div class="card border">
          <div class="card-body">
            <h6>
              <i class="ri-shield-star-line me-2"></i>
              {{ $groupUsers->first()->designation->name ?? 'Unknown Designation' }}
            </h6>
            <ul>
              @foreach($groupUsers as $user)
                <li><i class="ri-user-star-line me-1"></i>{{ $user->first_name }}</li>
              @endforeach
            </ul>
            <p class="small text-muted">Total: {{ $groupUsers->count() }} Users</p>
          </div>
        </div>
      </div>
      @empty
        <p class="text-muted text-center">No designation wise users found.</p>
      @endforelse
    </div>
  </div>

                                
                               </div>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- end tab pane -->
                <div class="tab-pane fade" id="project-activities" role="tabpanel">
                    <div class="card">
                        <div class="card-body">
                            <h5 class="card-title">Activities</h5>
                            <div class="acitivity-timeline py-3">
                                <div class="acitivity-item d-flex">
                                    <div class="flex-shrink-0">
                                        <img src="{{ URL::asset('build/images/users/avatar-1.jpg') }}" alt=""
                                            class="avatar-xs rounded-circle acitivity-avatar" />
                                    </div>
                                    <div class="flex-grow-1 ms-3">
                                        <h6 class="mb-1">Oliver Phillips <span
                                                class="badge bg-primary-subtle text-primary align-middle">New</span></h6>
                                        <p class="text-muted mb-2">We talked about a project on linkedin.</p>
                                        <small class="mb-0 text-muted">Today</small>
                                    </div>
                                </div>
                                <div class="acitivity-item py-3 d-flex">
                                    <div class="flex-shrink-0 avatar-xs acitivity-avatar">
                                        <div class="avatar-title bg-success-subtle text-success rounded-circle">
                                            N
                                        </div>
                                    </div>
                                    <div class="flex-grow-1 ms-3">
                                        <h6 class="mb-1">Nancy Martino <span
                                                class="badge bg-secondary-subtle text-secondary align-middle">In
                                                Progress</span></h6>
                                        <p class="text-muted mb-2"><i class="ri-file-text-line align-middle ms-2"></i>
                                            Create new project Building product</p>
                                        <div class="avatar-group mb-2">
                                            <a href="javascript: void(0);" class="avatar-group-item"
                                                data-bs-toggle="tooltip" data-bs-placement="top" title=""
                                                data-bs-original-title="Christi">
                                                <img src="{{ URL::asset('build/images/users/avatar-4.jpg') }}" alt=""
                                                    class="rounded-circle avatar-xs" />
                                            </a>
                                            <a href="javascript: void(0);" class="avatar-group-item"
                                                data-bs-toggle="tooltip" data-bs-placement="top" title=""
                                                data-bs-original-title="Frank Hook">
                                                <img src="{{ URL::asset('build/images/users/avatar-3.jpg') }}" alt=""
                                                    class="rounded-circle avatar-xs" />
                                            </a>
                                            <a href="javascript: void(0);" class="avatar-group-item"
                                                data-bs-toggle="tooltip" data-bs-placement="top" title=""
                                                data-bs-original-title=" Ruby">
                                                <div class="avatar-xs">
                                                    <div class="avatar-title rounded-circle bg-light text-primary">
                                                        R
                                                    </div>
                                                </div>
                                            </a>
                                            <a href="javascript: void(0);" class="avatar-group-item"
                                                data-bs-toggle="tooltip" data-bs-placement="top" title=""
                                                data-bs-original-title="more">
                                                <div class="avatar-xs">
                                                    <div class="avatar-title rounded-circle">
                                                        2+
                                                    </div>
                                                </div>
                                            </a>
                                        </div>
                                        <small class="mb-0 text-muted">Yesterday</small>
                                    </div>
                                </div>
                                <div class="acitivity-item py-3 d-flex">
                                    <div class="flex-shrink-0">
                                        <img src="{{ URL::asset('build/images/users/avatar-2.jpg') }}" alt=""
                                            class="avatar-xs rounded-circle acitivity-avatar" />
                                    </div>
                                    <div class="flex-grow-1 ms-3">
                                        <h6 class="mb-1">Natasha Carey <span
                                                class="badge bg-success-subtle text-success align-middle">Completed</span>
                                        </h6>
                                        <p class="text-muted mb-2">Adding a new event with attachments</p>
                                        <div class="row">
                                            <div class="col-xxl-4">
                                                <div class="row border border-dashed gx-2 p-2 mb-2">
                                                    <div class="col-4">
                                                        <img src="{{ URL::asset('build/images/small/img-2.jpg') }}" alt=""
                                                            class="img-fluid rounded" />
                                                    </div>
                                                    <!--end col-->
                                                    <div class="col-4">
                                                        <img src="{{ URL::asset('build/images/small/img-3.jpg') }}" alt=""
                                                            class="img-fluid rounded" />
                                                    </div>
                                                    <!--end col-->
                                                    <div class="col-4">
                                                        <img src="{{ URL::asset('build/images/small/img-4.jpg') }}" alt=""
                                                            class="img-fluid rounded" />
                                                    </div>
                                                    <!--end col-->
                                                </div>
                                                <!--end row-->
                                            </div>
                                        </div>
                                        <small class="mb-0 text-muted">25 Nov</small>
                                    </div>
                                </div>
                                <div class="acitivity-item py-3 d-flex">
                                    <div class="flex-shrink-0">
                                        <img src="{{ URL::asset('build/images/users/avatar-6.jpg') }}" alt=""
                                            class="avatar-xs rounded-circle acitivity-avatar" />
                                    </div>
                                    <div class="flex-grow-1 ms-3">
                                        <h6 class="mb-1">Bethany Johnson</h6>
                                        <p class="text-muted mb-2">added a new member to velzon dashboard</p>
                                        <small class="mb-0 text-muted">19 Nov</small>
                                    </div>
                                </div>
                                <div class="acitivity-item py-3 d-flex">
                                    <div class="flex-shrink-0">
                                        <div class="avatar-xs acitivity-avatar">
                                            <div class="avatar-title rounded-circle bg-danger-subtle text-danger">
                                                <i class="ri-shopping-bag-line"></i>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="flex-grow-1 ms-3">
                                        <h6 class="mb-1">Your order is placed <span
                                                class="badge bg-danger-subtle text-danger align-middle ms-1">Out of
                                                Delivery</span></h6>
                                        <p class="text-muted mb-2">These customers can rest assured their order has been
                                            placed.</p>
                                        <small class="mb-0 text-muted">16 Nov</small>
                                    </div>
                                </div>
                                <div class="acitivity-item py-3 d-flex">
                                    <div class="flex-shrink-0">
                                        <img src="{{ URL::asset('build/images/users/avatar-7.jpg') }}" alt=""
                                            class="avatar-xs rounded-circle acitivity-avatar" />
                                    </div>
                                    <div class="flex-grow-1 ms-3">
                                        <h6 class="mb-1">Lewis Pratt</h6>
                                        <p class="text-muted mb-2">They all have something to say beyond the words on the
                                            page. They can come across as casual or neutral, exotic or graphic. </p>
                                        <small class="mb-0 text-muted">22 Oct</small>
                                    </div>
                                </div>
                                <div class="acitivity-item py-3 d-flex">
                                    <div class="flex-shrink-0">
                                        <div class="avatar-xs acitivity-avatar">
                                            <div class="avatar-title rounded-circle bg-info-subtle text-info">
                                                <i class="ri-line-chart-line"></i>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="flex-grow-1 ms-3">
                                        <h6 class="mb-1">Monthly sales report</h6>
                                        <p class="text-muted mb-2"><span class="text-danger">2 days left</span>
                                            notification to submit the monthly sales report. <a href="javascript:void(0);"
                                                class="link-warning text-decoration-underline">Reports Builder</a></p>
                                        <small class="mb-0 text-muted">15 Oct</small>
                                    </div>
                                </div>
                                <div class="acitivity-item d-flex">
                                    <div class="flex-shrink-0">
                                        <img src="{{ URL::asset('build/images/users/avatar-8.jpg') }}" alt=""
                                            class="avatar-xs rounded-circle acitivity-avatar" />
                                    </div>
                                    <div class="flex-grow-1 ms-3">
                                        <h6 class="mb-1">New ticket received <span
                                                class="badge bg-success-subtle text-success align-middle">Completed</span>
                                        </h6>
                                        <p class="text-muted mb-2">User <span class="text-secondary">Erica245</span>
                                            submitted a ticket.</p>
                                        <small class="mb-0 text-muted">26 Aug</small>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <!--end card-body-->
                    </div>
                    <!--end card-->
                </div>
                <!-- end tab pane -->
                <div class="tab-pane fade" id="project-team" role="tabpanel">
             
<div class="team-list list-view-filter">

    @forelse($users as $u)
        <div class="card team-box">
            <div class="card-body px-4">
                <div class="row align-items-center team-row">

                    <div class="col team-settings">
                        <div class="row align-items-center">
                            <div class="col text-end dropdown">
                                <a href="javascript:void(0);" data-bs-toggle="dropdown" aria-expanded="false">
                                    <i class="ri-more-fill fs-17"></i>
                                </a>
                                <ul class="dropdown-menu dropdown-menu-end">
                                    <li>
                                        <a class="dropdown-item" href="../user/{{ $u->id }}">
                                            <i class="ri-eye-fill text-muted me-2 align-bottom"></i>View
                                        </a>
                                    </li>
                                    <li>
                                        <a class="dropdown-item" href="javascript:void(0);">
                                            <i class="ri-delete-bin-5-fill text-muted me-2 align-bottom"></i>Delete
                                        </a>
                                    </li>
                                </ul>
                            </div>
                        </div>
                    </div>

                    {{-- USER PROFILE --}}
                    <div class="col-lg-4 col">
                        <div class="team-profile-img">
                            <div class="avatar-lg img-thumbnail rounded-circle">
                                <img
                                    src="{{ ($u->avatar && file_exists(public_path('uploads/users/'.$u->avatar)))
                                        ? asset('uploads/users/'.$u->avatar)
                                        : URL::asset('images/user-img-2.png') }}"
                                    class="img-fluid d-block rounded-circle"
                                    alt="User Avatar" />
                            </div>
                            <div class="team-content">
                                <a href="../user/{{ $u->id }}" class="d-block">
                                    <h5 class="fs-16 mb-1">
                                        {{ $u->first_name }} {{ $u->last_name }}
                                    </h5>
                                </a>
                                <p class="text-muted mb-0">
                                    {{ $u->designation->name ?? 'Not assigned' }}
                                </p>
                            </div>
                        </div>
                    </div>

                    {{-- STATS (UNCHANGED) --}}
                    <div class="col-lg-4 col">
                        <div class="row text-muted text-center">
                            <div class="col-6 border-end border-end-dashed">
                                <h5 class="mb-1">225</h5>
                                <p class="text-muted mb-0">Projects</p>
                            </div>
                            <div class="col-6">
                                <h5 class="mb-1">197</h5>
                                <p class="text-muted mb-0">Tasks</p>
                            </div>
                        </div>
                    </div>

                    {{-- ACTION --}}
                    <div class="col-lg-2 col">
                        <div class="text-end">
                            <a href="../user/{{ $u->id }}" class="btn btn-light view-btn">
                                View Profile
                            </a>
                        </div>
                    </div>

                </div>
            </div>
        </div>
    @empty
        <p class="text-center text-muted">
            No users found for this organization.
        </p>
    @endforelse

</div>
    <!-- end team list -->

                  
                </div>
                <!-- end tab pane -->
            </div>
        </div>
        <!-- end col -->
    </div>
    <!-- end row -->
 
@endsection
@section('script')
    <script src="{{ URL::asset('build/js/pages/project-overview.init.js') }}"></script>
    
@endsection





 