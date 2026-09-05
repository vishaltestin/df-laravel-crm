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
            Users
        @endslot
    @endcomponent
<style>.offcanvas-body {
    padding: 0 !important;
}

.offcanvas-footer {
    position: sticky;
    bottom: 0;
    z-index: 10;
}</style>
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
                             <button type="button" class="btn btn-info" data-bs-toggle="offcanvas"
                                    href="#offcanvasExample"><i
                                        class="ri-filter-3-line align-bottom me-1"></i> Fliters</button>  
                                        
                                        
	 @if(in_array($currentRoleSlug, ['root','sadmin','admin']))
     <button type="button" class="btn btn-success add-btn"   id="create-btn">
 <i class="ri-add-line align-bottom me-1"></i> Add Users</button>
  <button class="btn btn-soft-danger" id="remove-actions" onClick="deleteMultiple()"><i class="ri-delete-bin-2-line"></i></button>
                 @endif        
					   
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

                                        <th></th>
                                        <th class="sort" data-sort="name">Name</th>
                                        <th class="sort" data-sort="email">Email</th>
                                          <th class="sort" data-sort="mobile">Mobile</th>
										  	 @if(in_array($currentRoleSlug, ['root','sadmin'])) <th class="sort" data-sort="organization">Org</th> @endif 
                                        <th class="sort" data-sort="Role">Role</th>
                                        <th class="sort" data-sort="date">Last Login</th>                                  
                                         <th class="sort" data-sort="date">Create Date</th>
                                        <th class="sort" data-sort="action">Action</th>
                                    </tr>
                                </thead>
                                <tbody class="list form-check-all">
								@foreach($users  as $user)

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
                                      <td class="avatar">   
<div class="flex-shrink-0">
    @if(!empty($user->avatar))
        <img src="{{ asset('uploads/users/'.$user->avatar) }}"
             alt="{{ $user->first_name }}"
             class="avatar-xs rounded-circle">
    @else
        <img src="{{ asset('uploads/users/user-img.png') }}"
             alt="Default Avatar"
             class="avatar-xs rounded-circle">
    @endif
</div>
</td> <td class="name">
										  <div class="flex-grow-1 ms-2 name">                                                
                                                <div class="flex-grow-1 ms-2 name">{{ $user->first_name }} {{ $user->last_name }}</div>
                                            </div>
                                        </td>
                                        <td class="email">{{ $user->email }}<br><span class="badge bg-info-subtle text-info p-2">{{ $user->designation_name }}</span></td>
                                        <td class="mobile">{{ $user->mobile }}</td>
	 @if(in_array($currentRoleSlug, ['root','sadmin']))  <td class="organization">{{ $user->organization_name }}</td> @endif 

                                        <td class="role_name">{{ $user->role_name }}</td>                                       
                                        <td class="date">{{ datec($user->last_login) }}</td>                                         
                                        <td class="date">{{ datec($user->created_at) }}</td>
                                        <td>
                                            <ul class="list-inline hstack gap-2 mb-0">
                                                <li class="list-inline-item" data-bs-toggle="tooltip"
                                                    data-bs-trigger="hover" data-bs-placement="top"
                                                    title="View">
                                                    <a href="user/{{ encrypt($user->rid) }}"><i
                                                            class="ri-eye-fill align-bottom text-muted"></i></a>
                                                </li>
                                                       
@php
    $isSelfEdit = ($currentUser->id == $user->id);
