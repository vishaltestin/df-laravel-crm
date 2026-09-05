@extends('FrontEnd.layouts.master')
@section('title', 'Designations')
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
            Designations
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
                                
                                <button type="button" class="btn btn-success add-btn"  id="addNewDesignation"  ><i class="ri-add-line align-bottom me-1"></i> Add Designations</button>
                               
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
                                         <th class="sort" data-sort="created_at">Create Date</th>
                                        <th class="sort" data-sort="action">Action</th>
                                    </tr>
                                </thead>
                                <tbody class="list form-check-all">
								@foreach($designations  as $deg)

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
                                            
                                                <div class="flex-grow-1 ms-2 name">{{ $deg->name }}</div>
                                            </div>
                                        </td>
                                             <td>
                                            <div class="d-flex align-items-center">
                                            
                                                <div class="flex-grow-1 ms-2 organization_name">{{ $deg->organization->name }}</div>
                                            </div>
                                        </td>
                                        <td class="gst">
                                           <div class="d-flex align-items-center">
                                            
                                                <div class="flex-grow-1 ms-2 created_at">{{ $deg->created_at }}</div>
                                            </div>
											</td>
                                         <td>
                                            <ul class="list-inline hstack gap-2 mb-0">
                                                
                                                
                                               <li class="list-inline-item" title="View">
    <a href="javascript:void(0)" onclick="viewDesignation('{{ encrypt($deg->id) }}')">
        <i class="ri-eye-fill align-bottom text-muted"></i>
    </a>
</li>
                            
                                         <li class="list-inline-item" data-bs-toggle="tooltip"
                                                    data-bs-trigger="hover" data-bs-placement="top"
                                                    title="Edit"> <a class="edit-item-btn editDesignation"    data-id="{{ $deg->id }}" ><i class="ri-pencil-fill align-bottom text-muted"></i></a>
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
                    </div>
                    <div class="modal fade" id="designationModal" tabindex="-1"
                        aria-labelledby="designationModalLabel" aria-hidden="true">
                        <div class="modal-dialog modal-dialog-centered">
                            <div class="modal-content">
                                <div class="modal-header bg-light p-3">
          <h5 class="modal-title" id="designationModalLabel">Add Designation</h5>
		  <button type="button" class="btn-close" data-bs-dismiss="modal"    aria-label="Close" id="close-modal"></button>
                                </div>
                                <form class="tablelist-form"  id="designationForm"  autocomplete="off" enctype="multipart/form-data">
                                  @csrf    <div class="modal-body">
                                         <div class="row g-3">
                                            <div class="col-lg-12">
                                               <input type="hidden" id="designation_id" value="">
                                                <div>
                                                    <label for="name"
                                                        class="form-label">Designation Name</label>
                                                    <input type="text" id="name"   class="form-control" placeholder="Enter Name"
                                                        required name="name">
                                                </div>
                                            </div>
                                            <!--end col-->
                                            <div class="col-lg-6">
                                                <div>
                                                    <label for="torganization_id" class="form-label">Organization</label>
                                                    <select class="form-control" name="organization_id"  id="organization_id"   >
                                                    <option value="">Select</option>  @foreach($organizations as $organization)
        <option value="{{ $organization->id }}">{{ $organization->name }}</option>
    @endforeach	
                                                    </select>
                                                </div>
                                            </div>
											 <div class="col-lg-6">
                                                <div>
                                                    <label for="statusf" class="form-label">Status </label>
                                                    <select class="form-control" name="status" id="statusf" >
                                                         <option value="1">Active</option>
     <option value="0">Deactive</option>
