@extends('FrontEnd.layouts.master')
@section('title', 'Edit Webform')

@section('css')
<link href="{{ URL::asset('build/libs/sweetalert2/sweetalert2.min.css') }}" rel="stylesheet">
<style>
/* Small helper styling to keep builder preview tidy */
.attribute-row .attr-card-title { font-size: 14px; }
.attribute-row .moveUpBtn, .attribute-row .moveDownBtn { margin-right:5px; }
.options-wrapper { margin-top:8px; }
.preview-card { border-radius:8px; padding:15px; }
</style>
@endsection

@section('content')

@component('FrontEnd.components.breadcrumb')
    @slot('li_1') CRM @endslot
    @slot('title') Edit Webform @endslot
@endcomponent

<div class="row">
    <div class="col-lg-12">
        <div class="card" id="webformEditCard">
            <div class="card-body">
                <div class="row">
                    <!-- LEFT SIDE (Form) -->
                    <div class="col-md-8">
                        <h4>Edit Webform</h4>

                        <div class="card p-3">

                            <div class="mb-3">
                                <label>Title *</label>
                                <input type="text" id="title" class="form-control" value="{{ $webform->title }}">
                            </div>

                            <div class="mb-3">
                                <label>Description</label>
                                <textarea id="description" class="form-control" rows="3">{{ $webform->description }}</textarea>
                            </div>

                            <div class="mb-3">
                                <label>Submit Button Label *</label>
                                <input type="text" id="submit_button_label" class="form-control"
                                       value="{{ $webform->submit_button_label }}">
                            </div>

                            <div class="mb-3">
                                <label>Submit Success Action*</label>
                                <select id="submit_success_action" class="form-control">
                                    <option value="message" {{ $webform->submit_success_action=='message'?'selected':'' }}>
                                        Display custom message
                                    </option>
                                    <option value="redirect" {{ $webform->submit_success_action=='redirect'?'selected':'' }}>
                                        Redirect To URL
                                    </option>
                                </select>
                            </div>

                            <div class="mb-3">
                                <label id="submit_success_label">
                                    {{ $webform->submit_success_action=='redirect' ? 'Redirect URL' : 'Message to display' }}
                                </label>
                                <input type="text" id="submit_success_content" class="form-control"
                                       value="{{ $webform->submit_success_content }}">
                            </div>

                            <div class="mb-3">
                                <label>Create Lead</label>
                                <div class="form-check form-switch">
                                    <input class="form-check-input" type="checkbox" id="create_lead"
                                           {{ $webform->create_lead ? 'checked':'' }}>
                                </div>
                            </div>

                            <!-- Colors -->
                            <h5>Customize Colors</h5>
                            <div class="row">

                                <div class="col-md-6 mb-2">
                                    <label>Background Color</label>
                                    <input type="color" id="background_color" class="form-control"
                                           value="{{ $webform->background_color ?? '#F7F8F9' }}">
                                </div>

                                <div class="col-md-6 mb-2">
                                    <label>Form Background</label>
                                    <input type="color" id="form_background_color" class="form-control"
                                           value="{{ $webform->form_background_color ?? '#FFFFFF' }}">
                                </div>

                                <div class="col-md-6 mb-2">
                                    <label>Form Title Color</label>
                                    <input type="color" id="form_title_color" class="form-control"
                                           value="{{ $webform->form_title_color ?? '#263238' }}">
                                </div>

                                <div class="col-md-6 mb-2">
                                    <label>Submit Button Color</label>
                                    <input type="color" id="form_submit_button_color" class="form-control"
                                           value="{{ $webform->form_submit_button_color ?? '#0E90D9' }}">
                                </div>

                                <div class="col-md-6 mb-2">
                                    <label>Attribute Label Color</label>
                                    <input type="color" id="attribute_label_color" class="form-control"
                                           value="{{ $webform->attribute_label_color ?? '#546E7A' }}">
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
         <option value="{{ $s->id }}" {{ $webform->lead_source_id == $s->id ? 'selected' : '' }}>
            {{ $s->name }}
         </option>
    @endforeach
</select>
                </div>

                <div class="col-md-4 mb-1">
                  <label>Campaign</label>
                 <select id="lead_campaign_id" class="form-control form-select">
    <option value="">Select Campaign</option>
    @foreach($campaigns ?? [] as $c)
         <option value="{{ $c->id }}" {{ $webform->lead_campaign_id == $c->id ? 'selected' : '' }}>
            {{ $c->name }}
         </option>
    @endforeach
