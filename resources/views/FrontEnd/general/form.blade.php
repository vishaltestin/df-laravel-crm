@extends('FrontEnd.layouts.master')
@section('title', 'Configuration')
@section('css')
<link href="{{ URL::asset('build/libs/sweetalert2/sweetalert2.min.css') }}" rel="stylesheet">

@endsection
@section('css')
@endsection
@section('content')
    @component('FrontEnd.components.breadcrumb')
        @slot('li_1')
            CRM
        @endslot
        @slot('title')
            General Settings
        @endslot
    @endcomponent

   <div class="row">
  <div class="col-lg-6">
		<div class="" id=""> 
			<div class="">
                    <div>
                    <div class="container mt-3">
<div class="container mt-3">   <h4> General </h4>
<p>Configure or  Defines the timezone and currency  used in the user interface, such as Arabic (ar), English (en), Spanish (es), Persian(fa) and Turkish (tr).  settings here. </p></div>
	</div>
	</div>
	</div>
		</div>

		</div>

	
	
	
        <div class="col-lg-6">
            <div class="card" id="leadsList"> 
			<div class="card-body">
                    <div>
                    <div class="container mt-3">
     <form id="generalForm" method="post">
        @csrf

        <div class="mb-3">
            <label>App Name</label>
            <input type="text" name="app_name" class="form-control"
                   value="{{ $setting->app_name }}">
        </div>

        <div class="mb-3">
            <label>Currency</label>
          <select name="currency" class="form-control">
    @php
        $currencies = [
            'USD' => 'US Dollar',
            'EUR' => 'Euro',
            'GBP' => 'British Pound',
            'INR' => 'Indian Rupee',
            'AUD' => 'Australian Dollar',
            'CAD' => 'Canadian Dollar',
            'SGD' => 'Singapore Dollar',
            'JPY' => 'Japanese Yen',
            'CNY' => 'Chinese Yuan',
            'NZD' => 'New Zealand Dollar',
            'CHF' => 'Swiss Franc',
            'AED' => 'UAE Dirham',
            'SAR' => 'Saudi Riyal',
            'QAR' => 'Qatari Riyal',
            'ZAR' => 'South African Rand',
            'MYR' => 'Malaysian Ringgit',
            'BHD' => 'Bahraini Dinar',
            'KWD' => 'Kuwaiti Dinar',
            'OMR' => 'Omani Rial'
        ];
    @endphp

    @foreach($currencies as $code => $name)
        <option value="{{ $code }}" {{ ($setting->currency ?? '') == $code ? 'selected' : '' }}>
            {{ $code }} - {{ $name }}
        </option>
    @endforeach
