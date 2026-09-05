@extends('FrontEnd.layouts.master')

@section('title', 'Create Web Forms')

@section('css')
<link href="{{ URL::asset('build/libs/sweetalert2/sweetalert2.min.css') }}" rel="stylesheet">
<style>
    .attribute-row .attr-card-title {
        font-size: 14px;
    }

    .attribute-row .moveUpBtn,
    .attribute-row .moveDownBtn {
        margin-right: 5px;
    }

    .options-wrapper {
        margin-top: 8px;
    }
</style>
<style>
    :root {
        --preview-primary: #0E90D9;
        --preview-title: #263238;
        --preview-label: #546E7A;
    }

    #previewContainer {
        border: none !important;
        padding: 10px !important;
        transition: background-color 0.3s ease;
        border-radius: 12px;
    }

    #previewFormBox {
        font-family: 'Outfit', sans-serif;
        padding: 16px !important;
        border-radius: 20px !important;
        box-shadow: 0 10px 30px rgba(0, 0, 0, 0.08);
        width: 100%;
    }

    #previewTitle {
        font-weight: 700;
        font-size: 1.4rem;
        text-align: center;
        margin-bottom: 5px;
        position: relative;
    }

    /* The "Divider" in Preview */
    #previewTitle::after {
        content: "";
        display: block;
        width: 100%;
        height: 2px;
        background: linear-gradient(90deg, transparent, var(--preview-primary), transparent);
        margin: 15px 0 25px 0;
        opacity: 0.5;
    }

    /* --- FORM FIELDS --- */
    #previewAttrs label {
        font-weight: 600;
        font-size: 0.8rem;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        margin-bottom: 8px;
        display: block;
    }

    #previewAttrs .form-control,
    #previewAttrs .form-select {
        border: 2px solid #edf2f7;
        border-radius: 10px;
        background-color: #f7fafc;
        min-height: 45px;
        font-size: 0.9rem;
    }

    #previewAttrs .form-control:focus, 
    #previewAttrs .form-select:focus {
        border-color: var(--preview-primary);
        background-color: #fff;
    }

    /* --- CAPTCHA PREVIEW --- */
    .preview-captcha-wrapper {
        background: #f8fafc;
        border: 1px solid #edf2f7;
        padding: 15px;
        border-radius: 14px;
        margin-top: 20px;
    }

    .preview-captcha-controls {
        display: flex;
        align-items: center;
        gap: 10px;
        margin-bottom: 10px;
    }

    .preview-captcha-img-box {
        flex: 2;
        background: #fff;
        border: 1px solid #cbd5e1;
        border-radius: 8px;
        height: 50px;
        display: flex;
        align-items: center;
        justify-content: center;
    }

    #captchaImg {
        height: 35px;
        width: auto;
    }

    .preview-reload-btn {
        flex: 1;
        height: 50px;
        background: transparent;
        border: 1.5px solid var(--preview-primary);
        color: var(--preview-primary);
        border-radius: 8px;
        font-size: 0.7rem;
        font-weight: 700;
        display: flex;
        gap: 4px;
        align-items: center;
        justify-content: center;
        transition: all 0.2s ease-in-out;
    }

    .preview-reload-btn:hover {
        background: var(--preview-primary);
        color: white !important;
    }

    /* --- SUBMIT BUTTON --- */
    #previewButton {
        border-radius: 12px !important;
        padding: 12px !important;
        font-weight: 700 !important;
        text-transform: uppercase;
        letter-spacing: 1px;
        border: none !important;
        color: #fff !important;
        margin-top: 20px;
        transition: transform 0.2s;
    }
</style>
@endsection

@section('content')

@component('FrontEnd.components.breadcrumb')
@slot('li_1') CRM @endslot
@slot('title') Create Web Forms @endslot
@endcomponent

