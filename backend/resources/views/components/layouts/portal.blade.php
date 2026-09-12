@php
    // Ports frontend/src/app/portal/(protected)/layout.tsx's navGroups —
    // already trimmed to real pages only there (Discography/Earnings
    // Overview folded into Releases/Royalty, Documents & Contracts dropped
    // since no Document entity exists — see that file's own comment).
    $navGroups = [
        ['items' => [
            ['href' => '/portal/dashboard', 'label' => 'Dashboard'],
            ['href' => '/portal/profile', 'label' => 'My Profile'],
        ]],
        ['heading' => 'Catalogue & Finance', 'items' => [
            ['href' => '/portal/releases', 'label' => 'My Releases'],
            ['href' => '/portal/royalty-statements', 'label' => 'Royalty & Earnings'],
        ]],
        ['heading' => 'More', 'items' => [
            ['href' => '/portal/bookings', 'label' => 'Bookings'],
            ['href' => '/portal/notifications', 'label' => 'Notifications'],
        ]],
    ];
@endphp

<!DOCTYPE html>
<html lang="en">
<head>
    @include('partials.head', ['title' => ($title ?? 'Dashboard').' · Tramax Portal'])
</head>
<body>
    <x-dashboard-shell
        brand="Tramax Portal"
        :nav-groups="$navGroups"
        user-label="Signed in as {{ auth()->user()->name }}"
        :logout-route="route('portal.logout')"
    >
        {{ $slot }}
    </x-dashboard-shell>

    <script src="{{ mix('js/app.js') }}"></script>
</body>
</html>
