@extends('landing.layouts.main')

@section('content')
    {!! renderSection('navbar') !!}
    {!! renderSection('hero') !!}
    {!! renderSection('services') !!}
    {!! renderSection('features') !!}
    {!! renderSection('plans') !!}
    {!! renderSection('faq') !!}
    {!! renderSection('team') !!}
    {!! renderSection('contact') !!}
    {!! renderSection('footer') !!}
@endsection