</select>

        </div>

        <div class="mb-3">
            <label>Timezone</label>
      <select name="timezone" class="form-control">

    <option value="Africa/Abidjan" @if($setting->timezone=="Africa/Abidjan") selected @endif>Africa/Abidjan</option>
    <option value="Africa/Accra" @if($setting->timezone=="Africa/Accra") selected @endif>Africa/Accra</option>
    <option value="Africa/Addis_Ababa" @if($setting->timezone=="Africa/Addis_Ababa") selected @endif>Africa/Addis_Ababa</option>
    <option value="Africa/Algiers" @if($setting->timezone=="Africa/Algiers") selected @endif>Africa/Algiers</option>
    <option value="Africa/Asmara" @if($setting->timezone=="Africa/Asmara") selected @endif>Africa/Asmara</option>
    <option value="Africa/Bamako" @if($setting->timezone=="Africa/Bamako") selected @endif>Africa/Bamako</option>
    <option value="Africa/Bangui" @if($setting->timezone=="Africa/Bangui") selected @endif>Africa/Bangui</option>
    <option value="Africa/Banjul" @if($setting->timezone=="Africa/Banjul") selected @endif>Africa/Banjul</option>
    <option value="Africa/Bissau" @if($setting->timezone=="Africa/Bissau") selected @endif>Africa/Bissau</option>
    <option value="Africa/Blantyre" @if($setting->timezone=="Africa/Blantyre") selected @endif>Africa/Blantyre</option>
    <option value="Africa/Brazzaville" @if($setting->timezone=="Africa/Brazzaville") selected @endif>Africa/Brazzaville</option>
    <option value="Africa/Bujumbura" @if($setting->timezone=="Africa/Bujumbura") selected @endif>Africa/Bujumbura</option>
    <option value="Africa/Cairo" @if($setting->timezone=="Africa/Cairo") selected @endif>Africa/Cairo</option>
    <option value="Africa/Casablanca" @if($setting->timezone=="Africa/Casablanca") selected @endif>Africa/Casablanca</option>
    <option value="Africa/Ceuta" @if($setting->timezone=="Africa/Ceuta") selected @endif>Africa/Ceuta</option>
    <option value="Africa/Conakry" @if($setting->timezone=="Africa/Conakry") selected @endif>Africa/Conakry</option>
    <option value="Africa/Dakar" @if($setting->timezone=="Africa/Dakar") selected @endif>Africa/Dakar</option>
    <option value="Africa/Dar_es_Salaam" @if($setting->timezone=="Africa/Dar_es_Salaam") selected @endif>Africa/Dar_es_Salaam</option>
    <option value="Africa/Djibouti" @if($setting->timezone=="Africa/Djibouti") selected @endif>Africa/Djibouti</option>
    <option value="Africa/Douala" @if($setting->timezone=="Africa/Douala") selected @endif>Africa/Douala</option>
    <option value="Africa/El_Aaiun" @if($setting->timezone=="Africa/El_Aaiun") selected @endif>Africa/El_Aaiun</option>
    <option value="Africa/Freetown" @if($setting->timezone=="Africa/Freetown") selected @endif>Africa/Freetown</option>
    <option value="Africa/Gaborone" @if($setting->timezone=="Africa/Gaborone") selected @endif>Africa/Gaborone</option>
    <option value="Africa/Harare" @if($setting->timezone=="Africa/Harare") selected @endif>Africa/Harare</option>
    <option value="Africa/Johannesburg" @if($setting->timezone=="Africa/Johannesburg") selected @endif>Africa/Johannesburg</option>
    <option value="Africa/Juba" @if($setting->timezone=="Africa/Juba") selected @endif>Africa/Juba</option>
    <option value="Africa/Kampala" @if($setting->timezone=="Africa/Kampala") selected @endif>Africa/Kampala</option>
    <option value="Africa/Khartoum" @if($setting->timezone=="Africa/Khartoum") selected @endif>Africa/Khartoum</option>
    <option value="Africa/Kigali" @if($setting->timezone=="Africa/Kigali") selected @endif>Africa/Kigali</option>
    <option value="Africa/Kinshasa" @if($setting->timezone=="Africa/Kinshasa") selected @endif>Africa/Kinshasa</option>
    <option value="Africa/Lagos" @if($setting->timezone=="Africa/Lagos") selected @endif>Africa/Lagos</option>
    <option value="Africa/Libreville" @if($setting->timezone=="Africa/Libreville") selected @endif>Africa/Libreville</option>
    <option value="Africa/Lome" @if($setting->timezone=="Africa/Lome") selected @endif>Africa/Lome</option>
    <option value="Africa/Luanda" @if($setting->timezone=="Africa/Luanda") selected @endif>Africa/Luanda</option>
    <option value="Africa/Lubumbashi" @if($setting->timezone=="Africa/Lubumbashi") selected @endif>Africa/Lubumbashi</option>
    <option value="Africa/Lusaka" @if($setting->timezone=="Africa/Lusaka") selected @endif>Africa/Lusaka</option>
    <option value="Africa/Malabo" @if($setting->timezone=="Africa/Malabo") selected @endif>Africa/Malabo</option>
    <option value="Africa/Maputo" @if($setting->timezone=="Africa/Maputo") selected @endif>Africa/Maputo</option>
    <option value="Africa/Maseru" @if($setting->timezone=="Africa/Maseru") selected @endif>Africa/Maseru</option>
    <option value="Africa/Mbabane" @if($setting->timezone=="Africa/Mbabane") selected @endif>Africa/Mbabane</option>
    <option value="Africa/Mogadishu" @if($setting->timezone=="Africa/Mogadishu") selected @endif>Africa/Mogadishu</option>
    <option value="Africa/Monrovia" @if($setting->timezone=="Africa/Monrovia") selected @endif>Africa/Monrovia</option>
    <option value="Africa/Nairobi" @if($setting->timezone=="Africa/Nairobi") selected @endif>Africa/Nairobi</option>
    <option value="Africa/Ndjamena" @if($setting->timezone=="Africa/Ndjamena") selected @endif>Africa/Ndjamena</option>
    <option value="Africa/Niamey" @if($setting->timezone=="Africa/Niamey") selected @endif>Africa/Niamey</option>
    <option value="Africa/Nouakchott" @if($setting->timezone=="Africa/Nouakchott") selected @endif>Africa/Nouakchott</option>
    <option value="Africa/Ouagadougou" @if($setting->timezone=="Africa/Ouagadougou") selected @endif>Africa/Ouagadougou</option>
    <option value="Africa/Porto-Novo" @if($setting->timezone=="Africa/Porto-Novo") selected @endif>Africa/Porto-Novo</option>
    <option value="Africa/Sao_Tome" @if($setting->timezone=="Africa/Sao_Tome") selected @endif>Africa/Sao_Tome</option>
    <option value="Africa/Tripoli" @if($setting->timezone=="Africa/Tripoli") selected @endif>Africa/Tripoli</option>
    <option value="Africa/Tunis" @if($setting->timezone=="Africa/Tunis") selected @endif>Africa/Tunis</option>
    <option value="Africa/Windhoek" @if($setting->timezone=="Africa/Windhoek") selected @endif>Africa/Windhoek</option>

    <option value="America/New_York" @if($setting->timezone=="America/New_York") selected @endif>America/New_York</option>
    <option value="America/Chicago" @if($setting->timezone=="America/Chicago") selected @endif>America/Chicago</option>
    <option value="America/Los_Angeles" @if($setting->timezone=="America/Los_Angeles") selected @endif>America/Los_Angeles</option>
    <option value="America/Denver" @if($setting->timezone=="America/Denver") selected @endif>America/Denver</option>
    <option value="America/Toronto" @if($setting->timezone=="America/Toronto") selected @endif>America/Toronto</option>
    <option value="America/Vancouver" @if($setting->timezone=="America/Vancouver") selected @endif>America/Vancouver</option>
    <option value="America/Mexico_City" @if($setting->timezone=="America/Mexico_City") selected @endif>America/Mexico_City</option>

    <option value="Asia/Kolkata" @if($setting->timezone=="") selected @endif   @if($setting->timezone=="Asia/Kolkata") selected @endif>Asia/Kolkata (India)</option>
    <option value="Asia/Dubai" @if($setting->timezone=="Asia/Dubai") selected @endif>Asia/Dubai</option>
    <option value="Asia/Kathmandu" @if($setting->timezone=="Asia/Kathmandu") selected @endif>Asia/Kathmandu</option>
    <option value="Asia/Shanghai" @if($setting->timezone=="Asia/Shanghai") selected @endif>Asia/Shanghai</option>
    <option value="Asia/Tokyo" @if($setting->timezone=="Asia/Tokyo") selected @endif>Asia/Tokyo</option>
    <option value="Asia/Singapore" @if($setting->timezone=="Asia/Singapore") selected @endif>Asia/Singapore</option>
    <option value="Asia/Hong_Kong" @if($setting->timezone=="Asia/Hong_Kong") selected @endif>Asia/Hong_Kong</option>
    <option value="Asia/Bangkok" @if($setting->timezone=="Asia/Bangkok") selected @endif>Asia/Bangkok</option>

    <option value="Australia/Sydney" @if($setting->timezone=="Australia/Sydney") selected @endif>Australia/Sydney</option>
    <option value="Australia/Melbourne" @if($setting->timezone=="Australia/Melbourne") selected @endif>Australia/Melbourne</option>
    <option value="Australia/Perth" @if($setting->timezone=="Australia/Perth") selected @endif>Australia/Perth</option>

    <option value="Europe/London" @if($setting->timezone=="Europe/London") selected @endif>Europe/London</option>
    <option value="Europe/Paris" @if($setting->timezone=="Europe/Paris") selected @endif>Europe/Paris</option>
    <option value="Europe/Berlin" @if($setting->timezone=="Europe/Berlin") selected @endif>Europe/Berlin</option>
    <option value="Europe/Madrid" @if($setting->timezone=="Europe/Madrid") selected @endif>Europe/Madrid</option>
    <option value="Europe/Rome" @if($setting->timezone=="Europe/Rome") selected @endif>Europe/Rome</option>
    <option value="Europe/Moscow" @if($setting->timezone=="Europe/Moscow") selected @endif>Europe/Moscow</option>

    <option value="Pacific/Auckland" @if($setting->timezone=="Pacific/Auckland") selected @endif>Pacific/Auckland</option>
    <option value="Pacific/Fiji" @if($setting->timezone=="Pacific/Fiji") selected @endif>Pacific/Fiji</option>

