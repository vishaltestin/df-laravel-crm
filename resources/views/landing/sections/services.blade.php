<section class="section" id="services">
    <div class="container">
        <div class="text-center mb-5">
            <h1 class="fw-bold">{{ $content['title'] }}</h1>
            <p class="text-muted">{{ $content['subtitle'] }}</p>
        </div>

        <div class="row g-4">
            @foreach($content['items'] as $service)
            <div class="col-lg-4">
                <div class="d-flex p-3">
                    <div class="avatar-sm icon-effect me-3">
                        <div class="avatar-title bg-transparent text-success rounded-circle">
                            <i class="{{ $service['icon'] }} fs-36"></i>
                        </div>
                    </div>

                    <div>
                        <h5>{{ $service['title'] }}</h5>
                        <p class="text-muted">{{ $service['desc'] }}</p>
                        <a href="{{ $service['link'] }}" class="fw-semibold">
                            Learn More <i class="ri-arrow-right-s-line"></i>
                        </a>
                    </div>
                </div>
            </div>
            @endforeach
        </div>

    </div>
</section>
