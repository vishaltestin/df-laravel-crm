@extends('FrontEnd.layouts.master')
@section('title', 'Lead Medium')
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
            Lead Medium
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
 								
    <button type="button" class="btn btn-success add-btn"  id="addBtn">+ Add Lead Medium</button>
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
										<th class="sort" data-sort="name"> Medium Name</th>
										 
										<th>Status</th>
										<th width="150">Action</th>
									</tr>
								</thead>
                                 <tbody class="list form-check-all" id="sourceTable">
        @foreach($types as $t)
        <tr data-id="{{ $t->id }}">
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
                                            
                                                <div class="flex-grow-1 ms-2 organization_name">{{ $t->name }}</div>
                                            </div>
                                        </td>


										<td>
                @if($t->status == 1)
                    <span class="badge bg-success">Active</span>
                @else
                    <span class="badge bg-danger">Inactive</span>
                @endif
            </td>
            <td>
             <ul class="list-inline hstack gap-2 mb-0">
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
-- Add/Edit Modal 
=========================== -->
<div class="modal fade" id="typeModal">
    <div class="modal-dialog">
        <div class="modal-content">

            <div class="modal-header">
                <h5 class="modal-title">Add / Edit Medium</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>

            <div class="modal-body">
                <input type="hidden" id="edit_id">

                <label>Medium Name</label>
                <input type="text" id="name" class="form-control">
            </div>

            <div class="modal-footer">
                <button class="btn btn-success" id="saveBtn">Save</button>
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
$(document).ready(function(){

    // Add New
    $('#addBtn').click(function(){
        $('#edit_id').val('');
        $('#name').val('');
        $('#typeModal').modal('show');
    });

    // Edit
    $('.editBtn').click(function(){
        let id = $(this).closest('tr').data('id');

        $.get("{{ url('types') }}/" + id + "/edit", function(res){
            $('#edit_id').val(res.id);
            $('#name').val(res.name);
            $('#typeModal').modal('show');
        });
    });

    // Save
    $('#saveBtn').click(function(){
        let id = $('#edit_id').val();
        let url = id ? "{{ url('types') }}/"+id+"/update" : "{{ url('types/store') }}";

        $.post(url, {
            _token: "{{ csrf_token() }}",
            name: $('#name').val()
        }, function(){
            location.reload();
        });
    });

    // Delete
    $('.deleteBtn').click(function(){
        if(!confirm("Delete this type?")) return;

        let id = $(this).closest('tr').data('id');

        $.ajax({
            url: "{{ url('types') }}/" + id,
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
