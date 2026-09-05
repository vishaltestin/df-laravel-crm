@extends('FrontEnd.layouts.master')
@section('title', 'Team')
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
            Team
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
                                 
                                <button type="button" class="btn btn-success add-btn" id="openAddTeam"><i
                                        class="ri-add-line align-bottom me-1"></i> Add Team</button>
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

                                        <th class="sort" data-sort="name">Name</th>
                                         <th class="sort" data-sort="organization_name">Organization</th>
                                         <th class="sort" data-sort="head">Head</th>
                                         <th class="sort" data-sort="members">Members</th>
                                         <th class="sort" data-sort="status">Status </th>
                                         <th class="sort" data-sort="created_at">Create Date</th>
                                        <th class="sort" data-sort="action">Action</th>
                                    </tr>
                                </thead>
                                <tbody class="list form-check-all">
							            @foreach($teams as $team)

                                    <tr>
                                        <th scope="row">
                                            <div class="form-check">
                                                <input class="form-check-input" type="checkbox"
                                                    name="chk_child" value="option1">
                                            </div>
                                        </th>
                                        <td class="id" style="display:none;"><a
                                                href="javascript:void(0);"
                                                class="fw-medium link-primary">#VZ2101</a></td>
                                        <td>
                                            <div class="d-flex align-items-center">
                                            
                                                <div class="flex-grow-1 ms-2 name">{{ $team->name }}</div>
                                            </div>
                                        </td>
                                             <td>
                                            <div class="d-flex align-items-center">
                                            
                                                <div class="flex-grow-1 ms-2 organization_name">{{ $team->organization->name ?? '-'  }}</div>
                                            </div>
                                        </td>
                                       

              <td>
                                            <div class="d-flex align-items-center">
                                            
                                                <div class="flex-grow-1 ms-2 heads">  {{ $users[$team->head] ?? 'N/A' }}</div>
                                            </div>
                                        </td>
                                       
									                 <td>
                                         <div class="col-sm-auto">
    <div class="avatar-group">

        @php
            $memberIds = explode(',', $team->team_member);
            $maxVisible = 4;
            $totalMembers = count($memberIds);
        @endphp

        @foreach($memberIds as $index => $m)

            @if($index < $maxVisible)

                @php
                    $avatar = $usersAvatar[$m] ?? null; // user_id => avatar.jpg
                @endphp

                <div class="avatar-group-item">
                    <a href="javascript:void(0);"
                       class="d-inline-block"
                       data-bs-toggle="tooltip"
                       data-bs-placement="top"
                       title="{{ $users[$m] ?? 'Unknown' }}">

                        @if($avatar && file_exists(public_path('uploads/users/'.$avatar)))
                            <img src="{{ asset('uploads/users/'.$avatar) }}"
                                 class="rounded-circle avatar-xxs"
                                 alt="">
                        @else
                            <img src="{{ URL::asset('images/user-img-2.png') }}"
                                 class="rounded-circle avatar-xxs"
                                 alt="">
                        @endif

                    </a>
                </div>

            @endif

        @endforeach

        {{-- Extra members count --}}
        @if($totalMembers > $maxVisible)
            <div class="avatar-group-item">
                <a href="javascript:void(0);">
                    <div class="avatar-xxs">
                        <span class="avatar-title rounded-circle bg-success text-white">
                            +{{ $totalMembers - $maxVisible }}
                        </span>
                    </div>
                </a>
            </div>
        @endif

    </div>
</div>

                                        </td>
                                       
                <td>
                                            <div class="d-flex align-items-center">
                                            
                                                <div class="flex-grow-1 ms-2 status">{{ $team->status ? 'Active' : 'Inactive'  }}</div>
                                            </div>
                                        </td>

									   <td class="gst">
                                           <div class="d-flex align-items-center">
                                            
                                                <div class="flex-grow-1 ms-2 created_at">{{ $team->created_at }}</div>
                                            </div>
											</td>
                                         <td>
                                            <ul class="list-inline hstack gap-2 mb-0">
                                                
                                                
                                               <li class="list-inline-item openViewTeam" data-bs-toggle="tooltip"
                                                    data-bs-trigger="hover" data-bs-placement="top"
                                                     data-id="{{ encrypt($team->id) }}" title="View">
    <i class="ri-eye-fill align-bottom text-muted"></i>
