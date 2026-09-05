@extends('FrontEnd.layouts.master')
@section('title', 'Attributes')
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
            {{ ucfirst($lookup) }} Attributes
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
 <button type="button" class="btn btn-success add-btn"  id="addBtn"> + Add {{ ucfirst($lookup) }}</button> 
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
										<th class="sort" data-sort="name">  Name</th>
										  <th>Field Type</th>
										<th>Entity Type</th>
										<th>Required</th>
										<th>Unique</th>
										<th width="150">Action</th>
									</tr>
								</thead>
                                 <tbody class="list form-check-all" id="sourceTable">
                @foreach($attributes as $a)
        <tr data-id="{{ $a->id }}">
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
             <td>{{ $a->type }}</td>
            <td>{{ $a->entity_type }}</td>
            <td>{{ $a->is_required ? 'Yes' : 'No' }}</td>
            <td>{{ $a->is_unique ? 'Yes' : 'No' }}</td>
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
  MODAL ADD/EDIT 
=========================== -->
<div class="modal fade" id="attrModal">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">

            <div class="modal-header">
                <h5 class="modal-title">Add / Edit</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>

            <div class="modal-body">

                <input type="hidden" id="edit_id">
                <input type="hidden" id="lookup_type" value="{{ $lookup }}">

                <div class="row">
                    
                    <div class="col-md-6 mb-3">
                        <label>Name</label>
                        <input type="text" id="name" class="form-control">
                    </div>

                    <div class="col-md-6 mb-3">
                        <label>Field Type</label>
                        <select id="type" class="form-control">
                            <option value="text">Text</option>
                            <option value="textarea">Textarea</option>
                            <option value="price">Price</option>
                            <option value="boolean">Boolean</option>
                            <option value="select">Select</option>
                            <option value="multiselect">Multiselect</option>
                            <option value="checkbox">Checkbox</option>
                            <option value="email">Email</option>
                            <option value="address">Address</option>
                            <option value="phone">Phone</option>
                            <option value="lookup">Lookup</option>
                            <option value="datetime">Datetime</option>
                            <option value="date">Date</option>
                            <option value="image">Image</option>
                            <option value="file">File</option>
                        </select>
                    </div>

                    <div class="col-md-4 mb-3">
                        <label>Entity Type</label>
                        <select id="entity_type" class="form-control">
                            <option value="lead">Lead</option>
                            <option value="course">Course</option>
                            <option value="organization">Organization</option>
                        </select>
                    </div>

                    <div class="col-md-4 mb-3">
                        <label>Sort Order</label>
                        <input type="number" id="sort_order" class="form-control" value="0">
                    </div>

                    <div class="col-md-4 mb-3">
                        <label>Validation</label>
                        <input type="text" id="validation" class="form-control" placeholder="e.g. min:3|max:10">
                    </div>

                    <div class="col-md-6 mb-3">
                        <label>
                            <input type="checkbox" id="is_required"> Required
                        </label>
                    </div>

                    <div class="col-md-6 mb-3">
                        <label>
                            <input type="checkbox" id="is_unique"> Unique
                        </label>
                    </div>

                </div>

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

    // ADD
    $('#addBtn').click(function(){
        $('#edit_id').val('');
        $('#name').val('');
        $('#sort_order').val(0);
        $('#validation').val('');
        $('#is_required').prop('checked', false);
        $('#is_unique').prop('checked', false);
        $('#type').val('text');
        $('#entity_type').val('lead');

        $('#attrModal').modal('show');
    });

    // EDIT
    $('.editBtn').click(function(){
        let id = $(this).closest('tr').data('id');

        $.get("{{ url('attributes') }}/" + id + "/edit", function(res){
            $('#edit_id').val(res.id);
            $('#name').val(res.name);
            $('#type').val(res.type);
            $('#entity_type').val(res.entity_type);
            $('#sort_order').val(res.sort_order);
            $('#validation').val(res.validation);
            $('#is_required').prop('checked', res.is_required == 1);
            $('#is_unique').prop('checked', res.is_unique == 1);

            $('#attrModal').modal('show');
        });
    });

    // SAVE
    $('#saveBtn').click(function(){

        let id = $('#edit_id').val();
        let url = id ? "{{ url('attributes') }}/" + id + "/update" : "{{ url('attributes/store') }}";

        $.post(url, {
            _token: "{{ csrf_token() }}",
            name: $('#name').val(),
            type: $('#type').val(),
            lookup_type: $('#lookup_type').val(),
            entity_type: $('#entity_type').val(),
            sort_order: $('#sort_order').val(),
            validation: $('#validation').val(),
            is_required: $('#is_required').is(':checked') ? 1 : 0,
            is_unique: $('#is_unique').is(':checked') ? 1 : 0,
        }, function(){
            location.reload();
        });

    });

    // DELETE
    $('.deleteBtn').click(function(){
        if(!confirm("Delete this attribute?")) return;

        let id = $(this).closest('tr').data('id');

        $.ajax({
            url: "{{ url('attributes') }}/" + id,
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
