@extends('FrontEnd.layouts.master')
@section('title', 'Logs')
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
            Webhooks Log
        @endslot
    @endcomponent

   <div class="row">
 

	
	
	
        <div class="col-lg-12">
            <div class="card" id="leadsList"> 
			<div class="card-body">
                    <div>
                    <div class="container mt-3"><table class="table table-bordered">
    <thead>
        <tr>
            <th>Date</th>
            <th>Source</th>
            <th>Payload</th>
        </tr>
    </thead>
    <tbody>
        @foreach($logs as $log)
        <tr>
            <td>{{ $log->created_at }}</td>
            <td>{{ $log->source }}</td>
            <td><pre>{{ json_encode(json_decode($log->payload), JSON_PRETTY_PRINT) }}</pre></td>
        </tr>
        @endforeach
    </tbody>
</table>

{{ $logs->links() }}
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
 