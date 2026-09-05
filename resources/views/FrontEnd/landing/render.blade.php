@extends('FrontEnd.layouts.app')

@section('content')
    @foreach($sections as $sec)
        @includeIf('FrontEnd.landing.sections.' . $sec['key'], ['data' => $sec['content']])
    @endforeach
@endsection
