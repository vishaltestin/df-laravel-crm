<section class="section" id="contact">
    <div class="container">

        <div class="text-center mb-5">
            <h3 class="fw-bold">{{ $content['title'] }}</h3>
            <p class="text-muted">{{ $content['subtitle'] }}</p>
        </div>

        <div class="row">
            <div class="col-lg-4">
                @foreach($content['addresses'] as $addr)
                <div class="mb-4">
                    <h6 class="text-muted">{{ $addr['title'] }}</h6>
                    <p class="fw-semibold">{{ $addr['text'] }}</p>
                </div>
                @endforeach
            </div>

            <div class="col-lg-8">
                {!! renderSection('form') !!}
            </div>
        </div>

    </div>
</section>
