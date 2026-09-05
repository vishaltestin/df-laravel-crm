@extends('FrontEnd.layouts.master')
@section('title', 'Leads')
@section('css')
<link href="{{ URL::asset('build/libs/sweetalert2/sweetalert2.min.css') }}" rel="stylesheet">
<style> 
.text-truncate-10 {
    display: inline-block;
    max-width: 10ch;      /* only 10 characters */
    overflow: hidden;
    white-space: nowrap;
    text-overflow: ellipsis;
    cursor: pointer;
}
 .table-responsive {
    overflow-x: auto !important;
    -webkit-overflow-scrolling: touch;
}#customerTable {
    white-space: nowrap;
}

.lead-border-left {
    border-left: 4px solid transparent;
    background-image: linear-gradient(to bottom, #ff4d4d, #b30000);
    background-repeat: no-repeat;
    background-size: 4px 100%;
    background-position: left center;
    padding-left: 2px;
    transition: 0.3s ease;
}
.offcanvas-body {
    overflow-y: auto;
}.offcanvas-body {
    max-height: calc(100vh - 120px);
    overflow-y: auto;
}
#quickLeadCanvas {
    height: 100vh;
}

#quickLeadCanvas .offcanvas-body {
    overflow-y: auto;
    height: calc(100vh - 60px);
}

.lead-border-left:hover {
    background-size: 6px 100%;
 }
</style>
@endsection
 
@section('content')
    @component('FrontEnd.components.breadcrumb')
        @slot('li_1')
            CRM
        @endslot
        @slot('title')
            Leads
        @endslot
    @endcomponent

     <div class="row">
        <div class="col-lg-12">
            <div class="card" id="leadsList">
                <div class="card-header border-0">

                    <div class="row g-4 align-items-center">
                        <div class="col-sm-3">
                            <div class="search-box">
                                <input type="text" class="form-control search"
                                    placeholder="Search for...">
                                <i class="ri-search-line search-icon"></i>
								
								
								
								
                            </div>
                        </div>
						<div class="col-sm-4">
        <div class="d-flex gap-2 align-items-center">

            <select class="form-select" style="max-width:280px"
                onchange="if(this.value){ window.location='{{ url()->current() }}?saved_filter_id='+this.value }">
                <option value="">-- Saved Filters --</option>

                @foreach($savedFilters as $sf)
                    <option value="{{ $sf->id }}"
                        {{ request('saved_filter_id') == $sf->id ? 'selected' : '' }}>
                        {{ $sf->name }} {{ $sf->is_default ? '(Default)' : '' }}
                    </option>
                @endforeach
            </select>
 
        </div>
    </div>

    
						
						
						
                        <div class="col-sm-auto ms-auto">
                            <div class="hstack gap-2">
								<div id="selectionInfo" class="alert alert-info py-1 px-3 mb-2" style="display:none;">
								Selected Leads: <strong><span id="selectedCount">0</span></strong>
								</div>

                            <button class="btn btn-info"
        data-bs-toggle="modal"
        data-bs-target="#leadApiDocModal">
    <i class="ri-file-text-line me-1"></i>   API Docs
</button>
                                <button type="button" class="btn btn-info" data-bs-toggle="offcanvas"
                                    href="#offcanvasExample"><i class="ri-filter-3-line align-bottom me-1"></i> Fliters</button>
								<button 
									type="button" 
									class="btn btn-success add-btn"
									data-bs-toggle="offcanvas"
									data-bs-target="#quickLeadCanvas">

									<i class="ri-add-line align-bottom me-1"></i> Add Leads
								</button>
							<div class="modal fade" id="uploadTypeModal" tabindex="-1">
  <div class="modal-dialog">
    <div class="modal-content">

      <div class="modal-header">
        <h5 class="modal-title">Select Primary Upload Type</h5>
        <!-- Top Close Icon -->
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>

      <div class="modal-body">
        <label class="form-check">
          <input type="radio" name="primary_key" value="email" checked>
          Email (Unique)
        </label>

        <label class="form-check mt-2">
          <input type="radio" name="primary_key" value="mobile">
          Mobile (Unique)
        </label>
      </div>

      <div class="modal-footer">
        <!-- Cancel Button -->
        <button type="button" class="btn btn-light" data-bs-dismiss="modal">
          Cancel
        </button>

        <button type="button" class="btn btn-primary" id="openInstructions">
          Continue
        </button>
      </div>

    </div>
  </div>
</div>
                         <span class="dropdown">
                                    <button class="btn btn-soft-info btn-icon fs-14" type="button"
                                        id="dropdownMenuButton1" data-bs-toggle="dropdown"
                                        aria-expanded="false">
                                        <i class="ri-settings-4-line"></i>
                                    </button>
                                    <ul class="dropdown-menu" aria-labelledby="dropdownMenuButton1">
                        <li><a onclick="openBulkDownload()" class="dropdown-item">Download Leads</a></li>
<li><a onclick="openBulkCommunicate()" class="dropdown-item">Communicate</a></li>
<li><a onclick="openBulkEvent()" class="dropdown-item">Event</a></li>
<li><a onclick="openBulkFacebook()" class="dropdown-item">Send to Facebook</a></li>
<li><a onclick="openBulkGoogle()" class="dropdown-item">Send to Google Ads</a></li>
<li><a onclick="openBulkReassign()" class="dropdown-item">Re-assign Leads</a></li>
<li><a onclick="openBulkStage()" class="dropdown-item">Change Lead Stage</a></li>
<li><a onclick="openBulkUpdate()" class="dropdown-item">Data Update</a></li>
<li><a onclick="openBulkDelete()" class="dropdown-item text-danger">Delete</a></li>
<li><a onclick="openBulkPipeline()" class="dropdown-item">Move to Pipeline</a></li>
 <li>
  <a href="javascript:void(0)" class="dropdown-item" onclick="openColumnModal()">
    Choose Columns
  </a>
</li>
                                   </ul>
                                </span> 
                            </div>
                        </div>
                    </div>
                </div>
        
  
   
					<div class="card-body">
                    <div>
                        <div class="table-responsive table-card">
                            <table class="table align-middle" id="customerTable">
                                <thead class="table-light">
                                    <tr>
                                         <th scope="col" style="width: 50px;">
                                            <div class="form-check">
                                                <input class="form-check-input" type="checkbox"
                                                    id="checkAll" value="option">
                                            </div>
                                        </th> 
                                         <th class=""></th>
                                       
{{-- DYNAMIC COLUMNS --}}
@if(in_array('name', $selectedColumns))
    <th class="sort" data-sort="name">Name</th>
@endif

@if(in_array('mobile', $selectedColumns))
    <th class="sort" data-sort="mobile">Phone</th>
@endif

@if(in_array('alternate_mobile', $selectedColumns))
    <th class="sort" data-sort="alternate_mobile">Alt Phone</th>
@endif

@if(in_array('email', $selectedColumns))
    <th class="sort" data-sort="email">Email</th>
@endif

@if(in_array('city', $selectedColumns))
    <th class="sort" data-sort="city">City</th>
@endif

@if(in_array('state', $selectedColumns))
    <th class="sort" data-sort="state">State</th>
@endif

@if(in_array('country', $selectedColumns))
    <th class="sort" data-sort="country">Country</th>
@endif

@if(in_array('organization_id', $selectedColumns) && in_array($currentRoleSlug, ['root','sadmin']))
    <th class="sort" data-sort="organization_id">Organization</th>
@endif

@if(in_array('institute_id', $selectedColumns))
    <th class="sort" data-sort="institute_id">Institute</th>
@endif

@if(in_array('programme_id', $selectedColumns))
    <th class="sort" data-sort="programme_id">Program</th>
@endif

@if(in_array('faculty_id', $selectedColumns))
    <th class="sort" data-sort="faculty_id">Course</th>
@endif

@if(in_array('application_form_id', $selectedColumns))
    <th class="sort" data-sort="application_form_id">Application Form</th>
@endif

@if(in_array('lead_source_id', $selectedColumns))
    <th class="sort" data-sort="lead_source_id">Source</th>
@endif

@if(in_array('lead_campaign_id', $selectedColumns))
    <th class="sort" data-sort="lead_campaign_id">Campaign</th>
@endif

@if(in_array('lead_marketing_event', $selectedColumns))
    <th class="sort" data-sort="lead_marketing_event">Event</th>
@endif

@if(in_array('type_id', $selectedColumns))
    <th class="sort" data-sort="type_id">Type</th>
@endif

@if(in_array('lead_status_id', $selectedColumns))
    <th class="sort" data-sort="lead_status_id">Status</th>
@endif

@if(in_array('lead_sub_status_id', $selectedColumns))
    <th class="sort" data-sort="lead_sub_status_id">Sub Status</th>
@endif

@if(in_array('pipeline_id', $selectedColumns))
    <th class="sort" data-sort="pipeline_id">Pipeline</th>
@endif

@if(in_array('stage_id', $selectedColumns))
    <th class="sort" data-sort="stage_id">Stage</th>
@endif

@if(in_array('lead_score', $selectedColumns))
    <th class="sort" data-sort="lead_score">Score</th>
@endif

@if(in_array('lead_weight_count', $selectedColumns))
    <th class="sort" data-sort="lead_weight_count">Weight</th>
@endif

@if(in_array('lead_assign_to', $selectedColumns))
    <th class="sort" data-sort="lead_assign_to">Assign To</th>
@endif

@if(in_array('created_by', $selectedColumns))
    <th class="sort" data-sort="created_by">Created By</th>
@endif

@if(in_array('next_followup_at', $selectedColumns))
    <th class="sort" data-sort="next_followup_at">Next Followup</th>
@endif

@if(in_array('lead_registration_date', $selectedColumns))
    <th class="sort" data-sort="lead_registration_date">Registration Date</th>
@endif

@if(in_array('created_at', $selectedColumns))
    <th class="sort" data-sort="created_at">Created</th>
@endif

@if(in_array('updated_at', $selectedColumns))
    <th class="sort" data-sort="updated_at">Updated</th>
