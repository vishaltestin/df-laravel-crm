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
    <h4 class="card-title mb-0">Edit Automation Rule</h4>
</div>

<div class="card-body">

<form id="automationRuleForm">
@csrf

<input type="hidden" name="rule_id" value="{{ $rule->id }}">

{{-- RULE NAME --}}
<div class="mb-3">
    <label>Rule Name</label>
    <input type="text"
           name="name"
           class="form-control"
           value="{{ $rule->name }}"
           required>
</div>

{{-- TRIGGER EVENT --}}
<div class="mb-3">
    <label>Trigger Event</label>
    <select name="trigger_event" id="trigger_event" class="form-control" required>
        <option value="">Select Event</option>
        @foreach([
            'lead.created' => 'Lead Created',
            'lead.assigned' => 'Lead Assigned',
            'lead.status.changed' => 'Lead Status Changed',
            'lead.followup.missed' => 'Follow-up Missed',
            'lead.inactive' => 'Lead Inactive',
            'lead.converted' => 'Lead Converted'
        ] as $key => $label)
            <option value="{{ $key }}"
                {{ $rule->trigger_event === $key ? 'selected' : '' }}>
                {{ $label }}
            </option>
        @endforeach
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

<button type="submit" class="btn btn-primary">Update Automation</button>
</form>

</div>
</div>
</div>
</div>
 @endsection
 
@section('script')
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
 <script>
 
 function getConditionValue(field) {
    let found = EXISTING_RULE.conditions.find(c => c.field === field);
    return found ? found.value : null;
}
/* =========================
   EXISTING RULE DATA
========================= */
window.EXISTING_RULE = {
    conditions: @json($rule->conditions ?? []),
    actions:    @json($rule->actions ?? [])
};

window.CURRENT_ORG_ID = "{{ session('organization') ?? '' }}";
let conditionIndex = 0;

/* =========================
   API ROUTES
========================= */
const API_ROUTES = {
    institute: "{{ url('/api/institutes') }}",
    program:   "{{ url('/api/programs') }}",
    course:    "{{ url('/api/courses') }}",
    state:     "{{ url('/api/states') }}",
    city:      "{{ url('/api/cities') }}",

    status:    "{{ url('/api/leadStatusesapi') }}",
    substatus: "{{ url('/api/lead-status-substatus') }}",

    email_template: "{{ url('/api/org/email-templates') }}",
    whatsapp_template:"{{ url('/api/org/whatsapp-templates') }}",
    sms_template: "{{ url('/api/org/sms-templates') }}",

    team: "{{ url('/api/org/teams') }}",
    user: "{{ url('/api/org/users') }}"
};
/* =========================
   CONDITION TEMPLATE
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
                   name="conditions[${index}][value]">
        </div>

        <div class="col-md-2">
            <button type="button" class="btn btn-danger remove-condition">X</button>
        </div>
    </div>`;
}
/* =========================
   LOAD DROPDOWN
========================= */
function loadDropdown(url, selectEl, extraParams = {}, selectedValue = null) {
    $.get(url, { org_id: CURRENT_ORG_ID, ...extraParams }, function (res) {

        let opt = `<option value="">Select</option>`;
        res.forEach(r => {
            opt += `<option value="${r.id}">${r.name}</option>`;
        });

        selectEl.html(opt);

        // 🔥 auto-select AFTER load
        if (selectedValue) {
            selectEl.val(selectedValue);
        }
    });
}

/* =========================
   INIT EDIT MODE
========================= */
$(document).ready(function () {

    $('#conditionsBox, #actionsBox').show();

    /* =====================
       CONDITIONS (STATE FIX)
    ===================== */
    (EXISTING_RULE.conditions || []).forEach((cond, i) => {

    let row = $(conditionRow(i));
    $('#conditionsWrapper').append(row);

    let fieldEl = row.find('.condition-field');
    let valueEl = row.find('.condition-value');

    fieldEl.val(cond.field).trigger('change');

    setTimeout(() => {

        if (cond.field === 'program') {
            loadDropdown(API_ROUTES.program, valueEl, {
                institute_id: getConditionValue('institute')
            }, cond.value);
        }

        else if (cond.field === 'course') {
            loadDropdown(API_ROUTES.course, valueEl, {
                institute_id: getConditionValue('institute'),
                program_id: getConditionValue('program')
            }, cond.value);
        }

        else if (cond.field === 'substatus') {
            loadDropdown(API_ROUTES.substatus, valueEl, {
                lead_status_id: getConditionValue('status')
            }, cond.value);
        }

        else if (API_ROUTES[cond.field]) {
            loadDropdown(API_ROUTES[cond.field], valueEl, {}, cond.value);
        }

        else {
            valueEl.val(cond.value);
        }

    }, 300);

    conditionIndex = i;
});

    /* =====================
       ACTION (OLD DATA FIX)
    ===================== */
    let actions = EXISTING_RULE.actions || {};
let detectedType = null;

/* 🔥 DETECT ACTION TYPE */
if (actions.team_id) {
    detectedType = 'assign_team';
}
else if (actions.user_id) {
    detectedType = 'assign_user';
}
else if (actions.template_id) {
    // default email (sms/wa differentiate later)
    detectedType = 'send_email';
}

if (detectedType) {

    // 1️⃣ select action type
    $('#action_type').val(detectedType).trigger('change');

    // 2️⃣ wait for dropdown load, then select value
    let interval = setInterval(() => {

        Object.entries(actions).forEach(([k, v]) => {
            let el = $(`[name="actions[${k}]"]`);
            if (el.length && el.find('option').length > 1) {
                el.val(v);
                clearInterval(interval);
            }
        });

    }, 100);
}


 
});

