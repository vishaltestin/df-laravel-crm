@extends('FrontEnd.layouts.master')
@section('content')
<div class="">


<!-- Main content -->
<section class="content">
  <!-- Small boxes (Stat box) -->
  <div class="row">



<div class="row">

    <div class="col-xs-12">
    <div class="box-header with-border">

        <div class="box-tools pull-right">

         <form action="" method="post" enctype="multipart/form-data" name="searchFltr" class="searchFltr">
                {{ csrf_field() }}
                Start Date <input type="date" data-inputmask="'alias': 'yyyy-mm-dd'" data-mask="" name="start_date" id="start_date" value="{{old('start_date')}}" required="">
                End Date <input type="date" data-inputmask="'alias': 'yyyy-mm-dd'" data-mask="" name="end_date" id="end_date" value="{{old('end_date')}}" required="">
                <button class="btn-primary" title="Search"><i class="ace-icon fa 	fa fa-search  icon-only bigger-100"></i></button>
            </form>

        </div>



           <b>  Record</b>

     


    </div>

    </div>
</div>


<div class="row">&nbsp;</div>

 @if(Sentinel::check() && Sentinel::getUser()->roles()->first()->slug=='root')

    <div class="col-lg-3 col-xs-6">
      <!-- small box -->
      <div class="small-box bg-yellow">
        <div class="inner">
          <h3>{{$user}}</h3>
          <p>Total User Registrations</p>
        </div>
        <div class="icon">
          <i class="ion ion-person-add"></i>
        </div>
        <a href="{{ env('APP_URL') }}/user" class="small-box-footer">More info <i class="fa fa-arrow-circle-right"></i></a>
      </div>
    </div>
    <!-- ./col -->



    
   </div>
  










@endif











  </div>
  <!-- /.row -->


</section>
<!-- /.content -->
</div>
@endsection

@section('javascript')
    <script src="{{ env('APP_URL') }}/bower_components/select2/dist/js/select2.full.min.js"></script>
    <!-- InputMask -->
    <script src="{{ env('APP_URL') }}/plugins/input-mask/jquery.inputmask.js"></script>
    <script src="{{ env('APP_URL') }}/plugins/input-mask/jquery.inputmask.date.extensions.js"></script>
    <script src="{{ env('APP_URL') }}/plugins/input-mask/jquery.inputmask.extensions.js"></script>
    <!-- date-range-picker -->
    <script src="{{ env('APP_URL') }}/bower_components/moment/min/moment.min.js"></script>
    <script src="{{ env('APP_URL') }}/bower_components/bootstrap-daterangepicker/daterangepicker.js"></script>
    <!-- bootstrap datepicker -->
    <script src="{{ env('APP_URL') }}/bower_components/bootstrap-datepicker/dist/js/bootstrap-datepicker.min.js"></script>
    <!-- bootstrap color picker -->
    <script src="{{ env('APP_URL') }}/bower_components/bootstrap-colorpicker/dist/js/bootstrap-colorpicker.min.js"></script>
    <!-- bootstrap time picker -->
    <script src="{{ env('APP_URL') }}/plugins/timepicker/bootstrap-timepicker.min.js"></script>
    <!-- SlimScroll -->
    <script src="{{ env('APP_URL') }}/bower_components/jquery-slimscroll/jquery.slimscroll.min.js"></script>
    <!-- iCheck 1.0.1 -->
    <script src="{{ env('APP_URL') }}/plugins/iCheck/icheck.min.js"></script>
<script>
    $(function () {
        //Initialize Select2 Elements
        $('.select2').select2()

        //Datemask dd/mm/yyyy
        $('#datemask').inputmask('dd/mm/yyyy', { 'placeholder': 'dd/mm/yyyy' })
        //Datemask2 mm/dd/yyyy
        $('#datemask2').inputmask('mm/dd/yyyy', { 'placeholder': 'mm/dd/yyyy' })
        //Money Euro
        $('[data-mask]').inputmask()

        //Date range picker
        $('#reservation').daterangepicker()
        //Date range picker with time picker
        $('#reservationtime').daterangepicker({ timePicker: true, timePickerIncrement: 30, format: 'MM/DD/YYYY h:mm A' })
        //Date range as a button
        $('#daterange-btn').daterangepicker(
            {
                ranges   : {
                    'Today'       : [moment(), moment()],
                    'Yesterday'   : [moment().subtract(1, 'days'), moment().subtract(1, 'days')],
                    'Last 7 Days' : [moment().subtract(6, 'days'), moment()],
                    'Last 30 Days': [moment().subtract(29, 'days'), moment()],
                    'This Month'  : [moment().startOf('month'), moment().endOf('month')],
                    'Last Month'  : [moment().subtract(1, 'month').startOf('month'), moment().subtract(1, 'month').endOf('month')]
                },
                startDate: moment().subtract(29, 'days'),
                endDate  : moment()
            },
            function (start, end) {
                $('#daterange-btn span').html(start.format('MMMM D, YYYY') + ' - ' + end.format('MMMM D, YYYY'))
            }
        )

        //Date picker
        $('#datepicker').datepicker({
            autoclose: true,
            format: 'dd-mm-yyyy'
        })

        //iCheck for checkbox and radio inputs
        $('input[type="checkbox"].minimal, input[type="radio"].minimal').iCheck({
            checkboxClass: 'icheckbox_minimal-blue',
            radioClass   : 'iradio_minimal-blue'
        })
        //Red color scheme for iCheck
        $('input[type="checkbox"].minimal-red, input[type="radio"].minimal-red').iCheck({
            checkboxClass: 'icheckbox_minimal-red',
            radioClass   : 'iradio_minimal-red'
        })
        //Flat red color scheme for iCheck
        $('input[type="checkbox"].flat-red, input[type="radio"].flat-red').iCheck({
            checkboxClass: 'icheckbox_flat-green',
            radioClass   : 'iradio_flat-green'
        })

        //Colorpicker
        $('.my-colorpicker1').colorpicker()
        //color picker with addon
        $('.my-colorpicker2').colorpicker()

        //Timepicker
        $('.timepicker').timepicker({
            showInputs: false
        })
    })
</script>
@endsection
