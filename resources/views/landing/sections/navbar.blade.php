   <nav class="navbar navbar-expand-lg navbar-landing fixed-top" id="navbar">
            <div class="container">
        <a class="navbar-brand" href="#">
            <img src="{{ $content['logo_dark'] ?? 'build/images/logo-dark.png' }}" height="22">
        </a>

        <button class="navbar-toggler py-0 fs-20 text-body" type="button" data-bs-toggle="collapse" data-bs-target="#navMenu">
            <i class="mdi mdi-menu"></i>
        </button>

        <div class="collapse navbar-collapse" id="navMenu">
            <ul class="navbar-nav mx-auto mt-2 mt-lg-0">
                @foreach($content['menu'] ?? [] as $menu)
                    <li class="nav-item">
                        <a class="nav-link fw-semibold" href="{{ $menu['link'] }}">{{ $menu['label'] }}</a>
                    </li>
                @endforeach
            </ul>

            <div>
                <a href="{{ $content['signin_link'] ?? '#' }}" class="btn btn-link">Sign In</a>
                <a href="{{ $content['signup_link'] ?? '#' }}" class="btn btn-primary">Sign Up</a>
            </div>
        </div>
    </div>
</nav>
