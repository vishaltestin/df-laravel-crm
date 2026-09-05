@extends('FrontEnd.layouts.master')
@section('title','Bulk Lead Upload')

@section('content')

@php
$primaryKey = request('primary_key','email');
@endphp

<div class="card">
  <div class="card-header d-flex align-items-center justify-content-between">
    <h5 class="mb-0">Bulk Offline Upload of Leads</h5>
<a href="{{ url('/leads/upload') }}" class="btn btn-success btn-sm">
  + New Upload
</a>
<a href="{{ url('/leads/upload/logs') }}" class="btn btn-primary btn-sm">
  View Upload Logs
</a>

    <a href="{{ route('leads.upload.sample', ['primary_key' => $primaryKey]) }}"
       class="btn btn-primary btn-sm">
      Download Sample CSV
    </a>
  </div>
@if(session('success'))
  <div class="alert alert-success">
    {{ session('success') }}
  </div>
@endif

@if(session('error'))
  <div class="alert alert-danger">
    {{ session('error') }}
  </div>
@endif

@if ($errors->any())
  <div class="alert alert-danger">
    <ul class="mb-0">
      @foreach ($errors->all() as $error)
        <li>{{ $error }}</li>
      @endforeach
    </ul>
  </div>
@endif

  <div class="card-body">

    {{-- INSTRUCTIONS --}}
    <div class="alert alert-info">
      <strong>CSV Instructions:</strong>
      <ol class="mb-0">
        <li>CSV format only</li>
        <li>Header row mandatory</li>
        <li>
          Required field:
          <b>{{ strtoupper($primaryKey) }}</b>
        </li>
        <li>No blank rows allowed</li>
        <li>Country/State/City must match predefined list</li>
        <li>Lead Source recommended</li>
        <li>Multi-select separator: <b>|</b></li>
        <li>Field Registration Attempt: Yes / No</li>
      </ol>
    </div>

    {{-- FORM --}}
    <form method="POST"
          action="{{ route('leads.upload.store') }}"
          enctype="multipart/form-data">
      @csrf

      <input type="hidden" name="primary_key" value="{{ $primaryKey }}">

      <div class="row mb-3">

        {{-- Upload Type --}}
        <div class="col-md-4">
          <label class="form-label">Upload Type</label>
          <select name="upload_action" class="form-select">
            <option value="create">Create New Leads</option>
            <option value="update">Update Existing Leads</option>
          </select>
        </div>

        {{-- Institute --}}
        <div class="col-md-4">
          <label class="form-label">Institute / Applying For</label>
          <select name="institute_id" id="institute_id" class="form-select">
            <option value="">Select Institute</option>
            @if(!empty($institutes))
              @foreach($institutes as $ins)
                <option value="{{ $ins->id }}">{{ $ins->name }}</option>
              @endforeach
            @endif
          </select>
        </div>
 

      </div>

      <div class="row mb-3">

        {{-- Program --}}
        <div class="col-md-4">
          <label class="form-label">Programme</label>
          <select name="program_id" id="program_id" class="form-select">
            <option value="">Select Programme</option>
          </select>
        </div>

        {{-- Course --}}
        <div class="col-md-4">
          <label class="form-label">Course</label>
          <select name="course_id" id="course_id" class="form-select">
            <option value="">Select Course</option>
          </select>
        </div>

        {{-- Assign User --}}
        <div class="col-md-4">
          <label class="form-label">Assign User</label>
          <select name="lead_assign_to" class="form-select">
            <option value="">Select</option>
            @if(!empty($users))
              @foreach($users as $u)
                <option value="{{ $u->id }}">
                  {{ $u->first_name }} {{ $u->last_name }}
                </option>
              @endforeach
            @endif
          </select>
        </div>

      </div>

      <div class="row mb-3">

        {{-- Assign same Owner --}}
        <div class="col-md-4">
          <label class="form-label">Assign same Owner against application</label>
          <select name="assign_same_owner" class="form-select">
            <option value="no">No</option>
            <option value="yes">Yes</option>
          </select>
        </div>

        {{-- CSV File --}}
        <div class="col-md-8">
          <label class="form-label">CSV File</label>
          <input type="file" name="csv_file" class="form-control" required>
          <small class="text-muted">
            Required column: <b>{{ $primaryKey }}</b>
          </small>
        </div>

      </div>

      <button class="btn btn-success">
        Upload Leads
      </button>

    </form>

  </div>
