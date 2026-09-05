@extends('FrontEnd.layouts.master')

@section('title', 'Landing Pages')

@section('css')
<link href="{{ URL::asset('build/libs/sweetalert2/sweetalert2.min.css') }}" rel="stylesheet">
@endsection


@section('content')

@component('FrontEnd.components.breadcrumb')
    @slot('li_1') CRM @endslot
    @slot('title')   Landing Pages @endslot
@endcomponent


<div class="card">
    <div class="card-header">
        <h4>Create Landing Page</h4>
    </div>

    <div class="card-body">

        <form action="{{ route('landing-pages.store') }}" method="POST">
            @csrf

            <div class="mb-3">
                <label class="form-label">Page Title</label>
                <input type="text" name="title" class="form-control" required>
            </div>

            <div class="mb-3">
                <label class="form-label">Slug</label>
                <input type="text" name="slug" class="form-control" required>
                <small class="text-muted">example: demo-velzon → URL: /lp/demo-velzon</small>
            </div>

            <div class="mb-3">
                <label class="form-label">Select Layout</label>
                <select name="layout_name" class="form-select" required>
                    <option value="velzon">Velzon Landing</option>
                </select>
            </div>

            <button class="btn btn-primary">Save Page</button>

        </form>

    </div>
</div>

@endsection
