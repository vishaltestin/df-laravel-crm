@extends('FrontEnd.layouts.master')
@section('title','Lead Distribution Report')

@section('content')

@component('FrontEnd.components.breadcrumb')
    @slot('li_1') Reports @endslot
    @slot('title') Lead Distribution @endslot
@endcomponent

<div class="row">
    <div class="col-md-6">
        <div class="card">
            <div class="card-header"><h5 class="mb-0">By Rule Type</h5></div>
            <div class="card-body">
                <table class="table table-sm">
                    <thead>
                    <tr>
                        <th>Rule Type</th>
                        <th>Leads</th>
                    </tr>
                    </thead>
                    <tbody>
                    @foreach($byRuleType as $row)
                        <tr>
                            <td>{{ ucfirst($row->rule_type) }}</td>
                            <td>{{ $row->total }}</td>
                        </tr>
                    @endforeach
                    @if($byRuleType->isEmpty())
                        <tr><td colspan="2">No data</td></tr>
                    @endif
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <div class="col-md-6">
        <div class="card">
            <div class="card-header"><h5 class="mb-0">By User</h5></div>
            <div class="card-body">
                <table class="table table-sm">
                    <thead>
                    <tr>
                        <th>User</th>
                        <th>Leads</th>
                    </tr>
                    </thead>
                    <tbody>
                    @foreach($byUser as $row)
                        <tr>
                            <td>{{ $users[$row->assigned_to]->first_name ?? ('User #'.$row->assigned_to) }}</td>
                            <td>{{ $row->total }}</td>
                        </tr>
                    @endforeach
                    @if($byUser->isEmpty())
                        <tr><td colspan="2">No data</td></tr>
                    @endif
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

@endsection

@section('script')
@endsection
