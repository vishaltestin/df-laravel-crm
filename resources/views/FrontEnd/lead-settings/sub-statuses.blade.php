@extends('FrontEnd.layouts.master')
@section('title', 'Lead Sub Status Settings')

@section('css')
    <link href="{{ URL::asset('build/libs/sweetalert2/sweetalert2.min.css') }}" rel="stylesheet">
@endsection

@section('content')
    @component('FrontEnd.components.breadcrumb')
        @slot('li_1') CRM @endslot
        @slot('title') Lead Sub Status Settings @endslot
    @endcomponent

    <div class="row">
        <div class="col-lg-12">
            <div class="card" id="leadSubStatusCard">
                <div class="card-header border-0">
                    <div class="row g-4 align-items-center">
                        <div class="col-sm">
                            <h5 class="card-title mb-0">Lead Sub Status (Under Main Status)</h5>
                        </div>
                        <div class="col-sm-auto">
                            <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addSubStatusModalGlobal">
                                + Add Sub Status
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
                                    <th>Main Status</th>
                                    <th>Sub Status</th>
                                    <th>Sort Order</th>
                                    <th>Active</th>
                                    <th width="180">Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($subStatuses as $sub)
                                    <tr>
                                        <td>{{ $sub->status->name ?? '-' }}</td>
                                        <td>{{ $sub->name }}</td>
                                        <td>{{ $sub->sort_order }}</td>
                                        <td>{{ $sub->is_active ? 'Yes' : 'No' }}</td>
                                        <td>
                                            <button type="button"
                                                    class="btn btn-sm btn-soft-info"
                                                    data-update-url="{{ route('lead.substatus.update', $sub->id) }}"
                                                    data-name="{{ $sub->name }}"
                                                    data-sort="{{ $sub->sort_order }}"
                                                    data-active="{{ $sub->is_active ? 1 : 0 }}"
                                                    onclick="editSubStatusGlobal(this)">
                                                Edit
                                            </button>

                                            <button type="button"
                                                    class="btn btn-sm btn-soft-danger"
                                                    data-delete-url="{{ route('lead.substatus.delete', $sub->id) }}"
                                                    onclick="deleteSubStatusGlobal(this)">
                                                Delete
                                            </button>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="5" class="text-center">No Sub Status found.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                </div> {{-- card-body --}}
            </div>
        </div>
    </div>

    {{-- Global: Add Sub Status Modal (select Main Status) --}}
    <div class="modal fade" id="addSubStatusModalGlobal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog">
            <form method="POST"
                  id="global_sub_status_form"
                  action=""
                  class="modal-content">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title">Add Sub Status (Global)</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"
                            aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Main Lead Status</label>
                        <select id="global_main_status" class="form-select" required>
                            <option value="">Select Main Status</option>
                            @foreach($statuses as $s)
                                <option value="{{ $s->id }}"
                                        data-action="{{ route('lead.substatus.store', $s->id) }}">
                                    {{ $s->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Sub Status Name</label>
                        <input type="text" name="name" class="form-control" required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Sort Order</label>
                        <input type="number" name="sort_order" class="form-control" value="0">
                    </div>

                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" name="is_active" id="globalSubActive" checked>
                        <label class="form-check-label" for="globalSubActive">Active</label>
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
        // Set correct action URL when main status is selected in global modal
        document.getElementById('global_main_status').addEventListener('change', function(){
            const option = this.options[this.selectedIndex];
            const action = option.getAttribute('data-action') || '';
            document.getElementById('global_sub_status_form').setAttribute('action', action);
        });

        function editSubStatusGlobal(btn) {
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
                    }, function(){
                        location.reload();
                    });
                }
            });
        }

        function deleteSubStatusGlobal(btn) {
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
    </script>
@endsection
