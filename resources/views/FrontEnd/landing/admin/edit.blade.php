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


<div class="container">

    <h3>Edit Landing Page</h3>

    <form action="{{ route('landing-pages.update', $page->id) }}" method="POST">
        @csrf
        @method('PUT')

        <div class="mb-3">
            <label>Title</label>
            <input type="text" name="title" class="form-control" value="{{ $page->title }}" required>
        </div>

        <div class="mb-3">
            <label>Slug</label>
            <input type="text" name="slug" class="form-control" value="{{ $page->slug }}" required>
        </div>

        <div class="mb-3">
            <label>Layout</label>
            <input type="text" name="layout_name" class="form-control" value="{{ $page->layout_name }}" required>
        </div>

        <div class="mb-3">
            <label>Status</label>
            <select name="status" class="form-control">
                <option value="1" {{ $page->status ? 'selected' : '' }}>Active</option>
                <option value="0" {{ !$page->status ? 'selected' : '' }}>Inactive</option>
            </select>
        </div>

        <button class="btn btn-primary">Update</button>
    </form>

</div>
@endsection
