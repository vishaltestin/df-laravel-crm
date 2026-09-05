@extends('FrontEnd.layouts.master')
@section('title','Lead Upload Logs')

@section('content')

<div class="card">
  <div class="card-header d-flex align-items-center justify-content-between">
    <h5 class="mb-0">Lead Upload Logs</h5>

    <a href="{{ route('leads.upload') }}" class="btn btn-success btn-sm">
      + New Upload
    </a>
  </div>

  <div class="card-body">

    @if(session('success'))
      <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    @if(session('error'))
      <div class="alert alert-danger">{{ session('error') }}</div>
    @endif

    <div class="table-responsive">
      <table class="table table-bordered table-striped align-middle">
        <thead>
          <tr>
            <th>#</th>
            <th>File</th>
            <th>Uploaded By</th>
            <th>Institute</th>
            <th>Program</th>
            <th>Course</th>
            <th>Assigned To</th>
            <th>Upload Type</th>
            <th>Rows</th>
            <th>Status</th>
            <th>Uploaded At</th>
            <th>Action</th>
          </tr>
        </thead>

        <tbody>
          @forelse($logs as $row)
            <tr>
              <td>{{ $row->id }}</td>

              <td>
                <div><b>{{ $row->file_name }}</b></div>
                <small class="text-muted">{{ $row->file_path }}</small>
              </td>

              <td>{{ $row->uploaded_by_name }}</td>

              <td>{{ $row->institute_name }}</td>
              <td>{{ $row->program_name }}</td>
              <td>{{ $row->course_name }}</td>

              <td>{{ $row->assigned_to_name }}</td>

              <td>
                <span class="badge bg-info">
                  {{ strtoupper($row->upload_type) }}
                </span>
              </td>

              <td>
                <div>Total: <b>{{ $row->total_rows }}</b></div>
                <div>Valid: <b>{{ $row->valid_rows }}</b></div>
                <div>Failed: <b>{{ $row->failed_rows }}</b></div>
                <div>Duplicate: <b>{{ $row->duplicate_rows }}</b></div>
              </td>

              <td>
                @if($row->status == 'uploaded')
                  <span class="badge bg-secondary">UPLOADED</span>
                @elseif($row->status == 'processing')
                  <span class="badge bg-warning">PROCESSING</span>
                @elseif($row->status == 'completed')
                  <span class="badge bg-success">COMPLETED</span>
                @elseif($row->status == 'failed')
                  <span class="badge bg-danger">FAILED</span>
                @else
                  <span class="badge bg-dark">{{ $row->status }}</span>
                @endif
              </td>

              <td>
                {{ \Carbon\Carbon::parse($row->created_at)->format('d-m-Y h:i A') }}
              </td>

              <td>
                <a href="{{ url($row->file_path) }}" target="_blank" class="btn btn-primary btn-sm">
                  Download
                </a>

                {{-- Optional: Process Now button --}}
                @if($row->status == 'uploaded')
                  <form action="{{ route('leads.upload.process', $row->id) }}"
                        method="POST"
                        style="display:inline-block;">
                    @csrf
                    
                  </form>
                @endif
              </td>
            </tr>
          @empty
            <tr>
              <td colspan="12" class="text-center">
                No Upload Logs Found
              </td>
            </tr>
          @endforelse
        </tbody>
      </table>
    </div>

    <div class="mt-3">
      {{ $logs->links() }}
    </div>

  </div>
</div>

@endsection
