@extends('FrontEnd.layouts.master')
@section('title', 'Sources')
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
            Sources
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
                                
								
    <button type="button" class="btn btn-success add-btn"  id="addBtn">+ Add Source</button>
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
										<th class="sort" data-sort="name">Source Name</th>
										<th>Lead Count</th>
										<th>Billing Amount</th>
										<th>Total</th>
										<th>Status</th>
										<th width="150">Action</th>
									</tr>
								</thead>
                                 <tbody class="list form-check-all" id="sourceTable">
        @foreach($sources as $s)
        <tr data-id="{{ $s->id }}">
           <th scope="row">
                                            <div class="form-check">
                                                <input class="form-check-input" type="checkbox"
                                                    name="chk_child" value="option1">
                                            </div>
                                        </th>
                                        <td class="id" style="display:none;"><a
                                                href="javascript:void(0);"
                                                class="fw-medium link-primary">#VZ2101</a></td>
             <td class="name">{{ $s->name }}</td>
            <td>{{ $s->lead_count }}</td>
            <td>{{ $s->billing_setup_amount }}</td>
            <td>{{ $s->lead_count * $s->billing_setup_amount }}</td>
            <td>
                @if($s->status == 1)
                    <span class="badge bg-success">Active</span>
                @else
                    <span class="badge bg-danger">Inactive</span>
                @endif
            </td>
            <td>
             <ul class="list-inline hstack gap-2 mb-0">
                <li class="list-inline-item" data-bs-toggle="tooltip"  data-bs-trigger="hover" data-bs-placement="top"  title="View Billing"> <a class="  btn-sm btn-warning billingBtn"> <i class="ri-pages-line align-bottom text-muted"></i></a></li>                                           
                       <li class="list-inline-item" data-bs-toggle="tooltip"  data-bs-trigger="hover" data-bs-placement="top"  title="Edit"> <a class="edit-item-btn editBtn"   	><i class="ri-pencil-fill align-bottom text-muted"></i></a></li>
                <li class="list-inline-item" data-bs-toggle="tooltip" title="Delete"><a class="  btn-sm btn-danger deleteBtn"> <i class="ri-delete-bin-fill align-bottom text-muted"></i></a></li>
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
 
                </div>
            </div>

        </div>
        <!--end col-->
    </div>
    <!--end row-->

<!-- ===========================
ADD / EDIT MODAL (NAME)
=========================== -->
<div class="modal fade" id="sourceModal">
    <div class="modal-dialog">
        <div class="modal-content">

            <div class="modal-header">
                <h5 class="modal-title">Add / Edit Source</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>

            <div class="modal-body">
                <input type="hidden" id="edit_id">
                <label>Source Name</label>
                <input type="text" id="name" class="form-control">
            </div>

            <div class="modal-footer">
                <button class="btn btn-success" id="saveBtn">Save</button>
            </div>

        </div>
    </div>
</div>


<!-- ===================================
 BILLING SIDEBAR (OFFCANVAS)
=================================== -->
<div class="offcanvas offcanvas-end" tabindex="-1" id="billingSidebar">
    <div class="offcanvas-header">
        <h5>Billing Setup</h5>
        <button type="button" class="btn-close" data-bs-dismiss="offcanvas"></button>
    </div>

    <div class="offcanvas-body">
        
        <input type="hidden" id="bill_id">

        <div class="mb-3">
            <label>Billing Amount</label>
            <input type="number" class="form-control" id="billing_setup_amount">
        </div>

        <div class="mb-3">
            <label>Status</label>
            <select id="status" class="form-control">
                <option value="1">Active</option>
                <option value="0">Inactive</option>
            </select>
        </div>

        <button class="btn btn-success w-100" id="billingSaveBtn">Update Billing</button>

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

    // -----------------------
    // OPEN ADD MODAL
    // -----------------------
    $('#addBtn').click(function(){
        $('#edit_id').val('');
        $('#name').val('');
        $('#sourceModal').modal('show');
    });

    // -----------------------
    // EDIT NAME
    // -----------------------
    $('.editBtn').click(function(){
        let id = $(this).closest('tr').data('id');

        $.get("{{ url('sources') }}/" + id + "/edit", function(res){
            $('#edit_id').val(res.id);
            $('#name').val(res.name);
            $('#sourceModal').modal('show');
        });
    });

    // -----------------------
    // SAVE NAME (ADD/EDIT)
    // -----------------------
    $('#saveBtn').click(function(){
        let id = $('#edit_id').val();
        let url = id ? "{{ url('sources') }}/"+id+"/update" : "{{ url('sources/store') }}";

        $.post(url, {
            _token: "{{ csrf_token() }}",
            name: $('#name').val()
        }, function(){
            location.reload();
        });
    });

    // -----------------------
    // OPEN BILLING SIDEBAR
    // -----------------------
    $('.billingBtn').click(function(){
        let id = $(this).closest('tr').data('id');
        $('#bill_id').val(id);

        $.get("{{ url('sources') }}/" + id + "/edit", function(res){
            $('#billing_setup_amount').val(res.billing_setup_amount);
            $('#status').val(res.status);
            var myCanvas = new bootstrap.Offcanvas($('#billingSidebar'));
            myCanvas.show();
        });
    });

    // -----------------------
    // SAVE BILLING
    // -----------------------
    $('#billingSaveBtn').click(function(){
        let id = $('#bill_id').val();

        $.post("{{ url('sources') }}/" + id + "/billing-update", {
            _token: "{{ csrf_token() }}",
            billing_setup_amount: $('#billing_setup_amount').val(),
            status: $('#status').val()
        }, function(){
            location.reload();
        });
    });

    // -----------------------
    // DELETE SOURCE
    // -----------------------
    $('.deleteBtn').click(function(){
        if(!confirm("Delete this source?")) return;

        let id = $(this).closest('tr').data('id');

        $.ajax({
            url: "{{ url('sources') }}/" + id,
            type: "DELETE",
            data: {_token: "{{ csrf_token() }}"},
            success: function(){
                location.reload();
            }
        });
    });

});
</script>
@endsection
