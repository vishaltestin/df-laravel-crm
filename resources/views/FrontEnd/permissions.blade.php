@extends('FrontEnd.layouts.master')
@section('content')


  @include('dashboard/layouts.action')

  <section class="content-header">
    <h1>
      User Role Permissions
    </h1>
    <ol class="breadcrumb">
      <li><a href="#"><i class="fa fa-dashboard"></i> Home</a></li>
      <li><a href="#">User Role Permissions</a></li>
    </ol>
  </section>

  <section class="content">

    <div class="box box-default">
      <div class="box-header with-border">
          <h3 class="box-title">User <b>{{$user->first_name}} {{$user->last_name}}</b> Role Permissions</h3>

        <div class="box-tools pull-right">

            <a href="{{ env('APP_URL') }}/user" class="btn btn-danger btn-xs"><i class="fa fa-arrow-left"></i></a>

        </div>

      </div>
      <!-- /.box-header -->
      <div class="box-body">



            <form role="form" method="POST" action="{{ env('APP_URL') }}/user/{{$user->id}}/save">

                @if(session('error'))
                    <div class="alert alert-danger">
                        {{session('error')}}
                    </div>
                @endif

                @if(session('success'))
                    <div class="alert alert-success">
                        {{session('success')}}
                    </div>
            @endif

            {{ csrf_field() }}

                    <div class="row">

                        @foreach($actions as $action)
                        <div class="col-md-4">

                            <?php $first= array_values($action)[0];
                            $firstname =explode(".", $first)[0];
                            ?>

                            <div class="form-group">
                                <div class="input-group">
                            <label>{{ucfirst($firstname)}}</label>
                                </div>
                            <select name="permissions[]" class="form-control select" style="width: 100%;" multiple="multiple">
                                    @foreach($action as $act)
                                        @if(explode(".", $act)[0]=="api")
                                            <option value="{{$act}}"  {{array_key_exists($act, $user->permissions)?"selected":""}}>
                                                {{isset(explode(".", $act)[2])?explode(".", $act)[1].".".explode(".", $act)[2]:explode(".", $act)[1]}}</option>
                                        @else
                                            <option value="{{$act}}" {{array_key_exists($act, $user->permissions)?"selected":""}}>

                                                {{explode(".", $act)[1]}}

                                            </option>
                                        @endif
                                    @endforeach
                            </select>

                            </div>

                        </div>
                        @endforeach




                    </div>


                    <div class="box-footer">
                    <button type="submit" class="btn btn-primary pull-right" style="position: relative; z-index: 1;">Add Permissions</button>

                     </div>


            </form>






      </div>
      <!-- /.box-body -->
    </div>
  </section>

@endsection
@section('javascript')
<script src="{{ URL::asset('backend/sumoselect/jquery.sumoselect.js') }}"></script>
<link href="{{ URL::asset('backend/sumoselect/sumoselect.css') }}" rel="stylesheet" />

<script type="text/javascript">
    $('.select').SumoSelect({ selectAll: true, placeholder: 'Nothing selected' });
</script>

@endsection