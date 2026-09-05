        <div class="vertical-overlay" data-bs-toggle="collapse" data-bs-target="#navbarSupportedContent.show"></div>
  <section class="section pb-0 hero-section" id="hero">
            <div class="bg-overlay bg-overlay-pattern"></div>
            <div class="container">
                <div class="row justify-content-center">
                    <div class="col-lg-8 col-sm-10">
                        <div class="text-center mt-lg-5 pt-5">
                            <h1 class="display-6 fw-bold mb-3 lh-base">    {!! $content['title'] ?? '' !!}</h1>
                            <p class="lead text-muted lh-base">            {!! $content['subtitle'] ?? '' !!}        </p>

                      
						
							<div class="d-flex gap-2 justify-content-center mt-4">
            @if(!empty($content['button1_text']))
                <a href="{{ $content['button1_link'] ?? '#' }}" class="btn btn-primary">
                    {{ $content['button1_text'] }} <i class="ri-arrow-right-line"></i>
                </a>
            @endif

            @if(!empty($content['button2_text']))
                <a href="{{ $content['button2_link'] ?? '#' }}" class="btn btn-danger">
                    {{ $content['button2_text'] }}
                </a>
            @endif
        </div>
                        </div>

                        <div class="mt-4 mt-sm-5 pt-sm-5 mb-sm-n5 demo-carousel">
                            <div class="demo-img-patten-top d-none d-sm-block">
                                <img src="assets/images/landing/img-pattern.png" class="d-block img-fluid" alt="...">
                            </div>
                            <div class="demo-img-patten-bottom d-none d-sm-block">
                                <img src="assets/images/landing/img-pattern.png" class="d-block img-fluid" alt="...">
                            </div>
        @if(!empty($content['carousel_images']) && is_array($content['carousel_images']))
                            <div class="carousel slide carousel-fade" data-bs-ride="carousel">
                                <div class="carousel-inner shadow-lg p-2 bg-white rounded">

                        @foreach($content['carousel_images'] as $index => $img)
                            <div class="carousel-item {{ $index == 0 ? 'active' : '' }}">
                                <img src="{{ $img }}" class="d-block w-100">
                            </div>
                        @endforeach

                    </div>
                </div>
         @endif

						</div>
					</div>
				</div>
			</div>
</section>
