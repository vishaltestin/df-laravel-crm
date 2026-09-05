<section class="section bg-light" id="plans">
    <div class="container">
        <div class="text-center mb-5">
            <h3 class="mb-3 fw-bold">{{ $data['title'] ?? 'Choose the plan' }}</h3>
            <p class="text-muted mb-4">{{ $data['subtitle'] ?? '' }}</p>
        </div>

        <div class="row gy-4">
            @foreach($data['plans'] ?? [] as $plan)
                <div class="col-lg-4">
                    <div class="card plan-box mb-0">
                        <div class="card-body p-4 m-2">
                            <div class="d-flex align-items-center">
                                <div class="flex-grow-1">
                                    <h5 class="mb-1 fw-bold">{{ $plan['name'] }}</h5>
                                    <p class="text-muted mb-0">{{ $plan['tag'] ?? '' }}</p>
                                </div>
                                <div class="avatar-sm">
                                    <div class="avatar-title bg-light rounded-circle text-primary">
                                        <i class="{{ $plan['icon'] ?? 'ri-book-mark-line' }} fs-20"></i>
                                    </div>
                                </div>
                            </div>

                            <div class="py-4 text-center">
                                <h1 class="month"><sup><small>{{ $plan['currency_symbol'] ?? '$' }}</small></sup><span class="ff-secondary fw-bold">{{ $plan['monthly'] }}</span> <span class="fs-13 text-muted">/Month</span></h1>
                                <h1 class="annual d-none"><sup><small>{{ $plan['currency_symbol'] ?? '$' }}</small></sup><span class="ff-secondary fw-bold">{{ $plan['annual'] ?? '' }}</span> <span class="fs-13 text-muted">/Year</span></h1>
                            </div>

                            <div>
                                <ul class="list-unstyled text-muted vstack gap-3 ff-secondary">
                                    @foreach($plan['features'] ?? [] as $f)
                                        <li><div class="d-flex"><div class="flex-shrink-0 text-success me-1"><i class="ri-checkbox-circle-fill fs-15 align-middle"></i></div><div class="flex-grow-1">{{ $f }}</div></div></li>
                                    @endforeach
                                </ul>
                                <div class="mt-4">
                                    <a href="{{ $plan['cta'] ?? 'javascript:void(0);' }}" class="btn btn-soft-success w-100">Get Started</a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>

    </div>
</section>