</div>
<div class="modal fade" id="uploadTypeModal" tabindex="-1"> <div class="modal-dialog"> <div class="modal-content"> <div class="modal-header"> <h5 class="modal-title">Select Primary Upload Type</h5> <!-- Top Close Icon --> <button type="button" class="btn-close" data-bs-dismiss="modal"></button> </div> <div class="modal-body"> <label class="form-check"> <input type="radio" name="primary_key" value="email" checked> Email (Unique) </label> <label class="form-check mt-2"> <input type="radio" name="primary_key" value="mobile"> Mobile (Unique) </label> </div> <div class="modal-footer"> <!-- Cancel Button --> <button type="button" class="btn btn-light" data-bs-dismiss="modal"> Cancel </button> <button type="button" class="btn btn-primary" id="openInstructions"> Continue </button> </div> </div> </div> </div>
{{-- DEPENDENT DROPDOWNS --}}
<script>
document.addEventListener("DOMContentLoaded", function(){

  const institute = document.getElementById("institute_id");
  const program   = document.getElementById("program_id");
  const course    = document.getElementById("course_id");

  // Institute -> Program
  institute.addEventListener("change", function(){

    let instituteId = this.value;

    program.innerHTML = `<option value="">Loading...</option>`;
    course.innerHTML  = `<option value="">Select Course</option>`;

    if(!instituteId){
      program.innerHTML = `<option value="">Select Programme</option>`;
      return;
    }

    fetch(`{{ url('/api/programs') }}?institute_id=${instituteId}`)
      .then(res => res.json())
      .then(data => {

        program.innerHTML = `<option value="">Select Programme</option>`;

        if(!data || data.length === 0){
          program.innerHTML = `<option value="">No Programme Found</option>`;
          return;
        }

        data.forEach(item => {
          program.innerHTML += `<option value="${item.id}">${item.name}</option>`;
        });

      }).catch(() => {
        program.innerHTML = `<option value="">Error loading programmes</option>`;
      });
  });

  // Program -> Course
  program.addEventListener("change", function(){

    let programId = this.value;

    course.innerHTML = `<option value="">Loading...</option>`;

    if(!programId){
      course.innerHTML = `<option value="">Select Course</option>`;
      return;
    }

    fetch(`{{ url('/api/courses') }}?program_id=${programId}`)
      .then(res => res.json())
      .then(data => {

        course.innerHTML = `<option value="">Select Course</option>`;

        if(!data || data.length === 0){
          course.innerHTML = `<option value="">No Course Found</option>`;
          return;
        }

        data.forEach(item => {
          course.innerHTML += `<option value="${item.id}">${item.name}</option>`;
        });

      }).catch(() => {
        course.innerHTML = `<option value="">Error loading courses</option>`;
      });
  });

});
</script>
<script>
document.addEventListener("DOMContentLoaded", function () {

    const urlParams = new URLSearchParams(window.location.search);
    const primaryKey = urlParams.get('primary_key');

    // Agar primary_key URL me nahi hai to modal open karo
    if (!primaryKey) {
        let uploadModal = new bootstrap.Modal(document.getElementById('uploadTypeModal'));
        uploadModal.show();
    }

});
</script><script>
document.getElementById('openInstructions').addEventListener('click', function () {

    let selected = document.querySelector('input[name="primary_key"]:checked').value;

    // Page reload with query param
    window.location.href = "{{ url('leads/upload') }}?primary_key=" + selected;

});
</script>

@endsection
