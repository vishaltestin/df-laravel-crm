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
.tabs-scroll-wrapper{
    overflow-x: auto;
    overflow-y: hidden;
    -webkit-overflow-scrolling: touch;
}

.tabs-scroll{
    flex-wrap: nowrap !important;
    white-space: nowrap;
}

.tabs-scroll .nav-link{
    white-space: nowrap;
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
            $iconList = [
                'ri-user-line',
                'ri-phone-line',
                'ri-chat-1-line',
                'ri-bill-line',
                'ri-contacts-book-line',
                'ri-checkbox-circle-line'
            ];

            $defaultIcon = 'ri-shape-line';

            $currentStageId = $lead->stage_id ?? null;

            $currentStage = collect($stages)->firstWhere('id', $currentStageId);
            $currentOrder = $currentStage->sort_order ?? null;
        @endphp

        @foreach($stages as $stage)

            @php
                $class = 'upcoming';

                if($currentOrder !== null){
                    if($stage->sort_order < $currentOrder){
                        $class = 'completed';
                    }
                    elseif($stage->sort_order == $currentOrder){
                        $class = 'current';
                    }
                }

                $idx  = $loop->index;
                $icon = $idx < count($iconList) ? $iconList[$idx] : $defaultIcon;
            @endphp

            <div class="pipeline-step {{ $class }}" title="{{ $stage->name }}">

                <div class="step-icon">
                    <i class="{{ $icon }} {{ $class == 'current' ? 'text-white' : 'text-muted' }}"></i>
                </div>

                <div>
                    <div class="fw-semibold" style="font-size:13px;">
                        {{ $stage->name }}
                    </div>

                    @if(!empty($stage->stage_description))
                        <div class="small text-muted">
                            {{ \Illuminate\Support\Str::limit($stage->stage_description,40) }}
                        </div>
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
					<hr>
					<button type="button"
        class="btn btn-success"
        id="btnCallNow"
        data-lead-id="{{ $lead->id }}">
    📞 Call Now
</button>

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
       <button type="button" class="btn btn-light" data-bs-dismiss="offcanvas">Close</button>
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
			
  @php
    $resp = $lead->applicationResponse;
 
    $app = $lead->application;
//dd($lead);

@endphp
			
			
  <div class="card">
               <div class="card-header d-flex align-items-center justify-content-between">
    <h5 class="card-title mb-0">
        <i class="ri-building-4-line me-1"></i> Application
    </h5>

    @if($lead->applicationForm)
        <a href="{{ route('application.form.show', [
            'code' => $lead->applicationForm->unique_code,
            'lead_id' => encrypt($lead->id)
        ]) }}" class="btn btn-sm btn-primary"  target="_blank">
            Fill Application Form
        </a>
    @endif
</div>

                <div class="card-body  ">

  <p><b>Current Step:</b> {{ $resp->current_step ?? 'Not Yet Started' }}</p>

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
	
	
		
		<button type="button" class="btn btn-success btn-sm" data-bs-toggle="modal" data-bs-target="#paymentModal">
    Take Payment
</button>

<div class="modal fade" id="paymentModal" tabindex="-1">
  <div class="modal-dialog">
    <form method="POST" id="leadPaymentForm">
        @csrf
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title">Take Payment - {{ $lead->name }}</h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
          </div>

          <div class="modal-body">

        {{-- ========================= PAYMENT CONSOLE (LEAD VIEW) ========================= --}} 
		<div class="border rounded p-3 bg-light"> 
		<div class="d-flex justify-content-between align-items-center mb-2"> 
		<h6 class="mb-0"><i class="ri-bank-card-line me-1"></i> Payment Console</h6> 
		@if(($app->payment_status ?? '') === 'paid') 
			<span class="badge bg-success">Paid</span> 
		@else <span class="badge bg-warning text-dark">Pending</span> 
		@endif </div> 
		{{-- Fees info --}} 
		@php $baseAmount = 0; 
		if($lead->application && $lead->application->form){ 		$baseAmount = (float)($lead->application->form->total_amount ?? $lead->application->form->fees ?? 0); } 
		$paidAmount = (float)($resp->payment_amount ?? 0); 
		@endphp 
		<div class="row g-2 mb-2"> 
		<div class="col-6"> 
		<div class="small text-muted">Total Payable</div> 
		<div class="fw-bold">₹<span id="payTotal">{{ number_format($baseAmount,2) }}</span></div> 
		</div>

		<div class="col-6 text-end"> <div class="small text-muted">Current Paid</div>
		<div class="fw-bold">₹<span id="payPaid">{{ number_format($paidAmount,2) }}</span></div> 
		</div>

		</div> 
		{{-- Payment Form --}} 
		<input type="hidden" name="coupon_id" id="coupon_id" value="">
		<input type="hidden" name="payment_amount" id="payment_amount" value="{{ $baseAmount }}">
		<input type="hidden" name="payment_status" id="payment_status" value="paid"> 
		<div class="row g-2"> <div class="col-md-6">
		<label class="form-label">Payment Method</label>
		<select name="payment_method" id="payment_method" class="form-select" required>
		<option value="">Select</option>
		<option value="online">Online</option> 
		<option value="offline">Offline</option> 
		<option value="cash">Cash</option>
		<option value="dd">DD</option>
		<option value="free">Free</option>
		</select>
		</div> 
		<div class="col-md-6"> 
		<label class="form-label">Final Amount</label> 
		<input type="number" step="0.01" class="form-control" name="final_amount" id="final_amount" value="{{ $baseAmount }}" required> </div> 
		<div class="col-md-8"> <label class="form-label">Coupon Code</label>
		<input type="text" class="form-control" name="coupon_code" id="coupon_code" placeholder="Enter coupon code (optional)"> </div> <div class="col-md-4 d-flex align-items-end"> 
		<button type="button" class="btn btn-outline-primary w-100" id="btnApplyCoupon"> Apply Coupon </button> </div> </div> <div class="d-flex justify-content-between align-items-center mt-3"> <div> <div class="small text-muted">Discount</div> <div class="fw-bold text-success">₹<span id="discount_amount">0.00</span></div>
		</div>
		<button type="submit" class="btn btn-success"> <i class="ri-check-line me-1"></i> Mark Payment Done </button>
		</div>  
		
		</div>
        </div>
		</div>
   </form>
  </div>
