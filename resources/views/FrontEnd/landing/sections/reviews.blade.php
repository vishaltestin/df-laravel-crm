<section class="section bg-primary" id="reviews">
    <div class="bg-overlay bg-overlay-pattern"></div>
    <div class="container">
        <div class="text-center">
            <div><i class="ri-double-quotes-l text-success display-3"></i></div>
            <h4 class="text-white mb-5">{{ $data['headline'] ?? 'Satisfied clients' }}</h4>

            <div class="swiper client-review-swiper rounded" dir="ltr">
                <div class="swiper-wrapper">
                    @foreach($data['slides'] ?? [] as $s)
                        <div class="swiper-slide">
                            <div class="row justify-content-center">
                                <div class="col-10">
                                    <div class="text-white-50">
                                        <p class="fs-20 mb-4">{{ $s['text'] }}</p>
                                        <div><h5 class="text-white">{{ $s['author'] }}</h5><p>- {{ $s['meta'] ?? '' }}</p></div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
                <div class="swiper-button-next bg-white rounded-circle"></div>
                <div class="swiper-button-prev bg-white rounded-circle"></div>
                <div class="swiper-pagination position-relative mt-2"></div>
            </div>
        </div>
    </div>
</section>
