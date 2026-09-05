@extends('FrontEnd.layouts.master')
@section('content')

  <!-- daterange picker -->
  <link rel="stylesheet" href="{{ env('APP_URL') }}/bower_components/bootstrap-daterangepicker/daterangepicker.css">
  <!-- bootstrap datepicker -->
  <link rel="stylesheet" href="{{ env('APP_URL') }}/bower_components/bootstrap-datepicker/dist/css/bootstrap-datepicker.min.css">
  <!-- iCheck for checkboxes and radio inputs -->
  <link rel="stylesheet" href="{{ env('APP_URL') }}/plugins/iCheck/all.css">
  <!-- Bootstrap Color Picker -->
  <link rel="stylesheet" href="{{ env('APP_URL') }}/bower_components/bootstrap-colorpicker/dist/css/bootstrap-colorpicker.min.css">
  <!-- Bootstrap time Picker -->
  <link rel="stylesheet" href="{{ env('APP_URL') }}/plugins/timepicker/bootstrap-timepicker.min.css">
  <!-- Select2 -->
  <link rel="stylesheet" href="{{ env('APP_URL') }}/bower_components/select2/dist/css/select2.min.css">

  <!-- Theme style -->
  <link rel="stylesheet" href="{{ env('APP_URL') }}/dist/css/AdminLTE.min.css">
  <!-- AdminLTE Skins. Choose a skin from the css/skins
       folder instead of downloading all of them to reduce the load. -->
  <link rel="stylesheet" href="{{ env('APP_URL') }}/dist/css/skins/_all-skins.min.css">



  <section class="content-header">
    <h1>
      Role
      <small>Management</small>
    </h1>
    <ol class="breadcrumb">
      <li><a href="{{ env('APP_URL') }}"><i class="fa fa-dashboard"></i> Home</a></li>
      <li><a href="{{ env('APP_URL') }}/role">Role Management</a></li>
      <li><a href="#">Role Add / Edit</a></li>
    </ol>
  </section>

  <section class="content">

    <div class="box box-default">
      <div class="box-header with-border">
        <h3 class="box-title">Role Add / Edit</h3>

        <div class="box-tools pull-right">

          <a href="{{ env('APP_URL') }}/role" class="btn btn-danger btn-xs"><i class="fa fa-arrow-left"></i></a>

        </div>
      </div>
      <!-- /.box-header -->
      <div class="box-body">


        @empty($roles)
          <form role="form" method="POST" action="{{ env('APP_URL') }}/role/add/new">
            @endempty

            @isset($roles)
              <form role="form" method="POST" action="{{ env('APP_URL') }}/role/edit/{{$roles->id}}">
                @endisset

        {{ csrf_field() }}
        <div class="row">
          <div class="col-md-6">

            <div class="form-group {{ $errors->has('slug') ? ' has-error' : '' }}">
              <label>Slug Name</label>
              <div class="input-group">
                <span class="input-group-addon"><i class="fa fa-file-text"></i></span>
                <input id="slug" type="text" class="form-control"  placeholder="Slug Name *" name="slug" value="{{ isset($roles->slug) ? $roles->slug : old('slug') }}" required autofocus>
              </div>

              @if ($errors->has('slug'))
                <span class="help-block"><strong>{{ $errors->first('slug') }}</strong></span>
              @endif

            </div>

          </div>
          <!-- /.col -->
          <div class="col-md-6">

            <div class="form-group {{ $errors->has('name') ? ' has-error' : '' }}">
              <label>Role Name</label>
              <div class="input-group">
                <span class="input-group-addon"><i class="fa fa-file-text"></i></span>
                <input id="name" type="text" class="form-control" placeholder="Role Name *" name="name" value="{{ isset($roles->name) ? $roles->name : old('name') }}" required autofocus>
              </div>
              @if ($errors->has('name'))
                <span class="help-block"><strong>{{ $errors->first('name') }}</strong></span>
              @endif
            </div>

            <!-- /.form-group -->


          </div>
          <!-- /.col -->

        </div>
        <!-- /.row -->
        <div class="box-footer">

          @empty($roles)
            @if (Sentinel::getUser()->hasAccess(['Role.AddRole']))
              <button type="submit" class="btn btn-primary">Add Role</button>
            @endif
          @endempty

          @isset($roles)
              @if (Sentinel::getUser()->hasAccess(['Role.EditRole']))
                <button type="submit" class="btn btn-primary">Update Role</button>
            @endif

          @endisset



        </div>

              </form>

      <!-- /.box-body -->
    </div>
  </section>

@endsection


@section('javascript')
<!-- Select2 -->
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
<!-- Page script -->
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