@endif

 										<th  ></th>
                                     </tr>
								</thead>
                                <tbody class="list form-check-all">
								
								    @forelse($leads as $lead)
									<tr>

    {{-- FIXED: checkbox --}}
    <th scope="row">
        <div class="form-check">
            <input class="form-check-input lead-checkbox"
                   type="checkbox"
                   name="chk_child[]"
                   value="{{ $lead->id }}">
        </div>
    </th>

    {{-- FIXED: duplicate badge / lead border --}}
    @if($lead->lead_assign_to > 0)
        <td data-bs-toggle="tooltip" data-bs-placement="right" title="{{ $lead->assigned_user_name }} Lead">
    @else
        <td class="lead-border-left" data-bs-toggle="tooltip" data-bs-placement="right" title="Untouched Lead">
    @endif

        @php
           $duplicates = \App\Lead::where('mobile', $lead->getRawOriginal('mobile'))
                ->where('id', '!=', $lead->id)
                ->orderBy('id', 'DESC')
                ->get();

            if($duplicates->count() == 0){
                $label = 'P';
                $color = 'bg-warning';
                $tooltip = 'Primary lead (first unique entry)';
            }
            elseif ($duplicates->count()== 1) {
                $label = 'S';
                $color = 'bg-info';
                $tooltip = 'Secondary lead (duplicate 1 time)';
            }
            else {
                $label = 'T';
                $color = 'bg-danger';
                $tooltip = 'Tertiary lead (duplicate 2 or more times)';
            }
			
			
        @endphp

        @if($duplicates->count() > 0)
            <span class="badge {{ $color }} rounded-pill"
                  data-bs-toggle="tooltip"
                  data-bs-placement="top"
                  title="{{ $tooltip }}">
                {{ $label }}
            </span>

            <span data-bs-toggle="modal" data-bs-target="#dupModal{{ $lead->id }}">
                <u>{{ $duplicates->count() }}</u>
            </span>

            {{-- Duplicate Modal --}}
            <div class="modal fade" id="dupModal{{ $lead->id }}" tabindex="-1">
              <div class="modal-dialog modal-lg">
                <div class="modal-content">
                  <div class="modal-header bg-light">
                    <h5 class="modal-title">Duplicates for {{ $lead->mobile }}</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                  </div>
                  <div class="modal-body">
                    <table class="table table-bordered">
                        <thead>
                            <tr>
                                <th>Name</th>
                                <th>Email</th>
                                <th>Campaign</th>
                                <th>Stage</th>
                                <th>Created</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($duplicates as $dup)
                            <tr>
                                <td>{{ $dup->name }}</td>
                                <td>{{ $dup->email }}</td>
                                <td>{{ $dup->lead_campaign_name ?? '-' }}</td>
                                <td>{{ $dup->stage_name ?? '-' }}</td>
                                <td>{{ optional($dup->created_at)->format('d M Y H:i:s') }}</td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                  </div>
                </div>
              </div>
            </div>
        @else
            <span class="badge {{ $color }} rounded-pill"
                  data-bs-toggle="tooltip"
                  data-bs-placement="top"
                  title="{{ $tooltip }}">
                {{ $label }}
            </span>
        @endif
    </td>


    {{-- DYNAMIC FIELDS --}}
    @if(in_array('name', $selectedColumns))
        <td class="name">
            <a href="leads/{{ encrypt($lead->id) }}">
                <span class="text-truncate-10" data-bs-toggle="tooltip" title="{{ $lead->name }}">
                    {{ $lead->name }} <i class="ri-arrow-right-s-line"></i>
                </span>
            </a>
        </td>
    @endif

    @if(in_array('mobile', $selectedColumns))
        <td class="mobile">{{ $lead->mobile }}</td>
    @endif

    @if(in_array('alternate_mobile', $selectedColumns))
        <td class="alternate_mobile">{{ $lead->alternate_mobile ?? '-' }}</td>
    @endif

    @if(in_array('email', $selectedColumns))
        <td class="email">{{ $lead->email }}</td>
    @endif

    @if(in_array('city', $selectedColumns))
        <td class="city">{{ $lead->city_name ?? '-' }}</td>
    @endif

    @if(in_array('state', $selectedColumns))
        <td class="state">{{ $lead->state_name ?? '-' }}</td>
    @endif

    @if(in_array('country', $selectedColumns))
        <td class="country">{{ $lead->country ?? '-' }}</td>
    @endif

    @if(in_array('organization_id', $selectedColumns) && in_array($currentRoleSlug, ['root','sadmin']))
        <td class="organization_id">{{ $lead->organization_name ?? '-' }}</td>
    @endif

    @if(in_array('institute_id', $selectedColumns))
        <td class="institute_id">{{ $lead->institute_name ?? '-' }}</td>
    @endif

    @if(in_array('programme_id', $selectedColumns))
        <td class="programme_id">{{ $lead->programme_name ?? '-' }}</td>
    @endif

    @if(in_array('faculty_id', $selectedColumns))
        <td class="faculty_id">{{ $lead->faculty_name ?? '-' }}</td>
    @endif

    @if(in_array('application_form_id', $selectedColumns))
        <td class="application_form_id">
            {{ $lead->application_form_id ?? '-' }}
        </td>
    @endif

    @if(in_array('lead_source_id', $selectedColumns))
        <td class="lead_source_id">{{ $lead->lead_source_name ?? '-' }}</td>
    @endif

    @if(in_array('lead_campaign_id', $selectedColumns))
        <td class="lead_campaign_id">{{ $lead->lead_campaign_name ?? '-' }}</td>
    @endif

    @if(in_array('lead_marketing_event', $selectedColumns))
        <td class="lead_marketing_event">{{ $lead->lead_marketing_event ?? '-' }}</td>
    @endif

    @if(in_array('type_id', $selectedColumns))
        <td class="type_id">{{ $lead->lead_type_name ?? '-' }}</td>
    @endif

    @if(in_array('lead_status_id', $selectedColumns))
        <td class="lead_status_id">{{ $lead->lead_status_id ?? '-' }}</td>
    @endif

    @if(in_array('lead_sub_status_id', $selectedColumns))
        <td class="lead_sub_status_id">{{ $lead->lead_sub_status_id ?? '-' }}</td>
    @endif

    @if(in_array('pipeline_id', $selectedColumns))
        <td class="pipeline_id">{{ $lead->pipeline_name ?? '-' }}</td>
    @endif

    @if(in_array('stage_id', $selectedColumns))
        <td class="stage_id">{{ $lead->stage_name ?? '-' }}</td>
    @endif

    @if(in_array('lead_score', $selectedColumns))
        <td class="lead_score">{{ $lead->lead_score ?? 0 }}</td>
    @endif

    @if(in_array('lead_weight_count', $selectedColumns))
        <td class="lead_weight_count">{{ $lead->lead_weight_count ?? 0 }}</td>
    @endif

    @if(in_array('lead_assign_to', $selectedColumns))
        <td class="lead_assign_to">
            @if($lead->lead_assign_to > 0)
                {{ $lead->assigned_user_name }}
            @else
                <span class="badge bg-warning">Unassigned</span>
            @endif
        </td>
    @endif

    @if(in_array('created_by', $selectedColumns))
        <td class="created_by">{{ $lead->created_by ?? '-' }}</td>
    @endif

    @if(in_array('next_followup_at', $selectedColumns))
        <td class="next_followup_at">
            {{ $lead->next_followup_at ? \Carbon\Carbon::parse($lead->next_followup_at)->format('d M Y H:i') : '-' }}
        </td>
    @endif

    @if(in_array('lead_registration_date', $selectedColumns))
        <td class="lead_registration_date">
            {{ $lead->lead_registration_date ? \Carbon\Carbon::parse($lead->lead_registration_date)->format('d M Y') : '-' }}
        </td>
    @endif

    @if(in_array('created_at', $selectedColumns))
        <td class="created_at">
            {{ $lead->created_at ? $lead->created_at->format('d M, Y') : '-' }}
        </td>
    @endif

    @if(in_array('updated_at', $selectedColumns))
        <td class="updated_at">
            {{ $lead->updated_at ? $lead->updated_at->format('d M, Y') : '-' }}
        </td>
    @endif


    {{-- FIXED: actions dropdown --}}
    <td>
        <div class="dropdown">
            <button class="btn btn-link text-muted p-1 py-0 fs-15" data-bs-toggle="dropdown">
                <i data-feather="more-horizontal" class="icon-sm"></i>
            </button>

            <div class="dropdown-menu dropdown-menu-end">

                <a class="dropdown-item" href="javascript:void(0)"
                   onclick="openLeadAction({{ $lead->id }}, 'communicate')">
                    <i class="ri-chat-1-line me-2"></i> Communicate
                </a>

                <a class="dropdown-item" href="javascript:void(0)"
                   onclick="openLeadAction({{ $lead->id }}, 'stage')">
                    <i class="ri-exchange-dollar-line me-2"></i> Change Lead Stage
                </a>

                <a class="dropdown-item" href="javascript:void(0)"
                   onclick="openLeadAction({{ $lead->id }}, 'reassign')">
                    <i class="ri-user-shared-line me-2"></i> Re-assign Lead
                </a>

                <a class="dropdown-item" href="javascript:void(0)"
                   onclick="openLeadAction({{ $lead->id }}, 'event')">
                    <i class="ri-calendar-event-line me-2"></i> Add Event
                </a>

                <a class="dropdown-item" href="javascript:void(0)"
                   onclick="openLeadAction({{ $lead->id }}, 'note')">
                    <i class="ri-sticky-note-line me-2"></i> Add Note
                </a>

                <a class="dropdown-item" href="leads/{{ encrypt($lead->id) }}">
                    <i class="ri-user-line me-2"></i> View Lead (Quick)
                </a>

                @if(in_array($currentRoleSlug, ['root','sadmin','admin','manager']))
                    <div class="dropdown-divider"></div>
                    <a class="dropdown-item text-danger" href="javascript:void(0)"
                       onclick="openLeadAction({{ $lead->id }}, 'delete')">
                        <i class="ri-delete-bin-6-line me-2"></i> Delete
                    </a>
                @endif
            </div>
        </div>
    </td>

</tr>

                        @empty

                            <tr>
                                <td colspan="11" class="text-center text-muted">No leads found.</td>
                            </tr>

                        @endforelse
							
						
								 
                                </tbody>
                            </table>
                     <div class="noresult" style="display: none">
                                <div class="text-center">
                                    <lord-icon src="https://cdn.lordicon.com/msoeawqm.json"
                                        trigger="loop" colors="primary:#121331,secondary:#08a88a"
                                        style="width:75px;height:75px">
                                    </lord-icon>
                                    <h5 class="mt-2">Sorry! No Result Found</h5>
                                    <p class="text-muted mb-0">We've searched more than 150+ leads We
                                        did not find any
                                        leads for you search.</p>
                                </div>
                            </div>
                        </div>
                        <div class="d-flex justify-content-end mt-3">
                            <div class="pagination-wrap hstack gap-2">
                                <a class="page-item pagination-prev disabled" href="#">
                                    Previous
                                </a>
                                <ul class="pagination listjs-pagination mb-0"></ul>
                                <a class="page-item pagination-next" href="#">
                                    Next
                                </a>
                            </div>
                        </div>
                   


				   </div>
          
								<div class="offcanvas offcanvas-end" tabindex="-1" id="quickLeadCanvas">
    <div class="offcanvas-header">
        <h5 class="offcanvas-title">Add Quick Lead</h5>
        <button type="button" class="btn-close" data-bs-dismiss="offcanvas"></button>
    </div>

    <form method="POST" action="{{ route('Leads.QuickStore') }}" id="quickLeadForm">
        @csrf
@php
    $user = \Sentinel::check();
    $orgId = $user ? registeruser($user->id)->organization : null;
