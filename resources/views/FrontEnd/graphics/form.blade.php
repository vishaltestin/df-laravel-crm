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
            Graphics Settings
        @endslot
    @endcomponent

 <div class="row">
        <div class="col-lg-6">
		<div class="" id=""> 
			<div class="">
                    <div>
                    <div class="container mt-3">
<div class="container mt-3">   <h4> Graphics  </h4>
<p>  Powered by Section Configurations </p>
<i> We can configure the design  section here. </i></div>
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
     <form id="graphicsForm">
        @csrf

        <div class="mb-2">
            <label>Theme Color</label>
            <input type="text" name="theme_color" class="form-control"
                   value="{{ $setting->theme_color }}">
        </div>

        <div class="mb-2">
            <label>Sidebar Color</label>
            <input type="text" name="sidebar_color" class="form-control"
                   value="{{ $setting->sidebar_color }}">
        </div>

        <div class="mb-2">
            <label>Navbar Color</label>
            <input type="text" name="navbar_color" class="form-control"
                   value="{{ $setting->navbar_color }}">
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
     <!--end row-->

@endsection
@section('script')
    <script src="{{ URL::asset('build/libs/list.js/list.min.js') }}"></script>
    <script src="{{ URL::asset('build/libs/list.pagination.js/list.pagination.min.js') }}"></script>
    <script src="{{ URL::asset('build/libs/sweetalert2/sweetalert2.min.js') }}"></script>
    
    
				<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

	
<script>
$("#graphicsForm").on("submit", function(e){
    e.preventDefault();

    $.ajax({
        url: "{{ url('graphics/update') }}",
        type: "POST",
        data: $(this).serialize(),
        success: function(res){
            alert(res.message);
        }
    });
});
</script>
   
@endsection
 