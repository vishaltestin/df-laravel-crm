@extends('FrontEnd.layouts.master')

@section('title','Institute Manager')

@section('css')
<link href="{{ URL::asset('build/libs/sweetalert2/sweetalert2.min.css') }}" rel="stylesheet">
<style>
.canvas-card { padding:20px; background:#fff; border-radius:8px; box-shadow:0 6px 18px rgba(0,0,0,0.06); }
.list-item { cursor:pointer; }
.list-item.active { background: rgba(14,144,217,0.06); border-left:4px solid #0E90D9; }
.small-muted { color:#6c757d; font-size:13px; }
</style>
@endsection

@section('content')
<div class="row">
    <div class="col-12">
        <div class="card p-3">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <h4 class="mb-0">Institute Manager</h4>
                <div>
                    <button class="btn btn-primary" id="openAddInstitute">+ Add Institute</button>
                </div>
            </div>

            <div class="row g-3">
                <div class="col-md-3">
                    <div class="canvas-card" style="min-height:420px;">
                        <h6>Institutes</h6>
                        <ul class="list-group mt-2" id="instituteList">
                            @foreach($institutes as $inst)
                                <li class="list-group-item list-item" data-id="{{ $inst->id }}">
                                    <div class="d-flex justify-content-between">
                                        <div>
                                            <strong>{{ $inst->name }}</strong><br>
                                            <span class="small-muted">#{{ $inst->id }}</span>
                                        </div>
                                        <div class="text-end">
                                            <button class="btn btn-sm btn-light edit-inst" data-id="{{ $inst->id }}">Edit</button>
                                            <button class="btn btn-sm btn-danger del-inst" data-id="{{ $inst->id }}">Del</button>
                                        </div>
                                    </div>
                                </li>
                            @endforeach
                        </ul>
                    </div>
                </div>

                <div class="col-md-4">
                    <div class="canvas-card" style="min-height:420px;">
                        <div class="d-flex justify-content-between">
                            <h6 id="progHeading">Programs/Faculty</h6>
                            <button class="btn btn-sm btn-warning" id="openAddProgram">+ Add Program/Faculty</button>
                        </div>

                        <div id="programList" class="mt-2">
                            <div class="small-muted">Select an institute to view programs/Faculty</div>
                        </div>
                    </div>
                </div>

                <div class="col-md-5">
                    <div class="canvas-card" style="min-height:420px;">
                        <div class="d-flex justify-content-between align-items-center">
                            <h6>Courses</h6>
                            <div>
                                <button class="btn btn-sm btn-success" id="openAddCourse">+ Add Course</button>
                            </div>
                        </div>

                        <div class="mt-2">
                            <div class="small-muted mb-2">Assign course to selected program</div>
                            <table class="table table-sm">
                                <thead><tr><th>Course</th><th>Program</th><th style="width:180px">Action</th></tr></thead>
                                <tbody id="coursesTable">
                                    @foreach($products as $p)
                                    <tr data-id="{{ $p->id }}" data-program-id="{{ $p->program_id ?? '' }}">
                                        <td>{{ $p->name }}</td>
                                        <td class="course-program">
                                            {{ $p->program ? $p->program->name : '-' }}
                                        </td>
                                        <td>
                                            <select class="form-select form-select-sm assign-program-select d-inline-block" style="width:40%;" data-course="{{ $p->id }}">
                                                <option value="">-- Program --</option>
                                            </select>

                                            <button class="btn btn-sm btn-primary edit-course" data-id="{{ $p->id }}">Edit</button>
                                            <button class="btn btn-sm btn-danger del-course" data-id="{{ $p->id }}">Del</button>
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>

                    </div>
                </div>

            </div>
        </div>
    </div>
</div>

<!-- Institute Modal -->
<div class="modal fade" id="instModal" tabindex="-1">
  <div class="modal-dialog">
    <div class="modal-content">
      <form id="instForm">
      <div class="modal-header"><h5 class="modal-title" id="instModalTitle">Add Institute</h5><button class="btn-close" data-bs-dismiss="modal"></button></div>
      <div class="modal-body">
            <input type="hidden" name="id" id="inst_id">
            <input type="hidden" name="organization_id" id="inst_org" value="{{ $orgId }}">
            <div class="mb-2">
                <label>Name</label>
                <input class="form-control" name="name" id="inst_name" required>
            </div>
            <div class="mb-2">
                <label>Description</label>
                <textarea class="form-control" name="description" id="inst_description"></textarea>
            </div>
      </div>
      <div class="modal-footer">
        <button class="btn btn-secondary" type="button" data-bs-dismiss="modal">Close</button>
        <button class="btn btn-primary" id="instSave">Save</button>
      </div>
      </form>
    </div>
  </div>
</div>

<!-- Program Modal -->
<div class="modal fade" id="progModal" tabindex="-1">
  <div class="modal-dialog">
    <div class="modal-content">
      <form id="progForm">
      <div class="modal-header"><h5 class="modal-title" id="progModalTitle">Add Program</h5><button class="btn-close" data-bs-dismiss="modal"></button></div>
      <div class="modal-body">
            <input type="hidden" name="id" id="prog_id">
            <div class="mb-2">
                <label>Institute</label>
                <select class="form-select" name="institute_id" id="prog_institute" required>
                    <option value="">Select Institute</option>
                    @foreach($institutes as $inst)
                        <option value="{{ $inst->id }}">{{ $inst->name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="mb-2">
                <label>Name</label>
                <input class="form-control" name="name" id="prog_name" required>
            </div>
            <div class="mb-2">
                <label>Description</label>
                <textarea class="form-control" name="description" id="prog_description"></textarea>
            </div>
      </div>
      <div class="modal-footer">
        <button class="btn btn-secondary" type="button" data-bs-dismiss="modal">Close</button>
        <button class="btn btn-primary" id="progSave">Save</button>
      </div>
      </form>
    </div>
  </div>
</div>

<!-- Course Modal -->
<div class="modal fade" id="courseModal" tabindex="-1">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
      <form id="courseForm">
      <div class="modal-header"><h5 class="modal-title" id="courseModalTitle">Add Course</h5><button class="btn-close" data-bs-dismiss="modal"></button></div>
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
        <button class="btn btn-primary" id="courseSave">Save Course</button>
      </div>
      </form>
    </div>
  </div>
</div>

@endsection

@section('script')
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="{{ URL::asset('build/libs/sweetalert2/sweetalert2.min.js') }}"></script>

<script>
$(function(){
    const instModal = new bootstrap.Modal(document.getElementById('instModal'));
    const progModal = new bootstrap.Modal(document.getElementById('progModal'));
    const courseModal = new bootstrap.Modal(document.getElementById('courseModal'));

    let selectedInstitute = null;

    // ---------- helpers ----------
  function fetchProgramsForSelect(selectedProgramId = null){
    $.get('{{ url("programs/all") }}', function(allPrograms){
        $('#course_program').html('<option value="">Select Program</option>');
        allPrograms.forEach(p => {
            $('#course_program').append(`<option value="${p.id}">${p.name}</option>`);
        });
        if(selectedProgramId) $('#course_program').val(selectedProgramId);
    });
}


    function refreshCourseRow(product){
        // update or append row in courses table
        const tr = $(`tr[data-id="${product.id}"]`);
        if(tr.length){
            tr.find('td:first').text(product.name);
            tr.attr('data-program-id', product.program_id || '');
            tr.find('.course-program').text(product.program ? product.program.name : '-');
        } else {
            // append new
            $('#coursesTable').prepend(`
                <tr data-id="${product.id}" data-program-id="${product.program_id ?? ''}">
                    <td>${product.name}</td>
                    <td class="course-program">${product.program ? product.program.name : '-'}</td>
                    <td>
                        <select class="form-select form-select-sm assign-program-select d-inline-block" style="width:40%;" data-course="${product.id}">
                            <option value="">-- Program --</option>
                        </select>
                        <button class="btn btn-sm btn-primary edit-course" data-id="${product.id}">Edit</button>
                        <button class="btn btn-sm btn-danger del-course" data-id="${product.id}">Del</button>
                    </td>
                </tr>
            `);
            // fill selects
            fillCourseProgramDropdown([]);
        }
    }

    // ---------- Institute CRUD (same as earlier) ----------
    $('#openAddInstitute').on('click', function(){ $('#instForm')[0].reset(); $('#inst_id').val(''); $('#instModalTitle').text('Add Institute'); instModal.show(); });
    $('#instSave').on('click', function(e){ e.preventDefault();
        const id = $('#inst_id').val();
        const payload = { organization_id: $('#inst_org').val(), name: $('#inst_name').val(), description: $('#inst_description').val(), _token:'{{ csrf_token() }}' };
        if(!payload.name){ Swal.fire('Error','Name required','error'); return; }
        const url = id ? '{{ url("institute/update") }}/'+id : '{{ url("institute/store") }}';
        $.post(url, payload, function(res){ if(res.status !== 'success') return; if(!id){ $('#instituteList').prepend(`<li class="list-group-item list-item" data-id="${res.data.id}"><div class="d-flex justify-content-between"><div><strong>${res.data.name}</strong><br><span class="small-muted">#${res.data.id}</span></div><div class="text-end"><button class="btn btn-sm btn-light edit-inst" data-id="${res.data.id}">Edit</button> <button class="btn btn-sm btn-danger del-inst" data-id="${res.data.id}">Del</button></div></div></li>`); $('#prog_institute').append(`<option value="${res.data.id}">${res.data.name}</option>`); } else { $(`#instituteList li[data-id="${id}"] strong`).text(res.data.name); $(`#prog_institute option[value="${id}"]`).text(res.data.name); } instModal.hide(); Swal.fire('Saved','Institute saved','success'); });
    });
    $(document).on('click','.edit-inst', function(){ const id = $(this).data('id'); $.get('{{ url("institute") }}/'+id, function(res){ $('#inst_id').val(res.id); $('#inst_name').val(res.name); $('#inst_description').val(res.description ?? ''); $('#instModalTitle').text('Edit Institute'); instModal.show(); }); });
    $(document).on('click','.del-inst', function(){ const id = $(this).data('id'); if(!confirm('Delete Institute?')) return; $.post('{{ url("institute/delete") }}/'+id, {_token:'{{ csrf_token() }}'}, function(res){ if(res.status==='success'){ $(`#instituteList li[data-id="${id}"]`).remove(); $('#programList').html('<div class="small-muted">Select an institute to view programs</div>'); Swal.fire('Deleted','Institute removed','success'); } }); });

    // ---------- Programs ----------
    $('#openAddProgram').on('click', function(){ $('#progForm')[0].reset(); $('#prog_id').val(''); $('#progModalTitle').text('Add Program'); if(selectedInstitute) $('#prog_institute').val(selectedInstitute); progModal.show(); });
    $('#progSave').on('click', function(e){ e.preventDefault(); const id=$('#prog_id').val(); const payload={ institute_id: $('#prog_institute').val(), name: $('#prog_name').val(), description: $('#prog_description').val(), _token:'{{ csrf_token() }}' }; if(!payload.institute_id){ Swal.fire('Error','Select Institute','error'); return; } if(!payload.name){ Swal.fire('Error','Program name required','error'); return; } const url = id ? '{{ url("program/update") }}/'+id : '{{ url("program/store") }}'; $.post(url,payload,function(res){ if(res.status !== 'success') return; if(!id && selectedInstitute==res.data.institute_id){ $('#programList').prepend(renderProgramItem(res.data)); } else if(id){ $(`#programList .program-item[data-id="${id}"] .prog-name`).text(res.data.name); } progModal.hide(); Swal.fire('Saved','Program saved','success'); }); });
    function renderProgramItem(p){ return `<div class="program-item p-2 border mb-2" data-id="${p.id}"><div class="d-flex justify-content-between"><div><strong class="prog-name">${p.name}</strong><br><span class="small-muted">#${p.id}</span></div><div><button class="btn btn-sm btn-light edit-prog" data-id="${p.id}">Edit</button> <button class="btn btn-sm btn-danger del-prog" data-id="${p.id}">Del</button></div></div></div>`; }
    $(document).on('click','.edit-prog', function(){ const id=$(this).data('id'); $.get('{{ url("program") }}/'+id,function(res){ $('#prog_id').val(res.id); $('#prog_name').val(res.name); $('#prog_description').val(res.description); $('#prog_institute').val(res.institute_id); $('#progModalTitle').text('Edit Program'); progModal.show(); }); });
    $(document).on('click','.del-prog', function(){ const id=$(this).data('id'); if(!confirm('Delete Program?')) return; $.post('{{ url("program/delete") }}/'+id,{_token:'{{ csrf_token() }}'},function(res){ if(res.status==='success'){ $(`#programList .program-item[data-id="${id}"]`).remove(); Swal.fire('Deleted','Program removed','success'); } }); });

    // ---------- clicking institute loads programs (single clean load) ----------
  //  $(document).on('click','#instituteList', function(){
   //     $('#instituteList').removeClass('active');
   //     $(this).addClass('active');
   //     selectedInstitute = $(this).data('id');
   //     loadPrograms(selectedInstitute);
   // });
    $(document).on('click', '.list-item', function(){
        $(' .list-item').removeClass('active');
        $(this).addClass('active');
        selectedInstitute = $(this).data('id');
		
		//alert(selectedInstitute);
        loadPrograms(selectedInstitute);
    });
    function loadPrograms(instId){
    $('#programList').html('<div class="small-muted">Loading...</div>');

    $.get(`{{ url("institute") }}/${instId}/programs`, function(res){

        // response {status, data}
        const programs = res.data || [];

        if(!programs.length){
            $('#programList').html('<div class="small-muted">No programs found</div>');
            return;
        }

        let html = '';
        programs.forEach(p => html += renderProgramItem(p));
        $('#programList').html(html);

        // ❌ NO all-program calls here
    })
    .fail(function(){
        $('#programList').html('<div class="small-muted">Failed to load</div>');
    });
}


    // ---------- Courses (Products) CRUD ----------
    $('#openAddCourse').on('click', function(){
        $('#courseForm')[0].reset();
        $('#course_id').val('');
        $('#courseModalTitle').text('Add Course');
        fetchProgramsForSelect();
        courseModal.show();
    });

    $('#courseSave').on('click', function(e){
        e.preventDefault();
        const id = $('#course_id').val();
        const payload = {
            sku: $('#course_sku').val(),
            name: $('#course_name').val(),
            description: $('#course_description').val(),
            quantity: $('#course_quantity').val(),
            price: $('#course_price').val(),
            program_id: $('#course_program').val() || null,
            status: $('#course_status').val(),
            _token:'{{ csrf_token() }}'
        };

const url = id
        ? `{{ route('products.update', ':id') }}`.replace(':id', id)
        : `{{ route('products.store') }}`;
		
		
		
        $.post(url, payload, function(res){
            if(res.status !== 'success'){
                let msg = 'Failed';
                if(res.errors) msg = Object.values(res.errors).flat().join("\n");
                return Swal.fire('Error', msg, 'error');
            }
            // refresh row
            refreshCourseRow(res.data);
            courseModal.hide();
            Swal.fire('Saved','Course saved','success');
        }).fail(function(xhr){
            let msg = 'Validation error';
            if(xhr.responseJSON && xhr.responseJSON.errors) msg = Object.values(xhr.responseJSON.errors).flat().join("\n");
            Swal.fire('Error', msg, 'error');
        });
    });

   $(document).on('click','.edit-course', function(){

    const id = $(this).data('id');
 $.get(`{{ url("course") }}/${id}/json`, function(res){        
        if(res.status){
            fillCourseModalWithData(res.data);
        } else {
            Swal.fire('Error', 'Course not found', 'error');
        }

    }).fail(function(){
        Swal.fire('Error','Unable to fetch course data.','error');
    });

});


    // I will implement a safe edit fetch using /product/{id}/json if you add it server-side. For now add this helper to fill modal:
    function fillCourseModalWithData(data){
        $('#course_id').val(data.id);
        $('#course_sku').val(data.sku);
        $('#course_name').val(data.name);
        $('#course_description').val(data.description || '');
        $('#course_quantity').val(data.quantity || 0);
        $('#course_price').val(data.price || 0);
        $('#course_status').val(data.status ?? 1);
        fetchProgramsForSelect(data.program_id);
        $('#courseModalTitle').text('Edit Course');
        courseModal.show();
    }

    // delete course
    $(document).on('click','.del-course', function(){
        const id = $(this).data('id');
        if(!confirm('Delete Course?')) return;
        $.post('{{ url("course/delete") }}/'+id, {_token:'{{ csrf_token() }}'}, function(res){
            if(res.status === 'success'){
                $(`tr[data-id="${id}"]`).remove();
                Swal.fire('Deleted','Course removed','success');
            }
        });
    });

    // assign program change (in table)
    $(document).on('change', '.assign-program-select', function(){
        const courseId = $(this).data('course');
        const programId = $(this).val() || null;
        $.post('{{ url("course/assign-program") }}', {course_id: courseId, program_id: programId, _token:'{{ csrf_token() }}'}, function(res){
            if(res.status === 'success'){
                const tr = $(`tr[data-id="${res.data.id}"]`);
                tr.find('.course-program').text(res.data.program_id ? $('select.assign-program-select[data-course="'+res.data.id+'"] option:selected').text() : '-');
                tr.attr('data-program-id', res.data.program_id || '');
                Swal.fire('Saved','Course assigned','success');
            }
        });
    });

    // populate assign selects with programs; if programs passed, use them, else fetch all
    function fillCourseProgramDropdown(programs){
        if(programs && programs.length){
            $('.assign-program-select').each(function(){
                const sel = $(this);
                const cur = sel.closest('tr').data('program-id') || '';
                sel.html('<option value="">-- Select Program --</option>');
                programs.forEach(p => sel.append(`<option value="${p.id}">${p.name}</option>`));
                if(cur) sel.val(cur);
            });
            return;
        }
        // fetch all
        $.get('{{ url("institute") }}/all/programs', function(allPrograms){
            $('.assign-program-select').each(function(){
                const sel = $(this);
                const cur = sel.closest('tr').data('program-id') || '';
                sel.html('<option value="">-- Select Program --</option>');
                allPrograms.forEach(p => sel.append(`<option value="${p.id}">${p.name}</option>`));
                if(cur) sel.val(cur);
            });
        });
    }

    // init: fill assign selects
    fillCourseProgramDropdown([]);

});
</script>
@endsection
