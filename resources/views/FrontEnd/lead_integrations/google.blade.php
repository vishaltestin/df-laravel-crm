@extends('FrontEnd.layouts.master')
@section('title', 'Google')
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
            Google Lead Settings
        @endslot
    @endcomponent

   <div class="row">
  <div class="col-lg-6">
		<div class="" id=""> 
			<div class="">
                    <div>
                    <div class="container mt-3">
<div class="container mt-3">   <h4>  Google Lead Settings </h4>
<p> Enter the required credentials to connect Google Lead Forms with your CRM.
Once configured, all Google leads will be automatically captured and stored in the system.</p></div>
	@php
    $baseUrl = url('/');
@endphp

<div class="alert alert-info">
    <strong>Webhook URL:</strong><br>

    Facebook:
    <code>{{ $baseUrl }}/webhook/facebook</code><br>

    Google:
    <code>{{ $baseUrl }}/webhook/google</code><br>

    Instagram:
    <code>{{ $baseUrl }}/webhook/instagram</code>
</div></div>
	</div>
	</div>
		</div>

		</div>

	
	
	
        <div class="col-lg-6">
            <div class="card" id="leadsList"> 
			<div class="card-body">
                    <div>
                    <div class="container mt-3"><form method="POST" action="{{ route('lead.integrations.save') }}">
@csrf

<input type="hidden" name="integrations[google][enabled]" value="0">

<div class="card">
    <div class="card-header">
        <input type="checkbox"
               name="integrations[google][enabled]" required
               value="1"
               {{ ($integration->is_enabled ?? false) ? 'checked' : '' }}>
        Google Lead Form
    </div>

    <div class="card-body">
        <input class="form-control"
               name="integrations[google][credentials][client_id]"
               placeholder="Client ID" required
               value="{{ isset($credentials['client_id']) ? ($credentials['client_id']->value) : '' }}">
    </div>
</div>

<button class="btn btn-primary mt-3">Save Google Settings</button>
</form>
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
 
	
  
@endsection
 