@endphp
     <input type="hidden" name="organization_id" value="{{ $orgId }}">
    <input type="hidden" name="created_by" value="{{ $user->id ?? 0 }}">      <div class="offcanvas-body">

            {{-- TABS --}}
            <ul class="nav nav-tabs mb-3" id="quickLeadTabs">
                <li class="nav-item">
                    <a class="nav-link active" data-mode="email" href="#">Email</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" data-mode="whatsapp" href="#">WhatsApp</a>
                </li>
				<li>
				<a href="javascript:void(0)"
								   class="nav-link"
								   data-bs-toggle="modal"
								   data-bs-target="#uploadTypeModal">
								   <i class="ri-upload-2-line"></i> Upload Leads
								</a>
								
								<li>
            </ul>

            <input type="hidden" name="upload_via" id="uploadVia" value="email">

            {{-- NAME --}}
            <div class="mb-3">
                <input type="text" name="name" class="form-control" placeholder="Enter Name *" required>
            </div>

            {{-- MOBILE --}}
            <div class="mb-3">
                <input type="text" name="mobile" class="form-control" placeholder="Enter Mobile Number *" required>
            </div>

            {{-- EMAIL (Shown only when email tab active) --}}
            <div class="mb-3 email-only">
                <input type="email" name="email" class="form-control" placeholder="Enter Email *">
            </div>

            {{-- STATE --}}
            <div class="mb-3">
                <select name="state" class="form-control" id="ql-state">
                    <option value="">Select State *</option>
                    @foreach($states as $s)
                        <option value="{{ $s->id }}">{{ $s->name }}</option>
                    @endforeach
                </select>
            </div>

            {{-- CITY --}}
            <div class="mb-3">
                <select name="city" class="form-control" id="ql-city">
                    <option value="">Select City *</option>
                </select>
            </div>

                 <div class="mb-3">
    
            {{-- QUALIFICATION --}}
           <select name="institute_id" id="ql-institute" class="form-control">
    <option value="">Select Institute *</option>
    @foreach($institutes as $i)
        <option value="{{ $i->id }}">{{ $i->name }}</option>
    @endforeach
</select>
 </div>

                 <div class="mb-3">

            {{-- FACULTY --}}
          <select name="programme_id" id="ql-program" class="form-control">
    <option value="">Select Program *</option>
</select>

 </div>

                 <div class="mb-3">
            {{-- PROGRAMME --}}
         <select name="faculty_id" id="ql-course" class="form-control">
    <option value="">Select Course *</option>
</select>

 </div>
<div class="mb-3">
    <select name="application_form_id" class="form-control" >
        <option value="">Select Application Form</option>
        @foreach($applicationForms as $form)
            <option value="{{ $form->id }}">
                {{ $form->title }} (₹{{ $form->total_amount }})
            </option>
        @endforeach
    </select>