@endphp
@if(!$isSelfEdit)
                                                        <li class="list-inline-item" data-bs-toggle="tooltip"
                                                    data-bs-trigger="hover" data-bs-placement="top"
                                                    title="Edit">
													<a class="edit-item-btn editUser" data-id="{{ $user->rid }}"><i
                                                            class="ri-pencil-fill align-bottom text-muted"></i></a></li>
          



		 
                                                <li class="list-inline-item" data-bs-toggle="tooltip"
                                                    data-bs-trigger="hover" data-bs-placement="top"
                                                    title="Delete">
                                                    <a class="remove-item-btn deleteUser" data-id="{{ $user->rid }}" data-bs-toggle="modal"
                                                        href="#deleteRecordModal">
                                                        <i
                                                            class="ri-delete-bin-fill align-bottom text-muted"></i>
                                                    </a>
                                                </li>
 @endif
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
                    </div>
                    <div class="modal fade" id="userModal" tabindex="-1"   aria-labelledby="exampleModalLabel" aria-hidden="true">
                        <form id="userForm" method="POST" enctype="multipart/form-data">
    <div class="modal-dialog modal-dialog-centered">
                            <div class="modal-content">
                                <div class="modal-header bg-light p-3">
                                    <h5 class="modal-title" id="exampleModalLabel"></h5>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal"
                                        aria-label="Close" id="close-modal"></button>
                                </div>

	@csrf   

	<div class="modal-body">
                <input type="hidden" id="user_id" name="id">
				<div class="row g-3">
        <div class="col-lg-12">
    <div class="text-center">
        <div class="position-relative d-inline-block">
            <!-- Click Button on Avatar -->
            <div class="position-absolute bottom-0 end-0">
                <label for="avatar" class="mb-0" data-bs-toggle="tooltip" title="Select Image">
                    <div class="avatar-xs cursor-pointer">
                        <div class="avatar-title bg-light border rounded-circle text-muted">
                            <i class="ri-image-fill"></i>
                        </div>
                    </div>
                </label>

                <!-- Hidden File Input -->
                <input class="form-control d-none" type="file"
                    accept="image/png,image/jpeg,image/jpg"
                    name="avatar" id="avatar">
            </div>
             <!-- Preview Image -->
            <div class="avatar-lg p-1">
                <div class="avatar-title bg-light rounded-circle">
                    <img src="{{ URL::asset('build/images/users/user-dummy-img.jpg') }}"
                        alt="Avatar image" id="lead-img"
                        class="avatar-md rounded-circle object-fit-cover">
                </div>
            </div>
        </div>

        <div class="invalid-feedback" id="error-avatar"></div>
        <h5 class="fs-13 mt-3">Avatar</h5>
    </div>
</div>

                                       


   <div class="col-lg-6">
                                                <div>
                                                    <label for="first_name"  class="form-label">First Name</label>
                                                    <input type="text" id="first_name"
                                                        class="form-control" placeholder="Enter First Name"  name="first_name" required />
                                                </div>          <div class="invalid-feedback" id="error-firstname"></div>
                                            </div>



   <div class="col-lg-6">
                                                <div>
                                                    <label for="last_name"  class="form-label">Last Name</label>
                                                    <input type="text" id="last_name"
                                                        class="form-control" placeholder="Enter Last Name"  name="last_name" required />
                                                </div>          <div class="invalid-feedback" id="error-lastname"></div>
                                            </div>


											<!--end col-->
                                            <div class="col-lg-6">
                                                <div>
                                                    <label for="email"  class="form-label">Email</label>
                                                    <input type="text" id="email"
                                                        class="form-control" placeholder="Enter email"  name="email" required />
                                                </div>          <div class="invalid-feedback" id="error-email"></div>
                                            </div>
                                            <!--end col-->
                                            <div class="col-lg-6">
                                                <div>
                                                    <label for="mobile" class="form-label">Mobile</label>
                                                    <input type="text" id="mobile" class="form-control" placeholder="Enter mobile"  name="mobile" required />
                                                </div>          <div class="invalid-feedback" id="error-mobile"></div>
                                            </div>
											
											
                                 	 @if(in_array($currentRoleSlug, ['root','sadmin']))
                                             <div class="col-lg-6">
                                                <div>
                                                    <label for="organization" class="form-label">Organization</label>
                                                    <select class="form-control" name="organization" id="organization"  required >
                                         <option value="">Select Organization</option>
    @foreach($organizations  as $org)
                                                       <option value="{{ $org->id }}">{{ $org->name }}</option>
													@endforeach	</select>
                                                </div>          
												<div class="invalid-feedback" id="error-organization"></div>
                                            </div>
											@endif
											
											@if(in_array($currentRoleSlug, ['admin']))

@php
    $sessionOrgs = session('organization_data', []);
    $selectedOrgs = session('organizations', []); // already selected (optional)
@endphp