</div>

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
<div style="overflow-x:auto; overflow-y:hidden; -webkit-overflow-scrolling:touch;">
    <ul class="nav nav-tabs mb-3 flex-nowrap"
        style="flex-wrap:nowrap; white-space:nowrap;">

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
            <a class="nav-link" data-bs-toggle="tab" href="#tab-calendar">
                <i class="ri-calendar-event-line me-1"></i> Events
            </a>
        </li>

        <li class="nav-item">
            <a class="nav-link" data-bs-toggle="tab" href="#tab-doc">
                <i class="ri-file-paper-2-line me-1"></i> DocVerify
            </a>
        </li>

        <li class="nav-item">
            <a class="nav-link" data-bs-toggle="tab" href="#tab-tickets">
                <i class="ri-customer-service-2-line me-1"></i> Tickets
            </a>
        </li>

        <li class="nav-item">
            <a class="nav-link" data-bs-toggle="tab" href="#tab-calls">
                <i class="ri-phone-line me-1"></i> Call Logs
            </a>
        </li>

        <li class="nav-item">
            <a class="nav-link" data-bs-toggle="tab" href="#tab-calendar-pro">
                <i class="ri-calendar-event-line me-1"></i> Calendar Pro
            </a>
        </li>

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
$uploadVia = ucfirst($extra['upload_via'] ?? '');
@endphp

@if(!empty($lead->programme->name ?? $lead->institute_applying_for))
<tr>
<th style="width:220px">Form Interested In</th>
<td>: {{ $lead->programme->name ?? $lead->institute_applying_for }}</td>
</tr>
@endif

@if(!empty($lead->email))
<tr>
<th>Email</th>
<td>: <span data-lead-email-table>{{ $lead->email }}</span></td>
</tr>
@endif

@if(!empty($lead->mobile))
<tr>
<th>Mobile</th>
<td>: <span data-lead-mobile-table>{{ $lead->mobile }}</span></td>
</tr>
@endif

@if(!empty($city->name))
<tr>
<th>City</th>
<td>: {{ $city->name }}</td>
</tr>
@endif

@if(!empty($state->name))
<tr>
<th>State</th>
<td>: {{ $state->name }}</td>
</tr>
@endif

@if(!empty($lead->leadStatus->name))
<tr>
<th>Status</th>
<td>: <span data-lead-status-table>{{ $lead->leadStatus->name }}</span></td>
</tr>
@endif

@if(!empty($lead->leadSubStatus->name))
<tr>
<th>Sub Status</th>
<td>: <span data-lead-substatus-table>{{ $lead->leadSubStatus->name }}</span></td>
</tr>
@endif

@if(!empty($lead->qualification_name))
<tr>
<td>Qualification</td>
<td>{{ $lead->qualification_name }}</td>
</tr>
@endif

@if(!empty($lead->faculty_name))
<tr>
<td>Faculty</td>
<td>{{ $lead->faculty_name }}</td>
</tr>
@endif

