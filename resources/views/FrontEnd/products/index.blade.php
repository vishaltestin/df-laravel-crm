@extends('FrontEnd.layouts.master')
@section('title', 'Courses')
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
            Courses
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
                                
								
<button type="button" class="btn btn-primary" id="openAddCoursePopup">
    + Add Courses
</button>
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
										<th class="sort" data-sort="sku">SKU</th>
										<th class="sort" data-sort="name">Name</th>
										<th class="sort" data-sort="description">Description</th>
										<th class="sort" data-sort="program">Program</th>
										<th class="sort" data-sort="qty">Qty</th>
										<th class="sort" data-sort="price">Price</th>
										<th width="180px">Action</th>
									</tr>
								</thead>

                                <tbody class="list form-check-all">
									@foreach($products as $p)
									<tr>
                                        <th scope="row">
                                            <div class="form-check">
                                                <input class="form-check-input" type="checkbox"   name="chk_child" value="option1">
                                            </div>
                                        </th>
                                        <td class="id" style="display:none;"><a
                                                href="javascript:void(0);"
                                                class="fw-medium link-primary">#VZ2101</a></td>
                                        <td class="sku">
                                            <div class="d-flex align-items-center">                                                
                                                <div class="flex-grow-1 ms-2 sku">{{ $p->sku }} </div>
                                        </td>
                     <td class="name">{{ $p->name }}</td>
                     <td class="description">{{ $p->description }}</td>
                     <td class="program">  {{ $p->program ? $p->program->name : '-' }}</td>
                    <td class="qty">{{ $p->quantity }}</td>
                    <td class="price">{{ $p->price }}</td>

                    <td>
                     <button class="btn btn-info btn-sm viewCourse" data-id="{{ $p->id }}">View</button>

<button class="btn btn-warning btn-sm editCourse" data-id="{{ $p->id }}">Edit</button>

<button class="btn btn-danger btn-sm deleteBtn" data-id="{{ $p->id }}">Delete</button>

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
</div>


                    </div>
 
                </div>
            </div>

        </div>
        <!--end col-->
    </div>
    <!--end row-->
<div class="modal fade" id="courseModal" tabindex="-1">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
      <form id="courseForm">
        @csrf
        <div class="modal-header">
          <h5 class="modal-title" id="courseModalTitle">Add Course</h5>
          <button class="btn-close" data-bs-dismiss="modal"></button>
        </div>

        <div class="modal-body">
          <input type="hidden" name="id" id="course_id">

          <div class="row">
            <div class="col-md-6 mb-2">
              <label>SKU</label>
              <input class="form-control" name="sku" id="course_sku" required>
            </div>

            <div class="col-md-6 mb-2">
              <label>Name</label>
              <input class="form-control" name="name" id="course_name" required>
            </div>

            <div class="col-md-6 mb-2">
              <label>Program</label>
              <select class="form-select" name="program_id" id="course_program">
                <option value="">Select Program</option>
              </select>
            </div>

            <div class="col-md-3 mb-2">
              <label>Quantity</label>
              <input class="form-control" name="quantity" id="course_quantity" type="number" required>
            </div>

            <div class="col-md-3 mb-2">
              <label>Price</label>
              <input class="form-control" name="price" id="course_price" type="number" required>
            </div>

            <div class="col-12 mb-2">
              <label>Description</label>
              <textarea class="form-control" name="description" id="course_description"></textarea>
            </div>

            <div class="col-12">
              <label>Status</label>
              <select class="form-select" name="status" id="course_status">
                <option value="1">Active</option>
                <option value="0">Inactive</option>
              </select>
            </div>
          </div>
        </div>

        <div class="modal-footer">
          <button class="btn btn-secondary" type="button" data-bs-dismiss="modal">Close</button>
          <button class="btn btn-primary" id="courseSaveBtn">Save Course</button>
        </div>
      </form>
    </div>
  </div>
</div>
<div class="modal fade" id="courseConsole" tabindex="-1">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">

      <form id="courseConsoleForm">
        @csrf
        <input type="hidden" id="cc_id" name="id">

        <div class="modal-header">
          <h5 class="modal-title" id="cc_title">Course</h5>
          <button class="btn-close" data-bs-dismiss="modal"></button>
        </div>

        <div class="modal-body">

          <div class="row">
            <div class="col-md-6 mb-2">
              <label>SKU</label>
              <input class="form-control" name="sku" id="cc_sku">
            </div>

            <div class="col-md-6 mb-2">
              <label>Name</label>
              <input class="form-control" name="name" id="cc_name">
            </div>

            <div class="col-md-6 mb-2">
              <label>Program</label>
              <select class="form-select" name="program_id" id="cc_program"></select>
            </div>

            <div class="col-md-3 mb-2">
              <label>Qty</label>
              <input class="form-control" name="quantity" id="cc_quantity" type="number">
            </div>

            <div class="col-md-3 mb-2">
              <label>Price</label>
              <input class="form-control" name="price" id="cc_price" type="number">
            </div>

            <div class="col-12 mb-2">
              <label>Description</label>
              <textarea class="form-control" name="description" id="cc_description"></textarea>
            </div>

            <div class="col-12">
              <label>Status</label>
              <select class="form-select" name="status" id="cc_status">
                <option value="1">Active</option>
                <option value="0">Inactive</option>
              </select>
            </div>

          </div>
        </div>

        <div class="modal-footer">
          <button class="btn btn-secondary" type="button" data-bs-dismiss="modal">Close</button>
          <button class="btn btn-primary" id="cc_save">Update</button>
        </div>
      </form>

    </div>
  </div>
