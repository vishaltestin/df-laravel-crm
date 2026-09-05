<section class="hero">
    <h1>{{ $content['title'] }}</h1>
    <p>{{ $content['subtitle'] }}</p>

    @if(!empty($content['button_text']))
    <a href="{{ $content['button_link'] }}" class="btn btn-primary">
        {{ $content['button_text'] }}
    </a>
    @endif
</section>
