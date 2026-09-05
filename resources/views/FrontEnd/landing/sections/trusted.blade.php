<div class="pt-5 mt-5">
    <div class="container">
        <div class="text-center mt-5">
            <h5 class="fs-20">{{ $data['heading'] ?? 'Trusted by' }}</h5>

            <div class="swiper trusted-client-slider mt-sm-5 mt-4 mb-sm-5 mb-4" dir="ltr">
                <div class="swiper-wrapper">
                    @foreach($data['logos'] ?? [] as $logo)
                        <div class="swiper-slide">
                            <div class="client-images">
                                <img src="{{ asset($logo) }}" alt="client" class="mx-auto img-fluid d-block">
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>
</div>
