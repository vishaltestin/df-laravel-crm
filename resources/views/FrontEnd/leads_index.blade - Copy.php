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
@section('css')
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
                        <div class="col-sm-auto ms-auto">
                            <div class="hstack gap-2">
                               <button class="btn btn-soft-danger" id="remove-actions" onClick="deleteMultiple()"><i class="ri-delete-bin-2-line"></i></button>
                           
                                 <button type="button" class="btn btn-info" data-bs-toggle="offcanvas"
                                    href="#offcanvasExample"><i
                                        class="ri-filter-3-line align-bottom me-1"></i> Fliters</button>
                             <button 
    type="button" 
    class="btn btn-success add-btn"
    data-bs-toggle="offcanvas"
    data-bs-target="#quickLeadCanvas">

    <i class="ri-add-line align-bottom me-1"></i> Add Leads
</button>
<a href="javascript:void(0)"
   class="btn btn-primary"
   data-bs-toggle="modal"
   data-bs-target="#uploadTypeModal">
   <i class="ri-upload-2-line"></i> Upload Leads
</a>
<div class="modal fade" id="uploadTypeModal">
  <div class="modal-dialog">
    <div class="modal-content">

      <div class="modal-header">
        <h5 class="modal-title">Select Primary Upload Type</h5>
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
        <button class="btn btn-primary" id="openInstructions">
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
                                        <li><a class="dropdown-item" href="#">Copy</a></li>
                                        <li><a class="dropdown-item" href="#">Move to pipline</a></li>
                                        <li><a class="dropdown-item" href="#">Add to exceptions</a></li>
                                        <li><a class="dropdown-item" href="#">Switch to common form
                                                view</a></li>
                                        <li><a class="dropdown-item" href="#">Reset form view to
                                                default</a></li>
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
                                         <th class="sort" data-sort="name">Name</th>
										<th class="sort" data-sort="mobile">Phone</th>
										<th class="sort" data-sort="email">Email</th>
										<th class="sort" data-sort="programmes">Course</th>
										<th class="sort" data-sort="lead_assign_to">Assign To</th>
										<th class="sort" data-sort="created_at">Create Date</th>
									   @if(in_array($currentRoleSlug, ['root','sadmin']))
							<th class="sort" data-sort="organization_id">Organization</th>@endif
 										<th  ></th>
                                     </tr>
								</thead>
                                <tbody class="list form-check-all">
								
								    @forelse($leads as $lead)
									<tr>
									<th scope="row">
                                            <div class="form-check">
                                                <input class="form-check-input" type="checkbox"   name="chk_child" value="option1">
                                            </div>
                                        </th>	@if($lead->lead_assign_to > 0)
									   <td class="" data-bs-toggle="tooltip" data-bs-placement="right" title="{{ $lead->assigned_user_name }} Lead">     
										@else
									   <td class="lead-border-left " data-bs-toggle="tooltip" data-bs-placement="right" title="Untouced Lead">     
										@endif
										 <div class="form-check">
										 
						                                           @php
$duplicates = \App\Lead::where('mobile', $lead->mobile)
    ->where('id', '!=', $lead->id)
    ->orderBy('id', 'DESC')
    ->get();
 
    if($duplicates->count() == 0){
        $label = 'P';
        $color = 'bg-warning'; // yellow
        $tooltip = 'Primary lead (first unique entry)';
    } 
    elseif ($duplicates->count()== 1) {
        $label = 'S';
        $color = 'bg-info'; // blue
        $tooltip = 'Secondary lead (duplicate 1 time)';
    } 
    else {
        $label = 'T';
        $color = 'bg-danger'; // red
        $tooltip = 'Tertiary lead (duplicate 2 or more times)';
    }
@endphp
 @if($duplicates->count() > 0)
<span class="badge {{ $color }} rounded-pill"       data-bs-toggle="tooltip"       data-bs-placement="top"       title="{{ $tooltip }}"> {{ $label }} </span>
  <span  class="  "     data-bs-toggle="modal" data-bs-target="#dupModal{{ $lead->id }}">     <u> {{ $duplicates->count() }}</u>  </span>



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
                    <td>{{ $dup->lead_campaign_name }}</td>
                    <td>{{ $dup->stage_name }}</td>
                    <td>{{ $dup->created_at->format('d M Y H:i:s') }}</td>
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
      title="{{ $tooltip }}"  >
    {{ $label }}     
