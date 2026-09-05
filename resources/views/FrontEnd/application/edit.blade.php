@extends('FrontEnd.layouts.master')
@section('title', 'Edit Application Form')

@section('css')
<link href="{{ URL::asset('build/libs/sweetalert2/sweetalert2.min.css') }}" rel="stylesheet">
@endsection

@section('content')

@component('FrontEnd.components.breadcrumb')
    @slot('li_1') CRM @endslot
    @slot('title') Edit Application Form @endslot
@endcomponent

<div class="row">
    <div class="col-lg-6"></div>

    <div class="col-lg-6">
        <div class="card">
            <div class="card-body">
                <div class="container mt-3">

<form action="{{ route('application.forms.update', $form->id) }}" method="POST">
@csrf

<!-- Title -->
<label class="mt-2 fw-bold">Form Title</label>
<input type="text" name="title" class="form-control"
       value="{{ $form->title }}" required>

<!-- Description -->
<label class="mt-3 fw-bold">Description</label>
<textarea name="description" class="form-control" rows="2">{{ $form->description }}</textarea>

<h5 class="mt-4">Form Configuration</h5>

<!-- Allowed Countries (MULTIPLE) -->
<label class="mt-3 fw-bold">Allowed Countries</label>
<select name="allowed_countries[]" id="allowed_countries"
        class="form-control" multiple required>
    @foreach($countries as $c)
        <option value="{{ $c->id }}"
            {{ in_array($c->id, $form->allowed_countries ?? []) ? 'selected' : '' }}>
            {{ $c->name }}
        </option>
    @endforeach
</select>
<small class="text-muted">
    Form fill karte time sirf ye countries dikhengi
</small>

<!-- Allowed Institutes (MULTIPLE) -->
<label class="mt-3 fw-bold">Allowed Institutes</label>
<select name="allowed_institutes[]" id="allowed_institutes"
        class="form-control" multiple required>
    @foreach($institutes as $i)
        <option value="{{ $i->id }}"
            {{ in_array($i->id, $form->allowed_institutes ?? []) ? 'selected' : '' }}>
            {{ $i->name }}
        </option>
    @endforeach
</select>
<small class="text-muted">
    Selected institutes ke hi programs/courses aage aayenge
</small>

<hr class="my-4">

<!-- Fees -->
<label class="mt-3 fw-bold">Fees (₹)</label>
<input type="number" class="form-control" name="fees"
       id="fees" value="{{ $form->fees }}">

<!-- Tax -->
<label class="mt-3 fw-bold">Tax (%)</label>
<select class="form-control" name="tax" id="tax">
    <option value="0"  {{ $form->tax==0?'selected':'' }}>0%</option>
    <option value="5"  {{ $form->tax==5?'selected':'' }}>5%</option>
    <option value="18" {{ $form->tax==18?'selected':'' }}>18%</option>
</select>

<!-- Total -->
<label class="mt-3 fw-bold">Total Amount</label>
<input type="number" class="form-control" name="total_amount"
       id="total" readonly value="{{ $form->total_amount }}">

<!-- Gateway -->
<label class="mt-3 fw-bold">Payment Gateway</label>
<select name="gateway" id="gateway" class="form-control">
    <option value="razorpay" {{ $form->gateway=='razorpay'?'selected':'' }}>Razorpay</option>
    <option value="cashfree" {{ $form->gateway=='cashfree'?'selected':'' }}>Cashfree</option>
    <option value="stripe"   {{ $form->gateway=='stripe'?'selected':'' }}>Stripe</option>
</select>

<hr class="my-4">
 
<label class="fw-bold mt-3">Form Name</label>
<input type="text"
       name="form_name"  value="{{ $form->form_name }}"
       class="form-control"
       placeholder="form1"
       required>

<small class="text-muted">
Example: form1 → resources/views/FrontEnd/application/forms/form1.php
</small>

<button class="btn btn-warning mt-3">
    Update Form
</button>

</form>
 

                </div>
            </div>
        </div>
    </div>
</div>

@endsection

@section('script')
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

<script>
// Auto calculate total
$("#fees, #tax").on("input change", function() {
    let f = Number($("#fees").val());
    let t = Number($("#tax").val());
    $("#total").val(f + (f * t / 100));
});

// Generate JSON Schema
function generateSchema() {
    let schema = {
        allowed_countries: $("#allowed_countries").val(),
        allowed_institutes: $("#allowed_institutes").val(),
        fees: Number($("#fees").val()),
        tax: Number($("#tax").val()),
        total_amount: Number($("#total").val()),
        gateway: $("#gateway").val()
    };

    $("#schemaField").val(JSON.stringify(schema, null, 4));
}
</script>
@endsection
