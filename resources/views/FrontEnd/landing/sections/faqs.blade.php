<section class="section">
    <div class="container">
        <div class="text-center mb-5">
            <h3 class="mb-3 fw-bold">{{ $data['title'] ?? 'Frequently Asked Questions' }}</h3>
            <p class="text-muted mb-4 ff-secondary">{{ $data['subtitle'] ?? '' }}</p>
        </div>

        <div class="row g-lg-5 g-4">
            <div class="col-lg-6">
                <div class="accordion" id="faq-left">
                    @foreach($data['left'] ?? [] as $k => $q)
                        <div class="accordion-item">
                            <h2 class="accordion-header" id="headingLeft{{ $k }}">
                                <button class="accordion-button {{ $k? 'collapsed':'' }}" type="button" data-bs-toggle="collapse" data-bs-target="#collapseLeft{{ $k }}">
                                    {{ $q['q'] }}
                                </button>
                            </h2>
                            <div id="collapseLeft{{ $k }}" class="accordion-collapse collapse {{ $k? '':'show' }}" data-bs-parent="#faq-left">
                                <div class="accordion-body ff-secondary">{{ $q['a'] }}</div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>

            <div class="col-lg-6">
                <div class="accordion" id="faq-right">
                    @foreach($data['right'] ?? [] as $k => $q)
                        <div class="accordion-item">
                            <h2 class="accordion-header" id="headingRight{{ $k }}">
                                <button class="accordion-button {{ $k? 'collapsed':'' }}" type="button" data-bs-toggle="collapse" data-bs-target="#collapseRight{{ $k }}">
                                    {{ $q['q'] }}
                                </button>
                            </h2>
                            <div id="collapseRight{{ $k }}" class="accordion-collapse collapse {{ $k? '':'show' }}" data-bs-parent="#faq-right">
                                <div class="accordion-body ff-secondary">{{ $q['a'] }}</div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>

    </div>
</section>
