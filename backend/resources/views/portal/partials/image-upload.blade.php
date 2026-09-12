{{--
    Portal variant of admin.partials.image-upload — same jQuery-driven
    upload widget, pointed at portal.uploads instead of admin.uploads
    (MediaUploadController restricts Role::ARTIST to the "artists" and
    "releases" upload contexts).
--}}
@php($value = $value ?? null)
<div class="mb-3">
    <label class="form-label small fw-medium">{{ $label }}</label>
    <div class="image-upload d-flex align-items-center gap-3"
         data-upload-url="{{ route('portal.uploads') }}"
         data-context="{{ $context }}">
        <img
            src="{{ $value }}"
            alt=""
            class="image-upload__preview rounded border {{ $value ? '' : 'd-none' }}"
            style="width: 3rem; height: 3rem; object-fit: cover; border-color: var(--color-border-default) !important;"
        />
        <input type="file" accept="image/*" class="image-upload__file form-control form-control-sm" style="max-width: 220px;" />
        <span class="image-upload__status small" style="color: var(--color-text-muted);"></span>
        <input type="hidden" name="{{ $name }}" class="image-upload__hidden" value="{{ $value }}" />
    </div>
</div>