/* =========================
   EVENTS
========================= */
$('#addCondition').on('click', function () {
    conditionIndex++;
    $('#conditionsWrapper').append(conditionRow(conditionIndex));
});

$(document).on('click', '.remove-condition', function () {
    $(this).closest('.condition-row').remove();
});

$(document).on('change', '.condition-field', function () {

    let field = $(this).val();
    let row   = $(this).closest('.condition-row');
    let selectEl = row.find('.condition-value');
    let name = selectEl.attr('name');

    selectEl.replaceWith(`
        <select class="form-control condition-value" name="${name}">
            <option value="">Loading...</option>
        </select>
    `);

    selectEl = row.find('.condition-value');

    // STATUS
    if (field === 'status') {
        loadDropdown(API_ROUTES.status, selectEl);
        return;
    }

    // SUBSTATUS
    if (field === 'substatus') {

        let statusId = getSelectedValue('status');

        if (!statusId) {
            selectEl.html('<option>Select Status First</option>');
            return;
        }

        loadDropdown(API_ROUTES.substatus, selectEl, {
            lead_status_id: statusId
        });

        return;
    }

    // CITY
    if (field === 'city') {

        let stateId = getSelectedValue('state');

        if (!stateId) {
            selectEl.html('<option>Select State First</option>');
            return;
        }

        loadDropdown(API_ROUTES.city, selectEl, { state_id: stateId });
        return;
    }

    // PROGRAM
    if (field === 'program') {

        let instituteId = getSelectedValue('institute');

        if (!instituteId) {
            selectEl.html('<option>Select Institute First</option>');
            return;
        }

        loadDropdown(API_ROUTES.program, selectEl, {
            institute_id: instituteId
        });

        return;
    }

    // COURSE
    if (field === 'course') {

        let instituteId = getSelectedValue('institute');
        let programId   = getSelectedValue('program');

        if (!instituteId || !programId) {
            selectEl.html('<option>Select Institute & Program First</option>');
            return;
        }

        loadDropdown(API_ROUTES.course, selectEl, {
            institute_id: instituteId,
            program_id: programId
        });

        return;
    }

    // NORMAL
    if (API_ROUTES[field]) {
        loadDropdown(API_ROUTES[field], selectEl);
    } else {
        selectEl.replaceWith(`<input type="text" class="form-control" name="${name}">`);
    }

});
/* =========================
   ACTION CHANGE
========================= */
$('#action_type').on('change', function () {

    let action = $(this).val();
    $('#actionOptions').html('');

    const map = {
        send_email: ['Email Template','emailTemplate','email_template','template_id'],
        send_whatsapp: ['WhatsApp Template','waTemplate','whatsapp_template','template_id'],
        send_sms: ['SMS Template','smsTemplate','sms_template','template_id'],
        assign_team: ['Select Team','teamSelect','team','team_id'],
        assign_user: ['Select User','userSelect','user','user_id']
    };

    if (!map[action]) return;

    let [label,id,route,key] = map[action];

    $('#actionOptions').html(`
        <label>${label}</label>
        <select name="actions[${key}]" class="form-control" id="${id}"></select>
    `);

    loadDropdown(API_ROUTES[route], $('#' + id));
});

/* =========================
   SUBMIT (UPDATE)
========================= */
$('#automationRuleForm').on('submit', function (e) {
    e.preventDefault();

    $.post("{{ route('automation.update', $rule->id) }}",
        $(this).serialize(),
        function () {
            alert('Automation Rule Updated');
            window.location.href = "{{ route('automation.index') }}";
        }
    );
});
</script>



@endsection











 