@extends('FrontEnd.layouts.master')
@section('title', 'Raw Lead  ')

@section('css')
<link href="{{ URL::asset('build/libs/sweetalert2/sweetalert2.min.css') }}" rel="stylesheet">
@endsection

@section('content')
@component('FrontEnd.components.breadcrumb')
@slot('li_1') CRM @endslot
@slot('title') Raw Lead List @endslot
@endcomponent
<input type="hidden" id="upload_id" value="{{ $upload->id ?? '' }}">
<div class="row">
    <div class="col-lg-12">
        <div class="card">
            <div class="card-header border-0">
                <div class="hstack gap-2">

                   

                    <!-- IVR Bulk trigger -->
                    <button class="btn btn-primary" id="sendIvrBtn">
                        <i class="ri-phone-line me-1"></i> Send IVR for Selected
                    </button>

                </div>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table align-middle">
                        <thead class="table-light">
                        <tr>
                            <th style="width:40px;"><input type="checkbox" id="checkAll"></th>
                            <th>ID</th>
                            <th>Name</th>
                            <th>Mobile</th>
                            <th>Course</th>
                            <th>City</th>
                            <th>Campaign</th>
                            <th>Source</th>
                            <th>Status</th>
                            <th>IVR Response</th>
                            <th>Lead Created</th>
                            <th>Action</th>
                        </tr>
                        </thead>

                        <tbody id="rawLeadTbody"></tbody>

                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

 @endsection
@section('script')
    <script src="{{ URL::asset('build/libs/list.js/list.min.js') }}"></script>
    <script src="{{ URL::asset('build/libs/list.pagination.js/list.pagination.min.js') }}"></script>
    <script src="{{ URL::asset('build/libs/sweetalert2/sweetalert2.min.js') }}"></script>
     	    <script src="{{ URL::asset('build/js/pages/crm-leads.init.js') }}"></script>

    
 
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

<script>
$(document).ready(function() {
 // CSRF header
$.ajaxSetup({ headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}' } });

// Load raw leads
window.loadRawLeads = function(){
  $.get("{{ route('automation.raw-leads.index') }}", {
    ajax: 1,
    upload_id: $("#upload_id").val()
}, function(r){
        var html = '';
		
		   // ✅ No data case
        if (!r.data || r.data.length === 0) {
            html += `
                <tr>
                    <td colspan="12" class="text-center text-muted">
                        No raw leads found for this upload.
                    </td>
                </tr>
            `;
            $('#rawLeadTbody').html(html);
            return;
        }

        // ✅ Data rows
		
        $.each(r.data, function(i,row){
            html += '<tr data-id="'+row.id+'">'
                + '<td><input type="checkbox" class="row-check"></td>'
                + '<td>'+row.id+'</td>'
                + '<td>'+(row.name ? row.name : '')+'</td>'
                + '<td>'+row.mobile+'</td>'
                + '<td>'+(row.course ? row.course : '')+'</td>'
                + '<td>'+(row.city ? row.city : '')+'</td>'
                + '<td>'+(row.campaign ? row.campaign : '')+'</td>'
                + '<td>'+(row.source ? row.source : '')+'</td>'
                + '<td>'+row.status+'</td>'
                + '<td>'+(row.ivr_response ? row.ivr_response : '')+'</td>'
                + '<td>'+(row.lead_created_status ? 'Yes':'No')+'</td>';

            // Convert button only if lead not created
            if(!row.lead_created_status){
                html += '<td><button class="btn btn-sm btn-success convert-btn">Convert</button></td>';
            } else {
                html += '<td></td>';
            }

            html += '</tr>';
        });
        $('#rawLeadTbody').html(html);
    });
};

// Initial call
loadRawLeads();

// Select all checkbox
$('#checkAll').on('change', function(){
    $('.row-check').prop('checked', $(this).is(':checked'));
});

// CSV import click
$('#csvImportBtn').on('click', function(){

    var fileInput = document.getElementById('csvFileInput');
    if(fileInput.files.length === 0){
        alert('Please select CSV file');
        return;
    }

    var fd = new FormData();
    fd.append('file', fileInput.files[0]);

    $.ajax({
        url: "{{ route('automation.raw-leads.import') }}",
        type: "POST",
        data: fd,
        processData: false,
        contentType: false,
        success: function(res){
            alert(res.message + " Rows: " + res.count);
            loadRawLeads();
        },
        error: function(){ alert('Import Failed'); }
    });

});

// Bulk IVR trigger click
$('#sendIvrBtn').on('click', function(){

    var ids = [];
    $('.row-check:checked').each(function(){
        ids.push($(this).closest('tr').data('id'));
    });

    if(ids.length === 0){
        alert('Please select leads first');
        return;
    }

    $.ajax({
        url: "{{ route('automation.raw-leads.send-ivr') }}",
        type: "POST",
        data: { ids: ids },
        success: function(res){
            alert(res.message);
            loadRawLeads();
        },
        error: function(){ alert('IVR Trigger Failed'); }
    });

});

// Manual convert click
$(document).on('click', '.convert-btn', function(){

    var id = $(this).closest('tr').data('id');

    $.ajax({
        url: "automation/raw-leads/" + id + "/convert",
        type: "POST",
        success: function(res){
            alert(res.message);
            loadRawLeads();
        },
        error: function(xhr){
            alert(xhr.responseJSON.message);
        }
    });

});

});
</script>
 
@endsection