<div class="col-lg-6">
    <label class="form-label">Organization</label>

    <div class="border rounded p-2" style="max-height:200px; overflow-y:auto;">

        @foreach($sessionOrgs as $org)
            <div class="form-check">
                <input class="form-check-input"
                       type="checkbox"
                       name="organization[]"
                       value="{{ $org['id'] }}"
                       id="org_{{ $org['id'] }}"
                       {{ in_array($org['id'], $selectedOrgs) ? 'checked' : '' }}>

                <label class="form-check-label" for="org_{{ $org['id'] }}">
                    {{ $org['name'] }}
                </label>
            </div>
        @endforeach

    </div>

    <div class="invalid-feedback" id="error-organization"></div>
</div>

@endif



                                            <!--end col-->
                                            <div class="col-lg-6">
                                                <div>
                                                    <label for="location-field"
                                                        class="form-label">Designation</label>
                                                     <select class="form-control" name="designation" id="designation"  required >
                                                    <option value="">Select Designation</option>
		@php
    $currentOrg = session('organization');
@endphp											@foreach($designations as $des)


<option value="{{ $org['id'] }}" 
    {{ $currentOrg == $org['id'] ? 'selected' : '' }}>
    {{ $org['name'] }}
</option>													@endforeach	</select>
                                                </div>          <div class="invalid-feedback" id="error-designation"></div>
                                            </div>
                                            <!--end col-->
                                   <!-- Change Password Toggle -->
<div class="col-lg-12 mb-2">
    <button type="button" class="btn btn-sm btn-outline-secondary" id="togglePasswordBtn">
        Change Password
    </button>
</div>

<!-- Password Fields (Hidden by default) -->
<div id="passwordSection" style="display:none;">
    <div class="row g-3">
        <div class="col-lg-6">
            <label class="form-label">Password</label>
            <input type="password" name="password" id="password"
                   class="form-control" autocomplete="new-password">
        </div>

        <div class="col-lg-6">
            <label class="form-label">Confirm Password</label>
            <input type="password" name="password_confirmation"
                   id="password_confirmation"
                   class="form-control" autocomplete="new-password">
        </div>
    </div>
</div>


<!--
         <div class="col-lg-6">
                                                <div>
                                                    <label for="password" class="form-label">Password</label>
                                                 <input type="password" id="password" class="form-control" placeholder="Enter password"  name="password" required />
                                                  
                                                </div>          <div class="invalid-feedback" id="error-password"></div>
                                            </div>
											
										<div class="col-lg-6">
                                                <div>
                                                    <label for="password_confirmation" class="form-label">Confirm Password</label>
                                                  <input type="password" id="password_confirmation" class="form-control" placeholder="Enter confirm password"  name="password_confirmation" required />
                                                 
                                                </div>          <div class="invalid-feedback" id="error-password_confirmation"></div>
                                            </div>
											
		-->									<div class="col-lg-6">
                                                <div>
                                                    <label for="date-field" class="form-label">Role*</label>
                                                  <select class="form-control" name="role" id="role" required  >
                                                 
                                                    <option value="">Select Role</option>

													@foreach($roles as $roles)
                                                       <option value="{{ $roles->id }}">{{ $roles->name }}</option>
													@endforeach	 
             
													</select>
                                                
                                                
                                                
                                                
                                                  </div>          <div class="invalid-feedback" id="error-role"></div>
                                            </div>
  
											
											 <div class="col-lg-6">
                                                <div>
                                                    <label for="date-field" class="form-label">Team </label>
  <select class="form-control" name="team" id="team" required  >
                                                 
                                                    <option value="">Select Team</option>

             	@foreach($teams as $team)
                                                       <option value="{{ $team->id }}">{{ $team->name }}</option>
													@endforeach	</select>
             
              </select>
                                     


									 </div>          <div class="invalid-feedback" id="error-team"></div>
                                            </div>
											 <div class="col-lg-6">
                                                <div>
                                                    <label for="date-field" class="form-label">Status </label>
                                                    <select class="form-control" name="status"
                                                        id="statusc"   >
                                      <option value="">Select Status</option>
     <option value="1">Active</option>  
	 <option value="0">Deactive</option></select>

	</div>
												</div>
											
											
											
											
											
											<div class="showerror"></div>
											
											
											
                                         </div>
                                        <!--end row-->
                                    </div>
                                    <div class="modal-footer">
                                        <div class="hstack gap-2 justify-content-end">
                                            <button type="button" class="btn btn-light"
                                                data-bs-dismiss="modal">Close</button>
                                            <button type="submit" class="btn btn-success saveBtn"
                                                id="add-btn submitBtn saveBtn">Add User</button>
                                            {{-- <button type="button" class="btn btn-success"
                                                id="edit-btn">Update</button> --}}
                                        </div>
                                    </div>
                                </form>
                            </div>
                     </div>
                    <!--end modal-->

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
                                        <h4 class="fs-semibold">You are about to delete a organization?</h4>
                                        <p class="text-muted fs-14 mb-4 pt-1">Deleting your organization will
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
        <h5 class="offcanvas-title">Users Filters</h5>
        <button type="button" class="btn-close" data-bs-dismiss="offcanvas"></button>
    </div>


                     <form method="GET" action="{{ url('user') }}" class="d-flex flex-column justify-content-end h-100">
    <!-- 🔽 Body -->
    <div class="offcanvas-body p-0 d-flex flex-column">
        
        <!-- Scrollable content -->
        <div class="flex-grow-1 overflow-auto p-3"><!-- User ID -->