@if(!empty($uploadVia))
<tr>
<td>Lead From</td>
<td><span class="badge bg-info">{{ $uploadVia }}</span></td>
</tr>
@endif

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
		<div class="mb-3">
    <h6 class="fw-bold text-primary mb-2">Upcoming Events</h6>
    <div class="table-responsive">
        <table class="table table-sm table-hover align-middle">
            <thead class="table-light">
                <tr>
                    <th>Date</th>
                    <th>Time</th>
                    <th>Subject</th>
                    <th>Type</th>
                    <th>Owner</th>
                </tr>
            </thead>
            <tbody>
                @forelse($lead->events as $event)
                    <tr>
                        <td>{{ \Carbon\Carbon::parse($event->start_at)->format('d M Y') }}</td>
                        <td>
                            {{ \Carbon\Carbon::parse($event->start_at)->format('H:i') }}
                            -
                            {{ \Carbon\Carbon::parse($event->end_at)->format('H:i') }}
                        </td>
                        <td>{{ $event->subject }}</td>
                        <td>
                            <span class="badge bg-info text-dark">
                                {{ ucfirst(str_replace('_',' ',$event->event_type)) }}
                            </span>
                        </td>
                        <td>{{ $event->owner->first_name ?? '—' }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="text-center text-muted">No upcoming events</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
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

        <div class="d-flex justify-content-between mb-2">
            <h6>Notes</h6>
            <button class="btn btn-sm btn-primary"
                    data-bs-toggle="modal"
                    data-bs-target="#addNoteModal"
                    onclick="resetFollowup('note')">
                Add Note
            </button>
        </div>

        <div class="table-responsive">
            <table class="table table-sm table-hover align-middle">
                <thead class="table-light">
                    <tr>
                        <th>Date</th>
                        <th>Created By</th>
                        <th>Note Preview</th>
                        <th>View</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($lead->notes as $n)
                        <tr>
                            <td>{{ optional($n->created_at)->format('d M Y H:i') }}</td>
                            <td>{{ $n->creator->first_name ?? 'System' }} {{ $n->creator->last_name ?? '' }}</td>
                            <td>{{ \Illuminate\Support\Str::limit(strip_tags($n->note ?? $n->message ?? '-'), 50) }}</td>
                            <td>
                                <button class="btn btn-sm btn-outline-primary"
                                        data-bs-toggle="modal"
                                        data-bs-target="#noteViewModal{{ $n->id }}">
                                    View
                                </button>
                            </td>
                        </tr>

                        {{-- View Note Modal --}}
                        <div class="modal fade" id="noteViewModal{{ $n->id }}" tabindex="-1">
                          <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable">
                            <div class="modal-content">
                              <div class="modal-header">
                                <h5 class="modal-title">Note Details</h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                              </div>
                              <div class="modal-body">
                                  <div class="mb-2">
                                      <strong>Date:</strong> {{ optional($n->created_at)->format('d M Y H:i') }}
                                  </div>
                                  <div class="mb-2">
                                      <strong>By:</strong> {{ $n->creator->first_name ?? 'System' }} {{ $n->creator->last_name ?? '' }}
                                  </div>
                                  <hr>
                                  <div>
                                      {!! nl2br(e($n->note ?? $n->message ?? '-')) !!}
                                  </div>
                              </div>
                            </div>
                          </div>
                        </div>

                    @empty
                        <tr>
                            <td colspan="4" class="text-center text-muted">No notes found.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

    </div>
</div>


                        {{-- COMMUNICATION LOGS --}}
                <div class="tab-pane" id="tab-comm">
    <div class="p-3">
        <div class="d-flex align-items-center justify-content-between mb-2">
            <h6 class="mb-0">Communication Logs</h6>
            <span class="text-muted small">
                Total: {{ $lead->communications->count() ?? 0 }}
            </span>
        </div>

        @php
            $comms = $lead->communications ?? collect();
        @endphp

        @if($comms->count())
            <div class="table-responsive">
                <table class="table table-sm table-hover align-middle">
                    <thead class="table-light">
                        <tr>
                            <th style="width:140px;">Date</th>
                            <th style="width:110px;">Channel</th>
                            <th>Details</th>
                            <th style="width:110px;">Status</th>
                            <th style="width:110px;">View</th>
                        </tr>
                    </thead>

                    <tbody>
                        @foreach($comms as $c)

                            @php
                                $status = $c->status ?? 'pending';

                                $statusClass = match($status) {
                                    'sent' => 'bg-success',
                                    'failed' => 'bg-danger',
                                    default => 'bg-warning text-dark'
                                };

                                $toText = $c->to ?? '-';
                                $subject = $c->subject ?? '';
                                $preview = \Illuminate\Support\Str::limit(strip_tags($c->content ?? ''), 60);

                                $queuedAt = $c->created_at ? $c->created_at->format('d M Y, h:i A') : '-';
                                $sentAt = $c->sent_at ? \Carbon\Carbon::parse($c->sent_at)->format('d M Y, h:i A') : '-';
                            @endphp

                            <tr>
                                {{-- Date --}}
                                <td>
                                    <div class="fw-semibold">{{ $queuedAt }}</div>
                                    <div class="text-muted small">
                                        Sent: <span class="fw-semibold">{{ $sentAt }}</span>
                                    </div>
                                </td>

                                {{-- Channel --}}
                                <td>
                                    <span class="badge bg-secondary text-uppercase">
                                        {{ $c->channel ?? 'N/A' }}
                                    </span>
                                </td>

                                {{-- Details (2 rows style) --}}
                                <td>
                                    <div class="fw-semibold">
                                        To: <span class="text-dark">{{ $toText }}</span>
                                    </div>

                                    <div class="text-muted small">
                                        @if($c->channel === 'email')
                                            Subject: <span class="text-dark">{{ $subject ?: '-' }}</span>
                                        @else
                                            {{ $preview ?: '-' }}
                                        @endif
                                    </div>
                                </td>

                                {{-- Status --}}
                                <td>
                                    <span class="badge {{ $statusClass }} text-uppercase">
                                        {{ $status }}
                                    </span>
                                </td>

                                {{-- View --}}
                                <td>
                                    <button class="btn btn-sm btn-outline-primary"
                                        data-bs-toggle="modal"
                                        data-bs-target="#commViewModal{{ $c->id }}">
                                        View
                                    </button>
                                </td>
                            </tr>

                            {{-- View Modal --}}
                            <div class="modal fade" id="commViewModal{{ $c->id }}" tabindex="-1">
                                <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable">
                                    <div class="modal-content">
                                        <div class="modal-header">
                                            <h5 class="modal-title">
                                                {{ strtoupper($c->channel) }} Details
                                            </h5>
                                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                        </div>

                                        <div class="modal-body">

                                            <div class="row g-2 mb-3">
                                                <div class="col-md-6">
                                                    <div class="border rounded p-2">
                                                        <strong>Queued At:</strong><br>
                                                        {{ $queuedAt }}
                                                    </div>
                                                </div>

                                                <div class="col-md-6">
                                                    <div class="border rounded p-2">
                                                        <strong>Sent At:</strong><br>
                                                        {{ $sentAt }}
                                                    </div>
                                                </div>

                                                <div class="col-md-6">
                                                    <div class="border rounded p-2">
                                                        <strong>To:</strong><br>
                                                        {{ $toText }}
                                                    </div>
                                                </div>

                                                <div class="col-md-6">
                                                    <div class="border rounded p-2">
                                                        <strong>Status:</strong><br>
                                                        <span class="badge {{ $statusClass }} text-uppercase">
                                                            {{ $status }}
                                                        </span>
                                                    </div>
                                                </div>

                                                @if($c->channel === 'email')
                                                <div class="col-md-12">
                                                    <div class="border rounded p-2">
                                                        <strong>Subject:</strong><br>
                                                        {{ $subject ?: '-' }}
                                                    </div>
                                                </div>
                                                @endif
                                            </div>

                                            <hr>

                                            <div>
                                                <strong>Message:</strong>
                                                <div class="border rounded p-3 mt-2">
                                                    {!! $c->content ?? '-' !!}
                                                </div>
                                            </div>

                                            {{-- Fail reason / response json --}}
                                            @if(!empty($c->response_json))
                                                <hr>
                                                <div>
                                                    <strong>Response / Error:</strong>
                                                    <pre class="bg-light p-2 rounded small mb-0">{{ $c->response_json }}</pre>
                                                </div>
                                            @endif

                                        </div>
                                    </div>
                                </div>
                            </div>

                        @endforeach
                    </tbody>
                </table>
            </div>

        @else
            <div class="text-center text-muted p-4">No communication logs.</div>
        @endif
    </div>
</div>

                        {{-- DOCVERIFY --}}
<div class="tab-pane" id="tab-doc">
    <div class="p-3">

        <div class="d-flex justify-content-between align-items-center mb-3">
            <h6 class="mb-0">DocVerify</h6>

            <button class="btn btn-sm btn-primary" data-bs-toggle="modal" data-bs-target="#uploadDocModal">
                <i class="ri-upload-2-line me-1"></i> Upload Document
            </button>
        </div>

        {{-- Documents List --}}
        <div class="table-responsive">
            <table class="table table-bordered table-sm align-middle">
                <thead class="bg-light">
                    <tr>
                        <th>Doc Name</th>
                        <th>Type</th>
                        <th>Uploaded</th>
                        <th width="160">Action</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($documents as $doc)
                        <tr>
                            <td>{{ $doc->doc_name }}</td>
                            <td>{{ $doc->doc_type ?? '-' }}</td>
                            <td>{{ \Carbon\Carbon::parse($doc->created_at)->format('d M Y, h:i A') }}</td>
                            <td>
                                <a class="btn btn-sm btn-outline-primary"
                                   target="_blank"
                                   href="{{ route('leads.documents.view', $doc->id) }}">
                                    View
                                </a>

                                <form method="POST"
                                      action="{{ route('leads.documents.delete', $doc->id) }}"
                                      style="display:inline-block"
                                      onsubmit="return confirm('Delete this document?')">
                                    @csrf
                                    @method('DELETE')
                                    <button class="btn btn-sm btn-outline-danger">
                                        Delete
                                    </button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="text-center text-muted">
                                No documents uploaded.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- Upload Modal --}}
        <div class="modal fade" id="uploadDocModal" tabindex="-1">
            <div class="modal-dialog">
                <form method="POST"
                      action="{{ route('leads.documents.upload', $lead->id) }}"
                      enctype="multipart/form-data"
                      class="modal-content">
                    @csrf

                    <div class="modal-header">
                        <h5 class="modal-title">Upload Document</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>

                    <div class="modal-body">

                        <div class="mb-2">
                            <label class="form-label">Document Type</label>
                            <select name="doc_type" class="form-select">
                                <option value="">Select</option>
                                <option value="aadhaar">Aadhaar</option>
                                <option value="pan">PAN</option>
                                <option value="photo">Photo</option>
                                <option value="marksheet">Marksheet</option>
                                <option value="other">Other</option>
                            </select>
                        </div>

                        <div class="mb-2">
                            <label class="form-label">Document Name (Optional)</label>
                            <input type="text" name="doc_name" class="form-control" placeholder="Eg: Aadhaar Front">
                        </div>

                        <div class="mb-2">
                            <label class="form-label">Upload File</label>
                            <input type="file" name="document" class="form-control" required>
                            <div class="small text-muted mt-1">Max 5MB | PDF/JPG/PNG supported</div>
                        </div>

                    </div>

                    <div class="modal-footer">
                        <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancel</button>
                        <button class="btn btn-primary">
                            Upload
                        </button>
                    </div>

                </form>
            </div>
        </div>

    </div>
