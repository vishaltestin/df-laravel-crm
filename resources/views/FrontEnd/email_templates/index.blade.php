@extends('FrontEnd.layouts.master')

@section('title', 'Email Templates')

@section('css')
<link href="{{ URL::asset('build/libs/sweetalert2/sweetalert2.min.css') }}" rel="stylesheet">
@endsection

@section('content')

@component('FrontEnd.components.breadcrumb')
    @slot('li_1') <i class="ri-stack-line me-1"></i> CRM @endslot
    @slot('title') <i class="ri-mail-open-line me-1"></i> Email Templates @endslot
@endcomponent

<div class="row">
    <div class="col-lg-12">
        <div class="card" id="leadsList">

            <!-- HEADER -->
            <div class="card-header border-0">
                <div class="row g-4 align-items-center">

                    <!-- SEARCH -->
                    <div class="col-sm-3">
                        <div class="search-box">
                            <input type="text" class="form-control search" placeholder="Search for...">
                            <i class="ri-search-line search-icon"></i>
                        </div>
                    </div>

                    <!-- ACTION STACK -->
                    <div class="col-sm-auto ms-auto">
                        <div class="hstack gap-2">

                           <!-- BULK DELETE -->
                            <button class="btn btn-soft-danger" id="remove-actions" onClick="deleteMultiple()">
                                <i class="ri-delete-bin-5-line me-1"></i>
                            </button>

                            <!-- ADD BUTTON -->
                            <button type="button" class="btn btn-success add-btn" id="addBtn">
                                <i class="ri-add-circle-line me-1"></i> + Add Email Templates
                            </button>
                         
                        </div>
                    </div>

                </div>
            </div>

            <!-- TABLE BODY -->
          
<div class="card-body">
    <div>
        <div class="table-responsive table-card">
            <table class="table align-middle" id="customerTable">
                <thead class="table-light">
                    <tr>
                        <th width="50"></th>
                        <th class="sort" data-sort="name">Title</th>
                        <th class="sort" data-sort="org_name">Organization</th>
                        <th class="sort" data-sort="category">Category</th>
                         <th class="sort" data-sort="status">Status</th>
                        <th class="sort" data-sort="sent_count">Sent Count</th>
                        <th width="150">
                            <i class="ri-settings-6-line me-1"></i> Action
                        </th>
                    </tr>
                </thead>

                <tbody class="list form-check-all">

                @forelse ($templates as $t)
                    <tr data-id="{{ $t->id }}">
                        <td>
                            <input class="form-check-input" type="checkbox"
                                   name="chk_child" value="{{ $t->id }}">
                        </td>

                        <td class="name">
                            <i class="ri-user-3-line me-1"></i> {{ $t->title ?? $t->title }}
                        </td>

                        <td class="org_name">
                            {{ optional($t->organization)->name ?? '—' }}
                        </td>

                      

                        <td class="send_by">
                            {{ $t->subject ?? '—' }}
                        </td>

                        <td class="status">
                            <span class="badge bg-{{ $t->status == 'active' ? 'success' : 'secondary' }}">
                                {{ ucfirst($t->status) }}
                            </span>
                        </td>

                        <td class="sent_count">
                            {{ $t->sent_count ?? 0 }}
                        </td>

                        <td>
                            <a class="btn btn-sm btn-info editBtn" title="Edit">
                                <i class="ri-pencil-line"></i>
                            </a>

                            <a class="btn btn-sm btn-danger deleteBtn" title="Delete">
                                <i class="ri-delete-bin-line"></i>
                            </a>
                        </td>
                    </tr>

                @empty
                    <tr>
                        <td colspan="7" class="text-center text-muted">
                            No records found.
                        </td>
                    </tr>
                @endforelse

                </tbody>
            </table>

            {{-- NO RESULT (List.js fallback) --}}
            <div class="noresult" style="display:none">
                <div class="text-center">
                    <lord-icon src="https://cdn.lordicon.com/msoeawqm.json"
                               trigger="loop"
                               colors="primary:#121331,secondary:#08a88a"
                               style="width:75px;height:75px">
                    </lord-icon>
                    <h5 class="mt-2">Sorry! No Result Found</h5>
                    <p class="text-muted mb-0">
                        We did not find any templates for your search.
                    </p>
                </div>
            </div>
        </div>
    </div>

    {{-- PAGINATION --}}
    <div class="d-flex justify-content-end mt-3">
        <div class="pagination-wrap hstack gap-2">
            <a class="page-item pagination-prev disabled" href="javascript:void(0)">
                <i class="ri-arrow-left-line me-1"></i> Previous
            </a>
            <ul class="pagination listjs-pagination mb-0"></ul>
            <a class="page-item pagination-next" href="javascript:void(0)">
                Next <i class="ri-arrow-right-line ms-1"></i>
            </a>
        </div>
    </div>
