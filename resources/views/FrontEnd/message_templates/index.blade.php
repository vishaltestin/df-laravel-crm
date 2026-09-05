@extends('FrontEnd.layouts.master')
@section('title', 'Message Templates')

@section('css')
    <link href="{{ URL::asset('build/libs/sweetalert2/sweetalert2.min.css') }}" rel="stylesheet">
@endsection

@section('content')
    @component('FrontEnd.components.breadcrumb')
        @slot('li_1')
            CRM
        @endslot
        @slot('title')
            Message Templates
        @endslot
    @endcomponent

    <div class="row">
        <div class="col-lg-12">
            <div class="card" id="templatesList">
                <div class="card-header border-0">
                    <div class="row g-4 align-items-center">
                        <div class="col-sm-3">
                            <div class="search-box">
                                <input type="text" class="form-control search" placeholder="Search for...">
                                <i class="ri-search-line search-icon"></i>
                            </div>
                        </div>
                        <div class="col-sm-auto ms-auto">
                            <div class="hstack gap-2">
                          
                                <button class="btn btn-soft-danger" id="remove-actions" onClick="deleteMultiple()">
                                    <i class="ri-delete-bin-2-line"></i>
                                </button>

                                {{-- ADD NEW BUTTON (OPEN MODAL) --}}
                                <button class="btn btn-primary" onclick="openAddModal()">
                                    + Add Message Template
                                </button>
                             </div>
                        </div>
                    </div>
                </div>

                {{-- TABLE LIST --}}
       
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
                        <th class="sort" data-sort="send_by">Send By</th>
                        <th class="sort" data-sort="status">Status</th>
                        <th class="sort" data-sort="sent_count">Sent Count</th>
                        <th width="160">Action</th>
                    </tr>
                </thead>

                <tbody class="list form-check-all">

                @forelse ($templates as $t)
                    <tr data-id="{{ $t->id }}">
                        <td>
                            <input class="form-check-input" type="checkbox"
                                   name="chk_child" value="{{ $t->id }}">
                        </td>

                        <td class="name">{{ $t->title }}</td>

                        <td class="org_name">
                            {{ optional($t->organization)->name ?? '—' }}
                        </td>

                        <td class="category">{{ $t->category ?? '—' }}</td>

                        <td class="send_by">{{ $t->send_by ?? '—' }}</td>

                        <td class="status">
                            <span class="badge bg-{{ $t->status == 'active' ? 'success' : 'secondary' }}">
                                {{ ucfirst($t->status) }}
                            </span>
                        </td>

                        <td class="sent_count">{{ $t->sent_count }}</td>

                        <td>
                            <button class="btn btn-sm btn-info"
                                    onclick="openEditModal({{ $t->id }})">
                                Edit
                            </button>
                            <button class="btn btn-sm btn-danger"
                                    onclick="deleteSingle({{ $t->id }})">
                                Delete
                            </button>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="8" class="text-center text-muted">
                            No records found.
                        </td>
                    </tr>
                @endforelse

                </tbody>
            </table>

            {{-- No Result (List.js fallback) --}}
            <div class="noresult" style="display: none">
                <div class="text-center">
                    <lord-icon src="https://cdn.lordicon.com/msoeawqm.json"
                               trigger="loop"
                               colors="primary:#121331,secondary:#08a88a"
                               style="width:75px;height:75px">
                    </lord-icon>
                    <h5 class="mt-2">Sorry! No Result Found</h5>
                    <p class="text-muted mb-0">
                        We did not find any message templates for your search.
                    </p>
                </div>
            </div>
        </div>

        {{-- Pagination --}}
        <div class="d-flex justify-content-end mt-3">
            <div class="pagination-wrap hstack gap-2">
                <a class="page-item pagination-prev disabled" href="javascript:void(0);">
                    Previous
                </a>
                <ul class="pagination listjs-pagination mb-0"></ul>
                <a class="page-item pagination-next" href="javascript:void(0);">
                    Next
                </a>
            </div>
        </div>
    </div>
</div>

            </div>

        </div>
    </div>


    {{-- ========================= --}}
    {{-- ADD / EDIT MODAL --}}
    {{-- ========================= --}}
    <div class="modal fade" id="templateModal" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">

                <div class="modal-header">
                    <h5 class="modal-title" id="modalTitle">Add Message Template</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"
                        aria-label="Close"></button>
                </div>

                <div class="modal-body">
                    <form id="templateForm">
                        @csrf
                        <input type="hidden" id="template_id" name="template_id">

                        <div class="row">
                            <div class="col-md-8">

                                <div class="mb-3">
                                    <label class="form-label">Title <span  class="avatar-group-item" data-bs-toggle="tooltip" data-bs-trigger="hover" data-bs-placement="top" title="Eg. Welcome SMS, Fee Reminder, WhatsApp OTP Message" data-bs-original-title="Eg.  Welcome SMS, Fee Reminder, WhatsApp OTP Message"><i class="ri-user-2-fill"></i> </span></label>
                                    <input type="text" id="title" name="title" class="form-control" required>
                                </div>

                                <div class="mb-3">
                                    <label class="form-label">Category <span  class="avatar-group-item" data-bs-toggle="tooltip" data-bs-trigger="hover" data-bs-placement="top" title="use-case Examples:    welcome,    followup,    payment,    otp,    reminder" data-bs-original-title="use-case Examples:    welcome,    followup,    payment,    otp,    reminder"><i class="ri-user-2-fill"></i> </span></label>
                                    <input type="text" id="category" name="category" class="form-control"
                                        placeholder="e.g. welcome, followup, payment">
                                </div>

                                <div class="mb-3">
                                    <label class="form-label">Organization</label>
                                    <select id="organization_id" name="organization_id" class="form-select" required>
                                        <option value="">-- Select Organization --</option>
                                        @foreach ($organizations as $org)
                                            <option value="{{ $org->id }}">{{ $org->name }}</option>
                                        @endforeach
                                    </select>
                                </div>

                                <div class="mb-3">
                                    <label class="form-label">Send By (Sender ID / Number)</label>
                                    <input type="text" id="send_by" name="send_by" class="form-control"
                                        placeholder="e.g. SKYITN / 9876543210">
                                </div>

                                <div class="mb-3">
                                    <label class="form-label">Status</label>
                                    <select id="status" name="status" class="form-select">
                                        <option value="active">Active</option>
                                        <option value="inactive">Inactive</option>
                                    </select>
                                </div>

                                <div class="mb-3">
                                    <label class="form-label">Message Body</label>
                                    <textarea id="message" name="message" rows="7" class="form-control"
                                        placeholder="Type your message here..." required></textarea>
                                </div>

                            </div>

                            <div class="col-md-4">
                                <h6>Dynamic Variables</h6>
                                <div class="border p-2">
                                   @php
