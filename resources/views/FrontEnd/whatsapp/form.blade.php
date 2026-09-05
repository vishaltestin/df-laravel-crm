@extends('FrontEnd.layouts.master')

@section('title', 'WhatsApp Settings')

@section('css')
<link href="{{ URL::asset('build/libs/sweetalert2/sweetalert2.min.css') }}" rel="stylesheet">
@endsection

@section('content')

@component('FrontEnd.components.breadcrumb')
    @slot('li_1') CRM @endslot
    @slot('title') WhatsApp Settings @endslot
@endcomponent

@php
    // null safe
    $providerUrl = $setting->provider_url ?? 'https://www.digitalfueled.in/api/whatsapp.php';
    $username    = $setting->username ?? 'df001#';
    $password    = $setting->password ?? 'df#123';
    $status      = $setting->status ?? 1;
@endphp

<div class="row">

    <div class="col-lg-6">
        <div class="card">
            <div class="card-body">
                <h4>WhatsApp Settings</h4>
                <p class="text-muted">
                    Configure here your WhatsApp setting for sending WhatsApp messages.
                </p>

                <hr>

                <p class="mb-1"><b>API URL:</b> {{ $providerUrl }}</p>
                <p class="mb-1"><b>Method:</b> POST</p>
                <p class="mb-0"><b>Parameters:</b> user, password, mobile, name, message</p>
            </div>
        </div>
    </div>

    <div class="col-lg-6">
        <div class="card" id="leadsList">
            <div class="card-body">

                <form id="whatsappForm">
                    @csrf

                    <div class="mb-2">
                        <label class="form-label">Provider URL</label>
                        <input type="text" name="provider_url" class="form-control"
                               value="{{ $providerUrl }}">
                    </div>

                    <div class="mb-2">
                        <label class="form-label">Username</label>
                        <input type="text" name="username" class="form-control"
                               value="{{ $username }}">
                    </div>

                    <div class="mb-2">
                        <label class="form-label">Password</label>
                        <input type="text" name="password" class="form-control"
                               value="{{ $password }}">
                    </div>

                    <div class="mb-2">
                        <label class="form-label">Status</label>
                        <select name="status" class="form-select">
                            <option value="1" {{ $status == 1 ? 'selected' : '' }}>Enabled</option>
                            <option value="0" {{ $status == 0 ? 'selected' : '' }}>Disabled</option>
                        </select>
                    </div>

                    <button class="btn btn-primary mt-2" type="submit">
                        Save
                    </button>

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
$("#whatsappForm").on("submit", function(e){
    e.preventDefault();

    $.ajax({
        url: "{{ url('whatsapp/update') }}",
        type: "POST",
        data: $(this).serialize(),
        success: function(res){
            Swal.fire({
                icon: "success",
                title: "Saved",
                text: res.message ?? "WhatsApp settings updated successfully"
            });
        },
        error: function(xhr){
            let msg = "Save failed";

            if(xhr.responseJSON && xhr.responseJSON.message){
                msg = xhr.responseJSON.message;
            }

            Swal.fire({
                icon: "error",
                title: "Error",
                text: msg
            });
        }
    });
});
</script>
@endsection
