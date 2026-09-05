@extends('FrontEnd.layouts.master')
@section('title', 'Coupon Usage Logs')

@section('content')
@component('FrontEnd.components.breadcrumb')
    @slot('li_1') CRM @endslot
    @slot('title') Coupon Usage Logs @endslot
@endcomponent

<div class="row">
    <div class="col-lg-12">

        <div class="card">
            <div class="card-header border-0 d-flex align-items-center justify-content-between">
                <h5 class="mb-0">
                    Coupon Usage Logs
                    @if(!empty($coupon))
                        <span class="badge bg-primary ms-2">{{ $coupon->code }}</span>
                    @endif
                </h5>

                <a href="{{ route('coupons.index') }}" class="btn btn-sm btn-secondary">
                    ← Back to Coupons
                </a>
            </div>

            <div class="card-body table-responsive">

                @if(isset($logs) && count($logs) > 0)

                    <table class="table table-bordered table-striped align-middle">
                        <thead class="table-light">
                            <tr>
                                <th style="width:70px;">#</th>
                                <th>Coupon Code</th>
                                <th>Used By (User)</th>
                                <th>Lead ID</th>
                                <th>Mobile</th>
                                <th>Amount</th>
                                <th>Discount</th>
                                <th>IP</th>
                                <th>Used At</th>
                            </tr>
                        </thead>

                        <tbody>
                            @foreach($logs as $k => $row)
                                <tr>
                                    <td>{{ $logs->firstItem() + $k }}</td>

                                    <td>
                                        <span class="fw-bold">{{ $row->coupon_code ?? '-' }}</span>
                                    </td>

                                    <td>
                                        {{ $row->user_name ?? 'System' }}
                                    </td>

                                    <td>
                                        {{ $row->lead_id ?? '-' }}
                                    </td>

                                    <td>
                                        {{ $row->mobile ?? '-' }}
                                    </td>

                                    <td>
                                        {{ $row->amount ?? '-' }}
                                    </td>

                                    <td>
                                        {{ $row->discount_amount ?? '-' }}
                                    </td>

                                    <td>
                                        {{ $row->ip_address ?? '-' }}
                                    </td>

                                    <td>
                                        {{ $row->created_at }}
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>

                    <div class="mt-3">
                        {{ $logs->links() }}
                    </div>

                @else
                    <div class="text-center p-4">
                        <h5 class="text-muted mb-0">No usage logs found.</h5>
                    </div>
                @endif

            </div>
        </div>

    </div>
</div>
@endsection
