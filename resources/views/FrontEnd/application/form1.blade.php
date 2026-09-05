<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width,initial-scale=1" />
  <title>ADTU —   Step Online Application (Clone)</title>

  <!-- Bootstrap 5 -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">

  <style>
    body { background:#f6f5fb; font-family: system-ui, -apple-system, "Segoe UI", Roboto, "Helvetica Neue", Arial; }
    .panel { background: #fff; border-radius:10px; padding: 22px; box-shadow: 0 1px 0 rgba(20,20,50,0.04); }
    .steps { display:flex; gap:18px; justify-content:center; margin-bottom:20px; }
    .step-pill { width:140px; text-align:center; padding:10px 6px; border-radius:40px; background:#efeef6; color:#6d5b9a; font-weight:600; }
    .step-pill.active { background:#2f2358; color:#fff; box-shadow:0 4px 10px rgba(47,35,88,0.12); }
    .section-title { color:#2f2358; font-weight:700; margin-bottom:14px; }
    .muted-box { background:#f0edf6; padding:12px 14px; border-radius:6px; }
    .error { color:#c92a2a; font-size:13px; margin-top:6px; display:block; }
    .small-note { font-size:12px; color:#666; }
    .required { color:#d00; margin-left:4px; }
    .form-actions { display:flex; gap:12px; justify-content:space-between; margin-top:18px; }
    .table-input td { vertical-align: middle; }
    input[type="file"] { padding: 6px 4px; }
    .summary-box { background:#eef0f8; padding:18px; border-radius:8px; }
    .hidden { display:none !important; }
  </style>
</head>
<body>

<div class="container py-4">
  <div class="panel">

    <!-- Header & Steps -->
    <div class="text-center mb-3">
      <h2 style="letter-spacing:0.2px; color:#3b3b3b;">ADTU Online Application Form <p>    {{ $form->description }}</p></h2>
      <p class="small-note">4 steps — please fill required fields (errors shown below each field)</p>
    </div> 
 
 
    <div class="steps mb-4">
      <div class="step-pill active" id="pill-1">Basic Information</div>
      <div class="step-pill" id="pill-2">Educational Details</div>
      <div class="step-pill" id="pill-3">Upload Documents</div>
      <div class="step-pill" id="pill-4">Payment</div>
    </div>

    <form id="appForm" novalidate enctype="multipart/form-data">
<input type="hidden" name="lead_key" value="{{ $leadKey }}">
<input type="hidden" name="form_id" value="{{ ($form->id) }}">

<input type="hidden" name="lead_id" value="{{ $lead->id }}">

   <!-- =========== STEP 1: BASIC INFO + COURSE + PARENTS + ADDRESS =========== -->
      <section class="step-section" data-step="1">

        <div class="section-title">Please fill up the Basic Information</div>

        <div class="row g-3">
          <div class="col-md-4">
            <label class="form-label">Full Name <span class="required">*</span></label>
            <input name="full_name" class="form-control" data-required="true"          value="{{ $response ? (json_decode($response->response_json, true)['name'] ?? '') : '' }}">
            <span class="error"></span>
          </div>

          <div class="col-md-4">
            <label class="form-label">Mobile Number <span class="required">*</span></label>
                       <div class="input-group">
<select class="form-select" name="mobile_cc" style="max-width:110px;">
                <option value="+91" selected>+91</option>
               </select>
              <input name="mobile" class="form-control" data-required="true" placeholder="Enter mobile no."        value="{{ $response ? (json_decode($response->response_json, true)['mobile'] ?? '') : '' }}">
             <span class="error"></span>
          </div>
          </div>

          <div class="col-md-4">
            <label class="form-label">Email Address <span class="required">*</span></label>
            <input type="email" name="email" class="form-control" data-required="true" />
            <span class="error"></span>
          </div>

          <div class="col-md-3">
            <label class="form-label">Date of Birth <span class="required">*</span></label>
            <input type="date" name="dob" class="form-control" data-required="true" />
            <span class="error"></span>
          </div>

          <div class="col-md-3">
            <label class="form-label">Gender <span class="required">*</span></label>
            <select name="gender" class="form-select" data-required="true">
              <option value="">Select</option>
              <option>Male</option>
              <option>Female</option>
              <option>Other</option>
            </select>
            <span class="error"></span>
          </div>

          <div class="col-md-3">
            <label class="form-label">Nationality <span class="required">*</span></label>
            <select name="nationality" class="form-select" data-required="true">
              <option value="">Select</option>
              <option value="Indian" selected>Indian</option>
              <option>Other</option>
            </select>
            <span class="error"></span>
          </div>

          <div class="col-md-3">
            <label class="form-label">Marital Status <span class="required">*</span></label>
            <select name="marital_status" class="form-select" data-required="true">
              <option value="">Select</option>
              <option>Single</option>
              <option>Married</option>
            </select>
            <span class="error"></span>
          </div>

          <div class="col-md-4">
            <label class="form-label">Category <span class="required">*</span></label>
            <select name="category" class="form-select" data-required="true">
            <option value="">Select</option><option value="General">General                            </option><option value="OBC">OBC                            </option><option selected="selected" value="MOBC">MOBC                            </option><option value="SC">SC                            </option><option value="ST(P)">ST(P)                            </option><option value="ST(H)">ST(H)                            </option><option value="Handicapped">Handicapped                            </option> 
            </select>
            <span class="error"></span>
          </div>

          <div class="col-md-4">
            <label class="form-label">Religion <span class="required">*</span></label>
            <select name="religion" class="form-select" data-required="true">
              <option value="">Select</option><option value="35860;;;AGNOSTIC">AGNOSTIC</option><option value="35861;;;ATHEIST">ATHEIST</option><option value="19205;;;BUDDHISM">BUDDHISM</option><option value="19203;;;CHRISTIANITY">CHRISTIANITY</option><option value="19201;;;HINDUISM">HINDUISM</option><option value="19202;;;ISLAM">ISLAM</option><option value="19206;;;JAINISM">JAINISM</option><option value="35862;;;JUDAISM">JUDAISM</option><option value="35863;;;NEO-PAGANISM">NEO-PAGANISM</option><option value="35864;;;NON-RELIGIOUS">NON-RELIGIOUS</option><option value="35865;;;PRIMAL-INDIGENOUS">PRIMAL-INDIGENOUS</option><option value="35866;;;RASTAFARIANISM">RASTAFARIANISM</option><option value="35867;;;SECULAR">SECULAR</option><option value="35868;;;SHINTO">SHINTO</option><option value="19204;;;SIKHISM">SIKHISM</option><option value="35869;;;SPIRITISM">SPIRITISM</option><option value="35870;;;TENRIKYO">TENRIKYO</option><option value="35871;;;UNITARIAN-UNIVERSALISM">UNITARIAN-UNIVERSALISM</option><option value="19524;;;ZOROASTRIANISM">ZOROASTRIANISM</option><option value="19207;;;RELIGION NOT SPECIFIED">RELIGION NOT SPECIFIED</option><option value="Other">Other</option> 
               
            </select>
            <span class="error"></span>
          </div>

        </div> <!-- row -->

        <hr class="my-4">

        <div class="section-title">Please fill up the course information</div>

        <div class="row g-3">
          <div class="col-md-4">
            <label class="form-label">Your Qualification <span class="required">*</span></label>
            <select name="qualification" class="form-select" data-required="true">
              <option value="">Select</option>
              <option>10th Standard</option>
              <option>12th Standard / Diploma</option>
              <option>Graduation</option>
            </select>
            <span class="error"></span>
          </div>

          <div class="col-md-4">
            <label class="form-label">Faculty <span class="required">*</span></label>
            <select name="faculty" class="form-select" data-required="true">
              <option value="">Select</option>
              <option>Faculty of Commerce and Management</option>
              <option>Faculty of Science</option>
            </select>
            <span class="error"></span>
          </div>

          <div class="col-md-4">
            <label class="form-label">Programme <span class="required">*</span></label>
            <select name="programme" class="form-select" data-required="true">
              <option value="">Select</option>
              <option>Bachelor of Business Administration (BBA)</option>
              <option>Bachelor of Computer Applications (BCA)</option>
            </select>
            <span class="error"></span>
          </div>

          <div class="col-md-4">
            <label class="form-label">Preference 1</label>
            <select name="pref1" class="form-select">
              <option value="">Select</option>
              <option>Bachelor of Business Administration (BBA)</option>
              <option>Bachelor of Commerce (BCom)</option>
            </select>
          </div>

          <div class="col-md-4">
            <label class="form-label">Preference 2</label>
            <select name="pref2" class="form-select">
              <option value="">Select</option>
            </select>
          </div>

          <div class="col-md-4">
            <label class="form-label">Preference 3</label>
            <select name="pref3" class="form-select">
              <option value="">Select</option>
            </select>
          </div>
        </div>

        <hr class="my-4">

        <div class="section-title">Please fill up the parent's information</div>

        <div class="row g-3">
          <div class="col-md-6">
            <label class="form-label">Father's Name <span class="required">*</span></label>
            <input name="father_name" class="form-control" data-required="true" />
            <span class="error"></span>
          </div>

          <div class="col-md-6">
            <label class="form-label">Father's Mobile Number <span class="required">*</span></label>
            <div class="input-group">
              <select class="form-select" name="father_cc" style="max-width:110px;">
                <option value="+91" selected>+91</option>
               </select>
              <input name="father_mobile" class="form-control" data-required="true" />
            </div>
            <span class="error"></span>
          </div>

          <div class="col-md-6">
            <label class="form-label">Mother's Name <span class="required">*</span></label>
            <input name="mother_name" class="form-control" data-required="true" />
            <span class="error"></span>
          </div>

          <div class="col-md-6">
            <label class="form-label">Mother's Mobile Number</label>
            <div class="input-group">
              <select class="form-select" name="mother_cc" style="max-width:110px;">
                <option value="+91" selected>+91</option>
               </select>
              <input name="mother_mobile" class="form-control" placeholder="Enter Your Mother's Mobile No" />
            </div>
            <span class="error"></span>
          </div>
        </div>

        <hr class="my-4">

        <div class="section-title">Please fill up the address</div>

        <div class="muted-box mb-3">
          <strong>Address for Correspondence</strong>
        </div>

        <div class="row g-3">
          <div class="col-md-3">
            <label class="form-label">Country <span class="required">*</span></label>
            <select name="corr_country" id="corr_country" class="form-select" data-required="true">
    <option value="">Select Country</option>
</select>

            <span class="error"></span>
          </div>

          <div class="col-md-3">
            <label class="form-label">State <span class="required">*</span></label>
           
<select name="corr_state" id="corr_state" class="form-select" data-required="true">
    <option value="">Select State</option>
</select>
        <!-- State manual -->
<input type="text" name="corr_state_text" id="corr_state_text"
       class="form-control mt-2 d-none"
       placeholder="Enter State manually">

            <span class="error"></span>
          </div>

        

          <div class="col-md-3">
            <label class="form-label">City <span class="required">*</span></label>
		<select id="corr_city" name="corr_city" class="form-select">
			<option value="">Select City</option>
		</select>      

<!-- City manual -->
			<input type="text" name="corr_city_text" id="corr_city_text"
       class="form-control mt-2 d-none"
       placeholder="Enter City manually">      <span class="error"></span>
          </div>

  <div class="col-md-3">
            <label class="form-label">District <span class="required"> </span></label>
 
<!-- District manual -->
<input type="text" name="corr_district_text" id="corr_district_text"
       class="form-control mt-2"
       placeholder="Enter District manually"> 
	   <span class="error"></span>
          </div>
 </div>

        <div class="row g-3">
	   <div class="col-md-6">
            <label class="form-label">Address Line 1 <span class="required">*</span></label>
            <input name="corr_address1" class="form-control" data-required="true" />
            <span class="error"></span>
          </div>

          <div class="col-md-6">
            <label class="form-label">Address Line 2</label>
            <input name="corr_address2" class="form-control" />
            <span class="error"></span>
          </div>

          <div class="col-md-3">
            <label class="form-label">Pincode <span class="required">*</span></label>
            <input name="corr_pincode" class="form-control" data-required="true" />
            <div class="small-note">In case your country doesn’t have pincode/zipcode enter 0</div>
            <span class="error"></span>
          </div>
        </div>

        <div class="mt-3">
          <label class="form-label">Is permanent address same as address for Correspondence? <span class="required">*</span></label>
          <div>
            <div class="form-check form-check-inline">
              <input class="form-check-input" type="radio" name="same_address" id="same_yes" value="yes" checked>
              <label class="form-check-label" for="same_yes">Yes</label>
            </div>
            <div class="form-check form-check-inline">
              <input class="form-check-input" type="radio" name="same_address" id="same_no" value="no">
              <label class="form-check-label" for="same_no">No</label>
            </div>
            <span class="error" id="same_address_error"></span>
          </div>
        </div>

        <!-- Permanent address (conditionally shown) -->
        <div id="permanent_section" class="mt-4 hidden">
          <div class="muted-box mb-3">
            <strong>Permanent Address</strong>
          </div>

          <div class="row g-3">
             <div class="col-md-3">
            <label class="form-label">Country <span class="required">*</span></label>
            <select name="per_country" id="per_country" class="form-select" data-required="true">
    <option value="">Select Country</option>
</select>

            <span class="error"></span>
          </div>

          <div class="col-md-3">
            <label class="form-label">State <span class="required">*</span></label>
           
<select name="per_state" id="per_state" class="form-select" data-required="true">
    <option value="">Select State</option>
</select>
        <!-- State manual -->
<input type="text" name="per_state_text" id="per_state_text"
       class="form-control mt-2 d-none"
       placeholder="Enter State manually">

            <span class="error"></span>
          </div>

       

          <div class="col-md-3">
            <label class="form-label">City <span class="required">*</span></label>
		<select id="per_city" name="per_city" class="form-select">
			<option value="">Select City</option>
		</select>      

<!-- City manual -->
			<input type="text" name="per_city_text" id="per_city_text"
       class="form-control mt-2 d-none"
       placeholder="Enter City manually">      <span class="error"></span>
          </div>

   <div class="col-md-3">
            <label class="form-label">District <span class="required">*</span></label>
          
 
<!-- District manual -->
<input type="text" name="per_district_text" id="per_district_text"
       class="form-control mt-2 "
       placeholder="Enter District manually"> 
	   <span class="error"></span>
          </div>
         </div>
		 <div class="row g-3">



            <div class="col-md-6">
              <label class="form-label">Address Line 1 <span class="required">*</span></label>
              <input name="perm_address1" class="form-control" />
              <span class="error"></span>
            </div>

            <div class="col-md-6">
              <label class="form-label">Address Line 2</label>
              <input name="perm_address2" class="form-control" />
              <span class="error"></span>
            </div>

            <div class="col-md-3">
              <label class="form-label">Pincode <span class="required">*</span></label>
              <input name="perm_pincode" class="form-control" />
              <div class="small-note">In case your country doesn’t have pincode/zipcode enter 0</div>
              <span class="error"></span>
            </div>
          </div>
        </div>

        <!-- Step controls -->
        <div class="form-actions mt-4">
          <div>
<button type="button"
        class="btn btn-outline-secondary"
        onclick="saveStep({ mode: 'exit' })">
    SAVE & EXIT
</button>          </div>
          <div>
<button type="button"
        class="btn btn-primary"
        onclick="saveStep({ mode: 'next', nextStep: 2 })">
    NEXT
</button>          </div>
        </div>
      </section>

      <!-- =========== STEP 2: EDUCATIONAL DETAILS =========== -->
      <section class="step-section hidden" data-step="2">

        <div class="section-title">Educational Details</div>

        <div class="small-note mb-3">Fill details for 10th and 12th. Required fields marked with *</div>

        <!-- 10th table -->
        <div class="mb-3 panel">
          <h6>10th Details</h6>
          <table class="table table-bordered table-input">
            <thead class="table-light">
              <tr>
                <th width="12%">Level</th>
                <th>School/Institute Name</th>
                <th>Board</th>
                <th width="10%">Year of Passing</th>
                <th width="12%">Result Status</th>
                <th width="12%">Marking System</th>
                <th>Total Marks</th>
                <th>Obtained Marks</th>
                <th>Obtained Percentage / CGPA</th>
              </tr>
            </thead>
            <tbody>
              <tr>
                <td>10th</td>
                <td><input name="tenth_school" class="form-control" data-required="true" /></td>
                <td><input name="tenth_board" class="form-control" data-required="true" /></td>
                <td><input name="tenth_year" class="form-control" data-required="true" /></td>
                <td>
                  <select name="tenth_result" class="form-select" data-required="true">
                    <option value="">Select</option>
                    <option>Passed</option>
                    <option>Await</option>
                  </select>
                </td>
                <td>
                  <select name="tenth_marking" class="form-select">
                    <option value="">Select</option>
                    <option>Marks</option>
                    <option>CGPA</option>
                  </select>
                </td>
                <td><input name="tenth_total" class="form-control" /></td>
                <td><input name="tenth_obtained" class="form-control" /></td>
                <td><input name="tenth_percent" class="form-control" /></td>
              </tr>
            </tbody>
          </table>
        </div>

        <!-- 12th table -->
        <div class="mb-3 panel">
          <h6>12th Details</h6>
          <table class="table table-bordered table-input">
            <thead class="table-light">
              <tr>
                <th width="12%">Level</th>
                <th>School/Institute Name</th>
                <th>Board / University</th>
                <th width="10%">Year of Passing</th>
                <th width="12%">Result Status</th>
                <th width="12%">Marking System</th>
                <th>Total Marks</th>
                <th>Obtained Marks</th>
                <th>Obtained Percentage / CGPA</th>
              </tr>
            </thead>
            <tbody>
              <tr>
                <td>12th</td>
                <td><input name="twelfth_school" class="form-control" data-required="true" /></td>
                <td><input name="twelfth_board" class="form-control" data-required="true" /></td>
                <td><input name="twelfth_year" class="form-control" data-required="true" /></td>
                <td>
                  <select name="twelfth_result" class="form-select" data-required="true">
                    <option value="">Select</option>
                    <option>Passed</option>
                    <option>Await</option>
                  </select>
                </td>
                <td>
                  <select name="twelfth_marking" class="form-select">
                    <option value="">Select</option>
                    <option>Marks</option>
                    <option>CGPA</option>
                  </select>
                </td>
                <td><input name="twelfth_total" class="form-control" /></td>
                <td><input name="twelfth_obtained" class="form-control" /></td>
                <td><input name="twelfth_percent" class="form-control" /></td>
              </tr>
            </tbody>
          </table>
        </div>

        <div class="form-actions">
     <button type="button"
        class="btn btn-outline-secondary"
        onclick="saveStep({ mode: 'exit' })">
    SAVE & EXIT
</button>          </div>
          <div>
<button type="button"
        class="btn btn-primary"
        onclick="saveStep({ mode: 'next', nextStep: 3 })">
    NEXT
</button>  
        </div>
      </section>

      <!-- =========== STEP 3: UPLOAD DOCUMENTS =========== -->
      <section class="step-section hidden" data-step="3">
        <div class="section-title">Upload Documents</div>

        <div class="row g-3">
          <div class="col-md-6">
            <label class="form-label">Upload Your Passport Size Colored Photograph <span class="required">*</span></label>
            <input type="file" name="photo" accept=".jpg,.jpeg,.png" class="form-control" data-required="true" id="photo_input" />
            <div class="small-note">Max Limit 1 MB (only jpeg / jpg / png allowed)</div>
            <span class="error"></span>
          </div>

          <div class="col-md-6">
            <label class="form-label">Upload Signature (optional)</label>
            <input type="file" name="signature" accept=".jpg,.jpeg,.png" class="form-control" />
            <span class="error"></span>
          </div>
        </div>

        <div class="form-actions mt-4">
<button type="button"
        class="btn btn-outline-secondary"
        onclick="saveStep({ mode: 'exit' })">
    SAVE & EXIT
</button>          </div>
          <div>
<button type="button"
        class="btn btn-primary"
        onclick="saveStep({ mode: 'next', nextStep: 4 })">
    NEXT
</button>  
        </div>
      </section>

      <!-- =========== STEP 4: PAYMENT SUMMARY =========== -->
      <section class="step-section hidden" data-step="4">
        <div class="section-title">Preview &amp; Payment</div>

        <div class="row">
          <div class="col-md-7">
            <div class="panel">
              <h6>Personal Details</h6>
              <div id="preview_personal" class="small-note mb-3">
                <!-- JS fills preview -->
              </div>

              <h6>Order Details</h6>
              <div class="small-note">Application Fee (1) — ₹1000</div>
            </div>
          </div>

          <div class="col-md-5">
            <div class="summary-box">
              <h6>Payment Details</h6>
              <div class="d-flex justify-content-between"><div>Sub Total (1)</div><div>₹1000.00</div></div>
              <div class="d-flex justify-content-between"><div>Payment Handling Charges</div><div>₹20.00</div></div>
              <div class="d-flex justify-content-between"><div>Taxes</div><div>₹3.60</div></div>
              <hr>
              <div class="d-flex justify-content-between fw-bold"><div>Total</div><div>₹1023.60</div></div>

              <div class="mt-3">
                <input id="coupon" class="form-control" placeholder="Do you have coupon ?" />
                <div class="d-grid mt-2"><button type="button" class="btn btn-outline-dark btn-sm" onclick="applyCoupon()">APPLY</button></div>
              </div>

              <div class="d-grid mt-3">
                <button type="submit" class="btn btn-primary">Make Payment</button>
              </div>
            </div>
          </div>
        </div>

        <div class="form-actions mt-4">
          <div>
          <div class="form-actions mt-4">
  <div>
    <button type="button"
            class="btn btn-outline-secondary"
            onclick="gotoStep(3)">
      BACK
    </button>

    <button type="button"
            class="btn btn-outline-secondary ms-2"
            onclick="saveStep({ mode: 'exit' })">
      SAVE & EXIT
    </button>
  </div>

  <div>
    <button type="button"
            class="btn btn-primary"
            onclick="submitAndPay()">
      MAKE PAYMENT
    </button>
  </div>
</div>
 </div>
          <div></div>
        </div>
      </section>

    </form>
  </div>
</div>

<!-- Bootstrap + JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>











<script>

  /* ---------- Validation ---------- */
function validateStep(step) {

  const section = document.querySelector('.step-section[data-step="'+step+'"]');
  if (!section) return true;

  let valid = true;

  // 🔹 Normal required fields
  qsa('[data-required="true"]', section).forEach(inp => {

    if (inp.offsetParent === null) return; // 🔥 hidden ignore

    const v = (inp.value || '').trim();
    const error = inp.closest('[class^="col-"]')?.querySelector('.error');

    if (!v) {
      if (error) error.innerText = 'This field is required';
      valid = false;
    } else if (error) {
      error.innerText = '';
    }
  });

  // 🔹 Permanent address ONLY when same_address = no
  const same = document.querySelector('input[name="same_address"]:checked');

  if (same && same.value === 'no') {

    const perm = document.getElementById('permanent_section');

    qsa('[data-required="true"]', perm).forEach(inp => {

      if (inp.offsetParent === null) return;

      const v = (inp.value || '').trim();
      const error = inp.closest('[class^="col-"]')?.querySelector('.error');

      if (!v) {
        if (error) error.innerText = 'This field is required';
        valid = false;
      } else if (error) {
        error.innerText = '';
      }
    });
  }

  return valid;
}



 
</script>

<script>
  /* ---------- helpers ---------- */
  function qs(selector, ctx=document) { return ctx.querySelector(selector); }
  function qsa(selector, ctx=document) { return Array.from(ctx.querySelectorAll(selector)); }

  // Show/hide step sections and pills
  function showStep(step) {
    qsa('.step-section').forEach(s => s.classList.add('hidden'));
    qsa('.step-pill').forEach(p => p.classList.remove('active'));
    const sec = document.querySelector('.step-section[data-step="'+step+'"]');
    if (sec) sec.classList.remove('hidden');
    const pill = document.getElementById('pill-'+step);
    if (pill) pill.classList.add('active');
    window.scrollTo({top:0, behavior:'smooth'});
  }

function gotoStep(step) {

  const current = getCurrentStep();

  if (step > current) {

    // ✅ same_address check
    if (current === 1) {
      const same = document.querySelector('input[name="same_address"]:checked');

      if (!same) {
        document.getElementById('same_address_error').innerText = 'Please choose Yes or No';
        return;
      }

      document.getElementById('same_address_error').innerText = '';

      // ✅ FIRST COPY
      if (same.value === 'yes') {
        copyCorrToPerm();
      }
    }

    // ✅ THEN validate
    if (!validateStep(current)) return;
  }

  showStep(step);
}

  function getCurrentStep() {
    return +qsa('.step-pill').findIndex(p => p.classList.contains('active')) + 1;
  }

  // initial show
  showStep(1);


qsa('input[name="same_address"]').forEach(r => {
  r.addEventListener('change', function(){

    if (this.value === 'yes') {
      document.getElementById('permanent_section').classList.add('hidden');
      copyCorrToPerm(); // 🔥 auto fill immediately
    }

    if (this.value === 'no') {
      document.getElementById('permanent_section').classList.remove('hidden');
    }
  });
});

document.addEventListener("DOMContentLoaded", function () {

  const selected = document.querySelector('input[name="same_address"]:checked');

  if (!selected) return;

  if (selected.value === 'no') {
    document.getElementById('permanent_section').classList.remove('hidden');
  }

  if (selected.value === 'yes') {
    document.getElementById('permanent_section').classList.add('hidden');
  }

});
 function copyCorrToPerm() {

    /* =========================
       COUNTRY
       ========================= */
    const corrCountry = document.getElementById('corr_country');
    const perCountry  = document.getElementById('per_country');

    if (corrCountry && perCountry) {
        perCountry.value = corrCountry.value || '';
        perCountry.dispatchEvent(new Event('change'));
    }

    /* =========================
       STATE (dropdown OR manual)
       ========================= */
    const corrStateSelect = document.getElementById('corr_state');
    const corrStateText   = document.getElementById('corr_state_text');
    const perStateSelect  = document.getElementById('per_state');
    const perStateText    = document.getElementById('per_state_text');

    if (corrStateSelect && corrStateSelect.value) {
        // dropdown used
        perStateSelect.value = corrStateSelect.value;
        perStateSelect.dispatchEvent(new Event('change'));
        perStateText.classList.add('d-none');
        perStateText.required = false;
    } else if (corrStateText && corrStateText.value) {
        // manual used
        perStateText.value = corrStateText.value;
        perStateText.classList.remove('d-none');
        perStateText.required = true;
    }

    /* =========================
       CITY (dropdown OR manual)
       ========================= */
    const corrCitySelect = document.getElementById('corr_city');
    const corrCityText   = document.getElementById('corr_city_text');
    const perCitySelect  = document.getElementById('per_city');
    const perCityText    = document.getElementById('per_city_text');

    if (corrCitySelect && corrCitySelect.value) {
        perCitySelect.value = corrCitySelect.value;
        perCityText.classList.add('d-none');
        perCityText.required = false;
    } else if (corrCityText && corrCityText.value) {
        perCityText.value = corrCityText.value;
        perCityText.classList.remove('d-none');
        perCityText.required = true;
    }

    /* =========================
       DISTRICT (ALWAYS MANUAL)
       ========================= */
    const corrDistrictText = document.getElementById('corr_district_text');
    const perDistrictText  = document.getElementById('per_district_text');

    if (corrDistrictText && perDistrictText) {
        perDistrictText.value = corrDistrictText.value || '';
         perDistrictText.required = true;
    }

    /* =========================
       ADDRESS & PINCODE
       ========================= */
    ['address1','address2','pincode'].forEach(function (field) {
        const src  = document.querySelector('[name="corr_' + field + '"]');
        const dest = document.querySelector('[name="per_' + field + '"]');
        if (src && dest) {
            dest.value = src.value || '';
        }
    });
}


 

  // Apply coupon (demo)
  function applyCoupon(){
    const c = document.getElementById('coupon').value.trim();
    if (!c) { alert('Enter coupon'); return; }
    alert('Coupon "'+c+'" applied (demo).');
  }

  // Fill preview
  function fillPreview(){
    const get = name => (document.querySelector('[name="'+name+'"]') ? document.querySelector('[name="'+name+'"]').value : '');
    const preview = document.getElementById('preview_personal');
    preview.innerHTML = `
      <div><strong>Name :</strong> ${get('full_name')}</div>
      <div><strong>Email Id :</strong> ${get('email')}</div>
      <div><strong>Phone Number :</strong> ${get('mobile_country_code')} ${get('mobile')}</div>
      <div class="mt-2"><strong>Programme :</strong> ${get('programme')}</div>
    `;
  }

  // on submit: validate last step and show collected data (demo)
  document.getElementById('appForm').addEventListener('submit', function(e){
    e.preventDefault();
    // validate current step (4)
    if (!validateStep(4)) return;
    // final validate previous steps too
    for (let s=1;s<=3;s++){
      if (!validateStep(s)) { gotoStep(s); return; }
    }

    // collect data (demo)
    const data = new FormData(this);
    // Example: show some fields
    alert('Form validated. In production send to server via AJAX / form post.\n\nName: '+data.get('full_name')+'\nEmail: '+data.get('email'));
    // For now we simulate submission success and reset to step 1
    // this.reset(); showStep(1);
  });

  // initial small listeners to clear error when editing
  qsa('input,select,textarea').forEach(inp=>{
    inp.addEventListener('input', function(){ const err = this.parentElement.querySelector('.error'); if (err) err.innerText=''; });
  });

  // Guard: when user tries to move from step1 to 2, ensure same_address radio chosen and copy if yes
  // (handled inside gotoStep)

</script>

<script>
const BASE_URL = '/crm/public';

$(document).ready(function () {

    /* ===============================
       LOCATION: Country → State → City
       =============================== */

    // Load countries
$.get(BASE_URL + '/api/countries', function (res) {

    $('#corr_country').html('<option value="">Select Country</option>');
    $('#per_country').html('<option value="">Select Country</option>');

    res.forEach(function (c) {
        $('#corr_country').append(`<option value="${c.id}">${c.name}</option>`);
        $('#per_country').append(`<option value="${c.id}">${c.name}</option>`);
    });

    // 🔥 EDIT MODE
 		if (window.savedData?.corr_country) {
			$('#corr_country')
				.val(window.savedData.corr_country)
				.trigger('change');
		}

		if (window.savedData?.per_country) {
			$('#per_country')
				.val(window.savedData.per_country)
				.trigger('change');
		}

});
/* ===============================
   CORRESPONDENCE: Country → State → City
   =============================== */

$('#corr_country').on('change', function () {

    let countryId = this.value;

    // reset all
    $('#corr_state, #corr_city').show();
    $('#corr_state_text, #corr_city_text')
        .addClass('d-none')
        .prop('required', false)
        .val('');

    if (!countryId) return;

    $('#corr_state').html('<option value="">Loading...</option>');
    $('#corr_city').html('<option value="">Select City</option>');
 
    $.get(BASE_URL + '/api/states', { country_id: countryId }, function (res) {

        /* ---------- NO STATES ---------- */
        if (!res.length) {

            $('#corr_state, #corr_city ')
                .hide()
                .prop('required', false);

            $('#corr_state_text, #corr_city_text ')
                .removeClass('d-none')
                .prop('required', true);

            return;
        }

        /* ---------- STATES FOUND ---------- */
        let html = '<option value="">Select State</option>';
        res.forEach(s => html += `<option value="${s.id}">${s.name}</option>`);
        $('#corr_state').html(html);

        // 🔥 EDIT MODE AUTO SELECT STATE
        if (window.savedData?.corr_state) {
            $('#corr_state')
                .val(window.savedData.corr_state)
                .trigger('change');
        }
		// 🔥 permanent state restore
		if (window.savedData?.per_state) {
			$('#per_state')
				.val(window.savedData.per_state)
				.trigger('change');
		}
    });
});


/* ===============================
   STATE → CITY
   =============================== */

$('#corr_state').on('change', function () {

    let stateId = this.value;

    $('#corr_city_text')
        .addClass('d-none')
        .prop('required', false)
        .val('');

    if (!stateId) return;

    $('#corr_city').html('<option value="">Loading...</option>');
 
    $.get(BASE_URL + '/api/cities', { state_id: stateId }, function (res) {

        /* ---------- NO CITIES ---------- */
        if (!res.length) {

            $('#corr_city').html('<option value="">No city found</option>');
 
            $('#corr_city_text')
                .removeClass('d-none')
                .prop('required', true);

            return;
        }

        /* ---------- CITIES FOUND ---------- */
        let html = '<option value="">Select City</option>';
        res.forEach(c => html += `<option value="${c.id}">${c.name}</option>`);

        $('#corr_city').html(html);
 		
        // 🔥 EDIT MODE AUTO SELECT CITY
        if (window.savedData?.corr_city) {
            $('#corr_city').val(window.savedData.corr_city);
        }

    
    });
});
$('#per_country').on('change', function () {
    let countryId = this.value;

    $('#per_state_text, #per_city_text')
        .addClass('d-none')
        .prop('required', false)
        .val('');

    if (!countryId) return;

    $('#per_state').html('<option value="">Loading...</option>');
    $('#per_city').html('<option value="">Select City</option>');

$.get(BASE_URL + '/api/states', { country_id: countryId }, function (res) {

    if (res.length === 0) {
        $('#per_state').html('<option value="">No state found</option>');
        $('#per_state_text').removeClass('d-none').prop('required', true);
        $('#per_city_text').removeClass('d-none').prop('required', true);
        return;
    }

    let html = '<option value="">Select State</option>';
    res.forEach(s => html += `<option value="${s.id}">${s.name}</option>`);
    $('#per_state').html(html);

    // 🔥 EDIT MODE STATE RESTORE
    if (window.savedData?.per_state) {
        $('#per_state')
            .val(window.savedData.per_state)
            .trigger('change');
    }

});
});
$('#per_state').on('change', function () {
    let stateId = this.value;

    $('#per_city_text')
        .addClass('d-none')
        .prop('required', false)
        .val('');

    if (!stateId) return;

    $('#per_city').html('<option value="">Loading...</option>');

    $.get(BASE_URL + '/api/cities', { state_id: stateId }, function (res) {

   


  if (res.length === 0) {
            // no cities → manual city + district
            $('#per_city').html('<option value="">No city found</option>');
            $('#per_city_text').removeClass('d-none').prop('required', true);
            return;
        }

      let html = '<option value="">Select City</option>';
		res.forEach(c => html += `<option value="${c.id}">${c.name}</option>`);

		$('#per_city').html(html);

		// 🔥 EDIT MODE AUTO SELECT CITY
		if (window.savedData?.per_city) {
			$('#per_city').val(window.savedData.per_city);
		}

        // district always required
     });
});

    /* ===============================
       EDUCATION: Institute → Faculty → Programme
       =============================== */

    // Load institutes
    $.get(BASE_URL + '/api/institutes', function (res) {
        $('#institute').html('<option value="">Select Institute</option>');
        res.forEach(function (i) {
            $('#institute').append(
                `<option value="${i.id}">${i.name}</option>`
            );
        });
    });

    // Institute → Faculty
    $('#institute').on('change', function () {
        let instituteId = this.value;
        if (!instituteId) return;

        $('#faculty').html('<option value="">Loading...</option>');
        $('#programme').html('<option value="">Select Programme</option>');

        $.get(BASE_URL + '/api/faculties', { institute_id: instituteId }, function (res) {
            let html = '<option value="">Select Faculty</option>';
            res.forEach(function (f) {
                html += `<option value="${f.id}">${f.name}</option>`;
            });
            $('#faculty').html(html);
        });
    });

    // Faculty → Programme
    $('#faculty').on('change', function () {
        let facultyId = this.value;
        if (!facultyId) return;

        $('#programme').html('<option value="">Loading...</option>');

        $.get(BASE_URL + '/api/programmes', { faculty_id: facultyId }, function (res) {
            let html = '<option value="">Select Programme</option>';
            res.forEach(function (p) {
                html += `<option value="${p.id}">${p.name}</option>`;
            });
            $('#programme').html(html);
        });
    });

});
function saveStep({ mode='exit', nextStep=null } = {}) {

  const step = getCurrentStep();

  if (!validateStep(step)) return;

  const fd = new FormData(document.getElementById('appForm'));
  fd.append('current_step', step);
  fd.append('progress', step * 25);

  fetch("{{ route('application.form.partial.save') }}", {
    method: 'POST',
    headers: { 'X-CSRF-TOKEN': "{{ csrf_token() }}" },
    body: fd
  })
  .then(r => r.json())
  .then(r => {

    if (!r.status) {
      alert(r.message || 'Save failed');
      return;
    }

    if (mode === 'exit') {
      alert('Saved successfully ✅');
      return;
    }

    if (mode === 'next') {
      showStep(nextStep);
    }
  });
}

window.savedData = @json($response ? json_decode($response->response_json, true) : []);

$(document).ready(function () {

  const d = window.savedData;
  if (!Object.keys(d).length) return;

  // basic fields
  Object.keys(d).forEach(k => {
    const el = document.querySelector(`[name="${k}"]`);
    if (!el) return;

    if (el.type === 'radio') {
      if (el.value == d[k]) el.checked = true;
    } else {
      el.value = d[k];
    }
  });

  // correspondence address
  if (d.corr_country) {
    $('#corr_country').val(d.corr_country).trigger('change');
  }

  // same address
  if (d.same_address === 'yes') {
    $('#same_yes').prop('checked', true).trigger('change');
  }
});

</script>
 
<script>
 
 $(document).ready(function () {

    if (!window.savedData || !Object.keys(window.savedData).length) return;

    const d = window.savedData;

    /* =====================
       BASIC FIELDS
       ===================== */
    Object.keys(d).forEach(function (key) {

        if ([
            'corr_country','corr_state','corr_city',
            'per_country','per_state','per_city'
        ].includes(key)) return;

        const el = document.querySelector(`[name="${key}"]`);
        if (!el) return;

        if (el.type === 'radio') {
            if (el.value == d[key]) el.checked = true;
        } else {
            el.value = d[key];
        }
    });

    /* =====================
       CORRESPONDENCE ADDRESS
       ===================== */
    if (d.corr_country) {
		
         $('#corr_country').val(d.corr_country).trigger('change');
    }

    /* =====================
       SAME ADDRESS
       ===================== */
    if (d.same_address === 'yes') {
        $('#same_yes').prop('checked', true);
    }

});
function submitAndPay() {

  // 🔴 FULL FORM VALIDATION (all steps)
  for (let step = 1; step <= 3; step++) {
    if (!validateStep(step)) {
      showStep(step);
      return;
    }
  }

  const fd = new FormData(document.getElementById('appForm'));
  fd.append('status', 'submitted');      // 🔥 FINAL
  fd.append('progress', 100);

  fetch("{{ route('application.form.final.submit') }}", {
    method: "POST",
    headers: {
      'X-CSRF-TOKEN': "{{ csrf_token() }}"
    },
    body: fd
  })
  .then(r => r.json())
  .then(r => {

    if (!r.status) {
      alert(r.message || 'Submission failed');
      return;
    }

    // ✅ REDIRECT TO PAYMENT GATEWAY
    window.location.href = r.payment_url;

  })
  .catch(() => alert('Server error'));
}

</script>

</body>
</html>