</select>

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
                                            <button type="submit" class="btn btn-success"
                                                id="add-btn  saveBtn">Add Designation</button>
                                            {{-- <button type="button" class="btn btn-success"
                                                id="edit-btn">Update</button> --}}
                                        </div>
                                    </div>
                                </form>
                            </div>
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


                    <div class="offcanvas offcanvas-end" tabindex="-1" id="offcanvasExample"   aria-labelledby="offcanvasExampleLabel">
                        <div class="offcanvas-header bg-light">
                            <h5 class="offcanvas-title" id="offcanvasExampleLabel"> </h5>
                            <button type="button" class="btn-close text-reset"
                                data-bs-dismiss="offcanvas" aria-label="Close"></button>
                        </div>
                        <!--end offcanvas-header-->
                        <form action="" class="d-flex flex-column justify-content-end h-100">
                            <div class="offcanvas-body">
                                  <div class="mb-3">
    <label class="form-label text-muted">Designation ID</label>
    <input type="text" class="form-control" id="view-id" readonly>
</div>

<div class="mb-3">
    <label class="form-label text-muted">Designation Name</label>
    <input type="text" class="form-control" id="view-name" readonly>
</div>
<div class="mb-3">

  <label class="form-label text-muted">Organization Name</label>
    <input type="text" class="form-control" id="view-organization" readonly>
</div>
<div class="mb-3">
    <label class="form-label text-muted">Created Date</label>
    <input type="text" class="form-control" id="view-date" readonly>
</div>
                             </div>
                            <!--end offcanvas-body-->
                            <div class="offcanvas-footer border-top p-3 text-center hstack gap-2">
                           <button type="button" class="btn btn-success w-100" data-bs-dismiss="offcanvas">
    Close
</button>
                            </div>
                            <!--end offcanvas-footer-->
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
     	    <script src="{{ URL::asset('build/js/pages/crm-leads.init.js') }}"></script>

    
	<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>


$(document).ready(function () {

    // open modal for add
    $('#addNewDesignation').on('click', function () {
        $('#designationForm')[0].reset();
        $('#designation_id').val('');
        $('#designationModalLabel').text('Add Designation');
        $('#saveBtn').text('Add Designation');
        $('#designationModal').modal('show');
    });

 // open modal for edit
$(document).on('click', '.editDesignation', function () {
    var id = $(this).data('id');
    $.ajax({
        url: "{{ url('designations') }}/" + id + "/edit",
        method: 'GET',
        success: function (response) {
            $('#designation_id').val(response.id);
            $('#name').val(response.name);
          $('#organization_id')
    .val(response.organization.id)
    .trigger('change');

            $('#statusf').val(response.status);
			$("#statusf").val(response.status).change();

            $('#designationModalLabel').text('Edit Designation');
            $('#saveBtn').text('Update Designation');
            $('#designationModal').modal('show');
        },
        error: function (xhr) {
            console.log(xhr.responseText);
        }
    });
});


    // save (add/update)
    $('#designationForm').on('submit', function (e) {
        e.preventDefault();
        var id = $('#designation_id').val();
        var formData = new FormData(this);
        var url = '';
        var method = 'POST';

        if (id === '') {
            url = "{{ route('Designation.Store') }}"; // add
        } else {
            url = "{{ url('designations') }}/" + id; // update
            formData.append('_method', 'PUT');
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
        text: response.message ?? 'Designation loaded successfully'
    });
                    $('#designationModal').modal('hide');
                    $('#designationForm')[0].reset();
                    // reload table (optional)
                    location.reload();
                } else {
                    alert('Error: ' + response.message);
                }
            },
            error: function (xhr) {
                console.log(xhr.responseText);
            }
        });
    });



});

function viewDesignation(id) {

    $.ajax({
        url: "{{ url('designations') }}/" + id,   // show() route
        type: "GET",
        success: function (response) {

            // ---- DATA FILL ----
            $("#view-id").val(response.id);
            $("#view-name").val(response.name);
			$("#offcanvasExampleLabel").text(response.name);
			$("#offcanvasExampleLabel").val(response.name);
			    $("#view-organization").val(response.organization_name);
            $("#view-date").val(response.created_at);

            // ---- OFFCANVAS OPEN ----
            const offcanvasEl = document.getElementById('offcanvasExample');
    const offcanvasInstance = bootstrap.Offcanvas.getOrCreateInstance(offcanvasEl);
    offcanvasInstance.show();
        },
        error: function () {
            alert("Something went wrong!");
        }
    });
}

</script>
@endsection
