<section class="section" id="faq">
    <div class="container">

        <div class="text-center mb-5">
            <h3 class="fw-bold">{{ $content['title'] }}</h3>
            <p class="text-muted">{{ $content['subtitle'] }}</p>
        </div>

        <div class="accordion" id="faqAccordion">
            @foreach($content['items'] as $index => $faq)
            <div class="accordion-item">
                <h2 class="accordion-header" id="h{{ $index }}">
                    <button class="accordion-button {{ $index > 0 ? 'collapsed' : '' }}"
                        type="button"
                        data-bs-toggle="collapse"
                        data-bs-target="#c{{ $index }}">
                        {{ $faq['q'] }}
                    </button>
                </h2>
                <div id="c{{ $index }}" class="accordion-collapse collapse {{ $index == 0 ? 'show' : '' }}">
                    <div class="accordion-body">
                        {{ $faq['a'] }}
                    </div>
                </div>
            </div>
            @endforeach
        </div>

    </div>
</section>