</li>
 <li class="list-inline-item  openEditTeam" data-id="{{ $team->id }}" title="Edit"  data-bs-toggle="tooltip"
                                                    data-bs-trigger="hover" data-bs-placement="top"
                                                    >
    <i class="ri-pencil-fill align-bottom text-muted"></i>
</li>
                                                <li class="list-inline-item" data-bs-toggle="tooltip"
                                                    data-bs-trigger="hover" data-bs-placement="top"
                                                    title="Delete">
                                                    <a class="remove-item-btn" data-bs-toggle="modal"
                                                        href="#deleteRecordModal">
                                                        <i
                                                            class="ri-delete-bin-fill align-bottom text-muted"></i>
                                                    </a>
                                                </li>
                                            </ul>
                                        </td>
                                    </tr>
                               @endforeach

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
                    <!-- ADD TEAM OFFCANVAS -->
<div class="offcanvas offcanvas-end" tabindex="-1" id="addTeamCanvas">
    <div class="offcanvas-header bg-light p-3">
        <h5 class="offcanvas-title" id="addTeamTitle">Add Team</h5>
        <button type="button" class="btn-close text-reset" data-bs-dismiss="offcanvas"></button>
    </div>
     <form class="d-flex flex-column justify-content-end h-100" id="addTeamForm"  autocomplete="off" enctype="multipart/form-data">

    @csrf
                            <div class="offcanvas-body">

    <!-- Organization -->
    <div class="mb-3">

    <!-- Team Name -->
         <label class="form-label">Team Name</label>
        <input type="text" name="team_name" class="form-control" required>
    </div>
<div class="mb-3">

    <label class="form-label">Organization</label>
        <select name="organization_id" id="organization" class="form-control organization" required>
            <option value="">Select</option>
            @foreach ($organizations as $org)
                <option value="{{ $org->id }}">{{ $org->name }}</option>
            @endforeach
        </select>
		</div>
     <!-- Team Members -->
    <div class="mb-3">
        <label class="form-label">Team Members</label>
        <select name="members[]" id="users" class="form-control users" multiple required>
            <option value="">Select Users</option>
        </select>
    </div>
     <div class="row g-3">
                                            <div class="col-lg-6">
        <label class="form-label">Team Head</label>
        <select name="head" id="head" class="form-control head"  required>
            <option value="">Select Users</option>
        </select>
    </div>
                                            <div class="col-lg-6">
        <label class="form-label">Status</label>
        <select name="status" id="status1" class="form-control"  required>
            <option value="">Select status</option>
			<option value="1">Active</option>
			<option value="0">DeActive</option>
        </select>
    </div>
</div>
 </div>

<div class="offcanvas-footer border-top p-3 text-center hstack gap-2">
                                <button type="submit" class="btn btn-success w-100">Save</button>
                            </div>

</form>                                <button class="btn btn-light w-100" data-bs-dismiss="offcanvas">Cancel</button>

</div>

<div class="offcanvas offcanvas-end" tabindex="-1" id="editTeamCanvas">
    <div class="offcanvas-header bg-light p-3">
        <h5 class="offcanvas-title" id="editTeamTitle">Edit Team</h5>
        <button type="button" class="btn-close text-reset" data-bs-dismiss="offcanvas"></button>
    </div>
       <form id="editTeamForm" class="d-flex flex-column justify-content-end h-100" method="POST" >

        @csrf
         <input type="hidden" id="edit_id" name="id">
    <input type="hidden" name="_method" value="PUT">
        <div class="offcanvas-body">

            <div class="mb-3">
                <label class="form-label">Team Name</label>
                <input type="text" id="edit_team_name" name="team_name" class="form-control" required>
            </div>

            <div class="mb-3">
                <label class="form-label">Organization</label>
                <select id="edit_organization" name="organization_id" class="form-control organization" required>
                    <option value="">Select</option>
                    @foreach ($organizations as $org)
                        <option value="{{ $org->id }}">{{ $org->name }}</option>
                    @endforeach
                </select>
            </div>

            <div class="mb-3">
                <label class="form-label">Team Members</label>
                <select id="edit_members" name="members[]" class="form-control users" multiple required>
                    <option value="">Select</option>
                </select>
            </div>

            <div class="row g-3">
                <div class="col-lg-6">
                    <label class="form-label">Team Head</label>
                    <select id="edit_head" name="head" class="form-control head" required>
                        <option value="">Select</option>
                    </select>
                </div>

                <div class="col-lg-6">
                    <label class="form-label">Status</label>
                    <select id="edit_status" name="status" class="form-control" required>
                        <option value="">Select</option>
                        <option value="1">Active</option>
                        <option value="0">DeActive</option>
                    </select>
                </div>
            </div>

        </div>

        <div class="offcanvas-footer border-top p-3 text-center hstack gap-2">
            <button type="submit" class="btn btn-success w-100">Update</button>
        </div>

    </form>            <button class="btn btn-light w-100" data-bs-dismiss="offcanvas">Cancel</button>