</span>


	 @endif

													</div>
 
										</td> 
                                         <td class="id" style="display:none;"><a  href="javascript:void(0);"  class="fw-medium link-primary">#VZ2101</a> </td>
										<td class="name"><a href="leads/{{ $lead->id }}"> 	<span class="text-truncate-10" data-bs-toggle="tooltip" title="{{ $lead->name }}">{{ $lead->name }} <i class="ri-arrow-right-s-line"></i> </a></span> </td>

										<!-- PHONE -->
										<td class="mobile">{{ $lead->mobile }}</td>

										<!-- EMAIL -->
										<td class="email">{{ $lead->email }}</td>

										<!-- Course/Programmes -->
										<td class="programmes">{{ $lead->programme_name   }}</td>

								

										<!-- Assign To -->
										<td class="lead_assign_to">
											@if($lead->lead_assign_to > 0)
												{{ $lead->assigned_user_name }}
											@else
												<span class="badge bg-warning">Unassigned</span>
											@endif
										</td>

										<!-- Create Date -->
										<td  class="created_at">{{ $lead->created_at->format('d M, Y') }}</td>

											<!-- Organization Name From Join -->
								 @if(in_array($currentRoleSlug, ['root','sadmin']))
												<td  class="organization_id">{{ $lead->organization_name ?? '-' }}</td>@endif



											<!-- Lead Score Duplicate Logic -->
										 

                                <td> 
								
 								
								
												
																
		<div class="dropdown">
    <button class="btn btn-link text-muted p-1 py-0 fs-15" data-bs-toggle="dropdown">
        <i data-feather="more-horizontal" class="icon-sm"></i>
    </button>

    <div class="dropdown-menu dropdown-menu-end">

        <a class="dropdown-item"
           href="javascript:void(0)"
           onclick="openLeadAction({{ $lead->id }}, 'communicate')">
            <i class="ri-chat-1-line me-2"></i> Communicate
        </a>

        <a class="dropdown-item"
           href="javascript:void(0)"
           onclick="openLeadAction({{ $lead->id }}, 'stage')">
            <i class="ri-exchange-dollar-line me-2"></i> Change Lead Stage
        </a>

        <a class="dropdown-item"
           href="javascript:void(0)"
           onclick="openLeadAction({{ $lead->id }}, 'reassign')">
            <i class="ri-user-shared-line me-2"></i> Re-assign Lead
        </a>

        <a class="dropdown-item"
           href="javascript:void(0)"
           onclick="openLeadAction({{ $lead->id }}, 'event')">
            <i class="ri-calendar-event-line me-2"></i> Add Event
        </a>

        <a class="dropdown-item"
           href="javascript:void(0)"
           onclick="openLeadAction({{ $lead->id }}, 'note')">
            <i class="ri-sticky-note-line me-2"></i> Add Note
        </a>

        <a class="dropdown-item"
           href="javascript:void(0)"
           onclick="openLeadAction({{ $lead->id }}, 'view')">
            <i class="ri-user-line me-2"></i> View Lead (Quick)
        </a>

        <div class="dropdown-divider"></div>

        <a class="dropdown-item text-danger"
           href="javascript:void(0)"
           onclick="openLeadAction({{ $lead->id }}, 'delete')">
            <i class="ri-delete-bin-6-line me-2"></i> Delete
        </a>

    </div>
</div>


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

        <div class="offcanvas-body">

            {{-- TABS --}}
            <ul class="nav nav-tabs mb-3" id="quickLeadTabs">
                <li class="nav-item">
                    <a class="nav-link active" data-mode="email" href="#">Email</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" data-mode="whatsapp" href="#">WhatsApp</a>
                </li>
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
    <select name="event_id" class="form-control">
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
{{-- GLOBAL LEAD ACTION OFFCANVAS --}}
<div class="offcanvas offcanvas-end" tabindex="-1" id="leadActionCanvas">
    <div class="offcanvas-header">
        <h5 class="offcanvas-title" id="leadCanvasTitle">Lead Action</h5>
        <button type="button" class="btn-close" data-bs-dismiss="offcanvas"></button>
    </div>
    <div class="offcanvas-body" id="leadCanvasBody">
        <div class="text-center text-muted p-4">Loading...</div>
    </div>
