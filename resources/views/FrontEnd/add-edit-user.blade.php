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

      User

      <small>Management</small>

    </h1>

    <ol class="breadcrumb">

      <li><a href="{{ env('APP_URL') }}"><i class="fa fa-dashboard"></i> Home</a></li>

      <li><a href="{{ env('APP_URL') }}/user">User Management</a></li>

      <li><a href="#">User Add / Edit</a></li>

    </ol>

  </section>



  <section class="content">



    <div class="box box-default">

      <div class="box-header with-border">

        <h3 class="box-title">User Add / Edit</h3>



        <div class="box-tools pull-right">



          <a href="{{ env('APP_URL') }}/user" class="btn btn-danger btn-xs"><i class="fa fa-arrow-left"></i></a>



        </div>

      </div>

      <!-- /.box-header -->

      <div class="box-body">





        @empty($users)

          <form role="form" method="POST" action="{{ env('APP_URL') }}/user/add/new">

            @endempty



            @isset($users)

              <form role="form" method="POST" action="{{ env('APP_URL') }}/user/edit/{{$users->id}}">

                @endisset



        {{ csrf_field() }}

        <div class="row">

          <div class="col-md-6">



            <div class="form-group {{ $errors->has('first_name') ? ' has-error' : '' }}">

              <label>First Name</label>

              <div class="input-group">

                <span class="input-group-addon"><i class="fa fa-file-text"></i></span>

                <input id="first_name" type="text" class="form-control"  placeholder="First Name *" name="first_name" value="{{ isset($users->first_name) ? $users->first_name : old('first_name') }}" required autofocus>

              </div>



              @if ($errors->has('first_name'))

                <span class="help-block"><strong>{{ $errors->first('first_name') }}</strong></span>

              @endif



            </div>









            <div class="form-group {{ $errors->has('email') ? ' has-error' : '' }}">

              <label>Email ID</label>

              <div class="input-group">

                <span class="input-group-addon"><i class="fa fa-envelope"></i></span>

                <input id="email" type="email" class="form-control" name="email" placeholder="Email *" value="{{ isset($users->email) ? $users->email : old('email') }}" required autofocus>

              </div>

              @if ($errors->has('email'))

                <span class="help-block"><strong>{{ $errors->first('email') }}</strong></span>

              @endif

            </div>







           

            <!-- /.form-group -->





            <div class="form-group  {{ $errors->has('role') ? ' has-error' : '' }}">

              <label>Roles</label>

              <select class="form-control select2" style="width: 100%;" name="role" id="role" required autofocus>

                <option value="">Select Role</option>

                <option value="root" @isset($roles->slug) @if($roles->slug=='root'){{ 'selected' }} @endif @endisset>Root Base</option>

                <option value="sadmin" @isset($roles->slug) @if($roles->slug=='sadmin'){{ 'selected' }} @endif @endisset>Super Admin</option>

                <option value="admin" @isset($roles->slug) @if($roles->slug=='admin'){{ 'selected' }} @endif @endisset>Administrator</option>

                <option value="manager" @isset($roles->slug) @if($roles->slug=='manager'){{ 'selected' }} @endif @endisset>Manager</option>             

                <option value="operater" @isset($roles->slug) @if($roles->slug=='operater'){{ 'selected' }} @endif @endisset>Operater</option>

             
              </select>

              @if ($errors->has('role'))

                <span class="help-block"><strong>{{ $errors->first('role') }}</strong></span>

              @endif

             </div>

            <!-- /.form-group -->



          </div>

          <!-- /.col -->

          <div class="col-md-6">



            <div class="form-group {{ $errors->has('last_name') ? ' has-error' : '' }}">

              <label>Last Name</label>

              <div class="input-group">

                <span class="input-group-addon"><i class="fa fa-file-text"></i></span>

                <input id="last_name" type="text" class="form-control" placeholder="Last Name *" name="last_name" value="{{ isset($users->last_name) ? $users->last_name : old('last_name') }}" required autofocus>

              </div>

              @if ($errors->has('last_name'))

                <span class="help-block"><strong>{{ $errors->first('last_name') }}</strong></span>

              @endif

            </div>



            <!-- /.form-group -->





            <div class="form-group {{ $errors->has('password') ? ' has-error' : '' }}">

              <label>Password</label>

              <div class="input-group">

                <span class="input-group-addon"><i class="fa fa-lock"></i></span>

                <input id="password" type="password" class="form-control" placeholder="Password *" name="password" value="" required autofocus>

               </div>

              @if ($errors->has('password'))

                <span class="help-block"><strong>{{ $errors->first('password') }}</strong></span>

              @endif

            </div>







            <div class="form-group {{ $errors->has('password') ? ' has-error' : '' }}">

              <label>Confirm Password</label>

              <div class="input-group">

                <span class="input-group-addon"><i class="fa fa-lock"></i></span>

                <input id="password-confirm" type="password" class="form-control" placeholder="Confirm Password *" name="password_confirmation" value="" required autofocus>

               </div>

              @if ($errors->has('password-confirm'))

                <span class="help-block"><strong>{{ $errors->first('password-confirm') }}</strong></span>

              @endif

            </div>







            <div class="form-group {{ $errors->has('status') ? ' has-error' : '' }}">

              <label>Status</label>

              <select class="form-control select2" style="width: 100%;" name="status" id="status" required autofocus>

                <option value="">Select Status</option>

                <option value="1" @isset($users->status)  @if($users->status==1){{ 'selected' }} @endif @endisset>Active</option>

                <option value="0" @isset($users->status)  @if($users->status==0){{ 'selected' }} @endif @endisset>Inactive</option>

              </select>

              @if ($errors->has('status'))

                <span class="help-block"><strong>{{ $errors->first('status') }}</strong></span>

              @endif

            </div>

            <!-- /.form-group -->









          </div>

          <!-- /.col -->



        </div>

        <!-- /.row -->

        <div class="box-footer">



          @empty($users)

            @if (Sentinel::getUser()->hasAccess(['User.AddUser']))

              <button type="submit" class="btn btn-primary">Add User</button>

            @endif

          @endempty



          @isset($users)

            @if (Sentinel::getUser()->hasAccess(['User.EditUser']))

                <button type="submit" class="btn btn-primary">Update User</button>

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

