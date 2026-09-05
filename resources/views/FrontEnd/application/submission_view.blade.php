@extends('FrontEnd.layouts.master')
@section('title', 'Submission View')

@section('content')
@component('FrontEnd.components.breadcrumb')
    @slot('li_1') CRM @endslot
    @slot('title') Submission View @endslot
@endcomponent

<div class="row">
    <div class="col-lg-12">
        <div class="card">
            <div class="card-body">

                <h3 class="mb-3">Submission Details</h3>

                <p class="mb-1"><strong>Submission ID:</strong> {{ $response->id }}</p>
                <p class="mb-1"><strong>Form ID:</strong> {{ $response->lead_form_id }}</p>
                <p class="mb-1"><strong>Lead ID:</strong> {{ $response->lead_id ?? '-' }}</p>
                <p class="mb-3"><strong>Submitted At:</strong> {{ $response->created_at }}</p>

                <hr>

                <h4 class="mb-2">Filled Data</h4>

                <table class="table table-bordered">
                    <thead>
                        <tr>
                            <th width="35%">Field</th>
                            <th>Value</th>
                        </tr>
                    </thead>

                    <tbody>
                        @forelse($data as $key => $value)
                            <tr>
                                <td>{{ ucfirst(str_replace('_', ' ', $key)) }}</td>
                                <td>
                                    @if(is_array($value))
                                        <pre style="margin:0;">{{ json_encode($value, JSON_PRETTY_PRINT) }}</pre>
                                    @else
                                        {{ $value ?? '-' }}
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="2" class="text-center">No data found</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>

                <a href="{{ route('application.form.submissions', $response->lead_form_id) }}"
                   class="btn btn-secondary">
                    Back
                </a>

            </div>
        </div>
    </div>
</div>
@endsection
