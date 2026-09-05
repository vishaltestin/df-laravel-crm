<section class="py-5 bg-primary position-relative">
    <div class="bg-overlay bg-overlay-pattern opacity-50"></div>
    <div class="container">
        <div class="row align-items-center gy-4">
            <div class="col-sm">
                <h4 class="text-white mb-0 fw-bold">{{ $data['title'] ?? 'Build your web App/SaaS' }}</h4>
            </div>
            <div class="col-sm-auto">
                <a href="{{ $data['cta_url'] ?? 'https://1.envato.market/velzon-admin' }}" target="_blank" class="btn bg-gradient btn-danger">
                    {{ $data['cta_text'] ?? 'Buy Now' }}
                </a>
            </div>
        </div>
    </div>
</section>
