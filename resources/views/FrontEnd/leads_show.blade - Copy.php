@extends('FrontEnd.layouts.master')
@section('title', $lead->name ?? 'Lead Details')

@section('css')
<link href="{{ URL::asset('build/libs/sweetalert2/sweetalert2.min.css') }}" rel="stylesheet">

<style>
/* small helpers */
.text-truncate-10 { display:inline-block; max-width:10ch; overflow:hidden; white-space:nowrap; text-overflow:ellipsis; cursor:pointer; }
.lead-border-left { border-left:4px solid transparent; background-image:linear-gradient(to bottom,#ff4d4d,#b30000); background-repeat:no-repeat; background-size:4px 100%; background-position:left center; padding-left:8px; }

/* pipeline */
.pipeline-container { margin-bottom:16px; }
.pipeline-bar { display:flex; gap:10px; align-items:center; overflow-x:auto; padding:10px; background:#fff; border-radius:8px; border:1px solid #f1f3f5; }
.pipeline-step { min-width:140px; padding:10px; border-radius:10px; background:#f7f8fa; display:flex; gap:10px; align-items:center; transition:all .15s ease; }
.pipeline-step .step-icon { width:36px; height:36px; border-radius:8px; display:flex; align-items:center; justify-content:center; background:rgba(0,0,0,0.04); font-size:16px; }
.pipeline-step.completed { background:#e9faf0; border:1px solid #c8f0dc; }
.pipeline-step.current { background:linear-gradient(90deg,#1abc9c,#16a085); color:#fff; box-shadow:0 6px 18px rgba(22,160,133,0.12); }
.pipeline-step.upcoming { opacity:0.9; }

.lead-info-edit {
    cursor: pointer;
    transition: 0.2s;
}
.lead-info-edit:hover {
    color:#0d6efd !important;
    transform: scale(1.1);
}

/* timeline */
.profile-timeline .timeline-item { position:relative; padding-left:70px; margin-bottom:24px; }
.profile-timeline .timeline-item:before { content:""; position:absolute; left:36px; top:0; bottom:0; width:2px; background:#f1f3f5; }
.profile-timeline .timeline-dot { position:absolute; left:26px; top:6px; width:28px; height:28px; border-radius:50%; display:flex; align-items:center; justify-content:center; background:#fff; border:2px solid #f1f3f5; }

/* offcanvas scroll area */
.offcanvas-body .form-control, .offcanvas-body textarea { width:100%; }

/* notes box small */
.notes-list { max-height:180px; overflow-y:auto; }

/* responsive tweaks */
@media (max-width:991px){ .pipeline-step { min-width:120px; } .col-xl-4, .col-xl-8 { flex:0 0 100%; max-width:100%; } }
</style>
@endsection

@section('content')
@component('FrontEnd.components.breadcrumb')
    @slot('li_1') CRM @endslot
    @slot('title') Lead Details @endslot
@endcomponent

{{-- CSRF meta for AJAX (if not in main layout) --}}
<meta name="csrf-token" content="{{ csrf_token() }}">

<div class="container-fluid p-0">

    {{-- ---------- PIPELINE BAR ---------- --}}
    <div class="pipeline-container">
        <div class="pipeline-bar">
            @php
                $currentStageId = $lead->stage_id ?? null;
                $leadStageOrder = $lead->stage_order ?? null;
                $iconList = ['ri-user-line','ri-phone-line','ri-chat-1-line','ri-bill-line','ri-contacts-book-line','ri-checkbox-circle-line'];
                $defaultIcon = 'ri-shape-line';
            @endphp

            @foreach($stages as $stage)
                @php
                    $class = 'upcoming';
                    if(isset($stage->stage_order) && isset($leadStageOrder)) {
                        $class = $stage->stage_order < $leadStageOrder ? 'completed' : ($stage->stage_order == $leadStageOrder ? 'current' : 'upcoming');
                    } else {
                        if($currentStageId && $stage->id == $currentStageId) $class = 'current';
                        elseif($currentStageId && $stage->id < $currentStageId) $class = 'completed';
                    }
                    $idx = $loop->index;
                    $icon = $idx < count($iconList) ? $iconList[$idx] : $defaultIcon;
                @endphp

                <div class="pipeline-step {{ $class }}" title="{{ $stage->name }}">
                    <div class="step-icon">
                        <i class="{{ $icon }} {{ $class == 'current' ? 'text-white' : 'text-muted' }}"></i>
                    </div>
                    <div>
                        <div class="fw-semibold" style="font-size:13px;">{{ $stage->name }}</div>
                        @if(!empty($stage->stage_description))
                            <div class="small text-muted">{{ \Illuminate\Support\Str::limit($stage->stage_description,40) }}</div>
                        @endif
                    </div>
                </div>
            @endforeach
        </div>
    </div>

    {{-- ---------- MAIN ROW: LEFT + RIGHT ---------- --}}
    <div class="row g-3">
        <!-- LEFT -->
        <div class="col-xl-4">
            <!-- LEAD SUMMARY CARD -->
            <div class="card">
                <div class="card-body text-center">
                    <div class="mb-2">
                     
                        <div class="avatar-lg rounded-circle bg-light d-inline-flex align-items-center justify-content-center" style="width:88px;height:88px;">
                            <span class="fs-20 fw-semibold text-muted">{{ strtoupper(substr($lead->name ?? 'L',0,1)) }}</span>
                        </div>
                        
                    </div>

                    <h5 class="mb-0" data-lead-name>{{ $lead->name ?? '-' }}</h5>
                    <p class="text-muted mb-1"><span data-lead-stage>{{ $lead->stage_name ?? $lead->stage->name ?? '-' }}</span></p>
<p class="text-muted mb-1">
    <b>Status:</b>  
    <span data-lead-status>{{ $lead->leadStatus->name ?? $lead->lead_status_name ?? '-' }}</span>

    <a href="javascript:;" 
       class="text-primary ms-2" 
       style="font-size:14px;" 
       data-bs-toggle="offcanvas" 
       data-bs-target="#changeStageCanvas"
       onclick="resetFollowup('status')">
       <i class="ri-pencil-line"></i>
    </a>
</p>

<p class="text-muted mb-1">
    <b>Sub Status:</b> 
    <span data-lead-substatus>{{ $lead->leadSubStatus->name ?? '-' }}</span>

    <a href="javascript:;" 
       class="text-primary ms-2" 
       style="font-size:14px;" 
       data-bs-toggle="offcanvas" 
       data-bs-target="#changeStageCanvas"
       onclick="resetFollowup('substatus')">
       <i class="ri-pencil-line"></i>
    </a>
</p>

 
                     <div class="d-flex gap-2 justify-content-center my-3">
                        @if(!empty($lead->email))
                        <a href="mailto:{{ $lead->email }}" class="btn btn-sm btn-light" data-bs-toggle="tooltip" title="{{ $lead->email }}"><i class="ri-mail-line"></i></a>
                        @endif
                        @if(!empty($lead->mobile))
                        <a href="tel:{{ $lead->mobile }}" class="btn btn-sm btn-light" data-bs-toggle="tooltip" title="{{ $lead->mobile }}"><i class="ri-phone-line"></i></a>
                        @endif
                        <button class="btn btn-sm btn-light" data-bs-toggle="tooltip" title="Add Note" data-bs-target="#addNoteModal" data-bs-toggle="modal"><i class="ri-chat-1-line"></i></button>
                        <button class="btn btn-sm btn-light" data-bs-toggle="tooltip" title="Timeline"><i class="ri-time-line"></i></button>
                    </div>

                    <div class="row gy-2">
                        <div class="col-6">
                            <div class="p-2 bg-light rounded text-center">
                                <div class="fs-6">Lead Score</div>
                                <div class="h5 mb-0" data-lead-score>{{ $lead->lead_score ?? 0 }}</div>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="p-2 bg-light rounded text-center">
                                <div class="fs-6">Weight</div>
                                <div class="h5 mb-0" data-lead-weight>{{ $lead->lead_weight_count ?? 0 }}</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

         	@php
 $state = \App\State::find($lead->state ?? null);
$city  = \App\City::find($lead->city ?? null);
$country = \App\Country::find($lead->country ?? null);
 
@endphp 	

   <!-- DETAILS -->
            <div class="card">
                <div class="card-header"><h5 class="card-title mb-0"><i class="ri-information-line me-1"></i> Lead Details</h5></div>
                <div class="card-body">
                    <ul class="list-unstyled mb-0">
                        <li><strong>Name:</strong> <span data-lead-name-short>{{ $lead->name ?? '-' }}</span></li>
                        <li><strong>Mobile:</strong> <span data-lead-mobile>{{ $lead->mobile ?? '-' }}</span></li>
                        <li><strong>Email:</strong> <span data-lead-email>{{ $lead->email ?? '-' }}</span></li>
                        <li><strong>City:</strong> {{ $city->name ?? '-' }}</li>
                        <li><strong>State:</strong> {{ $state->name ?? '-' }}</li>
                        <li><strong>Country:</strong> {{ $country->name ?? '-' }}</li>
                        <li><strong>Institute:</strong> {{ $lead->institute->name ?? 'N/A' }}</li>
                        <li><strong>Programme:</strong> {{ $lead->programme->name ?? 'N/A' }}</li>
                        <li><strong>Course:</strong> {{ $lead->faculty->name ?? 'N/A' }}</li>
                        <li><strong>Created:</strong> {{ optional($lead->created_at)->format('d M, Y H:i') ?? '-' }}</li>
                    </ul>
                    <hr>
                    <p class="mb-1"><b>Pipeline:</b> <span data-lead-pipeline>{{ $lead->pipeline->name ?? $lead->pipeline_name ?? '-' }}</span>   <a href="javascript:;"
       class="text-primary ms-2"
       data-bs-toggle="offcanvas"
       data-bs-target="#editPipelineCanvas">
       <i class="ri-pencil-line"></i>
    </a></p>
	
	
	<p>
  <b>Source:</b>
  <span data-lead-source>{{ $lead->source->name ?? '-' }}</span>

  <a href="javascript:;" data-bs-toggle="offcanvas" data-bs-target="#editSourceCanvas">
    <i class="ri-pencil-line"></i>
  </a>
</p><div class="offcanvas offcanvas-end" id="editSourceCanvas">
  <div class="offcanvas-body">
    <form id="sourceUpdateForm"
          action="{{ url('leads/'.$lead->id.'/update-source') }}"
          method="post">
      @csrf

      <select name="source_id" class="form-select">
        @foreach($sources as $s)
          <option value="{{ $s->id }}"
            {{ $lead->source_id == $s->id ? 'selected' : '' }}>
            {{ $s->name }}
          </option>
        @endforeach
      </select>

      <button class="btn btn-primary mt-3">Save</button>
    </form>
  </div>
</div>

                 </div>
            </div>
<div class="offcanvas offcanvas-end" id="editPipelineCanvas">
  <div class="offcanvas-header">
    <h5>Edit Pipeline</h5>
    <button class="btn-close" data-bs-dismiss="offcanvas"></button>
  </div>

  <div class="offcanvas-body">
    <form id="pipelineUpdateForm"
          method="post"
          action="{{ url('leads/'.$lead->id.'/update-pipeline') }}">
      @csrf

      <div class="mb-3">
        <label>Pipeline</label>
        <select name="pipeline_id" class="form-select" required>
          @foreach($pipelines as $p)
            <option value="{{ $p->id }}"
              {{ $lead->pipeline_id == $p->id ? 'selected' : '' }}>
              {{ $p->name }}
            </option>
          @endforeach
        </select>
      </div>

      <div class="text-end">
        <button class="btn btn-light" data-bs-dismiss="offcanvas">Cancel</button>
        <button class="btn btn-primary">Save</button>
      </div>
    </form>
  </div>
</div>

   <!-- ORGANIZATION -->
            <div class="card">
                <div class="card-header"><h5 class="card-title mb-0"><i class="ri-building-4-line me-1"></i> Organization</h5></div>
                <div class="card-body  ">
                    <ul class="list-unstyled mb-0">
						<li><strong>Name:</strong> <span data-lead-name-short>{{ $lead->organization->name ?? 'N/A' }}</span></li>
						 <li><strong>Mobile:</strong> <span data-lead-mobile>{{ $lead->organization->mobile ?? '-' }}</span></li>
						 <li><strong>Email:</strong> <span data-lead-email> {{ $lead->organization->email ?? 'N/A' }}</span></li>
					</ul>
 					</div>
            </div>
			
			
			
  <div class="card">
                <div class="card-header"><h5 class="card-title mb-0"><i class="ri-building-4-line me-1"></i> Application</h5></div>
                <div class="card-body  ">
  @php
    $resp = $lead->applicationResponse;
 
    $app = $lead->application;
@endphp

  <p><b>Current Step:</b> {{ $resp->current_step ?? '-' }}</p>

        <div class="progress mb-2" style="height:8px;">
            <div class="progress-bar bg-success"
                 style="width: {{ $resp->fill_percentage ?? 0 }}%">
            </div>
        </div>

        <small>{{ $resp->fill_percentage ?? 0 }}% Form Completed</small>
@if($lead->application)
 
        <p><b>Form:</b> {{ $lead->application->form->title }}</p>
         <p><b>Fees:</b> ₹{{ number_format($lead->application->form->fees,2) }}</p>
    <p><b>Total Amount:</b> ₹{{ number_format($lead->application->form->total_amount,2) }}</p>
 <p><b>Status:</b>

		@if($app)
    <span class="badge 
        {{ $app->payment_status == 'paid' ? 'bg-success' : 'bg-warning' }}">
        {{ ucfirst($app->payment_status) }}
    </span>
@else
    <span class="badge bg-secondary">Not Applied</span>
@endif
             
        </p>
  
<p><b>Programme:</b> {{ $lead->programme->name ?? '-' }}</p>
<p><b>Course / PG:</b> {{ $lead->faculty->name ?? '-' }}</p>      <p><b>Score:</b> {{ $lead->application->application_score }}%</p>

        {{-- progress --}}
        <div class="progress">
            <div class="progress-bar" style="width: {{ $resp->fill_percentage ?? 0 }}"></div>
        </div>
   
@endif

 					</div>
            </div>
			
			
			
			
			
			
			
			
			
            <!-- ASSIGNED USER -->
            <div class="card">
                <div class="card-header"><h5 class="card-title mb-0"><i class="ri-user-line me-1"></i> Assigned User</h5> </div>
                <div class="card-body text-center">
                     @if(!empty($lead->avatar))
                        <img src="{{ $lead->avatar }}" class="rounded-circle" width="88" height="88" alt="lead">
                        @else
                       <div class="avatar-sm rounded-circle bg-light d-inline-flex align-items-center justify-content-center mb-2">
                        <span class="fs-16">{{ strtoupper(substr($lead->assignedUser->first_name ?? 'U',0,1)) }}</span>
                    </div>
                        @endif
						
						
						
                    <h6 class="mb-0" data-lead-owner>{{ $lead->assignedUser->first_name ?? 'Unassigned' }} {{ $lead->assignedUser->last_name ?? 'Unassigned' }}</h6>
                    <p class="text-muted mb-0">Email <span data-lead-owner-id>{{ $lead->assignedUser->email ?? '-' }} </span></p>
                    <p class="text-muted mb-0">Mobile <span data-lead-owner-id>{{ $lead->assignedUser->mobile ?? '-' }} </span></p>
                                <li><a href="javascript:;" class="btn btn-sm btn-outline-warning" data-bs-toggle="modal" data-bs-target="#reassignModal"><i class="ri-user-shared-line me-1"></i> Re-assign</a></li>
    </div>
            </div>

        </div>

        <!-- RIGHT -->
        <div class="col-xl-8">
            <!-- ACTION BUTTONS (top) -->
            <div class="mb-2 d-flex gap-2 align-items-center">
                <ul class="list-unstyled d-flex gap-2 mb-0">
                    <li><a href="javascript:;" class="btn btn-sm btn-outline-primary" data-bs-toggle="modal" data-bs-target="#addNoteModal" onclick="resetFollowup('note')"><i class="ri-note-add-line me-1"></i> Add Note</a></li>
                    <li><a href="javascript:;" class="btn btn-sm btn-outline-secondary" data-bs-toggle="offcanvas" data-bs-target="#changeStageCanvas" onclick="resetFollowup('stage')"><i class="ri-shuffle-line me-1"></i> Change Stage</a></li>
                    <li><a href="javascript:;" class="btn btn-sm btn-outline-success" data-bs-toggle="offcanvas" data-bs-target="#communicationCanvas"><i class="ri-mail-send-line me-1"></i> Communicate</a></li>
                    <li><a href="javascript:;" class="btn btn-sm btn-outline-info" data-bs-toggle="offcanvas" data-bs-target="#addEventCanvas"><i class="ri-calendar-event-line me-1"></i> Add Event</a></li>
                    <li><a href="javascript:;" class="btn btn-sm btn-outline-dark" data-bs-toggle="offcanvas" data-bs-target="#whatsappChatCanvas"><i class="ri-whatsapp-line me-1"></i> WhatsApp History</a></li>
                </ul>
            </div>

            <!-- TABS -->
            <div class="card">
                <div class="card-body">
    <div class="tabs-scroll-wrapper">
<ul class="nav nav-tabs mb-3 flex-nowrap" >
    
    <li class="nav-item">
        <a class="nav-link active" data-bs-toggle="tab" href="#tab-details">
            <i class="ri-information-line me-1"></i> Lead Details
        </a>
    </li>

    <li class="nav-item">
        <a class="nav-link" data-bs-toggle="tab" href="#tab-timeline">
            <i class="ri-time-line me-1"></i> Timeline
        </a>
    </li>

    <li class="nav-item">
        <a class="nav-link" data-bs-toggle="tab" href="#tab-notes">
            <i class="ri-sticky-note-line me-1"></i> Notes
        </a>
    </li>

    <li class="nav-item">
        <a class="nav-link" data-bs-toggle="tab" href="#tab-comm">
            <i class="ri-mail-send-line me-1"></i> Communication
        </a>
    </li>

   
    <li class="nav-item">
        <a class="nav-link" data-bs-toggle="tab" href="#tab-calls">
            <i class="ri-phone-line me-1"></i> Call Logs
        </a>
    </li>
   <div class="dropdown ms-2">
        <button class="btn btn-sm btn-light" data-bs-toggle="dropdown">
            <i class="ri-more-2-fill"></i>
        </button>
        <ul class="dropdown-menu dropdown-menu-end">
           <li> <a class="dropdown-item" data-bs-toggle="tab" href="#tab-doc"> <i class="ri-file-paper-2-line me-1"></i> DocVerify</a>  </li>

    <li> <a class="dropdown-item" data-bs-toggle="tab" href="#tab-tickets">  <i class="ri-customer-service-2-line me-1"></i> Tickets </a> </li>


    <li  >
        <a class="dropdown-item" data-bs-toggle="tab" href="#tab-calendar">
            <i class="ri-calendar-event-line me-1"></i> Calendar Pro
        </a>
    </li>

  
        </ul>
    </div>

</ul>


</div>

 
                    <div class="tab-content">
                        {{-- DETAILS --}}
                        <div class="tab-pane active" id="tab-details">
                            <table class="table table-borderless">
                                <tbody>
			
									@php
$extra = json_decode($lead->extra_fields_json, true);
$state = \App\State::find($extra['state_id'] ?? null);
$city  = \App\City::find($extra['city_id'] ?? null);
$qualification = \App\Institute::find($extra['qualification'] ?? null);
$faculty       = \App\Program::find($extra['faculty'] ?? null);

$uploadVia = ucfirst($extra['upload_via'] ?? '-');
@endphp 					
   
                                     <tr><th style="width:220px">Form Interested In</th><td>: {{ $lead->programme->name ?? $lead->institute_applying_for ?? 'N/A' }}</td></tr>
                   <tr><th>Email</th><td>: <span data-lead-email-table>{{ $lead->email ?? '-' }}</span></td></tr>
                                    <tr><th>Mobile</th><td>: <span data-lead-mobile-table>{{ $lead->mobile ?? '-' }}</span></td></tr>
                                    <tr><th>City</th><td>: {{ $city->name ?? '-' }}</td></tr>
                                    <tr><th>State</th><td>: {{ $state->name ?? '-' }}</td></tr>

                                    <tr>
                                        <th>Status</th>
                                        <td>: <span data-lead-status-table>{{ $lead->leadStatus->name ?? 'N/A' }}</span></td>
                                    </tr>

                                    <tr>
                                        <th>Sub Status</th>
                                        <td>: <span data-lead-substatus-table>{{ $lead->leadSubStatus->name ?? 'N/A' }}</span></td>
                                    </tr>
									
									
  <tr><td>Qualification </td><td>             {{ $lead->qualification_name ?? '-' }}</td></tr>
 <tr><td>Faculty </td><td>{{ $lead->faculty_name ?? '-' }}</td></tr>
 <tr><td>Lead From</td><td><span class="badge bg-info">{{ $uploadVia }}</span></td></tr>



                                </tbody>
                            </table>
                        </div>

                        {{-- TIMELINE --}}
                        <div class="tab-pane" id="tab-timeline">
                            <div class="profile-timeline mt-3">
                                @php $logs = $timeline ?? collect(); @endphp

                                @if($logs && $logs->count())
                                    @foreach($logs as $log)
                                    <div class="timeline-item">
                                        <div class="timeline-dot"><i class="{{ $log->icon ?? 'ri-history-line' }}"></i></div>
                                        <div class="content">
                                            <div class="d-flex justify-content-between">
                                                <div>
                                                    <strong>{{ $log->title ?? ($log->action ?? 'Activity') }}</strong>
                                                    <div class="small text-muted">{{ $log->details ?? '' }}</div>
                                                </div>
                                                <div class="small text-muted">{{ optional($log->created_at)->format('d M, Y H:i') }}</div>
                                            </div>
                                        </div>
                                    </div>
                                    @endforeach
                                @else
                                    <div class="text-center p-4 text-muted">No activity found.</div>
                                @endif
                            </div>
                        </div>

                        {{-- CALENDAR --}}
                        <div class="tab-pane" id="tab-calendar">
                            <div class="p-3 text-muted"> 
		<style>
#leadCalendar {
    background: #fff;
    border-radius: 12px;
    padding: 20px;
    box-shadow: 0px 4px 12px rgba(0,0,0,0.05);
}

.fc .fc-toolbar-title {
    font-size: 22px !important;
    font-weight: 600 !important;
    color: #0d6efd;
}

.fc .fc-button {
    background-color: #0d6efd !important;
    border: none !important;
    border-radius: 6px !important;
    padding: 5px 12px !important;
}

.fc .fc-daygrid-day-number {
    font-weight: 600;
}

.offcanvas {
    border-radius: 14px 0 0 14px;
}

.offcanvas-header {
    background: #f7faff;
}

.form-control-lg {
    border-radius: 10px;
}
</style>
<link href="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.10/index.global.min.css" rel="stylesheet">
<script src="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.10/index.global.min.js"></script>

				
							
			<div class="card shadow-sm border-0 rounded-3">
    <div class="card-header  py-3 d-flex justify-content-between align-items-center">
       
        <button class="btn btn-light btn-sm fw-bold shadow-sm"
            data-bs-toggle="offcanvas"
            data-bs-target="#addEventCanvas">
            <i class="bi bi-plus-lg"></i> Add Event
        </button>
    </div>

    <div class="card-body p-3">
        <div id="leadCalendar"></div>
    </div>
</div>


 
							
							
							</div>
                        </div>

                        {{-- NOTES --}}
                        <div class="tab-pane" id="tab-notes">
                            <div class="p-3">
                                <h6>Add Note</h6>
                                <p><button class="btn btn-sm btn-primary" data-bs-toggle="modal" data-bs-target="#addNoteModal" onclick="resetFollowup('note')">Add Note</button></p>
                                <div class="notes-list border rounded p-2">
                                    @php $notes = $lead->notes ?? collect(); @endphp
                                    @if($notes->count())
                                        @foreach($notes as $n)
                                        <div class="mb-2">
                                            <div class="small text-muted">{{ optional($n->created_at)->format('d M, Y H:i') }} — {{ $n->created_by ?? 'System' }}</div>
                                            <div>{{ $n->note ?? $n->message ?? '-' }}</div>
                                        </div>
                                        @endforeach
                                    @else
                                        <div class="text-muted text-center p-3">No notes yet.</div>
                                    @endif
                                </div>
                            </div>
                        </div>

                        {{-- COMMUNICATION LOGS --}}
                        <div class="tab-pane" id="tab-comm">
                            <div class="p-3">
                                <h6>Communication Logs</h6>
                                <div class="text-muted">Messages, emails, SMS history will appear here.</div>
                                {{-- example list --}}
                                @php $comms = $lead->communications ?? collect(); @endphp
                                @if($comms->count())
                                    @foreach($comms as $c)
                                        <div class="border p-2 my-2">
                                            <div class="small text-muted">{{ optional($c->created_at)->format('d M, Y H:i') }} — {{ $c->channel ?? 'N/A' }}</div>
                                            <div>{{ \Illuminate\Support\Str::limit($c->content ?? $c->message ?? '-', 200) }}</div>
                                        </div>
                                    @endforeach
                                @else
                                    <div class="text-center text-muted p-4">No communication logs.</div>
                                @endif
                            </div>
                        </div>

                        {{-- DOCVERIFY --}}
                        <div class="tab-pane" id="tab-doc">
                            <div class="p-3">
                                <h6>DocVerify</h6>
                                <div class="text-muted">Document verification info will appear here.</div>
                            </div>
                        </div>

                        {{-- TICKETS --}}
                        <div class="tab-pane" id="tab-tickets">
                            <div class="p-3">
                                <h6>Tickets</h6>
                                <div class="text-muted">Support tickets / issues list.</div>
                            </div>
                        </div>

                        {{-- CALL LOGS --}}
                        <div class="tab-pane" id="tab-calls">
                            <div class="p-3">
                                <h6>Call Logs</h6>
                                <div class="text-muted">Call records & recordings.</div>
                            </div>
                        </div>

                    </div>
                </div>
            </div>

            <!-- STATUS LOG card (below tabs) -->
            <div class="card mt-3">
                <div class="card-header d-flex align-items-center">
                    <h5 class="card-title mb-0">Status Log</h5>
                   
                </div>
                <div class="card-body">
                    <div class="profile-timeline">
                        @php
                            $timelineItems = $lead->timeline_items ?? ($timeline ?? null) ?? [
                                (object)['title'=>'Order Placed','desc'=>'An order was placed','date'=>$lead->created_at ?? now(),'icon'=>'ri-shopping-bag-line'],
                            ];
                        @endphp

                        @foreach($timelineItems as $item)
                        <div class="timeline-item">
                            <div class="timeline-dot"><i class="{{ $item->icon ?? 'ri-checkbox-circle-line' }}"></i></div>
                            <div class="content">
                                <div class="d-flex justify-content-between">
                                    <div>
                                        <h6 class="mb-1 fw-semibold">{{ $item->title }}</h6>
                                        <div class="small text-muted">{{ $item->desc ?? '' }}</div>
                                    </div>
                                    <div class="small text-muted">{{ isset($item->date) && ( $item->date instanceof \DateTime || method_exists($item->date,'format') ) ? optional($item->date)->format('d M, Y') : ($item->date ?? '') }}</div>
                                </div>
                            </div>
                        </div>
                        @endforeach

                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- ------------------ OFFCANVAS / MODALS ------------------ --}}
{{-- Add Note Modal --}}
<div class="modal fade" id="addNoteModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-lg modal-dialog-scrollable">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">Add Note</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>

      <form id="addNoteForm" method="post" action="{{ url('leads/'.$lead->id.'/notes') }}">
        @csrf
        <div class="modal-body">
            <div class="mb-3">
                <textarea name="note" class="form-control" rows="4" maxlength="1024" placeholder="Note" required></textarea>
                <small class="text-muted">Only 1024 characters allowed.</small>
            </div>

            <hr>
            <h6 class="mb-2">Previous Activities</h6>
            <div class="notes-list">
                @php $notes = $lead->notes ?? collect(); @endphp
                @if($notes->count())
                    @foreach($notes as $n)
                    <div class="mb-2 border rounded p-2">
                        <div class="small text-muted">{{ optional($n->created_at)->format('d M, Y H:i') }} — {{ $n->created_by ?? 'System' }}</div>
                        <div>{{ $n->note ?? $n->message ?? '-' }}</div>
                    </div>
                    @endforeach
                @else
                    <div class="text-center text-muted p-3">No activity to show.</div>
                @endif
            </div>
        </div>

        <div class="modal-footer">
            <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancel</button>
            <button type="submit" class="btn btn-primary">Save</button>
        </div>
      </form>

    </div>
  </div>
</div>

{{-- Change Lead Stage Offcanvas --}}
<div class="offcanvas offcanvas-end" tabindex="-1" id="changeStageCanvas">
  <div class="offcanvas-header">
    <h5 class="offcanvas-title">Change Lead Stage</h5>
    <button type="button" class="btn-close text-reset" data-bs-dismiss="offcanvas"></button>
  </div>

  <div class="offcanvas-body">
    <form id="changeStageForm" method="post" action="{{ url('leads/'.$lead->id.'/change-stage') }}">
      @csrf

      {{-- Lead Stage (these are pipeline stages shown as options already) --}}
       
       <input type="hidden" name="stage_id" value="{{ $lead->stage_id }}">
      
      {{-- Lead Status (this is lead_statuses table) --}}
      <div class="mb-3">
        <label class="form-label">Lead Stage (Status)</label>
        <select name="lead_status_id" id="lead_status" class="form-select" required>
            <option value="">Select Lead Stage</option>
            @foreach($leadStatuses as $ls)
                <option value="{{ $ls->id }}" {{ $lead->lead_status_id == $ls->id ? 'selected' : '' }}>
                    {{ $ls->name }}
                </option>
            @endforeach
        </select>
      </div>

      {{-- Lead Sub Stage --}}
      <div class="mb-3">
        <label class="form-label">Lead Sub Stage</label>
        <select name="lead_sub_status_id" id="lead_sub_status" class="form-select">
            <option value="">Select Sub Stage</option>
        </select>
      </div>

      {{-- Owner --}}
      <div class="mb-3">
        <label class="form-label">Owner</label>
     :<b> {{ $lead->assignedUser->first_name ?? 'Unassigned' }}   {{ $lead->assignedUser->last_name ?? '' }}
	</b><input type="hidden" name="owner_id" value="{{ $lead->lead_assign_to }}">
      </div>

      {{-- Timezone --}}
     <input type="hidden" name="timezone" value="Asia/Kolkata">

      {{-- Followup Date --}}
      <div class="mb-3">
        <label class="form-label">Followup Date</label>
        <input type="datetime-local" name="followup_date" class="form-control" />
      </div>

      {{-- Note --}}
      <div class="mb-3">
        <label class="form-label">Note</label>
        <textarea name="stage_note" class="form-control" rows="3"></textarea>
        <small class="text-muted">Only 1024 characters allowed.</small>
      </div>

      <div class="d-flex justify-content-end gap-2">
        <button type="button" class="btn btn-light" data-bs-dismiss="offcanvas">Close</button>
        <button type="submit" class="btn btn-primary">Save</button>
      </div>
    </form>
  </div>
</div>

{{-- Add Event Offcanvas --}}
<div class="offcanvas offcanvas-end" tabindex="-1" id="addEventCanvas">
  <div class="offcanvas-header">
    <h5 class="offcanvas-title">Events - Follow-up</h5>
    <button type="button" class="btn-close text-reset" data-bs-dismiss="offcanvas"></button>
  </div>
  <div class="offcanvas-body">
    <form id="addEventForm" method="post" action="{{ url('leads/'.$lead->id.'/events') }}">
      @csrf
      <div class="mb-3">
        <label class="form-label">Subject *</label>
        <input type="text" name="subject" class="form-control" value="Follow up: {{ $lead->name }}" required />
      </div>

      <div class="row g-2 mb-3">
        <div class="col">
            <label class="form-label">From</label>
            <input type="datetime-local" name="start_at" class="form-control" required />
        </div>
        <div class="col">
            <label class="form-label">To</label>
            <input type="datetime-local" name="end_at" class="form-control" />
        </div>
      </div>

      <div class="mb-3">
        <label class="form-label">Owner *</label>
        <select name="owner_id" class="form-select">
            <option value="{{ $lead->lead_assign_to ?? '' }}">{{ $lead->assignedUser->first_name ?? 'Owner' }}</option>
            @foreach($owners as $o)
                <option value="{{ $o->id }}">{{ $o->first_name }} {{ $o->last_name }}</option>
            @endforeach
        </select>
      </div>

      <div class="mb-3">
        <label class="form-label">Organiser *</label>
        <input type="text" name="organiser" class="form-control" value="{{ $lead->assignedUser->first_name ?? '' }}" />
      </div>

      <div class="mb-3">
        <label class="form-label">Description</label>
        <textarea name="description" rows="4" class="form-control"></textarea>
      </div>

      <div class="d-flex justify-content-end gap-2">
        <button type="button" class="btn btn-light" data-bs-dismiss="offcanvas">Cancel</button>
        <button type="submit" class="btn btn-primary">Submit</button>
      </div>
    </form>
  </div>
</div>

{{-- Communication Offcanvas --}}
<div class="offcanvas offcanvas-end" tabindex="-1" id="communicationCanvas">
  <div class="offcanvas-header">
    <h5 class="offcanvas-title">Single Communication</h5>
    <button type="button" class="btn-close text-reset" data-bs-dismiss="offcanvas"></button>
  </div>
  <div class="offcanvas-body">
    <ul class="nav nav-tabs mb-3" role="tablist">
        <li class="nav-item"><a class="nav-link active" data-bs-toggle="tab" href="#comm-email">Email</a></li>
        <li class="nav-item"><a class="nav-link" data-bs-toggle="tab" href="#comm-sms">SMS</a></li>
        <li class="nav-item"><a class="nav-link" data-bs-toggle="tab" href="#comm-whatsapp">WhatsApp</a></li>
    </ul>

    <div class="tab-content">
        {{-- EMAIL --}}
        <div class="tab-pane active" id="comm-email">
            <form id="commEmailForm" method="post" action="{{ url('leads/'.$lead->id.'/communicate/email') }}">
                @csrf
                <div class="mb-2">
                    <label class="form-label">From Email</label>
                    <select name="from_email" class="form-select">
                        <option value="">default@company.com</option>
                    </select>
                </div>
                <div class="mb-2">
                    <label class="form-label">Send To</label>
                    <select name="send_to" class="form-select">
                        <option value="{{ $lead->email }}">{{ $lead->email ?? 'No Email' }}</option>
                    </select>
                </div>
                <div class="mb-2">
                    <label class="form-label">Subject</label>
                    <input type="text" name="subject" class="form-control" />
                </div>
                <div class="mb-2">
                    <label class="form-label">Message (Drag & drop editor placeholder)</label>
                    <textarea name="email_body" rows="6" class="form-control" placeholder="Enter email body or use template editor..."></textarea>
                </div>
                <div class="d-flex justify-content-end gap-2">
                    <button class="btn btn-light" data-bs-dismiss="offcanvas">Close</button>
                    <button type="submit" class="btn btn-primary">Send Now</button>
                </div>
            </form>
        </div>

        {{-- SMS --}}
        <div class="tab-pane" id="comm-sms">
            <form id="commSmsForm" method="post" action="{{ url('leads/'.$lead->id.'/communicate/sms') }}">
                @csrf
                <div class="mb-2">
                    <label class="form-label">Send To</label>
                    <select name="sms_to" class="form-select">
                        <option value="{{ $lead->mobile }}">{{ $lead->mobile ?? 'No Mobile' }}</option>
                    </select>
                </div>
                <div class="mb-2">
                    <label class="form-label">Message Text</label>
                    <textarea name="sms_body" rows="4" class="form-control" placeholder="Enter SMS text..."></textarea>
                </div>
                <div class="d-flex justify-content-end gap-2">
                    <button class="btn btn-light" data-bs-dismiss="offcanvas">Close</button>
                    <button type="submit" class="btn btn-primary">Send SMS</button>
                </div>
            </form>
        </div>

        {{-- WHATSAPP --}}
        <div class="tab-pane" id="comm-whatsapp">
            <form id="commWaForm" method="post" action="{{ url('leads/'.$lead->id.'/communicate/whatsapp') }}">
                @csrf
                <div class="mb-2">
                    <label class="form-label">Send To</label>
                    <select name="wa_to" class="form-select">
                        <option value="{{ $lead->mobile }}">{{ $lead->mobile ?? 'No Mobile' }}</option>
                    </select>
                </div>
                <div class="mb-2">
                    <label class="form-label">Template</label>
                    <select name="wa_template" class="form-select">
                        <option value="">Select Template</option>
                    </select>
                </div>
                <div class="mb-2">
                    <label class="form-label">Message Text</label>
                    <textarea name="wa_body" rows="4" class="form-control" placeholder="Enter WhatsApp message..."></textarea>
                </div>
                <div class="d-flex justify-content-end gap-2">
                    <button class="btn btn-light" data-bs-dismiss="offcanvas">Close</button>
                    <button type="submit" class="btn btn-success">Send WhatsApp</button>
                </div>
            </form>
        </div>
    </div>
  </div>
</div>

{{-- Reassign Modal --}}
<div class="modal fade" id="reassignModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-md">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">Re-assign Lead</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>

      <form id="reassignForm" method="post" action="{{ url('leads/'.$lead->id.'/reassign') }}">
        @csrf
        <div class="modal-body">
            <div class="mb-3">
                <label class="form-label">Re-assigned to *</label>
                <select name="reassign_to" class="form-select" required>
                    <option value="">Select counsellor</option>
                    @foreach(\App\Register::where('organization', $lead->organization_id)->get() as $r)
                        <option value="{{ $r->id }}">{{ $r->first_name }} {{ $r->last_name }}</option>
                    @endforeach
                </select>
            </div>

            <div class="mb-3">
                <label class="form-label">Type</label>
                <div class="d-flex gap-2">
                    <button type="button" class="btn btn-outline-primary btn-sm">Add Owner</button>
                    <button type="button" class="btn btn-outline-secondary btn-sm">Remove & Add</button>
                </div>
            </div>
        </div>

        <div class="modal-footer">
            <button type="submit" class="btn btn-primary">Reassign Leads</button>
        </div>
      </form>
            <button class="btn btn-light" data-bs-dismiss="modal">Cancel</button>

    </div>
  </div>
</div>

{{-- WhatsApp Chat History Offcanvas --}}
<div class="offcanvas offcanvas-end" tabindex="-1" id="whatsappChatCanvas">
  <div class="offcanvas-header">
    <h5 class="offcanvas-title">WhatsApp Chat History</h5>
    <button type="button" class="btn-close text-reset" data-bs-dismiss="offcanvas"></button>
  </div>
  <div class="offcanvas-body">
    <div class="mb-3">
        <div class="border rounded p-3" style="height:360px; overflow:auto;">
            @php $chats = $lead->whatsappChats ?? collect(); @endphp
            @if($chats->count())
                @foreach($chats as $c)
                    <div class="mb-2">
                        <div class="small text-muted">{{ optional($c->created_at)->format('d M, Y H:i') }}</div>
                        <div class="p-2 bg-light rounded">{{ $c->message ?? '-' }}</div>
                    </div>
                @endforeach
            @else
                <div class="text-center text-muted">No WhatsApp history.</div>
            @endif
        </div>
    </div>

    <form id="waReplyForm" method="post" action="{{ url('leads/'.$lead->id.'/whatsapp/send') }}">
        @csrf
        <div class="mb-2">
            <textarea name="reply" class="form-control" rows="3" placeholder="Type a reply..."></textarea>
        </div>
        <div class="text-end">
            <button class="btn btn-light" data-bs-dismiss="offcanvas">Close</button>
            <button type="submit" class="btn btn-success">Send</button>
        </div>
    </form>
  </div>
</div>

@endsection

@section('script')
<script src="{{ URL::asset('build/libs/list.js/list.min.js') }}"></script>
<script src="{{ URL::asset('build/libs/sweetalert2/sweetalert2.min.js') }}"></script>
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

<script>
/* ---------- AJAX setup ---------- */
$.ajaxSetup({
    headers: {
        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content'),
        'Accept': 'application/json'
    }
});

/* ---------- small helpers ---------- */
function closeOffcanvas(selectorId) {
    const el = document.getElementById(selectorId);
    if (!el) return;
    const inst = bootstrap.Offcanvas.getInstance(el) || new bootstrap.Offcanvas(el);
    inst.hide();
}
function closeModal(selectorId) {
    const el = document.getElementById(selectorId);
    if (!el) return;
    const inst = bootstrap.Modal.getInstance(el) || new bootstrap.Modal(el);
    inst.hide();
}
function escapeHtml(unsafe) {
    if (!unsafe) return '';
    return String(unsafe).replace(/[&<>"'`=\/]/g, function (s) {
        return ({'&':'&amp;','<':'&lt;','>':'&gt;','"':'&quot;',"'":'&#39;','/':'&#x2F;','`':'&#x60;','=':'&#x3D;'})[s];
    });
}
function limitStr(s,n){ if(!s) return ''; return s.length>n ? s.substr(0,n)+'...' : s; }

/* ---------- update UI from returned lead JSON ---------- */
function updateLeadSummary(lead){
    try{
        if(lead.name) document.querySelectorAll('[data-lead-name]').forEach(e=>e.textContent = lead.name);
        if(lead.pipeline && lead.pipeline.name) document.querySelectorAll('[data-lead-pipeline]').forEach(e=>e.textContent = lead.pipeline.name);
        if(lead.stage && lead.stage.name) document.querySelectorAll('[data-lead-stage]').forEach(e=>e.textContent = lead.stage.name);
        if(lead.leadStatus && lead.leadStatus.name) document.querySelectorAll('[data-lead-status]').forEach(e=>e.textContent = lead.leadStatus.name);
        if(lead.leadSubStatus && lead.leadSubStatus.name) document.querySelectorAll('[data-lead-substatus]').forEach(e=>e.textContent = lead.leadSubStatus.name);
        if(lead.assignedUser) {
            document.querySelectorAll('[data-lead-owner]').forEach(e=>e.textContent = (lead.assignedUser.first_name||'') + ' ' + (lead.assignedUser.last_name||''));
            document.querySelectorAll('[data-lead-owner-id]').forEach(e=>e.textContent = lead.assignedUser.id || lead.lead_assign_to || 0);
        }
        if(lead.lead_score !== undefined) document.querySelectorAll('[data-lead-score]').forEach(e=>e.textContent = lead.lead_score);
        if(lead.lead_weight_count !== undefined) document.querySelectorAll('[data-lead-weight]').forEach(e=>e.textContent = lead.lead_weight_count);
        if(lead.email) document.querySelectorAll('[data-lead-email],[data-lead-email-table]').forEach(e=>e.textContent = lead.email);
        if(lead.mobile) document.querySelectorAll('[data-lead-mobile],[data-lead-mobile-table]').forEach(e=>e.textContent = lead.mobile);
    }catch(e){ console.warn(e); }
}

function buildTimelineHtml(arr){
    if(!arr || !arr.length) return '<div class="text-center p-4 text-muted">No activity found.</div>';
    let html = '';
    arr.forEach(item=>{
        const date = item.created_at ? new Date(item.created_at).toLocaleString() : '';
        html += `<div class="timeline-item">
            <div class="timeline-dot"><i class="${escapeHtml(item.icon||'ri-history-line')}"></i></div>
            <div class="content">
                <div class="d-flex justify-content-between">
                    <div>
                        <strong>${escapeHtml(item.title||'Activity')}</strong>
                        <div class="small text-muted">${escapeHtml(limitStr(item.details||'',120))}</div>
                    </div>
                    <div class="small text-muted">${escapeHtml(date)}</div>
                </div>
            </div>
        </div>`;
    });
    return html;
}
function updateTimelineFromLead(lead){
    if(!lead || !lead.timeline) return;
    const container = document.querySelector('.profile-timeline');
    if(!container) return;
    container.innerHTML = buildTimelineHtml(lead.timeline);
}

/* ---------- sub-status list (client-side) ---------- */
const allSubStatuses = @json($leadSubStatuses ?? []);
const currentStatus = "{{ $lead->lead_status_id ?? '' }}";
const currentSub = "{{ $lead->lead_sub_status_id ?? '' }}";
function loadSubStatuses(statusId, selected=null){
    const dd = $("#lead_sub_status");
    dd.html('<option value="">Select Sub Stage</option>');
    if(!statusId) return;
    const filtered = allSubStatuses.filter(s => s.lead_status_id == statusId);
    filtered.forEach(s => dd.append(`<option value="${s.id}" ${selected==s.id ? 'selected' : ''}>${s.name}</option>`));
}
$(document).ready(function(){ if(currentStatus) loadSubStatuses(currentStatus, currentSub); });

/* ---------- generic AJAX form submit ---------- */
function ajaxSubmitForm(formSelector, successText){
    $(document).on('submit', formSelector, function(e){
        e.preventDefault();
        const $form = $(this);
        const url = $form.attr('action');
        const method = ($form.attr('method') || 'POST').toUpperCase();
        const formData = new FormData(this);

        $.ajax({
            url: url,
            method: method,
            data: formData,
            processData: false,
            contentType: false,
        }).done(function(res){
            if(res.status && res.status === 'success'){
                Swal.fire({ icon: 'success', title: 'Success', text: res.message || successText || 'Saved' });
                if(res.lead) {
                    updateLeadSummary(res.lead);
                    updateTimelineFromLead(res.lead);
                }
                const parentOff = $form.closest('.offcanvas');
                if(parentOff && parentOff.length) closeOffcanvas(parentOff.attr('id'));
                const parentModal = $form.closest('.modal');
                if(parentModal && parentModal.length) closeModal(parentModal.attr('id'));
            } else {
                Swal.fire({ icon: 'error', title: 'Error', text: res.message || 'Something went wrong' });
            }
        }).fail(function(xhr){
            let msg = 'Server error';
            try {
                if(xhr.responseJSON && xhr.responseJSON.message) msg = xhr.responseJSON.message;
                else if(xhr.responseText) msg = xhr.responseText;
            } catch(e){}
            Swal.fire({ icon: 'error', title: 'Error', text: msg });
        });
    });
}

/* attach forms */
ajaxSubmitForm('#changeStageForm','Stage updated');
ajaxSubmitForm('#addNoteForm','Note added');
ajaxSubmitForm('#addEventForm','Event created');
ajaxSubmitForm('#commEmailForm','Email sent');
ajaxSubmitForm('#commSmsForm','SMS sent');
ajaxSubmitForm('#commWaForm','WhatsApp sent');
ajaxSubmitForm('#reassignForm','Reassigned');
ajaxSubmitForm('#waReplyForm','Message sent');
ajaxSubmitForm('#pipelineUpdateForm','Pipeline updated');
ajaxSubmitForm('#sourceUpdateForm','Source updated');

/* update sub-status when status changes */
$(document).on('change', '#lead_status', function(){ loadSubStatuses($(this).val()); });

/* bootstrap tooltip init and pipeline scroll */
document.addEventListener('DOMContentLoaded', function () {
    var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'))
    tooltipTriggerList.map(function (tooltipTriggerEl) { return new bootstrap.Tooltip(tooltipTriggerEl) });

    const pb = document.querySelector('.pipeline-bar');
    if(pb) pb.addEventListener('wheel', function(e){ e.preventDefault(); pb.scrollLeft += e.deltaY; });
});

function resetFollowup(type){ console.log('resetFollowup', type); }
</script>


<script>
document.addEventListener("DOMContentLoaded", function () {

    var calendar = new FullCalendar.Calendar(document.getElementById("leadCalendar"), {
        initialView: "dayGridMonth",
        height: 650,
        selectable: true,

        headerToolbar: {
            left: "prev,next today",
            center: "title",
            right: "dayGridMonth,timeGridWeek,listWeek"
        },

        eventColor: "#0d6efd",
        eventDisplay: "block",

events: "{{ url('calendar/events') }}?lead_id={{ $lead->id }}",
        dateClick: function(info) {
            document.getElementById("selectedDate").value = info.dateStr;
            new bootstrap.Offcanvas('#addEventCanvas').show();
        }
    });

    calendar.render();


   
});
</script>
	



@endsection
