@extends('FrontEnd.layouts.master')
@section('title', 'IVR Logs')

@section('css')
<link href="{{ URL::asset('build/libs/sweetalert2/sweetalert2.min.css') }}" rel="stylesheet">
@endsection

@section('content')

@component('FrontEnd.components.breadcrumb')
    @slot('li_1') CRM @endslot
    @slot('title') IVR Logs @endslot
@endcomponent

<div class="row">
    <div class="col-lg-12">

        <div class="card">

            <div class="card-header">
                <h4 class="card-title mb-0">IVR Call Logs</h4>
            </div>

            <div class="card-body">

                <div class="table-responsive">
                    <table class="table align-middle">
                        <thead class="table-light">
                        <tr>
                            <th>ID</th>
                            <th>Lead ID</th>
                            <th>Name</th>
                            <th>Mobile</th>
                            <th>Campaign</th>
                            <th>Provider</th>
                            <th>Call Status</th>
                            <th>Response</th>
                            <th>Triggered At</th>
                        </tr>
                        </thead>

                        <tbody>
                        @forelse($logs as $log)
                            <tr>
                                <td>{{ $log->id }}</td>
                                <td>{{ $log->lead_id }}</td>
                                <td>{{ $log->lead_name }}</td>
                                <td>{{ $log->mobile }}</td>
                                <td>{{ $log->campaign }}</td>
                                <td>{{ $log->provider }}</td>
                                <td>{{ $log->call_status }}</td>
                                <td>{{ $log->ivr_response }}</td>
                                <td>{{ $log->created_at }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="9" class="text-center text-muted">No logs found</td>
                            </tr>
                        @endforelse
                        </tbody>

                    </table>
                </div>

                {{-- Pagination --}}
                <div class="mt-3">
                    {{ $logs->links() }}
                </div>

            </div>
        </div>

    </div>
</div>

@endsection
