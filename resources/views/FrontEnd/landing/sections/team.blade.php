<section class="section bg-light" id="team">
    <div class="container">
        <div class="text-center mb-5"><h3 class="mb-3 fw-bold">{{ $data['title'] ?? 'Our Team' }}</h3><p class="text-muted mb-4 ff-secondary">{{ $data['subtitle'] ?? '' }}</p></div>

        <div class="row">
            @foreach($data['members'] ?? [] as $m)
                <div class="col-lg-3 col-sm-6">
                    <div class="card">
                        <div class="card-body text-center p-4">
                            <div class="avatar-xl mx-auto mb-4 position-relative">
                                <img src="{{ asset($m['photo'] ?? 'assets/images/users/avatar-2.jpg') }}" class="img-fluid rounded-circle" alt="{{ $m['name'] }}">
                            </div>
                            <h5 class="mb-1"><a href="{{ $m['profile'] ?? '#' }}" class="text-body">{{ $m['name'] }}</a></h5>
                            <p class="text-muted mb-0 ff-secondary">{{ $m['role'] }}</p>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
</section>
