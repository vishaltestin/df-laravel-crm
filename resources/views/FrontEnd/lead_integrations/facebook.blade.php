@extends('FrontEnd.layouts.master')
@section('title', 'Facebook')
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
            Facebook Lead Settings
        @endslot
    @endcomponent

   <div class="row">
  <div class="col-lg-6">
		<div class="" id=""> 
			<div class="">
                    <div>
                    <div class="container mt-3">
<div class="container mt-3">   <h4> Facebook Lead Settings </h4>
<p>Enter the required credentials to connect Facebook Lead Ads with your CRM.
Once configured, all Facebook leads will be automatically captured and stored in the system. </p></div>
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
</div>	</div>
	</div>
	</div>
		</div>

		</div>

	
	
	
        <div class="col-lg-6">
            <div class="card" id="leadsList"> 
			<div class="card-body">
                    <div>
                    <div class="container mt-3">
					
					<form method="POST" action="{{ route('lead.integrations.save') }}">
@csrf

<input type="hidden" name="integrations[facebook][enabled]" value="0">

<div class="card">
    <div class="card-header">
        <input type="checkbox"
               name="integrations[facebook][enabled]" required
               value="1"
               {{ ($integration->is_enabled ?? false) ? 'checked' : '' }}>
        Facebook Lead Ads
    </div>

    <div class="card-body">
        <input class="form-control mb-2"
               name="integrations[facebook][credentials][app_id]"
               placeholder="App ID" required
               value="{{ isset($credentials['app_id']) ? ($credentials['app_id']->value) : '' }}">

        <input class="form-control mb-2"
               name="integrations[facebook][credentials][app_secret]" required
               placeholder="App Secret">

        <input class="form-control"
               name="integrations[facebook][credentials][verify_token]"
               placeholder="Webhook Verify Token" required
               value="{{ isset($credentials['verify_token']) ? ($credentials['verify_token']->value) : '' }}">
    </div>
</div>

<button class="btn btn-primary mt-3">Save Facebook Settings</button>
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
 