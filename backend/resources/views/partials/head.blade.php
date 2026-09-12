<meta charset="utf-8" />
<meta name="viewport" content="width=device-width, initial-scale=1" />
<meta name="csrf-token" content="{{ csrf_token() }}" />
<title>{{ $title ?? 'Tramax Entertainment' }}</title>
@isset($description)
    <meta name="description" content="{{ $description }}" />
@endisset

{{-- Same four Google Fonts the Next.js frontend loaded via next/font/google
     (see ui.md §4) — plain <link> here since there's no framework-level
     font-loading helper on the Laravel side. --}}
<link rel="preconnect" href="https://fonts.googleapis.com" />
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
<link
    href="https://fonts.googleapis.com/css2?family=Outfit:wght@500;600;700&family=Public+Sans:wght@400;500;600&family=Newsreader:ital,wght@1,500&family=IBM+Plex+Mono:wght@400;500;600&display=swap"
    rel="stylesheet"
/>

<link rel="stylesheet" href="{{ mix('css/app.css') }}" />