</div>


                        {{-- TICKETS --}}
                  <div class="tab-pane" id="tab-tickets">
					  <div class="p-3">

						<div class="d-flex justify-content-between mb-2">
						  <h6>Tickets / Queries</h6>
						  <button class="btn btn-sm btn-primary" onclick="openCreateTicket()">+ New Ticket</button>
						</div>

						<div id="ticketList" class="mt-2">
						  <div class="text-muted text-center">Click to load tickets</div>
						</div>

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
                $timelineItems = $timeline ?? collect();
            @endphp

            @forelse($timelineItems as $item)
                <div class="timeline-item">
                    <div class="timeline-dot">
                        <i class="{{ $item->icon ?? 'ri-history-line' }}"></i>
                    </div>
                    <div class="content">
                        <div class="d-flex justify-content-between">
                            <div>
                                <h6 class="mb-1 fw-semibold">{{ $item->title ?? 'Activity' }}</h6>
                                <div class="small text-muted">{{ $item->details ?? '' }}</div>
                            </div>
                            <div class="small text-muted">
                                {{ optional($item->created_at)->format('d M, Y H:i') }}
                            </div>
                        </div>
                    </div>
                </div>
            @empty
                <div class="text-center text-muted">No activity found.</div>
            @endforelse

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
            <input type="datetime-local" name="start_at" id="startAt" class="form-control" required />
        </div>
        <div class="col">
            <label class="form-label">To</label>
            <input type="datetime-local" name="end_at" id="endAt" class="form-control" />
        </div>
      </div>
