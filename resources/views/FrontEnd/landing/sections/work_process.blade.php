<section class="section">
    <div class="container">
        <div class="text-center mb-5"><h3 class="mb-3 fw-bold">{{ $data['title'] ?? 'Our Work Process' }}</h3><p class="text-muted mb-4 ff-secondary">{{ $data['subtitle'] ?? '' }}</p></div>

        <div class="row text-center">
            @foreach($data['steps'] ?? [] as $step)
                <div class="col-lg-4">
                    <div class="process-card mt-4">
                        <div class="avatar-sm icon-effect mx-auto mb-4"><div class="avatar-title bg-transparent text-success rounded-circle h1"><i class="{{ $step['icon'] ?? 'ri-quill-pen-line' }}"></i></div></div>
                        <h5>{{ $step['title'] }}</h5>
                        <p class="text-muted ff-secondary">{{ $step['desc'] }}</p>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
</section>
