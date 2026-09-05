@extends('FrontEnd.layouts.master')
@section('title', 'Create  Webhooks')
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
         Create    Webhooks
        @endslot
    @endcomponent

    <div class="row">
        <div class="col-lg-12">
            <div class="card" id="leadsList">
                <div class="card-header border-0">

                    <div class="row g-4 align-items-center">
                     
                        <div class="col-sm-auto ms-auto">
                            <div class="hstack gap-2">
                                 
								
   <a href="{{ route('webhooks.index') }}" class="btn btn-primary">Back</a>
      </div>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    <div class="live-preview">
                    <div>
						<div class="row">
                        <div class="col-lg-12">
                
<form method="POST" action="{{ route('webhooks.store') }}">
@csrf

<div class="row">

    <div class="col-md-6 mb-3">
        <label>Name *</label>
        <input type="text" name="name" class="form-control" required>
    </div>

    <div class="col-md-6 mb-3">
        <label>Entity Type *</label>
        <select name="entity_type" class="form-control" required>
            <option value="leads">Leads</option>
            <option value="contacts">Contacts</option>
            <option value="deals">Deals</option>
        </select>
    </div>

    <div class="col-md-12 mb-3">
        <label>Description *</label>
        <textarea class="form-control" name="description" required></textarea>
    </div>

    <div class="col-md-3 mb-3">
        <label>Method *</label>
        <select name="method" class="form-control" required>
            <option value="get">GET</option>
            <option value="post">POST</option>
            <option value="put">PUT</option>
        </select>
    </div>

    <div class="col-md-9 mb-3">
        <label>End Point *</label>
        <input type="text" name="end_point" class="form-control" required>
    </div>

</div>

<hr>

<h5>Query Parameters</h5>
<div id="queryWrapper">
    <div class="row mb-2">
        <div class="col-md-5">
            <input type="text" name="query_params[0][key]" class="form-control" placeholder="Key">
        </div>
        <div class="col-md-5">
            <input type="text" name="query_params[0][value]" class="form-control" placeholder="Value" >
        </div>
        <div class="col-md-2">
            <button type="button" class="btn btn-success addQuery">+</button>
        </div>
    </div>
</div>

<hr>

<h5>Headers</h5>
<div id="headerWrapper">
    <div class="row mb-2">
        <div class="col-md-5">
            <input type="text" name="headers[0][key]" class="form-control" placeholder="Header Key"  value="Content-Type">
        </div>
        <div class="col-md-5">
            <input type="text" name="headers[0][value]" class="form-control" placeholder="Header Value" value="text/plain;charset=UTF">
        </div>
        <div class="col-md-2">
            <button type="button" class="btn btn-success addHeader">+</button>
        </div>
    </div>
</div>

<hr>

 
<div class="col-md-4 mb-3">
    <label>Body</label>
    <select name="payload_type" class="form-control" required>
        <option value="x-www-form-urlencoded">x-www-form-urlencoded</option>
        <option value="form-data">Form Data</option>
        <option value="raw">Raw JSON</option>
    </select>
</div>

<div id="payloadWrapper">
    <div class="row mb-2">
        <div class="col-md-5">
            <input type="text" name="payload[0][key]" class="form-control" placeholder="Payload Key">
        </div>
        <div class="col-md-5">
            <input type="text" name="payload[0][value]" class="form-control" placeholder="Payload Value">
        </div>
        <div class="col-md-2">
            <button type="button" class="btn btn-success addPayload">+</button>
        </div>
    </div>
</div>

<hr>

<button class="btn btn-primary">Save Webhook</button>

</form>


						</div>
 
					</div>
				    </div>
					</div>

				</div>
			</div>
        <!--end col-->
    </div>
    <!--end row-->
        </div>

 @endsection
@section('script')
    <script src="{{ URL::asset('build/libs/list.js/list.min.js') }}"></script>
    <script src="{{ URL::asset('build/libs/list.pagination.js/list.pagination.min.js') }}"></script>
    <script src="{{ URL::asset('build/libs/sweetalert2/sweetalert2.min.js') }}"></script>
     	    <script src="{{ URL::asset('build/js/pages/crm-leads.init.js') }}"></script>
 	<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

    
 <script>
// Query add/remove
$(document).on('click', '.addQuery', function () {
    let row = `
    <div class="row mb-2">
        <div class="col-md-5"><input type="text" name="query_params[][key]" class="form-control"></div>
        <div class="col-md-5"><input type="text" name="query_params[][value]" class="form-control"></div>
        <div class="col-md-2"><button type="button" class="btn btn-danger removeRow">X</button></div>
    </div>`;
    $("#queryWrapper").append(row);
});

// Header add/remove
$(document).on('click', '.addHeader', function () {
    let row = `
    <div class="row mb-2">
        <div class="col-md-5"><input type="text" name="headers[][key]" class="form-control"></div>
        <div class="col-md-5"><input type="text" name="headers[][value]" class="form-control"></div>
        <div class="col-md-2"><button type="button" class="btn btn-danger removeRow">X</button></div>
    </div>`;
    $("#headerWrapper").append(row);
});

// Payload add/remove
$(document).on('click', '.addPayload', function () {
    let row = `
    <div class="row mb-2">
        <div class="col-md-5"><input type="text" name="payload[][key]" class="form-control"></div>
        <div class="col-md-5"><input type="text" name="payload[][value]" class="form-control"></div>
        <div class="col-md-2"><button type="button" class="btn btn-danger removeRow">X</button></div>
    </div>`;
    $("#payloadWrapper").append(row);
});

// Remove Row
$(document).on('click', '.removeRow', function () {
    $(this).closest('.row').remove();
});
</script>
@endsection
