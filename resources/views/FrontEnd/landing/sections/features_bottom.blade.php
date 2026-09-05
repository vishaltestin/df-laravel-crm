<section class="section">
    <div class="container">
        <div class="row align-items-center mt-5 pt-lg-5 gy-4">
            <div class="col-lg-6">
                <h5 class="fs-12 text-uppercase text-success">{{ $data['eyebrow'] ?? 'structure' }}</h5>
                <h4 class="mb-3">{{ $data['title'] ?? 'Well Documented' }}</h4>
                <p class="mb-4">{{ $data['description'] ?? '' }}</p>

                <div class="vstack gap-2">
                    @foreach($data['bullets'] ?? [] as $b)
                        <div class="d-flex align-items-center">
                            <div class="flex-shrink-0 me-2">
                                <div class="avatar-xs icon-effect">
                                    <div class="avatar-title bg-transparent text-success rounded-circle h2"><i class="ri-check-fill"></i></div>
                                </div>
                            </div>
                            <div class="flex-grow-1"><p class="mb-0">{{ $b }}</p></div>
                        </div>
                    @endforeach
                </div>
            </div>

            <div class="col-lg-6">
                <img src="{{ asset($data['image'] ?? 'assets/images/landing/features/img-2.png') }}" class="img-fluid" alt="feature-2">
            </div>
        </div>
    </div>
</section>
