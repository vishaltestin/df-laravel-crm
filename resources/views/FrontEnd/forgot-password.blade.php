@extends('FrontEnd/layouts.loginmaster')
@section('content')
    <div class="log-logo"><img src="{{ env('APP_URL') }}/dist/img/logo.jpg"></div>
    <div class="login-box-body">

        <form  method="POST" action="forgot-password" role="form">
            {{ csrf_field() }}


            @if(session('success'))
                <div class="alert alert-success">
                    {{session('success')}}
                </div>
            @endif


            @if(session('error'))
                <div class="alert alert-danger">
                    {{session('error')}}
                </div>
            @endif

            <div class="form-group has-feedback">
                    <input id="email" type="email" class="form-control" name="email" value="{{ old('email') }}" required>
                <span class="glyphicon glyphicon-envelope form-control-feedback"></span>
                    @if ($errors->has('email'))
                        <span class="help-block">
                                        <strong>{{ $errors->first('email') }}</strong>
                                    </span>
                    @endif

                </div>

                <button type="submit" class="btn btn-primary btn-block btn-flat">
                    Send Code
                </button>


        </form>

    </div>
    <style>
    .log-logo {
	margin-bottom: 9px;
	text-align: center;
	width: 100;
    margin: auto;
    margin-bottom:0px;
    background:#fff;
    border-bottom:1px solid #e2e2e2;
    padding-bottom:10px;
    }
    .log-logo img{
        width:150px;
    }
</style>
@endsection