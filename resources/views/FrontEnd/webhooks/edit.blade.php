@extends('FrontEnd.layouts.master')
@section('title', 'Edit   Webhooks')
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
         Edit     Webhooks
        @endslot
    @endcomponent
@php
    $qparams = is_string($webhook->query_params)
               ? json_decode($webhook->query_params, true)
               : ($webhook->query_params ?? []);

    $headers = is_string($webhook->headers)
               ? json_decode($webhook->headers, true)
               : ($webhook->headers ?? []);

    $payload = is_string($webhook->payload)
               ? json_decode($webhook->payload, true)
               : ($webhook->payload ?? []);
@endphp

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
  <form id="webhookForm">

        @csrf

        <input type="hidden" id="webhook_id" value="{{ $webhook->id }}">

        <div class="mb-3">
            <label class="form-label">Name *</label>
            <input type="text" class="form-control" id="name" value="{{ $webhook->name }}">
        </div>

        <div class="mb-3">
            <label class="form-label">Entity Type *</label>
            <select id="entity_type" class="form-control">
                <option value="leads" {{ $webhook->entity_type=='leads'?'selected':'' }}>Leads</option>
                <option value="customers" {{ $webhook->entity_type=='customers'?'selected':'' }}>Customers</option>
                <option value="orders" {{ $webhook->entity_type=='orders'?'selected':'' }}>Orders</option>
            </select>
        </div>

        <div class="mb-3">
            <label>Description *</label>
            <textarea id="description" class="form-control">{{ $webhook->description }}</textarea>
        </div>

        <div class="mb-3">
            <label>Method *</label>
            <select id="method" class="form-control">
                <option value="get" {{ $webhook->method=='get'?'selected':'' }}>GET</option>
                <option value="post" {{ $webhook->method=='post'?'selected':'' }}>POST</option>
                <option value="put" {{ $webhook->method=='put'?'selected':'' }}>PUT</option>
                <option value="delete" {{ $webhook->method=='delete'?'selected':'' }}>DELETE</option>
            </select>
        </div>

        <div class="mb-3">
            <label>Endpoint *</label>
            <input type="text" id="end_point" class="form-control" value="{{ $webhook->end_point }}">
        </div>

        <!-- Query params -->
        <div class="mb-3">
            <label>Query Parameters</label>

            <table class="table table-bordered" id="queryTable">
                <tbody>
                @foreach($qparams as $row)
                    <tr>
                        <td><input type="text" class="form-control q-key" value="{{ $row['key'] }}"></td>
                        <td><input type="text" class="form-control q-value" value="{{ $row['value'] }}"></td>
                        <td><button class="btn btn-sm btn-danger removeRow">X</button></td>
                    </tr>
                @endforeach
                </tbody>
            </table>

            <button class="btn btn-sm btn-success" id="addQueryBtn">+ Add Parameter</button>
        </div>

        <!-- headers -->
        <div class="mb-3">
            <label>Headers</label>

            <table class="table table-bordered" id="headerTable">
                <tbody>
                @foreach($headers as $row)
                    <tr>
                        <td><input type="text" class="form-control h-key" value="{{ $row['key'] }}"></td>
                        <td><input type="text" class="form-control h-value" value="{{ $row['value'] }}"></td>
                        <td><button class="btn btn-sm btn-danger removeRow">X</button></td>
                    </tr>
                @endforeach
                </tbody>
            </table>

            <button class="btn btn-sm btn-success" id="addHeaderBtn">+ Add Header</button>
        </div>

        <!-- payload -->
        <div class="mb-3">
            <label>Payload</label>

            <table class="table table-bordered" id="payloadTable">
                <tbody>
                @foreach($payload as $row)
                    <tr>
                        <td><input type="text" class="form-control p-key" value="{{ $row['key'] }}"></td>
                        <td><input type="text" class="form-control p-value" value="{{ $row['value'] }}"></td>
                        <td><button class="btn btn-sm btn-danger removeRow">X</button></td>
                    </tr>
                @endforeach
                </tbody>
            </table>

            <button class="btn btn-sm btn-success" id="addPayloadBtn">+ Add Payload</button>
        </div>

        <!-- Submit -->
        <button class="btn btn-primary" id="updateWebhookBtn">Update Webhook</button>

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