<div class="row">
    <div class="col-lg-12">
        <div class="card" id="webformCreate">
            <div class="card-body">

                <div class="row">

                    <!-- LEFT SIDE FORM -->
                    <div class="col-md-8">
                        <h4>Create Webform</h4>

                        <div class="card p-3">

                            <div class="mb-3">
                                <label>Title *</label>
                                <input type="text" id="title" class="form-control" required>
                            </div>

                            <div class="mb-3">
                                <label>Description</label>
                                <textarea id="description" class="form-control" rows="3"></textarea>
                            </div>

                            <div class="mb-3">
                                <label>Submit Button Label *</label>
                                <input type="text" id="submit_button_label" class="form-control" value="Submit">
                            </div>

                            <div class="mb-3">
                                <label>Submit Success Action *</label>
                                <select id="submit_success_action" class="form-control" required>
                                    <option value="message">Display custom message</option>
                                    <option value="redirect">Redirect To Url</option>
                                </select>
                            </div>

                            <div class="mb-3">
                                <label id="submit_success_label">Message to display</label>
                                <input type="text" id="submit_success_content" required class="form-control" placeholder="Enter message to display">
                            </div>

                            <div class="mb-3">
                                <label>Create Lead</label>
                                <div class="form-check form-switch">
                                    <input class="form-check-input" type="checkbox" id="create_lead" checked>
                                </div>
                            </div>

                            <h5>Customize Webform Colors</h5>
                            <div class="row">

                                <div class="col-md-6 mb-2">
                                    <label>Background Color</label>
                                    <input type="color" id="background_color" class="form-control form-control-color" value="#F7F8F9">
                                </div>

                                <div class="col-md-6 mb-2">
                                    <label>Form Background Color</label>
                                    <input type="color" id="form_background_color" class="form-control form-control-color" value="#FFFFFF">
                                </div>

                                <div class="col-md-6 mb-2">
                                    <label>Form Title Color</label>
                                    <input type="color" id="form_title_color" class="form-control form-control-color" value="#263238">
                                </div>

                                <div class="col-md-6 mb-2">
                                    <label>Submit Button Color</label>
                                    <input type="color" id="form_submit_button_color" class="form-control form-control-color" value="#0E90D9">
                                </div>

                                <div class="col-md-6 mb-2">
                                    <label>Attribute Label Color</label>
                                    <input type="color" id="attribute_label_color" class="form-control form-control-color" value="#546E7A">
                                </div>

                            </div>

                            <hr>

                            {{-- LEAD SETTINGS --}}
                            <h6 class="fw-bold text-primary">Lead Configuration</h6>

                            <input type="hidden" id="organization_id" value="{{ $orgId }}">
                            <input type="hidden" id="lead_status" value="0">
                            <input type="hidden" id="lead_score" value="0">
                            <input type="hidden" id="faculty" value="0">

                            <div class="row">
                                <div class="col-md-4 mb-1">
                                    <label>Lead Source</label>
                                    <select id="lead_source_id" class="form-control form-select">
                                        <option value="">Select Source</option>
                                        @foreach($leadSources ?? [] as $s)
                                        <option value="{{ $s->id }}">{{ $s->name }}</option>
                                        @endforeach
                                    </select>
                                </div>

                                <div class="col-md-4 mb-1">
                                    <label>Campaign</label>
                                    <select id="lead_campaign_id" class="form-control form-select">
                                        <option value="">Select Campaign</option>
                                        @foreach($campaigns ?? [] as $c)
                                        <option value="{{ $c->id }}">{{ $c->name }}</option>
                                        @endforeach
                                    </select>
                                </div>

                                <div class="col-md-4 mb-1">
                                    <label>Pipeline</label>
                                    <select id="pipeline_id" class="form-control form-select">
                                        <option value="">Select Pipeline</option>
                                        @foreach($pipelines ?? [] as $p)
                                        <option value="{{ $p->id }}">{{ $p->name }}</option>
                                        @endforeach
                                    </select>
                                </div>

                                <div class="col-md-4 mb-1">
                                    <label>Lead Type</label>
                                    <select id="type_id" class="form-control form-select">
                                        <option value="">Select Lead Type</option>
                                        @foreach($leadTypes ?? [] as $t)
                                        <option value="{{ $t->id }}">{{ $t->name }}</option>
                                        @endforeach
                                    </select>
                                </div>

                                <div class="col-md-6 mb-1">
                                    <label>Select Institutes</label>
                                    <select id="institute_select" class="form-control form-select" multiple>
                                        @foreach($institutes ?? [] as $ins)
                                        @if($ins->status == 1)
                                        <option value="{{ $ins->id }}">{{ $ins->name }}</option>
                                        @endif
                                        @endforeach
                                    </select>
                                </div>


                            </div>

                            <hr>

                            <h5>Attributes</h5>
                            <p class="text-muted">Add custom attributes to the form. Name, Email and Mobile must be present and required.</p>

                            <div id="attributesWrapper"></div>

                            <button class="btn btn-warning my-2" id="addAttrBtn">+ Add Attribute</button>

                            <div class="mt-3">
                                <button class="btn btn-success" id="saveBtn">Save Webform</button>
                            </div>

                        </div>
                    </div>



                    <!-- RIGHT SIDE PREVIEW -->
                    <div class="col-md-4">
                        <h5>Live Preview</h5>
                        <div id="previewContainer" class="p-3" style="border:1px solid #dfdfdf; border-radius:6px;">

                            <div id="previewFormBox" style="padding:15px; border-radius:6px;">

                                <h4 id="previewTitle" style="margin-bottom:15px;">-</h4>

                                <div id="previewAttrs"></div>

                                <button id="previewButton" class="btn mt-3 w-100">Submit</button>

                            </div>
                        </div>
                    </div>


                </div>

            </div>
        </div>
    </div>