<div class="mb-3">
  <label class="form-label">Event Type *</label>
  <select name="event_type" class="form-select" required>
      <option value="">Select Event Type</option>
      <option value="phone_call">Phone Call</option>
      <option value="video_call">Video Call</option>
      <option value="meeting">Meeting</option>
      <option value="lunch">Lunch</option>
  </select>
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
       <button type="button" class="btn btn-light" data-bs-dismiss="offcanvas">Close</button>
        <button type="submit" class="btn btn-primary">Submit</button>
      </div>
    </form>
  </div>
</div>

{{-- Communication Offcanvas --}}

<div class="offcanvas offcanvas-end" tabindex="-1" id="communicationCanvas" style="width:700px;">
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
            <label class="form-label">Template</label>
            <select id="emailTemplateSelect" class="form-select">
                <option value="">Select Template</option>
            </select>
        </div>
  @php
    $u = Sentinel::getUser();
@endphp
        <div class="mb-2">
            <label class="form-label">From Email</label>
            <select name="from_email" class="form-select">
                <option value="{{ $u->email ?? 'User Not Found' }}">{{ $u->email ?? 'User Not Found' }}</option>
            </select>
        </div>
 


  

        <div class="mb-2">
            <label class="form-label">Subject</label>
            <input type="text" name="subject" id="emailSubject" class="form-control" />
        </div>

        <div class="mb-2">
            <label class="form-label">Message</label>
            <textarea name="email_body" id="emailBody" rows="6" class="form-control"></textarea>
        </div>

        <div class="d-flex justify-content-end gap-2">
           <button type="button" class="btn btn-light" data-bs-dismiss="offcanvas">Close</button>
            <button type="submit" class="btn btn-primary">Send Now</button>
        </div>
    </form>
</div>

        {{-- SMS --}}
        <div class="tab-pane" id="comm-sms">
    <form id="commSmsForm" method="post" action="{{ url('leads/'.$lead->id.'/communicate/sms') }}">    
	@csrf

        <div class="mb-2">
            <label class="form-label">Template</label>
            <select id="smsTemplateSelect" class="form-select">
                <option value="">Select Template</option>
            </select>
        </div>

      

        <div class="mb-2">
            <label class="form-label">Message Text</label>
            <textarea name="sms_body" id="smsBody" rows="4" class="form-control"></textarea>
        </div>

        <div class="d-flex justify-content-end gap-2">
          <button type="button" class="btn btn-light" data-bs-dismiss="offcanvas">Close</button>
            <button type="submit" class="btn btn-primary">Send SMS</button>
        </div>
    </form>
</div>

        {{-- WHATSAPP --}}
        <div class="tab-pane" id="comm-whatsapp">
            <form id="commWaForm" method="post" action="{{ url('leads/'.$lead->id.'/communicate/whatsapp') }}">
                @csrf
                
                <div class="mb-2">
                    <label class="form-label">Template</label>
                    <select id="waTemplateSelect" name="wa_template" class="form-select">
                        <option value="">Select Template</option>
                    </select>
                </div>
                <div class="mb-2">
                    <label class="form-label">Message Text</label>
                    <textarea name="wa_body" id="waBody"  rows="4" class="form-control" placeholder="Enter WhatsApp message..."></textarea>
                </div>
                <div class="d-flex justify-content-end gap-2">
                    <button type="button" class="btn btn-light" data-bs-dismiss="offcanvas">Close</button>
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
           <button type="button" class="btn btn-light" data-bs-dismiss="offcanvas">Close</button>
            <button type="submit" class="btn btn-success">Send</button>
        </div>
    </form>
  </div>
