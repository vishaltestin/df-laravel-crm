@extends('FrontEnd.layouts.master')

@section('title', 'Create Web Forms')

@section('css')
<link href="{{ URL::asset('build/libs/sweetalert2/sweetalert2.min.css') }}" rel="stylesheet">
<style>
/* थोड़ी सी styling ताकि screenshot जैसा लगे */
.attribute-row .attr-card-title { font-size: 14px; }
.attribute-row .moveUpBtn, .attribute-row .moveDownBtn { margin-right:5px;}
.options-wrapper { margin-top:8px; }
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
                                <select id="submit_success_action" class="form-control"  required>
                                    <option value="message">Display custom message</option>
                                    <option value="redirect">Redirect To Url</option>
                                </select>
                            </div>

                            <div class="mb-3">
                                <label id="submit_success_label">Message to display</label>
                                <input type="text" id="submit_success_content"  required class="form-control" placeholder="Enter message to display">
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

               <div class="col-md-6 mb-2">
    <label>Application Form *</label>
    <select id="application_form_id" class="form-control form-select" required>
        <option value="">Select Application Form</option>
        @foreach($applicationForms ?? [] as $app)
            <option value="{{ $app->id }}">
                {{ $app->title }}
            </option>
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
/*
 Reference image (ignored in this run): /mnt/data/b52bc1fb-f154-4621-acad-7500011321d7.png
*/
$(function(){

    // allowed types
const allowedTypes = [
    'text','textarea',
    'email','email_otp',
    'number','mobile','mobile_otp',
    'date','time','datetime-local',
    'select','radio','checkbox',
    'country','state','city',
    'institute','programme','course',
    'captcha',
    'terms'   // 👈 ADD THIS
];
    // ---------- preview color handlers ----------
    $('#background_color, #form_background_color, #form_title_color, #form_submit_button_color, #attribute_label_color')
        .on('change input', updatePreviewColors);

    function updatePreviewColors(){
        $('#previewContainer').css('background-color', $('#background_color').val());
        $('#previewFormBox').css('background-color', $('#form_background_color').val());
        $('#previewTitle').css('color', $('#form_title_color').val());
        $('#previewButton').css('background-color', $('#form_submit_button_color').val());
        $('#previewAttrs label').css('color', $('#attribute_label_color').val());
    }

    $('#title, #submit_button_label').on('input', function(){
        $('#previewTitle').text($('#title').val() || '-');
        $('#previewButton').text($('#submit_button_label').val() || 'Submit');
    });

    $('#submit_success_action').on('change', function(){
        if($(this).val() === 'message'){
            $('#submit_success_label').text('Message to display');
            $('#submit_success_content').attr('placeholder', 'Enter message to display');
        } else {
            $('#submit_success_label').text('Redirect URL');
            $('#submit_success_content').attr('placeholder', 'Enter redirect URL');
        }
    });

    // ---------- attributes UI ----------
    let attrIndex = 0;

    function typeOptionsHtml(selected){
        let html = '';
        allowedTypes.forEach(t => {
            html += `<option value="${t}" ${t === selected ? 'selected' : ''}>${t}</option>`;
        });
        return html;
    }

    function escapeHtml(text) {
        return String(text || '').replace(/&/g,'&amp;').replace(/</g,'&lt;').replace(/>/g,'&gt;').replace(/"/g,'&quot;').replace(/'/g,'&#039;');
    }

    function renderAttributeRow(data = {}) {
        attrIndex++;
        const name = data.name ?? '';
        const lowerName = String(name).toLowerCase();
        const isDefault = ['name','email','mobile'].includes(lowerName);

        // mobile default should be number type; ensure type set to number if default mobile
        const typeVal = data.type ?? (lowerName === 'email' ? 'email' : (lowerName === 'mobile' ? 'number' : 'text'));

        const $card = $(`
            <div class="card p-2 mb-2 attribute-row" data-idx="${attrIndex}">
                <div class="d-flex justify-content-between align-items-center">
                    <strong class="attr-card-title">${isDefault ? escapeHtml(data.name) : 'Attribute'}</strong>
                    <div>
                        <button type="button" class="btn btn-sm btn-secondary moveUpBtn" title="Move Up">↑</button>
                        <button type="button" class="btn btn-sm btn-secondary moveDownBtn" title="Move Down">↓</button>
                        <button type="button" class="btn btn-sm btn-danger removeAttrBtn">Delete</button>
                    </div>
                </div>

                <div class="row mt-2">
                    <div class="col-md-4 mb-2">
                        <label>Name *</label>
                        <input type="text" class="form-control attr_name" value="${escapeHtml(data.name ?? '')}" ${isDefault ? 'readonly':''}>
                    </div>

                    <div class="col-md-4 mb-2">
                        <label>Placeholder</label>
                        <input type="text" class="form-control attr_placeholder" value="${escapeHtml(data.placeholder ?? '')}" ${isDefault ? 'readonly':''}>
                    </div>

                    <div class="col-md-4 mb-2">
                        <label>Type</label>
                        <select class="form-control attr_type" ${isDefault ? 'disabled' : ''}>
                            ${typeOptionsHtml(typeVal)}
                        </select>
                    </div>

                    <div class="col-md-3 mb-2 form-check-col">
                        <label>Required</label><br>
                        <input type="checkbox" class="attr_required" ${data.is_required ? 'checked' : (isDefault ? 'checked disabled' : '')}>
                    </div>

                    <div class="col-md-3 mb-2">
                        <label>Hidden</label><br>
                        <input type="checkbox" class="attr_hidden" ${data.is_hidden ? 'checked' : ''}>
                    </div>

                    <div class="col-md-3 mb-2">
                        <label>Sort Order</label>
                        <input type="number" class="form-control attr_sort" value="${(data.sort_order !== undefined) ? data.sort_order : 0}">
                    </div>

                    <div class="col-md-12 mb-2 options-wrapper" style="${(typeVal==='select' || typeVal==='radio' || typeVal==='checkbox') ? '' : 'display:none;'}">
                        <label>Options (comma separated, for select/radio/checkbox)</label>
                        <input type="text" class="form-control attr_options" value="${escapeHtml(data.options ?? '')}">
                    </div>

                    <div class="col-md-12 mb-2 default-value-wrapper"
                            style="${['country','state','city','institute','programme','course'].includes(typeVal) ? '' : 'display:none;'}">
                            <label>Default Value (comma separated)</label>
                            <input type="text"
                                class="form-control attr_default_value"
                                value="${escapeHtml(data.default_value ?? '')}"
                                placeholder="e.g. 1,2">
                        </div>
                </div>
            </div>
        `);

        // hide delete button for default rows
        if (isDefault) {
            $card.find('.removeAttrBtn').remove();
        }

        $('#attributesWrapper').append($card);
    }

    // add default mandatory attributes (non-deletable, non-editable name/place/type)
    renderAttributeRow({name:'name', placeholder:'Enter name', type:'text', is_required:1, is_hidden:0, sort_order:0});
   // renderAttributeRow({name:'email', placeholder:'Enter email', type:'email', is_required:1, is_hidden:0, sort_order:1});
  //  renderAttributeRow({name:'mobile', placeholder:'Enter mobile', type:'number', is_required:1, is_hidden:0, sort_order:2});

    // ensure order synced & preview rendered after defaults
    syncSortOrders();
    renderAttributesPreview();

    // click add attribute
    $('#addAttrBtn').on('click', function(e){
        e.preventDefault();
        renderAttributeRow({name:'', placeholder:'', type:'text', is_required:0, is_hidden:0, sort_order: ($('#attributesWrapper .attribute-row').length)});
        // scroll to newly added
        $('html,body').animate({scrollTop: $('#attributesWrapper').offset().top + $('#attributesWrapper').height()}, 300);
        syncSortOrders();
        renderAttributesPreview();
    });

    // remove custom row
    $(document).on('click', '.removeAttrBtn', function(){
        $(this).closest('.attribute-row').remove();
        syncSortOrders();
        renderAttributesPreview();
    });

    // move up
    $(document).on('click', '.moveUpBtn', function(){
        const $row = $(this).closest('.attribute-row');
        const $prev = $row.prev('.attribute-row');
        if ($prev.length) {
            $row.insertBefore($prev);
            syncSortOrders();
            renderAttributesPreview();
        }
    });

    // move down
    $(document).on('click', '.moveDownBtn', function(){
        const $row = $(this).closest('.attribute-row');
        const $next = $row.next('.attribute-row');
        if ($next.length) {
            $row.insertAfter($next);
            syncSortOrders();
            renderAttributesPreview();
        }
    });

    // show/hide options input based on type change
 $(document).on('change', '.attr_type', function(){

    const type = $(this).val();
    const $card = $(this).closest('.attribute-row');
    const $nameInput = $card.find('.attr_name');

    // =============================
    // SYSTEM FIELD LOCK MAP (FINAL)
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

    // Force system name + lock
    if (fixedFields[type]) {
        $nameInput.val(fixedFields[type]);
        $nameInput.prop('readonly', true);
    } else {
        $nameInput.prop('readonly', false);
    }

    // =============================
    // OPTIONS SHOW/HIDE
    // =============================
    if (['select','radio','checkbox'].includes(type)) {
        $card.find('.options-wrapper').show();
    } else {
        $card.find('.options-wrapper').hide();
        $card.find('.attr_options').val('');
    }

    // =============================
    // DEFAULT VALUE SHOW/HIDE
    // =============================
    if (['country','state','city','institute','programme','course'].includes(type)) {
        $card.find('.default-value-wrapper').show();
    } else {
        $card.find('.default-value-wrapper').hide();
        $card.find('.attr_default_value').val('');
    }

    renderAttributesPreview();
});

    // on any input change update preview
    $(document).on('input change', '.attr_name, .attr_placeholder, .attr_type, .attr_required, .attr_hidden, .attr_sort, .attr_options', function(){
        renderAttributesPreview();
    });

    // sync sort orders according to current DOM order (0..n-1)
    function syncSortOrders(){
        $('#attributesWrapper .attribute-row').each(function(i){
            $(this).find('.attr_sort').val(i);
        });
    }

    // collect attributes in DOM order and render preview
 function renderAttributesPreview() {
    let html = '';

    $('#attributesWrapper .attribute-row').each(function(){
        const name = $(this).find('.attr_name').val().trim();
        const placeholder = $(this).find('.attr_placeholder').val().trim();
        const type = $(this).find('.attr_type').val();
        const required = $(this).find('.attr_required').is(':checked');
        const hidden = $(this).find('.attr_hidden').is(':checked');
        const options = ($(this).find('.attr_options').val() || '')
                .split(',')
                .map(o => o.trim())
                .filter(o => o !== '');
        const opts = options.map(o => `<option>${escapeHtml(o)}</option>`).join('');
        const previewType = (type === 'mobile') ? 'number' : type;
        if (hidden) return;

        let inputHtml = '';

        if (type === 'textarea') {
            inputHtml = `<textarea class="form-control" placeholder="${escapeHtml(placeholder)}"></textarea>`;
        } 
        else if (type === 'select') {
            const opts = options.split(',').map(o => `<option>${escapeHtml(o.trim())}</option>`).join('');
            inputHtml = `<select class="form-control">${opts}</select>`;
        } 
        else if (type === 'radio') {
            inputHtml = options.split(',').map(o =>
                `<div class="form-check">
                    <input class="form-check-input" type="radio" name="pv_${escapeHtml(name)}">
                    <label class="form-check-label">${escapeHtml(o.trim())}</label>
                </div>`
            ).join('');
        } 
        else if (type === 'checkbox') {
            inputHtml = options.split(',').map(o =>
                `<div class="form-check">
                    <input class="form-check-input" type="checkbox">
                    <label class="form-check-label">${escapeHtml(o.trim())}</label>
                </div>`
            ).join('');
        }
else if (type === 'terms') {

    inputHtml = `
        <div class="form-check">
            <input class="form-check-input" type="checkbox">
            <label class="form-check-label">
                ${escapeHtml(placeholder || "I agree to the Terms & Conditions")}
            </label>
        </div>
    `;
}
        else if (type === 'email_otp') {
    inputHtml = `
        <input type="email" class="form-control mb-2" placeholder="${escapeHtml(placeholder)}">
        <button class="btn btn-sm btn-primary mt-1">Send OTP</button>
        <input type="text" class="form-control mt-2" placeholder="Enter OTP">`;
}
else if (type === 'mobile_otp') {
    inputHtml = `
        <input type="text" class="form-control mb-2" placeholder="${escapeHtml(placeholder)}">
        <button class="btn btn-sm btn-primary mt-1">Send OTP</button>
        <input type="text" class="form-control mt-2" placeholder="Enter OTP">`;
}
if(type === 'terms'){
    $nameInput.val('terms_and_conditions');
    $nameInput.prop('readonly', true);
}
else if (type === 'captcha') {
    inputHtml = `
        <div class="d-flex align-items-center gap-2">
            <div class="badge bg-secondary p-2">5 + 3 = ?</div>
            <button type="button" class="btn btn-sm btn-outline-primary">↻</button>
        </div>
        <input type="number" class="form-control mt-2" placeholder="Enter Answer">`;
}else if (['country','state','city','institute','programme','course'].includes(type)) {

    inputHtml = `
        <select class="form-control">
            <option>Select ${escapeHtml(name)}</option>
        </select>`;
}
        else {
            const previewType = (type === 'mobile') ? 'number' : type;
            inputHtml = `<input class="form-control" type="${previewType}" placeholder="${escapeHtml(placeholder)}">`;
        }

        html += `<div class="mb-2">
                    <label style="font-weight:600">${escapeHtml(name)} ${required ? "<span style='color:red'>*</span>" : ""}</label>
                    ${inputHtml}
                 </div>`;
    });

    $('#previewAttrs').html(html);
    updatePreviewColors();
}


    // initial render done earlier after default insertion
 $('#saveBtn').on('click', function(e){
    e.preventDefault();

    let attributes = [];

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
        let field = $(this).find('.attr_name').val().trim();
        const placeholder = $(this).find('.attr_placeholder').val().trim();
        const required = $(this).find('.attr_required').is(':checked');
        const hidden = $(this).find('.attr_hidden').is(':checked');
        const options = $(this).find('.attr_options').val()?.trim() || null;
        const default_value = $(this).find('.attr_default_value').val()?.trim() || null;

        if(hidden) return;

        // 🔥 FORCE SYSTEM NAME
        if (fixedFields[type]) {
            field = fixedFields[type];
        }

        if(!field) return;

        attributes.push({
        name: field.toLowerCase(),
        placeholder: placeholder,
        type: type,
        required: required ? 1 : 0,
        is_hidden: hidden ? 1 : 0,
        options: options,
        default_value: default_value
        });
    });

    const payload = {
        _token: "{{ csrf_token() }}",
        form_id: null,
        title: $('#title').val().trim(),
        description: $('#description').val().trim(),
        submit_button_label: $('#submit_button_label').val().trim(),
        submit_success_action: $('#submit_success_action').val(),
        submit_success_content: $('#submit_success_content').val().trim(),
        create_lead: $('#create_lead').is(':checked') ? 1 : 0,

        background_color: $('#background_color').val(),
        form_background_color: $('#form_background_color').val(),
        form_title_color: $('#form_title_color').val(),
        form_submit_button_color: $('#form_submit_button_color').val(),
        attribute_label_color: $('#attribute_label_color').val(),

        inbound_call: 0,
        outbound_call: 0,
        campaign_call: 0,
        alternate_mobile: null,
        institute_applying_for: $('#organization_id').val(),
        faculty: 0,

        form_attributes: attributes,
        extra_fields_json: JSON.stringify(attributes),

        organization_id: $('#organization_id').val(),
        lead_source_id: $('#lead_source_id').val() || null,
        lead_campaign_id: $('#lead_campaign_id').val() || null,
        pipeline_id: $('#pipeline_id').val() || null,
        type_id: $('#type_id').val() || null,
        application_form_id: $('#application_form_id').val(),

        view_count: 0,
        submit_count: 1,
        lead_weight_count: 0,
    };

    $.post("{{ url('webforms/store') }}", payload)
    .done(function(res){
        if(res.status === 'success'){
            Swal.fire("Saved!", res.message || "Webform saved successfully!", "success");
            window.location = "{{ url('webforms') }}";
        } else {
            let msg = "Failed to save";
            if (res.errors) {
                msg = Object.values(res.errors).map(arr => arr[0]).join("\n");
            }
            Swal.fire("Error", msg, "error");
        }
    })
    .fail(function(xhr){
        let msg = "Unknown Error";
        if(xhr.responseJSON && xhr.responseJSON.errors){
            msg = Object.values(xhr.responseJSON.errors).map(arr => arr[0]).join("\n");
        }
        Swal.fire("Validation Error", msg, "error");
        console.error(xhr.responseJSON);
    });

});
 

});
</script>

@endsection
