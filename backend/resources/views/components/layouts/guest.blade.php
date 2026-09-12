<!DOCTYPE html>
<html lang="en">
<head>
    @include('partials.head')
</head>
<body class="d-flex min-vh-100 flex-column align-items-center justify-content-center px-3">
    {{ $slot }}

    <script src="{{ mix('js/app.js') }}"></script>
</body>
</html>