</div>


<div class="offcanvas offcanvas-end" tabindex="-1" id="ticketCanvas">
  <div class="offcanvas-header">
    <h5 id="ticketTitle">Ticket</h5>
    <button class="btn-close" data-bs-dismiss="offcanvas"></button>
  </div>
  <div class="offcanvas-body" id="ticketCanvasBody">
    Loading...
  </div>
</div>
<div class="offcanvas offcanvas-end" tabindex="-1" id="createTicketCanvas">
  <div class="offcanvas-header">
    <h5>Create Ticket</h5>
    <button class="btn-close" data-bs-dismiss="offcanvas"></button>
  </div>
  <div class="offcanvas-body">
    <form id="createTicketForm">
      @csrf
      <input type="text" name="subject" class="form-control mb-2" placeholder="Subject" required>
      <select name="category" class="form-select mb-2">
        <option value="general">General</option>
        <option value="payment">Payment</option>
        <option value="technical">Technical</option>
        <option value="academic">Academic</option>
      </select>
      <textarea name="message" class="form-control mb-2" placeholder="Message" required></textarea>
      <button class="btn btn-primary w-100">Create Ticket</button>
    </form>
  </div>
</div>



@endsection

@section('script')
<script src="{{ URL::asset('build/libs/list.js/list.min.js') }}"></script>
<script src="{{ URL::asset('build/libs/sweetalert2/sweetalert2.min.js') }}"></script>
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.tiny.cloud/1/wh3ibzcxh5pbxxmvikxjhqqjcdv2dwc55t132iss53se0kuz/tinymce/6/tinymce.min.js"></script>
<script>



/* ---------------- TICKET SYSTEM (INLINE) ---------------- */

function loadTickets() {
  $.get("{{ url('leads/'.$lead->id.'/tickets') }}", function(res){
    let html = '';
    if(res.length){
      res.forEach(t=>{
        html += `
          <div class="border rounded p-2 mb-2">
            <b>${t.subject}</b>
            <div class="small text-muted">${t.category} | ${t.status}</div>
            <button class="btn btn-sm btn-light mt-1" onclick="openTicket(${t.id})">Open</button>
          </div>
        `;
      });
    } else {
      html = '<div class="text-muted text-center">No tickets found</div>';
    }
    $("#ticketList").html(html);
  });
}

function openTicket(id){
  $.get("{{ url('tickets') }}/"+id, function(res){
    let msgs = '';
    res.messages.forEach(m=>{
      msgs += `
        <div class="mb-2">
          <small>${m.sender_type}</small>
          <div class="p-2 rounded ${m.sender_type=='admin'?'bg-light':'bg-info text-white'}">
            ${m.message}
          </div>
        </div>
      `;
    });

    $("#ticketCanvasBody").html(`
      <h6>${res.ticket.subject}</h6>
      <div class="border p-2 mb-2" style="max-height:280px;overflow:auto">${msgs}</div>
      <form id="replyTicketForm">
        <textarea name="message" class="form-control mb-2" placeholder="Reply..." required></textarea>
        <button class="btn btn-success btn-sm w-100">Send Reply</button>
      </form>
    `);

    $("#replyTicketForm").submit(function(e){
      e.preventDefault();
      $.post("{{ url('tickets/reply') }}/"+id, $(this).serialize(), function(){
        openTicket(id);
        loadTickets();
      });
    });

    new bootstrap.Offcanvas('#ticketCanvas').show();
  });
}

function openCreateTicket(){
  new bootstrap.Offcanvas('#createTicketCanvas').show();
}

$("#createTicketForm").submit(function(e){
  e.preventDefault();
  $.post("{{ url('leads/'.$lead->id.'/tickets/create') }}", $(this).serialize(), function(){
    loadTickets();
    bootstrap.Offcanvas.getInstance(document.getElementById('createTicketCanvas')).hide();
  });
});

/* Auto load tickets when tab opens */
$('a[data-bs-toggle="tab"][href="#tab-tickets"]').on('shown.bs.tab', function () {
  loadTickets();
});





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
 
function loadCurrentStatusSubstatus(leadId) {
    $.ajax({
        url: "{{ url('/api/lead-status-substatus')}}",
        type: "POST",
        data: {
            lead_id: leadId,
            _token: "{{ csrf_token() }}"
        },
        success: function (res) {
            if (res.lead_status_id) {
                $('#lead_status').val(res.lead_status_id);

                let html = '<option value="">Select Sub Stage</option>';
                res.sub_statuses.forEach(row => {
                    let selected = row.id == res.lead_sub_status_id ? 'selected' : '';
                    html += `<option value="${row.id}" ${selected}>${row.name}</option>`;
                });

                $('#lead_sub_status').html(html);
            }
        },
        error: function(err){
            console.log('Load current failed', err);
        }
    });
}