</div>

        </div>
    </div>
</div>

<!-- ✅ CKEDITOR CONTENT MODAL -->
<div class="modal fade" id="templateModal">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">

            <div class="modal-header">
                <h5 class="modal-title">
                    <i class="ri-mail-settings-line me-1"></i> Add / Edit Template
                </h5>
                <button class="btn-close" data-bs-dismiss="modal"></button>
            </div>

            <div class="modal-body">
                <input type="hidden" id="edit_id">

                <div class="mb-3">
                    <label><i class="ri-user-voice-line me-1"></i> Template Name</label>
                    <input type="text" name="title" id="title" class="form-control">
                </div>

                <div class="mb-3">
                    <label><i class="ri-chat-quote-line me-1"></i> Subject</label>
                    <input type="text" id="subject" class="form-control">
                </div>

                <div class="mb-3">
                    <label><i class="ri-text-spacing me-1"></i> Content</label>
                    <textarea id="content" class="form-control"></textarea>
                </div>

                <!-- ✅ Dynamic Variables box same pattern -->
                <div class="mb-3">
                    <label class="form-label fw-semibold">
                        <i class="ri-code-s-slash-line me-1"></i> Dynamic Variables
                    </label>
                    @php
$variables = [
    '{lead_id}',
    '{lead_name}',
    '{lead_mobile}',
    '{lead_email}',
    '{lead_city}',
    '{lead_state}',
    '{lead_next_followup}',
    '{lead_registration_date}',
	'{lead_assign_to}',
	'{customer_name}',
    '{customer_email}',
    '{customer_mobile}',
    '{lead_id}',
    '{lead_source}',
    '{assigned_user}',
	'{lead_city}',
	'{lead_state}',
	'{programme_name}',
	'{institute_name}',
	'{assigned_user_name}'
];
@endphp


<div class="border p-2 rounded" id="variableBox">
    @foreach($variables as $v)
        <button type="button"
                class="btn btn-sm btn-soft-primary mb-1 me-1"
                onclick="insertVariable('{{ $v }}')">
            {{ $v }}
        </button>
    @endforeach
</div>  
                     <small class="text-muted">
                        <i class="ri-cursor-line me-1"></i> Click to insert into content box
                    </small>
                </div>

                <!-- ✅ STATUS DROPDOWN -->
                <div class="mb-3">
                    <label><i class="ri-toggle-line me-1"></i> Status</label>
                    <select id="status" class="form-select">
                        <option value="active">Active</option>
                        <option value="inactive">Inactive</option>
                    </select>
                </div>

            </div>

            <div class="modal-footer">
                <button class="btn btn-success" id="saveBtn">
                    <i class="ri-save-3-line me-1"></i> Save
                </button>
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
<script src="https://cdn.ckeditor.com/ckeditor5/39.0.1/classic/ckeditor.js"></script>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
$(document).on('click', '.editBtn', function () {

    let id = $(this).closest('tr').data('id');

    $.get("{{ url('email-templates') }}/" + id + "/edit", function (res) {

        if (!res.template) {
            Swal.fire('Error', 'Invalid response', 'error');
            return;
        }

        let t = res.template;

        // ---- BASIC FIELDS ----
        $('#edit_id').val(t.id);
        $('#title').val(t.title);
        $('#subject').val(t.subject);
        $('#status').val(t.status ?? 'active');

        // ---- OPEN MODAL FIRST ----
        $('#templateModal').modal('show');

        // ---- CKEDITOR SAFE SET ----
        setTimeout(function () {
            if (editor) {
                editor.setData(t.message ?? '');
            }
        }, 300);

    }).fail(function () {
        Swal.fire('Error', 'Unable to load template', 'error');
    });

});
</script>
 <script>
