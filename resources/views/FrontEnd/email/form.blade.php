@extends('FrontEnd.layouts.master')
@section('title', 'Configuration')
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
            SMTP Settings
        @endslot
    @endcomponent

    <div class="row">
        <div class="col-lg-6">
		<div class="" id=""> 
			<div class="">
                    <div>
                    <div class="container mt-3">
<div class="container mt-3">   <h4> IMAP Account

</h4>
<p>Configure your IMAP account settings here. </p>


@if(!empty($setting->mail_mailer))
    <a href="{{ url('smtp-test') }}" class="btn btn-success mt-2" target="_new">
        Test SMTP
    </a>
@endif






</div>
	</div>
	</div>
	</div>
		</div>

		</div>

	
	
	
        <div class="col-lg-6">
            <div class="card" id="leadsList"> 
			<div class="card-body">
                    <div>
                    <div class="container mt-3">
<div class="container mt-3">
 
    <form id="emailForm">
        @csrf

        <div class="mb-2">
            <label>Mailer</label>
            <input type="text" name="mail_mailer" class="form-control"
                   value="{{ $setting->mail_mailer }}">
        </div>

        <div class="mb-2">
            <label>Host</label>
            <input type="text" name="mail_host" class="form-control"
                   value="{{ $setting->mail_host }}">
        </div>

        <div class="mb-2">
            <label>Port</label>
            <input type="text" name="mail_port" class="form-control"
                   value="{{ $setting->mail_port }}">
        </div>
 <div class="mb-2">
            <label>Encryption Type </label>
            <input type="text" name="mail_encryption" class="form-control"
                   value="{{ $setting->mail_encryption }}">
        </div>
		
	 <div class="mb-2">

<div class="form-check form-switch">
    <input class="form-check-input" type="checkbox" role="switch" id="flexSwitchCheckDefault" value="{{ $setting->mail_validatessl }}" name="mail_validatessl">
    <label class="form-check-label" for="flexSwitchCheckDefault">Validate SSL Certificate </label>
</div>
		
	        </div>
	
		<div class="mb-2 ">
		<label class="" for="mail_username">IMAP Username </label>
		 <input type="text" name="mail_username" class=" form-control " id="mail_username" value="{{ $setting->mail_username }}">
		 </div>
       <div class="mb-2  ">
	   <label class="" for="mail_password">IMAP Password </label>
	   
	    <input type="password" name="mail_password" class=" form-control" id="mail_password" value="{{ $setting->mail_password }}">
		
		</div>

<div class="mb-2">
            <label>Sender Email </label>
            <input type="email" name="mail_from_address" class="form-control"
                   value="{{ $setting->mail_from_address }}">
        </div>
		
		<div class="mb-2">
            <label>Sender Name</label>
            <input type="text" name="mail_from_name" class="form-control"
                   value="{{ $setting->mail_from_name }}">
        </div>
		
		
        <button class="btn btn-primary mt-2">Save</button>  
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

@endsection
@section('script')
    <script src="{{ URL::asset('build/libs/list.js/list.min.js') }}"></script>
    <script src="{{ URL::asset('build/libs/list.pagination.js/list.pagination.min.js') }}"></script>
    <script src="{{ URL::asset('build/libs/sweetalert2/sweetalert2.min.js') }}"></script>
    
    
			<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

<script>
$("#emailForm").on("submit", function(e){
    e.preventDefault();

    $.ajax({
        url: "{{ url('email/update') }}",
        type: "POST",
        data: $(this).serialize(),
        success: function(res){
            alert(res.message);
        }
    });
});
</script>

@endsection
 