$('#changeStageCanvas').on('shown.bs.offcanvas', function () {
    loadCurrentStatusSubstatus({{ $lead->id }});
});

$(document).on('change', '#lead_status', function () {
    let statusId = $(this).val();

    $('#lead_sub_status').html('<option value="">Loading...</option>');

    if (!statusId) {
        $('#lead_sub_status').html('<option value="">Select Sub Stage</option>');
        return;
    }

    $.ajax({
	url: "{{ url('/api/lead-status-substatus')}}",
        type: "POST",
        data: {
            lead_status_id: statusId,
            _token: "{{ csrf_token() }}"
        },
        success: function (res) {
            let html = '<option value="">Select Sub Stage</option>';
            res.forEach(row => {
                html += `<option value="${row.id}">${row.name}</option>`;
            });
            $('#lead_sub_status').html(html);
        },
        error: function(err){
            console.log('Substatus load failed', err);
        }
    });
});
 

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
let emailTplLoaded = false;
let smsTplLoaded = false;
let waTplLoaded = false;

function loadEmailTemplates(){
    if(emailTplLoaded) return;

    $('#emailTemplateSelect').html('<option>Loading...</option>');

    $.get('{{ url('api/email-templates') }}')
    .done(function(res){
        let html = '<option value="">Select Template</option>';

        if(res.length === 0){
            html += '<option value="">No templates found</option>';
        } else {
            res.forEach(t=>{
                html += `<option value="${t.id}">${t.name}</option>`;
            });
        }

        $('#emailTemplateSelect').html(html);
        emailTplLoaded = true;
    })
    .fail(function(){
        $('#emailTemplateSelect').html('<option>Error loading templates</option>');
    });
}

function loadSmsTemplates(){
    if(smsTplLoaded) return;

    $('#smsTemplateSelect').html('<option>Loading...</option>');

    $.get('{{ url('api/sms-templates') }}')
    .done(function(res){
        let html = '<option value="">Select Template</option>';

        if(res.length === 0){
            html += '<option value="">No templates found</option>';
        } else {
            res.forEach(t=>{
                html += `<option value="${t.id}">${t.name}</option>`;
            });
        }

        $('#smsTemplateSelect').html(html);
        smsTplLoaded = true;
    })
    .fail(function(){
        $('#smsTemplateSelect').html('<option>Error loading templates</option>');
    });
}

function loadWhatsappTemplates(){
    if(waTplLoaded) return;

    $('#waTemplateSelect').html('<option>Loading...</option>');

    $.get('{{ url('api/whatsapp-templates') }}')
    .done(function(res){
        let html = '<option value="">Select Template</option>';

        if(res.length === 0){
            html += '<option value="">No templates found</option>';
        } else {
            res.forEach(t=>{
                html += `<option value="${t.id}">${t.name}</option>`;
            });
        }

        $('#waTemplateSelect').html(html);
        waTplLoaded = true;
    })
    .fail(function(){
        $('#waTemplateSelect').html('<option>Error loading templates</option>');
    });
}
document.getElementById('communicationCanvas')
  .addEventListener('shown.bs.offcanvas', function () {
      loadEmailTemplates();
      loadSmsTemplates();
      loadWhatsappTemplates();
});
$('#emailTemplateSelect').change(function(){
    let id = $(this).val();
    if(!id) return;

    $.get('{{ url("api/template-by-id") }}', {
        id:id,
        type:'email'
    }, function(res){
        $('#emailSubject').val(res.subject || res.title);
      //  $('#emailBody').html(res.message || res.message);
	    tinymce.get('emailBody').setContent(res.message);
    });
});
$('#smsTemplateSelect').change(function(){
    let id = $(this).val();
    if(!id) return;

    $.get('{{ url("api/template-by-id") }}', {
        id:id,
        type:'sms'
    }, function(res){
        $('#smsBody').val(res.message || res.message);
    });
});
$('#waTemplateSelect').change(function(){
    let id = $(this).val();
    if(!id) return;

    $.get('{{ url("api/template-by-id") }}', {
        id:id,
        type:'whatsapp'
    }, function(res){
        $('#waBody').val(res.message || res.message);
    });
});

