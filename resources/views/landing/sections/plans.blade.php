<section class="section bg-light" id="plans">
    <div class="container">

        <div class="text-center mb-5">
            <h3 class="fw-bold">{{ $content['title'] }}</h3>
            <p class="text-muted">{{ $content['subtitle'] }}</p>
        </div>

        <div class="row g-4">

            @foreach($content['plans'] as $plan)
            <div class="col-lg-4">
                <div class="card plan-box">
                    <div class="card-body">
                        <h5 class="fw-bold">{{ $plan['name'] }}</h5>
                        <p class="text-muted">{{ $plan['tagline'] }}</p>

                        <h1 class="my-4">
                            <sup>$</sup>{{ $plan['price'] }}
                        </h1>

                        <ul class="text-muted ff-secondary">
                            @foreach($plan['features'] as $f)
                            <li>
                                <i class="{{ $f['available'] ? 'ri-checkbox-circle-fill text-success' : 'ri-close-circle-fill text-danger' }}"></i>
                                {{ $f['label'] }}
                            </li>
                            @endforeach
                        </ul>

                        <a href="{{ $plan['button_link'] }}" class="btn btn-soft-success w-100 mt-3">
                            {{ $plan['button_text'] }}
                        </a>
                    </div>
                </div>
            </div>
            @endforeach

        </div>
    </div>
</section>
