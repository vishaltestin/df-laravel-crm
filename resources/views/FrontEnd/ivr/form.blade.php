@extends('FrontEnd.layouts.master')
@section('title', 'IVR Settings')

@section('css')
<link href="{{ URL::asset('build/libs/sweetalert2/sweetalert2.min.css') }}" rel="stylesheet">
@endsection

@section('content')
@component('FrontEnd.components.breadcrumb')
    @slot('li_1') CRM @endslot
    @slot('title') IVR Settings @endslot
@endcomponent

<div class="row">

    <div class="col-lg-6">
        <div class="p-3">
            <h4>IVR</h4>
            <p>Configure here your IVR setting for calls, click-to-call, call logs & contacts (Organization wise).</p>
        </div>
    </div>

    <div class="col-lg-6">
        <div class="card" id="leadsList">
            <div class="card-body">

                <form id="ivrForm">
                    @csrf

                  

                    <div class="mb-2">
                        <label class="form-label">Provider (Optional)</label>
                        <input type="text" name="provider" id="provider" class="form-control"
                               placeholder="ex: telephony"        value="{{ $setting->provider ?? 'telephony' }}">
                    </div>

                    <div class="mb-2">
                        <label class="form-label">Base URL</label>
                        <input type="text" name="base_url" id="base_url" class="form-control"
                               placeholder="https://xxxxxxxxx.com"   value="{{ $setting->base_url ?? '' }}">
                    </div>

                    <div class="mb-2">
                        <label class="form-label">Authcode</label>
                        <input type="text" name="authcode" id="authcode" class="form-control"
                               placeholder="Unique auth key"        value="{{ $setting->authcode ?? '' }}">
                    </div>

                    <div class="mb-2">
                        <label class="form-label">Default Deskphone (Optional)</label>
                        <input type="text" name="default_deskphone" id="default_deskphone" class="form-control"
                               placeholder="0120xxxxxx"        value="{{ $setting->default_deskphone ?? '' }}">
                    </div>

                    <div class="mb-2">
                        <label class="form-label">Caller ID (Optional)</label>
                        <input type="text" name="caller_id" id="caller_id" class="form-control"
                               placeholder="0120xxxxxx"        value="{{ $setting->caller_id ?? '' }}">
                    </div>

                    <div class="mb-2">
                        <label class="form-label">Status</label>
                        <select name="status" id="status" class="form-control">
                            <option value="1" {{ ($setting->status ?? 1) == 1 ? 'selected' : '' }}>Active</option>
    <option value="0" {{ ($setting->status ?? 1) == 0 ? 'selected' : '' }}>Inactive</option>
                        </select>
                    </div>

                    <div class="d-flex gap-2 mt-3">
                        <button type="submit" class="btn btn-primary">Save</button>
                        <button type="button" id="btnTest" class="btn btn-outline-secondary">Test Connection</button>
                  <button type="button" class="btn btn-info" id="btnApiNotes">
    📌 API Notes
</button>  </div>

                </form>

            </div>
        </div>
    </div>

</div>
@endsection

@section('script')
<script src="{{ URL::asset('build/libs/sweetalert2/sweetalert2.min.js') }}"></script>
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

