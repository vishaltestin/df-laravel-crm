@extends('FrontEnd.layouts.master')
@section('title', 'Users')
@section('css')
<link href="{{ URL::asset('build/libs/sweetalert2/sweetalert2.min.css') }}" rel="stylesheet">

@endsection
@section('css')


@endsection
@section('content')
    @component('FrontEnd.components.breadcrumb')
        @slot('li_1')
            CRM
        @endslot
        @slot('title')
            {{ $user->first_name }} {{ $user->last_name }}
        @endslot
    @endcomponent
<div class="profile-cover bg-primary">
    <div class="container-fluid">
        <div class="profile-header">
            <div class="row align-items-center">
                <div class="col-auto">
                    <div class="avatar-lg">
					
					
					
					
					
					
                        <img  src="{{ ($user->avatar && file_exists(public_path('uploads/users/'.$user->avatar)))
                                        ? asset('uploads/users/'.$user->avatar)
                                        : URL::asset('images/user-img-2.png') }}" class="rounded-circle img-thumbnail" alt="profile">
                    </div>
                </div>
          <pre>
 
</pre>      <div class="col">
                    <h3 class="text-white mb-1">{{ $user->first_name }} {{ $user->last_name }}</h3>
                    <p class="text-white-50 mb-0">{{ $user->email }}</p>
                    <p class="text-white-50 mb-0">Mobile: {{ $user->mobile }}</p>
                </div>

               <div class="col-auto text-end">

<span class="badge {{ $user->status ? 'bg-success' : 'bg-danger' }}">
    {{ $user->status ? 'Active' : 'Inactive' }}
</span>

</div>

            </div>
        </div>
    </div>
</div>


<div class="container mt-4">

    <!-- PROFILE TABS -->
    <ul class="nav nav-tabs nav-tabs-custom" role="tablist">

        <li class="nav-item">
            <a class="nav-link active" data-bs-toggle="tab" href="#overview" role="tab">
                Overview
            </a>
        </li>

        <li class="nav-item">
            <a class="nav-link" data-bs-toggle="tab" href="#activities" role="tab">
                Activities (User Logs)
            </a>
        </li>

        <li class="nav-item">
            <a class="nav-link" data-bs-toggle="tab" href="#organization" role="tab">
                Organization Logs
            </a>
        </li>

        <li class="nav-item">
            <a class="nav-link" data-bs-toggle="tab" href="#lead" role="tab">
                Lead Logs
            </a>
        </li>

    </ul>


    <div class="tab-content">

        <!-- ======================= OVERVIEW TAB ======================== -->
        <div class="tab-pane fade show active" id="overview">

            <div class="card mt-3">
                <div class="card-header"><strong>User Information</strong></div>
                <div class="card-body">
                    <div class="row">

                        <div class="col-md-6">
                            <p><strong>Name:</strong> {{ $user->first_name }} {{ $user->last_name }}</p>
                            <p><strong>Email:</strong> {{ $user->email }}</p>
                            <p><strong>Mobile:</strong> {{ $user->mobile }}</p>
                        </div>

                        <div class="col-md-6">
                            <p><strong>Status:</strong> {{ $user->status ? 'Active' : 'Inactive' }}</p>
                         </div>

                    </div>
                </div>
            </div>

        </div>


        <!-- ======================= USER ACTIVITY LOGS ======================== -->
        <div class="tab-pane fade" id="activities">

            <div class="card mt-3">
                <div class="card-header"><strong>User Activity Timeline</strong></div>
                <div class="card-body">

                    @forelse ($userLogs as $log)
                        <div class="d-flex mb-4">
                            <div class="flex-shrink-0">
                                <div class="avatar-xs">
                                    <span class="avatar-title rounded-circle bg-primary text-white">U</span>
                                </div>
                            </div>

                            <div class="flex-grow-1 ms-3">
                                <h6 class="mb-1">{{ $log->description }}</h6>
                                <p class="text-muted mb-0">{{ date('d M Y h:i A', strtotime($log->created_at)) }}</p>
                                <hr>
                            </div>
                        </div>
                    @empty
                        <p class="text-muted">No user logs found.</p>
                    @endforelse

                </div>
            </div>

        </div>


        <!-- ======================= ORGANIZATION LOGS ======================== -->
        <div class="tab-pane fade" id="organization">

            <div class="card mt-3">
                <div class="card-header"><strong>Organization Activity</strong></div>
                <div class="card-body">

                    @forelse ($orgLogs as $log)
                        <div class="d-flex mb-4">
                            <div class="flex-shrink-0">
                                <div class="avatar-xs">
                                    <span class="avatar-title rounded-circle bg-info text-white">O</span>
                                </div>
                            </div>

                            <div class="flex-grow-1 ms-3">
                                <h6 class="mb-1">{{ $log->description }}</h6>
                                <p class="text-muted mb-0">{{ date('d M Y h:i A', strtotime($log->created_at)) }}</p>
                                <hr>
                            </div>
                        </div>
                    @empty
                        <p class="text-muted">No organization logs found.</p>
                    @endforelse

                </div>
            </div>

        </div>


        <!-- ======================= LEAD LOGS ======================== -->
        <div class="tab-pane fade" id="lead">

            <div class="card mt-3">
                <div class="card-header"><strong>Lead Activity</strong></div>
                <div class="card-body">

                    @forelse ($leadLogs as $log)
                        <div class="d-flex mb-4">

                            <div class="flex-shrink-0">
                                <div class="avatar-xs">
                                    <span class="avatar-title rounded-circle bg-warning text-white">L</span>
                                </div>
                            </div>

                            <div class="flex-grow-1 ms-3">
                                <h6 class="mb-1">{{ $log->description }}</h6>
                                <p class="text-muted mb-0">{{ date('d M Y h:i A', strtotime($log->created_at)) }}</p>
                                <hr>
                            </div>

                        </div>
                    @empty
                        <p class="text-muted">No lead logs found.</p>
                    @endforelse

                </div>
            </div>

        </div>

    </div>

</div>

@endsection
