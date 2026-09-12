@php
    // Ports the navGroups const from frontend/src/app/admin/(protected)/layout.tsx
    // verbatim. Every item renders regardless of role — per-module RBAC is
    // enforced server-side by routes/web.php's role: middleware, matching
    // the original's own comment on why the sidebar doesn't try to predict
    // access itself.
    $navGroups = [
        ['items' => [['href' => '/admin/dashboard', 'label' => 'Dashboard']]],
        ['heading' => 'Artists', 'items' => [
            ['href' => '/admin/applications', 'label' => 'Applications'],
            ['href' => '/admin/artists', 'label' => 'Artists'],
        ]],
        ['heading' => 'Catalogue', 'items' => [
            ['href' => '/admin/releases', 'label' => 'Music / Releases'],
            ['href' => '/admin/rights', 'label' => 'Rights & Catalogue'],
        ]],
        ['heading' => 'Business', 'items' => [
            ['href' => '/admin/royalty', 'label' => 'Royalty & Revenue'],
            ['href' => '/admin/licensing', 'label' => 'Licensing Requests'],
            ['href' => '/admin/partners', 'label' => 'Partners'],
        ]],
        ['heading' => 'Public site', 'items' => [
            ['href' => '/admin/events', 'label' => 'Events'],
            ['href' => '/admin/store', 'label' => 'Merchandise'],
            ['href' => '/admin/content', 'label' => 'News / Pages / Media'],
        ]],
        ['heading' => 'System', 'items' => [
            ['href' => '/admin/users', 'label' => 'Users & Roles'],
            ['href' => '/admin/audit-log', 'label' => 'Audit Log'],
            ['href' => '/admin/reports', 'label' => 'Reports'],
        ]],
    ];
@endphp

<!DOCTYPE html>
<html lang="en">
<head>
    @include('partials.head', ['title' => ($title ?? 'Admin').' · Tramax Admin'])
</head>
<body>
    <x-dashboard-shell
        brand="Tramax Admin"
        :nav-groups="$navGroups"
        user-label="Signed in as {{ auth()->user()->name }} · {{ auth()->user()->role->name }}"
        :logout-route="route('admin.logout')"
    >
        {{ $slot }}
    </x-dashboard-shell>

    <script src="{{ mix('js/app.js') }}"></script>
</body>
</html>
