@extends('FrontEnd.layouts.master')
@section('title', 'Campaigns')

@section('css')
<link href="{{ URL::asset('build/libs/sweetalert2/sweetalert2.min.css') }}" rel="stylesheet">
@endsection

@section('content')

@component('FrontEnd.components.breadcrumb')
    @slot('li_1')
        CRM
    @endslot
    @slot('title')
        Campaigns
    @endslot
@endcomponent

<div class="row">
    <div class="col-lg-12">
        <div class="card" id="leadsList">

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

                            <button type="button" class="btn btn-success add-btn" id="addBtn">
                                + Add Campaigns
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
                                            <input class="form-check-input" type="checkbox" id="checkAll" value="option">
                                        </div>
                                    </th>
                                    <th class="sort" data-sort="name">Name</th>
                                    <th>Subject</th>
                                    <th>Type</th>
                                    <th>Status</th>
                                    <th width="150">Action</th>
                                </tr>
                            </thead>

                            <tbody class="list form-check-all" id="sourceTable">
                                @foreach($campaigns as $a)
                                    <tr data-id="{{ $a->id }}">
                                        <th scope="row">
                                            <div class="form-check">
                                                <input class="form-check-input" type="checkbox" name="chk_child" value="option1">
                                            </div>
                                        </th>

                                        <td class="id" style="display:none;">
                                            <a href="javascript:void(0);" class="fw-medium link-primary">#VZ2101</a>
                                        </td>

                                        <td class="name">{{ $a->name }}</td>
                                        <td>{{ $a->subject }}</td>
                                        <td>{{ $a->type }}</td>
                                        <td>{{ $a->status }}</td>

                                        <td>
                                            <ul class="list-inline hstack gap-2 mb-0">
                                                <li class="list-inline-item" data-bs-toggle="tooltip" data-bs-trigger="hover" data-bs-placement="top" title="Edit">
                                                    <a class="edit-item-btn editBtn">
                                                        <i class="ri-pencil-fill align-bottom text-muted"></i>
                                                    </a>
                                                </li>

                                                <li class="list-inline-item" data-bs-toggle="tooltip" title="Delete">
                                                    <a class="btn-sm btn-danger deleteBtn">
                                                        <i class="ri-delete-bin-fill align-bottom text-muted"></i>
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
                                <p class="text-muted mb-0">
                                    We've searched more than 150+ leads We did not find any leads for you search.
                                </p>
                            </div>
                        </div>

                    </div>

                    <div class="d-flex justify-content-end mt-3">
                        <div class="pagination-wrap hstack gap-2">
                            <a class="page-item pagination-prev disabled" href="#">Previous</a>
                            <ul class="pagination listjs-pagination mb-0"></ul>
                            <a class="page-item pagination-next" href="#">Next</a>
                        </div>
                    </div>

                </div>
            </div>

        </div>
    </div>
</div>

