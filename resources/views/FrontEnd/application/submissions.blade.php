@extends('FrontEnd.layouts.master')
@section('title', 'Applications Form')
@section('css')
<link href="{{ URL::asset('build/libs/sweetalert2/sweetalert2.min.css') }}" rel="stylesheet">
@endsection
@section('css')
@endsection
@section('content')
    @component('FrontEnd.components.breadcrumb')
        @slot('li_1')CRM  @endslot
        @slot('title') Applications Form   @endslot
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
                           
                <p class="mb-1"><strong>Form:</strong> {{ $form->title }}</p>
                <p class="mb-1"><strong>Form ID:</strong> {{ $form->unique_code }}</p>

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
                           <th>Lead</th>
<th>Mobile</th>
<th>Email</th>
                            <th>Fill %</th>
                            <th>Email Verified</th>
                            <th>OTP Verified</th>
                            <th>Payment</th>
                            <th>Status</th>
                            <th>Submitted At</th>
                            <th>Action</th>
                        </tr>
                  </thead>

                                <tbody class="list form-check-all">
                        @forelse($responses as $res)
                          <tr>
                                        <th scope="row">
                                            <div class="form-check">
                                                <input class="form-check-input" type="checkbox"   name="chk_child" value="option1">
                                            </div>
                                        </th> <td class="id" style="display:none;"><a
                                                href="javascript:void(0);"
                                                class="fw-medium link-primary">#VZ2101</a> {{ $res->id }}</td>
<td>{{ $res->lead_name ?? '-' }}</td>
<td>{{ $res->lead_mobile ?? '-' }}</td>
<td>{{ $res->lead_email ?? '-' }}</td>
                                <td>{{ $res->application_score ?? 0 }}%</td>
                                <td>{{ ($res->email_verified ?? 0) == 1 ? 'Yes' : 'No' }}</td>
                                <td>{{ ($res->otp_verified ?? 0) == 1 ? 'Yes' : 'No' }}</td>

                                <td>
                                    ₹{{ $res->payment_amount ?? 0 }}
                                    <br>
                                    <small>
                                        {{ $res->app_payment_method ?? '-' }}
                                        |
{{ $res->app_payment_status ?? 'pending' }}                                    </small>
                                </td>

                                <td>{{ $res->status ?? '-' }}</td>
                                <td>{{ $res->created_at }}</td>

                                <td>
                                    <a href="{{ route('application.form.submission.view', $res->id) }}"
                                       class="btn btn-sm btn-primary">
                                        View
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="9" class="text-center">
                                    No submissions found.
                                </td>
                            </tr>
                        @endforelse
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