</div>

             {{-- SOURCE + MEDIUM + CAMPAIGN --}}
            <div class="row mb-3">
                <div class="col">
                    <select name="source" class="form-control">
                        <option value="">Select Source *</option>
                        @foreach($sources as $s)
                            <option value="{{ $s->id }}">{{ $s->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col">
                    <select name="medium" class="form-control">
                        <option value="">Select Medium *</option>
                        @foreach($types as $m)
                            <option value="{{ $m->id }}">{{ $m->name }}</option>
                        @endforeach
                    </select>
                </div>
                </div>
				            <div class="row mb-3">

                <div class="col">
                    <select name="campaign" class="form-control">
                        <option value="">Select Campaign </option>
                        @foreach($campaigns as $c)
                            <option value="{{ $c->id }}">{{ $c->name }}</option>
                        @endforeach
                    </select>
                </div>
				
				{{-- EVENT --}}
                <div class="col">
    <select name="lead_marketing_event" class="form-control">
        <option value="">Select Event </option>
        @foreach($events as $e)
            <option value="{{ $e->id }}">{{ $e->name }}</option>
        @endforeach
    </select>
</div>

            </div>

            {{-- ASSIGN TO OWNER --}}
            <div class="mb-3">
                <label class="form-label">Assign To Owner</label>
              @php
    $user = \Sentinel::check();   // logged-in user
@endphp

<input type="text" disabled class="form-control" value="{{ $user->first_name }}">
<input type="hidden" name="lead_assign_to" value="{{ $user->id }}">

            </div>

        </div>

        {{-- FOOTER BUTTONS --}}
        <div class="offcanvas-footer p-3 border-top d-flex justify-content-between">
            <button type="button" class="btn btn-light" data-bs-dismiss="offcanvas">Cancel</button>
<button type="submit" class="btn btn-success w-50">
    Save Lead
</button>

        </div>

    </form>
</div>

							 

                    <!-- Modal -->
                    <div class="modal fade zoomIn" id="deleteRecordModal" tabindex="-1"
                        aria-labelledby="deleteRecordLabel" aria-hidden="true">
                        <div class="modal-dialog modal-dialog-centered">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <button type="button" class="btn-close" data-bs-dismiss="modal"
                                        aria-label="Close" id="btn-close"></button>
                                </div>
                                <div class="modal-body p-5 text-center">
                                    <lord-icon src="https://cdn.lordicon.com/gsqxdxog.json"
                                        trigger="loop" colors="primary:#405189,secondary:#f06548"
                                        style="width:90px;height:90px"></lord-icon>
                                    <div class="mt-4 text-center">
                                        <h4 class="fs-semibold">You are about to delete a lead ?</h4>
                                        <p class="text-muted fs-14 mb-4 pt-1">Deleting your lead will
                                            remove all of your information from our database.</p>
                                        <div class="hstack gap-2 justify-content-center remove">
                                            <button
                                                class="btn btn-link link-success fw-medium text-decoration-none"
                                                data-bs-dismiss="modal" id="deleteRecord-close"><i
                                                    class="ri-close-line me-1 align-middle"></i>
                                                Close</button>
                                            <button class="btn btn-danger" id="delete-record">Yes,
                                                Delete It!!</button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <!--end modal -->

<div class="offcanvas offcanvas-end" tabindex="-1" id="offcanvasExample">
    <div class="offcanvas-header bg-light">
        <h5 class="offcanvas-title">Lead Filters</h5>
        <button type="button" class="btn-close" data-bs-dismiss="offcanvas"></button>
    </div>

    <form method="GET" action="{{ route('Leads.ListView') }}" class="d-flex flex-column h-100">

        <input type="hidden" name="filter_open" value="1"> {{-- Canvas reopen flag --}}

        <div class="offcanvas-body">
@if(request('filter_open') == 1){{-- SAVE FILTER BOX --}}
<div class="card border mb-3">
    <div class="card-body p-3">
        <label class="form-label fw-semibold mb-2">Save this filter</label>

        <div class="d-flex gap-2">
            <input type="text" id="filter_name" class="form-control"
                   placeholder="Filter Name (e.g. Today Leads)">

            <button type="button" class="btn btn-primary" onclick="saveFilterNow()">
                Save
            </button>
        </div>

        <small class="text-muted d-block mt-2">
            Current applied filters will be saved.
        </small>
    </div>
</div>
@endif
            {{-- DATE RANGE --}}
            <div class="mb-4">
                <label class="form-label fw-semibold text-muted">Date Range</label>
                <div class="row g-2">
                    <div class="col">
                        <input type="date" name="date_from" class="form-control"
                            value="{{ request('date_from') }}">
                    </div>
                    <div class="col">
                        <input type="date" name="date_to" class="form-control"
                            value="{{ request('date_to') }}">
                    </div>
                </div>
            </div>

            {{-- BASIC INFO --}}
            <div class="mb-4">
                <label class="form-label fw-semibold text-muted">Basic Info</label>
                <input type="text" name="name" class="form-control mb-2" placeholder="Name"
                    value="{{ request('name') }}">
                <input type="text" name="mobile" class="form-control mb-2" placeholder="Mobile"
                    value="{{ request('mobile') }}">
                <input type="email" name="email" class="form-control" placeholder="Email"
                    value="{{ request('email') }}">
            </div>

            {{-- LOCATION --}}
            <div class="mb-4">
                <label class="form-label fw-semibold text-muted">Location</label>

                {{-- COUNTRY --}}
                <select class="form-control mb-2" id="filter-country" name="country">
                    <option value="">Select Country</option>
                    @foreach($countries as $c)
                        <option value="{{ $c->id }}" 
                            {{ request('country') == $c->id ? 'selected' : '' }}>
                            {{ $c->name }}
                        </option>
                    @endforeach
                </select>

                {{-- STATE --}}
                <select class="form-control mb-2" id="filter-state" name="state">
                    <option value="">Select State</option>
                </select>

                {{-- CITY --}}
                <select class="form-control" id="filter-city" name="city">
                    <option value="">Select City</option>
                </select>
            </div>

            {{-- PROGRAMME --}}
            <div class="mb-4">
                <label class="form-label fw-semibold text-muted">Program / Course</label>
                <select class="form-control" name="programmes">
                    <option value="">Select Programme</option>
                    @foreach($products as $p)
                        <option value="{{ $p->id }}"
                            {{ request('programmes') == $p->id ? 'selected' : '' }}>
                            {{ $p->name }}
                        </option>
                    @endforeach
                </select>
            </div>

            {{-- LEAD STATUS --}}
            <div class="mb-4">
                <label class="form-label fw-semibold text-muted">Lead Status</label>

                <select class="form-control mb-2" name="lead_status_id">
                    <option value="">Select Status</option>
                    @foreach($leadStatuses as $ls)
                        <option value="{{ $ls->id }}"
                            {{ request('lead_status_id') == $ls->id ? 'selected' : '' }}>
                            {{ $ls->name }}
                        </option>
                    @endforeach
                </select>

                <select class="form-control" name="lead_sub_status_id">
                    <option value="">Select Sub Status</option>
                    @foreach($leadSubStatuses as $sub)
                        <option value="{{ $sub->id }}"
                            {{ request('lead_sub_status_id') == $sub->id ? 'selected' : '' }}>
                            {{ $sub->name }}
                        </option>
                    @endforeach
                </select>
            </div>

            {{-- PIPELINE + STAGE --}}
            <div class="mb-4">
                <label class="form-label fw-semibold text-muted">Pipeline / Stage</label>

                <select class="form-control mb-2" name="pipeline_id">
                    <option value="">Select Pipeline</option>
                    @foreach($pipelines as $pl)
                        <option value="{{ $pl->id }}"
                            {{ request('pipeline_id') == $pl->id ? 'selected' : '' }}>
                            {{ $pl->name }}
                        </option>
                    @endforeach
                </select>

                <select class="form-control" name="stage_id">
                    <option value="">Select Stage</option>
                    @foreach($stages as $st)
                        <option value="{{ $st->id }}"
                            {{ request('stage_id') == $st->id ? 'selected' : '' }}>
                            {{ $st->name }}
                        </option>
                    @endforeach
                </select>
            </div>

            {{-- ASSIGN TO --}}
            <div class="mb-4">
                <label class="form-label fw-semibold text-muted">Assign To</label>
                <select class="form-control" name="lead_assign_to">
                    <option value="">Select Team Member</option>
                    @foreach($teamMembers as $t)
                        <option value="{{ $t->id }}"
                            {{ request('lead_assign_to') == $t->id ? 'selected' : '' }}>
                            {{ $t->first_name }}
                        </option>
                    @endforeach
                </select>
            </div>

            {{-- SCORE --}}
            <div class="mb-4">
                <label class="form-label fw-semibold text-muted">Lead Score</label>
                <div class="row g-2">
                    <div class="col">
                        <input type="number" name="score_from" class="form-control" placeholder="Min"
                            value="{{ request('score_from') }}">
                    </div>
                    <div class="col">
                        <input type="number" name="score_to" class="form-control" placeholder="Max"
                            value="{{ request('score_to') }}">
                    </div>
                </div>
            </div>

          

        </div>

        <div class="offcanvas-footer border-top p-3 bg-white position-sticky bottom-0">  <div class="hstack gap-2">
                <button type="reset" class="btn btn-light w-50" id="resetFilter">Reset</button>
                <button type="submit" class="btn btn-success w-50">Apply Filters</button>
            </div></div>

    </form>
</div>




            </div>
            </div>

        </div>
            </div>

        </div>
        <!--end col-->
    </div>
    <!--end row-->
 <div class="modal fade" id="columnsModal" tabindex="-1">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">

      <div class="modal-header">
        <h5 class="modal-title">Choose Visible Columns</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>

      <div class="modal-body">

        @php
          $allColumns = [
            'name' => 'Name',
            'mobile' => 'Mobile',
            'alternate_mobile' => 'Alternate Mobile',
            'email' => 'Email',
            'city' => 'City',
            'state' => 'State',
            'country' => 'Country',
            'institute_id' => 'Institute',
            'programme_id' => 'Programme',
            'faculty_id' => 'Faculty',
            'application_form_id' => 'Application Form',
            'lead_source_id' => 'Source',
            'lead_campaign_id' => 'Campaign',
            'lead_marketing_event' => 'Event',
            'type_id' => 'Type',
            'lead_status_id' => 'Lead Status',
            'lead_sub_status_id' => 'Sub Status',
            'lead_score' => 'Lead Score',
            'lead_weight_count' => 'Lead Weight',
            'lead_assign_to' => 'Assign To',
            'created_by' => 'Created By',
            'next_followup_at' => 'Next Followup',
            'lead_registration_date' => 'Registration Date',
            'created_at' => 'Created At',
          ];
        @endphp

        <div class="row">
          @foreach($allColumns as $key => $label)
            <div class="col-md-4 mb-2">
              <label class="form-check">
                <input type="checkbox" class="form-check-input lead-col-checkbox"
                       value="{{ $key }}"
                       {{ in_array($key, $selectedColumns) ? 'checked' : '' }}>
                {{ $label }}
              </label>
            </div>
          @endforeach
        </div>

      </div>

      <div class="modal-footer">
        <button class="btn btn-light" data-bs-dismiss="modal">Close</button>
        <button class="btn btn-primary" onclick="saveColumnsNow()">Save Columns</button>
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

      <form id="addNoteForm" method="post" data-action-template="{{ url('leads/__ID__/notes') }}">
        @csrf
        <div class="modal-body">
            <div class="mb-3">
                <textarea name="note" class="form-control" rows="4" maxlength="1024" placeholder="Note" required></textarea>
                <small class="text-muted">Only 1024 characters allowed.</small>
            </div>

            <hr>
            <h6 class="mb-2">Previous Activities</h6>
           <div id="leadNotesList" class="notes-list text-muted text-center">
				Select a lead to view notes
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
    <form id="changeStageForm" method="post" data-action-template="{{ url('leads/__ID__/change-stage') }}">
      @csrf

      {{-- Lead Stage (these are pipeline stages shown as options already) --}}
       
<input type="hidden" name="stage_id" id="changeStage_stage_id">      
      {{-- Lead Status (this is lead_statuses table) --}}
      <div class="mb-3">
        <label class="form-label">Lead Stage (Status)</label>
        <select name="lead_status_id" id="lead_status" class="form-select" required>
            <option value="">Select Lead Stage</option>
            @foreach($leadStatuses as $ls)
          <option value="{{ $ls->id }}">{{ $ls->name }}</option>
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
     :<b id="changeStageOwner">Unassigned</b>
<input type="hidden" name="owner_id" id="changeStageOwnerId">


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
<div class="offcanvas offcanvas-end" tabindex="-1" id="bulkeventCanvas">
  <div class="offcanvas-header">
    <h5 class="offcanvas-title">Events - Follow-up</h5>
    <button type="button" class="btn-close text-reset" data-bs-dismiss="offcanvas"></button>
  </div>
  <div class="offcanvas-body">
    <form id="addEventForm" method="post" action="{{ url('leads/0/events') }}" data-action-template="{{ url('leads/__ID__/events') }}">
      @csrf
      <div class="mb-3">
        <label class="form-label">Subject *</label>
<input type="text" name="subject" id="eventSubject"  class="form-control"  required value="Follow up">    
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
<option id="eventOwnerOption" value="">Select Owner</option>

            @foreach($teamMembers  as $o)
                <option value="{{ $o->id }}">{{ $o->first_name }} {{ $o->last_name }}</option>
            @endforeach
        </select>
      </div>

      <div class="mb-3">
        <label class="form-label">Organiser *</label>
			<input type="text" name="organiser" class="form-control"  id="eventOrganiser" required>
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
<div class="offcanvas offcanvas-end" tabindex="-1" id="communicationCanvas"  style="width:700px;">
  <div class="offcanvas-header">
    <h5 class="offcanvas-title">  Communication</h5>
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
   <form id="commEmailForm" method="post" action="{{ url('leads/__ID__/communicate/email') }}">

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
    <form id="commSmsForm" method="post" action="{{ url('leads/__ID__/communicate/sms') }}">    
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
            <form id="commWaForm" method="post" action="{{ url('leads/__ID__/communicate/whatsapp') }}">
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

      <form id="reassignForm" method="post" data-action-template="{{ url('leads/__ID__/reassign') }}">
        @csrf
        <div class="modal-body">
            <div class="mb-3">
                <label class="form-label">Re-assigned to *</label>
              <select name="reassign_to" class="form-select" id="reassignUserSelect">
    <option value="">Select counsellor</option>
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
        <div id="waChatList" class="text-muted text-center">Select a lead to load chats</div>
    </div>

    <form id="waReplyForm" method="post" data-action-template="{{ url('leads/__ID__/whatsapp/send') }}">
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

{{-- bulk action  Offcanvas --}}




{{-- bulk action  Offcanvas --}}
 
{{-- bulk Communication Offcanvas --}}
<div class="offcanvas offcanvas-end" tabindex="-1" id="bulkcommunicationCanvas"  style="width:700px;">
  <div class="offcanvas-header">
    <h5 class="offcanvas-title">Single Communication</h5>
    <button type="button" class="btn-close text-reset" data-bs-dismiss="offcanvas"></button>
  </div>
   
  <div class="offcanvas-body">
   <ul class="nav nav-tabs mb-3" role="tablist">
    <li class="nav-item">
        <a class="nav-link active" data-bs-toggle="tab" href="#bulk-comm-email">Email</a>
    </li>
    <li class="nav-item">
        <a class="nav-link" data-bs-toggle="tab" href="#bulk-comm-sms">SMS</a>
    </li>
    <li class="nav-item">
        <a class="nav-link" data-bs-toggle="tab" href="#bulk-comm-whatsapp">WhatsApp</a>
    </li>
</ul>

    <div class="tab-content">
        {{-- EMAIL --}}
<div class="tab-pane fade show active" id="bulk-comm-email">
       <form id="bulkEmailForm" method="post" action="{{ route('leads.bulk.email') }}">
         @csrf
          <input type="hidden" name="ids" id="bulkEmailIds">
        <div class="mb-2">
            <label class="form-label">Template</label>
            <select id="emailTemplateSelectbulk" class="form-select">
                <option value="">Select Template</option>
            </select>
        </div>

        <div class="mb-2">
            <label class="form-label">From Email</label>
            <select name="from_email" class="form-select">
                <option value="">default@company.com</option>
            </select>
        </div>

        

        <div class="mb-2">
            <label class="form-label">Subject</label>
            <input type="text" name="subject" id="emailSubjectbulk" class="form-control" />
        </div>

        <div class="mb-2">
            <label class="form-label">Message</label>
            <textarea name="email_body" id="emailBodybulk" rows="6" class="form-control"></textarea>
        </div>

        <div class="d-flex justify-content-end gap-2">
           <button type="button" class="btn btn-light" data-bs-dismiss="offcanvas">Close</button>
            <button type="submit" class="btn btn-primary">Send Now</button>
        </div>
    </form>
</div>

        {{-- SMS --}}
      <div class="tab-pane fade" id="bulk-comm-sms">
        <form id="bulkSmsForm" method="post" action="{{ route('leads.bulk.sms') }}">

		@csrf
          <input type="hidden" name="ids" id="bulkSmsIds">
        <div class="mb-2">
            <label class="form-label">Template</label>
            <select id="smsTemplateSelectbulk" class="form-select">
                <option value="">Select Template</option>
            </select>
        </div>

      
        <div class="mb-2">
            <label class="form-label">Message Text</label>
            <textarea name="sms_body" id="smsBodybulk" rows="4" class="form-control"></textarea>
        </div>

        <div class="d-flex justify-content-end gap-2">
          <button type="button" class="btn btn-light" data-bs-dismiss="offcanvas">Close</button>
            <button type="submit" class="btn btn-primary">Send SMS</button>
        </div>
    </form>
</div>

        {{-- WHATSAPP --}}
       <div class="tab-pane fade" id="bulk-comm-whatsapp">
        <form id="bulkWaForm" method="post" action="{{ route('leads.bulk.whatsapp') }}">
		@csrf
                     <input type="hidden" name="ids" id="bulkWaIds">
				 
                <div class="mb-2">
                    <label class="form-label">Template</label>
                    <select id="waTemplateSelectbulk" name="wa_template" class="form-select">
                        <option value="">Select Template</option>
                    </select>
                </div>
                <div class="mb-2">
                    <label class="form-label">Message Text</label>
                    <textarea name="wa_body" id="waBodybulk"  rows="4" class="form-control" placeholder="Enter WhatsApp message..."></textarea>
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

{{-- Change Lead Stage Offcanvas --}}
<div class="offcanvas offcanvas-end" tabindex="-1" id="bulkchangeStageCanvas">
  
  <div class="offcanvas-header">
    <h5 class="offcanvas-title">Bulk Change Lead Stage</h5>
    <button class="btn-close" data-bs-dismiss="offcanvas"></button>
  </div>

<form id="bulkStageForm" method="post" action="{{ route('leads.bulk.stage') }}">

    @csrf
    <input type="hidden" name="ids" id="bulkStageIds">

    <div class="offcanvas-body">

      <p id="bulkStageText" class="fw-semibold mb-3">
        You are about to update stage for 0 leads
      </p>

      <div class="mb-3">
        <label class="form-label">Lead Status</label>
        <select name="lead_status_id" class="form-select"  id="lead_statusbulk" >
          <option value="">Select Status</option>
         @foreach($leadStatuses as $ls)
          <option value="{{ $ls->id }}">{{ $ls->name }}</option>
            @endforeach  </select>
      </div>

      <div class="mb-3">
        <label class="form-label">Lead Sub Status</label>
        <select name="lead_sub_status_id" class="form-select"  id="lead_sub_statusbulk" >
          <option value="">Select Sub Status</option>
        </select>
      </div>

    </div>

    <div class="offcanvas-footer border-top p-3 d-flex justify-content-between">
      <button type="submit" class="btn btn-primary">Update Stage</button>
      <button type="button" class="btn btn-light" data-bs-dismiss="offcanvas">
        Cancel
      </button>
    </div>

  </form>

</div>

{{-- bulk Reassign Modal --}}
<div class="modal fade" id="bulkreassignModal" tabindex="-1">
  <div class="modal-dialog modal-md">
    <div class="modal-content">

      <div class="modal-header">
        <h5 class="modal-title">Re-assign Leads</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>

      <form id="bulkReassignForm" method="post" action="{{ route('leads.bulk.reassign') }}">

        @csrf
        <input type="hidden" name="ids" id="bulkReassignIds">

        <div class="modal-body">

          <p id="bulkReassignText" class="fw-semibold mb-3">
            You are about to reassign 0 leads
          </p>

          <div class="mb-3">
            <label class="form-label">Re-assigned to *</label>
            <select name="reassign_to" class="form-select" id="reassignUserSelectbulk">
              <option value="">Select counsellor</option>
            </select>
          </div>

        </div>

        <div class="modal-footer">
          <button type="submit" class="btn btn-primary">Reassign Leads</button>
          <button type="button" class="btn btn-light" data-bs-dismiss="modal">
            Cancel
          </button>
        </div>

      </form>

    </div>
  </div>
</div>


{{-- bulk Download  Modal --}}
<div class="modal fade" id="bulkdownloadModal" tabindex="-1">
  <div class="modal-dialog modal-md">
    <div class="modal-content">

      <div class="modal-header">
        <h5 class="modal-title">Download Lead</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>

     <form id="bulkDownloadForm" method="post" action="{{ route('leads.bulk.download') }}">
        @csrf
        <input type="hidden" name="ids" id="bulkDownloadIds">

        <div class="modal-body">
          <p id="bulkDownloadText" class="fw-semibold">
            You are about to download 0 leads
          </p>
        </div>

        <div class="modal-footer">
          <button type="submit" class="btn btn-success">Download CSV</button>
          <button type="button" class="btn btn-light" data-bs-dismiss="modal">
            Cancel
          </button>
        </div>

      </form>

    </div>
  </div>
</div>


{{-- bulk Delete  Modal --}}
<div class="modal fade" id="bulkDeleteModal" tabindex="-1">
  <div class="modal-dialog modal-md">
    <div class="modal-content">

      <div class="modal-header">
        <h5 class="modal-title text-danger">Delete Leads</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>

      <form id="bulkDeleteForm" method="post" action="{{ route('leads.bulk.delete') }}">

        @csrf
        <input type="hidden" name="ids" id="bulkDeleteIds">

        <div class="modal-body">

          <p id="bulkDeleteText" class="fw-semibold text-danger mb-2">
            You are about to delete 0 leads
          </p>

          <p class="text-muted mb-0">
            This action cannot be undone.
          </p>

        </div>

        <div class="modal-footer">
          <button type="submit" class="btn btn-danger">Delete Leads</button>
          <button type="button" class="btn btn-light" data-bs-dismiss="modal">
            Cancel
          </button>
        </div>

      </form>

    </div>
  </div>
</div>

{{-- data update data  Modal --}}
<div class="modal fade" id="bulkDataupdateModal" tabindex="-1">
  <div class="modal-dialog modal-md">
    <div class="modal-content">

      <div class="modal-header">
        <h5 class="modal-title">Bulk Data Update</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>

      <form id="bulkDataUpdateForm" method="post" action="{{ route('leads.bulk.data.update') }}">


        @csrf
        <input type="hidden" name="ids" id="bulkDataIds">

        <div class="modal-body">

          <p id="bulkDataText" class="fw-semibold mb-3">
            You are about to update 0 leads
          </p>

          {{-- INSTITUTE --}}
          <div class="mb-2">
            <label class="form-label">Institute</label>
            <select name="institute_id" class="form-select">
              <option value="">Select Institute</option>
              @foreach($institutes as $i)
                <option value="{{ $i->id }}">{{ $i->name }}</option>
              @endforeach
            </select>
          </div>

          {{-- PROGRAM --}}
          <div class="mb-2">
            <label class="form-label">Program</label>
            <select name="programme_id" class="form-select">
              <option value="">Select Program</option>
              @foreach($programs as $p)
                <option value="{{ $p->id }}">{{ $p->name }}</option>
              @endforeach
            </select>
          </div>

          {{-- COURSE --}}
          <div class="mb-2">
            <label class="form-label">Course</label>
            <select name="course_id" class="form-select">
              <option value="">Select Course</option>
              @foreach($products as $c)
                <option value="{{ $c->id }}">{{ $c->name }}</option>
              @endforeach
            </select>
          </div>

          {{-- STATE --}}
          <div class="mb-2">
            <label class="form-label">State</label>
            <select name="state_id" class="form-select">
              <option value="">Select State</option>
              @foreach($states as $s)
                <option value="{{ $s->id }}">{{ $s->name }}</option>
              @endforeach
            </select>
          </div>

          {{-- CITY --}}
          <div class="mb-2">
            <label class="form-label">City</label>
            <select name="city_id" class="form-select">
              <option value="">Select City</option>
              @foreach($cities as $c)
                <option value="{{ $c->id }}">{{ $c->name }}</option>
              @endforeach
            </select>
          </div>

        </div>

        <div class="modal-footer">
          <button type="submit" class="btn btn-primary">Update Leads</button>
          <button type="button" class="btn btn-light" data-bs-dismiss="modal">
            Cancel
          </button>
        </div>

      </form>

    </div>
  </div>
</div>

{{--   pipeline update  Modal --}}
<div class="modal fade" id="bulkpipelineupdateModal" tabindex="-1">
  <div class="modal-dialog modal-md">
    <div class="modal-content">

      <div class="modal-header">
        <h5 class="modal-title">Move Leads to Pipeline</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>

      <form id="bulkPipelineForm" method="post" action="{{ route('leads.bulk.pipeline') }}">

        @csrf
        <input type="hidden" name="ids" id="bulkPipelineIds">

        <div class="modal-body">

          <p id="bulkPipelineText" class="fw-semibold mb-3">
            You are about to move 0 leads to pipeline
          </p>

          <div class="mb-3">
            <label class="form-label">Select Pipeline</label>
             <select name="pipeline_id" class="form-select">
      <option value="">Select Pipeline</option>
      @foreach($pipelines as $pl)
        <option value="{{ $pl->id }}">{{ $pl->name }}</option>
      @endforeach
    </select>
          </div>

        </div>

        <div class="modal-footer">
          <button type="submit" class="btn btn-primary">Move Leads</button>
          <button type="button" class="btn btn-light" data-bs-dismiss="modal">
            Cancel
          </button>
        </div>

      </form>

    </div>
  </div>
</div>


{{--   facebook    Modal --}}
<div class="modal fade" id="bulkfacebookModal" tabindex="-1">
  <div class="modal-dialog modal-md">
    <div class="modal-content">

      <div class="modal-header">
        <h5 class="modal-title text-primary">Send Leads to Facebook Audience</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>

<form id="bulkFacebookForm" method="post" action="{{ route('leads.bulk.facebook') }}">
        @csrf
        <input type="hidden" name="ids" id="bulkFacebookIds">

        <div class="modal-body">

          <p id="bulkFacebookText" class="fw-semibold mb-3">
            You are about to send 0 leads to Facebook
          </p>

          <div class="mb-3">
            <label class="form-label">Audience Name</label>
            <input type="text" name="audience_name" class="form-control"
                   placeholder="e.g. CRM Leads Jan 2026">
          </div>

          <div class="alert alert-info py-2 mb-0">
            Selected leads will be synced to Facebook Custom Audience.
          </div>

        </div>

        <div class="modal-footer">
          <button type="submit" class="btn btn-primary">Send to Facebook</button>
          <button type="button" class="btn btn-light" data-bs-dismiss="modal">
            Cancel
          </button>
        </div>

      </form>

    </div>
  </div>
</div>


{{--   google    Modal --}}
<div class="modal fade" id="bulkgoogleModal" tabindex="-1">
  <div class="modal-dialog modal-md">
    <div class="modal-content">

      <div class="modal-header">
        <h5 class="modal-title text-success">Send Leads to Google Ads Audience</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>

<form id="bulkGoogleForm" method="post" action="{{ route('leads.bulk.google') }}">
        @csrf
        <input type="hidden" name="ids" id="bulkGoogleIds">

        <div class="modal-body">

          <p id="bulkGoogleText" class="fw-semibold mb-3">
            You are about to send 0 leads to Google Ads
          </p>

          <div class="mb-3">
            <label class="form-label">Audience Name</label>
            <input type="text" name="audience_name" class="form-control"
                   placeholder="e.g. Remarketing Leads">
          </div>

          <div class="alert alert-warning py-2 mb-0">
            Leads will be uploaded as Customer Match audience.
          </div>

        </div>

        <div class="modal-footer">
          <button type="submit" class="btn btn-success">Send to Google</button>
          <button type="button" class="btn btn-light" data-bs-dismiss="modal">
            Cancel
          </button>
        </div>

      </form>

    </div>
  </div>
</div>


 @endsection
@section('script')
 @section('script')

{{-- 1) Vendor libs --}}
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

<script src="{{ URL::asset('build/libs/list.js/list.min.js') }}"></script>
<script src="{{ URL::asset('build/libs/list.pagination.js/list.pagination.min.js') }}"></script>
<script src="{{ URL::asset('build/libs/sweetalert2/sweetalert2.min.js') }}"></script>
<script src="{{ URL::asset('build/js/pages/crm-leads.init.js') }}"></script>

{{-- TinyMCE --}}
<script src="https://cdn.tiny.cloud/1/wh3ibzcxh5pbxxmvikxjhqqjcdv2dwc55t132iss53se0kuz/tinymce/6/tinymce.min.js"></script>

<script>
    window.APP_URL = "{{ url('') }}";
</script>

<script>
/**
 * ==========================================================
 * 0) CSRF GLOBAL SETUP (MOST IMPORTANT FIX FOR 419)
 * ==========================================================
 */
$.ajaxSetup({
    headers: {
        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content')
    },
    xhrFields: {
        withCredentials: true
    }
});
</script>

<script>
/**
 * ==========================================================
 * 1) QUICK LEAD OFFCANVAS TAB SWITCH
 * ==========================================================
 */
$(document).on("click", "#quickLeadTabs .nav-link", function (e) {
    e.preventDefault();

    $("#quickLeadTabs .nav-link").removeClass("active");
    $(this).addClass("active");

    let mode = $(this).data("mode");
    $("#uploadVia").val(mode);

    if (mode === "email") {
        $(".email-only").show();
        $("input[name='email']").prop("required", true);
    } else {
        $(".email-only").hide();
        $("input[name='email']").prop("required", false);
    }
});
</script>

<script>
/**
 * ==========================================================
 * 2) QUICK LEAD CASCADING DROPDOWNS
 * ==========================================================
 */

// Cities by state
$(document).on('change', '#ql-state', function () {
    let state_id = $(this).val();

    $("#ql-city").html('<option value="">Loading...</option>');

    $.ajax({
        url: "{{ url('/api/cities') }}",
        type: "GET",
        data: { state_id: state_id },
        success: function (res) {
            let html = '<option value="">Select City *</option>';
            res.forEach(row => {
                html += `<option value="${row.id}">${row.name}</option>`;
            });
            $('#ql-city').html(html);
        }
    });
});

// Programs by institute
$(document).on('change', '#ql-institute', function () {
    let institute_id = $(this).val();

    $("#ql-program").html('<option value="">Loading...</option>');
    $("#ql-course").html('<option value="">Select Program</option>');

    $.ajax({
        url: "{{ url('/api/programs') }}",
        type: "GET",
        data: { institute_id: institute_id },
        success: function (res) {
            let html = '<option value="">Select Program *</option>';
            res.forEach(row => {
                html += `<option value="${row.id}">${row.name}</option>`;
            });
            $('#ql-program').html(html);
        }
    });
});

// Courses by program
$(document).on('change', '#ql-program', function () {
    let program_id = $(this).val();

    $('#ql-course').html('<option value="">Loading...</option>');

    if (!program_id) {
        $('#ql-course').html('<option value="">Select Course *</option>');
        return;
    }

    $.ajax({
        url: "{{ url('/api/courses') }}",
        type: "GET",
        data: { programme_ids: program_id },
        success: function (res) {
            let html = '<option value="">Select Course *</option>';
            res.forEach(row => {
                html += `<option value="${row.id}">${row.name}</option>`;
            });
            $('#ql-course').html(html);
        },
        error: function () {
            $('#ql-course').html('<option value="">No courses found</option>');
        }
    });
});
</script>

<script>
/**
 * ==========================================================
 * 3) FILTER OFFCANVAS AUTO OPEN + LOCATION FILTERS
 * ==========================================================
 */

document.addEventListener("DOMContentLoaded", function () {
    const urlParams = new URLSearchParams(window.location.search);
    if (urlParams.get('filter_open') == '1') {
        var offcanvas = new bootstrap.Offcanvas('#offcanvasExample');
        offcanvas.show();
    }
});

// Country -> states
$(document).on('change', '#filter-country', function () {
    loadStates($(this).val(), null);
});

// State -> cities
$(document).on('change', '#filter-state', function () {
    loadCities($(this).val(), null);
});

// Prefill after page load
$(document).ready(function () {
    let selectedCountry = "{{ request('country') }}";
    let selectedState   = "{{ request('state') }}";
    let selectedCity    = "{{ request('city') }}";

    if (selectedCountry) {
        loadStates(selectedCountry, selectedState);

        if (selectedState) {
            loadCities(selectedState, selectedCity);
        }
    }
});

function loadStates(country_id, preselect = null) {
    $('#filter-state').html('<option value="">Loading...</option>');
    $('#filter-city').html('<option value="">Select City</option>');

    $.ajax({
        url: "{{ url('/api/states') }}",
        type: "GET",
        data: { country_id: country_id },
        success: function (res) {
            let html = '<option value="">Select State</option>';
            res.forEach(row => {
                html += `<option value="${row.id}" ${preselect == row.id ? 'selected' : ''}>${row.name}</option>`;
            });
            $('#filter-state').html(html);
        }
    });
}

function loadCities(state_id, preselect = null) {
    $('#filter-city').html('<option value="">Loading...</option>');

    $.ajax({
        url: "{{ url('/api/cities') }}",
        type: "GET",
        data: { state_id: state_id },
        success: function (res) {
            let html = '<option value="">Select City</option>';
            res.forEach(row => {
                html += `<option value="${row.id}" ${preselect == row.id ? 'selected' : ''}>${row.name}</option>`;
            });
            $('#filter-city').html(html);
        }
    });
}

// Reset filters
$(document).on("click", "#resetFilter", function () {
    window.location = "{{ route('Leads.ListView') }}";
});
</script>

<script>
/**
 * ==========================================================
 * 4) UPLOAD TYPE MODAL REDIRECT
 * ==========================================================
 */
$(document).on("click", "#openInstructions", function () {
    let key = document.querySelector('input[name="primary_key"]:checked')?.value || "email";
    window.location.href = "{{ route('leads.upload') }}?primary_key=" + key;
});
</script>

<script>
/**
 * ==========================================================
 * 5) QUICK LEAD FORM SUBMIT AJAX
 * ==========================================================
 */
$(document).on('submit', '#quickLeadForm', function (e) {
    e.preventDefault();

    let form = $(this);
    let submitBtn = form.find('button[type="submit"]');

    submitBtn.prop('disabled', true).text('Saving...');

    $.ajax({
        url: form.attr('action'),
        type: 'POST',
        data: form.serialize(),
        success: function (response) {

            submitBtn.prop('disabled', false).text('Save Lead');

            if (response.status) {

                Swal.fire({
                    icon: 'success',
                    title: 'Success',
                    text: response.message,
                    timer: 1500,
                    showConfirmButton: false
                });

                let canvas = bootstrap.Offcanvas.getInstance(document.getElementById('quickLeadCanvas'));
                if (canvas) canvas.hide();

                setTimeout(function(){
                    location.reload();
                }, 1500);

            } else {
                Swal.fire('Error', response.message, 'error');
            }
        },
        error: function () {
            submitBtn.prop('disabled', false).text('Save Lead');
            Swal.fire('Error', 'Something went wrong.', 'error');
        }
    });
});
</script>

<script>
/**
 * ==========================================================
 * 6) LEAD ACTIONS (SINGLE)
 * ==========================================================
 */

function closeAllLeadPopups() {
    document.querySelectorAll('.modal.show').forEach(el => {
        bootstrap.Modal.getInstance(el)?.hide();
    });
    document.querySelectorAll('.offcanvas.show').forEach(el => {
        bootstrap.Offcanvas.getInstance(el)?.hide();
    });
}

let CURRENT_LEAD_ID = null;

function setFormAction(formId, leadId) {
    const form = document.getElementById(formId);
    if (!form) return;

    // 🔥 if action template exists, use it
    const tpl = form.getAttribute('data-action-template');
    if (tpl) {
        form.setAttribute('action', tpl.replace('__ID__', leadId));
        return;
    }

    // 🔥 if direct action has __ID__, replace it too
    const direct = form.getAttribute('action');
    if (direct && direct.includes('__ID__')) {
        form.setAttribute('action', direct.replace('__ID__', leadId));
    }
}

function openLeadAction(leadId, action) {

    CURRENT_LEAD_ID = leadId;
    closeAllLeadPopups();

    [
        'changeStageForm',
        'addNoteForm',
        'addEventForm',
        'commEmailForm',
        'commSmsForm',
        'commWaForm',
        'reassignForm',
        'waReplyForm'
    ].forEach(id => setFormAction(id, leadId));

    loadLeadData(leadId);

    switch (action) {
        case 'note': new bootstrap.Modal('#addNoteModal').show(); break;
        case 'stage': new bootstrap.Offcanvas('#changeStageCanvas').show(); break;
        case 'event': new bootstrap.Offcanvas('#addEventCanvas').show(); break;
        case 'communicate': new bootstrap.Offcanvas('#communicationCanvas').show(); break;
        case 'reassign': new bootstrap.Modal('#reassignModal').show(); break;
        case 'delete': new bootstrap.Modal('#deleteRecordModal').show(); break;
    }
}

function loadLeadData(leadId){
    $.get(APP_URL + '/leads/' + leadId + '/json', function(res){

        $('#changeStage_stage_id').val(res.stage_id);
        $('#lead_status').val(res.lead_status_id);
        $('#changeStageOwner').text(res.assigned_user || 'Unassigned');
        $('#changeStageOwnerId').val(res.lead_assign_to);

        $('#eventSubject').val('Follow up: ' + res.name);
        $('#eventOwnerOption').val(res.lead_assign_to).text(res.assigned_user);
        $('#eventOrganiser').val(res.assigned_user);

        // Notes
        let notesHtml = '';
        if(res.notes && res.notes.length){
            res.notes.forEach(n=>{
                notesHtml += `<div class="border p-2 mb-1">
                    <small>${n.created_at}</small><br>${n.note}
                </div>`;
            });
        } else {
            notesHtml = '<div class="text-muted">No notes</div>';
        }
        $('#leadNotesList').html(notesHtml);

        // WhatsApp History
        let waHtml = '';
        if(res.whatsappChats && res.whatsappChats.length){
            res.whatsappChats.forEach(c=>{
                waHtml += `<div class="border p-2 mb-1">${c.message}</div>`;
            });
        } else {
            waHtml = '<div class="text-muted">No WhatsApp history</div>';
        }
        $('#waChatList').html(waHtml);

    });
}
</script>

<script>
/**
 * ==========================================================
 * 7) LEAD STATUS -> SUB STATUS (SINGLE + BULK)
 * ==========================================================
 */
$(document).on('change', '#lead_status', function () {
    loadSubStatuses($(this).val(), null, '#lead_sub_status');
});

$(document).on('change', '#lead_statusbulk', function () {
    loadSubStatuses($(this).val(), null, '#lead_sub_statusbulk');
});

function loadSubStatuses(statusId, preselect = null, targetSelect = '#lead_sub_status') {

    $(targetSelect).html('<option value="">Loading...</option>');

    if (!statusId) {
        $(targetSelect).html('<option value="">Select Sub Stage</option>');
        return;
    }

    $.ajax({
        url: APP_URL + '/api/lead-status-substatus',
        type: "POST",
        data: { lead_status_id: statusId },
        success: function (res) {
            let html = '<option value="">Select Sub Stage</option>';
            res.forEach(row => {
                html += `<option value="${row.id}" ${preselect == row.id ? 'selected' : ''}>${row.name}</option>`;
            });
            $(targetSelect).html(html);
        },
        error: function (err) {
            console.error('Substatus load failed', err);
        }
    });
}
</script>

<script>
/**
 * ==========================================================
 * 8) AJAX SUBMIT FOR ALL data-action-template FORMS
 * ==========================================================
 */
$(document).on('submit', 'form[data-action-template]', function (e) {

    e.preventDefault();
    const form = $(this);

    $.ajax({
        url: form.attr('action'),
        method: form.attr('method') || 'POST',
        data: form.serialize(),
        success(res) {
            Swal.fire({
                icon: 'success',
                title: 'Done',
                text: res.message || 'Saved successfully'
            });
            closeAllLeadPopups();
        },
        error(xhr) {
            let msg = 'Something went wrong';
            if (xhr.responseJSON?.message) msg = xhr.responseJSON.message;
            Swal.fire('Error', msg, 'error');
        }
    });
});
</script>

<script>
/**
 * ==========================================================
 * 9) TEMPLATES LOAD (EMAIL/SMS/WA) - SINGLE + BULK
 * ==========================================================
 */

let emailTplLoaded = false;
let smsTplLoaded = false;
let waTplLoaded = false;

function loadEmailTemplates(){
    if(emailTplLoaded) return;

    $('#emailTemplateSelect').html('<option>Loading...</option>');
    $('#emailTemplateSelectbulk').html('<option>Loading...</option>');

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
        $('#emailTemplateSelectbulk').html(html);
        emailTplLoaded = true;
    })
    .fail(function(){
        $('#emailTemplateSelect').html('<option>Error loading templates</option>');
        $('#emailTemplateSelectbulk').html('<option>Error loading templates</option>');
    });
}