$('#saveBtn').on('click', function () {

    let id = $('#edit_id').val();

    let url = id
        ? "{{ url('email-templates') }}/" + id + "/update"
        : "{{ url('email-templates/store') }}";

    $.post(url, {
        _token: "{{ csrf_token() }}",
        title: $('#title').val(),
        subject: $('#subject').val(),
        content: editor ? editor.getData() : '',
        status: $('#status').val()
    })
    .done(function (res) {

        Swal.fire({
            icon: 'success',
            title: 'Saved Successfully',
            text: res.message ?? 'Email template saved successfully',
            confirmButtonText: 'OK'
        }).then(() => {
            location.reload();   // ✅ redirect AFTER alert
        });

    })
    .fail(function (xhr) {

        let msg = 'Save failed';
        if (xhr.responseJSON && xhr.responseJSON.message) {
            msg = xhr.responseJSON.message;
        }

        Swal.fire({
            icon: 'error',
            title: 'Error',
            text: msg
        });
    });
});
</script>
<script>

let editor = null;
let activeInput = 'content'; // subject / content

// ===============================
// CKEDITOR INIT (ONLY ONCE)
// ===============================
$(document).ready(function () {

    ClassicEditor
        .create(document.querySelector('#content'))
        .then(e => {
            editor = e;
        })
        .catch(error => {
            console.error(error);
        });

    // ===============================
    // Track focus (Subject / Content)
    // ===============================
    $('#subject').on('focus', function () {
        activeInput = 'subject';
    });

    $('#content').on('focus', function () {
        activeInput = 'content';
    });

    // ===============================
    // ADD MODAL
    // ===============================
    $('#addBtn').on('click', function () {

        $('#edit_id').val('');
        $('#title').val('');
        $('#subject').val('');
        $('#status').val('active');

        $('#templateModal').modal('show');

        setTimeout(function () {
            if (editor) {
                editor.setData('');
            }
        }, 300);
    });

    // ===============================
    // EDIT MODAL
    // ===============================
 // ===============================
    // SAVE (ADD / UPDATE)
    // ===============================
     
    // ===============================
    // DELETE
    // ===============================
    $('.deleteBtn').on('click', function () {

        let id = $(this).closest('tr').data('id');

        if (!confirm("Delete this template?")) return;

        $.ajax({
            url: "{{ url('email-templates') }}/" + id,
            type: "DELETE",
            data: { _token: "{{ csrf_token() }}" },
            success: function () {
                location.reload();
            },
            error: function () {
                Swal.fire('Error', 'Delete failed', 'error');
            }
        });
    });

});

// ===============================
// INSERT VARIABLE (SUBJECT / BODY)
// ===============================
function insertVariable(param) {

    // SUBJECT FIELD
    if (activeInput === 'subject') {

        let subject = $('#subject');
        let pos = subject.prop('selectionStart') || subject.val().length;
        let val = subject.val();

        subject.val(
            val.substring(0, pos) + param + val.substring(pos)
        );

        subject.focus();
        subject[0].selectionEnd = pos + param.length;
        return;
    }

    // CKEDITOR BODY
    if (editor) {
        editor.model.change(writer => {
            const position = editor.model.document.selection.getFirstPosition();
            writer.insertText(param, position);
        });
        editor.editing.view.focus();
    }
}
</script>

@endsection
