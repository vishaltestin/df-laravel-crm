@extends('FrontEnd.layouts.master')
@section('title', 'Lead  Settings')

@section('css')
    <link href="{{ URL::asset('build/libs/sweetalert2/sweetalert2.min.css') }}" rel="stylesheet">
@endsection

@section('content')
    @component('FrontEnd.components.breadcrumb')
        @slot('li_1') CRM @endslot
        @slot('title') Lead  Settings @endslot
    @endcomponent

    <div class="row">
        <div class="col-lg-12">
            <div class="card" id="leadSubStatusCard">
                <div class="card-header border-0">
                    <div class="row g-4 align-items-center">
                        <div class="col-sm">
                            <h5 class="card-title mb-0">Lead Settings</h5>
                        </div>
                        <div class="col-sm-auto">
                           
                        </div>
                    </div>
                </div>

                <div class="card-body">

                    @if(session('success'))
                        <div class="alert alert-success">{{ session('success') }}</div>
                    @endif

                    <div class="table-responsive table-card">
                
    <form method="POST" action="{{ route('lead-settings/save') }}">
        @csrf

        <div class="table-responsive">
            <table class="table table-bordered align-middle">
                <thead>
                    <tr>
                        <th>Role</th>
                        <th>View Mobile</th>
                        <th>View Email</th>
                        <th>Can Download</th>
                    </tr>
                </thead>
                <tbody>

         @foreach($roles as $role)
    @php
        $roleSettings = $settings->get($role->slug, collect());
        $viewMobile = $roleSettings->where('field','view_mobile')->first();
        $viewEmail = $roleSettings->where('field','view_email')->first();
        $canDownload = $roleSettings->where('field','can_download')->first();
    @endphp

    <tr>
        <td>{{ ucfirst($role->slug) }}</td>

        <td class="text-center">
            <input type="checkbox"
                   name="settings[{{ $role->slug }}][view_mobile]"
                   value="1"
                   {{ ($viewMobile && $viewMobile->can_view == 1) ? 'checked' : '' }}>
        </td>

        <td class="text-center">
            <input type="checkbox"
                   name="settings[{{ $role->slug }}][view_email]"
                   value="1"
                   {{ ($viewEmail && $viewEmail->can_view == 1) ? 'checked' : '' }}>
        </td>

        <td class="text-center">
            <input type="checkbox"
                   name="settings[{{ $role->slug }}][can_download]"
                   value="1"
                   {{ ($canDownload && $canDownload->can_download == 1) ? 'checked' : '' }}>
        </td>
    </tr>
@endforeach

                </tbody>
            </table>
        </div>

        <button class="btn btn-primary mt-3">Save Settings</button>
    </form>
    </div>

                </div> {{-- card-body --}}
            </div>
        </div>
    </div>

   @endsection

@section('script')
    <script src="{{ URL::asset('build/libs/sweetalert2/sweetalert2.min.js') }}"></script>
    
 @endsection
