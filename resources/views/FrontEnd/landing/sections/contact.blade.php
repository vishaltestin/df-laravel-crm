<section class="section" id="contact">
    <div class="container">
        <div class="text-center mb-5"><h3 class="mb-3 fw-bold">{{ $data['title'] ?? 'Get in touch' }}</h3><p class="text-muted mb-4 ff-secondary">{{ $data['subtitle'] ?? '' }}</p></div>

        <div class="row gy-4">
            <div class="col-lg-4">
                <div>
                    @foreach($data['addresses'] ?? [] as $addr)
                        <div class="mt-4">
                            <h5 class="fs-13 text-muted text-uppercase">Office Address:</h5>
                            <div class="ff-secondary fw-semibold">{!! $addr !!}</div>
                        </div>
                    @endforeach
                </div>
            </div>

            <div class="col-lg-8">
                <form action="{{ $data['form_action'] ?? route('contact.submit') }}" method="{{ $data['form_method'] ?? 'POST' }}">
                    @csrf
                    <div class="row">
                        <div class="col-lg-6"><input name="name" type="text" class="form-control bg-light border-light" placeholder="Your name*"></div>
                        <div class="col-lg-6"><input name="email" type="email" class="form-control bg-light border-light" placeholder="Your email*"></div>
                    </div>
                    <div class="row mt-3"><div class="col-lg-12"><input name="subject" type="text" class="form-control bg-light border-light" placeholder="Subject"></div></div>
                    <div class="row mt-3"><div class="col-lg-12"><textarea name="message" rows="3" class="form-control bg-light border-light" placeholder="Your message..."></textarea></div></div>
                    <div class="row mt-3"><div class="col-lg-12 text-end"><button class="btn btn-primary" type="submit">{{ $data['submit_text'] ?? 'Send Message' }}</button></div></div>
                </form>
            </div>
        </div>
    </div>
</section>
