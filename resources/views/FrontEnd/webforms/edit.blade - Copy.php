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

                <div class="col-md-6 mb-1">
                  <label>Programmes</label>
                  <div class="border p-2 rounded">
                   @php
   $selectedProgrammes = json_decode($webform->attr_programme_check, true) ?? [];
@endphp

@foreach($products ?? [] as $prod)
    @if($prod->status == 1)
    <div class="form-check mb-1">
        <input type="checkbox" class="form-check-input attr_programme_check"
               value="{{ $prod->id }}" data-name="{{ $prod->name }}"
               {{ in_array($prod->id, $selectedProgrammes) ? 'checked' : '' }}>
        <label class="form-check-label small">{{ $prod->name }}</label>
    </div>
    @endif
@endforeach

                  </div>
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
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
$(document).ready(function () {

    // ------------- 1. Prefill Lead Config Fields -------------
    const leadConfig = {
        organization_id: "{{ $orgId ?? '' }}",
        lead_source_id: "{{ $webform->lead_source_id ?? '' }}",
        lead_campaign_id: "{{ $webform->lead_campaign_id ?? '' }}",
        type_id: "{{ $webform->type_id ?? '' }}",
        pipeline_id: "{{ $webform->pipeline_id ?? '' }}"
    };

    // Set dropdowns with correct selectors (duplicates हटाए नहीं गए, बस गलत selectors सुधारे ↓)
    $('#organization_id').val(leadConfig.organization_id);
    $('#lead_source_id').val(leadConfig.lead_source_id);
    $('#lead_campaign_id').val(leadConfig.lead_campaign_id);
    $('#type_id').val(leadConfig.type_id);
    $('#pipeline_id').val(leadConfig.pipeline_id);

    // ------------- 2. Prefill Programmes as per DB -------------
    const selectedProgrammes = @json($webform->attr_programme_check ?? []);
    $('.attr_programme_check').each(function(){
        const val = $(this).val();
        if(selectedProgrammes.includes(Number(val))){
            $(this).prop('checked', true);
        }
    });

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

    const allowedTypes = ['text','textarea','email','number','mobile','date','time','datetime-local','select','radio','checkbox'];

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
                        <input type="text" class="form-control attr_name" value="${escapeHtml(fieldName)}" ${isDefault?'readonly':''}>
                    </div>
                    <div class="col-md-4 mb-2">
                        <label>Placeholder</label>
                        <input type="text" class="form-control attr_placeholder" value="${escapeHtml(placeholder)}" ${isDefault?'readonly':''}>
                    </div>
                    <div class="col-md-4 mb-2">
                        <label>Type</label>
                        <select class="form-control attr_type" ${isDefault?'disabled':''}>
                            ${typeOptionsHtml(type)}
                        </select>
                    </div>
                    <div class="col-md-3 mb-2">
                        <label>Required</label><br>
                        <input type="checkbox" class="attr_required" ${required ? 'checked':''} ${isDefault?'checked disabled':''}>
                    </div>
                    <div class="col-md-3 mb-2">
                        <label>Hidden</label><br>
                        <input type="checkbox" class="attr_hidden" ${hidden ? 'checked':''}>
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
        $('#previewTitle').text($('#title').val());
        $('#previewButton').text($('#submit_button_label').val());

        let html='';
        $('#attributesWrapper .attribute-row').each(function(){
            const name = $(this).find('.attr_name').val();
            const placeholder = $(this).find('.attr_placeholder').val();
            const type = $(this).find('.attr_type').val();
            const required = $(this).find('.attr_required').is(':checked');
            const hidden = $(this).find('.attr_hidden').is(':checked');
            if(hidden) return;

            let input='';
            if (type==='textarea') input=`<textarea class="form-control" placeholder="${escapeHtml(placeholder)}"></textarea>`;
            else if (type==='select') input = `<select class="form-control">${placeholder.split(',').map(o => `<option>${escapeHtml(o.trim())}</option>`).join('')}</select>`;
            else if (type==='radio') input = placeholder.split(',').map(o => `<div class="form-check"><input type="radio"> <label>${escapeHtml(o.trim())}</label></div>`).join('');
            else if (type==='checkbox') input = placeholder.split(',').map(o => `<div class="form-check"><input type="checkbox"> <label>${escapeHtml(o.trim())}</label></div>`).join('');
            else input=`<input type="${type==='email'?'email':'text'}" class="form-control" placeholder="${escapeHtml(placeholder)}">`;

            html += `<div class="mb-2"><label>${escapeHtml(name)} ${required?"*":""}</label>${input}</div>`;
        });

        $('#previewAttrs').html(html);
    }

    updatePreview(); // initial load

});
</script>


@endsection
