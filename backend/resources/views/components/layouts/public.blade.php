@php
    // Ports components/marketing/site-header.tsx and site-footer.tsx.
    $navLinks = [
        ['href' => '/artists', 'label' => 'Artists'],
        ['href' => '/music', 'label' => 'Music'],
        ['href' => '/videos', 'label' => 'Videos'],
        ['href' => '/events', 'label' => 'Events'],
        ['href' => '/news', 'label' => 'News'],
        ['href' => '/store', 'label' => 'Store'],
        ['href' => '/about', 'label' => 'About'],
    ];
    $footerColumns = [
        ['heading' => 'Explore', 'links' => [
            ['href' => '/artists', 'label' => 'Artists'],
            ['href' => '/music', 'label' => 'Music'],
            ['href' => '/events', 'label' => 'Events'],
            ['href' => '/news', 'label' => 'News'],
        ]],
        ['heading' => 'Work with us', 'links' => [
            ['href' => '/artists#submit', 'label' => 'Submit your music'],
            ['href' => '/licensing', 'label' => 'Licensing'],
            ['href' => '/partnerships', 'label' => 'Partnerships'],
        ]],
        ['heading' => 'Company', 'links' => [
            ['href' => '/about', 'label' => 'About Tramax'],
            ['href' => '/contact', 'label' => 'Contact'],
        ]],
    ];
@endphp

<!DOCTYPE html>
<html lang="en">
<head>
    @include('partials.head', ['title' => isset($title) ? $title.' · Tramax Entertainment' : 'Tramax Entertainment'])
</head>
<body class="d-flex flex-column min-vh-100">
    <header class="site-header">
        <a href="/" class="site-header__brand">Tramax</a>
        <nav class="site-header__nav">
            @foreach ($navLinks as $link)
                <a href="{{ $link['href'] }}" class="site-header__nav-link">{{ $link['label'] }}</a>
            @endforeach
        </nav>
        <a href="/contact" class="btn btn-primary rounded-pill site-header__cta">Contact</a>
    </header>

    <main class="flex-grow-1">
        {{ $slot }}
    </main>

    <footer class="site-footer">
        <div class="site-footer__grid">
            <div>
                <p class="site-footer__brand mb-0">Tramax</p>
                <p class="small mt-2 mb-0" style="max-width: 24ch; color: var(--color-text-secondary);">Discover. Develop. Promote.</p>
            </div>
            @foreach ($footerColumns as $column)
                <div>
                    <p class="site-footer__heading mb-0">{{ $column['heading'] }}</p>
                    <ul class="list-unstyled d-flex flex-column gap-2 mt-3 mb-0">
                        @foreach ($column['links'] as $link)
                            <li><a href="{{ $link['href'] }}" class="site-footer__link">{{ $link['label'] }}</a></li>
                        @endforeach
                    </ul>
                </div>
            @endforeach
        </div>
        <p class="site-footer__bottom mb-0">&copy; {{ date('Y') }} Tramax Entertainment Ltd.</p>
    </footer>

    <script src="{{ mix('js/app.js') }}"></script>
</body>
</html>