$parameters = [
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
@endphp           @foreach ($parameters as $p)
                                        <button type="button" class="btn btn-sm btn-soft-primary mb-1"
                                            onclick="insertParam('{{ $p }}')">
                                            {{ $p }}
                                        </button>
                                    @endforeach
                                </div>

                                <div class="mt-3 small text-muted">
                                    <b>Info:</b>  Clicking on a variable will insert it into the message body.
                                </div>
                            </div>

                        </div>

                    </form>
                </div>

                <div class="modal-footer">
                    <button class="btn btn-light" data-bs-dismiss="modal">Cancel</button>
                    <button class="btn btn-success" onclick="saveTemplate()">Save</button>
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
    // ====== List.js init (simple) ======
    var options = {
        valueNames: ['name', 'org_name', 'category', 'send_by', 'status', 'sent_count'],
        page: 10,
        pagination: true
    };
    var messageList = new List('templatesList', options);

    // ====== Helper: open Add Modal ======
    function openAddModal() {
        $('#modalTitle').text('Add Message Template');
        $('#templateForm')[0].reset();
        $('#template_id').val('');
        $('#status').val('active');
        $('#templateModal').modal('show');
    }

    // ====== Insert variable into message ======
    function insertParam(param) {
        let textarea = document.getElementById('message');
        textarea.value += (textarea.value ? ' ' : '') + param;
        textarea.focus();
    }

    // ====== Save (Add / Update) via AJAX ======
    function saveTemplate() {
        let id = $('#template_id').val();

        let url = id
            ? "{{ url('message-templates') }}/" + id + "/update"
            : "{{ route('message.templates.store') }}";

        $.ajax({
            url: url,
            type: 'POST',
            data: $('#templateForm').serialize(),
            success: function(res) {
                Swal.fire('Success', res.message, 'success').then(() => {
                    location.reload();
                });
            },
            error: function(xhr) {
                let msg = 'Something went wrong';
                if (xhr.responseJSON && xhr.responseJSON.message) {
                    msg = xhr.responseJSON.message;
                }
                Swal.fire('Error', msg, 'error');
            }
        });
    }

    // ====== Open Edit Modal ======
    function openEditModal(id) {
        $.ajax({
            url: "{{ url('message-templates') }}/" + id + "/edit",
            type: 'GET',
            success: function(res) {
                let t = res.template;

                $('#modalTitle').text('Edit Message Template');
                $('#template_id').val(t.id);
                $('#title').val(t.title);
                $('#category').val(t.category);
                $('#send_by').val(t.send_by);
                $('#status').val(t.status);
                $('#message').val(t.message);
                $('#organization_id').val(t.organization_id);

                $('#templateModal').modal('show');
            },
            error: function() {
                Swal.fire('Error', 'Unable to load template', 'error');
            }
        });
    }

    // ====== Delete Single ======
    function deleteSingle(id) {
        Swal.fire({
            title: 'Are you sure?',
            text: 'This template will be permanently deleted.',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonText: 'Yes, delete it!'
        }).then((result) => {
            if (!result.isConfirmed) return;

            $.ajax({
                url: "{{ url('message-templates') }}/" + id,
                type: 'DELETE',
                data: {
                    _token: "{{ csrf_token() }}"
                },
                success: function(res) {
                    Swal.fire('Deleted!', res.message, 'success').then(() => {
                        location.reload();
                    });
                },
                error: function() {
                    Swal.fire('Error', 'Delete failed', 'error');
                }
            });
        });
    }

    // ====== Bulk Delete ======
    function deleteMultiple() {
        let ids = [];
        $('input[name="chk_child"]:checked').each(function() {
            ids.push($(this).val());
        });

        if (ids.length === 0) {
            Swal.fire('Info', 'Please select at least one template.', 'info');
            return;
        }

        Swal.fire({
            title: 'Are you sure?',
            text: 'Selected templates will be permanently deleted.',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonText: 'Yes, delete all'
        }).then((result) => {
            if (!result.isConfirmed) return;

            $.ajax({
                url: "{{ route('message.templates.bulk-delete') }}",
                type: 'POST',
                data: {
                    _token: "{{ csrf_token() }}",
                    ids: ids
                },
                success: function(res) {
                    Swal.fire('Deleted!', res.message, 'success').then(() => {
                        location.reload();
                    });
                },
                error: function() {
                    Swal.fire('Error', 'Bulk delete failed', 'error');
                }
            });
        });
    }

    // CheckAll logic
    $('#checkAll').on('change', function() {
        $('input[name="chk_child"]').prop('checked', $(this).is(':checked'));
    });
</script>
@endsection
