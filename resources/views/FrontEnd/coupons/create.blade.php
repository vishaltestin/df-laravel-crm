@extends('FrontEnd.layouts.master')
@section('title', 'Create Coupon')

@section('content')
@component('FrontEnd.components.breadcrumb')
    @slot('li_1') CRM @endslot
    @slot('title') Create Coupon @endslot
@endcomponent

<div class="row">
    <div class="col-lg-8">

        @if(session('error'))
            <div class="alert alert-danger">{{ session('error') }}</div>
        @endif

        <div class="card">
            <div class="card-body">

                <form id="couponForm" method="POST" action="{{ route('coupons.store') }}">
                    @csrf
<div id="formErrors" class="alert alert-danger d-none"></div>
                    <div class="mb-2">
                        <label class="form-label">Mode</label>
                        <select name="mode" id="mode" class="form-control">
                            <option value="single">Single Coupon</option>
                            <option value="bulk">Bulk Generate</option>
                        </select>
                    </div>

                    <div class="mb-2" id="singleBox">
                        <label class="form-label">Coupon Code</label>
                        <input type="text" name="code" class="form-control" placeholder="EX: NEWYEAR50">
                    </div>

                    <div class="mb-2 d-none" id="bulkBox">
                        <label class="form-label">Bulk Count</label>
                        <input type="number" name="bulk_count" class="form-control" placeholder="ex: 100">
                    </div>

                    <hr>

                    <div class="mb-2">
                        <label class="form-label">Coupon Name (Optional)</label>
                        <input type="text" name="name" class="form-control">
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-2">
                            <label class="form-label">Type</label>
                            <select name="type" class="form-control">
                                <option value="flat">Flat Discount</option>
                                <option value="percent">Percentage</option>
                            </select>
                        </div>

                        <div class="col-md-6 mb-2">
                            <label class="form-label">Value</label>
                            <input type="number" step="0.01" name="value" class="form-control" required>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-2">
                            <label class="form-label">Min Amount</label>
                            <input type="number" step="0.01" name="min_amount" class="form-control">
                        </div>
                        <div class="col-md-6 mb-2">
                            <label class="form-label">Max Discount</label>
                            <input type="number" step="0.01" name="max_discount" class="form-control">
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-2">
                            <label class="form-label">Usage Type</label>
                            <select name="usage_type" id="usage_type" class="form-control">
                                <option value="single">Single Use</option>
                                <option value="multiple">Multiple Use</option>
                            </select>
                        </div>
                        <div class="col-md-6 mb-2">
                            <label class="form-label">Usage Limit (only for multiple)</label>
                            <input type="number" name="usage_limit" class="form-control" placeholder="ex: 100">
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-2">
                            <label class="form-label">Start Date</label>
                            <input type="datetime-local" name="start_at" class="form-control">
                        </div>
                        <div class="col-md-6 mb-2">
                            <label class="form-label">End Date</label>
                            <input type="datetime-local" name="end_at" class="form-control">
                        </div>
                    </div>

                    <div class="mb-2">
                        <label class="form-label">Status</label>
                        <select name="status" class="form-control">
                            <option value="1">Active</option>
                            <option value="0">Inactive</option>
                        </select>
                    </div>

                    <button class="btn btn-primary mt-2">Save</button>

                </form>

            </div>
        </div>

    </div>
</div>
@endsection

@section('script')
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="{{ URL::asset('build/libs/sweetalert2/sweetalert2.min.js') }}"></script>


<script>
document.getElementById("mode").addEventListener("change", function(){
    let mode = this.value;
    document.getElementById("singleBox").classList.toggle("d-none", mode === "bulk");
    document.getElementById("bulkBox").classList.toggle("d-none", mode !== "bulk");
});
</script>
<script>
$(document).ready(function(){

    $.ajaxSetup({
        headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}' }
    });

    $("#saveCouponBtn").on("click", function(){

        let form = $("#couponForm")[0];
        let fd = new FormData(form);

        $.ajax({
            url: "{{ route('coupons.store') }}",
            type: "POST",
            data: fd,
            processData: false,
            contentType: false,

            success: function(res){

                if(res.status){
                    Swal.fire("Success", res.message, "success").then(() => {
                        window.location.href = "{{ route('coupons.index') }}";
                    });
                } else {
                    Swal.fire("Error", res.message, "error");
                }
            },

            error: function(xhr){

                // Laravel 422 validation
                if(xhr.status === 422){
                    let res = xhr.responseJSON;
                    Swal.fire("Validation Error", res.message, "warning");
                    return;
                }

                Swal.fire("Error", "Something went wrong!", "error");
            }
        });

    });

});
</script>
@endsection