</select>
  </div>
 
 
 
 <h5 class="mt-4">Payment Gateway</h5>

<div class="mb-3">
    <label>Payment Gateway</label>
    <select name="payment[gateway]" id="payment_gateway" class="form-control">
        <option value="">-- Select Gateway --</option>
        <option value="razorpay">Razorpay</option>
        <option value="stripe">Stripe</option>
        <option value="payu">PayU</option>
        <option value="cashfree">Cashfree</option>
    </select>
</div>

<div id="payment_fields" style="display:none;">
    
    <div class="mb-3">
        <label>Mode</label>
        <select name="payment[mode]" class="form-control">
            <option value="test">Test</option>
            <option value="live">Live</option>
        </select>
    </div>

    <div class="mb-3">
        <label>Key ID</label>
        <input type="text" name="payment[key_id]" class="form-control">
    </div>

    <div class="mb-3">
        <label>Secret Key</label>
        <input type="password" name="payment[secret_key]" class="form-control">
    </div>

    <div class="mb-3">
        <label>Merchant ID</label>
        <input type="text" name="payment[merchant_id]" class="form-control">
    </div>

    <div class="mb-3">
        <label>Webhook Secret</label>
        <input type="text" name="payment[webhook_secret]" class="form-control">
    </div>

</div>





        <button class="btn btn-primary mt-2">Save</button>  
    </form>
</div>


                       
                    </div>
            
                </div>
            </div>

        </div>
        <!--end col-->
    </div>
    <!--end row-->

@endsection
@section('script')
    <script src="{{ URL::asset('build/libs/list.js/list.min.js') }}"></script>
    <script src="{{ URL::asset('build/libs/list.pagination.js/list.pagination.min.js') }}"></script>
    <script src="{{ URL::asset('build/libs/sweetalert2/sweetalert2.min.js') }}"></script>
    
    
	
		<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>


<script>
$("#generalForm").on("submit", function(e){
    e.preventDefault();
    $.ajax({
        url: "{{ url('general/update') }}",
        type: "POST",
        data: $(this).serialize(),
        success: function(res){
            alert(res.message);
        }
    });
});


</script>
   <script>
$('#payment_gateway').on('change', function () {
    if ($(this).val()) {
        $('#payment_fields').slideDown();
    } else {
        $('#payment_fields').slideUp();
    }
});
</script>

@endsection
 