<div class="mb-3">
    <label class="form-label">User ID</label>
    <input type="text"
           name="user_id"
           class="form-control"
           placeholder="Enter User ID"
           value="{{ request('user_id') }}">
</div>

<!-- First Name -->
<div class="mb-3">
    <label class="form-label">First Name</label>
    <input type="text"
           name="first_name"
           class="form-control"
           placeholder="Enter First Name"
           value="{{ request('first_name') }}">
</div>

<!-- Last Name -->
<div class="mb-3">
    <label class="form-label">Last Name</label>
    <input type="text"
           name="last_name"
           class="form-control"
           placeholder="Enter Last Name"
           value="{{ request('last_name') }}">
</div>

<!-- Email -->
<div class="mb-3">
    <label class="form-label">Email</label>
    <input type="text"
           name="email"
           class="form-control"
           placeholder="Enter Email"
           value="{{ request('email') }}">
</div>

<!-- Mobile -->
<div class="mb-3">
    <label class="form-label">Mobile</label>
    <input type="text"
           name="mobile"
           class="form-control"
           placeholder="Enter Mobile"
           value="{{ request('mobile') }}">
</div>

<!-- Organization -->
<div class="mb-3">
<label class="form-label">Organization</label>
<select name="organization" class="form-control">
<option value="">All Organizations</option>
@foreach($organizations as $org)
<option value="{{ $org->id }}" {{ request('organization') == $org->id ? 'selected' : '' }}>
{{ $org->name }}
</option>
@endforeach
</select>
</div>


<!-- Role -->
  


<!-- Designation -->
<div class="mb-3">
<label class="form-label">Designation</label>
<select name="designation" class="form-control">
<option value="">All Designations</option>
@foreach($designations as $d)
<option value="{{ $d->id }}" {{ request('designation') == $d->id ? 'selected' : '' }}>
{{ $d->name }}
</option>
@endforeach
</select>
</div>


<!-- Team -->
<div class="mb-3">
<label class="form-label">Team</label>
<select name="team" class="form-control">
<option value="">All Teams</option>
@foreach($teams as $t)
<option value="{{ $t->id }}" {{ request('team') == $t->id ? 'selected' : '' }}>
{{ $t->name }}
</option>
@endforeach
</select>
</div>


<!-- Status -->
<div class="mb-3">
<label class="form-label">Status</label>
<select name="status" class="form-control">
<option value="">All</option>
<option value="1" {{ request('status') === '1' ? 'selected' : '' }}>Active</option>
<option value="0" {{ request('status') === '0' ? 'selected' : '' }}>Inactive</option>
</select>
</div>

 <div class="mb-3">

                <a href="{{ url('users') }}" class="btn btn-light w-40">Clear Filter</a>
                <button type="submit" class="btn btn-success w-50">Apply Filters</button>
            </div>
			 <div class="mb-3">
&nbsp;</div>
        </div>
        </div>
 