function loadSmsTemplates(){
    if(smsTplLoaded) return;

    $('#smsTemplateSelect').html('<option>Loading...</option>');
    $('#smsTemplateSelectbulk').html('<option>Loading...</option>');

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
        $('#smsTemplateSelectbulk').html(html);
        smsTplLoaded = true;
    })
    .fail(function(){
        $('#smsTemplateSelect').html('<option>Error loading templates</option>');
        $('#smsTemplateSelectbulk').html('<option>Error loading templates</option>');
    });
}

function loadWhatsappTemplates(){
    if(waTplLoaded) return;

    $('#waTemplateSelect').html('<option>Loading...</option>');
    $('#waTemplateSelectbulk').html('<option>Loading...</option>');

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
        $('#waTemplateSelectbulk').html(html);
        waTplLoaded = true;
    })
    .fail(function(){
        $('#waTemplateSelect').html('<option>Error loading templates</option>');
        $('#waTemplateSelectbulk').html('<option>Error loading templates</option>');
    });
}

document.getElementById('communicationCanvas')?.addEventListener('shown.bs.offcanvas', function () {
    loadEmailTemplates();
    loadSmsTemplates();
    loadWhatsappTemplates();
});

document.getElementById('bulkcommunicationCanvas')?.addEventListener('shown.bs.offcanvas', function () {
    loadEmailTemplates();
    loadSmsTemplates();
    loadWhatsappTemplates();
});

