@extends('FrontEnd.layouts.master')
@section('title', 'Communication Report')

@section('content')

@component('FrontEnd.components.breadcrumb')
    @slot('li_1') Reports @endslot
    @slot('title') Communication Report @endslot
@endcomponent

<div class="card">
    <div class="card-body">

        <form method="GET" class="row g-2 mb-3">
            <div class="col-md-2">
                <label class="form-label">From</label>
                <input type="date" name="from" class="form-control" value="{{ $from }}">
            </div>

            <div class="col-md-2">
                <label class="form-label">To</label>
                <input type="date" name="to" class="form-control" value="{{ $to }}">
            </div>

            <div class="col-md-2">
                <label class="form-label">Channel</label>
                <select name="channel" class="form-select">
                    <option value="all" {{ ($channel=='all')?'selected':'' }}>All</option>
                    <option value="email" {{ ($channel=='email')?'selected':'' }}>Email</option>
                    <option value="sms" {{ ($channel=='sms')?'selected':'' }}>SMS</option>
                    <option value="whatsapp" {{ ($channel=='whatsapp')?'selected':'' }}>WhatsApp</option>
                </select>
            </div>

            <div class="col-md-2">
                <label class="form-label">Status</label>
                <select name="status" class="form-select">
                    <option value="all" {{ ($status=='all')?'selected':'' }}>All</option>
                    <option value="pending" {{ ($status=='pending')?'selected':'' }}>Pending</option>
                    <option value="sent" {{ ($status=='sent')?'selected':'' }}>Sent</option>
                    <option value="failed" {{ ($status=='failed')?'selected':'' }}>Failed</option>
                </select>
            </div>

            <div class="col-md-2 d-flex align-items-end">
                <button class="btn btn-primary w-100">
                    Filter
                </button>
            </div>

            <div class="col-md-2 d-flex align-items-end">
                <a href="{{ url()->current() }}" class="btn btn-light w-100">
                    Reset
                </a>
            </div>
        </form>

        <div class="table-responsive">
            <table class="table table-sm table-hover align-middle">
                <thead class="table-light">
                    <tr>
                        <th>Date</th>
                        <th>Channel</th>
                        <th>Lead</th>
                        <th>To</th>
                        <th>Subject</th>
                        <th>Message</th>
                        <th>Status</th>
                        <th>Sent At</th>
                        <th>View</th>
                    </tr>
                </thead>

                <tbody>
                @forelse($logs as $c)

                    @php
                        $status = $c->status ?? 'pending';

                        $statusClass = match($status) {
                            'sent' => 'bg-success',
                            'failed' => 'bg-danger',
                            default => 'bg-warning text-dark'
                        };
                    @endphp

                    <tr>
                        <td>
                            <div class="fw-semibold">
                                {{ \Carbon\Carbon::parse($c->created_at)->format('d M Y') }}
                            </div>
                            <div class="text-muted small">
                                {{ \Carbon\Carbon::parse($c->created_at)->format('h:i A') }}
                            </div>
                        </td>

                        <td>
                            <span class="badge bg-secondary text-uppercase">
                                {{ $c->channel }}
                            </span>
                        </td>

                        <td>#{{ $c->lead_id ?? '-' }}</td>

                        <td>{{ $c->to ?? '-' }}</td>

                        <td>{{ $c->subject ?? '-' }}</td>

                        <td>{{ \Illuminate\Support\Str::limit(strip_tags($c->content ?? ''), 50) }}</td>

                        <td>
                            <span class="badge {{ $statusClass }} text-uppercase">
                                {{ $status }}
                            </span>
                        </td>

                        <td>
                            {{ $c->sent_at ? \Carbon\Carbon::parse($c->sent_at)->format('d M Y, h:i A') : '-' }}
                        </td>

                        <td>
                            <button class="btn btn-sm btn-outline-primary"
                                data-bs-toggle="modal"
                                data-bs-target="#commModal{{ $c->id }}">
                                View
                            </button>
                        </td>
                    </tr>

                    {{-- Modal --}}
                    <div class="modal fade" id="commModal{{ $c->id }}" tabindex="-1">
                        <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h5 class="modal-title">Communication Details</h5>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                </div>
                                <div class="modal-body">
                                    <p><strong>Channel:</strong> {{ strtoupper($c->channel) }}</p>
                                    <p><strong>To:</strong> {{ $c->to }}</p>
                                    <p><strong>Subject:</strong> {{ $c->subject ?? '-' }}</p>
                                    <p><strong>Status:</strong> {{ $c->status }}</p>
                                    <p><strong>Sent At:</strong> {{ $c->sent_at ?? '-' }}</p>
                                    <hr>
                                    <div>
                                        <strong>Message:</strong>
                                        <div class="border rounded p-3 mt-2">
                                            {!! $c->content !!}
                                        </div>
                                    </div>

                                    @if(!empty($c->response_json))
                                        <hr>
                                        <strong>Response / Error:</strong>
                                        <pre class="bg-light p-2 rounded small">{{ $c->response_json }}</pre>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>

                @empty
                    <tr>
                        <td colspan="9" class="text-center text-muted">No logs found</td>
                    </tr>
                @endforelse
                </tbody>
            </table>
        </div>

        <div class="mt-3">
            {{ $logs->links() }}
        </div>

    </div>
</div>

@endsection
