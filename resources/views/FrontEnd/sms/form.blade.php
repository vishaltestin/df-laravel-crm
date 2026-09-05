@extends('FrontEnd.layouts.master')
@section('title', 'Configuration')

@section('css')
<link href="{{ URL::asset('build/libs/sweetalert2/sweetalert2.min.css') }}" rel="stylesheet">
@endsection

@section('content')

@component('FrontEnd.components.breadcrumb')
    @slot('li_1') CRM @endslot
    @slot('title') SMS Settings @endslot
@endcomponent


<div class="row">

    <!-- LEFT INFO BLOCK -->
    <div class="col-lg-6">
        <div class="container mt-3">
            <h4>SMS Settings</h4>
            <p>Configure here your SMS settings for sending messages.</p>
        </div>
    </div>


    <!-- RIGHT SETTINGS FORM -->
    <div class="col-lg-6">
        <div class="card" id="smsSettings">
            <div class="card-body">

                <!-- 1️⃣ FORM : SAVE SETTINGS -->
                <form id="smsForm">
                    @csrf
                    <h5 class="mb-3">API Configuration</h5>

                    <div class="mb-2">
                        <label>Provider</label>
                        <input type="text" name="provider" class="form-control"
                               value="{{ $setting->provider }}">
                    </div>

                    <div class="mb-2">
                        <label>API Key</label>
                        <input type="text" name="api_key" class="form-control"
                               value="{{ $setting->api_key }}">
                    </div>

                    <div class="mb-2">
                        <label>Sender ID</label>
                        <input type="text" name="sender_id" class="form-control"
                               value="{{ $setting->sender_id }}">
                    </div>

                    <div class="mb-2">
                        <label>API URL</label>
                        <input type="text" name="api_url" class="form-control"
                               value="{{ $setting->api_url }}">
                    </div>

                    <div class="mb-2">
                        <label>Method (GET/POST)</label>
                        <select name="http_method" class="form-control">
                            <option value="GET"  {{ $setting->http_method=='GET' ? 'selected':'' }}>GET</option>
                            <option value="POST" {{ $setting->http_method=='POST' ? 'selected':'' }}>POST</option>
                        </select>
                    </div>

                    <div class="mb-2">
                        <label>Mobile Param</label>
                        <input type="text" name="mobile_param" class="form-control"
                               value="{{ $setting->mobile_param }}">
                    </div>

                    <div class="mb-2">
                        <label>Message Param</label>
                        <input type="text" name="message_param" class="form-control"
                               value="{{ $setting->message_param }}">
                    </div>

                    <div class="mb-2">
                        <label>Extra Params (Raw String)</label>
                        <input type="text" name="extra_params" class="form-control"
                               value="{{ $setting->extra_params }}">
                    </div>

                    <button class="btn btn-primary mt-2 w-100">Save Settings</button>
                </form>

                <hr class="my-4">

                <!-- 2️⃣ FORM : TEST SMS ONLY IF SETTINGS FILLED -->
                @if($setting->provider != '')
                <h5 class="mb-3">Send Test SMS</h5>

                <form id="smsTestForm">
                    @csrf

                    <div class="mb-2">
                        <label>Test Mobile Number</label>
                        <input type="text" id="test_mobile" class="form-control"
                               placeholder="Enter mobile number">
                    </div>

                    <div class="mb-2">
                        <label>Test Message</label>
                        <input type="text" id="test_message" class="form-control"
                               placeholder="Enter test message">
                    </div>

                    <button type="button" id="testSmsBtn" class="btn btn-info w-100">
                        Send Test SMS
                    </button>
                </form>
                @endif

            </div>
        </div>
    </div>
</div>

@endsection


@section('script')

<script src="{{ URL::asset('build/libs/sweetalert2/sweetalert2.min.js') }}"></script>
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>


<!-- SAVE FORM -->
<script>
$("#smsForm").on("submit", function(e){
    e.preventDefault();

    $.ajax({
        url: "{{ url('sms/update') }}",
        type: "POST",
        data: $(this).serialize(),
        success: function(res){
            Swal.fire("Done!", res.message, "success");
        }
    });
});
</script>


<!-- TEST SMS FORM -->
<script>
$("#testSmsBtn").on("click", function(e){
    e.preventDefault();

    $.post("{{ url('sms/test') }}", {
        mobile: $("#test_mobile").val(),
        message: $("#test_message").val(),
        _token: "{{ csrf_token() }}"
    }, function(res){
        Swal.fire("Server Response", res.response, "info");
    });
});
</script>

@endsection