// Template apply
$(document).on('change', '#emailTemplateSelect', function(){
    let id = $(this).val();
    if(!id) return;

    $.get('{{ url("api/template-by-id") }}', { id:id, type:'email' }, function(res){
        $('#emailSubject').val(res.subject || res.title || '');
        if(tinymce.get('emailBody')) tinymce.get('emailBody').setContent(res.message || '');
    });
});

$(document).on('change', '#smsTemplateSelect', function(){
    let id = $(this).val();
    if(!id) return;

    $.get('{{ url("api/template-by-id") }}', { id:id, type:'sms' }, function(res){
        $('#smsBody').val(res.message || '');
    });
});

$(document).on('change', '#waTemplateSelect', function(){
    let id = $(this).val();
    if(!id) return;

    $.get('{{ url("api/template-by-id") }}', { id:id, type:'whatsapp' }, function(res){
        $('#waBody').val(res.message || '');
    });
});

// BULK
$(document).on('change', '#emailTemplateSelectbulk', function(){
    let id = $(this).val();
    if(!id) return;

    $.get('{{ url("api/template-by-id") }}', { id:id, type:'email' }, function(res){
        $('#emailSubjectbulk').val(res.subject || res.title || '');
        if(tinymce.get('emailBodybulk')) tinymce.get('emailBodybulk').setContent(res.message || '');
    });
});

