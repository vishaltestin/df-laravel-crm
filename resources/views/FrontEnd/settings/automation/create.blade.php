@extends('FrontEnd.layouts.master')
@section('title', 'Automation Rules')
 
 

@section('css')
<link href="{{ URL::asset('build/libs/sweetalert2/sweetalert2.min.css') }}" rel="stylesheet">
@endsection

@section('content')

@component('FrontEnd.components.breadcrumb')
    @slot('li_1') CRM @endslot
    @slot('title') Automation Rules @endslot
@endcomponent

<div class="row">
<div class="col-lg-12">
<div class="card">

<div class="card-header">
    <h4 class="card-title mb-0">Create Automation Rule</h4>
</div>

<div class="card-body">

<form id="automationRuleForm">
@csrf

{{-- RULE NAME --}}
<div class="mb-3">
    <label>Rule Name</label>
    <input type="text" name="name" class="form-control" required>
</div>

{{-- TRIGGER EVENT --}}
<div class="mb-3">
    <label>Trigger Event</label>
    <select name="trigger_event" id="trigger_event" class="form-control" required>
        <option value="">Select Event</option>
        <option value="lead.created">Lead Created</option>
        <option value="lead.assigned">Lead Assigned</option>
        <option value="lead.status.changed">Lead Status Changed</option>
        <option value="lead.followup.missed">Follow-up Missed</option>
        <option value="lead.inactive">Lead Inactive</option>
        <option value="lead.converted">Lead Converted</option>
    </select>
</div>

{{-- CONDITIONS --}}
<div id="conditionsBox" style="display:none">
    <h5>IF Conditions</h5>
    <div id="conditionsWrapper"></div>

    <button type="button" class="btn btn-sm btn-secondary mt-2" id="addCondition">
        + Add Condition
    </button>
</div>

<hr>

{{-- ACTION --}}
<div id="actionsBox" style="display:none">
    <h5>Action</h5>

    <div class="mb-3">
        <label>Action Type</label>
        <select name="action_type" id="action_type" class="form-control">
            <option value="">Select Action</option>

            <optgroup label="Communication">
                <option value="send_email">Send Email</option>
                <option value="send_whatsapp">Send WhatsApp</option>
                <option value="send_sms">Send SMS</option>
            </optgroup>

            <optgroup label="Assignment">
                <option value="assign_team">Assign Team</option>
                <option value="assign_user">Assign User</option>
            </optgroup>
        </select>
    </div>

    <div id="actionOptions"></div>
</div>

<hr>

<button type="submit" class="btn btn-primary">Save Automation</button>

</form>

</div>
</div>
</div>
</div>
 @endsection
 
@section('script')
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>

function getSelectedValue(fieldName) {

    let row = $('#conditionsWrapper')
        .find('.condition-field')
        .filter(function () {
            return $(this).val() === fieldName;
        })
        .last()
        .closest('.condition-row');

    if (!row.length) return null;

    let value = row.find('.condition-value').val();

    return value ? value : null;
}
/* =========================
   GLOBAL ORG CONTEXT
========================= */
window.CURRENT_ORG_ID = "{{ session('organization') ?? '' }}";

let conditionIndex = 0;

/* =================================
   API ROUTE CONFIG (API ONLY – SAFE)
================================= */
const API_ROUTES = {
    institute: "{{ url('/api/institutes') }}",
    program:   "{{ url('/api/programs') }}",
    course:    "{{ url('/api/courses') }}",
    state:     "{{ url('/api/states') }}",
    city:      "{{ url('/api/cities') }}",

    status:    "{{ url('/api/leadStatusesapi') }}",
    substatus: "{{ url('/api/lead-status-substatus') }}",  

    email_template: "{{ url('/api/org/email-templates') }}",
    whatsapp_template: "{{ url('/api/org/whatsapp-templates') }}",
    sms_template: "{{ url('/api/org/sms-templates') }}",

    team: "{{ url('/api/org/teams') }}",
    user: "{{ url('/api/org/users') }}"
};

/* =========================
   CONDITION ROW TEMPLATE
========================= */
function conditionRow(index) {
    return `
    <div class="row condition-row mb-2">
        <div class="col-md-3">
            <select class="form-control condition-field" name="conditions[${index}][field]">
                <option value="">Field</option>
                <option value="institute">Institute</option>
				 <option value="program">Program</option>
                <option value="course">Course</option>

                <option value="status">Status</option>
                <option value="substatus">Sub-Status</option>
                <option value="state">State</option>
                <option value="city">City</option>
            </select>
        </div>

        <div class="col-md-2">
            <select class="form-control" name="conditions[${index}][operator]">
                <option value="=">=</option>
                <option value="!=">!=</option>
                <option value="contains">Contains</option>
            </select>
        </div>

        <div class="col-md-4">
            <input type="text"
                   class="form-control condition-value"
                   name="conditions[${index}][value]"
                   placeholder="Value">
        </div>

        <div class="col-md-2">
            <button type="button" class="btn btn-danger remove-condition">X</button>
        </div>
    </div>`;
}

/* =========================
   TRIGGER EVENT
========================= */
$('#trigger_event').on('change', function () {
    $('#conditionsBox').show();
    $('#actionsBox').show();

    if ($('#conditionsWrapper').children().length === 0) {
        $('#conditionsWrapper').append(conditionRow(conditionIndex));
    }
});

/* =========================
   ADD / REMOVE CONDITION
========================= */
$('#addCondition').on('click', function () {
    conditionIndex++;
    $('#conditionsWrapper').append(conditionRow(conditionIndex));
});

$(document).on('click', '.remove-condition', function () {
    $(this).closest('.condition-row').remove();
});

