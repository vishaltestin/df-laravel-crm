<section class="section bg-light" id="team">
    <div class="container">

        <div class="text-center mb-5">
            <h3 class="fw-bold">{{ $content['title'] }}</h3>
            <p class="text-muted">{{ $content['subtitle'] }}</p>
        </div>

        <div class="row">
        @foreach($content['members'] as $m)
            <div class="col-lg-3 col-sm-6">
                <div class="card text-center p-4">
                    <img src="{{ $m['photo'] }}" class="rounded-circle avatar-xl mx-auto">
                    <h5 class="mt-3">{{ $m['name'] }}</h5>
                    <p class="text-muted">{{ $m['role'] }}</p>
                </div>
            </div>
        @endforeach
        </div>

    </div>
</section>
