@extends('FrontEnd.layouts.master')
@section('title', 'Verified Leads')

@section('css')
<link href="{{ URL::asset('build/libs/sweetalert2/sweetalert2.min.css') }}" rel="stylesheet">
@endsection

@section('content')
@component('FrontEnd.components.breadcrumb')
    @slot('li_1') CRM @endslot
    @slot('title') Verified Leads @endslot
@endcomponent

<div class="row">
    <div class="col-lg-12">

        <div class="card">
            <div class="card-header border-0">
                <h4 class="card-title">Verified Leads</h4>
            </div>

            <div class="card-body">
                <div class="table-responsive">
                    
                    <table class="table align-middle">
                        <thead class="table-light">
                        <tr>
                            <th>ID</th>
                            <th>Name</th>
                            <th>Mobile</th>
                            <th>Course</th>
                            <th>City</th>
                            <th>Campaign</th>
                            <th>Source</th>
                            <th>IVR Response</th>
                            <th>Verified On</th>
                        </tr>
                        </thead>

                        <tbody id="verifiedBody"></tbody>

                    </table>

                </div>
            </div>
        </div>

    </div>
</div>

@endsection


@section('script')
<script src="{{ URL::asset('build/libs/sweetalert2/sweetalert2.min.js') }}"></script>

<script>
$(document).ready(function() {

    // Load verified leads
    loadVerified();

    function loadVerified() {
        $.get("{{ route('automation.verified-leads') }}", { ajax:1 }, function(r){

            var html = '';

            $.each(r.data, function(i, row){

                html += '<tr>'
                    + '<td>'+row.id+'</td>'
                    + '<td>'+(row.name ?? '')+'</td>'
                    + '<td>'+row.mobile+'</td>'
                    + '<td>'+(row.course ?? '')+'</td>'
                    + '<td>'+(row.city ?? '')+'</td>'
                    + '<td>'+(row.campaign ?? '')+'</td>'
                    + '<td>'+(row.source ?? '')+'</td>'
                    + '<td>'+(row.ivr_response ?? '')+'</td>'
                    + '<td>'+row.verified_at+'</td>'
                    + '</tr>';
            });

            $('#verifiedBody').html(html);

        });
    }

});
</script>

@endsection
