<section class="section pb-0 hero-section" id="hero">
    <div class="bg-overlay bg-overlay-pattern"></div>
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-lg-8 col-sm-10">
                <div class="text-center mt-lg-5 pt-5">
                    <h1 class="display-6 fw-bold mb-3 lh-base">{!! $data['title'] ?? 'Hero Title' !!}</h1>
                    <p class="lead text-muted lh-base">{!! $data['subtitle'] ?? '' !!}</p>

                    <div class="d-flex gap-2 justify-content-center mt-4">
                        <a href="{{ $data['primary_btn']['url'] ?? '#' }}" class="btn btn-primary">
                            {{ $data['primary_btn']['text'] ?? 'Get Started' }} <i class="ri-arrow-right-line align-middle ms-1"></i>
                        </a>
                        <a href="{{ $data['secondary_btn']['url'] ?? '#' }}" class="btn btn-danger">
                            {{ $data['secondary_btn']['text'] ?? 'View Plans' }} <i class="ri-eye-line align-middle ms-1"></i>
                        </a>
                    </div>
                </div>

                <div class="mt-4 mt-sm-5 pt-sm-5 mb-sm-n5 demo-carousel">
                    <div class="carousel slide carousel-fade" data-bs-ride="carousel">
                        <div class="carousel-inner shadow-lg p-2 bg-white rounded">
                            @foreach($data['carousel'] ?? [] as $i => $img)
                                <div class="carousel-item {{ $i===0 ? 'active' : '' }}" data-bs-interval="2000">
                                    <img src="{{ asset($img) }}" class="d-block w-100" alt="slide-{{ $i }}">
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </div>
</section>
