@extends('FrontEnd.layouts.master')
@section('title', 'Edit Coupon')

@section('css')
<link href="{{ URL::asset('build/libs/sweetalert2/sweetalert2.min.css') }}" rel="stylesheet">
@endsection

@section('content')
@component('FrontEnd.components.breadcrumb')
    @slot('li_1') CRM @endslot
    @slot('title') Edit Coupon @endslot
@endcomponent

<div class="row">
    <div class="col-lg-8">
        <div class="card">
            <div class="card-body">

                <form id="couponEditForm" method="POST" action="{{ route('coupons.update', $coupon->id) }}">
                    @csrf

                    <div class="mb-2">
                        <label class="form-label">Coupon Code</label>
                        <input type="text" class="form-control" value="{{ $coupon->code }}" disabled>
                    </div>

                    <div class="mb-2">
                        <label class="form-label">Coupon Name</label>
                        <input type="text" name="name" class="form-control" value="{{ $coupon->name }}">
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-2">
                            <label class="form-label">Type</label>
                            <select name="type" class="form-control">
                                <option value="flat" {{ $coupon->type=='flat'?'selected':'' }}>Flat</option>
                                <option value="percent" {{ $coupon->type=='percent'?'selected':'' }}>Percent</option>
                            </select>
                        </div>

                        <div class="col-md-6 mb-2">
                            <label class="form-label">Value</label>
                            <input type="number" step="0.01" name="value" class="form-control" value="{{ $coupon->value }}" required>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-2">
                            <label class="form-label">Min Amount</label>
                            <input type="number" step="0.01" name="min_amount" class="form-control" value="{{ $coupon->min_amount }}">
                        </div>
                        <div class="col-md-6 mb-2">
                            <label class="form-label">Max Discount</label>
                            <input type="number" step="0.01" name="max_discount" class="form-control" value="{{ $coupon->max_discount }}">
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-2">
                            <label class="form-label">Usage Type</label>
                            <select name="usage_type" class="form-control">
                                <option value="single" {{ $coupon->usage_type=='single'?'selected':'' }}>Single</option>
                                <option value="multiple" {{ $coupon->usage_type=='multiple'?'selected':'' }}>Multiple</option>
                            </select>
                        </div>

                        <div class="col-md-6 mb-2">
                            <label class="form-label">Usage Limit</label>
                            <input type="number" name="usage_limit" class="form-control" value="{{ $coupon->usage_limit }}">
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-2">
                            <label class="form-label">Start Date</label>
                            <input type="datetime-local" name="start_at" class="form-control"
                                   value="{{ $coupon->start_at ? \Carbon\Carbon::parse($coupon->start_at)->format('Y-m-d\TH:i') : '' }}">
                        </div>
                        <div class="col-md-6 mb-2">
                            <label class="form-label">End Date</label>
                            <input type="datetime-local" name="end_at" class="form-control"
                                   value="{{ $coupon->end_at ? \Carbon\Carbon::parse($coupon->end_at)->format('Y-m-d\TH:i') : '' }}">
                        </div>
                    </div>

                    <div class="mb-2">
                        <label class="form-label">Status</label>
                        <select name="status" class="form-control">
                            <option value="1" {{ $coupon->status==1?'selected':'' }}>Active</option>
                            <option value="0" {{ $coupon->status==0?'selected':'' }}>Inactive</option>
                        </select>
                    </div>

                    <button class="btn btn-primary mt-2">Update</button>
                    <a href="{{ route('coupons.index') }}" class="btn btn-light mt-2">Back</a>

                </form>

            </div>
        </div>
    </div>
</div>
@endsection

@section('script')
<script src="{{ URL::asset('build/libs/sweetalert2/sweetalert2.min.js') }}"></script>
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

<script>
$(document).ready(function(){

    $.ajaxSetup({
        headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}' }
    });

    $("#couponEditForm").on("submit", function(e){
        e.preventDefault();

        let form = $(this);
        let url  = form.attr("action");
        let fd   = new FormData(this);

        $.ajax({
            url: url,
            type: "POST",
            data: fd,
            processData: false,
            contentType: false,
            success: function(res){

                if(res.status){
                    Swal.fire("Success", res.message, "success").then(() => {
                        if(res.redirect){
                            window.location.href = res.redirect;
                        }
                    });
                } else {
                    Swal.fire("Error", res.message, "error");
                }

            },
            error: function(xhr){

                let msg = "Something went wrong";
                if(xhr.responseJSON && xhr.responseJSON.message){
                    msg = xhr.responseJSON.message;
                }

                Swal.fire("Error", msg, "error");
            }
        });

    });

});
</script>
@endsection