</div>

<div class="offcanvas offcanvas-end" tabindex="-1" id="viewTeamCanvas">
    <div class="offcanvas-header bg-light p-3">
        <h5 class="offcanvas-title">View Team</h5>
        <button type="button" class="btn-close text-reset" data-bs-dismiss="offcanvas"></button>
    </div>
     <form class="d-flex flex-column justify-content-end h-100" id="addTeamForm"  autocomplete="off" enctype="multipart/form-data">

    @csrf
                            <div class="offcanvas-body">

     <div class="mb-3">
            <label class="form-label fw-bold">Team Name</label>
            <div id="view_name">-</div>
        </div>

        <div class="mb-3">
            <label class="form-label fw-bold">  Head</label>
            <div id="view_head">-</div>
        </div>
 <div class="mb-3">
            <label class="form-label fw-bold">  Organization</label>
            <div id="view_organization">-</div>
        </div>
        <div class="mb-3">
            <label class="form-label fw-bold">Team Members</label>
            <div id="view_members">-</div>
        </div>

        <div class="mb-3">
            <label class="form-label fw-bold">Status</label>
            <div id="view_status">-</div>
        </div>

        <div class="mb-3">
            <label class="form-label fw-bold">Created At</label>
            <div id="view_created">-</div>
        </div>

    </div>
</form>

 
<div class="offcanvas-footer border-top p-3 text-center hstack gap-2">
                                      <button class="btn btn-light w-100" data-bs-dismiss="offcanvas">Close</button>
									  </div>

</div>


                   
                </div>
            </div>

        </div>
        <!--end col-->
    </div>
    <!--end row-->

@endsection
@section('script')
    <script src="{{ URL::asset('build/libs/list.js/list.min.js') }}"></script>
    <script src="{{ URL::asset('build/libs/list.pagination.js/list.pagination.min.js') }}"></script>
    <script src="{{ URL::asset('build/libs/sweetalert2/sweetalert2.min.js') }}"></script>
     	    <script src="{{ URL::asset('build/js/pages/crm-leads.init.js') }}"></script>

    
	<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
	<script>
	$("#openAddTeam").on("click", function () {
    var offcanvas = new bootstrap.Offcanvas(document.getElementById("addTeamCanvas"));
    offcanvas.show();
});
/*$(".openEditTeam").on("click", function () {
    var offcanvas = new bootstrap.Offcanvas(document.getElementById("editTeamCanvas"));
    offcanvas.show();
});
$(".openViewTeam").on("click", function () {
    var offcanvas = new bootstrap.Offcanvas(document.getElementById("viewTeamCanvas"));
    offcanvas.show();
});*/