<!-- OFFCANVAS -->
<div class="offcanvas offcanvas-end" id="campaignCanvas">
    <div class="offcanvas-header">
        <h5 id="canvasTitle">Add Campaign</h5>
        <button type="button" class="btn-close" data-bs-dismiss="offcanvas"></button>
    </div>

    <div class="offcanvas-body">

        <input type="hidden" id="edit_id">

        <label>Name</label>
        <input type="text" id="name" class="form-control mb-2">

        <label>Subject</label>
        <input type="text" id="subject" class="form-control mb-2">

        <!-- ✅ Missing fields (JS me already use ho rahe the) -->
        <label>Type</label>
        <select id="type" class="form-control mb-2">
            <option value="Email">Email</option>
            <option value="Whatsapp">Whatsapp</option>
            <option value="SMS">SMS</option>
        </select>

        <label>Mail To</label>
        <input type="text" id="mail_to" class="form-control mb-2" placeholder="example@gmail.com">

        <label>Spooling</label>
        <select id="spooling" class="form-control mb-2">
            <option value="No">No</option>
            <option value="Yes">Yes</option>
        </select>

        <label>Select Template</label>
        <select id="marketing_template_id" class="form-control mb-2">
            <option value="">Select Email Template</option>
            @foreach($templates as $t)
                <option value="{{ $t->id }}">{{ $t->name }}</option>
            @endforeach
        </select>

        <label>Marketing Event</label>
        <select id="marketing_event_id" class="form-control mb-2">
            <option value="">Select Event</option>
            @foreach($events as $e)
                <option value="{{ $e->id }}">{{ $e->name }}</option>
            @endforeach
        </select>

        <label>Status</label>
        <div class="form-check form-switch mb-3">
            <input class="form-check-input" type="checkbox" id="statusSwitch">
            <label class="form-check-label" for="statusSwitch">Active</label>
        </div>

        <button class="btn btn-success w-100" id="saveBtn">Save</button>

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
$(document).ready(function(){

    // default status Active
    $('#statusSwitch').prop('checked', true);

    // -------------------------
    // OPEN ADD CANVAS
    // -------------------------
    $('#addBtn').click(function(){

        $('#canvasTitle').text("Add Campaign");

        $('#edit_id').val('');
        $('#name').val('');
        $('#subject').val('');
        $('#mail_to').val('');

        $('#marketing_template_id').val('');
        $('#marketing_event_id').val('');

        $('#statusSwitch').prop('checked', true);

        $('#type').val('Email');
        $('#spooling').val('No');

        new bootstrap.Offcanvas(document.getElementById('campaignCanvas')).show();
    });


    // -------------------------
    // EDIT
    // -------------------------
    $('.editBtn').click(function(){

        let id = $(this).closest('tr').data('id');

        $.get("{{ url('campaigns') }}/" + id + "/edit")
        .done(function(res){

            $('#canvasTitle').text("Edit Campaign");

            $('#edit_id').val(res.id);
            $('#name').val(res.name);
            $('#subject').val(res.subject);

            $('#statusSwitch').prop('checked', res.status === "Active");

            $('#type').val(res.type);
            $('#mail_to').val(res.mail_to);
            $('#spooling').val(res.spooling);

            $('#marketing_template_id').val(res.marketing_template_id);
            $('#marketing_event_id').val(res.marketing_event_id);

            new bootstrap.Offcanvas(document.getElementById('campaignCanvas')).show();
        })
        .fail(function(){
            Swal.fire("Error", "Unable to load campaign data", "error");
        });

    });


    // -------------------------
    // SAVE ADD / UPDATE
    // -------------------------
    $('#saveBtn').click(function(){

        let id = $('#edit_id').val();

        let url = id
            ? "{{ url('campaigns') }}/" + id + "/update"
            : "{{ url('campaigns/store') }}";

        let payload = {
            _token: "{{ csrf_token() }}",
            name: $('#name').val(),
            subject: $('#subject').val(),
            status: $('#statusSwitch').is(':checked') ? "Active" : "Inactive",
            type: $('#type').val(),
            mail_to: $('#mail_to').val(),
            spooling: $('#spooling').val(),
            marketing_template_id: $('#marketing_template_id').val(),
            marketing_event_id: $('#marketing_event_id').val()
        };

        $.post(url, payload)
        .done(function(){
            location.reload();
        })
        .fail(function(xhr){

            // Laravel validation errors
            if(xhr.status === 422){
                let errors = xhr.responseJSON?.errors || {};
                let msg = '';

                Object.keys(errors).forEach(function(key){
                    msg += errors[key][0] + "<br>";
                });

                Swal.fire({
                    icon: "warning",
                    title: "Validation Error",
                    html: msg
                });

                return;
            }

            Swal.fire({
                icon: "error",
                title: "Error",
                text: xhr.responseJSON?.message || "Something went wrong!"
            });

        });

    });


    // -------------------------
    // DELETE
    // -------------------------
    $('.deleteBtn').click(function(){

        let id = $(this).closest('tr').data('id');

        Swal.fire({
            title: "Are you sure?",
            text: "This campaign will be deleted permanently!",
            icon: "warning",
            showCancelButton: true,
            confirmButtonText: "Yes, delete it",
            cancelButtonText: "Cancel"
        }).then((result) => {

            if(!result.isConfirmed) return;

            $.ajax({
                url: "{{ url('campaigns') }}/" + id,
                type: "DELETE",
                data: { _token: "{{ csrf_token() }}" }
            })
            .done(function(){
                Swal.fire("Deleted!", "Campaign deleted successfully", "success")
                .then(()=> location.reload());
            })
            .fail(function(xhr){
                Swal.fire("Error", xhr.responseJSON?.message || "Delete failed", "error");
            });

        });

    });

});
</script>

@endsection