$(document).on('change', '#smsTemplateSelectbulk', function(){
    let id = $(this).val();
    if(!id) return;

    $.get('{{ url("api/template-by-id") }}', { id:id, type:'sms' }, function(res){
        $('#smsBodybulk').val(res.message || '');
    });
});

$(document).on('change', '#waTemplateSelectbulk', function(){
    let id = $(this).val();
    if(!id) return;

    $.get('{{ url("api/template-by-id") }}', { id:id, type:'whatsapp' }, function(res){
        $('#waBodybulk').val(res.message || '');
    });
});
</script>

<script>
/**
 * ==========================================================
 * 10) TINYMCE INIT (419 FIXED)
 * ==========================================================
 */
function initTiny(selector){

    if (!document.querySelector(selector)) return;

    // Already initialized
    if (tinymce.get(selector.replace('#',''))) return;

    tinymce.init({
        selector: selector,
        height: 300,
        plugins: 'link image code lists',
        toolbar: 'undo redo | bold italic | alignleft aligncenter alignright | bullist numlist | link image | code',
        menubar: false,

        image_advtab: true,
        extended_valid_elements: "img[class|src|alt|title|width|height|style]",
        automatic_uploads: true,

        images_upload_handler: function (blobInfo) {

            return new Promise(function (resolve, reject) {

                let csrf = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '';

                let xhr = new XMLHttpRequest();
                xhr.open('POST', "{{ url('tinymce/upload-image') }}", true);
                xhr.withCredentials = true;

                xhr.setRequestHeader('X-CSRF-TOKEN', csrf);
                xhr.setRequestHeader('X-Requested-With', 'XMLHttpRequest');

                xhr.onload = function () {
                    if (xhr.status !== 200) {
                        reject('HTTP Error: ' + xhr.status);
                        return;
                    }

                    let json = {};
                    try {
                        json = JSON.parse(xhr.responseText);
                    } catch(e){
                        reject('Invalid JSON response');
                        return;
                    }

                    if (!json || !json.location) {
                        reject('Invalid response: no location');
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

                let csrf = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '';
                let file = this.files[0];

                let formData = new FormData();
                formData.append('file', file);

                fetch("{{ url('tinymce/upload-file') }}", {
                    method: 'POST',
                    credentials: 'same-origin',
                    headers: {
                        'X-CSRF-TOKEN': csrf,
                        'X-Requested-With': 'XMLHttpRequest'
                    },
                    body: formData
                })
                .then(res => res.json())
                .then(data => {
                    if(data.location){
                        cb(data.location, { text: file.name });
                    }else{
                        alert('Upload failed: invalid response');
                    }
                })
                .catch(() => {
                    alert('Upload failed');
                });
            };

            input.click();
        }
    });
}

initTiny('#emailBody');
initTiny('#emailBodybulk');
</script>

<script>
/**
 * ==========================================================
 * 11) EVENT END TIME AUTO +15 MIN
 * ==========================================================
 */
document.getElementById('startAt')?.addEventListener('change', function () {
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
</script>

<script>
/**
 * ==========================================================
 * 12) REASSIGN USERS LOAD
 * ==========================================================
 */
let reassignUsersLoaded = false;

function loadReassignUsers(){
    if(reassignUsersLoaded) return;

    $('#reassignUserSelect').html('<option value="">Loading...</option>');
    $('#reassignUserSelectbulk').html('<option value="">Loading...</option>');

    $.get(APP_URL + '/api/users-for-reassign', function(res){

        let html = '<option value="">Select counsellor</option>';
        res.forEach(u=>{
            html += `<option value="${u.id}">${u.first_name} ${u.last_name ?? ''}</option>`;
        });

        $('#reassignUserSelect').html(html);
        $('#reassignUserSelectbulk').html(html);
        reassignUsersLoaded = true;
    });
}

document.getElementById('reassignModal')?.addEventListener('shown.bs.modal', function () {
    loadReassignUsers();
});
</script>

<script>
/**
 * ==========================================================
 * 13) SINGLE DELETE
 * ==========================================================
 */
const LEAD_DELETE_URL_TEMPLATE = "{{ route('leads.destroy', ':id') }}";

document.getElementById('delete-record')?.addEventListener('click', function () {

    if (!CURRENT_LEAD_ID) return;

    let url = LEAD_DELETE_URL_TEMPLATE.replace(':id', CURRENT_LEAD_ID);

    fetch(url, {
        method: 'DELETE',
        credentials: 'same-origin',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content'),
            'Accept': 'application/json'
        }
    })
    .then(res => res.json())
    .then(resp => {
        if (resp.status) {
            location.reload();
        } else {
            alert('Delete failed!');
        }
    })
    .catch(err => console.error(err));
});
</script>

<script>
/**
 * ==========================================================
 * 14) BULK SELECTION + BULK OPENERS
 * ==========================================================
 */
$(document).ready(function(){

    function updateSelectionCount(){
        let count = $('.lead-checkbox:checked').length;
        $('#selectedCount').text(count);

        if(count > 0){
            $('#selectionInfo').fadeIn();
        }else{
            $('#selectionInfo').fadeOut();
        }
    }

    $(document).on('change', '.lead-checkbox', function(){
        updateSelectionCount();

        let total = $('.lead-checkbox').length;
        let checked = $('.lead-checkbox:checked').length;
        $('#checkAll').prop('checked', total === checked);
    });

    $('#checkAll').on('change', function(){
        $('.lead-checkbox').prop('checked', this.checked);
        updateSelectionCount();
    });

});

function getSelectedLeadIds() {
    let ids = [];
    $('.lead-checkbox:checked').each(function () {
        ids.push($(this).val());
    });
    return ids;
}

function ensureLeadsSelected() {
    let ids = getSelectedLeadIds();
    if (ids.length === 0) {
        alert('Please select at least one lead');
        return false;
    }
    return true;
}

function openBulkDownload(){
    if(!ensureLeadsSelected()) return;

    let ids = getSelectedLeadIds();
    $('#bulkDownloadIds').val(ids.join(','));
    $('#bulkDownloadText').text('You are about to download ' + ids.length + ' leads');

    new bootstrap.Modal(document.getElementById('bulkdownloadModal')).show();
}

function openBulkFacebook(){
    if(!ensureLeadsSelected()) return;

    let ids = getSelectedLeadIds();
    $('#bulkFacebookIds').val(ids.join(','));
    $('#bulkFacebookText').text('You are about to send ' + ids.length + ' leads to Facebook');

    new bootstrap.Modal(document.getElementById('bulkfacebookModal')).show();
}

function openBulkGoogle(){
    if(!ensureLeadsSelected()) return;

    let ids = getSelectedLeadIds();
    $('#bulkGoogleIds').val(ids.join(','));
    $('#bulkGoogleText').text('You are about to send ' + ids.length + ' leads to Google Ads');

    new bootstrap.Modal(document.getElementById('bulkgoogleModal')).show();
}

function openBulkReassign(){
    if(!ensureLeadsSelected()) return;

    let ids = getSelectedLeadIds();
    $('#bulkReassignIds').val(ids.join(','));
    $('#bulkReassignText').text('You are about to reassign ' + ids.length + ' leads');

    new bootstrap.Modal(document.getElementById('bulkreassignModal')).show();
    loadReassignUsers();
}

function openBulkStage(){
    if(!ensureLeadsSelected()) return;

    let ids = getSelectedLeadIds();
    $('#bulkStageIds').val(ids.join(','));
    $('#bulkStageText').text('You are about to update stage for ' + ids.length + ' leads');

    new bootstrap.Offcanvas(document.getElementById('bulkchangeStageCanvas')).show();
}

function openBulkUpdate(){
    if(!ensureLeadsSelected()) return;

    let ids = getSelectedLeadIds();
    $('#bulkDataIds').val(ids.join(','));
    $('#bulkDataText').text('You are about to update ' + ids.length + ' leads');

    new bootstrap.Modal(document.getElementById('bulkDataupdateModal')).show();
}

function openBulkDelete(){
    if(!ensureLeadsSelected()) return;

    let ids = getSelectedLeadIds();
    $('#bulkDeleteIds').val(ids.join(','));
    $('#bulkDeleteText').text('You are about to delete ' + ids.length + ' leads');

    new bootstrap.Modal(document.getElementById('bulkDeleteModal')).show();
}

function openBulkPipeline(){
    if(!ensureLeadsSelected()) return;

    let ids = getSelectedLeadIds();
    $('#bulkPipelineIds').val(ids.join(','));
    $('#bulkPipelineText').text('You are about to move ' + ids.length + ' leads to pipeline');

    new bootstrap.Modal(document.getElementById('bulkpipelineupdateModal')).show();
}

function openBulkCommunicate(){
    if(!ensureLeadsSelected()) return;

    let ids = getSelectedLeadIds().join(',');
    $('#bulkEmailIds').val(ids);
    $('#bulkSmsIds').val(ids);
    $('#bulkWaIds').val(ids);

    new bootstrap.Offcanvas(document.getElementById('bulkcommunicationCanvas')).show();
}

function openBulkEvent(){
    if(!ensureLeadsSelected()) return;

    let ids = getSelectedLeadIds().join(',');
  

    new bootstrap.Offcanvas(document.getElementById('bulkeventCanvas')).show();
}
</script>

<script>
/**
 * ==========================================================
 * 15) BULK FORM SUBMISSIONS
 * ==========================================================
 */

// Bulk file downloads (facebook/google/download)
function downloadCsvForm(formId, modalId){

    $(document).on('submit', formId, function(e){
        e.preventDefault();

        let form = this;
        let formData = new FormData(form);

        fetch(form.action, {
            method: 'POST',
            credentials: 'same-origin',
            body: formData,
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content')
            }
        })
        .then(response => {
            const disposition = response.headers.get('Content-Disposition');
            let fileName = 'leads.csv';

            if (disposition && disposition.includes('filename=')) {
                fileName = disposition.split('filename=')[1].replace(/"/g, '');
            }

            return response.blob().then(blob => ({ blob, fileName }));
        })
        .then(({ blob, fileName }) => {
            let url = window.URL.createObjectURL(blob);
            let a = document.createElement('a');
            a.href = url;
            a.download = fileName;
            document.body.appendChild(a);
            a.click();
            a.remove();
            window.URL.revokeObjectURL(url);

            bootstrap.Modal.getInstance(document.getElementById(modalId))?.hide();
        })
        .catch(err => {
            alert('Download failed');
            console.error(err);
        });
    });
}

downloadCsvForm('#bulkGoogleForm','bulkgoogleModal');
downloadCsvForm('#bulkFacebookForm','bulkfacebookModal');
downloadCsvForm('#bulkDownloadForm','bulkdownloadModal');

// Bulk delete/pipeline/update/reassign/stage
function ajaxFormSubmit(formId, onDone){

    $(document).on('submit', formId, function(e){
        e.preventDefault();

        let form = $(this);

        $.ajax({
            url: form.attr('action'),
            type: 'POST',
            data: form.serialize(),
            success: function(res){
                Swal.fire({
                    icon: res.status === false ? 'warning' : 'success',
                    title: res.status === false ? 'Oops!' : 'Done!',
                    text: res.message || 'Success'
                }).then(() => {
                    if(onDone) onDone();
                    location.reload();
                });
            },
            error: function(){
                Swal.fire('Error','Something went wrong','error');
            }
        });
    });
}

ajaxFormSubmit('#bulkDeleteForm', function(){
    bootstrap.Modal.getInstance(document.getElementById('bulkDeleteModal'))?.hide();
});

ajaxFormSubmit('#bulkPipelineForm', function(){
    bootstrap.Modal.getInstance(document.getElementById('bulkpipelineupdateModal'))?.hide();
});
ajaxFormSubmit('#bulkEventForm', function(){
    bootstrap.Modal.getInstance(document.getElementById('bulkEventModal'))?.hide();
});
ajaxFormSubmit('#bulkDataUpdateForm', function(){
    bootstrap.Modal.getInstance(document.getElementById('bulkDataupdateModal'))?.hide();
});

ajaxFormSubmit('#bulkReassignForm', function(){
    bootstrap.Modal.getInstance(document.getElementById('bulkreassignModal'))?.hide();
});

ajaxFormSubmit('#bulkStageForm', function(){
    bootstrap.Offcanvas.getInstance(document.getElementById('bulkchangeStageCanvas'))?.hide();
});

// Bulk communication
function bulkCommSubmit(formId){
    $(document).on('submit', formId, function(e){
        e.preventDefault();

        let form = $(this);

        $.post(form.attr('action'), form.serialize())
        .done(function(res){
            Swal.fire('Done', res.message || 'Sent', 'success').then(()=>{
                bootstrap.Offcanvas.getInstance(document.getElementById('bulkcommunicationCanvas'))?.hide();
                location.reload();
            });
        })
        .fail(()=> Swal.fire('Error','Failed','error'));
    });
}

bulkCommSubmit('#bulkEmailForm');
bulkCommSubmit('#bulkSmsForm');
bulkCommSubmit('#bulkWaForm');
</script>

<script>
/**
 * ==========================================================
 * 16) SAVE FILTER + SAVE COLUMNS
 * ==========================================================
 */
window.currentFilters = @json(request()->except(['page','saved_filter_id']));

function saveFilterNow() {

    let nameEl = document.getElementById("filter_name");
    let filterName = nameEl ? nameEl.value : "";

    if(!filterName){
        filterName = prompt("Filter name?");
        if(!filterName) return;
    }

    let payload = {
        name: filterName,
        filters: window.currentFilters || {},
        columns: window.selectedColumns || [],
        sort: window.currentSort || {}
    };

    fetch("{{ route('leads.saved_filters.store') }}", {
        method: "POST",
        credentials: "same-origin",
        headers: {
            "Content-Type": "application/json",
            "X-CSRF-TOKEN": document.querySelector('meta[name="csrf-token"]')?.getAttribute('content')
        },
        body: JSON.stringify(payload)
    })
    .then(r => r.json())
    .then(res => {
        alert(res.message || "Saved");
        window.location.reload();
    });
}

function openColumnModal(){
    new bootstrap.Modal(document.getElementById('columnsModal')).show();
}

function saveColumnsNow(){

    let cols = [];
    document.querySelectorAll('.lead-col-checkbox:checked').forEach(cb=>{
        cols.push(cb.value);
    });

    if(cols.length === 0){
        alert("Please select at least 1 column");
        return;
    }

    fetch("{{ route('leads.columns.save') }}", {
        method: "POST",
        credentials: "same-origin",
        headers: {
            "Content-Type": "application/json",
            "X-CSRF-TOKEN": document.querySelector('meta[name="csrf-token"]')?.getAttribute('content')
        },
        body: JSON.stringify({ columns: cols })
    })
    .then(r=>r.json())
    .then(res=>{
        alert(res.message || "Saved");
        location.reload();
    });
}
</script>
<div class="modal fade" id="leadApiDocModal" tabindex="-1">
    <div class="modal-dialog modal-xl modal-dialog-scrollable">
        <div class="modal-content">

            <div class="modal-header bg-light">
                <h5 class="modal-title">
                    Lead Create API Documentation
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>

            <div class="modal-body">

                <h6 class="fw-bold">Endpoint</h6>
                <pre>POST {{ url('api/leads/add') }}</pre>

                <hr>

                <h6 class="fw-bold">Required Parameters</h6>
                <ul>
                    <li><b>organization_id</b> (integer)</li>
                    <li><b>name</b> (string)</li>
                    <li><b>mobile</b> (string)</li>
                </ul>

                <h6 class="fw-bold">Optional Parameters</h6>
                <ul>
                    <li>created_by</li>
                    <li>upload_via (mobile, email, whatsapp, google_ads, bulk, partner_api)</li>
                    <li>source_type (api, manual_quick_store, bulk_cron, google_ads)</li>
                    <li>institute_id</li>
                    <li>programme_id</li>
                    <li>faculty_id</li>
                    <li>application_form_id</li>
                    <li>state</li>
                    <li>city</li>
                </ul>

                <hr>

                <h6 class="fw-bold">Sample JSON Request</h6>
                <pre>{
  "organization_id": 1,
  "created_by": 2,
  "name": "Raj Kumar",
  "mobile": "9876543210",
  "email": "raj@test.com",
  "upload_via": "google_ads",
  "source_type": "google_ads"
}</pre>

                <hr>

                <h6 class="fw-bold">Success Response</h6>
                <pre>{
  "status": true,
  "message": "Lead created successfully",
  "lead_id": 45
}</pre>

                <hr>

                <h6 class="fw-bold">Error Response</h6>
                <pre>{
  "status": false,
  "message": "Organization ID required"
}</pre>

                <hr>

                <h6 class="fw-bold">Internal Process</h6>
                <ul>
                    <li>Insert into leads</li>
                    <li>Insert into lead_creation_logs</li>
                    <li>Create lead_verifications</li>
                    <li>Create communication log</li>
                    <li>Increment lead source count</li>
                </ul>

            </div>

            <div class="modal-footer">
                <button class="btn btn-secondary" data-bs-dismiss="modal">
                    Close
                </button>
            </div>

        </div>
    </div>
</div>
@endsection
