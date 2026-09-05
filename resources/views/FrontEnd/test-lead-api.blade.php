@extends('FrontEnd.layouts.master')
@section('title', 'Test Lead API')

@section('content')
@component('FrontEnd.components.breadcrumb')
    @slot('li_1') CRM @endslot
    @slot('title') Test Lead API @endslot
@endcomponent

@php
    $user = Sentinel::check();
    $orgId = $user ? registeruser($user->id)->organization : null;
@endphp

<div class="container">
    <h3>Full Lead API Testing Form</h3>

    <form id="testLeadForm">

        <!-- BASIC INFO -->
        <h5>Basic Info</h5>
        <input type="text" name="name" placeholder="Name" required class="form-control mb-2">
        <input type="text" name="mobile" placeholder="Mobile" required class="form-control mb-2">
        <input type="text" name="alternate_mobile" placeholder="Alternate Mobile" class="form-control mb-2">
        <input type="email" name="email" placeholder="Email" class="form-control mb-2">

        <input type="text" name="city" placeholder="City" class="form-control mb-2">
        <input type="text" name="state" placeholder="State" class="form-control mb-2">
        <input type="text" name="country" value="80" class="form-control mb-2">

        <!-- ORG INFO -->
        <h5>Organization</h5>
        <input type="number" name="organization_id" value="{{ $orgId }}" class="form-control mb-2">
        <input type="number" name="created_by" value="{{ $user->id ?? 0 }}" class="form-control mb-2">

        <!-- ACADEMIC -->
        <h5>Academic Info</h5>
        <input type="number" name="institute_id" placeholder="Institute ID" class="form-control mb-2">
        <input type="number" name="programme_id" placeholder="Programme ID" class="form-control mb-2">
        <input type="number" name="faculty_id" placeholder="Faculty ID" class="form-control mb-2">
        <input type="number" name="application_form_id" placeholder="Application Form ID" required class="form-control mb-2">

        <!-- MARKETING -->
        <h5>Marketing</h5>
        <input type="number" name="lead_source_id" placeholder="Lead Source ID" class="form-control mb-2">
        <input type="number" name="lead_campaign_id" placeholder="Lead Campaign ID" class="form-control mb-2">
        <input type="number" name="lead_marketing_event" placeholder="Marketing Event ID" required class="form-control mb-2">

        <!-- PIPELINE -->
        <h5>Pipeline</h5>
        <input type="number" name="pipeline_id" placeholder="Pipeline ID" class="form-control mb-2">
        <input type="number" name="stage_id" placeholder="Stage ID" class="form-control mb-2">
        <input type="number" name="lead_status_id" placeholder="Lead Status ID" class="form-control mb-2">
        <input type="number" name="lead_sub_status_id" placeholder="Lead Sub Status ID" class="form-control mb-2">

        <!-- ASSIGNMENT -->
        <h5>Assignment</h5>
        <input type="number" name="lead_assign_to" placeholder="Assign To User ID" class="form-control mb-2">

        <!-- SYSTEM -->
        <h5>System</h5>
        <select name="upload_via" class="form-control mb-2">
            <option value="mobile">Mobile</option>
            <option value="email">Email</option>
            <option value="whatsapp">WhatsApp</option>
            <option value="bulk">Bulk</option>
            <option value="google_ads">Google Ads</option>
            <option value="partner_api">Partner API</option>
        </select>

        <select name="source_type" class="form-control mb-3">
            <option value="api_direct">API Direct</option>
            <option value="bulk_cron">Bulk Cron</option>
            <option value="google_ads">Google Ads</option>
            <option value="partner_api">Partner API</option>
        </select>

        <button type="submit" class="btn btn-primary">Create Lead</button>
    </form>

    <hr>
    <div id="responseBox"></div>
</div>

<script>
document.getElementById("testLeadForm").addEventListener("submit", function(e) {
    e.preventDefault();

    let formData = new FormData(this);

    fetch("{{ url('api/leads/add') }}", {
        method: "POST",
        headers: {
            "X-CSRF-TOKEN": "{{ csrf_token() }}"
        },
        body: formData
    })
    .then(res => res.json())
    .then(data => {
        document.getElementById("responseBox").innerHTML =
            "<pre>" + JSON.stringify(data, null, 2) + "</pre>";
    })
    .catch(err => {
        document.getElementById("responseBox").innerHTML =
            "<span style='color:red;'>Error occurred</span>";
    });
});
</script>

@endsection