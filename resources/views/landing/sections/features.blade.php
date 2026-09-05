<section class="section bg-light" id="features">
    <div class="container">
        <div class="row align-items-center gy-4">

            <div class="col-lg-6">
                <img src="{{ $content['image'] }}" class="img-fluid">
            </div>

            <div class="col-lg-6">
                <h3>{{ $content['title'] }}</h3>
                <p class="text-muted">{{ $content['desc'] }}</p>

                <ul class="text-muted ff-secondary">
                    @foreach($content['bullets'] as $b)
                    <li class="mb-2">
                        <i class="ri-check-fill text-success"></i> {{ $b }}
                    </li>
                    @endforeach
                </ul>

                <a href="{{ $content['button_link'] }}" class="btn btn-primary">
                    {{ $content['button_text'] }}
                </a>
            </div>

        </div>
    </div>
</section>
