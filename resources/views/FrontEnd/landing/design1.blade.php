{{-- Main dynamic layout that renders sections in order --}}
@extends('FrontEnd.layouts.master')
@section('content')
@foreach($sections as $sec)
    @php
        $key = $sec['key'];
        $data = $sec['content'] ?? [];
    @endphp

    @includeIf('FrontEnd.landing.sections.' . $key, ['data' => $data])
@endforeach
@endsection
