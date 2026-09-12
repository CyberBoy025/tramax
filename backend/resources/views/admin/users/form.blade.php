@php
    $isEditingSelf = $user && auth()->id() === $user->id;
@endphp

<x-layouts.admin :title="$user ? 'Edit User' : 'New User'">
    <div class="d-flex align-items-center justify-content-between" style="max-width: 32rem;">
        <h1 class="fs-3 fw-semibold mb-0">{{ $user ? 'Edit "'.$user->name.'"' : 'New User' }}</h1>
        <a href="{{ route('admin.users.index') }}" class="small">Cancel</a>
    </div>

    <form method="POST"
          action="{{ $user ? route('admin.users.update', $user) : route('admin.users.store') }}"
          class="mt-4" style="max-width: 32rem;">
        @csrf
        @if ($user) @method('PATCH') @endif

        <div class="mb-3">
            <label class="form-label small fw-medium">Name</label>
            <input type="text" name="name" required class="form-control @error('name') is-invalid @enderror" value="{{ old('name', $user->name ?? '') }}" />
            @error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror
        </div>

        <div class="mb-3">
            <label class="form-label small fw-medium">Email</label>
            <input type="email" name="email" required class="form-control @error('email') is-invalid @enderror" value="{{ old('email', $user->email ?? '') }}" />
            @error('email')<div class="invalid-feedback">{{ $message }}</div>@enderror
        </div>

        <div class="mb-3">
            <label class="form-label small fw-medium">
                Password
                @if ($user)<span style="color: var(--color-text-muted);">(leave blank to keep current)</span>@endif
            </label>
            <input type="password" name="password" @if(!$user) required @endif minlength="8"
                   class="form-control @error('password') is-invalid @enderror"
                   placeholder="{{ $user ? '••••••••' : '' }}" />
            @error('password')<div class="invalid-feedback">{{ $message }}</div>@enderror
        </div>

        <div class="row g-3">
            <div class="col-sm-6">
                <label class="form-label small fw-medium">
                    Role
                    @if ($isEditingSelf)<span class="d-block" style="color: var(--color-text-muted); font-weight: normal;">(you can't change your own role)</span>@endif
                </label>
                <select name="role_id" required @disabled($isEditingSelf) class="form-select @error('role_id') is-invalid @enderror">
                    @if (!$user)<option value="" disabled selected>Select a role…</option>@endif
                    @foreach ($roles as $role)
                        <option value="{{ $role->id }}" @selected(old('role_id', $user->role_id ?? '') == $role->id)>{{ $role->name }}</option>
                    @endforeach
                </select>
                @error('role_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>
            <div class="col-sm-6">
                <label class="form-label small fw-medium">
                    Status
                    @if ($isEditingSelf)<span class="d-block" style="color: var(--color-text-muted); font-weight: normal;">(you can't suspend yourself)</span>@endif
                </label>
                <select name="status" @disabled($isEditingSelf) class="form-select @error('status') is-invalid @enderror">
                    <option value="Active" @selected(old('status', $user->status ?? 'Active') === 'Active')>Active</option>
                    <option value="Suspended" @selected(old('status', $user->status ?? 'Active') === 'Suspended')>Suspended</option>
                </select>
                @error('status')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>
        </div>

        <div class="d-flex gap-2 mt-4">
            <button type="submit" class="btn btn-primary">{{ $user ? 'Save Changes' : 'Add User' }}</button>
            <a href="{{ route('admin.users.index') }}" class="btn btn-outline-secondary">Cancel</a>
        </div>
    </form>
</x-layouts.admin>