</select>

                </div>

                <div class="col-md-4 mb-1">
                  <label>Pipeline</label>
                 <select id="pipeline_id" class="form-control form-select">
    <option value="">Select Pipeline</option>
    @foreach($pipelines ?? [] as $p)
         <option value="{{ $p->id }}" {{ $webform->pipeline_id == $p->id ? 'selected' : '' }}>
            {{ $p->name }}
         </option>
    @endforeach
</select>

                </div>

                <div class="col-md-4 mb-1">
                  <label>Lead Type</label>
                 <select id="type_id" class="form-control form-select">
    <option value="">Select Lead Type</option>
    @foreach($leadTypes ?? [] as $t)
         <option value="{{ $t->id }}" {{ $webform->type_id == $t->id ? 'selected' : '' }}>
            {{ $t->name }}
         </option>
    @endforeach
</select>

                </div>

             
               <div class="col-md-6 mb-2">
    <label>Application Form *</label>
  <select id="application_form_id" class="form-control form-select" required>
    <option value="">Select Application Form</option>

    @foreach($applicationForms ?? [] as $app)
        <option value="{{ $app->id }}"
            {{ ($webform->application_form_id == $app->id) ? 'selected' : '' }}>
            {{ $app->title }}
        </option>
    @endforeach

</select>
</div>

                  
                </div>

             

              <hr>
                            <!-- Attributes -->
                            <h5>Attributes</h5>
                            <p class="text-muted">Name, Email, Mobile always required.</p>

                            <div id="attributesWrapper"></div>

                            <button class="btn btn-warning mb-3" id="addAttrBtn">+ Add Attribute</button>

                            <button class="btn btn-success w-100" id="saveBtn">Update Webform</button>

                        </div>
                    </div>

                    <!-- RIGHT SIDE (Preview) -->
                    <div class="col-md-4">
                        <h5>Preview</h5>

                        <div class="card p-3 preview-card" id="previewBox"
                             style="background: {{ $webform->background_color ?? '#F7F8F9' }};">

                            <h4 id="previewTitle" style="color: {{ $webform->form_title_color ?? '#263238' }};">
                                {{ $webform->title }}
                            </h4>

                            <div id="previewAttrs"></div>

                            <button id="previewButton"
                                    class="btn mt-3 w-100"
                                    style="background: {{ $webform->form_submit_button_color ?? '#0E90D9' }}; color:#fff;">
                                {{ $webform->submit_button_label }}
                            </button>

                        </div>

                        <hr>

                        <div class="mt-3">
                            <img src="/mnt/data/94545101-3742-4a66-af49-e58b6f1161a8.png" alt="preview-screenshot" style="max-width:100%;border-radius:6px;">
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
$(document).ready(function () {



    $('#addAttrBtn').on('click', function(e){
    e.preventDefault();
    addAttributeRow({});
    $('html,body').animate({
        scrollTop: $('#attributesWrapper').offset().top + $('#attributesWrapper').height()
    }, 300);
});



    // ------------- 1. Prefill Lead Config Fields -------------
    const leadConfig = {
        organization_id: "{{ $orgId ?? '' }}",
        lead_source_id: "{{ $webform->lead_source_id ?? '' }}",
        lead_campaign_id: "{{ $webform->lead_campaign_id ?? '' }}",
        type_id: "{{ $webform->type_id ?? '' }}",
        pipeline_id: "{{ $webform->pipeline_id ?? '' }}",
        application_form_id: "{{ $webform->application_form_id ?? '' }}"
    };

    // Set dropdowns with correct selectors (duplicates हटाए नहीं गए, बस गलत selectors सुधारे ↓)
    $('#organization_id').val(leadConfig.organization_id);
    $('#lead_source_id').val(leadConfig.lead_source_id);
    $('#lead_campaign_id').val(leadConfig.lead_campaign_id);
    $('#type_id').val(leadConfig.type_id);
    $('#pipeline_id').val(leadConfig.pipeline_id);
    $('#application_form_id').val(leadConfig.application_form_id);

     

    // ------------- 3. Live Preview Update -------------
    $('#background_color, #form_background_color, #form_title_color, #form_submit_button_color, #attribute_label_color')
    .on('input change', function(){
        $('#previewBox').css('background', $('#background_color').val());
        $('#previewTitle').css('color', $('#form_title_color').val());
        $('#previewButton').css('background', $('#form_submit_button_color').val());
        $('#previewAttrs label').css('color', $('#attribute_label_color').val());
    });

    $('#title, #submit_button_label').on('input', function(){
        $('#previewTitle').text($('#title').val() || '-');
        $('#previewButton').text($('#submit_button_label').val() || 'Submit');
    });

    // ------------- 4. Load Existing Attributes -------------
    const existingAttrs = @json($webform->attributes ?? []);
    let indexCount = 0;

    function escapeHtml(text) {
        return String(text ?? '')
            .replace(/&/g,"&amp;")
            .replace(/</g,"&lt;")
            .replace(/>/g,"&gt;")
            .replace(/"/g,"&quot;")
            .replace(/'/g,"&#039;");
    }

const allowedTypes = [
    'text','textarea',
    'email','email_otp',
    'number','mobile','mobile_otp',
    'date','time','datetime-local',
    'select','radio','checkbox',
    'country','state','city',
    'institute','programme','course',
    'captcha',
    'terms' // 👈 ADD
];
    function typeOptionsHtml(selected){
       return allowedTypes.map(t=> `<option value="${t}" ${t===selected?'selected':''}>${t}</option>`).join('');
    }

    function addAttributeRow(data={}) {
        indexCount++;

        const fieldName = data.name || '';
        const placeholder = data.placeholder || '';
        const type = data.type || 'text';
        const required = data.is_required || 0;
        const hidden = data.is_hidden || 0;

        const lower = fieldName.toLowerCase().trim();
        const isDefault = ['name','email','mobile'].includes(lower);

        const $card = $(`
            <div class="card p-2 mb-2 attribute-row" data-idx="${indexCount}" data-attribute-id="${data.id ?? ''}">
                <div class="d-flex justify-content-between align-items-center">
                    <strong class="attr-card-title">${isDefault ? escapeHtml(fieldName||'') : 'Attribute'}</strong>
                    <div>
                        ${isDefault ? '' : `<button type="button" class="btn btn-sm btn-danger removeAttrBtn">Delete</button>`}
                        <button type="button" class="btn btn-sm btn-secondary moveUpBtn">↑</button>
                        <button type="button" class="btn btn-sm btn-secondary moveDownBtn">↓</button>
                    </div>
                </div>

               <div class="row mt-2">

    <div class="col-md-4 mb-2">
        <label>Name *</label>
        <input type="text" class="form-control attr_name"
               value="${escapeHtml(fieldName)}"
               ${isDefault?'readonly':''}>
    </div>

    <div class="col-md-4 mb-2">
        <label>Placeholder</label>
        <input type="text" class="form-control attr_placeholder"
               value="${escapeHtml(placeholder)}"
               ${isDefault?'readonly':''}>
    </div>

    <div class="col-md-4 mb-2">
        <label>Type</label>
        <select class="form-control attr_type"
            ${isDefault?'disabled':''}>
            ${typeOptionsHtml(type)}
        </select>
    </div>

    <div class="col-md-3 mb-2">
        <label>Required</label><br>
        <input type="checkbox" class="attr_required"
               ${required ? 'checked':''}
               ${isDefault?'checked disabled':''}>
    </div>

    <div class="col-md-3 mb-2">
        <label>Hidden</label><br>
        <input type="checkbox" class="attr_hidden"
               ${hidden ? 'checked':''}>
    </div>

    <!-- OPTIONS -->
    <div class="col-md-12 mb-2 options-wrapper"
        style="${['select','radio','checkbox'].includes(type)?'':'display:none;'}">
        <label>Options (comma separated)</label>
        <input type="text"
            class="form-control attr_options"
            value="${escapeHtml(data.options || '')}">
    </div>

    <!-- DEFAULT VALUE -->
    <div class="col-md-12 mb-2 default-value-wrapper"
        style="${['country','state','city','institute','programme','course'].includes(type)?'':'display:none;'}">
        <label>Default Value (comma separated)</label>
        <input type="text"
            class="form-control attr_default_value"
            value="${escapeHtml(data.default_value || '')}"
            placeholder="e.g. 1,2">
    </div>

</div>
            </div>
        `);

        $('#attributesWrapper').append($card);
        updatePreview();
    }

    // Existing rows loop untouched, बस अंदर data bindings सही कर दिए ✅
    existingAttrs.forEach(a=> addAttributeRow(a));

    function updatePreview() {

    $('#previewTitle').text($('#title').val() || '-');
    $('#previewButton').text($('#submit_button_label').val() || 'Submit');

    let html='';

    $('#attributesWrapper .attribute-row').each(function(){

        const name = $(this).find('.attr_name').val();
        const placeholder = $(this).find('.attr_placeholder').val();
        const type = $(this).find('.attr_type').val();
        const required = $(this).find('.attr_required').is(':checked');
        const hidden = $(this).find('.attr_hidden').is(':checked');
        const options = ($(this).find('.attr_options').val() || '')
            .split(',')
            .map(o => o.trim())
            .filter(o => o !== '');

        if(hidden) return;

        let input='';

        if (type==='textarea') {
            input=`<textarea class="form-control" placeholder="${escapeHtml(placeholder)}"></textarea>`;
        }

        else if (type==='select') {
            const opts = options.map(o=>`<option>${escapeHtml(o)}</option>`).join('');
            input = `<select class="form-control">${opts}</select>`;
        }

        else if (type==='radio') {
            input = options.map(o=>`
                <div class="form-check">
                    <input type="radio" class="form-check-input">
                    <label>${escapeHtml(o)}</label>
                </div>`).join('');
        }

        else if (type==='checkbox') {
            input = options.map(o=>`
                <div class="form-check">
                    <input type="checkbox" class="form-check-input">
                    <label>${escapeHtml(o)}</label>
                </div>`).join('');
        }

        else if (type==='email_otp') {
            input = `
                <input type="email" class="form-control mb-2" placeholder="${escapeHtml(placeholder)}">
                <button class="btn btn-sm btn-primary mt-1">Send OTP</button>
                <input type="text" class="form-control mt-2" placeholder="Enter OTP">`;
        }

        else if (type==='mobile_otp') {
            input = `
                <input type="text" class="form-control mb-2" placeholder="${escapeHtml(placeholder)}">
                <button class="btn btn-sm btn-primary mt-1">Send OTP</button>
                <input type="text" class="form-control mt-2" placeholder="Enter OTP">`;
        }

        else if (['country','state','city','institute','programme','course'].includes(type)) {
            input = `<select class="form-control"><option>Select ${escapeHtml(name)}</option></select>`;
        }

        else if (type==='captcha') {
            input = `
                <div class="preview-captcha-wrapper">
                    <label>Verify Captcha *</label>
                    <div class="preview-captcha-controls">
                        <div class="preview-captcha-img-box">
                            <img src="{{ url('captcha-image') }}?${Date.now()}" style="max-height:40px;">
                        </div>
                        <button type="button" class="preview-reload-btn"
                            onclick="this.previousElementSibling.querySelector('img').src='{{ url('captcha-image') }}?'+Date.now();">
                            Reload
                        </button>
                    </div>
                    <input type="text" class="form-control mt-2" placeholder="Enter code">
                </div>`;
        }
else if (type === 'terms') {

    input = `
        <div class="form-check">
            <input type="checkbox" class="form-check-input">
            <label class="form-check-label">
                ${escapeHtml(placeholder || "I agree to the Terms & Conditions")}
            </label>
        </div>`;
}
        else {
            const inputType = (type==='email') ? 'email' :
                              (type==='number' || type==='mobile') ? 'number' : 'text';

            input=`<input type="${inputType}" class="form-control" placeholder="${escapeHtml(placeholder)}">`;
        }

        html += `
            <div class="mb-2">
                <label style="font-weight:600">
                    ${escapeHtml(name)} ${required?'<span style="color:red">*</span>':''}
                </label>
                ${input}
            </div>`;
    });

    $('#previewAttrs').html(html);
}
$(document).on('change', '.attr_type', function(){

    const type = $(this).val();
    const card = $(this).closest('.attribute-row');
    const nameInput = card.find('.attr_name');

    // =============================
    // SYSTEM FIELD LOCK MAP
    // =============================
    const fixedFields = {
        name: 'name',
        email: 'email',
        email_otp: 'email',
        mobile: 'mobile',
        mobile_otp: 'mobile',
        country: 'country',
        state: 'state',
        city: 'city',
        institute: 'institute',
        programme: 'programme',
        course: 'course'
    };

    // If system type → lock name
    if (fixedFields[type]) {
        nameInput.val(fixedFields[type]);
        nameInput.prop('readonly', true);
    } else {
        nameInput.prop('readonly', false);
    }

    // =============================
    // OPTIONS SHOW/HIDE
    // =============================
    if(['select','radio','checkbox'].includes(type)){
        card.find('.options-wrapper').show();
    } else {
        card.find('.options-wrapper').hide();
        card.find('.attr_options').val('');
    }

    // =============================
    // DEFAULT VALUE SHOW/HIDE
    // =============================
    if(['country','state','city','institute','programme','course'].includes(type)){
        card.find('.default-value-wrapper').show();
    } else {
        card.find('.default-value-wrapper').hide();
        card.find('.attr_default_value').val('');
    }

});

$('#saveBtn').off('click').on('click', function(e){

    e.preventDefault();

    let arr = [];

    const fixedFields = {
        name: 'name',
        email: 'email',
        email_otp: 'email',
        mobile: 'mobile',
        mobile_otp: 'mobile',
        country: 'country',
        state: 'state',
        city: 'city',
        institute: 'institute',
        programme: 'programme',
        course: 'course'
    };

    $('#attributesWrapper .attribute-row').each(function(){

        const type = $(this).find('.attr_type').val();
        let name = $(this).find('.attr_name').val().trim();

        // FORCE SYSTEM NAME
        if (fixedFields[type]) {
            name = fixedFields[type];
        }

        arr.push({
            name: name.toLowerCase(),
            placeholder: $(this).find('.attr_placeholder').val().trim(),
            type: type,
            required: $(this).find('.attr_required').is(':checked') ? 1 : 0,
            is_hidden: $(this).find('.attr_hidden').is(':checked') ? 1 : 0,
            options: $(this).find('.attr_options').val() || null,
            default_value: $(this).find('.attr_default_value').val() || null
        });

    });

    $.ajax({
        url: "{{ url('webforms/'.$webform->id.'/update') }}",
        type: "POST",
        dataType: "json",
        data: {
            _token: "{{ csrf_token() }}",
            title: $('#title').val(),
            description: $('#description').val(),
            submit_button_label: $('#submit_button_label').val(),
            submit_success_action: $('#submit_success_action').val(),
            submit_success_content: $('#submit_success_content').val(),
            create_lead: $('#create_lead').is(':checked') ? 1 : 0,
            background_color: $('#background_color').val(),
            form_background_color: $('#form_background_color').val(),
            form_title_color: $('#form_title_color').val(),
            form_submit_button_color: $('#form_submit_button_color').val(),
            attribute_label_color: $('#attribute_label_color').val(),
            lead_source_id: $('#lead_source_id').val(),
            lead_campaign_id: $('#lead_campaign_id').val(),
            pipeline_id: $('#pipeline_id').val(),
            type_id: $('#type_id').val(),
            application_form_id: $('#application_form_id').val() || null,
            form_attributes: arr
        },
        success: function(res){
            Swal.fire({
                title: "Success",
                text: res.message,
                icon: "success"
            }).then(function(){
                window.location.href = "{{ url('webforms') }}";
            });
        },
        error: function(xhr){
            console.log(xhr.responseText);
            Swal.fire("Error", "Something went wrong", "error");
        }
    });

});



// DELETE ATTRIBUTE
$(document).on('click', '.removeAttrBtn', function () {
    $(this).closest('.attribute-row').remove();
    updatePreview();
});

// MOVE UP
$(document).on('click', '.moveUpBtn', function () {

    const current = $(this).closest('.attribute-row');
    const prev = current.prev('.attribute-row');

    if (prev.length) {
        current.insertBefore(prev);
        updatePreview();
    }
});// MOVE DOWN
$(document).on('click', '.moveDownBtn', function () {

    const current = $(this).closest('.attribute-row');
    const next = current.next('.attribute-row');

    if (next.length) {
        current.insertAfter(next);
        updatePreview();
    }
});
});
</script>


@endsection
