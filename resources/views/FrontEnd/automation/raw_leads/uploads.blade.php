@extends('FrontEnd.layouts.master')
@section('title', 'Raw Lead Uploads')

@section('content')
@component('FrontEnd.components.breadcrumb')
    @slot('li_1') CRM @endslot
    @slot('title') Raw Lead Uploads @endslot
@endcomponent

<div class="row">
    <div class="col-lg-12">

        @if(session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
        @endif

        <div class="card">
            <div class="card-header border-0">
                <div class="hstack gap-2">

                    <form action="{{ route('automation.raw-leads.upload') }}" method="POST" enctype="multipart/form-data" class="d-flex gap-2">
                        @csrf
                        <input type="file" name="file" class="form-control" style="max-width:260px;" required>
                        <button class="btn btn-primary">
                            <i class="ri-upload-line me-1"></i> Upload File
                        </button>
                    </form>
  <a href="{{ route('automation.raw-leads.download-sample') }}" class="btn btn-sm btn-info">
                        <i class="ri-download-line me-1"></i> Download Sample CSV
                    </a>
					
					
					<button type="button" class="btn btn-sm btn-dark" data-bs-toggle="modal" data-bs-target="#apiLeadModal">
    <i class="ri-code-line me-1"></i> Lead API Tester
</button>

<div class="modal fade" id="apiLeadModal" tabindex="-1">
  <div class="modal-dialog modal-lg modal-dialog-scrollable">
    <div class="modal-content">

      <div class="modal-header">
        <h5 class="modal-title">Lead Create API (Test)</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>

      <div class="modal-body">
 <div class="border rounded p-3 bg-light">

  <h5 class="mb-2">Lead Create API (Documentation)</h5>

  <!-- METHOD + ENDPOINT -->
  <div class="mb-3">
    <div><b>Method:</b> <span class="badge bg-dark">POST</span></div>
    <div><b>Endpoint:</b> <code>/api/leads/store</code></div>
    <div class="text-muted small mt-1">Auth required (Sentinel user session).</div>
  </div>

  <hr>

  <!-- INPUTS TABLE -->
  <h6 class="mb-2">Input Fields</h6>

  <div class="table-responsive">
    <table class="table table-bordered table-sm align-middle">
      <thead class="table-light">
        <tr>
          <th>Field Name</th>
          <th>Type</th>
          <th>Required</th>
          <th>Notes</th>
        </tr>
      </thead>
      <tbody>
        <tr>
          <td><code>name</code></td>
          <td>string</td>
          <td><span class="badge bg-warning text-dark">YES</span></td>
          <td>Max 150 chars</td>
        </tr>

        <tr>
          <td><code>mobile</code></td>
          <td>string</td>
          <td><span class="badge bg-warning text-dark">YES</span></td>
          <td>Max 20 chars</td>
        </tr>

        <tr>
          <td><code>upload_via</code></td>
          <td>string</td>
          <td><span class="badge bg-secondary">NO</span></td>
          <td>Values: <code>mobile</code>, <code>email</code>, <code>whatsapp</code></td>
        </tr>

        <tr>
          <td><code>email</code></td>
          <td>string</td>
          <td><span class="badge bg-secondary">NO</span></td>
          <td>Required only if <code>upload_via=email</code></td>
        </tr>

        <tr>
          <td><code>state</code></td>
          <td>string</td>
          <td><span class="badge bg-secondary">NO</span></td>
          <td>Example: Punjab</td>
        </tr>

        <tr>
          <td><code>city</code></td>
          <td>string</td>
          <td><span class="badge bg-secondary">NO</span></td>
          <td>Example: Ludhiana</td>
        </tr>

        <tr>
          <td><code>country</code></td>
          <td>integer/string</td>
          <td><span class="badge bg-secondary">NO</span></td>
          <td>Default: 80</td>
        </tr>

        <tr>
          <td><code>lead_assign_to</code></td>
          <td>integer</td>
          <td><span class="badge bg-secondary">NO</span></td>
          <td>Default: current user id</td>
        </tr>

        <tr>
          <td><code>institute_id</code></td>
          <td>integer</td>
          <td><span class="badge bg-secondary">NO</span></td>
          <td>Institute Master ID</td>
        </tr>

        <tr>
          <td><code>programme_id</code></td>
          <td>integer</td>
          <td><span class="badge bg-secondary">NO</span></td>
          <td>Programme Master ID</td>
        </tr>

        <tr>
          <td><code>faculty_id</code></td>
          <td>integer</td>
          <td><span class="badge bg-secondary">NO</span></td>
          <td>Faculty Master ID</td>
        </tr>

        <tr>
          <td><code>application_form_id</code></td>
          <td>integer</td>
          <td><span class="badge bg-secondary">NO</span></td>
          <td>If present, will create row in <code>lead_applications</code> and <code>application_form_responses</code></td>
        </tr>

        <tr>
          <td><code>source</code></td>
          <td>integer</td>
          <td><span class="badge bg-secondary">NO</span></td>
          <td>Lead Source ID</td>
        </tr>

        <tr>
          <td><code>campaign</code></td>
          <td>integer</td>
          <td><span class="badge bg-secondary">NO</span></td>
          <td>Lead Campaign ID</td>
        </tr>

        <tr>
          <td><code>medium</code></td>
          <td>integer</td>
          <td><span class="badge bg-secondary">NO</span></td>
          <td>Maps to <code>type_id</code></td>
        </tr>

        <tr>
          <td><code>event_id</code></td>
          <td>integer</td>
          <td><span class="badge bg-secondary">NO</span></td>
          <td>Marketing event ID</td>
        </tr>

        <tr>
          <td><code>qualification</code></td>
          <td>string</td>
          <td><span class="badge bg-secondary">NO</span></td>
          <td>Saved in <code>extra_fields_json</code></td>
        </tr>
      </tbody>
    </table>
  </div>

  <hr>

  <!-- SUCCESS RESPONSE -->
  <h6 class="mb-2">Success Response (200)</h6>
  <pre class="bg-dark text-white p-3 rounded small mb-3">
{
  "status": true,
  "message": "Lead created successfully",
  "lead_id": 123
}</pre>

  <!-- FAIL RESPONSES -->
  <h6 class="mb-2">Failure Responses</h6>

  <div class="mb-2"><b>Unauthenticated (401)</b></div>
  <pre class="bg-dark text-white p-3 rounded small mb-3">
{
  "status": false,
  "message": "Unauthenticated"
}</pre>

  <div class="mb-2"><b>Validation Error (422)</b></div>
  <pre class="bg-dark text-white p-3 rounded small mb-3">
{
  "message": "The mobile field is required.",
  "errors": {
    "mobile": [
      "The mobile field is required."
    ]
  }
}</pre>

  <div class="mb-2"><b>Server Error (500)</b></div>
  <pre class="bg-dark text-white p-3 rounded small">
{
  "status": false,
  "message": "Something went wrong"
}</pre>

</div>


    </div>
  </div>
</div>

                </div>
            </div>

            <div class="card-body table-responsive">
                <table class="table align-middle table-bordered">
                    <thead class="table-light">
                        <tr>
                            <th>ID</th>
                            <th>File</th>
                            <th>Total</th>
                            <th>Valid</th>
                            <th>Duplicate</th>
                            <th>Failed</th>
                            <th>Status</th>
                            <th>Uploaded At</th>
                            <th>Action</th>
                        </tr>
                    </thead>

                    <tbody>
                        @forelse($uploads as $row)
                            <tr>
                                <td>{{ $row->id }}</td>
                                <td><a href="{{ url('/raw-leads/') }}/{{ $row->file_name }}">{{ $row->file_name }}</a></td>
                                <td>{{ $row->total_rows }}</td>
                                <td>{{ $row->valid_rows }}</td>
                                <td>{{ $row->duplicate_rows }}</td>
                                <td>{{ $row->failed_rows }}</td>
                                <td>
                                    @php
                                        $badge = 'secondary';
                                        if($row->status == 'pending') $badge = 'warning';
                                        if($row->status == 'processing') $badge = 'info';
                                        if($row->status == 'completed') $badge = 'success';
                                        if($row->status == 'failed') $badge = 'danger';
                                    @endphp
                                    <span class="badge bg-{{ $badge }}">
                                        {{ strtoupper($row->status) }}
                                    </span>
                                </td>
                                <td>{{ $row->created_at }}</td>

                                <td>
<div class="dropdown">
    <button class="btn btn-sm btn-primary dropdown-toggle" type="button" data-bs-toggle="dropdown">
        Action
    </button>

    <ul class="dropdown-menu">

        <li>
            <a class="dropdown-item"
               href="{{ route('automation.raw-leads.upload.view', $row->id) }}">
                👁 View Data
            </a>
        </li>

        @if($row->status == 'completed')
            <li>
                <a class="dropdown-item"
                   href="{{ route('automation.raw-leads.upload.view', $row->id) }}?tab=send">
                    📩 Send Communication
                </a>
            </li>
        @endif

    </ul>
</div>

</td>
                                
                            </tr>
                        @empty
                            <tr>
                                <td colspan="9" class="text-center">No uploads found.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>

                <div class="mt-3">
                    {{ $uploads->links() }}
                </div>
            </div>
        </div>

    </div>
</div>
@endsection
