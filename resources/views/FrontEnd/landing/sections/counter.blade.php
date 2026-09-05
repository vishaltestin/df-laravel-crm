<section class="py-5 position-relative bg-light">
    <div class="container">
        <div class="row text-center gy-4">
            @foreach($data['counters'] ?? [] as $c)
                <div class="col-lg-3 col-6">
                    <div>
                        <h2 class="mb-2"><span class="counter-value" data-target="{{ $c['target'] }}">0</span>{{ $c['suffix'] ?? '' }}</h2>
                        <div class="text-muted">{{ $c['label'] }}</div>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
</section>
