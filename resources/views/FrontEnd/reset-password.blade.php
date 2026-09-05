@extends('FrontEnd/layouts.loginmaster')
@section('content')
<div class="log-logo"><img src="{{ env('APP_URL') }}/dist/img/logo.jpg"></div>
    <div class="login-box-body">
        <form  method="POST" action="" role="form">
            @if(count($errors)>0)
                <div class="alert  alert-danger">
                    <ul>
                        @foreach($errors->all() as $error)
                            <li>{{$error}}</li>
                        @endforeach
                    </ul>
                </div>
            @endif
            {{ csrf_field() }}
                <div class="form-group has-feedback">
                    <input id="password" type="password" class="form-control" name="password" required>
                    <span class="glyphicon glyphicon-lock form-control-feedback"></span>
                    @if ($errors->has('password'))
                        <span class="help-block">
                            <strong>{{ $errors->first('password') }}</strong>
                        </span>
                    @endif
                </div>
                <div class="form-group has-feedback">
                    <input id="password-confirm" type="password" class="form-control" name="password_confirmation" required>
                    <span class="glyphicon glyphicon-lock form-control-feedback"></span>
                    @if ($errors->has('password-confirm'))
                        <span class="help-block">
                            <strong>{{ $errors->first('password-confirm') }}</strong>
                        </span>
                    @endif
                </div>
                <button type="submit" class="btn btn-primary btn-block btn-flat">
                    Update Password
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