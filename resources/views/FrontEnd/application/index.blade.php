@extends('FrontEnd.layouts.master')
@section('title', 'Applications Form')
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
            Applications Form
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
                                
								
     <a href="{{ route('application.forms.create') }}" class="btn btn-primary"> + Add Application Form</a>
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
									 <th>Title</th>
									<th>Unique Code</th>
									<th>Status</th>
								<th>Total Submissions</th>	<th>Actions</th>
 									</tr>
								</thead>

                                <tbody class="list form-check-all">
									@foreach($forms as $form)									
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
                                                <div class="flex-grow-1 ms-2 name">{{ $form->title }} </div>
                                        </td>
 										<td>{{ $form->unique_code }}</td>
										<td>
    @if($form->status == 1)
        <span class="badge bg-success">Active</span>
    @else
        <span class="badge bg-danger">Deactive</span>
    @endif
</td>
<td>
    <span class="badge bg-dark">{{ $form->responses_count }}</span>
</td>
<td>
    <a href="{{ route('application.form.submissions', encrypt($form->id)) }}"
       class="btn btn-sm btn-info">
        View Submissions
    </a>

    <a href="{{ route('application.forms.edit', encrypt($form->id)) }}"
       class="btn btn-sm btn-primary">
        <i class="ri-pencil-line"></i> Edit
    </a>
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

 @endsection
@section('script')
    <script src="{{ URL::asset('build/libs/list.js/list.min.js') }}"></script>
    <script src="{{ URL::asset('build/libs/list.pagination.js/list.pagination.min.js') }}"></script>
    <script src="{{ URL::asset('build/libs/sweetalert2/sweetalert2.min.js') }}"></script>
   		<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
   	    <script src="{{ URL::asset('build/js/pages/crm-leads.init.js') }}"></script>

    

<script>
$(document).on("click", ".deleteBtn", function () {
    if(!confirm("Delete this product?")) return;

    var id = $(this).data("id");

    $.ajax({
        url: "/products/" + id,
        type: "DELETE",
        data: { _token: "{{ csrf_token() }}" },
        success: function (res) {
            location.reload();
        }
    });
});
</script>
@endsection
