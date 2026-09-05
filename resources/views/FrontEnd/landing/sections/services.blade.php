<section class="section" id="services">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-lg-8 text-center mb-4">
                <h1 class="mb-3 ff-secondary fw-bold lh-base">{{ $data['title'] ?? 'Services' }}</h1>
                <p class="text-muted">{{ $data['subtitle'] ?? '' }}</p>
            </div>
        </div>

        <div class="row g-3">
            @foreach($data['items'] ?? [] as $service)
                <div class="col-lg-4">
                    <div class="d-flex p-3">
                        <div class="flex-shrink-0 me-3">
                            <div class="avatar-sm icon-effect">
                                <div class="avatar-title bg-transparent text-success rounded-circle">
                                    <i class="{{ $service['icon'] ?? 'ri-pencil-ruler-2-line' }} fs-36"></i>
                                </div>
                            </div>
                        </div>

                        <div class="flex-grow-1">
                            <h5 class="fs-18">{{ $service['title'] }}</h5>
                            <p class="text-muted my-3 ff-secondary">{{ $service['description'] }}</p>
                            <a href="{{ $service['link'] ?? '#' }}" class="fs-13 fw-semibold">Learn More <i class="ri-arrow-right-s-line align-bottom"></i></a>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
</section>
