<section class="section bg-light py-5" id="features">
    <div class="container">
        <div class="row align-items-center gy-4">
            <div class="col-lg-6 col-sm-7 mx-auto">
                <img src="{{ asset($data['image'] ?? 'assets/images/landing/features/img-1.png') }}" class="img-fluid mx-auto" alt="features-image">
            </div>
            <div class="col-lg-6">
                <h3 class="mb-3 fs-20">{{ $data['title'] ?? 'Huge collection of widgets' }}</h3>
                <p class="mb-4 ff-secondary fs-16">{{ $data['description'] ?? '' }}</p>

                <div class="row pt-3">
                    @foreach($data['stats'] ?? [] as $s)
                        <div class="col-3 text-center">
                            <h4>{{ $s['value'] }}</h4>
                            <p>{{ $s['label'] }}</p>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>
</section>
