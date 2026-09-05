@extends('FrontEnd.layouts.master')
@section('title', 'Lead Status Settings')

@section('css')
    <link href="{{ URL::asset('build/libs/sweetalert2/sweetalert2.min.css') }}" rel="stylesheet">
@endsection

@section('content')
    @component('FrontEnd.components.breadcrumb')
        @slot('li_1') CRM @endslot
        @slot('title') Lead Status Settings @endslot
    @endcomponent

    <div class="row">
        <div class="col-lg-12">
            <div class="card" id="leadStatusCard">
                <div class="card-header border-0">
                    <div class="row g-4 align-items-center">
                        <div class="col-sm">
                            <h5 class="card-title mb-0">Lead Status (Main)</h5>
                        </div>
                        <div class="col-sm-auto">
                            <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addStatusModal">
                                + Add Lead Status
                            </button>
                        </div>
                    </div>
                </div>

                <div class="card-body">

                    @if(session('success'))
                        <div class="alert alert-success">{{ session('success') }}</div>
                    @endif

                    <div class="table-responsive table-card">
                        <table class="table align-middle">
                            <thead class="table-light">
                                <tr>
                                    <th>Status Name</th>
                                    <th>Sort Order</th>
                                    <th>Active</th>
                                    <th>Sub Status Count</th>
                                    <th width="220">Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($statuses as $s)
                                    <tr>
                                        <td>{{ $s->name }}</td>
                                        <td>{{ $s->sort_order }}</td>
                                        <td>{{ $s->is_active ? 'Yes' : 'No' }}</td>
                                        <td>{{ $s->subStatuses->count() }}</td>
                                        <td>
                                            {{-- Edit main status --}}
                                            <button type="button"
                                                    class="btn btn-sm btn-soft-info"
                                                    data-update-url="{{ route('lead.status.update', $s->id) }}"
                                                    data-name="{{ $s->name }}"
                                                    data-sort="{{ $s->sort_order }}"
                                                    data-active="{{ $s->is_active ? 1 : 0 }}"
                                                    onclick="editStatus(this)">
                                                Edit
                                            </button>

                                            {{-- Delete main status --}}
                                            <button type="button"
                                                    class="btn btn-sm btn-soft-danger"
                                                    data-delete-url="{{ route('lead.status.delete', $s->id) }}"
                                                    onclick="deleteStatus(this)">
                                                Delete
                                            </button>

                                            {{-- Add Sub status under this main --}}
                                            <button type="button"
                                                    class="btn btn-sm btn-soft-secondary"
                                                    data-action="{{ route('lead.substatus.store', $s->id) }}"
                                                    data-status-name="{{ $s->name }}"
                                                    onclick="setSubStatusForm(this)"
                                                    data-bs-toggle="modal"
                                                    data-bs-target="#addSubStatusModal">
                                                + Sub Status
                                            </button>
                                        </td>
                                    </tr>

                                    @if($s->subStatuses->count())
                                        <tr>
                                            <td colspan="5">
                                                <strong>Sub Statuses for "{{ $s->name }}":</strong>
                                                <ul class="mt-2 mb-0">
                                                    @foreach($s->subStatuses as $sub)
                                                        <li class="d-flex justify-content-between align-items-center mb-1">
                                                            <span>{{ $sub->name }}</span>
                                                            <span>
                                                                <button type="button"
                                                                        class="btn btn-xs btn-outline-info"
                                                                        data-update-url="{{ route('lead.substatus.update', $sub->id) }}"
                                                                        data-name="{{ $sub->name }}"
                                                                        data-sort="{{ $sub->sort_order }}"
                                                                        data-active="{{ $sub->is_active ? 1 : 0 }}"
                                                                        onclick="editSubStatus(this)">
                                                                    Edit
                                                                </button>
                                                                <button type="button"
                                                                        class="btn btn-xs btn-outline-danger"
                                                                        data-delete-url="{{ route('lead.substatus.delete', $sub->id) }}"
                                                                        onclick="deleteSubStatus(this)">
                                                                    Delete
                                                                </button>
                                                            </span>
                                                        </li>
                                                    @endforeach
                                                </ul>
                                            </td>
                                        </tr>
                                    @endif

                                @empty
                                    <tr>
                                        <td colspan="5" class="text-center">No Lead Status found.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <hr class="my-4">

                    {{-- Demo: Dependent dropdown example (optional, for testing AJAX) --}}
                    <h5 class="mb-3">Test Dependent Dropdown (Status → Sub Status)</h5>
                    <div class="row g-3">
                        <div class="col-md-4">
                            <select id="test_lead_status" class="form-select"
                                    onchange="getLeadSubStages(this.value, 'test_lead_sub_status')">
                                <option value="">Select Lead Status</option>
                                @foreach($statuses as $s)
                                    <option value="{{ $s->id }}">{{ $s->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-4">
                            <select id="test_lead_sub_status" class="form-select">
                                <option value="">Lead Sub Stage</option>
                            </select>
                        </div>
                    </div>

                </div> {{-- card-body --}}
            </div>
        </div>
    </div>

    {{-- Add Main Status Modal --}}
    <div class="modal fade" id="addStatusModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog">
            <form method="POST" action="{{ route('lead.status.store') }}" class="modal-content">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title">Add Lead Status</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"
                            aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Status Name</label>
                        <input type="text" name="name" class="form-control" required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Color (optional)</label>
                        <input type="color" name="color" class="form-control form-control-color">
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Sort Order</label>
                        <input type="number" name="sort_order" class="form-control" value="0">
                    </div>

                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" name="is_active" id="statusActive" checked>
                        <label class="form-check-label" for="statusActive">Active</label>
                    </div>
                </div>
                <div class="modal-footer">
                    <button class="btn btn-primary w-100">Save</button>
                </div>
            </form>
        </div>
    </div>

    {{-- Add Sub Status Modal --}}
    <div class="modal fade" id="addSubStatusModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog">
            <form method="POST" id="sub_status_form" action="" class="modal-content">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title">
                        Add Sub Status
                        <span id="subStatusParentTitle" class="text-muted small"></span>
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"
                            aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Sub Status Name</label>
                        <input type="text" name="name" class="form-control" required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Sort Order</label>
                        <input type="number" name="sort_order" class="form-control" value="0">
                    </div>

                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" name="is_active" id="subStatusActive" checked>
                        <label class="form-check-label" for="subStatusActive">Active</label>
                    </div>
                </div>
                <div class="modal-footer">
                    <button class="btn btn-primary w-100">Save</button>
                </div>
            </form>
        </div>
    </div>
@endsection

@section('script')
    <script src="{{ URL::asset('build/libs/sweetalert2/sweetalert2.min.js') }}"></script>
    
	<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script>
        // Set sub status form action dynamically
        function setSubStatusForm(btn) {
            const action = btn.getAttribute('data-action');
            const name   = btn.getAttribute('data-status-name');

            document.getElementById('sub_status_form').setAttribute('action', action);
            document.getElementById('subStatusParentTitle').innerText = ' for ' + name;
        }

        // Edit main status via AJAX + SweetAlert
        function editStatus(btn) {
            const url   = btn.getAttribute('data-update-url');
            const name  = btn.getAttribute('data-name');
            const sort  = btn.getAttribute('data-sort');
            const active= btn.getAttribute('data-active') === '1';

            Swal.fire({
                title: 'Edit Lead Status',
                html:
                    '<input id="swal_status_name" class="swal2-input" placeholder="Name" value="'+ name +'">' +
                    '<input id="swal_sort_order" class="swal2-input" type="number" placeholder="Sort Order" value="'+ sort +'">' +
                    '<div class="form-check mt-2" style="text-align:left; margin-left:10px;">' +
                    '  <input class="form-check-input" type="checkbox" id="swal_is_active" '+ (active ? 'checked' : '') +'> ' +
                    '  <label class="form-check-label" for="swal_is_active">Active</label>' +
                    '</div>',
                focusConfirm: false,
                showCancelButton: true,
                confirmButtonText: 'Update',
                preConfirm: () => {
                    return {
                        name: document.getElementById('swal_status_name').value,
                        sort_order: document.getElementById('swal_sort_order').value,
                        is_active: document.getElementById('swal_is_active').checked ? 1 : 0
                    };
                }
            }).then((result) => {
                if (result.isConfirmed) {
                    $.post(url, {
                        _token: "{{ csrf_token() }}",
                        name: result.value.name,
                        sort_order: result.value.sort_order,
                        is_active: result.value.is_active
                    }, function () {
                        location.reload();
                    });
                }
            });
        }

        // Delete main status
        function deleteStatus(btn) {
            const url = btn.getAttribute('data-delete-url');

            Swal.fire({
                title: 'Delete this Lead Status?',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: 'Yes, Delete'
            }).then((res) => {
                if (res.isConfirmed) {
                    window.location = url; // GET route as per your routes
                }
            });
        }

        // Edit sub status
        function editSubStatus(btn) {
            const url   = btn.getAttribute('data-update-url');
            const name  = btn.getAttribute('data-name');
            const sort  = btn.getAttribute('data-sort');
            const active= btn.getAttribute('data-active') === '1';

            Swal.fire({
                title: 'Edit Sub Status',
                html:
                    '<input id="swal_sub_name" class="swal2-input" placeholder="Name" value="'+ name +'">' +
                    '<input id="swal_sub_sort" class="swal2-input" type="number" placeholder="Sort Order" value="'+ sort +'">' +
                    '<div class="form-check mt-2" style="text-align:left; margin-left:10px;">' +
                    '  <input class="form-check-input" type="checkbox" id="swal_sub_active" '+ (active ? 'checked' : '') +'> ' +
                    '  <label class="form-check-label" for="swal_sub_active">Active</label>' +
                    '</div>',
                showCancelButton: true,
                confirmButtonText: 'Update',
                preConfirm: () => {
                    return {
                        name: document.getElementById('swal_sub_name').value,
                        sort_order: document.getElementById('swal_sub_sort').value,
                        is_active: document.getElementById('swal_sub_active').checked ? 1 : 0
                    };
                }
            }).then((res)=>{
                if(res.isConfirmed){
                    $.post(url, {
                        _token: "{{ csrf_token() }}",
                        name: res.value.name,
                        sort_order: res.value.sort_order,
                        is_active: res.value.is_active
                    }, function () {
                        location.reload();
                    });
                }
            });
        }

        // Delete sub status
        function deleteSubStatus(btn) {
            const url = btn.getAttribute('data-delete-url');

            Swal.fire({
                title: 'Delete this Sub Status?',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: 'Yes, Delete'
            }).then((res)=>{
                if(res.isConfirmed){
                    window.location = url;
                }
            });
        }

        // Dependent dropdown AJAX (Status → Sub Status)
        function getLeadSubStages(statusId, targetSelectId) {
            if (!statusId) {
                document.getElementById(targetSelectId).innerHTML =
                    '<option value=\"\">Lead Sub Stage</option>';
                return;
            }

            $.ajax({
                url: "{{ route('ajax.lead.substatuses') }}",
                type: "POST",
                data: {
                    _token: "{{ csrf_token() }}",
                    status_id: statusId
                },
                success: function (res) {
                    document.getElementById(targetSelectId).innerHTML = res.options;
                }
            });
        }
    </script>
@endsection