/* =========================
   LOAD DROPDOWN (ORG AWARE)
========================= */
function loadDropdown(url, selectEl, extraParams = {}) {

    let params = {
        org_id: window.CURRENT_ORG_ID,
        ...extraParams
    };

    $.get(url, params, function (res) {

        let opt = `<option value="">Select</option>`;

        if (Array.isArray(res)) {
            res.forEach(r => {
                opt += `<option value="${r.id}">${r.name}</option>`;
            });
        }

        selectEl.html(opt);
    });
}

function resetField(fieldName) {

    $('#conditionsWrapper')
        .find('.condition-field')
        .filter(function () {
            return $(this).val() === fieldName;
        })
        .closest('.condition-row')
        .find('.condition-value')
        .html('<option value="">Select</option>');
}
$(document).on('change', '.condition-value', function () {

    let row = $(this).closest('.condition-row');
    let field = row.find('.condition-field').val();

    if (field === 'institute') {
        resetField('program');
        resetField('course');
    }

    if (field === 'program') {
        resetField('course');
    }

    if (field === 'state') {
        resetField('city');
    }
});
/* =========================
   CONDITION FIELD CHANGE
========================= */
$(document).on('change', '.condition-field', function () {

    let field = $(this).val();
    let row   = $(this).closest('.condition-row');
    let selectEl = row.find('.condition-value');
    let name = selectEl.attr('name') || `conditions[${conditionIndex}][value]`;

    // reset value field
    selectEl.replaceWith(`
        <select class="form-control condition-value" name="${name}">
            <option value="">Loading...</option>
        </select>
    `);

    selectEl = row.find('.condition-value');

    /* =========================
       STATUS (simple load)
    ========================= */
    if (field === 'status') {
        loadDropdown(API_ROUTES.status, selectEl);
        return;
    }

    /* =========================
       SUB STATUS → needs STATUS
    ========================= */
    if (field === 'substatus') {

        let statusId = getSelectedValue('status');

        if (!statusId) {
            selectEl.html('<option value="">Select Status First</option>');
            return;
        }

        loadDropdown(API_ROUTES.substatus, selectEl, {
            lead_status_id: statusId
        });

        return;
    }

    /* =========================
       CITY → needs STATE
    ========================= */
    if (field === 'city') {

        let stateId = getSelectedValue('state');

        if (!stateId) {
            selectEl.html('<option value="">Select State First</option>');
            return;
        }

        loadDropdown(API_ROUTES.city, selectEl, { state_id: stateId });
        return;
    }

    /* =========================
       PROGRAM → needs INSTITUTE
    ========================= */
    if (field === 'program') {

        let instituteId = getSelectedValue('institute');

        if (!instituteId) {
            selectEl.html('<option value="">Select Institute First</option>');
            return;
        }

        loadDropdown(API_ROUTES.program, selectEl, { institute_id: instituteId });
        return;
    }

    /* =========================
       COURSE → needs INSTITUTE + PROGRAM
    ========================= */
    if (field === 'course') {

        let instituteId = getSelectedValue('institute');
        let programId   = getSelectedValue('program');

        if (!instituteId) {
            selectEl.html('<option value="">Select Institute First</option>');
            return;
        }

        if (!programId) {
            selectEl.html('<option value="">Select Program First</option>');
            return;
        }

        loadDropdown(API_ROUTES.course, selectEl, {
            institute_id: instituteId,
            program_id: programId
        });

        return;
    }

    /* =========================
       NORMAL FIELDS
    ========================= */
    if (API_ROUTES[field]) {
        loadDropdown(API_ROUTES[field], selectEl);
    } else {
        selectEl.replaceWith(`
            <input type="text"
                   class="form-control condition-value"
                   name="${name}"
                   placeholder="Enter Value">
        `);
    }

});

/* =========================
   ACTION TYPE CHANGE
========================= */
$('#action_type').on('change', function () {

    let action = $(this).val();
    $('#actionOptions').html('');

    if (action === 'send_email') {
        $('#actionOptions').html(`
            <label>Email Template</label>
            <select name="actions[template_id]"
                    class="form-control"
                    id="emailTemplate"></select>
        `);
        loadDropdown(API_ROUTES.email_template, $('#emailTemplate'));
    }

    if (action === 'send_whatsapp') {
        $('#actionOptions').html(`
            <label>WhatsApp Template</label>
            <select name="actions[template_id]"
                    class="form-control"
                    id="waTemplate"></select>
        `);
        loadDropdown(API_ROUTES.whatsapp_template, $('#waTemplate'));
    }

    if (action === 'send_sms') {
        $('#actionOptions').html(`
            <label>SMS Template</label>
            <select name="actions[template_id]"
                    class="form-control"
                    id="smsTemplate"></select>
        `);
        loadDropdown(API_ROUTES.sms_template, $('#smsTemplate'));
    }

    if (action === 'assign_team') {
        $('#actionOptions').html(`
            <label>Select Team</label>
            <select name="actions[team_id]"
                    class="form-control"
                    id="teamSelect"></select>
        `);
        loadDropdown(API_ROUTES.team, $('#teamSelect'));
    }

    if (action === 'assign_user') {
        $('#actionOptions').html(`
            <label>Select User</label>
            <select name="actions[user_id]"
                    class="form-control"
                    id="userSelect"></select>
        `);
        loadDropdown(API_ROUTES.user, $('#userSelect'));
    }
});

/* =========================
   FORM SUBMIT
========================= */
$('#automationRuleForm').on('submit', function (e) {
    e.preventDefault();

    $.ajax({
        url: "{{ route('automation.store') }}",
        type: "POST",
        data: $(this).serialize(),
        success: function () {
            alert('Automation Rule Saved');
            window.location.href = "{{ route('automation.index') }}";
        },
        error: function () {
            alert('Something went wrong');
        }
    });
});
</script>





@endsection











 