@extends('FrontEnd.layouts.master')
@section('title', 'Leads')
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
            Leads
        @endslot
    @endcomponent
@section('content')
<div class="container">
    <h2>Add New Lead</h2>

    <form action="{{ env('APP_URL') }}/leads/store" method="POST">
        @csrf
        <div class="mb-3">
            <label>Name</label>
            <input type="text" name="name" class="form-control" required>
        </div>

        <div class="mb-3">
            <label>Mobile</label>
            <input type="text" name="mobile" class="form-control" required>
        </div>

        <div class="mb-3">
            <label>City</label>
            <input type="text" name="city" class="form-control">
        </div>

        <div class="mb-3">
            <label>Country</label>
            <input type="text" name="country" class="form-control">
        </div>

        <button type="submit" class="btn btn-success">Save Lead</button>
    </form>
</div>
@endsection