</div>

@endsection



@section('script')

<script src="{{ URL::asset('build/libs/sweetalert2/sweetalert2.min.js') }}"></script>
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
$(function() 
{
let currentCaptcha = '';

function generateCaptcha(){
    const num1 = Math.floor(Math.random() * 10);
    const num2 = Math.floor(Math.random() * 10);
    currentCaptcha = num1 + num2;
    return `${num1} + ${num2} = ?`;
}
    const allowedTypes = [
        'text','textarea',
        'email','email_otp',
        'number','mobile','mobile_otp',
        'date','time','datetime-local',
        'select','radio','checkbox',
        'state','city',
        'institute','programme','course'
    ];

    function normalizeType(t) {
        if (t === 'mobile') return 'number';
        if (['state','city','institute','programme','course'].includes(t)) return t;
        return t;
    }

    let statesCache = null;

    $('#background_color, #form_background_color, #form_title_color, #form_submit_button_color, #attribute_label_color')
        .on('change input', updatePreviewColors);

    function updatePreviewColors() {
        const primary = $('#form_submit_button_color').val();
        const titleColor = $('#form_title_color').val();
        const labelColor = $('#attribute_label_color').val();

        document.documentElement.style.setProperty('--preview-primary', primary);
        document.documentElement.style.setProperty('--preview-title', titleColor);
        document.documentElement.style.setProperty('--preview-label', labelColor);

        $('#previewContainer').css('background-color', $('#background_color').val());
        $('#previewFormBox').css('background-color', $('#form_background_color').val());
        $('#previewTitle').css('color', titleColor);
        $('#previewButton').css('background-color', primary);
        $('#previewAttrs label').css('color', labelColor);

        $('.preview-reload-btn').css({
            'border-color': primary,
            'color': primary
        });
    }

    $('#title, #submit_button_label').on('input', function() {
        $('#previewTitle').text($('#title').val() || '-');
        $('#previewButton').text($('#submit_button_label').val() || 'Submit');
    });

    let attrIndex = 0;

    function typeOptionsHtml(selected) {
        let html = '';
        allowedTypes.forEach(t => {
            html += `<option value="${t}" ${t === selected ? 'selected' : ''}>${t}</option>`;
        });
        return html;
    }

    function escapeHtml(text) {
        return String(text || '')
            .replace(/&/g,'&amp;')
            .replace(/</g,'&lt;')
            .replace(/>/g,'&gt;')
            .replace(/"/g,'&quot;')
            .replace(/'/g,'&#039;');
    }

    function renderAttributeRow(data = {}) {
        attrIndex++;
        const name = data.name ?? '';
        const lowerName = String(name).toLowerCase();
        const isDefault = ['name'].includes(lowerName);

        const typeVal = data.type ?? 'text';

        const $card = $(`
        <div class="card p-2 mb-2 attribute-row" data-idx="${attrIndex}">
            <div class="d-flex justify-content-between align-items-center">
                <strong>${isDefault ? escapeHtml(name) : 'Attribute'}</strong>
                <div>
						<button type="button" class="btn btn-sm btn-secondary moveUpBtn">↑</button>
						<button type="button" class="btn btn-sm btn-secondary moveDownBtn">↓</button>
						<button type="button" class="btn btn-sm btn-danger removeAttrBtn">Delete</button>
                 </div>
            </div>

            <div class="row mt-2">
                <div class="col-md-4 mb-2">
                    <label>Name *</label>
                    <input type="text" class="form-control attr_name"
                        value="${escapeHtml(name)}"
                        ${isDefault ? 'readonly' : ''}>
                </div>

                <div class="col-md-4 mb-2">
                    <label>Placeholder</label>
                    <input type="text" class="form-control attr_placeholder"
                        value="${escapeHtml(data.placeholder ?? '')}">
                </div>

                <div class="col-md-4 mb-2">
                    <label>Type</label>
                    <select class="form-control attr_type">
                        ${typeOptionsHtml(typeVal)}
                    </select>
                </div>

                <div class="col-md-3 mb-2">
                    <label>Required</label><br>
                    <input type="checkbox" class="attr_required"
                        ${data.is_required ? 'checked' : ''}>
                </div>

                <div class="col-md-3 mb-2">
                    <label>Hidden</label><br>
                    <input type="checkbox" class="attr_hidden">
                </div>

                <div class="col-md-3 mb-2">
                    <label>Sort Order</label>
                    <input type="number" class="form-control attr_sort"
                        value="${data.sort_order ?? 0}">
                </div>
            </div>
        </div>`);

        if (isDefault) $card.find('.removeAttrBtn').remove();
        $('#attributesWrapper').append($card);
    }

    // DEFAULT FIELD
    renderAttributeRow({
        name: 'name',
        placeholder: 'Enter name',
        type: 'text',
        is_required: 1
    });

    renderAttributesPreview();

    $('#addAttrBtn').on('click', function(e) {
        e.preventDefault();
        renderAttributeRow({});
        renderAttributesPreview();
    });

    $(document).on('input change', '.attr_name, .attr_placeholder, .attr_type, .attr_required', function() {
        renderAttributesPreview();
    });

    function renderAttributesPreview() {

    let html = '';
    let idx = 0;

    $('#attributesWrapper .attribute-row').each(function() {

        idx++;

        const name = $(this).find('.attr_name').val().trim();
        const placeholder = $(this).find('.attr_placeholder').val().trim();
        const type = $(this).find('.attr_type').val();
        const required = $(this).find('.attr_required').is(':checked');

        let inputHtml = '';

        // NORMAL INPUT TYPES
        if(type === 'text' || type === 'email' || type === 'number' || type === 'date' || type === 'time' || type === 'datetime-local') {

            const previewType = type === 'mobile' ? 'number' : type;
            inputHtml = `<input class="form-control" type="${previewType}" placeholder="${placeholder}">`;

        }

        // EMAIL OTP
        else if(type === 'email_otp'){

            inputHtml = `
            <div class="otp-wrapper">
                <input type="email" class="form-control otp-email" placeholder="${placeholder}">
                
                <button type="button" 
                    class="btn btn-sm btn-primary mt-2 send-email-otp" 
                    disabled>
                    Get OTP
                </button>

                <div class="otp-box mt-2 d- ">
                    <input type="text" 
                        class="form-control email-otp-input" 
                        placeholder="Enter OTP"
                        disabled>
                </div>
            </div>`;

        }

        // MOBILE OTP
        else if(type === 'mobile_otp'){

            inputHtml = `
            <div class="otp-wrapper">
                <input type="text" maxlength="10" 
                    class="form-control otp-mobile" 
                    placeholder="${placeholder}">
                
                <button type="button" 
                    class="btn btn-sm btn-primary mt-2 send-mobile-otp" 
                    disabled>
                    Get OTP
                </button>

                <div class="otp-box mt-2 d-">
                    <input type="text" 
                        class="form-control mobile-otp-input" 
                        placeholder="Enter OTP"
                        disabled>
                </div>
            </div>`;

        }
         // FALLBACK
        else {
            inputHtml = `<input class="form-control" type="text" placeholder="${placeholder}">`;
        }

        html += `
        <div class="mb-2">
            <label>${name} ${required ? "<span style='color:red'>*</span>" : ""}</label>
            ${inputHtml}
        </div>`;

    });
html += `
<div class="mb-3 mt-3">
    <label>Captcha *</label>
    <div class="d-flex align-items-center gap-2">
        <div class="badge bg-secondary p-2 captcha-question">
            ${generateCaptcha()}
        </div>
        <button type="button" class="btn btn-sm btn-outline-primary refresh-captcha">
            ↻
        </button>
    </div>
    <input type="number" class="form-control mt-2 captcha-input" placeholder="Enter Answer">
</div>
`;
    $('#previewAttrs').html(html);
    updatePreviewColors();
}
   // EMAIL OTP SEND
$(document).on('click', '.send-email-otp', function(){

    const wrapper = $(this).closest('.otp-wrapper');
    const email = wrapper.find('.otp-email').val();

    if(!email) return;

    // Yaha AJAX call lagega
    console.log("Email OTP sent to:", email);

    wrapper.find('.otp-box').removeClass('d-none');
    wrapper.find('.email-otp-input')
        .prop('disabled', false)
        .focus();

});// MOBILE OTP SEND
$(document).on('click', '.send-mobile-otp', function(){

    const wrapper = $(this).closest('.otp-wrapper');
    const mobile = wrapper.find('.otp-mobile').val();

    if(!mobile) return;

    // Yaha AJAX call lagega
    console.log("Mobile OTP sent to:", mobile);

    wrapper.find('.otp-box').removeClass('d-none');
    wrapper.find('.mobile-otp-input')
        .prop('disabled', false)
        .focus();

});


// Email validation
$(document).on('input', '.otp-email', function(){
    const valid = /^[^\s@]+@[^\s@]+\.[^\s@]+$/.test($(this).val());
    $(this).siblings('.send-email-otp').prop('disabled', !valid);
});

// Mobile validation
$(document).on('input', '.otp-mobile', function(){
    const valid = /^[0-9]{10}$/.test($(this).val());
    $(this).siblings('.send-mobile-otp').prop('disabled', !valid);
});

$(document).on('click', '.refresh-captcha', function(){
    $('.captcha-question').text(generateCaptcha());
});


$(document).on('click', '.moveUpBtn', function(){

    const $card = $(this).closest('.attribute-row');
    const $prev = $card.prev('.attribute-row');

    if($prev.length){
        $card.insertBefore($prev);
        renderAttributesPreview();
    }

});$(document).on('click', '.moveDownBtn', function(){

    const $card = $(this).closest('.attribute-row');
    const $next = $card.next('.attribute-row');

    if($next.length){
        $card.insertAfter($next);
        renderAttributesPreview();
    }

});

$('#attributesWrapper').sortable({
    update: function(){
        renderAttributesPreview();
    }
});


function populateSelect($select, data, placeholder = 'Select') {

    $select.empty();

    if (!data || data.length === 0) {
        $select.append(`<option value="">No Data</option>`);
        return;
    }

    // Placeholder
    $select.append(`<option value="">${placeholder}</option>`);

    $.each(data, function(i, item){
        $select.append(
            `<option value="${item.id}">${item.name}</option>`
        );
    });

    // 👇 IMPORTANT PART
    if (data.length === 1) {
        $select.val(data[0].id).trigger('change');
    }
}


const API_BASE = window.location.origin + '/api/';
$(document).on('change', '.country-select', function(){

    const countryId = $(this).val();
    const $state = $('.state-select');
    const $city = $('.city-select');

    $state.empty();
    $city.empty();

    if(!countryId) return;

    $.ajax({
        url: API_BASE + 'states',
        data: { country_id: countryId },
        success: function(res){
            populateSelect($state, res, 'Select State');
        }
    });

});$(document).on('change', '.state-select', function(){

    const stateId = $(this).val();
    const $city = $('.city-select');

    $city.empty();

    if(!stateId) return;

    $.ajax({
         url: API_BASE + 'cities',
        data: { state_id: stateId },
        success: function(res){
            populateSelect($city, res, 'Select City');
        }
    });

});$(document).on('change', '.institute-select', function(){

    const instituteId = $(this).val();
    const $programme = $('.programme-select');
    const $course = $('.course-select');

    $programme.empty();
    $course.empty();

    if(!instituteId) return;

    $.ajax({
         url: API_BASE + 'programmes',
        data: { institute_id: instituteId },
        success: function(res){
            populateSelect($programme, res, 'Select Programme');
        }
    });

});$(document).on('change', '.programme-select', function(){

    const programmeId = $(this).val();
    const $course = $('.course-select');

    $course.empty();

    if(!programmeId) return;

    $.ajax({
         url: API_BASE + 'courses',
        data: { programme_id: programmeId },
        success: function(res){
            populateSelect($course, res, 'Select Course');
        }
    });

});
});








</script>3
@endsection