</script>
<script>
tinymce.init({
  selector: '#emailBody',
  height: 300,
 plugins: 'link image code lists',
toolbar: 'undo redo | bold italic | alignleft aligncenter alignright | bullist numlist | link image | code',
image_advtab: true,
extended_valid_elements: "img[class|src|alt|title|width|height|style]",
   menubar: false,
	automatic_uploads: true,
  image_title: true,
  image_description: true,

  // ye important hai
  image_dimensions: true,
  
    // optional: default class remove
  image_class_list: [
    {title: 'None', value: ''},
    {title: 'Responsive', value: 'img-fluid'}
  ],
  
  
  images_upload_handler: function (blobInfo, progress) {
    return new Promise(function (resolve, reject) {

      let xhr = new XMLHttpRequest();
      xhr.open('POST', "{{ url('tinymce/upload-image') }}");

      xhr.setRequestHeader('X-CSRF-TOKEN',
        document.querySelector('meta[name="csrf-token"]').getAttribute('content')
      );

      xhr.setRequestHeader('X-Requested-With', 'XMLHttpRequest');

      xhr.onload = function () {
        if (xhr.status != 200) {
          reject('HTTP Error: ' + xhr.status);
          return;
        }

        let json = JSON.parse(xhr.responseText);

        if (!json || !json.location) {
          reject('Invalid response');
          return;
        }

        resolve(json.location);
      };

      xhr.onerror = function () {
        reject('Upload failed');
      };

      let formData = new FormData();
      formData.append('file', blobInfo.blob(), blobInfo.filename());

      xhr.send(formData);
    });
  },

  file_picker_types: 'file image',
  file_picker_callback: function (cb, value, meta) {

    let input = document.createElement('input');
    input.type = 'file';

    if (meta.filetype === 'image') {
      input.accept = 'image/*';
    } else {
      input.accept = '.pdf,.doc,.docx,.xls,.xlsx';
    }

    input.onchange = function () {

      let file = this.files[0];
      let formData = new FormData();
      formData.append('file', file);

      fetch("{{ url('tinymce/upload-file') }}", {
        method: 'POST',
        headers: {
          'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
          'X-Requested-With': 'XMLHttpRequest'
        },
        body: formData
      })
      .then(res => res.json())
      .then(data => {
        cb(data.location, { text: file.name });
      })
      .catch(() => {
        alert('Upload failed');
      });
    };

    input.click();
  }
});
</script>


<script>
document.getElementById('startAt').addEventListener('change', function () {
    let start = new Date(this.value);
    if (!isNaN(start.getTime())) {
        start.setMinutes(start.getMinutes() + 15);

        let year = start.getFullYear();
        let month = String(start.getMonth() + 1).padStart(2, '0');
        let day = String(start.getDate()).padStart(2, '0');
        let hours = String(start.getHours()).padStart(2, '0');
        let minutes = String(start.getMinutes()).padStart(2, '0');

        document.getElementById('endAt').value = `${year}-${month}-${day}T${hours}:${minutes}`;
    }
});








$("#btnCallNow").on("click", function(){

    let leadId = $(this).data("lead-id");

    Swal.fire({
        title: "Start Call?",
        text: "The agent will receive the call first, and the student will be connected as soon as he picks up.",
        icon: "question",
        showCancelButton: true,
        confirmButtonText: "Yes, Call",
        cancelButtonText: "Cancel"
    }).then((result) => {

        if(!result.isConfirmed) return;

        $.ajax({
            url: "{{ url('leads') }}/" + leadId + "/call",
            type: "POST",
            data: {
                _token: "{{ csrf_token() }}"
            },
            success: function(res){
                Swal.fire("Done!", res.message, "success");
            },
            error: function(xhr){
                Swal.fire("Failed!", xhr.responseJSON?.message ?? "Call failed", "error");
            }
        });

    });

});



</script>
<script>
document.getElementById('btnApplyCoupon').addEventListener('click', function () {

    let coupon = document.getElementById('coupon_code').value.trim();
    let total  = parseFloat(document.getElementById('payment_amount').value);

    if(!coupon){
        alert("Enter coupon code");
        return;
    }

    $.ajax({
        url: "{{ route('leads.payment.apply_coupon', $lead->id) }}",
        type: "POST",
        data: {
            _token: "{{ csrf_token() }}",
            coupon_code: coupon,
            total_amount: total
        },
        success: function(res){
            if(!res.status){
                alert(res.message);
                return;
            }
				$("#coupon_id").val(res.coupon_id);
            $("#discount_amount").text(parseFloat(res.discount_amount).toFixed(2));
            $("#final_amount").val(parseFloat(res.final_amount).toFixed(2));
        }
    });
});
</script>
<script>
$("#leadPaymentForm").submit(function(e){
    e.preventDefault();

    $.ajax({
        url: "{{ route('leads.payment.save', $lead->id) }}",
        type: "POST",
        data: $(this).serialize(),
        success: function(res){
            alert(res.message);
            if(res.status){
                location.reload();
            }
        }
    });
});



</script>
<script>
document.addEventListener("DOMContentLoaded", function () {

    let leadAssign = "{{ $lead->lead_assign_to ?? 0 }}";

    if(!leadAssign || leadAssign == 0){

        let modalEl = document.getElementById('reassignModal');

        // Title change
        let title = modalEl.querySelector('.modal-title');
        if(title){
            title.innerText = "Lead Not Assigned - Please Assign Owner";
            title.classList.add("text-danger");
        }

        // Submit button text change
        let submitBtn = modalEl.querySelector('button[type="submit"]');
        if(submitBtn){
            submitBtn.innerText = "Assign Lead";
            submitBtn.classList.remove("btn-primary");
            submitBtn.classList.add("btn-success");
        }

        // Cancel button hide
        modalEl.querySelectorAll('[data-bs-dismiss="modal"]').forEach(el=>{
            el.style.display = "none";
        });

        // Close icon hide
        modalEl.querySelectorAll('.btn-close').forEach(el=>{
            el.style.display = "none";
        });

        let modal = new bootstrap.Modal(modalEl,{
            backdrop: 'static',
            keyboard: false
        });

        modal.show();
    }

});
</script>

@endsection