<script>
$(document).ready(function(){

      function fillForm(data){
        $("#provider").val(data.provider ?? "telephony");
        $("#base_url").val(data.base_url ?? "");
        $("#authcode").val(data.authcode ?? "");
        $("#default_deskphone").val(data.default_deskphone ?? "");
        $("#caller_id").val(data.caller_id ?? "");
        $("#status").val((data.status ?? 1).toString());
    }

    // ✅ Page load: fetch current org setting
    $.ajax({
        url: "{{ url('ivr/get') }}",
        type: "POST",
        data: {
            _token: "{{ csrf_token() }}"
        },
        success: function(res){
            if(res.status === 'success'){
                fillForm(res.data);
            }
        },
        error: function(){
            // silent
        }
    });

    // On organization change => load settings
    $("#organization_id").on("change", function(){
        let organization_id = $(this).val();

        if(!organization_id){
            fillForm({});
            return;
        }

        $.ajax({
            url: "{{ url('ivr/get') }}",
            type: "POST",
            data: {
                _token: "{{ csrf_token() }}",
                organization_id: organization_id
            },
            success: function(res){
                if(res.status){
                    fillForm(res.data);
                }
            },
            error: function(){
                Swal.fire("Error", "Unable to load settings.", "error");
            }
        });
    });

    // Save settings
      $("#ivrForm").on("submit", function(e){
        e.preventDefault();

        $.ajax({
            url: "{{ url('ivr/update') }}",
            type: "POST",
            data: $(this).serialize(),
            success: function(res){
                Swal.fire("Saved!", res.message, "success");
            },
            error: function(xhr){
                Swal.fire("Error!", xhr.responseJSON?.message ?? "Something went wrong", "error");
            }
        });
    });

    // Test connection
        $("#btnTest").on("click", function(){

        let base_url = $("#base_url").val();
        let authcode = $("#authcode").val();

        if(!base_url || !authcode){
            Swal.fire("Required", "Base URL and Authcode required for testing.", "warning");
            return;
        }

        $.ajax({
            url: "{{ url('ivr/test') }}",
            type: "POST",
            data: {
                _token: "{{ csrf_token() }}",
                base_url: base_url,
                authcode: authcode
            },
            success: function(res){
                Swal.fire("Success!", res.message, "success");
            },
            error: function(xhr){
                Swal.fire("Failed!", xhr.responseJSON?.message ?? "Connection failed", "error");
            }
        });
    });
});
</script><script>
$("#btnApiNotes").on("click", function(){

    let html = `
    <div style="text-align:left; font-size:14px; line-height:1.5">

        <h4 style="margin:0 0 10px 0;">Cloud Telephony API Notes</h4>

        <p style="margin:0 0 12px 0;">
            <b>Base URL:</b> <code>{base_url}</code><br>
            <b>Auth:</b> Most APIs use header: <code>authcode: {authcode}</code>
        </p>

        <hr>

        <h5>1) Contacts / CRM</h5>
        <ul>
            <li><b>Add Contact</b> (POST) <code>/api/savecontact_v2</code></li>
            <li><b>Edit Contact</b> (POST) <code>/api/editContact_v2</code></li>
            <li><b>Delete Contact</b> (POST) <code>/api/deleteContact</code></li>
            <li><b>Contact List</b> (POST) <code>/api/contact_list_v2</code></li>
        </ul>

        <h5>2) Team / Members (Agents)</h5>
        <ul>
            <li><b>Add Member</b> (POST) <code>/api/addmember_v2</code></li>
            <li><b>Update Member</b> (POST) <code>/api/updatemember_v2</code></li>
            <li><b>Delete Member</b> (POST) <code>/api/deletemember_v2</code></li>
            <li><b>Member List</b> (POST) <code>/api/getmemberlist_V2</code></li>
            <li><b>Member Analysis</b> (POST) <code>/api/member_analysis_v2</code></li>
        </ul>

        <h5>3) Call Groups</h5>
        <ul>
            <li><b>Create Group</b> (POST) <code>/api/createcallgroup</code></li>
            <li><b>Update Group</b> (POST) <code>/api/updategroup_v2</code></li>
            <li><b>Delete Group</b> (POST) <code>/api/deletegroup</code></li>
            <li><b>Group List</b> (POST) <code>/api/getgrouplist_v2</code></li>
            <li><b>Group By ID</b> (POST) <code>/api/getgroupbyid_v2</code></li>
            <li><b>Group Schedule</b> (POST) <code>/api/getgroupschedule</code></li>
        </ul>

        <h5>4) Calling / Call Logs</h5>
        <ul>
            <li><b>Call Logs</b> (POST) <code>/api/call_list_v2</code></li>
            <li><b>Routing Details</b> (POST) <code>/api/getroutingdetail_V2</code></li>
            <li><b>Live Calls</b> (GET) <code>/api/live_call_v2?authcode=...</code></li>
        </ul>

        <h5>5) Click2Call (Outgoing)</h5>
        <ul>
            <li><b>Normal Click2Call</b> (GET) <code>/api/click_to_call_v2</code></li>
            <li><b>Click2Call With Group</b> (GET) <code>/api/click_to_call_v2 + group_name</code></li>
            <li><b>Reverse Click2Call</b> (GET) <code>/api/click_to_call_v3</code></li>
        </ul>

        <h5>6) Broadcast (OBD)</h5>
        <ul>
            <li><b>Create Campaign</b> (POST multipart) <code>/api/create_obd_campaign_v2</code></li>
            <li><b>Broadcast Logs</b> (POST) <code>/api/obdlogs_v2</code></li>
        </ul>

        <h5>7) Security / Blocking</h5>
        <ul>
            <li><b>Block Number</b> (POST) <code>/api/block_number</code></li>
            <li><b>Unblock Number</b> (POST) <code>/api/unblock_number</code></li>
        </ul>

        <h5>8) Login / Profile</h5>
        <ul>
            <li><b>Signin</b> (POST) <code>/api_v2/signin</code></li>
            <li><b>Profile Billing</b> (POST) <code>/api/profile_billing_v2</code></li>
        </ul>

        <h5>9) Voice OTP</h5>
        <ul>
            <li><b>OTP On Call</b> (GET) <code>/api/otp_on_call_v2</code></li>
        </ul>

        <hr>

        <p style="margin:0;">
            <b>Tip:</b> Always store <code>base_url</code> and <code>authcode</code> in IVR Settings first.
        </p>

    </div>
    `;

    Swal.fire({
        title: "API Notes",
        html: html,
        width: 900,
        confirmButtonText: "Close",
        showCloseButton: true
    });

});
</script>

@endsection