$(document).ready(function () {

    // --- Organization Change → Load Users ---
    $('.organization').on('change', function () {
        var orgId = $(this).val();
if(orgId!='') 
{
	$('#users').empty().append('<option>Loading...</option>');
		    $('#head').html('<option>Loading...</option>');

        $.ajax({
            url: "{{ url('get-users') }}/" + orgId,
            method: "GET",
            success: function (data) {
			$('.users').empty();
            $('.head').empty();

            $('.users').append('<option value="">Select Users</option>');
            $('.head').append('<option value="">Select Head</option>');
			
			$.each(data, function (i, user) {
            let name = user.first_name + ' ' + user.last_name;

            $('.users').append('<option value="' + user.id + '">' + name + '</option>');
            $('.head').append('<option value="' + user.id + '">' + name + '</option>');
            });

            }
        });
	}
    });

    // --- Team Form Submit ---
  $('#addTeamForm').on('submit', function (e) {
    e.preventDefault();

    $.ajax({
        url: "{{ route('teams.store') }}",
        method: "POST",
        data: new FormData(this),
        contentType: false,
        processData: false,

        success: function (response) {
            if (response.status == "success") {

                // Custom Sweet Alert Trigger
                document.getElementById("custom-sa-success").click();

                // Offcanvas hide
                var offcanvas = bootstrap.Offcanvas.getInstance(document.getElementById("addTeamCanvas"));
                offcanvas.hide();

                // Page reload
                setTimeout(() => {
                    location.reload();
                }, 800);
            }
        }
    });
});



});
$(document).on("click", ".openViewTeam", function () {
 var offcanvas = new bootstrap.Offcanvas(document.getElementById("viewTeamCanvas"));
    offcanvas.show();
    let id = $(this).data("id");
     $.ajax({
        url: "{{ url('teams') }}/" + id,
        method: "GET",
        success: function (res) {

          
            $("#view_organization").text(res.organization);

            $("#view_name").text(res.name);
            $("#view_head").text(res.head_name);
            $("#view_members").html(res.member_names); // <-- multiple aa jayenge
            $("#view_created").html(res.created_at);
            $("#view_status").text(res.status == 1 ? 'Active' : 'Inactive');
            $("#view_org").text(res.organization);

         }
    });

});



$(document).on("click", ".openEditTeam", function () {

    let id = $(this).data("id");
    var offcanvas = new bootstrap.Offcanvas($("#editTeamCanvas")[0]);
    offcanvas.show();

    $.ajax({
        url: "{{ url('teams') }}/" + id + "/edit",
        method: "GET",
        success: function (res) {

            $("#edit_id").val(res.id);
            $("#edit_team_name").val(res.name);
            $("#edit_organization").val(res.organization_id);
  			// organization के users fetch करना
            fetchOrganizationUsers(res.organization_id, res.members,res.head);

            $("#edit_status").val(res.status);
        }
    });

});


$("#editTeamForm").on("submit", function (e) {
    e.preventDefault();

    let id = $("#edit_id").val();

    // ✅ serialize is correct here
    let formData = $(this).serialize();

    $.ajax({
        url: "{{ url('teams') }}/" + id,
        method: "POST",              // ✅ ALWAYS POST
        data: formData,              // ✅ serialized data
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        },

        success: function (res) {

            Swal.fire({
                icon: 'success',
                title: 'Updated',
                text: 'Team updated successfully',
                timer: 1500,
                showConfirmButton: false
            });

            // Close offcanvas
            let offcanvasEl = document.getElementById('editTeamCanvas');
            let offcanvas = bootstrap.Offcanvas.getInstance(offcanvasEl);
            offcanvas.hide();

            setTimeout(() => location.reload(), 800);
        },

        error: function (xhr) {
            Swal.fire({
                icon: 'error',
                title: 'Error',
                text: 'Something went wrong!'
            });
        }
    });
});



function fetchOrganizationUsers(orgId, selectedMembers = [], selectedHead = null) {
    $.ajax({
        url: "{{ url('get-users') }}/" + orgId, // Backend route जो users return करे
        method: "GET",
        success: function(users) {
            let membersOptions = '';
            let headOptions = '<option value="">Select</option>';
            selectedMembers = (selectedMembers || []).map(String);
          
		  users.forEach(function(user) {
                // Team Members options
			  let uid = String(user.id); // 👈 string cast
			 let selectedM = selectedMembers.includes(uid) ? 'selected' : ''; 
				membersOptions += `
                    <option value="${user.id}" ${selectedM}>
                        ${user.first_name} ${user.last_name}
                    </option>
                `;
                // Team Head options
                let selectedH = (user.id == selectedHead) ? 'selected' : '';
                headOptions += `<option value="${user.id}" ${selectedH}>${user.first_name} ${user.last_name}</option>`;
            });

            $("#edit_members").html(membersOptions);
            $("#edit_head").html(headOptions);
        }
    });
}

// अगर user organization change करे edit form में
$("#edit_organization").on('change', function() {
    let orgId = $(this).val();
    fetchOrganizationUsers(orgId);
});

</script>
 @endsection