</div>

{{-- GLOBAL LEAD ACTION MODAL --}}
<div class="modal fade" id="leadActionModal" tabindex="-1">
    <div class="modal-dialog modal-lg modal-dialog-scrollable">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="leadModalTitle">Lead Action</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body" id="leadModalBody">
                <div class="text-center text-muted p-4">Loading...</div>
            </div>
        </div>
    </div>
</div>

 @endsection
@section('script')
    <script src="{{ URL::asset('build/libs/list.js/list.min.js') }}"></script>
    <script src="{{ URL::asset('build/libs/list.pagination.js/list.pagination.min.js') }}"></script>
    <script src="{{ URL::asset('build/libs/sweetalert2/sweetalert2.min.js') }}"></script>
   	    <script src="{{ URL::asset('build/js/pages/crm-leads.init.js') }}"></script>
   	    
   		<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

 <script>
 
 
 // ----------------------
// TAB SWITCH LOGIC
// ----------------------
$("#quickLeadTabs .nav-link").on("click", function (e) {
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

// ----------------------
// LOAD CITIES ON STATE
// ----------------------
$('#ql-state').on('change', function () {
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


// ----------------------
// LOAD program ON institute
// ----------------------
$('#ql-institute').on('change', function () {
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


 $('#ql-program').on('change', function () {
    let program_id = $(this).val();

    $('#ql-course').html('<option value="">Loading...</option>');

    if (!program_id) {
        $('#ql-course').html('<option value="">Select Course *</option>');
        return;
    }

    $.ajax({
        url: "{{ url('/api/courses') }}",
        type: "GET",
        data: { programme_ids: program_id },   // ✅ MATCHED KEY
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
 



// ----------------------------
// OPEN CANVAS IF FILTER USED
// ----------------------------
document.addEventListener("DOMContentLoaded", function () {
    const urlParams = new URLSearchParams(window.location.search);
    if (urlParams.get('filter_open') == '1') {
        var offcanvas = new bootstrap.Offcanvas('#offcanvasExample');
        offcanvas.show();
    }
});

// ----------------------------
// LOAD STATES ON COUNTRY CHANGE
// ----------------------------
$('#filter-country').on('change', function () {
    loadStates($(this).val(), null);  
});

// ----------------------------
// LOAD CITIES ON STATE CHANGE
// ----------------------------
$('#filter-state').on('change', function () {
    loadCities($(this).val(), null);
});

// --------------------------------
// AUTO PREFILL AFTER PAGE LOAD
// --------------------------------
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


// --------------------------------
// FUNCTIONS
// --------------------------------
function loadStates(country_id, preselect = null) {

    $('#filter-state').html('<option value="">Loading...</option>');
    $('#filter-city').html('<option value="">Select City</option>');

    let url = "{{ url('/api/states') }}";

    $.ajax({
        url: url,
        type: "GET",
        data: { country_id: country_id },
        success: function (res) {

            let html = '<option value="">Select State</option>';

            res.forEach(row => {
                html += `<option value="${row.id}" 
                    ${preselect == row.id ? 'selected' : ''}>
                    ${row.name}
                </option>`;
            });

            $('#filter-state').html(html);
        }
    });
}
function loadCities(state_id, preselect = null) {

    $('#filter-city').html('<option value="">Loading...</option>');

    let url = "{{ url('/api/cities') }}";

    $.ajax({
        url: url,
        type: "GET",
        data: { state_id: state_id },
        success: function (res) {

            let html = '<option value="">Select City</option>';

            res.forEach(row => {
                html += `<option value="${row.id}" 
                    ${preselect == row.id ? 'selected' : ''}>
                    ${row.name}
                </option>`;
            });

            $('#filter-city').html(html);
        }
    });
}

// ----------------------------
// RESET FILTERS
// ----------------------------
$("#resetFilter").click(function () {
    window.location = "{{ route('Leads.ListView') }}";
});

</script>
<script>
document.getElementById('openInstructions').onclick = function () {
    let key = document.querySelector('input[name="primary_key"]:checked').value;
    window.location.href = "{{ route('leads.upload') }}?primary_key=" + key;
};






 
$(document).ready(function () {

    
    // -----------------------------
    // FORM SUBMIT (AJAX)
    // -----------------------------
    $('#quickLeadForm').on('submit', function (e) {
        e.preventDefault();

        let form = $(this);
        let errors = [];

        // -----------------------------
        // BASIC REQUIRED FIELDS
        // -----------------------------
        let name     = $('input[name="name"]').val().trim();
        let mobile   = $('input[name="mobile"]').val().trim();
        let uploadVia = $('#uploadVia').val();
        let email    = $('input[name="email"]').val().trim();

        let state    = $('#ql-state').val();
        let city     = $('#ql-city').val();

        let institute = $('#ql-institute').val();
        let program   = $('#ql-program').val();
        let course    = $('#ql-course').val();

        let source   = $('select[name="source"]').val();
        let medium   = $('select[name="medium"]').val();

        // -----------------------------
        // VALIDATIONS
        // -----------------------------
        if (!name) errors.push('Name is required');
        if (!mobile) errors.push('Mobile is required');

        if (uploadVia === 'email' && !email) {
            errors.push('Email is required');
        }

        if (!state) errors.push('State is required');
        if (!city) errors.push('City is required');

        if (!institute) errors.push('Institute is required');
        if (!program) errors.push('Program is required');
        if (!course) errors.push('Course is required');

        if (!source) errors.push('Source is required');
        if (!medium) errors.push('Medium is required');

        // -----------------------------
        // SHOW ERRORS (IF ANY)
        // -----------------------------
        if (errors.length > 0) {
            alert(errors.join("\n"));
            return false;
        }

        // -----------------------------
        // DISABLE BUTTON
        // -----------------------------
        let submitBtn = form.find('button[type="submit"]');
        submitBtn.prop('disabled', true).text('Saving...');

        // -----------------------------
        // AJAX SUBMIT
        // -----------------------------
        $.ajax({
            url: form.attr('action'),
            type: 'POST',
            data: form.serialize(),
            success: function (res) {

                // SUCCESS UX
                submitBtn.prop('disabled', false).text('Save Lead');

                // Close offcanvas
                let canvas = bootstrap.Offcanvas.getInstance(
                    document.getElementById('quickLeadCanvas')
                );
                if (canvas) canvas.hide();

                // Optional: reload leads list
                location.reload();
            },
            error: function (xhr) {
                submitBtn.prop('disabled', false).text('Save Lead');

                if (xhr.responseJSON && xhr.responseJSON.errors) {
                    let msg = '';
                    $.each(xhr.responseJSON.errors, function (k, v) {
                        msg += v[0] + "\n";
                    });
                    alert(msg);
                } else {
                    alert('Something went wrong. Please try again.');
                }
            }
        });

    });

});
</script>
<script>
    window.APP_URL = "{{ url('') }}";
</script>
<script>
function openLeadAction(leadId, action) {

        const base = window.APP_URL;

    const routes = {
        stage:       `${base}/leads/${leadId}/popup/change-stage`,
        reassign:    `${base}/leads/${leadId}/popup/reassign`,
        note:        `${base}/leads/${leadId}/popup/add-note`,
        communicate: `${base}/leads/${leadId}/popup/communicate`,
        event:       `${base}/leads/${leadId}/popup/add-event`,
        view:        `${base}/leads/${leadId}/popup/view`,
        delete:      `${base}/leads/${leadId}/popup/delete`
    };

    const titles = {
        stage: 'Change Lead Stage',
        reassign: 'Re-assign Lead',
        note: 'Add Note',
        communicate: 'Communicate',
        event: 'Add Event',
        view: 'Lead Quick View',
        delete: 'Delete Lead'
    };

    const modalActions = ['note', 'view', 'delete'];

    if (modalActions.includes(action)) {
        $('#leadModalTitle').text(titles[action]);
        $('#leadModalBody').html('Loading...');
        new bootstrap.Modal('#leadActionModal').show();
        $('#leadModalBody').load(routes[action]);
    } else {
        $('#leadCanvasTitle').text(titles[action]);
        $('#leadCanvasBody').html('Loading...');
        new bootstrap.Offcanvas('#leadActionCanvas').show();
        $('#leadCanvasBody').load(routes[action]);
    }
}
</script>

 @endsection
 