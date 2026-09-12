<x-layouts.guest>
    <p class="fw-semibold" style="font-family: var(--font-display); font-size: 1.125rem;">Tramax Admin</p>
    <h1 class="mt-2 mb-4 fs-3 fw-semibold">Admin Sign In</h1>

    <form method="POST" action="{{ route('admin.login.attempt') }}" class="w-100" style="max-width: 22rem;">
        @csrf

        @if ($errors->any())
            <div class="alert alert-danger py-2 small">{{ $errors->first() }}</div>
        @endif

        <label class="d-block mb-3">
            <span class="d-block small fw-medium mb-1">Email</span>
            <input type="email" name="email" value="{{ old('email') }}" required autofocus class="form-control" />
        </label>
        <label class="d-block mb-3">
            <span class="d-block small fw-medium mb-1">Password</span>
            <input type="password" name="password" required class="form-control" />
        </label>
        <button type="submit" class="btn btn-primary w-100">Sign In</button>
    </form>
</x-layouts.guest>
