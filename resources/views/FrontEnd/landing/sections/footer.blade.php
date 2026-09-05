<footer class="custom-footer bg-dark py-5 position-relative">
    <div class="container">
        <div class="row">
            <div class="col-lg-4 mt-4">
                <img src="{{ asset($data['logo'] ?? 'assets/images/logo-light.png') }}" height="17" alt="logo">
                <div class="mt-4 fs-13">
                    <p>{{ $data['desc'] ?? 'Premium Multipurpose Admin & Dashboard Template' }}</p>
                </div>
            </div>

            <div class="col-lg-7 ms-lg-auto">
                <div class="row">
                    @foreach($data['links_column'] ?? [] as $col)
                        <div class="col-sm-4 mt-4">
                            <h5 class="text-white mb-0">{{ $col['title'] }}</h5>
                            <div class="text-muted mt-3">
                                <ul class="list-unstyled ff-secondary footer-list fs-14">
                                    @foreach($col['items'] ?? [] as $it)
                                        <li><a href="{{ $it['url'] ?? '#' }}">{{ $it['label'] }}</a></li>
                                    @endforeach
                                </ul>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>

        <div class="row text-center text-sm-start align-items-center mt-5">
            <div class="col-sm-6">
                <p class="copy-rights mb-0">&copy; <script>document.write(new Date().getFullYear())</script> {{ $data['copyright'] ?? 'Velzon - Themesbrand' }}</p>
            </div>
            <div class="col-sm-6">
                <div class="text-sm-end mt-3 mt-sm-0">
                    <ul class="list-inline mb-0 footer-social-link">
                        @foreach($data['socials'] ?? [] as $s)
                            <li class="list-inline-item"><a href="{{ $s['url'] ?? '#' }}" class="avatar-xs d-block"><div class="avatar-title rounded-circle"><i class="{{ $s['icon'] }}"></i></div></a></li>
                        @endforeach
                    </ul>
                </div>
            </div>
        </div>
    </div>
</footer>
