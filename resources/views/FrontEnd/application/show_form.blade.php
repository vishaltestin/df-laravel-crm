@extends('FrontEnd.layouts.master')
@section('title', 'Applications Form')
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
            Applications Form
        @endslot
    @endcomponent

   <div class="row">
 
	

	
        <div class="col-lg-12">
            <div class="card" id="leadsList"> 
			 <div class="card-body">

	   <div class="alert alert-info mb-3">
            <div class="d-flex justify-content-between flex-wrap">
                <div>
                    <strong>Lead:</strong> {{ $lead->name ?? '-' }} <br>
                    <strong>Mobile:</strong> {{ $lead->mobile ?? '-' }} <br>
                    <strong>Email:</strong> {{ $lead->email ?? '-' }}
                </div>

                
            </div>
        </div>
                <h4>{{ $form->title }}</h4>
                <p class="text-muted">{{ $form->description }}</p>

              

                    {{-- FORM INCLUDE --}}
@include('FrontEnd.application.form1', [
    'form' => $form,
    'lead' => $lead
])
            

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
$("#dynamicForm").submit(function(e){
    e.preventDefault();
     $.ajax({
        url: "{{ route('application.form.submit') }}",
        type: "POST",
        data: {
            form_id: $("input[name=form_id]").val(),
             form_data: $(this).serializeArray(),
            _token: "{{ csrf_token() }}"
        },
        success: function(res){
            alert(res.message);
        }
    });
});
</script>


@endsection