</div>

 @endsection
@section('script')
    <script src="{{ URL::asset('build/libs/list.js/list.min.js') }}"></script>
    <script src="{{ URL::asset('build/libs/list.pagination.js/list.pagination.min.js') }}"></script>
    <script src="{{ URL::asset('build/libs/sweetalert2/sweetalert2.min.js') }}"></script>
   		<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
   	    <script src="{{ URL::asset('build/js/pages/crm-leads.init.js') }}"></script>

    
<script>
$(function(){

    const ccModal = new bootstrap.Modal(document.getElementById('courseConsole'));

    /* =========================
     * LOAD ALL PROGRAMS
     * ========================= */
    function loadPrograms(selected = null){
        $.get('{{ url("programs/all") }}', function(res){

            const programs = Array.isArray(res) ? res : [];
            const $sel = $('#cc_program');

            $sel.html('<option value="">Select Program</option>');
            programs.forEach(p=>{
                $sel.append(`<option value="${p.id}">${p.name}</option>`);
            });

            if(selected) $sel.val(selected);
        });
    }

    /* =========================
     * VIEW COURSE
     * ========================= */
    $(document).on('click','.viewCourse', function(){

        const id = $(this).data('id');

        $.get(`{{ url("course") }}/${id}/json`, function(res){

            if(!res.status){
                return Swal.fire('Error','Course not found','error');
            }

            fillConsole(res.data, true);
            $('#cc_title').text('View Course');
            $('#cc_save').hide();

            ccModal.show();
        });
    });

    /* =========================
     * EDIT COURSE
     * ========================= */
    $(document).on('click','.editCourse', function(){

        const id = $(this).data('id');

        $.get(`{{ url("course") }}/${id}/json`, function(res){

            if(!res.status){
                return Swal.fire('Error','Course not found','error');
            }

            fillConsole(res.data, false);
            $('#cc_title').text('Edit Course');
            $('#cc_save').show();

            ccModal.show();
        });
    });

    /* =========================
     * FILL MODAL
     * ========================= */
    function fillConsole(data, readOnly){

        $('#cc_id').val(data.id);
        $('#cc_sku').val(data.sku).prop('disabled', readOnly);
        $('#cc_name').val(data.name).prop('disabled', readOnly);
        $('#cc_quantity').val(data.quantity).prop('disabled', readOnly);
        $('#cc_price').val(data.price).prop('disabled', readOnly);
        $('#cc_description').val(data.description).prop('disabled', readOnly);
        $('#cc_status').val(data.status).prop('disabled', readOnly);

        loadPrograms(data.program_id);
        $('#cc_program').prop('disabled', readOnly);
    }

    /* =========================
     * UPDATE COURSE
     * ========================= */
    $('#cc_save').on('click', function(e){
        e.preventDefault();

        const id = $('#cc_id').val();

        $.ajax({
			url: `{{ url("products") }}/${id}/update`,
            type: "POST",
            data: $('#courseConsoleForm').serialize(),
            success: function(res){
                if(res.status === 'success'){
                    ccModal.hide();
                    Swal.fire('Updated','Course updated','success');
                    location.reload(); // or update row dynamically
                }
            },
            error: function(){
                Swal.fire('Error','Update failed','error');
            }
        });
    });

});
</script>

<script>
$(function(){

    const courseModal = new bootstrap.Modal(document.getElementById('courseModal'));

    /* ===============================
     *  OPEN COURSE POPUP
     * =============================== */
    $('#openAddCoursePopup').on('click', function(){

        // reset form
        $('#courseForm')[0].reset();
        $('#course_id').val('');
        $('#courseModalTitle').text('Add Course');

        // load programs
        loadProgramsForCourse();

        courseModal.show();
    });

    /* ===============================
     *  LOAD PROGRAMS (ALL)
     * =============================== */
function loadProgramsForCourse(){

    $.get('{{ url("programs/all") }}', function(res){

        // ✅ DIRECT ARRAY
        const programs = Array.isArray(res) ? res : [];

        const $select = $('#course_program');
        $select.html('<option value="">Select Program</option>');

        programs.forEach(p => {
            $select.append(`<option value="${p.id}">${p.name}</option>`);
        });
    });
}


    /* ===============================
     *  SAVE COURSE (AJAX)
     * =============================== */
    $('#courseSaveBtn').on('click', function(e){
        e.preventDefault();

        $.ajax({
            url: "{{ route('products.store') }}",
            type: "POST",
            data: $('#courseForm').serialize(),
            success: function(res){
                if(res.status === 'success'){
                    courseModal.hide();
                    Swal.fire('Saved','Course added successfully','success');

                    // OPTIONAL: reload table or append row
                    // location.reload();
                }
            },
            error: function(xhr){
                let msg = 'Something went wrong';
                if(xhr.responseJSON && xhr.responseJSON.errors){
                    msg = Object.values(xhr.responseJSON.errors).flat().join('<br>');
                }
                Swal.fire('Error', msg, 'error');
            }
        });
    });

});

$(document).on('click','.deleteBtn', function(){
    const id = $(this).data('id');

    Swal.fire({
        title: 'Delete?',
        text: 'This course will be removed',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonText: 'Yes, delete'
    }).then((res)=>{
        if(res.isConfirmed){
            $.post(`{{ url("course/delete") }}/${id}`, {_token:'{{ csrf_token() }}'}, function(r){
                if(r.status === 'success'){
                    Swal.fire('Deleted','Course removed','success');
                    $(`tr[data-id="${id}"]`).remove();
                }
            });
        }
    });
});

</script>

@endsection
