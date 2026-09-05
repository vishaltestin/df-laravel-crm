@extends('FrontEnd.layouts.master')
@section('title', 'Team')
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
          Permission
        @endslot
    @endcomponent



<div class="row">
                        <div class="col-lg-12">
                            <div class="card">
                                <div class="card-header align-items-center d-flex">
                                    <h4 class="card-title mb-0 flex-grow-1"> Role Permissions</h4>
                                    <div class="flex-shrink-0">
                                        <div class="form-check form-switch form-switch-right form-switch-md">
                                            <label for="navbarscrollspy-showcode" class="form-label text-muted"> <b>{{$role->name}}</b>  </label>
                                         </div>
                                    </div>
                                </div><!-- end card header -->

                                <div class="card-body">
  

  <section class="content">

    <div class="box box-default">
  
      <!-- /.box-header -->
      <div class="box-body">



            <form role="form" method="POST" action="{{ env('APP_URL') }}/role/{{$role->id}}/save">

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
                                            <option value="{{$act}}"  {{array_key_exists($act, $role->permissions)?"selected":""}}>
                                                {{isset(explode(".", $act)[2])?explode(".", $act)[1].".".explode(".", $act)[2]:explode(".", $act)[1]}}</option>
                                        @else
                                            <option value="{{$act}}" {{array_key_exists($act, $role->permissions)?"selected":""}}>

                                                {{explode(".", $act)[1]}}

                                            </option>
                                        @endif
                                    @endforeach
                            </select>

                            </div>

                        </div>
                        @endforeach




                    </div>


                    <div class="row box-footer">
                                           <div class="col-md-4 pt-5">
 <button type="submit" class="btn btn-primary pull-right" style="position: relative; z-index: 1;">Add Permissions</button>

                     </div>
                     </div>


            </form>






      </div>
      <!-- /.box-body -->
    </div>
  </section>

                            
                                </div><!-- end card-body -->
                            </div><!-- end card -->
                        </div>
                        <!-- end col -->
                    </div>

@endsection
@section('javascript')
<script src="{{ URL::asset('backend/sumoselect/jquery.sumoselect.js') }}"></script>
<link href="{{ URL::asset('backend/sumoselect/sumoselect.css') }}" rel="stylesheet" />

<script type="text/javascript">
    $('.select').SumoSelect({ selectAll: true, placeholder: 'Nothing selected' });
</script>

@endsection