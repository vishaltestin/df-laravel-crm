@extends('FrontEnd.layouts.master')
@section('title', 'Coupons')

@section('content')
@component('FrontEnd.components.breadcrumb')
    @slot('li_1') CRM @endslot
    @slot('title') Coupons @endslot
@endcomponent

<div class="row">
    <div class="col-lg-12">

        @if(session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
        @endif
        @if(session('error'))
            <div class="alert alert-danger">{{ session('error') }}</div>
        @endif

        <div class="card">
            <div class="card-header border-0 d-flex justify-content-between">
                <h5 class="mb-0">Coupon List</h5>
                <a href="{{ route('coupons.create') }}" class="btn btn-primary btn-sm">
                    + Create Coupon
                </a>
            </div>

            <div class="card-body table-responsive">
                <table class="table table-bordered align-middle">
                    <thead class="table-light">
                        <tr>
                            <th>ID</th>
                            <th>Code</th>
                            <th>Type</th>
                            <th>Value</th>
                            <th>Usage</th>
                            <th>Used</th>
                            <th>Status</th>
                            <th>Valid</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                    @forelse($coupons as $row)
                        <tr>
                            <td>{{ $row->id }}</td>
                            <td><b>{{ $row->code }}</b></td>
                            <td>{{ strtoupper($row->type) }}</td>
                            <td>
                                @if($row->type == 'percent')
                                    {{ $row->value }}%
                                @else
                                    ₹{{ $row->value }}
                                @endif
                            </td>
                            <td>
                                {{ strtoupper($row->usage_type) }}
                                @if($row->usage_type == 'multiple')
                                    <br><small>Limit: {{ $row->usage_limit ?? '∞' }}</small>
                                @endif
                            </td>
                            <td>{{ $row->used_count }}</td>
                            <td>
                                <span class="badge bg-{{ $row->status ? 'success':'danger' }}">
                                    {{ $row->status ? 'Active':'Inactive' }}
                                </span>
                            </td>
                            <td>
                                <small>
                                    {{ $row->start_at ? $row->start_at : '-' }} <br>
                                    {{ $row->end_at ? $row->end_at : '-' }}
                                </small>
                            </td>
                            <td class="d-flex gap-2">
                                <a href="{{ route('coupons.edit', $row->id) }}" class="btn btn-sm btn-info">
                                    Edit
                                </a>
                                <a href="{{ route('coupons.usage', $row->id) }}" class="btn btn-sm btn-warning">
                                    Logs
                                </a>
                                <button class="btn btn-sm btn-danger btnDelete" data-id="{{ $row->id }}">
                                    Delete
                                </button>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="9" class="text-center">No coupons found.</td>
                        </tr>
                    @endforelse
                    </tbody>
                </table>

                {{ $coupons->links() }}
            </div>
        </div>
    </div>
</div>
@endsection

@section('script')
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
$(document).on("click", ".btnDelete", function(){
    let id = $(this).data("id");

    if(!confirm("Delete coupon?")) return;

    $.ajax({
        url: "{{ url('coupons') }}/" + id,
        type: "DELETE",
        data: { _token: "{{ csrf_token() }}" },
        success: function(res){
            alert(res.message);
            location.reload();
        }
    });
});
</script>
@endsection