</form>


     </div>
                    <!--end offcanvas-->

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
 <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
         	    <script src="{{ URL::asset('build/js/pages/crm-leads.init.js') }}"></script>


 <script>
    $(document).ready(function () {

    // 🔹 Add User Modal Open
    $('#create-btn').on('click', function () {
    $('#userForm')[0].reset();  // normal fields reset
    $('#user_id').val('');      // hidden id reset
	 $('#lead-img').attr('src', '{{ asset('build/images/users/user-dummy-img.jpg') }}');
  $('#exampleModalLabel').text('Add User');
    $('.saveBtn').text('Add User');

    // Password visible & required
    $('#passwordSection').show();
    $('#togglePasswordBtn').hide();

    $('#password').prop('required', true);
    $('#password_confirmation').prop('required', true);
	
	
    // All select2 dropdown reset
    $('#organization').val('').trigger('change');
    $('#designation').val('').trigger('change');
    $('#role').val('').trigger('change');
    $('#status').val('').trigger('change');
    $('#team').val('').trigger('change');
 
 
	
	
          $('#userModal').modal('show');
    });

    // 🔹 Edit User Modal Open
  // EDIT USER
$(document).on('click', '.editUser', function () {

    var id = $(this).data('id');

    $('#userForm')[0].reset();
    $('#user_id').val(id); // 🔑 EDIT MODE

    $('#exampleModalLabel').text('Edit User');
    $('.saveBtn').text('Update User');
    $('.saveBtn').val('Update User');

    // Password hidden by default
    $('#passwordSection').hide();
    $('#togglePasswordBtn').show().text('Change Password');

    $('#password').val('').prop('required', false);
    $('#password_confirmation').val('').prop('required', false);

    // avatar default
    $('#lead-img').attr('src', "{{ asset('build/images/users/user-dummy-img.jpg') }}");

    // fetch user data
    $.ajax({
        url: '{{ url('user/get') }}/' + id,
        type: 'GET',
        success: function (response) {

            $('#first_name').val(response.first_name);
            $('#last_name').val(response.last_name);
            $('#email').val(response.email);
            $('#mobile').val(response.mobile);
             $('#organization').val(response.organization).trigger('change');
             setTimeout(function () {
                $('#designation').val(response.designation).trigger('change');
                $('#team').val(response.team).trigger('change');
                $('#role').val(response.role).trigger('change');
                $('#statusc').val(response.status).trigger('change');
            }, 300);

            if (response.avatar) {
                $('#lead-img').attr(
                    'src',
                    "{{ asset('uploads/users') }}/" + response.avatar
                );
            }

            $('#userModal').modal('show');
        }
    });
});

  $('#togglePasswordBtn').on('click', function () {

    $('#passwordSection').slideToggle();

    const visible = $('#passwordSection').is(':visible');

    $('#password').prop('required', visible);
    $('#password_confirmation').prop('required', visible);

    $(this).text(visible ? 'Cancel Password Change' : 'Change Password');

    if (!visible) {
        $('#password').val('');
        $('#password_confirmation').val('');
    }
});
    // 🔹 Add / Update User AJAX
    $('#userForm').on('submit', function (e) {
        e.preventDefault();

        var id = $('#user_id').val();
        var formData = new FormData(this);
        var url = '';
        var method = 'POST';

        if (id === '') {
            url = "{{ route('User.AddUser') }}"; // Add
        } else {
            url = "{{ url('user/edit') }}/" + id; // Update
        }

        $.ajax({
            url: url,
            method: method,
            data: formData,
            processData: false,
            contentType: false,

            success: function (response) {
                if (response.status === 'success') {
                    Swal.fire({
                        icon: 'success',
                        title: 'Success',
                        text: response.message,
                        timer: 2000,
                        showConfirmButton: false
                    }).then(() => location.reload());
                }
            },

            error: function (xhr) {

                // ✅ Validation errors
                if (xhr.status === 422 && xhr.responseJSON?.errors) {

                    let errors = xhr.responseJSON.errors;
                    let errorHtml = '';

                    Object.keys(errors).forEach(function (key) {
                        errorHtml += `<p>• ${errors[key][0]}</p>`;
                    });

                    Swal.fire({
                        icon: 'error',
                        title: 'Validation Error',
                        html: errorHtml
                    });

                } else {

                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: 'Something went wrong while saving user.'
                    });

                }
            }
        });
    });

});</script>

 @endsection
